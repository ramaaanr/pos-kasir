<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    protected $fillable = [
        'debt_id',
        'customer_id',
        'user_id',
        'amount',
        'payment_method',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
