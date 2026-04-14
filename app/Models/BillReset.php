<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'reset_date', 'reset_include'])]
class BillReset extends Model
{
    protected $table = 'bill_reset';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function casts(): array
    {
        return [
            'reset_date' => 'date',
            'reset_include' => 'integer',
        ];
    }
}
