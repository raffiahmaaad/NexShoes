<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $sportCategory = Category::where('slug', 'sepatu-olahraga')->first();
        $casualCategory = Category::where('slug', 'sepatu-kasual')->first();
        $sneakersCategory = Category::where('slug', 'sepatu-sneakers')->first();
        $canvasCategory = Category::where('slug', 'sepatu-canvas')->first();

        // Get brand IDs
        $nikeBrand = Brand::where('slug', 'nike')->first();
        $adidasBrand = Brand::where('slug', 'adidas')->first();
        $converseBrand = Brand::where('slug', 'converse')->first();
        $vansBrand = Brand::where('slug', 'vans')->first();
        $newBalanceBrand = Brand::where('slug', 'new-balance')->first();
        $pumaBrand = Brand::where('slug', 'puma')->first();
        $onitsukaBrand = Brand::where('slug', 'onitsuka-tiger')->first();
        $reebokBrand = Brand::where('slug', 'reebok')->first();
        $skechersBrand = Brand::where('slug', 'skechers')->first();
        $filaBrand = Brand::where('slug', 'fila')->first();
        $compassBrand = Brand::where('slug', 'compass')->first();
        $ventelaBrand = Brand::where('slug', 'ventela')->first();

        $products = [
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Air Force 1 Low White',
                'slug' => 'nike-air-force-1-low-white',
                'description' => 'Sepatu basket klasik dengan desain ikonik yang telah menjadi legenda street fashion.',
                'price' => 1299000,
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Stan Smith Original',
                'slug' => 'adidas-stan-smith-original',
                'description' => 'Tennis shoe legendaris dengan desain minimalis yang cocok untuk penggunaan sehari-hari.',
                'price' => 899000,
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $canvasCategory->id,
                'brand_id' => $converseBrand->id,
                'name' => 'Converse Chuck Taylor All Star High',
                'slug' => 'converse-chuck-taylor-all-star-high',
                'description' => 'Sepatu canvas high-top klasik dengan gaya retro yang timeless.',
                'price' => 699000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $vansBrand->id,
                'name' => 'Vans Old Skool Black White',
                'slug' => 'vans-old-skool-black-white',
                'description' => 'Sepatu skate ikonik dengan side stripe yang terkenal, cocok untuk streetwear.',
                'price' => 799000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sportCategory->id,
                'brand_id' => $newBalanceBrand->id,
                'name' => 'New Balance 574 Core',
                'slug' => 'new-balance-574-core',
                'description' => 'Running shoe dengan teknologi ENCAP untuk kenyamanan maksimal sepanjang hari.',
                'price' => 1099000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma Suede Classic',
                'slug' => 'puma-suede-classic',
                'description' => 'Sepatu suede klasik dengan desain retro yang cocok untuk casual wear.',
                'price' => 649000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'category_id' => $sportCategory->id,
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Air Max 90 Essential',
                'slug' => 'nike-air-max-90-essential',
                'description' => 'Sepatu running dengan teknologi Air Max yang memberikan bantalan optimal.',
                'price' => 1599000,
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sportCategory->id,
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Ultraboost 22',
                'slug' => 'adidas-ultraboost-22',
                'description' => 'Sepatu running premium dengan teknologi Boost untuk energy return terbaik.',
                'price' => 2299000,
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $converseBrand->id,
                'name' => 'Converse One Star Pro',
                'slug' => 'converse-one-star-pro',
                'description' => 'Sepatu skate dengan desain one star yang ikonik dan durable.',
                'price' => 899000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $vansBrand->id,
                'name' => 'Vans Authentic Platform',
                'slug' => 'vans-authentic-platform',
                'description' => 'Sepatu canvas dengan platform yang memberikan tinggi ekstra dengan gaya kasual.',
                'price' => 849000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $newBalanceBrand->id,
                'name' => 'New Balance 327 Retro',
                'slug' => 'new-balance-327-retro',
                'description' => 'Sepatu lifestyle dengan inspirasi vintage 70an dan teknologi modern.',
                'price' => 1199000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma RS-X Reinvention',
                'slug' => 'puma-rs-x-reinvention',
                'description' => 'Sepatu chunky dengan teknologi RS untuk kenyamanan dan gaya futuristik.',
                'price' => 1399000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Dunk Low Panda',
                'slug' => 'nike-dunk-low-panda',
                'description' => 'Sepatu basketball retro dengan colorway hitam putih yang populer.',
                'price' => 1799000,
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Gazelle Bold',
                'slug' => 'adidas-gazelle-bold',
                'description' => 'Sepatu lifestyle dengan platform yang memberikan twist modern pada desain klasik.',
                'price' => 1099000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $onitsukaBrand->id,
                'name' => 'Onitsuka Tiger Mexico 66',
                'slug' => 'onitsuka-tiger-mexico-66',
                'description' => 'Sepatu heritage dengan desain ikonik stripes dan comfort yang luar biasa.',
                'price' => 1299000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $reebokBrand->id,
                'name' => 'Reebok Classic Leather',
                'slug' => 'reebok-classic-leather',
                'description' => 'Sepatu lifestyle klasik dengan upper leather berkualitas tinggi.',
                'price' => 749000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $skechersBrand->id,
                'name' => 'Skechers D\'Lites Fresh Start',
                'slug' => 'skechers-dlites-fresh-start',
                'description' => 'Sepatu chunky dengan sole tebal untuk kenyamanan maksimal sepanjang hari.',
                'price' => 899000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $sneakersCategory->id,
                'brand_id' => $filaBrand->id,
                'name' => 'FILA Disruptor II',
                'slug' => 'fila-disruptor-ii',
                'description' => 'Sepatu chunky dengan desain bold dan sole yang tebal untuk tampilan statement.',
                'price' => 999000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => $casualCategory->id,
                'brand_id' => $compassBrand->id,
                'name' => 'Compass Gazelle Low',
                'slug' => 'compass-gazelle-low',
                'description' => 'Sepatu lokal dengan kualitas internasional, cocok untuk daily wear.',
                'price' => 399000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'category_id' => $canvasCategory->id,
                'brand_id' => $ventelaBrand->id,
                'name' => 'Ventela Public Low',
                'slug' => 'ventela-public-low',
                'description' => 'Sepatu canvas lokal dengan desain minimalis dan harga terjangkau.',
                'price' => 299000,
                'is_active' => true,
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']], // Cari berdasarkan slug
                $product
            );
        }
    }
}
