<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'input_unit_name',
        'qty_masuk_original',
        'batch_code',
        'harga_beli_per_unit',
        'harga_jual_per_unit',
        'qty_masuk_base',
        'qty_sisa_base',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
