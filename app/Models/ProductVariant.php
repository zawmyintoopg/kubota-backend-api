<?php

namespace App\Models;

use App\Models\Stock;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'variant_name',
        'sell_price',
        'last_purchase_price',
        'avg_purchase_price',
        'last_purchase_date',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function stock()
    {
        return $this->hasOne(Stock::class, 'variant_id', 'id');
    }

}

