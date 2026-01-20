<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductBatchController extends Controller
{
    /**
     * Display a listing of the product batches.
     */
    public function index()
    {
        return view('admin.product-batches.index');
    }
}
