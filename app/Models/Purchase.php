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
        'purchase_date', 'voucher_no', 'supplier_id', 'payment_method_id',
        'transaction_type_id', 'user_id', 'shift_id',
        'sub_total', 'discount_amount', 'tax_amount', 'grand_total',
        'paid_amount', 'balance_amount', 'status'
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function paymentMethod() {
        return $this->belongsTo(paymentMethod::class);
    }
     public function items() {
        return $this->belongsTo(Item::class);
    }
    
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }
}
