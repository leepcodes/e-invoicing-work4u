<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;

class InvoiceItem extends Model
{
      use HasFactory;

    protected $fillable = [
        //Invoice
        'invoice_id',

        //item id
        'item_id',
        //Item
        'line_number',
        'item_code',
        'description',
        'quantity',
        'unit_code',
        'unit_price',

        //Amounts
        'gross_amount',
        'discount_amount',
        'net_amount',

        //Tax
        'tax_type',
        'tax_category',
        'tax_rate',
        'taxable_amount',
        'tax_amount',

        //Final Line Total
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',

        'unit_price' => 'decimal:2',

        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',

        'tax_rate' => 'decimal:4',
        'taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',

        'line_total' => 'decimal:2',
    ];

    //Invoice -> Invoiceitem belongs to Invoice
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
