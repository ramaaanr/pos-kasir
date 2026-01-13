<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryLog extends Model
{
    protected $fillable = ['category_id', 'action', 'old_value', 'new_value'];
}
