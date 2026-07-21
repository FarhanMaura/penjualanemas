<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Produk toko ini hanya jual emas 24K dalam 5 ukuran:
     *  - 1 Gram
     *  - Setengah Gram (0.5g)
     *  - 1 Suku (0.25g)
     *  - 2 Suku (0.5g) — sama berat dengan setengah gram tapi beda format
     *  - 3 Suku (0.75g)
     *
     * Harga base dihitung dari harga jual seeder ~Rp 1.620.000/gram
     */
    public function run(): void
    {
        $catId = fn(string $slug) => Category::where('slug', $slug)->value('id');        $products = [
            [
                'category_slug' => 'cincin-emas',
                'sku'           => 'JW-CIN-24K-01',
                'name'          => 'Cincin Solitaire Emas Putih',
                'description'   => 'Cincin solitaire emas putih 24 karat (kadar 999) dengan berlian mikro pilihan. Sangat elegan untuk acara formal maupun pertunangan.',
                'purity'        => '24K',
                'weight'        => 2.500,
                'base_price'    => 2250000,
                'stock'         => 12,
                'sort_order'    => 1,
                'images'        => ['/images/products/cincin.png'],
            ],
            [
                'category_slug' => 'cincin-emas',
                'sku'           => 'JW-CIN-24K-02',
                'name'          => 'Cincin Kawin Klasik',
                'description'   => 'Cincin kawin polos klasik dari emas kuning 24 karat (kadar 999). Memiliki kilau hangat tradisional yang abadi dan nyaman digunakan sehari-hari.',
                'purity'        => '24K',
                'weight'        => 4.000,
                'base_price'    => 3920000,
                'stock'         => 8,
                'sort_order'    => 2,
                'images'        => ['/images/products/cincin.png'],
            ],
            [
                'category_slug' => 'kalung-emas',
                'sku'           => 'JW-KAL-24K-01',
                'name'          => 'Kalung Rantai Rolo',
                'description'   => 'Kalung model rantai rolo ramping dari emas murni 24 karat (kadar 999). Fleksibel dan cocok dipadukan dengan liontin favorit Anda.',
                'purity'        => '24K',
                'weight'        => 5.000,
                'base_price'    => 4450000,
                'stock'         => 7,
                'sort_order'    => 3,
                'images'        => ['/images/products/kalung.png'],
            ],
            [
                'category_slug' => 'kalung-emas',
                'sku'           => 'JW-KAL-24K-02',
                'name'          => 'Kalung Emas Murni Liontin',
                'description'   => 'Kalung emas murni berkadar 24 karat (kadar 999) dilengkapi liontin berbentuk hati. Pilihan ekonomis dengan penampilan mewah.',
                'purity'        => '24K',
                'weight'        => 3.500,
                'base_price'    => 2800000,
                'stock'         => 15,
                'sort_order'    => 4,
                'images'        => ['/images/products/kalung.png'],
            ],
            [
                'category_slug' => 'gelang-emas',
                'sku'           => 'JW-GEL-24K-01',
                'name'          => 'Gelang Bangle Kaku',
                'description'   => 'Gelang kaku (bangle) ukir motif tradisional dari emas kuning 24 karat (kadar 999). Kuat, elegan, dan bernilai tinggi.',
                'purity'        => '24K',
                'weight'        => 8.000,
                'base_price'    => 7840000,
                'stock'         => 5,
                'sort_order'    => 5,
                'images'        => ['/images/products/gelang.png'],
            ],
            [
                'category_slug' => 'gelang-emas',
                'sku'           => 'JW-GEL-24K-02',
                'name'          => 'Gelang Rantai Plat',
                'description'   => 'Gelang rantai model plat dari emas murni 24 karat (kadar 999). Cocok untuk pria maupun wanita dengan gaya modern.',
                'purity'        => '24K',
                'weight'        => 6.000,
                'base_price'    => 5340000,
                'stock'         => 10,
                'sort_order'    => 6,
                'images'        => ['/images/products/gelang.png'],
            ],
            [
                'category_slug' => 'anting-emas',
                'sku'           => 'JW-ANT-24K-01',
                'name'          => 'Anting Giwang Bulat',
                'description'   => 'Anting giwang (stud) bulat simpel dari emas murni 24 karat (kadar 999). Nyaman digunakan sehari-hari tanpa mengganggu aktivitas.',
                'purity'        => '24K',
                'weight'        => 1.500,
                'base_price'    => 1350000,
                'stock'         => 20,
                'sort_order'    => 7,
                'images'        => ['/images/products/anting.png'],
            ],
            [
                'category_slug' => 'anting-emas',
                'sku'           => 'JW-ANT-24K-02',
                'name'          => 'Anting Gantung Rumbai',
                'description'   => 'Anting gantung dengan rumbai cantik emas 24 karat (kadar 999). Memberikan kesan mewah, dinamis, dan anggun saat dipakai.',
                'purity'        => '24K',
                'weight'        => 3.000,
                'base_price'    => 2940000,
                'stock'         => 9,
                'sort_order'    => 8,
                'images'        => ['/images/products/anting.png'],
            ],
            [
                'category_slug' => 'cincin-emas',
                'sku'           => 'JW-CIN-24K-03',
                'name'          => 'Cincin Emas Murni Anak',
                'description'   => 'Cincin emas murni berkadar 24 karat (kadar 999) untuk anak-anak dengan hiasan karakter lucu. Aman bagi kulit sensitif balita.',
                'purity'        => '24K',
                'weight'        => 1.000,
                'base_price'    => 550000,
                'stock'         => 25,
                'sort_order'    => 9,
                'images'        => ['/images/products/cincin.png'],
            ],
            [
                'category_slug' => 'gelang-emas',
                'sku'           => 'JW-GEL-24K-03',
                'name'          => 'Gelang Murni Anak Kerincing',
                'description'   => 'Gelang emas murni berkadar 24 karat (kadar 999) model rantai kerincing untuk balita. Dilengkapi krincing kecil berbunyi halus.',
                'purity'        => '24K',
                'weight'        => 2.000,
                'base_price'    => 1100000,
                'stock'         => 15,
                'sort_order'    => 10,
                'images'        => ['/images/products/gelang.png'],
            ],
        ];

        foreach ($products as $p) {
            $categoryId = $catId($p['category_slug']);
            if (! $categoryId) continue;

            Product::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'category_id'    => $categoryId,
                    'name'           => $p['name'],
                    'slug'           => Str::slug($p['name']),
                    'description'    => $p['description'],
                    'gold_purity'    => $p['purity'],
                    'weight_gram'    => $p['weight'],
                    'base_price'     => $p['base_price'],
                    'buy_back_price' => round($p['base_price'] * 0.97, 2),
                    'stock'          => $p['stock'],
                    'images'         => $p['images'],
                    'is_available'   => $p['stock'] > 0,
                    'is_reservable'  => $p['stock'] > 0,
                    'sort_order'     => $p['sort_order'],
                ]
            );
        }
    }
}
