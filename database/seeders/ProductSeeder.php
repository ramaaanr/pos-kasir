<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories exist
        $makanan = ProductCategory::where('name', 'Makanan')->first();
        $minuman = ProductCategory::where('name', 'Minuman')->first();
        $atk = ProductCategory::where('name', 'Alat Tulis')->first();

        if (!$makanan || !$minuman || !$atk) {
            $this->call(ProductCategorySeeder::class);
            $makanan = ProductCategory::where('name', 'Makanan')->first();
            $minuman = ProductCategory::where('name', 'Minuman')->first();
            $atk = ProductCategory::where('name', 'Alat Tulis')->first();
        }

        $products = [
            [
                'category_id' => $makanan->id,
                'kode_produk' => 'PRD-ABC123',
                'nama' => 'Indomie Goreng',
                'base_unit' => 'Pcs',
                'harga_beli_default' => 2500,
                'harga_jual_default' => 3500,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'kode_produk' => 'PRD-DEF456',
                'nama' => 'Coca Cola 330ml',
                'base_unit' => 'Botol',
                'harga_beli_default' => 4000,
                'harga_jual_default' => 6000,
                'is_active' => true,
            ],
            [
                'category_id' => $atk->id,
                'kode_produk' => 'PRD-GHI789',
                'nama' => 'Pensil 2B',
                'base_unit' => 'Pcs',
                'harga_beli_default' => 1500,
                'harga_jual_default' => 2500,
                'is_active' => false,
            ],
            [
                'category_id' => $makanan->id,
                'kode_produk' => 'PRD-JKL012',
                'nama' => 'Beras Pandan Wangi 5kg',
                'base_unit' => 'Karung',
                'harga_beli_default' => 65000,
                'harga_jual_default' => 75000,
                'is_active' => true,
            ],
        ];

        // Add 16 more dummy products to reach 20
        $additionalProducts = [
            ['nama' => 'Aqua 600ml', 'cat' => $minuman, 'harga_beli' => 2500, 'harga_jual' => 3500],
            ['nama' => 'Mie Sedap Kuah', 'cat' => $makanan, 'harga_beli' => 2400, 'harga_jual' => 3400],
            ['nama' => 'Buku Tulis Sidu', 'cat' => $atk, 'harga_beli' => 3000, 'harga_jual' => 4500],
            ['nama' => 'Minyak Goreng 1L', 'cat' => $makanan, 'harga_beli' => 14000, 'harga_jual' => 16000],
            ['nama' => 'Teh Botol Sosro', 'cat' => $minuman, 'harga_beli' => 3500, 'harga_jual' => 5000],
            ['nama' => 'Penghapus Joyko', 'cat' => $atk, 'harga_beli' => 1000, 'harga_jual' => 2000],
            ['nama' => 'Gula Pasir 1kg', 'cat' => $makanan, 'harga_beli' => 12500, 'harga_jual' => 14500],
            ['nama' => 'Sprite 330ml', 'cat' => $minuman, 'harga_beli' => 4000, 'harga_jual' => 6000],
            ['nama' => 'Bolpoin Pilot', 'cat' => $atk, 'harga_beli' => 2500, 'harga_jual' => 4000],
            ['nama' => 'Kopi Kapal Api', 'cat' => $makanan, 'harga_beli' => 1500, 'harga_jual' => 2500],
            ['nama' => 'Susu Kental Manis', 'cat' => $makanan, 'harga_beli' => 10000, 'harga_jual' => 12000],
            ['nama' => 'Fanta 330ml', 'cat' => $minuman, 'harga_beli' => 4000, 'harga_jual' => 6000],
            ['nama' => 'Penggaris 30cm', 'cat' => $atk, 'harga_beli' => 2000, 'harga_jual' => 3500],
            ['nama' => 'Garam Dapur', 'cat' => $makanan, 'harga_beli' => 2000, 'harga_jual' => 3000],
            ['nama' => 'Ultra Milk 250ml', 'cat' => $minuman, 'harga_beli' => 5000, 'harga_jual' => 6500],
            ['nama' => 'Tipe-X Kertas', 'cat' => $atk, 'harga_beli' => 5000, 'harga_jual' => 8000],
        ];

        foreach ($additionalProducts as $index => $item) {
            $products[] = [
                'category_id' => $item['cat']->id,
                'kode_produk' => 'PRD-X' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama' => $item['nama'],
                'base_unit' => 'Pcs',
                'harga_beli_default' => $item['harga_beli'],
                'harga_jual_default' => $item['harga_jual'],
                'is_active' => true,
            ];
        }

        foreach ($products as $product) {
            Product::updateOrCreate(['kode_produk' => $product['kode_produk']], $product);
        }
    }
}
