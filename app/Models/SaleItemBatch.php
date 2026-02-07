<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItemBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_item_id',
        'product_batch_id',
        'qty_base',
        'harga_beli_per_unit',
    ];

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class);
    }
}
