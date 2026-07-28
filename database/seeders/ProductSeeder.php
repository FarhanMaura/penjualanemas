<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Data Real Produk Emas Toko Sinar Baru II:
     * 
     * Spesifikasi Berat:
     *  - 0,5 gram   : Setengah Gram (0.500g)
     *  - 1,0 gram   : 1 Gram (1.000g)
     *  - 1,65 gram  : 1/4 Suku (1.650g)
     *  - 3,4 gram   : Setengah Suku (3.400g)
     *  - 6,7 gram   : 1 Suku (6.700g)
     *  - 13,4 gram  : 2 Suku (13.400g)
     *  - 20,1 gram  : 3 Suku (20.100g)
     *  - 33,5 gram  : 5 Suku (33.500g)
     */
    public function run(): void
    {
        $catId = fn(string $slug) => Category::where('slug', $slug)->value('id');

        // Definisi varian berat
        $weightSpecs = [
            '0.5'  => ['gram' => 0.500, 'label' => 'Setengah Gram', 'tag' => '0,5g',  'code' => '05G'],
            '1.0'  => ['gram' => 1.000, 'label' => '1 Gram',        'tag' => '1,0g',  'code' => '10G'],
            '1.65' => ['gram' => 1.650, 'label' => '1/4 Suku',      'tag' => '1,65g', 'code' => '14S'],
            '3.4'  => ['gram' => 3.400, 'label' => 'Setengah Suku', 'tag' => '3,4g',  'code' => '12S'],
            '6.7'  => ['gram' => 6.700, 'label' => '1 Suku',        'tag' => '6,7g',  'code' => '1S'],
            '13.4' => ['gram' => 13.400, 'label' => '2 Suku',       'tag' => '13,4g', 'code' => '2S'],
            '20.1' => ['gram' => 20.100, 'label' => '3 Suku',       'tag' => '20,1g', 'code' => '3S'],
            '33.5' => ['gram' => 33.500, 'label' => '5 Suku',       'tag' => '33,5g', 'code' => '5S'],
        ];

        // Definisi kategori & model serta varian berat yang dijual
        $categoriesData = [
            'cincin-emas' => [
                'prefix'  => 'JW-CIN',
                'title'   => 'Cincin',
                'image'   => '/images/products/cincin.png',
                'models'  => ['Bangkok', 'Asahan', 'Borobudur', 'Chanel', 'Sultan'],
                'weights' => ['0.5', '1.0', '1.65', '3.4', '6.7'],
            ],
            'kalung-emas' => [
                'prefix'  => 'JW-KAL',
                'title'   => 'Kalung',
                'image'   => '/images/products/kalung.png',
                'models'  => ['Padi', 'Medan'],
                'weights' => ['1.0', '1.65', '3.4', '6.7', '13.4', '20.1', '33.5'],
            ],
            'gelang-emas' => [
                'prefix'  => 'JW-GEL',
                'title'   => 'Gelang',
                'image'   => '/images/products/gelang.png',
                'models'  => ['Padi', 'Medan'],
                'weights' => ['1.0', '1.65', '3.4', '6.7', '13.4', '20.1', '33.5'],
            ],
            'anting-emas' => [
                'prefix'  => 'JW-ANT',
                'title'   => 'Anting',
                'image'   => '/images/products/anting.png',
                'models'  => ['Rantai Bintang', 'Micimouse', 'Patam'],
                'weights' => ['0.5', '1.0', '1.65'],
            ],
        ];

        $estimatedPricePerGram = 1620000;
        $sortOrder = 1;

        foreach ($categoriesData as $catSlug => $config) {
            $categoryId = $catId($catSlug);
            if (! $categoryId) continue;

            foreach ($config['models'] as $mIdx => $modelName) {
                foreach ($config['weights'] as $wKey) {
                    $wInfo = $weightSpecs[$wKey];
                    
                    // Format Nama Produk
                    if ($wKey === '1.0') {
                        $productName = "{$config['title']} {$modelName} 1 Gram";
                    } else {
                        $productName = "{$config['title']} {$modelName} ({$wInfo['label']} - {$wInfo['tag']})";
                    }

                    // SKU unik
                    $modelSlug = strtoupper(Str::slug($modelName, ''));
                    $sku = "{$config['prefix']}-{$modelSlug}-{$wInfo['code']}";

                    // Deskripsi Produk
                    $desc = "{$config['title']} murni model {$modelName} berkadar 24 karat (999) dengan ukuran berat {$wInfo['label']} ({$wInfo['gram']} gram). Memiliki kilau mewah, garansi keaslian, dan cocok untuk pemakaian maupun investasi.";

                    // Base price
                    $basePrice = round($estimatedPricePerGram * $wInfo['gram'], -3);
                    $stock = rand(8, 25);

                    // Tandai produk dasar/ori (model pertama & ukuran berat standar)
                    $isBasic = ($mIdx === 0 && ($wKey === '1.0' || ($wKey === '0.5' && $config['title'] === 'Anting')));

                    Product::updateOrCreate(
                        ['sku' => $sku],
                        [
                            'category_id'    => $categoryId,
                            'name'           => $productName,
                            'slug'           => Str::slug($productName),
                            'description'    => $desc,
                            'gold_purity'    => '24K',
                            'weight_gram'    => $wInfo['gram'],
                            'base_price'     => $basePrice,
                            'buy_back_price' => round($basePrice * 0.97, -3),
                            'stock'          => $stock,
                            'images'         => [$config['image']],
                            'is_available'   => true,
                            'is_reservable'  => true,
                            'is_basic'       => $isBasic,
                            'sort_order'     => $sortOrder++,
                        ]
                    );
                }
            }
        }
    }
}
