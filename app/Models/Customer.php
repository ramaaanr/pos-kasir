<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public function debt_payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'total_debt',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}
