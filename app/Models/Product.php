<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'part_number',
        'name',
        'description',
        'category_id',
        'brand',
        'price',
        'stock_quantity',
        'low_stock_threshold',
        'featured',
        'status'
    ];

    // Optional: relation to category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function brand()
    {
        return $this->hasMany(Brand::class);
    }
}
