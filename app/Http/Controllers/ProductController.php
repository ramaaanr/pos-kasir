<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, ProductCategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the products.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    /**
     * Print product barcode.
     */
    public function printBarcode(int $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.barcode', compact('product'));
    }
}
