<?php

namespace Database\Seeders;

use App\Models\Website\Faq;
use App\Models\Website\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clear existing data safely (disable foreign key checks for truncation)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Faq::truncate();
        FaqCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Category (with required slug)
        $category = FaqCategory::create([
            'name' => 'General Questions',
            'slug' => Str::slug('General Questions'),
        ]);

        $faqs = [
            [
                'slug' => 'how-does-your-laundry-service-work',
                'content' => [[
                    'ques' => 'How does your laundry service work?',
                    'answer' => 'Our service is simple: 1) Schedule a pickup online or through our app, 2) We collect your laundry from your doorstep, 3) Our experts wash, dry, and fold your clothes, 4) We deliver clean laundry back to you within 24-48 hours.'
                ]]
            ],
            [
                'slug' => 'what-are-your-delivery-times',
                'content' => [[
                    'ques' => 'What are your delivery times?',
                    'answer' => 'We offer standard delivery (24-48 hours) and express delivery (same-day or next-day). Our delivery hours are 8 AM to 10 PM, 7 days a week.'
                ]]
            ],
            [
                'slug' => 'how-do-you-price-your-services',
                'content' => [[
                    'ques' => 'How do you price your services?',
                    'answer' => 'Our pricing is based on the weight or item type. Wash and Fold starts at $1.50 per pound, Dry Cleaning from $5.99 per item, and Ironing from $2.99 per item.'
                ]]
            ],
            [
                'slug' => 'do-you-offer-same-day-service',
                'content' => [[
                    'ques' => 'Do you offer same-day service?',
                    'answer' => 'Yes! We offer same-day pickup and delivery for orders placed before 10 AM.'
                ]]
            ],
            [
                'slug' => 'what-type-of-detergents-do-you-use',
                'content' => [[
                    'ques' => 'What type of detergents do you use?',
                    'answer' => 'We use premium, hypoallergenic detergents that are gentle on fabrics and safe for sensitive skin.'
                ]]
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create([
                'faq_category_id' => $category->id,
                'slug' => $faq['slug'],
                'content' => json_encode($faq['content']), // ensure JSON format
                'status' => 'active',
            ]);
        }
    }
}
