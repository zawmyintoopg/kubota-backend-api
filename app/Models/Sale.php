<?php
namespace App\Models;

use App\Models\Item;
use App\Models\User;
use App\Models\Customer;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date', 'voucher_no', 'customer_id', 'payment_method_id',
        'transaction_type_id', 'user_id', 'shift_id',
        'sub_total', 'discount_amount', 'tax_amount', 'grand_total',
        'paid_amount', 'balance_amount', 'status'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
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
    
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }
}
