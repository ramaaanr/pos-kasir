<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'jaminan',
        'amount',
        'status',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function getTotalPaidAttribute(): int
    {
        return (int) $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute(): int
    {
        return (int) ($this->amount - $this->total_paid);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
