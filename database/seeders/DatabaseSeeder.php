<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@arete.com',
        ], [
            'name' => 'Admin',
            'phone' => '+1 555 120 2024',
            'password' => Hash::make('password'),
            'role' => 'super administrator',
            'status' => 'active',
        ]);

        $categories = collect([
            ['name' => 'Orals', 'slug' => 'orals'],
            ['name' => 'Peptides', 'slug' => 'peptides'],
            ['name' => 'Fat Burners', 'slug' => 'fat-burners'],
            ['name' => 'Post Cycle Therapy', 'slug' => 'post-cycle-therapy'],
        ])->map(fn ($category, $index) => Category::updateOrCreate(
            ['slug' => $category['slug']],
            $category + [
                'description' => 'Arete Performance ' . strtolower($category['name']) . ' collection.',
                'status' => 'active',
                'sort_order' => $index + 1,
            ]
        ));

        collect([
            ['Testosterone Booster', 'testosterone-booster', 'ARTE-1001', 48.00, 120, 0],
            ['Fat Burner Pro', 'fat-burner-pro', 'ARTE-1002', 45.00, 98, 2],
            ['Whey Protein Isolate', 'whey-protein-isolate', 'ARTE-1003', 39.00, 85, 1],
            ['Creatine Monohydrate', 'creatine-monohydrate', 'ARTE-1004', 29.00, 65, 0],
            ['Pre-Workout Extreme', 'pre-workout-extreme', 'ARTE-1005', 42.00, 60, 1],
        ])->each(function ($product) use ($categories): void {
            Product::updateOrCreate([
                'slug' => $product[1],
            ], [
                'category_id' => $categories[$product[5]]->id,
                'name' => $product[0],
                'sku' => $product[2],
                'short_description' => 'Premium performance supplement.',
                'description' => 'Premium performance supplement from Arete Performance.',
                'image' => 'backend/assets/imgs/product-bottle.png',
                'price' => $product[3],
                'stock' => $product[4],
                'status' => 'active',
                'is_featured' => true,
            ]);
        });
    }
}
