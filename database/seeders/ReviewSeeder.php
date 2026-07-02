<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::orderBy('id')->take(5)->get();

        $reviews = [
            ['Olivia Carter', 'Bodybuilder', 'Daily routine ka important part ban chuka hai. Easy to use, premium packaging, aur results consistent hain.', 'frontend/assets/images/testimonials/oliviacarter.png'],
            ['Mike R.', 'Fitness Coach', 'Quality you can trust and results you can see. Products feel professional from order to delivery.', 'frontend/assets/images/testimonials/miker.png'],
            ['Sophia Bennett', 'Athlete', 'This fits perfectly into my performance lifestyle. Clean packaging and smooth experience overall.', 'frontend/assets/images/testimonials/sophiabennett.png'],
            ['Daniel K.', 'Powerlifter', 'Shipping was discreet, support was fast, and the products looked exactly as described.', 'frontend/assets/images/testimonials/danielk.png'],
            ['Ava Mitchell', 'Trainer', 'Easy to recommend. The catalogue is clear, checkout is simple, and the quality feels reliable.', 'frontend/assets/images/testimonials/avamitchell.png'],
        ];

        foreach ($reviews as $index => [$name, $title, $comment, $avatar]) {
            Review::updateOrCreate(
                ['customer_name' => $name],
                [
                    'product_id' => $products[$index]->id ?? null,
                    'customer_title' => $title,
                    'rating' => 5,
                    'comment' => $comment,
                    'avatar' => $avatar,
                    'status' => 'active',
                    'is_featured' => $index === 1,
                ]
            );
        }
    }
}
