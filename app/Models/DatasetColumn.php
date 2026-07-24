<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'column_name',
        'label',
        'data_type',
        'decimals',
        'measure',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }
}
