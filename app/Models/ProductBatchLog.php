<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatchLog extends Model
{
    protected $fillable = [
        'product_batch_id',
        'user_id',
        'action',
        'qty_change',
        'qty_before',
        'qty_after',
        'description',
    ];

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
