<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Art & Design',
                'slug' => 'art-design',
                'description' => 'Decorative objects, sculptures, and artistic pieces',
                'icon' => 'palette',
                'sort_order' => 1,
            ],
            [
                'name' => 'Tools & Parts',
                'slug' => 'tools-parts',
                'description' => 'Functional tools, replacement parts, and hardware',
                'icon' => 'wrench',
                'sort_order' => 2,
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
                'description' => 'Fun items, board game accessories, and collectibles',
                'icon' => 'puzzle',
                'sort_order' => 3,
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Household items, organizers, planters, and home decor',
                'icon' => 'home',
                'sort_order' => 4,
            ],
            [
                'name' => 'Fashion & Accessories',
                'slug' => 'fashion-accessories',
                'description' => 'Jewelry, wearables, and fashion items',
                'icon' => 'shirt',
                'sort_order' => 5,
            ],
            [
                'name' => 'Miniatures & Models',
                'slug' => 'miniatures-models',
                'description' => 'Scale models, figurines, and miniature collections',
                'icon' => 'cube',
                'sort_order' => 6,
            ],
            [
                'name' => 'Electronics & Tech',
                'slug' => 'electronics-tech',
                'description' => 'Cases, mounts, and technical accessories',
                'icon' => 'cpu',
                'sort_order' => 7,
            ],
            [
                'name' => 'Educational',
                'slug' => 'educational',
                'description' => 'Learning aids, science models, and teaching tools',
                'icon' => 'academic-cap',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
