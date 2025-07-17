<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part1 extends Seeder
{
    /**
     * SECTOR SEEDER PART 1 (ID 1-50)
     * Ana kategoriler + temel sektörler + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 1 yükleniyor (ID 1-50)...\n";

        // Sadece Part 1 ID aralığındaki verileri temizle
        DB::table('ai_profile_sectors')->whereBetween('id', [1, 50])->delete();
        DB::table('ai_profile_questions')->whereBetween('id', [6000, 6999])->delete();

        // Ana kategoriler ve temel sektörleri oluştur
        $this->createBasicStructure();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();
        
        // Kalan sektörlere genel sorular ekle
        $this->addGeneralSectorQuestions();

        echo "✅ Part 1 tamamlandı! (Ana kategoriler + temel sektörler)\n";
    }
    
    
    private function addSectorQuestions(): void
    {
        // ORGANIZE EDİLEN SEKTÖR SORULARI - HER SEKTÖR KENDI DOSYASINDA
        
        // ===========================================
        // 1. TEKNOLOJİ VE WEB SEKTÖRLERİ 🔧
        // ===========================================
        
        // WEB TASARIM VE GELİŞTİRME
        $webQuestions = [
            [
                'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_specific_services', 'question_text' => 'Hangi web hizmetlerini sunuyorsunuz?',
                'help_text' => 'Web tasarım ve geliştirme alanındaki uzmanlaştığınız hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Kurumsal web sitesi', 'value' => 'kurumsal_web'],
                    ['label' => 'E-ticaret sitesi', 'value' => 'eticaret'],
                    ['label' => 'Blog/Portfolio', 'value' => 'blog_portfolio'],
                    ['label' => 'Landing page', 'value' => 'landing_page'],
                    ['label' => 'Laravel uygulamaları', 'value' => 'laravel'],
                    ['label' => 'React/Vue.js uygulamaları', 'value' => 'react_vue'],
                    ['label' => 'SEO optimizasyonu', 'value' => 'seo'],
                    ['label' => 'Hosting ve bakım', 'value' => 'hosting'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_project_types', 'question_text' => 'Genellikle hangi tür projeler üzerinde çalışıyorsunuz?',
                'help_text' => 'Proje büyüklüğü ve türleri açısından uzmanlaştığınız alanlar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Küçük işletme siteleri', 'value' => 'small_business'],
                    ['label' => 'Kurumsal projeler', 'value' => 'enterprise'],
                    ['label' => 'Startup projeleri', 'value' => 'startup'],
                    ['label' => 'E-ticaret platformları', 'value' => 'ecommerce'],
                    ['label' => 'Web uygulamaları', 'value' => 'web_apps'],
                    ['label' => 'Mobil uyumlu siteler', 'value' => 'responsive'],
                    ['label' => 'SaaS uygulamaları', 'value' => 'saas'],
                    ['label' => 'Portal/Dashboard', 'value' => 'dashboard'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'project_types'
            ]
        ];

        // TEKNOLOJİ GENEL
        $technologyQuestions = [
            [
                'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'tech_specific_services', 'question_text' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'help_text' => 'Teknoloji alanındaki uzmanlaştığınız hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Yazılım geliştirme', 'value' => 'software_development'],
                    ['label' => 'Mobil uygulama', 'value' => 'mobile_app'],
                    ['label' => 'Sistem entegrasyonu', 'value' => 'system_integration'],
                    ['label' => 'Bulut çözümleri', 'value' => 'cloud_solutions'],
                    ['label' => 'Veri analizi', 'value' => 'data_analytics'],
                    ['label' => 'Yapay zeka/ML', 'value' => 'ai_ml'],
                    ['label' => 'Siber güvenlik', 'value' => 'cybersecurity'],
                    ['label' => 'DevOps/Altyapı', 'value' => 'devops'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // MOBİL UYGULAMA
        $mobileQuestions = [
            [
                'sector_code' => 'mobile', 'step' => 3, 'section' => null,
                'question_key' => 'mobile_platforms_detailed', 'question_text' => 'Hangi platformlarda mobil uygulama geliştiriyorsunuz?',
                'help_text' => 'Mobil uygulama geliştirme platformları ve teknolojileriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'iOS (Swift)', 'value' => 'ios_swift'],
                    ['label' => 'Android (Kotlin/Java)', 'value' => 'android'],
                    ['label' => 'React Native', 'value' => 'react_native'],
                    ['label' => 'Flutter', 'value' => 'flutter'],
                    ['label' => 'Ionic', 'value' => 'ionic'],
                    ['label' => 'Xamarin', 'value' => 'xamarin'],
                    ['label' => 'Progressive Web App', 'value' => 'pwa'],
                    ['label' => 'Hybrid app', 'value' => 'hybrid'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // ===========================================
        // 2. SAĞLIK SEKTÖRLERİ 🏥
        // ===========================================
        
        // SAĞLIK GENEL
        $healthQuestions = [
            [
                'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_specific_services', 'question_text' => 'Hangi sağlık hizmetlerini sunuyorsunuz?',
                'help_text' => 'Sağlık alanındaki uzmanlaştığınız hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Genel pratisyen', 'value' => 'general_practice'],
                    ['label' => 'Dahiliye', 'value' => 'internal_medicine'],
                    ['label' => 'Kardiyoloji', 'value' => 'cardiology'],
                    ['label' => 'Nöroloji', 'value' => 'neurology'],
                    ['label' => 'Ortopedi', 'value' => 'orthopedics'],
                    ['label' => 'Dermatologi', 'value' => 'dermatology'],
                    ['label' => 'Göz hastalıkları', 'value' => 'ophthalmology'],
                    ['label' => 'Dış güzellik/Estetik', 'value' => 'aesthetic'],
                    ['label' => 'Diş hekimliği', 'value' => 'dentistry'],
                    ['label' => 'Fizyoterapi', 'value' => 'physiotherapy'],
                    ['label' => 'Psikolog/Psikiyatrist', 'value' => 'psychology'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // HASTANE
        $hospitalQuestions = [
            [
                'sector_code' => 'hospital', 'step' => 3, 'section' => null,
                'question_key' => 'hospital_departments_detailed', 'question_text' => 'Hangi tıbbi bölümleriniz var?',
                'help_text' => 'Hastane/kliniğinizdeki aktif tıbbi bölümler ve uzmanlar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Dahiliye', 'value' => 'internal_medicine'],
                    ['label' => 'Genel Cerrahi', 'value' => 'general_surgery'],
                    ['label' => 'Kadın Doğum', 'value' => 'gynecology'],
                    ['label' => 'Çocuk Sağlığı', 'value' => 'pediatrics'],
                    ['label' => 'Kardiyoloji', 'value' => 'cardiology'],
                    ['label' => 'Nöroloji', 'value' => 'neurology'],
                    ['label' => 'Ortopedi', 'value' => 'orthopedics'],
                    ['label' => 'KBB', 'value' => 'ent'],
                    ['label' => 'Göz', 'value' => 'ophthalmology'],
                    ['label' => 'Psikiyatri', 'value' => 'psychiatry'],
                    ['label' => 'Üroloji', 'value' => 'urology'],
                    ['label' => 'Dermatoloji', 'value' => 'dermatology'],
                    ['label' => 'Acil Servis', 'value' => 'emergency'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 85,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // DİŞ HEKİMLİĞİ
        $dentalQuestions = [
            [
                'sector_code' => 'dental', 'step' => 3, 'section' => null,
                'question_key' => 'dental_treatments_detailed', 'question_text' => 'Hangi diş tedavilerini uyguluyorsunuz?',
                'help_text' => 'Diş kliniğinizdeki tedavi seçenekleri ve uzmanlık alanları',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Genel muayene', 'value' => 'general_exam'],
                    ['label' => 'Dolgu', 'value' => 'filling'],
                    ['label' => 'Kanal tedavisi', 'value' => 'root_canal'],
                    ['label' => 'Çekim', 'value' => 'extraction'],
                    ['label' => 'İmplant', 'value' => 'implant'],
                    ['label' => 'Protez', 'value' => 'prosthesis'],
                    ['label' => 'Ortodonti', 'value' => 'orthodontics'],
                    ['label' => 'Beyazlatma', 'value' => 'whitening'],
                    ['label' => 'Estetik diş', 'value' => 'cosmetic'],
                    ['label' => 'Periodontal tedavi', 'value' => 'periodontal'],
                    ['label' => 'Çocuk diş', 'value' => 'pediatric'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 85,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // ===========================================
        // 3. EĞİTİM SEKTÖRLERİ 🎓
        // ===========================================
        
        // EĞİTİM GENEL
        $educationQuestions = [
            [
                'sector_code' => 'education', 'step' => 3, 'section' => null,
                'question_key' => 'education_programs', 'question_text' => 'Hangi eğitim programlarınız bulunuyor?',
                'help_text' => 'Sunduğunuz eğitim program türleri ve seviyeler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Okul öncesi', 'value' => 'preschool'],
                    ['label' => 'İlkokul', 'value' => 'primary'],
                    ['label' => 'Ortaokul', 'value' => 'middle'],
                    ['label' => 'Lise', 'value' => 'high_school'],
                    ['label' => 'Üniversite', 'value' => 'university'],
                    ['label' => 'Yüksek lisans', 'value' => 'masters'],
                    ['label' => 'Doktora', 'value' => 'phd'],
                    ['label' => 'Sertifika programları', 'value' => 'certificate'],
                    ['label' => 'Online eğitim', 'value' => 'online'],
                    ['label' => 'Kurumsal eğitim', 'value' => 'corporate'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 1, 'ai_weight' => 90,
                'category' => 'sector', 'ai_priority' => 1, 'always_include' => true,
                'context_category' => 'service_portfolio'
            ]
        ];

        // OKUL
        $schoolQuestions = [
            [
                'sector_code' => 'school', 'step' => 3, 'section' => null,
                'question_key' => 'education_levels_detailed', 'question_text' => 'Hangi eğitim seviyelerinde hizmet veriyorsunuz?',
                'help_text' => 'Eğitim kurumunuzda bulunan sınıf seviyeleri ve programlar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Anaokulu', 'value' => 'kindergarten'],
                    ['label' => 'İlkokul', 'value' => 'elementary'],
                    ['label' => 'Ortaokul', 'value' => 'middle_school'],
                    ['label' => 'Lise', 'value' => 'high_school'],
                    ['label' => 'Üniversite hazırlık', 'value' => 'university_prep'],
                    ['label' => 'Yetişkin eğitimi', 'value' => 'adult_education'],
                    ['label' => 'Özel eğitim', 'value' => 'special_education'],
                    ['label' => 'Yetenekli çocuklar', 'value' => 'gifted_children'],
                    ['label' => 'Online eğitim', 'value' => 'online_education'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // DİL KURSU
        $languageQuestions = [
            [
                'sector_code' => 'language', 'step' => 3, 'section' => null,
                'question_key' => 'languages_offered_detailed', 'question_text' => 'Hangi dillerde eğitim veriyorsunuz?',
                'help_text' => 'Dil kursunuzda öğretilen yabancı diller ve seviyeler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'İngilizce', 'value' => 'english'],
                    ['label' => 'Almanca', 'value' => 'german'],
                    ['label' => 'Fransızca', 'value' => 'french'],
                    ['label' => 'İtalyanca', 'value' => 'italian'],
                    ['label' => 'İspanyolca', 'value' => 'spanish'],
                    ['label' => 'Rusça', 'value' => 'russian'],
                    ['label' => 'Arapça', 'value' => 'arabic'],
                    ['label' => 'Çince', 'value' => 'chinese'],
                    ['label' => 'Japonca', 'value' => 'japanese'],
                    ['label' => 'IELTS/TOEFL hazırlık', 'value' => 'ielts_toefl'],
                    ['label' => 'İş İngilizcesi', 'value' => 'business_english'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // Tüm soru gruplarını birleştir
        $allQuestions = array_merge(
            $webQuestions, 
            $technologyQuestions, 
            $mobileQuestions,
            $healthQuestions, 
            $hospitalQuestions, 
            $dentalQuestions,
            $educationQuestions, 
            $schoolQuestions, 
            $languageQuestions
        );

        foreach ($allQuestions as $question) {
            // Duplicate question_key kontrolü
            $exists = DB::table('ai_profile_questions')
                ->where('question_key', $question['question_key'])
                ->exists();
                
            if (!$exists) {
                DB::table('ai_profile_questions')->insert(array_merge($question, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            } else {
                echo "⚠️ Question key '{$question['question_key']}' zaten var, atlandı\n";
            }
        }

        echo "❓ Part 1: " . count($allQuestions) . " organize edilmiş sektör sorusu eklendi\n";
    }
    
    /**
     * Temel ana kategoriler ve sektörleri oluştur
     */
    private function createBasicStructure(): void
    {
        echo "📥 Temel ana kategoriler ve sektörler yükleniyor...\n";
        
        // Önce ana kategorileri oluştur
        $this->createMainCategories();
        
        // Sonra temel sektörleri oluştur
        $this->createBasicSectors();
        
        echo "✅ Temel sektörler başarıyla yüklendi!\n";
        
        // Final check - toplam sektör sayısını göster
        $totalSectors = DB::table('ai_profile_sectors')->count();
        echo "📊 Veritabanında toplam {$totalSectors} sektör var\n";
    }
    
    /**
     * Ana kategorileri oluştur
     */
    private function createMainCategories(): void
    {
        $mainCategories = [
            ['id' => 1, 'code' => 'teknoloji', 'name' => 'Teknoloji', 'description' => 'Teknoloji ve bilişim sektörleri', 'emoji' => '💻', 'color' => 'primary', 'keywords' => 'teknoloji,bilişim,yazılım,web'],
            ['id' => 2, 'code' => 'pazarlama', 'name' => 'Pazarlama', 'description' => 'Pazarlama ve reklam sektörleri', 'emoji' => '📈', 'color' => 'success', 'keywords' => 'pazarlama,reklam,marketing'],
            ['id' => 3, 'code' => 'hizmet', 'name' => 'Hizmet', 'description' => 'Hizmet sektörleri', 'emoji' => '🤝', 'color' => 'warning', 'keywords' => 'hizmet,danışmanlık,service'],
            ['id' => 4, 'code' => 'ticaret', 'name' => 'Ticaret', 'description' => 'Ticaret ve e-ticaret', 'emoji' => '🛒', 'color' => 'danger', 'keywords' => 'ticaret,satış,e-ticaret'],
            ['id' => 5, 'code' => 'saglik', 'name' => 'Sağlık', 'description' => 'Sağlık ve tıp sektörleri', 'emoji' => '⚕️', 'color' => 'info', 'keywords' => 'sağlık,tıp,hastane'],
            ['id' => 6, 'code' => 'egitim', 'name' => 'Eğitim', 'description' => 'Eğitim ve öğretim', 'emoji' => '🎓', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,school'],
            ['id' => 7, 'code' => 'yemek_icecek', 'name' => 'Yemek & İçecek', 'description' => 'Yemek ve içecek sektörleri', 'emoji' => '🍽️', 'color' => 'orange', 'keywords' => 'yemek,içecek,restoran'],
            ['id' => 8, 'code' => 'sanat_tasarim', 'name' => 'Sanat & Tasarım', 'description' => 'Sanat ve tasarım sektörleri', 'emoji' => '🎨', 'color' => 'purple', 'keywords' => 'sanat,tasarım,grafik'],
            ['id' => 9, 'code' => 'spor_wellness', 'name' => 'Spor & Wellness', 'description' => 'Spor ve sağlık sektörleri', 'emoji' => '🏃', 'color' => 'green', 'keywords' => 'spor,fitness,wellness'],
            ['id' => 10, 'code' => 'otomotiv', 'name' => 'Otomotiv', 'description' => 'Otomotiv ve ulaşım', 'emoji' => '🚗', 'color' => 'dark', 'keywords' => 'otomotiv,ulaşım,araba'],
            ['id' => 11, 'code' => 'finans_sigorta', 'name' => 'Finans & Sigorta', 'description' => 'Finans ve sigorta sektörleri', 'emoji' => '💰', 'color' => 'yellow', 'keywords' => 'finans,sigorta,banka'],
            ['id' => 12, 'code' => 'hukuk', 'name' => 'Hukuk', 'description' => 'Hukuk ve danışmanlık', 'emoji' => '⚖️', 'color' => 'indigo', 'keywords' => 'hukuk,avukat,legal'],
            ['id' => 13, 'code' => 'emlak_insaat', 'name' => 'Emlak & İnşaat', 'description' => 'Emlak ve inşaat sektörleri', 'emoji' => '🏠', 'color' => 'blue', 'keywords' => 'emlak,inşaat,ev'],
            ['id' => 14, 'code' => 'guzellik_bakim', 'name' => 'Güzellik & Bakım', 'description' => 'Güzellik ve bakım sektörleri', 'emoji' => '💄', 'color' => 'rose', 'keywords' => 'güzellik,bakım,kuaför'],
            ['id' => 18, 'code' => 'diger_hizmetler', 'name' => 'Diğer Hizmetler', 'description' => 'Diğer hizmet sektörleri', 'emoji' => '🔧', 'color' => 'secondary', 'keywords' => 'diğer,hizmet,genel']
        ];
        
        foreach ($mainCategories as $category) {
            try {
                DB::table('ai_profile_sectors')->insert([
                    'id' => $category['id'],
                    'code' => $category['code'],
                    'name' => $category['name'],
                    'category_id' => null,
                    'description' => $category['description'],
                    'emoji' => $category['emoji'],
                    'icon' => null,
                    'color' => $category['color'],
                    'keywords' => $category['keywords'],
                    'is_subcategory' => 0,
                    'is_active' => 1,
                    'sort_order' => $category['id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                echo "⚠️ Ana kategori ID {$category['id']} atlandı: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ 18 ana kategori eklendi!\n";
    }
    
    /**
     * Temel sektörleri oluştur
     */
    private function createBasicSectors(): void
    {
        $basicSectors = [
            // Teknoloji Alt Sektörleri
            ['id' => 21, 'code' => 'web', 'name' => 'Web Tasarım', 'category_id' => 1, 'description' => 'Website tasarım, UI/UX', 'emoji' => '🌐', 'color' => 'primary', 'keywords' => 'web,tasarım,ui,ux,website'],
            ['id' => 22, 'code' => 'software', 'name' => 'Yazılım Geliştirme', 'category_id' => 1, 'description' => 'Mobil ve web uygulamaları', 'emoji' => '⚡', 'color' => 'primary', 'keywords' => 'yazılım,geliştirme,kod,programming'],
            ['id' => 23, 'code' => 'mobile', 'name' => 'Mobil Uygulama', 'category_id' => 1, 'description' => 'iOS ve Android uygulamaları', 'emoji' => '📱', 'color' => 'primary', 'keywords' => 'mobil,uygulama,ios,android,app'],
            ['id' => 24, 'code' => 'graphic_design', 'name' => 'Grafik Tasarım', 'category_id' => 8, 'description' => 'Logo, kurumsal kimlik', 'emoji' => '🎨', 'color' => 'purple', 'keywords' => 'grafik,tasarım,logo,kimlik'],
            ['id' => 25, 'code' => 'cybersecurity', 'name' => 'Siber Güvenlik', 'category_id' => 1, 'description' => 'Siber güvenlik hizmetleri', 'emoji' => '🔒', 'color' => 'primary', 'keywords' => 'güvenlik,siber,security,koruma'],
            
            // Pazarlama Alt Sektörleri
            ['id' => 26, 'code' => 'digital_marketing', 'name' => 'Dijital Pazarlama', 'category_id' => 2, 'description' => 'SEO, SEM, sosyal medya', 'emoji' => '🚀', 'color' => 'success', 'keywords' => 'dijital,pazarlama,seo,sem,sosyal'],
            ['id' => 27, 'code' => 'social_media', 'name' => 'Sosyal Medya', 'category_id' => 2, 'description' => 'Sosyal medya yönetimi', 'emoji' => '📲', 'color' => 'success', 'keywords' => 'sosyal,medya,instagram,facebook,twitter'],
            ['id' => 28, 'code' => 'advertising', 'name' => 'Reklam Ajansı', 'category_id' => 2, 'description' => 'Reklam ve tanıtım', 'emoji' => '📢', 'color' => 'success', 'keywords' => 'reklam,ajans,tanıtım,advertising'],
            
            // Hizmet Alt Sektörleri
            ['id' => 29, 'code' => 'consulting', 'name' => 'Danışmanlık', 'category_id' => 3, 'description' => 'İş danışmanlığı', 'emoji' => '💡', 'color' => 'warning', 'keywords' => 'danışmanlık,iş,consulting'],
            ['id' => 30, 'code' => 'accounting', 'name' => 'Muhasebe', 'category_id' => 3, 'description' => 'Muhasebe ve finans', 'emoji' => '🧮', 'color' => 'warning', 'keywords' => 'muhasebe,finans,accounting'],
            ['id' => 31, 'code' => 'cleaning_services', 'name' => 'Temizlik Hizmeti', 'category_id' => 3, 'description' => 'Temizlik hizmetleri', 'emoji' => '🧹', 'color' => 'warning', 'keywords' => 'temizlik,hijyen,cleaning'],
            
            // Ticaret Alt Sektörleri
            ['id' => 32, 'code' => 'retail', 'name' => 'Perakende', 'category_id' => 4, 'description' => 'Perakende satış', 'emoji' => '🛍️', 'color' => 'danger', 'keywords' => 'perakende,mağaza,satış'],
            ['id' => 33, 'code' => 'ecommerce', 'name' => 'E-ticaret', 'category_id' => 4, 'description' => 'Online satış', 'emoji' => '🛒', 'color' => 'danger', 'keywords' => 'e-ticaret,online,satış'],
            
            // Sağlık Alt Sektörleri
            ['id' => 34, 'code' => 'health', 'name' => 'Sağlık', 'category_id' => 5, 'description' => 'Genel sağlık hizmetleri', 'emoji' => '⚕️', 'color' => 'info', 'keywords' => 'sağlık,tıp,doktor'],
            ['id' => 35, 'code' => 'hospital', 'name' => 'Hastane', 'category_id' => 5, 'description' => 'Hastane hizmetleri', 'emoji' => '🏥', 'color' => 'info', 'keywords' => 'hastane,tıp,tedavi'],
            ['id' => 36, 'code' => 'dental', 'name' => 'Diş Hekimliği', 'category_id' => 5, 'description' => 'Diş tedavisi', 'emoji' => '🦷', 'color' => 'info', 'keywords' => 'diş,hekimliği,dental'],
            
            // Eğitim Alt Sektörleri
            ['id' => 37, 'code' => 'education', 'name' => 'Eğitim', 'category_id' => 6, 'description' => 'Genel eğitim', 'emoji' => '🎓', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,school'],
            ['id' => 38, 'code' => 'school', 'name' => 'Okul', 'category_id' => 6, 'description' => 'Okul eğitimi', 'emoji' => '🏫', 'color' => 'secondary', 'keywords' => 'okul,eğitim,öğrenci'],
            ['id' => 39, 'code' => 'language', 'name' => 'Dil Kursu', 'category_id' => 6, 'description' => 'Dil eğitimi', 'emoji' => '🗣️', 'color' => 'secondary', 'keywords' => 'dil,kurs,language'],
            
            // Yemek & İçecek Alt Sektörleri
            ['id' => 40, 'code' => 'food', 'name' => 'Yemek & İçecek', 'category_id' => 7, 'description' => 'Restoran ve yemek', 'emoji' => '🍽️', 'color' => 'orange', 'keywords' => 'yemek,içecek,restoran'],
            ['id' => 41, 'code' => 'restaurant', 'name' => 'Restoran', 'category_id' => 7, 'description' => 'Restoran hizmetleri', 'emoji' => '🍴', 'color' => 'orange', 'keywords' => 'restoran,yemek,meal'],
            ['id' => 42, 'code' => 'cafe', 'name' => 'Kafe', 'category_id' => 7, 'description' => 'Kafe ve kahve', 'emoji' => '☕', 'color' => 'orange', 'keywords' => 'kafe,kahve,coffee'],
            
            // Spor Alt Sektörleri
            ['id' => 43, 'code' => 'sports', 'name' => 'Spor', 'category_id' => 9, 'description' => 'Spor ve fitness', 'emoji' => '⚽', 'color' => 'green', 'keywords' => 'spor,fitness,antrenman'],
            ['id' => 44, 'code' => 'fitness', 'name' => 'Fitness', 'category_id' => 9, 'description' => 'Fitness ve spor salonu', 'emoji' => '🏋️', 'color' => 'green', 'keywords' => 'fitness,spor,gym'],
            
            // Otomotiv Alt Sektörleri
            ['id' => 45, 'code' => 'automotive', 'name' => 'Otomotiv', 'category_id' => 10, 'description' => 'Araç satış ve servis', 'emoji' => '🚗', 'color' => 'dark', 'keywords' => 'otomotiv,araç,car'],
            
            // Finans Alt Sektörleri
            ['id' => 46, 'code' => 'finance', 'name' => 'Finans', 'category_id' => 11, 'description' => 'Finansal hizmetler', 'emoji' => '💰', 'color' => 'yellow', 'keywords' => 'finans,para,money'],
            
            // Hukuk Alt Sektörleri
            ['id' => 47, 'code' => 'legal', 'name' => 'Hukuk', 'category_id' => 12, 'description' => 'Hukuki hizmetler', 'emoji' => '⚖️', 'color' => 'indigo', 'keywords' => 'hukuk,avukat,legal'],
            
            // İnşaat Alt Sektörleri
            ['id' => 48, 'code' => 'construction', 'name' => 'İnşaat', 'category_id' => 13, 'description' => 'İnşaat hizmetleri', 'emoji' => '🏗️', 'color' => 'blue', 'keywords' => 'inşaat,yapı,construction'],
            
            // Sanat Alt Sektörleri
            ['id' => 49, 'code' => 'art_design', 'name' => 'Sanat & Tasarım', 'category_id' => 8, 'description' => 'Sanat ve tasarım', 'emoji' => '🎨', 'color' => 'purple', 'keywords' => 'sanat,tasarım,art'],
            
            // Güzellik Alt Sektörleri
            ['id' => 50, 'code' => 'beauty', 'name' => 'Güzellik', 'category_id' => 14, 'description' => 'Güzellik ve bakım', 'emoji' => '💄', 'color' => 'rose', 'keywords' => 'güzellik,bakım,beauty']
        ];
        
        $addedCount = 0;
        foreach ($basicSectors as $sector) {
            try {
                DB::table('ai_profile_sectors')->insert([
                    'id' => $sector['id'],
                    'code' => $sector['code'],
                    'name' => $sector['name'],
                    'category_id' => $sector['category_id'],
                    'description' => $sector['description'],
                    'emoji' => $sector['emoji'],
                    'icon' => null,
                    'color' => $sector['color'],
                    'keywords' => $sector['keywords'],
                    'is_subcategory' => 1,
                    'is_active' => 1,
                    'sort_order' => $sector['id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $addedCount++;
            } catch (\Exception $e) {
                echo "⚠️ Sektör ID {$sector['id']} atlandı: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$addedCount} temel sektör eklendi!\n";
    }
    
    /**
     * Kalan sektörlere genel sorular ekle
     */
    private function addGeneralSectorQuestions(): void
    {
        echo "📋 Temel sektörlere genel sorular ekleniyor...\n";
        
        // Her sektör için genel soru tanımları
        $sectorQuestions = [
            // TEKNOLOJİ SEKTÖRLERİ
            'technology' => [
                'question_text' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'options' => '["Yazılım geliştirme", "Web tasarım", "Mobil uygulama", "Sistem yönetimi", "Siber güvenlik", "Veri analizi", "Bulut çözümleri", "IT danışmanlığı", "Teknik destek", "E-ticaret çözümleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'software' => [
                'question_text' => 'Hangi yazılım geliştirme alanlarında uzmanlaştınız?',
                'options' => '["Web uygulamaları", "Masaüstü yazılım", "Mobil app", "API geliştirme", "Veritabanı tasarım", "ERP sistemi", "CRM sistemi", "E-ticaret platformu", "Blockchain", "AI/ML", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'cybersecurity' => [
                'question_text' => 'Hangi siber güvenlik hizmetlerini veriyorsunuz?',
                'options' => '["Penetrasyon testi", "Güvenlik denetimi", "Firewall kurulumu", "Antivirus çözümleri", "Veri şifreleme", "Güvenlik eğitimi", "SOC hizmetleri", "KVKK uyumluluk", "Incident response", "Güvenlik danışmanlığı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'digital_marketing' => [
                'question_text' => 'Hangi dijital pazarlama hizmetlerini sunuyorsunuz?',
                'options' => '["Google Ads", "Facebook Ads", "Instagram Ads", "SEO", "SEM", "Social Media Management", "İçerik pazarlama", "Email pazarlama", "Influencer pazarlama", "Analytics & raporlama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'advertising' => [
                'question_text' => 'Hangi reklam ve tanıtım hizmetlerini sağlıyorsunuz?',
                'options' => '["Kreatif tasarım", "Marka yönetimi", "Medya planlama", "Outdoor reklam", "Dijital reklam", "TV/Radyo reklam", "Basın ilanları", "Etkinlik organizasyonu", "Sponsorluk", "PR hizmetleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'consulting' => [
                'question_text' => 'Hangi danışmanlık hizmetlerini sunuyorsunuz?',
                'options' => '["Yönetim danışmanlığı", "Stratejik planlama", "İnsan kaynakları", "Finansal danışmanlık", "Pazarlama danışmanlığı", "Operasyonel iyileştirme", "Dijital dönüşüm", "Kalite yönetimi", "Risk yönetimi", "Proje yönetimi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'accounting' => [
                'question_text' => 'Hangi muhasebe ve finans hizmetlerini veriyorsunuz?',
                'options' => '["Defter tutma", "Vergi beyannameleri", "SGK işlemleri", "Bordro hazırlama", "Mali müşavirlik", "Bağımsız denetim", "Finansal analiz", "Bütçe hazırlama", "Maliyet analizi", "Vergi optimizasyonu", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'retail' => [
                'question_text' => 'Hangi perakende hizmetlerini sunuyorsunuz?',
                'options' => '["Mağaza satışı", "Online satış", "Toptan satış", "Müşteri hizmetleri", "Kargo/teslimat", "İade/değişim", "Satış sonrası destek", "Ürün danışmanlığı", "Garanti hizmetleri", "Özel sipariş", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'ecommerce' => [
                'question_text' => 'Hangi e-ticaret hizmetlerini sağlıyorsunuz?',
                'options' => '["Online mağaza", "Marketplace satış", "B2B e-ticaret", "Dropshipping", "Dijital pazarlama", "Lojistik yönetimi", "Ödeme sistemleri", "Müşteri destek", "Envanter yönetimi", "Analytics", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'fitness' => [
                'question_text' => 'Hangi fitness ve spor hizmetlerini sunuyorsunuz?',
                'options' => '["Kişisel antrenörlük", "Grup dersleri", "Fitness programları", "Beslenme danışmanlığı", "Spor masajı", "Rehabilitasyon", "Yoga/Pilates", "Cardio eğitimi", "Güç antrenmanı", "Spor psikologu", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'finance' => [
                'question_text' => 'Hangi finans hizmetlerini sunuyorsunuz?',
                'options' => '["Yatırım danışmanlığı", "Kredi danışmanlığı", "Sigorta aracılığı", "Emlak finansmanı", "Emeklilik planlaması", "Portföy yönetimi", "Finansal planlama", "Vergi danışmanlığı", "Borsa işlemleri", "Döviz işlemleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'legal' => [
                'question_text' => 'Hangi hukuki hizmetleri sunuyorsunuz?',
                'options' => '["Ticaret hukuku", "İş hukuku", "Aile hukuku", "Ceza hukuku", "Emlak hukuku", "Şirket kuruluşu", "Sözleşme hazırlama", "Dava takibi", "Arabuluculuk", "Hukuki danışmanlık", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'construction' => [
                'question_text' => 'Hangi inşaat hizmetlerini sunuyorsunuz?',
                'options' => '["Konut inşaatı", "Ticari yapı", "Endüstriyel yapı", "Tadilat/renovasyon", "İç mimarlık", "Peyzaj", "Proje yönetimi", "Mimari tasarım", "Müteahhitlik", "Ruhsat işlemleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'art_design' => [
                'question_text' => 'Hangi sanat ve tasarım hizmetlerini sunuyorsunuz?',
                'options' => '["Grafik tasarım", "Logo tasarımı", "Kurumsal kimlik", "Web tasarımı", "Ambalaj tasarımı", "İllüstrasyon", "Fotoğrafçılık", "Video editing", "Motion graphics", "Sanat eserleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'beauty' => [
                'question_text' => 'Hangi güzellik ve bakım hizmetlerini sunuyorsunuz?',
                'options' => '["Kuaförlük", "Makyaj", "Cilt bakımı", "Nail art", "Masaj", "Epilasyon", "Kaş tasarımı", "Saç bakımı", "Estetik uygulamalar", "Güzellik danışmanlığı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ]
        ];
        
        $addedCount = 0;
        
        foreach ($sectorQuestions as $sectorCode => $questionData) {
            $question = [
                'sector_code' => $sectorCode,
                'step' => 3,
                'section' => null,
                'question_key' => $sectorCode . '_specific_services',
                'question_text' => $questionData['question_text'],
                'help_text' => 'Sektörünüzdeki spesifik hizmet ve ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => $questionData['options'],
                'validation_rules' => null,
                'depends_on' => null,
                'show_if' => null,
                'is_required' => 0,
                'is_active' => 1,
                'sort_order' => 5,
                'priority' => 2,
                'ai_weight' => 85,
                'category' => 'company',
                'ai_priority' => 2,
                'always_include' => 0,
                'context_category' => 'service_portfolio'
            ];
            
            // Duplicate question_key kontrolü
            $exists = DB::table('ai_profile_questions')
                ->where('question_key', $question['question_key'])
                ->exists();
                
            if (!$exists) {
                DB::table('ai_profile_questions')->insert(array_merge($question, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                $addedCount++;
                
                if ($addedCount % 5 == 0) {
                    echo "📊 {$addedCount} sektöre genel soru eklendi...\n";
                }
            } else {
                echo "⚠️ Question key '{$question['question_key']}' zaten var, atlandı\n";
            }
        }
        
        echo "✅ Temel sektörlere {$addedCount} genel soru eklendi!\n";
    }
}