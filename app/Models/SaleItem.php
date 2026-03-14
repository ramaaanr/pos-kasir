<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_batch_id',
        'qty_base',
        'is_bonus_item',
        'unit_label',
        'unit_multiplier',
        'harga_jual_per_unit',
        'subtotal',
    ];

    protected $casts = [
        'qty_base' => 'integer',
        'is_bonus_item' => 'boolean',
        'unit_multiplier' => 'integer',
        'harga_jual_per_unit' => 'integer',
        'subtotal' => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class);
    }
}
