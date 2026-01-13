<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'is_active' => true],
            ['name' => 'Minuman', 'is_active' => true],
            ['name' => 'Snack', 'is_active' => false],
            ['name' => 'Sembako', 'is_active' => true],
            ['name' => 'Alat Tulis', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
