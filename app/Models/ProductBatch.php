<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'is_bonus',
        'bonus_note',
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
        'is_bonus'            => 'boolean',
        'tanggal_masuk'       => 'date',
        'harga_beli_per_unit' => 'float',
        'harga_jual_per_unit' => 'float',
        'qty_masuk_base'      => 'integer',
        'qty_sisa_base'       => 'integer',
        'qty_masuk_original'  => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adjustmentItems()
    {
        return $this->hasMany(StockAdjustmentItem::class, 'product_batch_id');
    }
}
