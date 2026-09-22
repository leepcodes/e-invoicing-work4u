<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Buyer extends Model
{
     use HasFactory;

    protected $fillable = [
        'seller_id',

        'tin',
        'registered_name',
        'trade_name',
        'customer_code',

        'address_line_1',
        'address_line_2',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country_code',

        'email',
        'phone',
    ];

    //Seller -> Belongs To Seller
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //Invoice -> Buyer Has Many Invoice
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

}


