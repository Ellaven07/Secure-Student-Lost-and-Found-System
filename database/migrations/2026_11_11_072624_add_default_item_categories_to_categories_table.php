<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Phones, laptops, tablets, chargers, headphones, and other electronic devices.'],
            ['name' => 'IDs and Cards', 'description' => 'Student IDs, school cards, licenses, and other identification or access cards.'],
            ['name' => 'Bags', 'description' => 'Backpacks, purses, wallets, pouches, and other bags.'],
            ['name' => 'Clothing and Uniforms', 'description' => 'Clothes, jackets, uniforms, and wearable items.'],
            ['name' => 'Books and School Supplies', 'description' => 'Books, notebooks, stationery, and classroom materials.'],
            ['name' => 'Keys', 'description' => 'Keys, keychains, and key cards.'],
            ['name' => 'Accessories', 'description' => 'Jewelry, watches, glasses, umbrellas, and personal accessories.'],
            ['name' => 'Appliances', 'description' => 'Small appliances and electronic equipment.'],
            ['name' => 'Sports and Recreation', 'description' => 'Sports equipment, recreation gear, and related items.'],
            ['name' => 'Other', 'description' => 'Items that do not fit another category.'],
        ];

        $now = now();

        DB::table('categories')->insertOrIgnore(array_map(
            fn (array $category): array => [...$category, 'created_at' => $now, 'updated_at' => $now],
            $categories,
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep categories in place because reports may reference them after seeding.
    }
};
