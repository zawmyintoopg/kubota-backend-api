<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'unit_name',
        'to_base',
        'remarks',
        'status'
    ];
}
