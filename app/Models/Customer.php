<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'customer_name',
        'phone',
        'address',
        'contact_person',
        'status',
    ];
}
