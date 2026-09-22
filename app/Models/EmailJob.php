<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailJob extends Model
{
    protected $fillable = [
        'seller_id',
        'buyer_id',
        'email',
        'subject',
        'status',
        'total_items',
        'sent_items',
        'failed_items',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EmailJobItem::class);
    }
}
