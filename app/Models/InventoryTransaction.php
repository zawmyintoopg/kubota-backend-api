<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    //
    protected $fillable = [
        'proudct_id',
        'transaction_type',
        'quantity',
        'reference_type',
        'reference_id',
        'note',
        'created_by'
    ];
}
