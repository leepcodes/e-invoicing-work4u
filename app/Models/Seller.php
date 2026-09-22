<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'tin',
        'registered_name',
        'trade_name',
        'branch_code',

        'address_line_1',
        'address_line_2',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country_code',

        'company_email',
        'phone',

        'logo',
    ];

    //User -> Seller Belong To One User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //Buyer -> Seller Has Many Buyer
    public function buyers(): HasMany
    {
        return $this->hasMany(Buyer::class);
    }

    //Invoice -> Seller Has Many invoices
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    //Subcription
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    //Items
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
