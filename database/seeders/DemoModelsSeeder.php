<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ModelFile;
use App\Models\ThreeDModel;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoModelsSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::where('email', 'demo@modela.gr')->first();
        $adminUser = User::where('email', 'admin@modela.gr')->first();
        
        if (!$demoUser || !$adminUser) {
            return;
        }

        $categories = Category::all();

        $models = [
            [
                'user_id' => $demoUser->id,
                'category_id' => $categories->where('slug', 'tools-parts')->first()?->id ?? 1,
                'title' => 'Adjustable Phone Stand',
                'slug' => 'adjustable-phone-stand',
                'description' => 'A versatile phone stand with adjustable angle. Perfect for desk use, video calls, or watching content. Fits most smartphone sizes.',
                'license' => 'Creative Commons - Attribution',
                'tags' => ['phone', 'stand', 'desk', 'accessory'],
                'visibility' => 'public',
                'price_type' => 'print_only',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'view_count' => 245,
                'download_count' => 67,
                'like_count' => 23,
            ],
            [
                'user_id' => $adminUser->id,
                'category_id' => $categories->where('slug', 'home-garden')->first()?->id ?? 1,
                'title' => 'Minimalist Planter Pot',
                'slug' => 'minimalist-planter-pot',
                'description' => 'Modern geometric planter for small succulents and cacti. Features drainage holes and a clean design that fits any decor.',
                'license' => 'Creative Commons - Attribution',
                'tags' => ['planter', 'pot', 'succulent', 'home decor'],
                'visibility' => 'public',
                'price_type' => 'free',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'view_count' => 523,
                'download_count' => 156,
                'like_count' => 89,
            ],
            [
                'user_id' => $demoUser->id,
                'category_id' => $categories->where('slug', 'toys-games')->first()?->id ?? 1,
                'title' => 'Articulated Dragon',
                'slug' => 'articulated-dragon',
                'description' => 'Fully articulated dragon model that prints in place without supports. Fun to fidget with and display. Approximately 15cm long when assembled.',
                'license' => 'Personal Use Only',
                'tags' => ['dragon', 'articulated', 'toy', 'fidget'],
                'visibility' => 'public',
                'price_type' => 'paid',
                'model_price' => 4.99,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'view_count' => 1234,
                'download_count' => 345,
                'like_count' => 234,
            ],
            [
                'user_id' => $adminUser->id,
                'category_id' => $categories->where('slug', 'art-design')->first()?->id ?? 1,
                'title' => 'Abstract Sculpture Base',
                'slug' => 'abstract-sculpture-base',
                'description' => 'Contemporary abstract sculpture perfect for home or office decor. Inspired by modern art movements.',
                'license' => 'Creative Commons - Attribution',
                'tags' => ['sculpture', 'art', 'modern', 'decor'],
                'visibility' => 'public',
                'price_type' => 'print_only',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'view_count' => 187,
                'download_count' => 23,
                'like_count' => 45,
            ],
            [
                'user_id' => $demoUser->id,
                'category_id' => $categories->where('slug', 'electronics-tech')->first()?->id ?? 1,
                'title' => 'Cable Management Clips',
                'slug' => 'cable-management-clips',
                'description' => 'Set of cable clips for organizing desk cables. Adhesive backing for easy mounting. Fits cables up to 6mm diameter.',
                'license' => 'Creative Commons - Attribution',
                'tags' => ['cable', 'organization', 'desk', 'management'],
                'visibility' => 'public',
                'price_type' => 'free',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'view_count' => 892,
                'download_count' => 234,
                'like_count' => 123,
            ],
            [
                'user_id' => $adminUser->id,
                'category_id' => $categories->where('slug', 'miniatures-models')->first()?->id ?? 1,
                'title' => 'Tabletop Gaming Dice Tower',
                'slug' => 'dice-tower',
                'description' => 'Compact dice tower for tabletop RPGs. Ensures fair rolls every time. Modular design allows for customization.',
                'license' => 'Personal Use Only',
                'tags' => ['dice', 'gaming', 'tabletop', 'rpg'],
                'visibility' => 'public',
                'price_type' => 'print_only',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'view_count' => 456,
                'download_count' => 78,
                'like_count' => 67,
            ],
        ];

        foreach ($models as $modelData) {
            $model = ThreeDModel::create($modelData);
            
            // Add a demo file with metadata
            ModelFile::create([
                'three_d_model_id' => $model->id,
                'original_name' => $model->slug . '.stl',
                'disk' => 'public',
                'path' => 'models/' . $model->slug . '.stl',
                'mime_type' => 'application/sla',
                'size' => rand(50000, 5000000),
                'format' => 'stl',
                'checksum' => hash('sha256', $model->slug . time()),
                'metadata' => [
                    'volume_mm3' => rand(10000, 150000),
                    'dimensions_mm' => [
                        'x' => rand(20, 150),
                        'y' => rand(20, 150),
                        'z' => rand(10, 100),
                    ],
                    'triangle_count' => rand(5000, 50000),
                ],
                'processing_status' => 'completed',
            ]);
        }
    }
}
