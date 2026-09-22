<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'currency_code',
        'currency_name',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
