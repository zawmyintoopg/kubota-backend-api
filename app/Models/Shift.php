<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'transdate',
        'open_time',
        'close_time',
        'opening_cash',
        'closing_cash',
        'cash_sales',
        'card_sales',
        'total_sales',
        'difference',
        'status',
        'edo_status'
    ];

    protected $casts = [
        'open_time'  => 'datetime',
        'close_time' => 'datetime',
    ];

    // 🔗 RELATION
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // 🔗 CASHIER
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
