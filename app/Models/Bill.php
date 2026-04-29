<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['staff_id', 'date', 'company_id', 'invoice_number', 'category_id', 'price', 'liters', 'bill_value'])]
class Bill extends Model
{
    protected $table = 'bill';

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'price' => 'decimal:2',
            'liters' => 'decimal:4',
            'bill_value' => 'decimal:4',
        ];
    }
}
