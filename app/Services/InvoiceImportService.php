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

    //handles CSV upload and dispatches the import job
    public function import(Request $request): CsvImportJob
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:51200']]);

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

        ImportInvoicesCsvJob::dispatch(
            $importJob->id,
            0,
            $sellerId,
            $userId
        );

        return $importJob;
    }

    //processes CSV rows in chunks
    public function processChunk(CsvImportJob $importJob, int $offset, int $chunkSize = self::CHUNK_SIZE, ?int $sellerId = null, ?int $userId = null): bool
    {
        $sellerId ??= $importJob->seller_id;

        if (! $sellerId) throw new RuntimeException('Seller account could not be determined for this import job.');
        if (! $userId) throw new RuntimeException('User account could not be determined for this import job.');
        if (! Storage::disk('local')->exists($importJob->file_path)) throw new RuntimeException('The uploaded CSV file could not be found.');

        $path = Storage::disk('local')->path($importJob->file_path);
        $handle = fopen($path, 'r');

        if ($handle === false) throw new RuntimeException('Unable to open the CSV file.');

        try {
            $header = fgetcsv($handle);
            if ($header === false) throw new RuntimeException('The CSV file is empty.');

            $header = array_map(fn ($value) => trim((string) $value), $header);
            $this->validateHeader($header);

            $currentRow = 0;
            while ($currentRow < $offset) {
                $row = fgetcsv($handle);
                if ($row === false) { fclose($handle); return false; }
                if ($this->isEmptyRow($row)) continue;
                $currentRow++;
            }

            $rows = [];
            while (count($rows) < $chunkSize && ($row = fgetcsv($handle)) !== false) {
                if ($this->isEmptyRow($row)) continue;
                $rows[] = $row;
            }

            $hasMoreRows = count($rows) === $chunkSize;
            if (empty($rows)) { fclose($handle); return false; }

            $processed = $successful = $failed = $skipped = 0;

            foreach ($rows as $row) {
                $processed++;
                $data = [];

                try {
                    $data = $this->mapRow($header, $row);

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
                    $invoiceData = $this->prepareInvoiceData($data, $sellerId, $buyer->id);

                    app(InvoiceService::class)->create(
                        $invoiceData,
                        $userId
                    );

                    $successful++;
                } catch (Throwable $e) {
                    $failed++;
                    $errorMessage = "Row {$processed}: {$e->getMessage()}";
                    $existingErrors = $importJob->error_message;

                    $importJob->update([
                        'error_message' => $existingErrors ? $existingErrors . PHP_EOL . $errorMessage : $errorMessage,
                    ]);

                    Log::error('Invoice CSV import failed', [
                        'import_job_id' => $importJob->id,
                        'seller_id' => $sellerId,
                        'user_id' => $userId,
                        'row' => $processed,
                        'data' => $data ?: $row,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
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

    //validates/converts dates
    private function normalizeDate(mixed $value, string $fieldName): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable $e) {
            throw new RuntimeException(
                "Invalid {$fieldName} '{$value}'. Expected format: YYYY-MM-DD."
            );
        }
    }

    //checks duplicate invoices
    private function isDuplicateRow(array $data, int $sellerId): bool
    {
        $tin = trim((string) ($data['tin'] ?? ''));
        $referenceNumber = trim((string) ($data['reference_number'] ?? ''));
        $rawInvoiceDate = trim((string) ($data['invoice_date'] ?? ''));

        if ($tin === '' || $referenceNumber === '' || $rawInvoiceDate === '') return false;

        try {
            $invoiceDate = Carbon::parse($rawInvoiceDate)->format('Y-m-d');
        } catch (Throwable $e) {
            return false;
        }

        return Invoice::query()
            ->where('invoices.seller_id', $sellerId)
            ->where('invoices.reference_number', $referenceNumber)
            ->whereDate('invoices.invoice_date', $invoiceDate)
            ->whereHas('buyer', fn ($q) => $q->where('tin', $tin))
            ->exists();
    }

    //finds the buyer
    private function findBuyer(array $data, int $sellerId): Buyer
    {
        $tin = trim((string) ($data['tin'] ?? ''));
        $registeredName = trim((string) ($data['registered_name'] ?? ''));

        if ($tin === '') throw new RuntimeException('Buyer TIN is required.');
        if ($registeredName === '') throw new RuntimeException('Buyer registered name is required.');

        $buyer = Buyer::query()
            ->where('seller_id', $sellerId)
            ->where('tin', $tin)
            ->whereRaw('LOWER(TRIM(registered_name)) = ?', [strtolower($registeredName)])
            ->first();

        if (! $buyer) throw new RuntimeException("Buyer not found for TIN '{$tin}' and registered name '{$registeredName}'.");

        return $buyer;
    }

    //prepares invoice data
    private function prepareInvoiceData(array $data, int $sellerId, int $buyerId): array
    {

        $description = trim((string) ($data['description'] ?? ''));
        if ($description === '') throw new RuntimeException('Item description is required.');

        $quantity = (float) ($data['quantity'] ?? 0);
        if ($quantity <= 0) throw new RuntimeException('Quantity must be greater than zero.');

        $unitPrice = (float) ($data['unit_price'] ?? 0);
        if ($unitPrice < 0) throw new RuntimeException('Unit price cannot be negative.');

        $discount = (float) ($data['discount_amount'] ?? 0);
        if ($discount < 0) throw new RuntimeException('Discount cannot be negative.');

        $taxRate = (float) ($data['tax_rate'] ?? 0);
        if ($taxRate < 0) throw new RuntimeException('Tax rate cannot be negative.');

        $amountPaid = (float) ($data['amount_paid'] ?? 0);

        if ($amountPaid < 0) throw new RuntimeException('Amount paid cannot be negative.');

        $paymentStatus = strtoupper(
            trim((string) ($data['payment_status'] ?? ''))
        );

        if ($paymentStatus === '') {
            $paymentStatus = 'UNPAID';
        }

        $currencyCode = strtoupper(trim((string) ($data['currency_code'] ?? 'PHP')));

        if (strlen($currencyCode) !== 3) throw new RuntimeException('Currency code must be exactly 3 characters.');

        return [
            'seller_id' => $sellerId,
            'buyer_id' => $buyerId,
            'document_type' => $this->nullableValue($data['document_type'] ?? 'INVOICE'),

           'invoice_date' => $this->normalizeDate(
                $data['invoice_date'] ?? now()->format('Y-m-d'),
                'invoice date'
            ),
            'invoice_time' => $this->nullableValue($data['invoice_time'] ?? null),
            'due_date' => $this->normalizeDate(
                $data['due_date'] ?? null,
                'due date'
            ),

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

    //validates required CSV columns
    private function validateHeader(array $header): void
    {
        $requiredColumns = [
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

        foreach ($requiredColumns as $column) {
            if (! in_array($column, $header, true)) throw new RuntimeException("Missing required CSV column: {$column}");
        }
    }

    //maps CSV columns to row data
    private function mapRow(array $header, array $row): array
    {
        $row = array_pad($row, count($header), null);
        $row = array_slice($row, 0, count($header));

        return array_combine($header, $row);
    }

    //converts empty values to null
    private function nullableValue(mixed $value): ?string
    {
        if ($value === null) return null;
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    //checks whether a row is empty
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') return false;
        }

        return true;
    }
}
