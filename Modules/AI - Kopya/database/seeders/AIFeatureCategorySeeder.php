<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeatureCategory;
use App\Helpers\TenantHelpers;

class AIFeatureCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Central veritabanında kategoriler oluştur
        TenantHelpers::central(function() {
            $this->command->info('AI Feature Categories central veritabanında oluşturuluyor...');
            
            // Mevcut kategorileri temizle
            AIFeatureCategory::query()->delete();
            
            // Ana kategoriler
            $categories = [
                [
                    'title' => 'İçerik Üretimi',
                    'slug' => 'icerik-uretimi',
                    'description' => 'Metin, makale, blog yazıları ve SEO içeriği üretim araçları',
                    'icon' => 'fas fa-pen-fancy',
                    'order' => 1,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Pazarlama',
                    'slug' => 'pazarlama',
                    'description' => 'Dijital pazarlama, sosyal medya ve reklam içerikleri',
                    'icon' => 'fas fa-bullhorn',
                    'order' => 2,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'SEO & Analiz',
                    'slug' => 'seo-analiz',
                    'description' => 'Arama motoru optimizasyonu ve içerik analizi araçları',
                    'icon' => 'fas fa-chart-line',
                    'order' => 3,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Çeviri & Dil',
                    'slug' => 'ceviri-dil',
                    'description' => 'Dil çevirisi, gramer kontrolü ve dil geliştirme',
                    'icon' => 'fas fa-language',
                    'order' => 4,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'İş & Finans',
                    'slug' => 'is-finans',
                    'description' => 'İş planları, finansal analiz ve şirket yönetimi',
                    'icon' => 'fas fa-briefcase',
                    'order' => 5,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Eğitim & Öğretim',
                    'slug' => 'egitim-ogretim',
                    'description' => 'Eğitim materyalleri, kurs içeriği ve öğretim araçları',
                    'icon' => 'fas fa-graduation-cap',
                    'order' => 6,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Yaratıcılık & Sanat',
                    'slug' => 'yaraticilik-sanat',
                    'description' => 'Kreatif yazım, hikaye anlatımı ve sanatsal içerik',
                    'icon' => 'fas fa-palette',
                    'order' => 7,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Kod & Teknoloji',
                    'slug' => 'kod-teknoloji',
                    'description' => 'Yazılım geliştirme, kod analizi ve teknik dokümantasyon',
                    'icon' => 'fas fa-code',
                    'order' => 8,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Araştırma & Analiz',
                    'slug' => 'arastirma-analiz',
                    'description' => 'Pazar araştırması, veri analizi ve raporlama',
                    'icon' => 'fas fa-search',
                    'order' => 9,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ],
                [
                    'title' => 'Diğer',
                    'slug' => 'diger',
                    'description' => 'Diğer yardımcı araçlar ve özel fonksiyonlar',
                    'icon' => 'fas fa-ellipsis-h',
                    'order' => 10,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ]
            ];
            
            foreach ($categories as $categoryData) {
                AIFeatureCategory::create($categoryData);
                $this->command->info("✅ Kategori oluşturuldu: {$categoryData['title']}");
            }
            
            $this->command->info('AI Feature Categories başarıyla oluşturuldu!');
        });
    }
}