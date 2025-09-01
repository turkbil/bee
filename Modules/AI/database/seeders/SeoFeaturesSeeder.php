<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Features Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        
        echo "\n🎯 SEO FEATURES oluşturuluyor...\n";

        // SEO Comprehensive Audit feature (ID 305)
        $seoFeature = [
            'id' => 305,
            'name' => 'SEO Comprehensive Audit',
            'slug' => 'seo-comprehensive-audit',
            'description' => 'Sayfalar için kapsamlı SEO analizi ve iyileştirme önerileri',
            'ai_feature_category_id' => 13, // SEO category (assuming it exists)
            'emoji' => '🔍',
            'icon' => 'ti-search',
            'module_type' => 'seo',
            'category' => 'optimization',
            'supported_modules' => json_encode(['page', 'blog', 'portfolio']),
            'status' => 'active',
            'sort_order' => 1,
            'template_support' => false,
            'bulk_support' => false,
            'streaming_support' => false,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('ai_features')->updateOrInsert(
            ['id' => 305],
            $seoFeature
        );

        echo "✅ SEO Comprehensive Audit feature oluşturuldu (ID: 305)\n";

        // SEO Smart Recommendations feature (ID 306)
        $seoRecommendationsFeature = [
            'id' => 306,
            'name' => 'SEO Smart Recommendations',
            'slug' => 'seo-smart-recommendations',
            'description' => 'Premium AI-powered SEO önerileri ve otomatik uygulama sistemi',
            'ai_feature_category_id' => 13, // SEO category
            'emoji' => '🎯',
            'icon' => 'ti-magic',
            'module_type' => 'seo',
            'category' => 'recommendations',
            'supported_modules' => json_encode(['page', 'blog', 'portfolio', 'announcement']),
            'status' => 'active',
            'sort_order' => 2,
            'template_support' => false,
            'bulk_support' => false,
            'streaming_support' => false,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('ai_features')->updateOrInsert(
            ['id' => 306],
            $seoRecommendationsFeature
        );

        echo "✅ SEO Smart Recommendations feature oluşturuldu (ID: 306)\n";
        echo "✅ SEO FEATURES tamamlandı!\n\n";
    }
}