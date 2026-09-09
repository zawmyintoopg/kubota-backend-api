<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $fillable = ['item_id','movement_type','qty','reference_id','note','balance_after','movement_date','maker_user_id'];

    public function variant() {
        return $this->belongsTo(Item::class,'item_id');
    }
}

