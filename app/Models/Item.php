<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'seller_id',
        'item_code',
        'description',
        'unit_code',
        'unit_price',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
