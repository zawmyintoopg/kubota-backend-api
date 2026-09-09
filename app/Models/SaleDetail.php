<?php
namespace App\Models;

use App\Models\Item;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'item_id', 'quantity', 'unit_price','discount_amount', 'total_price'
    ];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }
}
