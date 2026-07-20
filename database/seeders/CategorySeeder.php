<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Toko ini menjual berbagai macam perhiasan emas (Cincin, Kalung, Gelang, Anting)
        $categories = [
            ['name' => 'Cincin Emas', 'slug' => 'cincin-emas', 'icon' => '💍', 'sort_order' => 1],
            ['name' => 'Kalung Emas', 'slug' => 'kalung-emas', 'icon' => '📿', 'sort_order' => 2],
            ['name' => 'Gelang Emas', 'slug' => 'gelang-emas', 'icon' => '⌚', 'sort_order' => 3],
            ['name' => 'Anting Emas', 'slug' => 'anting-emas', 'icon' => '✨', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
