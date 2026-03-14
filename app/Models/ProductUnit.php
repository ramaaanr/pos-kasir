<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'label',
        'multiplier',
        'harga_jual',
        'harga_beli',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
