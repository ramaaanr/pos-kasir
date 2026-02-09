<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftReport extends Model
{
    protected $fillable = [
        'user_id',
        'opening_cash',
        'cash_received',
        'cash_in_drawer',
        'difference',
        'note',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'opening_cash' => 'integer',
        'cash_received' => 'integer',
        'cash_in_drawer' => 'integer',
        'difference' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
