<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['pump_id', 'staff_id', 'meter_amount', 'date'])]
class Sale extends Model
{
    //
}
