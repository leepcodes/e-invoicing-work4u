<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Invoice extends Model
    {
        use HasFactory;

        protected $fillable = [

            //Ownership
            'seller_id',
            'buyer_id',

            // Buyer Snapshot
            'buyer_name',
            'buyer_tin',
            'buyer_trade_name',
            'buyer_address_line_1',
            'buyer_address_line_2',
            'buyer_barangay',
            'buyer_city',
            'buyer_province',
            'buyer_postal_code',
            'buyer_country_code',
            'buyer_email',
            'buyer_phone',

            //Document
            'document_type',
            'invoice_number',
            'invoice_date',
            'invoice_time',
            'due_date',

            'reference_number',
            'purchase_order_number',

            //Currency
            'currency_code',
            'accounting_currency_code',
            'exchange_rate',
            'exchange_rate_date',
            'exchange_rate_source',

            //Invoice Totals
            'gross_amount',
            'discount_amount',
            'taxable_amount',
            'vat_amount',
            'withholding_tax_amount',
            'other_tax_amount',
            'net_amount',
            'total_amount',
            'amount_paid',
            'amount_due',

            //Accounting Totals
            'accounting_gross_amount',
            'accounting_taxable_amount',
            'accounting_vat_amount',
            'accounting_total_amount',
            'accounting_amount_due',

            //Payment
            'payment_terms',
            'payment_method',
            'payment_status',

            //System
            'created_by',
            'system_branch_code',
            'fiscal_year',
            'source',
            'remarks',
        ];

        protected $casts = [
            'invoice_date' => 'date',
            'invoice_time' => 'datetime:H:i:s',
            'due_date' => 'date',

            'exchange_rate_date' => 'date',

            'exchange_rate' => 'decimal:8',

            'gross_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'withholding_tax_amount' => 'decimal:2',
            'other_tax_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',

            'accounting_gross_amount' => 'decimal:2',
            'accounting_taxable_amount' => 'decimal:2',
            'accounting_vat_amount' => 'decimal:2',
            'accounting_total_amount' => 'decimal:2',
            'accounting_amount_due' => 'decimal:2',
        ];

        //Seller -> Invoice belongs to seller
        public function seller(): BelongsTo
        {
            return $this->belongsTo(Seller::class);
        }

        //Buyer -> Invoice Belongs to buyer
        public function buyer(): BelongsTo
        {
            return $this->belongsTo(Buyer::class);
        }

        //Invoice Items -> Invoice has many invoiceitem
        public function items(): HasMany
        {
            return $this->hasMany(InvoiceItem::class);
        }

        //Created By
        public function creator(): BelongsTo
        {
            return $this->belongsTo(Seller::class, 'registered_name');
        }

    }
