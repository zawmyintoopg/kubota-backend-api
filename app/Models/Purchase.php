<?php
namespace App\Models;

use App\Models\Item;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SaleDetail;
use App\Models\PurchaseDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number','supplier_id','purchase_date', 'subtotal', 'discount_amount', 'total_amount',
        'status', 'note', 'created_by'
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
