<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'row_number',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }
}
