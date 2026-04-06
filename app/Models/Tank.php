<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tank_name', 'tank_capacity', 'category_id'])]
class Tank extends Model
{
    //
}
