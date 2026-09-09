<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'orderlevel_qty',
        'onhand_qty',
        'quantity'
    ];

    
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

}

