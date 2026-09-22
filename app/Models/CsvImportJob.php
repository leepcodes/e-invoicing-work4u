<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsvImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',

        'file_name',
        'file_path',

        'status',

        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',

        'error_message',

        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'seller_id' => 'integer',

        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'successful_rows' => 'integer',
        'failed_rows' => 'integer',

        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
