<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['code', 'name', 'phone_code', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }

    public function buyers(): HasMany
    {
        return $this->hasMany(Buyer::class);
    }
}
