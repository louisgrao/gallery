<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $sale = Category::create(['title' => 'Sale', 'parent_id' => 0]);
        $watches = Category::create(['title' => 'Watches', 'parent_id' => 0]);
        $accessories = Category::create(['title' => 'Accessories', 'parent_id' => 0]);

        // 2. Create Product 1: The Watch (With multiple variants and an addon)
        $watch = Product::create([
            'title' => 'Watch',
            'description' => '<p>Meet our special watch! It\'s made of strong metal and is perfect for anyone who loves watches that can do cool things.</p>',
            'url_slug' => 'smart-watch',
            'base_price' => 29.99,
            'sales_count' => 15,
            'product_status' => true,
        ]);
        
        $watch->categories()->attach([$sale->id, $watches->id]);

        // Create Physical Variants for the Watch
        $watch->variants()->createMany([
            [
                'title' => 'Small - Standard - Red',
                'sku' => 'watch-sm-std-red',
                'price' => 30.99, // Base 29.99 + 1.00 Red color modifier
                'quantity' => 50,
                'weight' => 9.99,
                'attributes' => ['Size' => 'Small', 'Type' => 'Standard', 'Color' => 'Red'],
            ],
            [
                'title' => 'Large - Deluxe - Blue',
                'sku' => 'watch-lg-dlx-blue',
                'price' => 51.99, // Base 29.99 + 8.99 Large + 10.00 Deluxe + 3.00 Blue
                'quantity' => 25,
                'weight' => 8.99,
                'attributes' => ['Size' => 'Large', 'Type' => 'Deluxe', 'Color' => 'Blue'],
            ],
        ]);

        // Create an Addon for the Watch
        $watch->addons()->create([
            'name' => 'Delivery Date',
            'type' => 'datetime',
            'price_modifier' => 5.00,
            'is_required' => false,
        ]);

        // 3. Create Product 2: The Wallet (Simple product, one default variant)
        $wallet = Product::create([
            'title' => 'Wallet',
            'description' => '<p>Discover our sleek black wallet, a must-have accessory that combines simplicity with practicality.</p>',
            'url_slug' => 'black-wallet',
            'base_price' => 14.99,
            'sales_count' => 42,
            'product_status' => true,
        ]);

        $wallet->categories()->attach([$sale->id, $accessories->id]);

        // A simple product still needs one variant to hold stock and exact price
        $wallet->variants()->create([
            'title' => 'Default',
            'sku' => 'wallet-blk',
            'price' => 14.99,
            'quantity' => -1, // Unlimited
            'weight' => 0.00,
            'attributes' => null,
        ]);
    }
}