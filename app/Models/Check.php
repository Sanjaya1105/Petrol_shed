<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['staff_id', 'date', 'company', 'check_date', 'amount'])]
class Check extends Model
{
    protected $table = 'checks';

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_date' => 'date',
            'amount' => 'decimal:3',
        ];
    }
}
