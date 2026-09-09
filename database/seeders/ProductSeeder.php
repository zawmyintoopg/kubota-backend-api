<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Sample products
        $products = [
            [
                'product_code' => 'TSHIRT001',
                'name' => 'Cotton T-Shirt',
                'category_id' => 4, // T-Shirts
                'brand_id' => 1,    // Zara
                'image' => null,
                'description' => 'Comfortable cotton t-shirt for daily wear',
                'base_unit_id' => 1, // Pcs
                'status' => 'active',
            ],
            [
                'product_code' => 'JEANS001',
                'name' => 'Denim Jeans',
                'category_id' => 6, // Pants & Jeans
                'brand_id' => 6,    // Levi's
                'image' => null,
                'description' => 'Classic blue denim jeans',
                'base_unit_id' => 1, // Pcs
                'status' => 'active',
            ],
            [
                'product_code' => 'DRESS001',
                'name' => 'Summer Dress',
                'category_id' => 7, // Dresses
                'brand_id' => 2,    // H&M
                'image' => null,
                'description' => 'Light and airy summer dress',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'SKIRT001',
                'name' => 'Mini Skirt',
                'category_id' => 8, // Skirts
                'brand_id' => 3,    // Uniqlo
                'image' => null,
                'description' => 'Stylish mini skirt for casual wear',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'JACKET001',
                'name' => 'Leather Jacket',
                'category_id' => 9, // Jackets
                'brand_id' => 4,    // Nike
                'image' => null,
                'description' => 'Premium leather jacket for all seasons',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'HOODIE001',
                'name' => 'Hoodie Sweatshirt',
                'category_id' => 10, // Hoodies & Sweatshirts
                'brand_id' => 5,     // Adidas
                'image' => null,
                'description' => 'Comfortable hoodie for sports and casual',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'BOXTSHIRT001',
                'name' => 'Box of T-Shirts',
                'category_id' => 4,
                'brand_id' => 1,
                'image' => null,
                'description' => '6 pcs of Cotton T-Shirts',
                'base_unit_id' => 2, // Box = 6 Pcs
                'status' => 'active',
            ],
            [
                'product_code' => 'PACKJEANS001',
                'name' => 'Pack of Jeans',
                'category_id' => 6,
                'brand_id' => 6,
                'image' => null,
                'description' => '12 pcs of Denim Jeans',
                'base_unit_id' => 3, // Packen = 12 Pcs
                'status' => 'active',
            ],
            [
                'product_code' => 'TSHIRT002',
                'name' => 'V-Neck T-Shirt',
                'category_id' => 4,
                'brand_id' => 7, // Puma
                'image' => null,
                'description' => 'V-Neck cotton T-Shirt',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'DRESS002',
                'name' => 'Evening Dress',
                'category_id' => 7,
                'brand_id' => 8, // Forever 21
                'image' => null,
                'description' => 'Elegant evening dress',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'SHIRT001',
                'name' => 'Formal Shirt',
                'category_id' => 5,
                'brand_id' => 9, // Gap
                'image' => null,
                'description' => 'Men formal shirt for office wear',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'COAT001',
                'name' => 'Winter Coat',
                'category_id' => 11,
                'brand_id' => 10, // Tommy Hilfiger
                'image' => null,
                'description' => 'Warm winter coat',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'SPORT001',
                'name' => 'Sports Shorts',
                'category_id' => 10,
                'brand_id' => 5, // Adidas
                'image' => null,
                'description' => 'Shorts for sports',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'TSHIRT003',
                'name' => 'Graphic T-Shirt',
                'category_id' => 4,
                'brand_id' => 2, // H&M
                'image' => null,
                'description' => 'Printed graphic t-shirt',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'SKIRT002',
                'name' => 'Pleated Skirt',
                'category_id' => 8,
                'brand_id' => 3, // Uniqlo
                'image' => null,
                'description' => 'Pleated skirt for women',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'JACKET002',
                'name' => 'Bomber Jacket',
                'category_id' => 9,
                'brand_id' => 4, // Nike
                'image' => null,
                'description' => 'Trendy bomber jacket',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'HOODIE002',
                'name' => 'Zip Hoodie',
                'category_id' => 10,
                'brand_id' => 7, // Puma
                'image' => null,
                'description' => 'Zip-up hoodie',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'TSHIRT004',
                'name' => 'Round Neck T-Shirt',
                'category_id' => 4,
                'brand_id' => 6, // Levi's
                'image' => null,
                'description' => 'Round neck casual t-shirt',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'DRESS003',
                'name' => 'Cocktail Dress',
                'category_id' => 7,
                'brand_id' => 8, // Forever 21
                'image' => null,
                'description' => 'Cocktail party dress',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
            [
                'product_code' => 'PANTS001',
                'name' => 'Chinos Pants',
                'category_id' => 6,
                'brand_id' => 9, // Gap
                'image' => null,
                'description' => 'Casual chinos pants',
                'base_unit_id' => 1,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
