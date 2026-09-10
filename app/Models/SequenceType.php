<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SequenceType extends Model
{
    protected $fillable = [
        'sequence_type',
        'prefix',
        'current_number',
        'number_length'
    ];
}
