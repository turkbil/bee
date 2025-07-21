<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIFeaturePrompt;
use Illuminate\Support\Str;
use App\Helpers\TenantHelpers;

class AISEOFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SEO Features'ları central veritabanında oluştur
        TenantHelpers::central(function() {
            $this->command->info('SEO AI Features oluşturuluyor...');
            
            // SEO Prompt'ları önce yükle
            $this->call(AISEOPromptsSeeder::class);
            
            // SEO Feature Category'yi kontrol et/oluştur
            $this->ensureSEOCategory();
            
            // SEO Feature'larını oluştur
            $this->createSEOFeatures();
            
            $this->command->info('✅ SEO AI Features başarıyla oluşturuldu!');
        });
    }

    /**
     * SEO kategorisini kontrol et/oluştur
     */
    private function ensureSEOCategory(): void
    {
        $seoCategory = DB::table('ai_feature_categories')
            ->where('title', 'SEO & Marketing')
            ->first();

        if (!$seoCategory) {
            DB::table('ai_feature_categories')->insert([
                'title' => 'SEO & Marketing',
                'slug' => 'seo-marketing',
                'description' => 'Arama motoru optimizasyonu ve dijital pazarlama araçları',
                'icon' => 'fas fa-search',
                'order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info("✓ SEO & Marketing kategorisi oluşturuldu");
        }
    }

    /**
     * SEO Feature'larını oluştur
     */
    private function createSEOFeatures(): void
    {
        // Kategori ID'sini al
        $seoCategory = DB::table('ai_feature_categories')
            ->where('title', 'SEO & Marketing')
            ->first();

        $seoFeatures = [
            [
                'name' => 'Hızlı SEO Analizi',
                'slug' => 'hizli-seo-analizi',
                'description' => 'Web sayfanızın SEO performansını anında analiz edin, puan alın ve somut iyileştirme önerileri keşfedin',
                'emoji' => '⚡',
                'icon' => 'fas fa-tachometer-alt',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'beginner',
                'quick_prompt' => 'Sen bir SEO uzmanısın. Verilen web sayfası içeriğini analiz et ve hızlı SEO puanı ile öneriler ver.',
                'helper_function' => 'ai_seo_quick_analysis',
                'button_text' => 'Hızlı Analiz Yap',
                'helper_description' => 'Web sayfası içeriğini hızlıca analiz ederek SEO puanı ve temel öneriler sunar',
                'input_placeholder' => 'Analiz edilecek web sayfası içeriğini yapıştırın...',
                'example_inputs' => [
                    'Web sayfası başlığı ve içeriği',
                    'Blog post metni',
                    'Ürün sayfası açıklaması'
                ],
                'response_template' => [
                    'format' => 'structured_analysis',
                    'sections' => ['Puan', 'Kritik Sorunlar', 'Öneriler', 'Gelişmiş İpuçları'],
                    'scoring' => true,
                    'actionable' => true
                ],
                'prompts' => ['SEO İçerik Analiz Uzmanı']
            ],
            [
                'name' => 'Anahtar Kelime Araştırması',
                'slug' => 'anahtar-kelime-arastirmasi',
                'description' => 'Hedef kitlenize uygun anahtar kelimeleri keşfedin, rekabet analizi yapın ve content strategy oluşturun',
                'emoji' => '🔍',
                'icon' => 'fas fa-key',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'intermediate',
                'quick_prompt' => 'Sen bir anahtar kelime uzmanısın. Verilen konu için SEO-friendly anahtar kelime önerileri ve analizi yap.',
                'helper_function' => 'ai_keyword_research',
                'button_text' => 'Anahtar Kelime Bul',
                'helper_description' => 'Konu bazlı anahtar kelime araştırması yaparak LSI ve long-tail önerileri sunar',
                'input_placeholder' => 'Araştırılacak konu veya ana anahtar kelimeyi girin...',
                'example_inputs' => [
                    'web tasarım',
                    'organik gıda satışı',
                    'muhasebe hizmetleri'
                ],
                'response_template' => [
                    'format' => 'keyword_analysis',
                    'sections' => ['Ana Kelime', 'LSI Kelimeler', 'Long-tail Fırsatları', 'Rekabet Analizi'],
                    'difficulty_score' => true,
                    'search_volume_estimate' => true
                ],
                'prompts' => ['SEO Anahtar Kelime Uzmanı']
            ],
            [
                'name' => 'SEO Başlık Üretici',
                'slug' => 'seo-baslik-uretici',
                'description' => 'Google\'da üst sıralarda çıkacak, tıklanma oranı yüksek başlık ve meta açıklamalar oluşturun',
                'emoji' => '📝',
                'icon' => 'fas fa-heading',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'beginner',
                'quick_prompt' => 'Sen bir SEO copywriter\'sın. Verilen konu için Google\'da üst sıralarda çıkacak başlık ve meta açıklama oluştur.',
                'helper_function' => 'ai_seo_title_generator',
                'button_text' => 'Başlık Oluştur',
                'helper_description' => 'SEO-optimized başlık ve meta açıklama önerileri ile CTR artışı sağlar',
                'input_placeholder' => 'Başlık oluşturulacak konu veya anahtar kelimeyi girin...',
                'example_inputs' => [
                    'WordPress güvenlik ipuçları',
                    'E-ticaret site kurulumu',
                    'Sosyal medya pazarlama stratejileri'
                ],
                'response_template' => [
                    'format' => 'title_meta_package',
                    'sections' => ['Önerilen Title', 'Meta Description', 'CTR Tahminı', 'A/B Test Varyasyonları'],
                    'character_count' => true,
                    'ctr_optimization' => true
                ],
                'prompts' => ['SEO Başlık Meta Uzmanı']
            ],
            [
                'name' => 'Teknik SEO Kontrol',
                'slug' => 'teknik-seo-kontrol',
                'description' => 'Web sitenizin teknik SEO performansını detaylı analiz edin ve developer-friendly öneriler alın',
                'emoji' => '⚙️',
                'icon' => 'fas fa-cogs',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'advanced',
                'quick_prompt' => 'Sen bir teknik SEO uzmanısın. Web sitesinin teknik durumunu analiz et ve implementation önerileri ver.',
                'helper_function' => 'ai_technical_seo_audit',
                'button_text' => 'Teknik Kontrol',
                'helper_description' => 'Site performansı, mobile optimization ve teknik SEO faktörlerini analiz eder',
                'input_placeholder' => 'Web sitesi URL\'si veya teknik detayları girin...',
                'example_inputs' => [
                    'https://example.com',
                    'Site hızı problemi var',
                    'Mobile uyumluluk kontrolü'
                ],
                'response_template' => [
                    'format' => 'technical_audit',
                    'sections' => ['Kritik Teknik Sorunlar', 'Performans İyileştirmeleri', 'Mobile Optimizasyon', 'Implementation'],
                    'priority_levels' => true,
                    'developer_friendly' => true
                ],
                'prompts' => ['SEO Teknik Analiz Uzmanı']
            ],
            [
                'name' => 'İçerik Stratejisi Planlayıcı',
                'slug' => 'icerik-stratejisi-planlayici',
                'description' => 'Google\'da rank yapacak içerik planları oluşturun, topic cluster\'lar ve content calendar hazırlayın',
                'emoji' => '📊',
                'icon' => 'fas fa-sitemap',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'expert',
                'quick_prompt' => 'Sen bir içerik stratejistin. Verilen sektör için SEO-odaklı content strategy ve plan oluştur.',
                'helper_function' => 'ai_content_strategy_planner',
                'button_text' => 'Strateji Oluştur',
                'helper_description' => 'Sektör bazlı content pillar stratejisi ve SEO-friendly içerik takvimi hazırlar',
                'input_placeholder' => 'Sektör, hedef kitle veya business bilgilerini girin...',
                'example_inputs' => [
                    'Diş hekimliği kliniği',
                    'E-ticaret elektronik mağazası',
                    'Yazılım geliştirme şirketi'
                ],
                'response_template' => [
                    'format' => 'strategy_blueprint',
                    'sections' => ['İçerik Pillar Önerisi', 'Supporting Content', 'Anahtar Kelime Haritası', 'İçerik Takvimi'],
                    'timeline' => '3-month',
                    'competitive_analysis' => true
                ],
                'prompts' => ['SEO İçerik Stratejisti']
            ],
            [
                'name' => 'Local SEO Optimizer',
                'slug' => 'local-seo-optimizer',
                'description' => 'Yerel işletmeniz için Google My Business optimization ve local search stratejileri geliştirin',
                'emoji' => '📍',
                'icon' => 'fas fa-map-marker-alt',
                'ai_feature_category_id' => $seoCategory->ai_feature_category_id,
                'complexity_level' => 'intermediate',
                'quick_prompt' => 'Sen bir local SEO uzmanısın. Yerel işletme için Google My Business ve local search optimization öneriler.',
                'helper_function' => 'ai_local_seo_optimizer',
                'button_text' => 'Local SEO Yap',
                'helper_description' => 'Yerel işletmeler için Google Maps ve local search visibility artışı sağlar',
                'input_placeholder' => 'İşletme türü, şehir ve hizmet alanlarını girin...',
                'example_inputs' => [
                    'İstanbul kuaförü',
                    'Ankara avukat ofisi',
                    'İzmir restoran'
                ],
                'response_template' => [
                    'format' => 'local_seo_plan',
                    'sections' => ['GMB Optimization', 'Local Citations', 'Review Strategy', 'Local Content'],
                    'geo_targeting' => true,
                    'citation_opportunities' => true
                ],
                'prompts' => ['SEO İçerik Analiz Uzmanı', 'SEO İçerik Stratejisti']
            ]
        ];

        foreach ($seoFeatures as $featureData) {
            // Prompt ID'lerini bul
            $promptIds = [];
            if (isset($featureData['prompts'])) {
                foreach ($featureData['prompts'] as $promptName) {
                    $prompt = Prompt::where('name', $promptName)->first();
                    if ($prompt) {
                        $promptIds[] = $prompt->id;
                    }
                }
                unset($featureData['prompts']);
            }

            // JSON alanları encode et
            $featureData['example_inputs'] = json_encode($featureData['example_inputs']);
            $featureData['response_template'] = json_encode($featureData['response_template']);
            
            // Varsayılan değerler
            $featureData['is_featured'] = true;
            $featureData['show_in_examples'] = true;
            $featureData['requires_input'] = true;
            $featureData['is_system'] = false;
            $featureData['status'] = 'active';
            $featureData['sort_order'] = 1;
            $featureData['usage_count'] = 0;

            // Feature'ı oluştur
            $feature = AIFeature::create($featureData);

            // Prompt'ları bağla
            foreach ($promptIds as $promptId) {
                AIFeaturePrompt::create([
                    'feature_id' => $feature->id,
                    'prompt_id' => $promptId,
                    'priority' => 1,
                    'role' => 'primary',
                    'is_active' => true
                ]);
            }

            $this->command->info("✓ SEO Feature oluşturuldu: {$featureData['name']}");
        }
    }
}