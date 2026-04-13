<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['pump_name', 'category_id', 'tank_id', 'date'])]
class Pump extends Model
{
    //
}
