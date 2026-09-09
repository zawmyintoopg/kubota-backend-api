<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id', 'item_id', 'quantity', 'unit_price','discount_amount', 'total_price'
    ];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function product() {
        return $this->belongsTo(Item::class);
    }
}
