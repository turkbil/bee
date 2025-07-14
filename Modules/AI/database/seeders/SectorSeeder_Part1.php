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
        $questions = [
            // TEKNOLOJI SEKTÖRÜ SORULARI
            [
                'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_services_detailed', 'question_text' => 'Hangi web hizmetlerini sunuyorsunuz?',
                'help_text' => 'Web tasarım ve geliştirme alanındaki uzmanlaştığınız hizmetler',
                'input_type' => 'checkbox',
                'options' => '["Kurumsal web sitesi", "E-ticaret sitesi", "Blog/portfolio", "Landing page", "WordPress", "Laravel", "React/Vue", "SEO optimizasyonu", "Hosting/domain", "Bakım/güncelleme", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'mobile', 'step' => 3, 'section' => null,
                'question_key' => 'mobile_platforms_detailed', 'question_text' => 'Hangi platformlarda mobil uygulama geliştiriyorsunuz?',
                'help_text' => 'Mobil uygulama geliştirme platformları ve teknolojileriniz',
                'input_type' => 'checkbox',
                'options' => '["iOS (Swift)", "Android (Kotlin/Java)", "React Native", "Flutter", "Ionic", "Xamarin", "Progressive Web App", "Hybrid app", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // SAĞLIK SEKTÖRÜ SORULARI
            [
                'sector_code' => 'hospital', 'step' => 3, 'section' => null,
                'question_key' => 'hospital_departments_detailed', 'question_text' => 'Hangi tıbbi bölümleriniz var?',
                'help_text' => 'Hastane/kliniğinizdeki aktif tıbbi bölümler ve uzmanlar',
                'input_type' => 'checkbox',
                'options' => '["Dahiliye", "Genel Cerrahi", "Kadın Doğum", "Çocuk Sağlığı", "Kardiyoloji", "Nöroloji", "Ortopedi", "KBB", "Göz", "Psikiyatri", "Üroloji", "Dermatoloji", "Acil Servis", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'dental', 'step' => 3, 'section' => null,
                'question_key' => 'dental_treatments_detailed', 'question_text' => 'Hangi diş tedavilerini uyguluyorsunuz?',
                'help_text' => 'Diş kliniğinizdeki tedavi seçenekleri ve uzmanlık alanları',
                'input_type' => 'checkbox',
                'options' => '["Genel muayene", "Dolgu", "Kanal tedavisi", "Çekim", "İmplant", "Protez", "Ortodonti", "Beyazlatma", "Estetik diş", "Periodontal tedavi", "Çocuk diş", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EĞİTİM SEKTÖRÜ SORULARI
            [
                'sector_code' => 'school', 'step' => 3, 'section' => null,
                'question_key' => 'education_levels_detailed', 'question_text' => 'Hangi eğitim seviyelerinde hizmet veriyorsunuz?',
                'help_text' => 'Eğitim kurumunuzda bulunan sınıf seviyeleri ve programlar',
                'input_type' => 'checkbox',
                'options' => '["Anaokulu", "İlkokul", "Ortaokul", "Lise", "Üniversite hazırlık", "Yetişkin eğitimi", "Özel eğitim", "Yetenekli çocuklar", "Online eğitim", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'language', 'step' => 3, 'section' => null,
                'question_key' => 'languages_offered_detailed', 'question_text' => 'Hangi dillerde eğitim veriyorsunuz?',
                'help_text' => 'Dil kursunuzda öğretilen yabancı diller ve seviyeler',
                'input_type' => 'checkbox',
                'options' => '["İngilizce", "Almanca", "Fransızca", "İtalyanca", "İspanyolca", "Rusça", "Arapça", "Çince", "Japonca", "IELTS/TOEFL hazırlık", "İş İngilizcesi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // ANA SEKTÖRLER İÇİN EK SORULAR (SQL'den gelen kategoriler)
            [
                'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'tech_specialization', 'question_text' => 'Teknoloji alanında hangi uzmanlık alanlarınız var?',
                'help_text' => 'Bilişim ve teknoloji konularındaki ana yetkinlikleriniz',
                'input_type' => 'checkbox',
                'options' => '["Yazılım geliştirme", "Web tasarım", "Mobil uygulama", "Veritabanı yönetimi", "Siber güvenlik", "Bulut çözümleri", "AI/ML", "DevOps", "UI/UX tasarım", "Sistem yönetimi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 1, 'ai_weight' => 90, 'category' => 'company',
                'ai_priority' => 1, 'always_include' => 1, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_facility_type', 'question_text' => 'Sağlık tesisiniznin türü nedir?',
                'help_text' => 'Faaliyet gösterdiğiniz sağlık hizmeti kategorisi',
                'input_type' => 'checkbox',
                'options' => '["Hastane", "Özel klinik", "Poliklinik", "Tıp merkezi", "Laboratuvar", "Eczane", "Fizik tedavi", "Diyetisyen", "Psikolog", "Veteriner", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 1, 'ai_weight' => 90, 'category' => 'company',
                'ai_priority' => 1, 'always_include' => 1, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'education', 'step' => 3, 'section' => null,
                'question_key' => 'education_programs', 'question_text' => 'Hangi eğitim programlarınız bulunuyor?',
                'help_text' => 'Sunduğunuz eğitim program türleri ve seviyeler',
                'input_type' => 'checkbox',
                'options' => '["Okul öncesi", "İlkokul", "Ortaokul", "Lise", "Üniversite", "Yüksek lisans", "Doktora", "Sertifika programları", "Online eğitim", "Kurumsal eğitim", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 1, 'ai_weight' => 90, 'category' => 'company',
                'ai_priority' => 1, 'always_include' => 1, 'context_category' => 'service_portfolio'
            ]
        ];

        foreach ($questions as $question) {
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

        echo "❓ Part 1: " . count($questions) . " özel soru eklendi\n";
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