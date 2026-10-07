<?php

namespace App\Services;

use App\Jobs\ImportInvoicesCsvJob;
use App\Models\Buyer;
use App\Models\CsvImportJob;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class InvoiceImportService
{
    private const CHUNK_SIZE = 500;

    private const IMPORT_COLUMNS = [
        'tin',
        'registered_name',
        'document_type',
        'invoice_date',
        'invoice_time',
        'due_date',
        'reference_number',
        'purchase_order_number',
        'currency_code',
        'accounting_currency_code',
        'exchange_rate',
        'exchange_rate_date',
        'exchange_rate_source',
        'amount_paid',
        'payment_terms',
        'payment_method',
        'payment_status',
        'system_branch_code',
        'fiscal_year',
        'source',
        'remarks',
        'item_code',
        'description',
        'quantity',
        'unit_code',
        'unit_price',
        'discount_amount',
        'tax_type',
        'tax_category',
        'tax_rate',
    ];

    public function import(Request $request): CsvImportJob
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
        ]);

        $user = auth()->user();
        $sellerId = $user?->seller?->id;
        $userId = $user?->id;

        if (! $sellerId) abort(403, 'Seller account not found.');
        if (! $userId) abort(403, 'User account not found.');

        $file = $request->file('file');
        $filePath = $file->store('invoice-imports', 'local');

        $importJob = CsvImportJob::create([
            'seller_id' => $sellerId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'status' => 'PENDING',
            'total_rows' => 0,
            'processed_rows' => 0,
            'successful_rows' => 0,
            'failed_rows' => 0,
            'skipped_rows' => 0,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);

        ImportInvoicesCsvJob::dispatch($importJob->id, 0, $sellerId, $userId);

        return $importJob;
    }

    public function processChunk(
        CsvImportJob $importJob,
        int $offset,
        int $chunkSize = self::CHUNK_SIZE,
        ?int $sellerId = null,
        ?int $userId = null
    ): bool {
        $sellerId ??= $importJob->seller_id;

        if (! $sellerId) throw new RuntimeException('Seller account could not be determined for this import job.');
        if (! $userId) throw new RuntimeException('User account could not be determined for this import job.');
        if (! Storage::disk('local')->exists($importJob->file_path)) throw new RuntimeException('The uploaded CSV file could not be found.');

        $handle = fopen(Storage::disk('local')->path($importJob->file_path), 'r');

        if ($handle === false) throw new RuntimeException('Unable to open the CSV file.');

        try {
            $firstRow = fgetcsv($handle);

            if ($firstRow === false) throw new RuntimeException('The CSV file is empty.');

            $firstRow = $this->cleanRow($firstRow);
            $hasHeader = $this->hasRecognizedHeaders($firstRow);

            if ($hasHeader) {
                $header = $this->normalizeHeader($firstRow);
                $rows = $this->readRows($handle, $offset, $chunkSize);
            } else {
                $header = self::IMPORT_COLUMNS;
                $rows = $this->readHeaderlessRows($handle, $firstRow, $offset, $chunkSize);
            }

            if (empty($rows)) {
                fclose($handle);
                return false;
            }

            $hasMoreRows = count($rows) === $chunkSize;
            $processed = $successful = $failed = $skipped = 0;

            foreach ($rows as $row) {
                $processed++;
                $data = [];

                try {
                    $data = $hasHeader
                        ? $this->mapRow($header, $row)
                        : $this->mapRowByReference($row);

                    if ($this->isDuplicateRow($data, $sellerId)) {
                        $skipped++;

                        Log::info('Invoice CSV import row skipped (duplicate)', [
                            'import_job_id' => $importJob->id,
                            'seller_id' => $sellerId,
                            'row' => $processed,
                            'tin' => $data['tin'] ?? null,
                            'reference_number' => $data['reference_number'] ?? null,
                            'invoice_date' => $data['invoice_date'] ?? null,
                        ]);

                        continue;
                    }

                    $buyer = $this->findBuyer($data, $sellerId);

                    app(InvoiceService::class)->create(
                        $this->prepareInvoiceData($data, $sellerId, $buyer->id),
                        $userId
                    );

                    $successful++;
                } catch (Throwable $e) {
                    $failed++;
                    $errorMessage = "Row {$processed}: {$e->getMessage()}";
                    $existingErrors = $importJob->error_message;

                    $importJob->update([
                        'error_message' => $existingErrors
                            ? $existingErrors . PHP_EOL . $errorMessage
                            : $errorMessage,
                    ]);

                    Log::error('Invoice CSV import failed', [
                        'import_job_id' => $importJob->id,
                        'seller_id' => $sellerId,
                        'user_id' => $userId,
                        'row' => $processed,
                        'data' => $data ?: $row,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $importJob->increment('processed_rows', $processed);
            $importJob->increment('successful_rows', $successful);
            $importJob->increment('failed_rows', $failed);
            $importJob->increment('skipped_rows', $skipped);

            fclose($handle);

            return $hasMoreRows;
        } catch (Throwable $e) {
            fclose($handle);
            throw $e;
        }
    }

    private function hasRecognizedHeaders(array $row): bool
    {
        $columns = array_map(
            fn ($value) => $this->normalizeColumnName($value),
            $row
        );

        return count(array_intersect($columns, self::IMPORT_COLUMNS)) > 0;
    }

    private function normalizeHeader(array $header): array
    {
        return array_map(
            fn ($value) => $this->normalizeColumnName($value),
            $header
        );
    }

    private function normalizeColumnName(mixed $value): string
    {
        $value = trim((string) $value, " \t\n\r\0\x0B\xEF\xBB\xBF");
        $value = strtolower($value);

        return preg_replace('/[\s\-]+/', '_', $value);
    }

    private function readRows($handle, int $offset, int $chunkSize): array
    {
        $currentRow = 0;

        while ($currentRow < $offset) {
            $row = fgetcsv($handle);

            if ($row === false) return [];

            if ($this->isEmptyRow($row)) continue;

            $currentRow++;
        }

        $rows = [];

        while (count($rows) < $chunkSize && ($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($row)) continue;

            $rows[] = $row;
        }

        return $rows;
    }

    private function readHeaderlessRows($handle, array $firstRow, int $offset, int $chunkSize): array
    {
        $currentRow = 0;
        $rows = [];

        if (! $this->isEmptyRow($firstRow)) {
            if ($currentRow >= $offset) $rows[] = $firstRow;
            $currentRow++;
        }

        while (count($rows) < $chunkSize && ($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($row)) continue;

            if ($currentRow < $offset) {
                $currentRow++;
                continue;
            }

            $rows[] = $row;
            $currentRow++;
        }

        return $rows;
    }

    private function mapRow(array $header, array $row): array
    {
        $row = $this->cleanRow($row);
        $row = array_pad($row, count($header), null);

        return array_combine($header, array_slice($row, 0, count($header)));
    }

    private function mapRowByReference(array $row): array
    {
        $row = $this->cleanRow($row);
        $data = [];

        foreach (self::IMPORT_COLUMNS as $index => $column) {
            $data[$column] = $row[$index] ?? null;
        }

        return $data;
    }

    private function normalizeDate(mixed $value, string $fieldName): ?string
    {
        if ($value === null || trim((string) $value) === '') return null;

        $value = trim((string) $value);

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            throw new RuntimeException(
                "Invalid {$fieldName} '{$value}'. Expected format: YYYY-MM-DD."
            );
        }
    }

    private function isDuplicateRow(array $data, int $sellerId): bool
    {
        $tin = trim((string) ($data['tin'] ?? ''));
        $referenceNumber = trim((string) ($data['reference_number'] ?? ''));
        $invoiceDate = trim((string) ($data['invoice_date'] ?? ''));

        if ($tin === '' || $referenceNumber === '' || $invoiceDate === '') return false;

        try {
            $invoiceDate = Carbon::parse($invoiceDate)->format('Y-m-d');
        } catch (Throwable) {
            return false;
        }

        return Invoice::query()
            ->where('invoices.seller_id', $sellerId)
            ->where('invoices.reference_number', $referenceNumber)
            ->whereDate('invoices.invoice_date', $invoiceDate)
            ->whereHas('buyer', fn ($q) => $q->where('tin', $tin))
            ->exists();
    }

    private function findBuyer(array $data, int $sellerId): Buyer
    {
        $tin = $this->normalizeTin($data['tin'] ?? '');
        $name = $this->normalizeText($data['registered_name'] ?? '');

        if ($tin === '') {
            throw new RuntimeException('Buyer TIN is required.');
        }

        $buyers = Buyer::where('seller_id', $sellerId)
            ->whereNotNull('tin')
            ->get();

        $buyer = $buyers->first(function (Buyer $buyer) use ($tin, $name) {
            if ($this->normalizeTin($buyer->tin) !== $tin) {
                return false;
            }

            if ($name === '') {
                return true;
            }

            $dbName = $this->normalizeText($buyer->registered_name);

            if ($dbName === $name) {
                return true;
            }

            similar_text($dbName, $name, $percent);

            return $percent >= 85;
        });

        if (!$buyer) {
            throw new RuntimeException(
                "Buyer not found for TIN '{$tin}' and registered name '{$name}'."
            );
        }

        return $buyer;
    }

    private function normalizeTin(mixed $value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    private function normalizeText(mixed $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = preg_replace('/[^\pL\pN\s]/u', ' ', $value);

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function prepareInvoiceData(array $data, int $sellerId, int $buyerId): array
    {
        $description = trim((string) ($data['description'] ?? ''));
        if ($description === '') throw new RuntimeException('Item description is required.');

        $quantity = (float) ($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            throw new RuntimeException(
                "Quantity must be greater than zero. Received: '" .
                ($data['quantity'] ?? 'NULL') .
                "'"
            );
        }

        $unitPrice = (float) ($data['unit_price'] ?? 0);
        if ($unitPrice < 0) throw new RuntimeException('Unit price cannot be negative.');

        $discount = (float) ($data['discount_amount'] ?? 0);
        if ($discount < 0) throw new RuntimeException('Discount cannot be negative.');

        $taxRate = (float) ($data['tax_rate'] ?? 0);
        if ($taxRate < 0) throw new RuntimeException('Tax rate cannot be negative.');

        $amountPaid = (float) ($data['amount_paid'] ?? 0);
        if ($amountPaid < 0) throw new RuntimeException('Amount paid cannot be negative.');

        $paymentStatus = strtoupper(trim((string) ($data['payment_status'] ?? ''))) ?: 'UNPAID';
        $currencyCode = strtoupper(trim((string) ($data['currency_code'] ?? 'PHP')));

        if (strlen($currencyCode) !== 3) {
            throw new RuntimeException('Currency code must be exactly 3 characters.');
        }

        return [
            'seller_id' => $sellerId,
            'buyer_id' => $buyerId,
            'document_type' => $this->nullableValue($data['document_type'] ?? 'INVOICE'),
            'invoice_date' => $this->normalizeDate(
                $data['invoice_date'] ?? now()->format('Y-m-d'),
                'invoice date'
            ),
            'invoice_time' => $this->nullableValue($data['invoice_time'] ?? null),
            'due_date' => $this->normalizeDate($data['due_date'] ?? null, 'due date'),
            'reference_number' => $this->nullableValue($data['reference_number'] ?? null),
            'purchase_order_number' => $this->nullableValue($data['purchase_order_number'] ?? null),
            'currency_code' => $currencyCode,
            'accounting_currency_code' => $this->nullableValue($data['accounting_currency_code'] ?? null),
            'exchange_rate' => $this->nullableValue($data['exchange_rate'] ?? null),
            'exchange_rate_date' => $this->normalizeDate(
                $data['exchange_rate_date'] ?? null,
                'exchange rate date'
            ),
            'exchange_rate_source' => $this->nullableValue($data['exchange_rate_source'] ?? null),
            'amount_paid' => $amountPaid,
            'payment_terms' => $this->nullableValue($data['payment_terms'] ?? null),
            'payment_method' => $this->nullableValue($data['payment_method'] ?? null),
            'payment_status' => $paymentStatus,
            'system_branch_code' => $this->nullableValue($data['system_branch_code'] ?? null),
            'fiscal_year' => ! empty($data['fiscal_year']) ? (int) $data['fiscal_year'] : now()->year,
            'source' => $this->nullableValue($data['source'] ?? 'CSV'),
            'remarks' => $this->nullableValue($data['remarks'] ?? null),
            'items' => [[
                'item_code' => $this->nullableValue($data['item_code'] ?? null),
                'description' => $description,
                'quantity' => $quantity,
                'unit_code' => trim((string) ($data['unit_code'] ?? '')),
                'unit_price' => $unitPrice,
                'discount_amount' => $discount,
                'tax_type' => $this->nullableValue($data['tax_type'] ?? null),
                'tax_category' => $this->nullableValue($data['tax_category'] ?? null),
                'tax_rate' => $taxRate,
            ]],
        ];
    }

    private function nullableValue(mixed $value): ?string
    {
        if ($value === null) return null;

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function cleanRow(array $row): array
    {
        return array_map(
            fn ($value) => trim((string) $value, " \t\n\r\0\x0B\xEF\xBB\xBF"),
            $row
        );
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') return false;
        }

        return true;
    }
}
