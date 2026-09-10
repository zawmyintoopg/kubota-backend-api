<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'product_name', 'part_number','quantity', 'unit_cost','amount'
    ];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function product() {
        return $this->belongsTo(Item::class);
    }
}
