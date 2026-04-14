<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['staff_id', 'date', 'category', 'cash_values', 'cash_total'])]
class CashCollection extends Model
{
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'cash_values' => 'array',
            'category' => 'string',
            'cash_total' => 'decimal:2',
        ];
    }
}
