<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\SalesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\PurchasesSeeder;
use Database\Seeders\SalesItemSeeder;
use Database\Seeders\PurchaseItemsSeeder;
use Database\Seeders\ProductVariantSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
            $this->call([
                CategorySeeder::class,
                BrandSeeder::class,
                UnitSeeder::class,
                ProductSeeder::class,
                ProductVariantSeeder::class,
                UserSeeder::class,
                PurchasesSeeder::class,
                PurchaseItemsSeeder::class,
                PaymentMethodSeeder::class,
                SalesItemSeeder::class,
                SalesSeeder::class,
                StockMovementSeeder::class,
                TransacionTypeSeeder::class,
            ]);
        }
    }
