<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class InvoiceService
{
    public function getCreateData(): array
    {
        return [
            'buyers' => $this->getBuyers(),
            'items' => $this->getItems(),
            'invoiceNumber' => $this->generateInvoiceNumber(),
            'invoiceDate' => now()->format('Y-m-d'),
            'invoiceTime' => now()->format('H:i'),
            'exchangeRateDate' => now()->format('Y-m-d'),
        ];
    }
    public function getBuyers()
    {
        $sellerId = auth()->user()?->seller?->id;

        return Buyer::query()
            ->where('seller_id', $sellerId)
            ->select(['id', 'seller_id', 'registered_name', 'trade_name', 'customer_code'])
            ->orderBy('registered_name')
            ->get();
    }

    public function getItems()
    {
        $sellerId = auth()->user()?->seller?->id;

        return Item::query()
            ->where('seller_id', $sellerId)
            ->select([
                'id',
                'seller_id',
                'item_code',
                'description',
                'unit_code',
                'unit_price',
            ])
            ->orderBy('item_code')
            ->get();
    }

    public function getInvoices(array $filters = [])
    {
        $sellerId = auth()->user()?->seller?->id;

        return Invoice::query()
            ->where('seller_id', $sellerId)
            ->with('buyer:id,registered_name,trade_name,customer_code')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('buyer', fn ($query) => $query->where('registered_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['invoice_date_from'] ?? null, fn ($query, $date) => $query->whereDate('invoice_date', '>=', $date))
            ->when($filters['invoice_date_to'] ?? null, fn ($query, $date) => $query->whereDate('invoice_date', '<=', $date))
            ->when($filters['due_date_from'] ?? null, fn ($query, $date) => $query->whereDate('due_date', '>=', $date))
            ->when($filters['due_date_to'] ?? null, fn ($query, $date) => $query->whereDate('due_date', '<=', $date))
            ->orderByDesc('created_at')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function getInvoiceDetails(Invoice $invoice): Invoice
    {
        return $invoice->load([
            'seller',
            'buyer',
            'items' => fn ($query) => $query->orderBy('line_number'),
        ]);
    }

    public function generateInvoiceNumber(?int $sellerId = null): string
    {
        $year = now()->year;
        $sellerId ??= auth()->user()?->seller?->id;

        if (! $sellerId) {
            throw new \RuntimeException('Seller account could not be determined.');
        }

        $lastInvoice = Invoice::query()
        ->where('seller_id', $sellerId)
        ->where('invoice_number', 'like', "INV-{$year}-%")
        ->orderByDesc('id')
        ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -6)) + 1 : 1;

        return sprintf('INV-%d-%06d', $year, $sequence);
    }

    public function create(array $data, ?int $userId = null): Invoice
    {
            Log::info('InvoiceService::create called', [
            'seller_id_from_data' => $data['seller_id'] ?? null,
            'user_id' => $userId,
            'has_items' => !empty($data['items']),
        ]);

        return DB::transaction(function () use ($data, $userId) {

            $sellerId = $data['seller_id'] ?? auth()->user()?->seller?->id;

            if (! $sellerId) {
                throw new \RuntimeException('Seller account could not be determined.');
            }

            $createdBy = $userId ?? auth()->id();

            if (! $createdBy) {
                throw new \RuntimeException('User account could not be determined.');
            }

            $items = $data['items'] ?? [];

            if (empty($items)) {
                throw new \RuntimeException('Invoice must contain at least one item.');
            }

            $buyer = null;

            if (! empty($data['buyer_id'])) {
                $buyer = Buyer::query()
                    ->where('id', $data['buyer_id'])
                    ->where('seller_id', $sellerId)
                    ->first();

                if (! $buyer) {
                    throw new \RuntimeException('Selected buyer does not belong to this seller.');
                }

                $buyerSnapshot = [
                    'buyer_name' => $buyer->registered_name,
                    'buyer_tin' => $buyer->tin,
                    'buyer_trade_name' => $buyer->trade_name,
                    'buyer_address_line_1' => $buyer->address_line_1,
                    'buyer_address_line_2' => $buyer->address_line_2,
                    'buyer_barangay' => $buyer->barangay,
                    'buyer_city' => $buyer->city,
                    'buyer_province' => $buyer->province,
                    'buyer_postal_code' => $buyer->postal_code,
                    'buyer_country_code' => $buyer->country_code ?? 'PH',
                    'buyer_email' => $buyer->email,
                    'buyer_phone' => $buyer->phone,
                ];
            } else {
                $buyerSnapshot = [
                    'buyer_name' => $data['buyer_name'] ?? null,
                    'buyer_tin' => $data['buyer_tin'] ?? null,
                    'buyer_trade_name' => $data['buyer_trade_name'] ?? null,
                    'buyer_address_line_1' => $data['buyer_address_line_1'] ?? null,
                    'buyer_address_line_2' => $data['buyer_address_line_2'] ?? null,
                    'buyer_barangay' => $data['buyer_barangay'] ?? null,
                    'buyer_city' => $data['buyer_city'] ?? null,
                    'buyer_province' => $data['buyer_province'] ?? null,
                    'buyer_postal_code' => $data['buyer_postal_code'] ?? null,
                    'buyer_country_code' => $data['buyer_country_code'] ?? 'PH',
                    'buyer_email' => $data['buyer_email'] ?? null,
                    'buyer_phone' => $data['buyer_phone'] ?? null,
                ];
            }

            $invoiceNumber = $this->generateInvoiceNumber($sellerId);

            $grossAmount = 0;
            $discountAmount = 0;
            $taxableAmount = 0;
            $vatAmount = 0;
            $netAmount = 0;
            $totalAmount = 0;

            foreach ($items as $index => &$item) {



                $itemId = $item['item_id'] ?? null;

                if ($itemId) {
                    $masterItem = Item::query()
                        ->where('id', $itemId)
                        ->where('seller_id', $sellerId)
                        ->first();

                    if (! $masterItem) {
                        throw new \RuntimeException(
                            'Selected item does not belong to this seller.'
                        );
                    }

                    $item['item_code'] = $masterItem->item_code;
                    $item['description'] = $masterItem->description;
                    $item['unit_code'] = $masterItem->unit_code;

                } else {
                    $item['item_id'] = null;
                }

                $quantity = (float) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $discount = (float) ($item['discount_amount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);
                $taxType = strtoupper(trim((string) ($item['tax_type'] ?? ($taxRate > 0 ? 'VAT' : 'NONE'))));

                if ($quantity <= 0) {
                    throw new \RuntimeException('Quantity must be greater than zero.');
                }

                if ($unitPrice < 0) {
                    throw new \RuntimeException('Unit price cannot be negative.');
                }

                if ($discount < 0) {
                    throw new \RuntimeException('Discount cannot be negative.');
                }

                if ($taxRate < 0) {
                    throw new \RuntimeException('Tax rate cannot be negative.');
                }

                $itemGross = $quantity * $unitPrice;
                $itemNet = max($itemGross - $discount, 0);
                $itemTaxable = $taxType === 'VAT' ? $itemNet : 0;
                $itemTax = $itemTaxable * ($taxRate / 100);
                $itemTotal = $itemNet + $itemTax;

                $item['line_number'] = $index + 1;
                $item['gross_amount'] = round($itemGross, 2);
                $item['discount_amount'] = round($discount, 2);
                $item['net_amount'] = round($itemNet, 2);
                $item['taxable_amount'] = round($itemTaxable, 2);
                $item['tax_amount'] = round($itemTax, 2);
                $item['line_total'] = round($itemTotal, 2);

                $grossAmount += $itemGross;
                $discountAmount += $discount;
                $taxableAmount += $itemTaxable;
                $vatAmount += $itemTax;
                $netAmount += $itemNet;
                $totalAmount += $itemTotal;
            }

            unset($item);

            $amountPaid = (float) ($data['amount_paid'] ?? 0);

            if ($amountPaid < 0) {
                throw new \RuntimeException('Amount paid cannot be negative.');
            }

            $amountDue = max($totalAmount - $amountPaid, 0);
            $exchangeRate = (float) ($data['exchange_rate'] ?? 1);
            $exchangeRate = $exchangeRate > 0 ? $exchangeRate : 1;

            $paymentStatus = match (true) {
                $amountPaid <= 0 => 'UNPAID',
                $amountPaid >= $totalAmount => 'PAID',
                default => 'PARTIALLY PAID',
            };

            $invoice = Invoice::create([
                'seller_id' => $sellerId,
                'buyer_id' => $buyer?->id,
                'created_by' => $createdBy,
                ...$buyerSnapshot,
                'document_type' => $data['document_type'] ?? 'INVOICE',
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $data['invoice_date'] ?? now()->format('Y-m-d'),
                'invoice_time' => $data['invoice_time'] ?? now()->format('H:i'),
                'due_date' => $data['due_date'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'purchase_order_number' => $data['purchase_order_number'] ?? null,
                'currency_code' => $data['currency_code'] ?? 'PHP',
                'accounting_currency_code' => $data['accounting_currency_code'] ?? null,
                'exchange_rate' => $exchangeRate,
                'exchange_rate_date' => $data['exchange_rate_date'] ?? null,
                'exchange_rate_source' => $data['exchange_rate_source'] ?? null,
                'gross_amount' => round($grossAmount, 2),
                'discount_amount' => round($discountAmount, 2),
                'taxable_amount' => round($taxableAmount, 2),
                'vat_amount' => round($vatAmount, 2),
                'withholding_tax_amount' => 0,
                'other_tax_amount' => 0,
                'net_amount' => round($netAmount, 2),
                'total_amount' => round($totalAmount, 2),
                'amount_paid' => round($amountPaid, 2),
                'amount_due' => round($amountDue, 2),
                'accounting_gross_amount' => round($grossAmount * $exchangeRate, 2),
                'accounting_taxable_amount' => round($taxableAmount * $exchangeRate, 2),
                'accounting_vat_amount' => round($vatAmount * $exchangeRate, 2),
                'accounting_total_amount' => round($totalAmount * $exchangeRate, 2),
                'accounting_amount_due' => round($amountDue * $exchangeRate, 2),
                'payment_terms' => $data['payment_terms'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $paymentStatus,
                'system_branch_code' => $data['system_branch_code'] ?? null,
                'fiscal_year' => $data['fiscal_year'] ?? now()->year,
                'source' => $data['source'] ?? 'WEB',
                'remarks' => $data['remarks'] ?? null,
            ]);

            $invoice->items()->createMany($items);

            return $invoice->load('items');
        });
    }

    public function updatePayment(Invoice $invoice, array $data): Invoice
    {
        $amountPaid = (float) $data['amount_paid'];
        $totalAmount = (float) $invoice->total_amount;
        $amountDue = max($totalAmount - $amountPaid, 0);
        $exchangeRate = (float) ($invoice->exchange_rate ?? 1) ?: 1;

        $invoice->update([
            'amount_paid' => round($amountPaid, 2),
            'amount_due' => round($amountDue, 2),
            'accounting_amount_due' => round($amountDue * $exchangeRate, 2),
            'payment_status' => $data['payment_status'],
        ]);

        return $invoice->fresh();
    }

    public function toJson(Invoice $invoice): array
    {
        $invoice = $invoice->load([
            'seller',
            'buyer',
            'items' => fn ($query) => $query->orderBy('line_number'),
        ]);

        return [
            'document' => [
                'document_type' => $invoice->document_type,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                'invoice_time' => $invoice->invoice_time?->format('H:i:s'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'reference_number' => $invoice->reference_number,
                'purchase_order_number' => $invoice->purchase_order_number,
            ],

            'seller' => [
                'tin' => $invoice->seller->tin,
                'registered_name' => $invoice->seller->registered_name,
                'trade_name' => $invoice->seller->trade_name,
                'branch_code' => $invoice->seller->branch_code,

                'business_address' => [
                    'address_line_1' => $invoice->seller->address_line_1,
                    'address_line_2' => $invoice->seller->address_line_2,
                    'barangay' => $invoice->seller->barangay,
                    'city' => $invoice->seller->city,
                    'province' => $invoice->seller->province,
                    'postal_code' => $invoice->seller->postal_code,
                    'country_code' => $invoice->seller->country_code ?? 'PH',
                ],

                'email' => $invoice->seller->company_email,
                'phone' => $invoice->seller->phone,
            ],

            'buyer' => [
                'tin' => $invoice->buyer?->tin ?? $invoice->buyer_tin,
                'registered_name' => $invoice->buyer?->registered_name ?? $invoice->buyer_name,
                'trade_name' => $invoice->buyer?->trade_name ?? $invoice->buyer_trade_name,
                'customer_code' => $invoice->buyer?->customer_code,

                'business_address' => [
                    'address_line_1' => $invoice->buyer?->address_line_1 ?? $invoice->buyer_address_line_1,
                    'address_line_2' => $invoice->buyer?->address_line_2 ?? $invoice->buyer_address_line_2,
                    'barangay' => $invoice->buyer?->barangay ?? $invoice->buyer_barangay,
                    'city' => $invoice->buyer?->city ?? $invoice->buyer_city,
                    'province' => $invoice->buyer?->province ?? $invoice->buyer_province,
                    'postal_code' => $invoice->buyer?->postal_code ?? $invoice->buyer_postal_code,
                    'country_code' => $invoice->buyer?->country_code ?? $invoice->buyer_country_code ?? 'PH',
                ],

                'email' => $invoice->buyer?->email ?? $invoice->buyer_email,
                'phone' => $invoice->buyer?->phone ?? $invoice->buyer_phone,
            ],

            'currency' => [
                'currency_code' => $invoice->currency_code,
                'accounting_currency_code' => $invoice->accounting_currency_code,
                'exchange_rate' => (float) $invoice->exchange_rate,
                'exchange_rate_date' => $invoice->exchange_rate_date?->format('Y-m-d'),
                'exchange_rate_source' => $invoice->exchange_rate_source,
            ],

            'items' => $invoice->items->map(fn ($item) => [
                'item_id' => $item->item_id,
                'line_number' => $item->line_number,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_code' => $item->unit_code,
                'unit_price' => (float) $item->unit_price,
                'gross_amount' => (float) $item->gross_amount,
                'discount_amount' => (float) $item->discount_amount,
                'net_amount' => (float) $item->net_amount,

                'tax' => [
                    'tax_type' => $item->tax_type,
                    'tax_category' => $item->tax_category,
                    'tax_rate' => (float) $item->tax_rate,
                    'taxable_amount' => (float) $item->taxable_amount,
                    'tax_amount' => (float) $item->tax_amount,
                ],

                'line_total' => (float) $item->line_total,
            ])->values()->toArray(),

            'totals' => [
                'gross_amount' => (float) $invoice->gross_amount,
                'discount_amount' => (float) $invoice->discount_amount,
                'taxable_amount' => (float) $invoice->taxable_amount,
                'vat_amount' => (float) $invoice->vat_amount,
                'withholding_tax_amount' => (float) $invoice->withholding_tax_amount,
                'other_tax_amount' => (float) $invoice->other_tax_amount,
                'net_amount' => (float) $invoice->net_amount,
                'total_amount' => (float) $invoice->total_amount,
                'amount_paid' => (float) $invoice->amount_paid,
                'amount_due' => (float) $invoice->amount_due,
            ],

            'accounting_totals' => [
                'gross_amount' => (float) $invoice->accounting_gross_amount,
                'taxable_amount' => (float) $invoice->accounting_taxable_amount,
                'vat_amount' => (float) $invoice->accounting_vat_amount,
                'total_amount' => (float) $invoice->accounting_total_amount,
                'amount_due' => (float) $invoice->accounting_amount_due,
            ],

            'payment' => [
                'payment_terms' => $invoice->payment_terms,
                'payment_method' => $invoice->payment_method,
                'payment_status' => $invoice->payment_status,
            ],

            'system' => [
                'created_by' => $invoice->created_by,
                'branch_code' => $invoice->system_branch_code,
                'fiscal_year' => $invoice->fiscal_year,
                'source' => $invoice->source,
                'remarks' => $invoice->remarks,
            ],
        ];
    }
}
