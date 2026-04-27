<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['gas_type', 'date', 'staff_id', 'morning_balance', 'night_balance', 'today_sale', 'amount'])]
class GasDetail extends Model
{
    protected $table = 'gas_data';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
