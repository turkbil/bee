<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part1 extends Seeder
{
    /**
     * SECTOR SEEDER PART 1 (ID 1-50)
     * Teknoloji, Sağlık, Eğitim sektörleri + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 1 yükleniyor (ID 1-50)...\n";

        // Mevcut sektörleri temizle (sadece Part 1'de)
        DB::table('ai_profile_sectors')->truncate();
        DB::table('ai_profile_questions')->whereBetween('id', [6000, 6999])->delete();

        // ÖNCE SQL dosyasından 162 detaylı sektörü yükle
        $this->restoreFromSQL();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();
        
        // SQL'den gelen 162 sektöre genel sorular ekle
        $this->addSQLSectorQuestions();

        echo "✅ Part 1 tamamlandı! (Teknoloji, Sağlık, Eğitim)\n";
    }
    
    
    private function addSectorQuestions(): void
    {
        // ORGANIZE EDİLEN SEKTÖR SORULARI - HER SEKTÖR KENDI DOSYASINDA
        
        // ===========================================
        // 1. TEKNOLOJİ VE WEB SEKTÖRLERI 🔧
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
        // 2. SAĞLIK SEKTÖRLERI 🏥
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
        // 3. EĞİTİM SEKTÖRLERI 🎓
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
     * SQL dosyasından 162 sektörü restore et
     */
    private function restoreFromSQL(): void
    {
        echo "📥 SQL dosyasından 162 sektör yükleniyor...\n";
        
        $sqlBackupPath = '/mnt/c/Users/nurul/Downloads/ai_profile_sectors.sql';
        
        if (!file_exists($sqlBackupPath)) {
            echo "⚠️ SQL dosyası bulunamadı\n";
            return;
        }
        
        try {
            $sqlContent = file_get_contents($sqlBackupPath);
            
            // Daha güçlü regex pattern - tüm SQL INSERT değerlerini yakalar
            $pattern = '/\((\d+),\s*\'([^\']+)\',\s*(NULL|\d+),\s*\'([^\']*(?:\'\'[^\']*)*)\',\s*(NULL|\'[^\']*\'?),\s*(NULL|\'[^\']*\'?),\s*(NULL|\'[^\']*\'?),\s*\'([^\']*(?:\'\'[^\']*)*)\',\s*(NULL|\'[^\']*(?:\'\'[^\']*)*\'?),\s*(\d+),\s*(\d+),\s*(\d+),\s*\'([^\']+)\',\s*\'([^\']+)\'\)/s';
            
            preg_match_all($pattern, $sqlContent, $matches, PREG_SET_ORDER);
            
            echo "🔍 Toplam " . count($matches) . " sektör bulundu\n";
            
            $addedCount = 0;
            foreach ($matches as $match) {
                // Null değerleri düzgün parse et
                $categoryId = $match[3] === 'NULL' ? null : (int) $match[3];
                $icon = $match[5] === 'NULL' ? null : trim($match[5], "'");
                $emoji = $match[6] === 'NULL' ? null : trim($match[6], "'");
                $color = $match[7] === 'NULL' ? null : trim($match[7], "'");
                $keywords = $match[9] === 'NULL' ? null : trim($match[9], "'");
                
                $sectorData = [
                    'id' => (int) $match[1],
                    'code' => $match[2],
                    'category_id' => $categoryId,
                    'name' => $match[4],
                    'icon' => $icon,
                    'emoji' => $emoji,
                    'color' => $color,
                    'description' => $match[8],
                    'keywords' => $keywords,
                    'is_subcategory' => (int) $match[10],
                    'is_active' => (int) $match[11],
                    'sort_order' => (int) $match[12],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                try {
                    DB::table('ai_profile_sectors')->insert($sectorData);
                    $addedCount++;
                    
                    if ($addedCount % 20 == 0) {
                        echo "📊 {$addedCount} sektör eklendi...\n";
                    }
                } catch (\Exception $e) {
                    // ID çakışması durumunda geç
                    echo "⚠️ ID {$sectorData['id']} atlandı: " . $e->getMessage() . "\n";
                    continue;
                }
            }
            
            echo "✅ SQL'den {$addedCount} sektör başarıyla yüklendi!\n";
            
            // Final check - toplam sektör sayısını göster
            $totalSectors = DB::table('ai_profile_sectors')->count();
            echo "📊 Veritabanında toplam {$totalSectors} sektör var\n";
            
        } catch (\Exception $e) {
            echo "⚠️ SQL parse hatası: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * SQL'den gelen 162 sektöre somut hizmet soruları ekle
     */
    private function addSQLSectorQuestions(): void
    {
        echo "📋 SQL sektörlerine somut hizmet soruları ekleniyor...\n";
        
        // Her sektör için özel soru tanımları
        $sectorQuestions = [
            // TEKNOLOJİ SEKTÖRLERI
            'technology' => [
                'question_text' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'options' => '["Yazılım geliştirme", "Web tasarım", "Mobil uygulama", "Sistem yönetimi", "Siber güvenlik", "Veri analizi", "Bulut çözümleri", "IT danışmanlığı", "Teknik destek", "E-ticaret çözümleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'web' => [
                'question_text' => 'Hangi web tasarım ve geliştirme hizmetlerini veriyorsunuz?',
                'options' => '["Kurumsal web sitesi", "E-ticaret sitesi", "Blog & portfolio", "Landing page", "WordPress", "Laravel", "React/Vue", "SEO optimizasyonu", "Hosting & domain", "Web bakım", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'mobile' => [
                'question_text' => 'Hangi mobil uygulama platformlarında geliştirme yapıyorsunuz?',
                'options' => '["iOS (Swift)", "Android (Java/Kotlin)", "React Native", "Flutter", "Ionic", "Progressive Web App", "Xamarin", "Unity oyun", "App Store yayınlama", "App bakım", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'software' => [
                'question_text' => 'Hangi yazılım geliştirme alanlarında uzmanlaştınız?',
                'options' => '["Web uygulamaları", "Masaüstü yazılım", "Mobil app", "API geliştirme", "Veritabanı tasarım", "ERP sistemi", "CRM sistemi", "E-ticaret platformu", "Blockchain", "AI/ML", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'graphic_design' => [
                'question_text' => 'Hangi grafik tasarım hizmetlerini sağlıyorsunuz?',
                'options' => '["Logo tasarım", "Kurumsal kimlik", "Web tasarım", "UI/UX tasarım", "Baskı tasarımı", "Sosyal medya tasarımı", "Ambalaj tasarımı", "İllüstrasyon", "3D tasarım", "Video grafik", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'digital_marketing' => [
                'question_text' => 'Hangi dijital pazarlama hizmetlerini sunuyorsunuz?',
                'options' => '["Google Ads", "Facebook Ads", "Instagram Ads", "SEO", "SEM", "Social Media Management", "İçerik pazarlama", "Email pazarlama", "Influencer pazarlama", "Analytics & raporlama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'cybersecurity' => [
                'question_text' => 'Hangi siber güvenlik hizmetlerini veriyorsunuz?',
                'options' => '["Penetrasyon testi", "Güvenlik denetimi", "Firewall kurulumu", "Antivirus çözümleri", "Veri şifreleme", "Güvenlik eğitimi", "SOC hizmetleri", "KVKK uyumluluk", "Incident response", "Güvenlik danışmanlığı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // SAĞLIK SEKTÖRLERI
            'health' => [
                'question_text' => 'Hangi sağlık hizmetlerini sunuyorsunuz?',
                'options' => '["Genel muayene", "Uzman doktor", "Ameliyat", "Laboratuvar", "Radyoloji", "Acil servis", "Yoğun bakım", "Fizik tedavi", "Psikiyatri", "Check-up", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'hospital' => [
                'question_text' => 'Hastanenizde hangi tıbbi bölümler bulunuyor?',
                'options' => '["Dahiliye", "Genel Cerrahi", "Kadın Doğum", "Çocuk Sağlığı", "Kardiyoloji", "Nöroloji", "Ortopedi", "KBB", "Göz", "Üroloji", "Dermatoloji", "Acil Servis", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'dental' => [
                'question_text' => 'Hangi diş tedavilerini uyguluyorsunuz?',
                'options' => '["Genel muayene", "Dolgu", "Kanal tedavisi", "Çekim", "İmplant", "Protez", "Ortodonti", "Beyazlatma", "Estetik diş", "Periodontal tedavi", "Çocuk diş", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'aesthetic' => [
                'question_text' => 'Hangi estetik ve plastik cerrahi işlemlerini yapıyorsunuz?',
                'options' => '["Botoks", "Dolgu", "Rinoplasti", "Liposuction", "Meme estetiği", "Karın germe", "Saç ekimi", "Lazer epilasyon", "Cilt yenileme", "Göz kapağı estetiği", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'pharmacy' => [
                'question_text' => 'Eczanenizde hangi ürün ve hizmetleri sunuyorsunuz?',
                'options' => '["Reçeteli ilaçlar", "Reçetesiz ilaçlar", "Vitamin & takviye", "Kozmetik ürünler", "Bebek ürünleri", "Tıbbi cihazlar", "İlaç danışmanlığı", "Tansiyon ölçümü", "Online sipariş", "Evde ilaç teslimatı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // EĞİTİM SEKTÖRLERI
            'education' => [
                'question_text' => 'Hangi eğitim hizmetlerini sunuyorsunuz?',
                'options' => '["Okul öncesi", "İlkokul", "Ortaokul", "Lise", "Üniversite", "Yetişkin eğitimi", "Sertifika programları", "Online eğitim", "Özel ders", "Kurumsal eğitim", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'school' => [
                'question_text' => 'Okulunuzda hangi eğitim seviyeleri bulunuyor?',
                'options' => '["Anaokulu", "İlkokul", "Ortaokul", "Lise", "Fen lisesi", "Anadolu lisesi", "Meslek lisesi", "Özel eğitim", "Yetenek geliştirme", "Olimpiyat hazırlık", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'language' => [
                'question_text' => 'Hangi dillerde eğitim veriyorsunuz?',
                'options' => '["İngilizce", "Almanca", "Fransızca", "İtalyanca", "İspanyolca", "Rusça", "Arapça", "Çince", "Japonca", "IELTS/TOEFL hazırlık", "İş dili", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // YEMEK & İÇECEK SEKTÖRLERI
            'food' => [
                'question_text' => 'Hangi yemek ve içecek hizmetlerini sunuyorsunuz?',
                'options' => '["Restoran", "Cafe", "Fast food", "Catering", "Ev yemekleri", "Organik gıda", "Vegan menü", "Glutensiz menü", "Paket servis", "Online sipariş", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'restaurant' => [
                'question_text' => 'Restoranınızın mutfak türü ve özellikleri nelerdir?',
                'options' => '["Türk mutfağı", "İtalyan mutfağı", "Uzak Doğu", "Fast food", "Seafood", "Vejetaryen", "Vegan", "Organik", "Fine dining", "Aile restoranı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            'cafe' => [
                'question_text' => 'Kafenizde hangi ürün ve hizmetleri sunuyorsunuz?',
                'options' => '["Espresso kahveler", "Filtre kahve", "Soğuk kahveler", "Çay çeşitleri", "Tatlılar", "Sandviç & salata", "WiFi", "Çalışma alanı", "Etkinlik alanı", "Takeaway", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // PERAKENDE & E-TİCARET
            'retail' => [
                'question_text' => 'Hangi perakende ve e-ticaret hizmetlerini sunuyorsunuz?',
                'options' => '["Online mağaza", "Fiziki mağaza", "Toptan satış", "Perakende satış", "Kargo & teslimat", "Müşteri hizmetleri", "İade & değişim", "Ödeme sistemleri", "Mobil uygulama", "Sadakat programı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // İNŞAAT & EMLAK
            'construction' => [
                'question_text' => 'Hangi inşaat ve emlak hizmetlerini veriyorsunuz?',
                'options' => '["Konut inşaatı", "Ticari inşaat", "Tadilat & renovasyon", "İç mimarlık", "Proje tasarımı", "Emlak danışmanlığı", "Emlak değerleme", "Kiralama", "Satış", "Yatırım danışmanlığı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // FİNANS & MUHASEBE
            'finance' => [
                'question_text' => 'Hangi finans ve muhasebe hizmetlerini sağlıyorsunuz?',
                'options' => '["Mali müşavirlik", "Defter tutma", "Vergi danışmanlığı", "SGK işlemleri", "Bordro hazırlama", "Finansal analiz", "Kredi danışmanlığı", "Yatırım danışmanlığı", "Sigorta", "Emeklilik planlaması", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // SANAT & TASARIM
            'art_design' => [
                'question_text' => 'Hangi sanat ve tasarım hizmetlerini sunuyorsunuz?',
                'options' => '["Grafik tasarım", "İç mimarlık", "Endüstriyel tasarım", "Moda tasarımı", "Resim & heykel", "Fotoğrafçılık", "Video prodüksiyon", "Müzik prodüksiyonu", "Sanat eğitimi", "Sanat danışmanlığı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // SPOR & FITNESS
            'sports' => [
                'question_text' => 'Hangi spor ve fitness hizmetlerini veriyorsunuz?',
                'options' => '["Fitness antrenmanı", "Kişisel antrenör", "Grup dersleri", "Yoga", "Pilates", "Crossfit", "Yüzme", "Beslenme danışmanlığı", "Fizyoterapi", "Spor masajı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
            ],
            
            // OTOMOTİV
            'automotive' => [
                'question_text' => 'Hangi otomotiv hizmetlerini sağlıyorsunuz?',
                'options' => '["Araç satışı", "İkinci el araç", "Servis & bakım", "Yedek parça", "Lastik değişimi", "Oto elektrik", "Kaporta & boya", "Araç ekspertizi", "Sigorta işlemleri", "Araç kiralama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]'
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
                
                if ($addedCount % 10 == 0) {
                    echo "📊 {$addedCount} sektöre somut soru eklendi...\n";
                }
            } else {
                echo "⚠️ Question key '{$question['question_key']}' zaten var, atlandı\n";
            }
        }
        
        echo "✅ SQL sektörlerine {$addedCount} somut hizmet sorusu eklendi!\n";
        
        // Kalan tüm SQL sektörlerine genel sorular ekle
        $this->addRemainingSQLSectorQuestions();
    }
    
    /**
     * Kalan tüm SQL sektörlerine genel sorular ekle
     */
    private function addRemainingSQLSectorQuestions(): void
    {
        echo "📋 Kalan SQL sektörlerine genel sorular ekleniyor...\n";
        
        // Tüm SQL sektörlerini al
        $allSqlSectors = DB::table('ai_profile_sectors')
            ->whereBetween('id', [1, 162])
            ->pluck('code', 'name')
            ->toArray();
            
        // Zaten sorusu olan sektörleri al
        $sectorsWithQuestions = DB::table('ai_profile_questions')
            ->where('question_key', 'LIKE', '%_specific_services')
            ->pluck('sector_code')
            ->toArray();
            
        // Sorusu olmayan sektörleri bul
        $sectorsWithoutQuestions = array_diff(array_keys($allSqlSectors), $sectorsWithQuestions);
        
        echo "📊 Sorusu olmayan " . count($sectorsWithoutQuestions) . " sektöre genel soru ekleniyor...\n";
        
        $addedCount = 0;
        
        foreach ($sectorsWithoutQuestions as $sectorCode) {
            $sectorName = $allSqlSectors[$sectorCode];
            
            $question = [
                'sector_code' => $sectorCode,
                'step' => 3,
                'section' => null,
                'question_key' => $sectorCode . '_specific_services',
                'question_text' => 'Bu sektörde hangi hizmet ve ürünleri sunuyorsunuz?',
                'help_text' => $sectorName . ' alanında sunduğunuz spesifik hizmet ve ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => '["Danışmanlık hizmeti", "Ürün satışı", "Hizmet sağlama", "Eğitim & kurs", "Bakım & onarım", "Tasarım & planlama", "Üretim", "Dağıtım & lojistik", "İthalat & ihracat", "Teknik destek", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null,
                'depends_on' => null,
                'show_if' => null,
                'is_required' => 0,
                'is_active' => 1,
                'sort_order' => 5,
                'priority' => 3,
                'ai_weight' => 70,
                'category' => 'company',
                'ai_priority' => 3,
                'always_include' => 0,
                'context_category' => 'service_portfolio'
            ];
            
            try {
                DB::table('ai_profile_questions')->insert(array_merge($question, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                $addedCount++;
                
                if ($addedCount % 20 == 0) {
                    echo "📊 {$addedCount} sektöre genel soru eklendi...\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ {$sectorCode} sorusu eklenemedi: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ Kalan SQL sektörlerine {$addedCount} genel soru eklendi!\n";
    }
}