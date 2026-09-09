<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'old_price',
        'new_price',
        'purchase_id',
        'changed_by',
        'source',
        'changed_at',
    ];

    // PriceHistory.php
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public $timestamps = true;
}

