<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_code','item_name','category_id','brand_id','cost','unit_price',
        'unit_qty','unit_id','on_hand_qty','image','pos_color','status'
    ];

    // Item belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Item belongs to a Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
