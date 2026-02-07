<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'total',
        'total_paid',
        'payment_method',
        'status',
        'cash_received',
        'cash_change',
    ];

    protected $casts = [
        'total' => 'integer',
        'total_paid' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function debt()
    {
        return $this->hasOne(Debt::class);
    }
}
