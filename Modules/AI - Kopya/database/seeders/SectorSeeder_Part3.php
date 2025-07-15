<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part3 extends Seeder
{
    /**
     * SECTOR SEEDER PART 3 (ID 179+)
     * Hizmet sektörleri ve özelleşmiş professional alanlar + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 3 yükleniyor (ID 179+)...\n";

        // Hizmet ve professional sektörleri ekle (ID 179+)
        $this->addServiceSectors();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();

        echo "✅ Part 3 tamamlandı! (Hizmet & Professional sektörler)\n";
    }
    
    private function addServiceSectors(): void
    {
        // Professional ve hizmet sektörleri (ID 179'dan başlayarak)
        $sectors = [
            // HİZMET SEKTÖRLERI (ID 179-190)
            ['id' => 179, 'code' => 'cleaning_services', 'category_id' => 18, 'name' => 'Temizlik Hizmetleri', 'emoji' => '🧽', 'color' => 'blue', 'description' => 'Ev, ofis, endüstriyel temizlik', 'keywords' => 'temizlik, cleaning, ev, ofis'],
            ['id' => 180, 'code' => 'security_services', 'category_id' => 18, 'name' => 'Güvenlik Hizmetleri', 'emoji' => '🛡️', 'color' => 'red', 'description' => 'Güvenlik, kamera sistemi, alarm', 'keywords' => 'güvenlik, security, kamera, alarm'],
            ['id' => 181, 'code' => 'logistics_cargo', 'category_id' => 18, 'name' => 'Lojistik & Kargo', 'emoji' => '📦', 'color' => 'orange', 'description' => 'Kargo, nakliye, depolama', 'keywords' => 'kargo, lojistik, nakliye, depo'],
            ['id' => 182, 'code' => 'wedding_events', 'category_id' => 18, 'name' => 'Düğün & Etkinlik', 'emoji' => '💒', 'color' => 'pink', 'description' => 'Düğün organizasyonu, etkinlik', 'keywords' => 'düğün, etkinlik, organizasyon, event'],
            ['id' => 183, 'code' => 'translation_services', 'category_id' => 18, 'name' => 'Çeviri & Tercümanlık', 'emoji' => '🗣️', 'color' => 'purple', 'description' => 'Çeviri, tercümanlık, noter', 'keywords' => 'çeviri, tercüman, translation, noter'],
            ['id' => 184, 'code' => 'consulting_services', 'category_id' => 18, 'name' => 'Danışmanlık Hizmetleri', 'emoji' => '💼', 'color' => 'blue', 'description' => 'İş danışmanlığı, strateji, yönetim', 'keywords' => 'danışmanlık, consulting, strateji, yönetim'],
            ['id' => 185, 'code' => 'repair_technical', 'category_id' => 18, 'name' => 'Teknik Servis & Tamir', 'emoji' => '🔧', 'color' => 'gray', 'description' => 'Elektronik tamir, teknik servis', 'keywords' => 'tamir, servis, teknik, elektronik'],
            ['id' => 186, 'code' => 'photography_video', 'category_id' => 8, 'name' => 'Fotoğraf & Video', 'emoji' => '📸', 'color' => 'purple', 'description' => 'Fotoğrafçılık, video çekim, edit', 'keywords' => 'fotoğraf, video, çekim, edit'],
            ['id' => 187, 'code' => 'advertising_agency', 'category_id' => 8, 'name' => 'Reklam Ajansı', 'emoji' => '📢', 'color' => 'red', 'description' => 'Reklam, kreatif, marka yönetimi', 'keywords' => 'reklam, ajans, kreatif, marka'],
            ['id' => 188, 'code' => 'printing_design', 'category_id' => 8, 'name' => 'Matbaa & Tasarım', 'emoji' => '🖨️', 'color' => 'cyan', 'description' => 'Matbaa, baskı, tasarım hizmetleri', 'keywords' => 'matbaa, baskı, tasarım, printing'],
            
            // ÖZEL ESNAF SEKTÖRLERI (ID 191-200)
            ['id' => 191, 'code' => 'hairdresser_barber', 'category_id' => 14, 'name' => 'Kuaför & Berber', 'emoji' => '💇', 'color' => 'pink', 'description' => 'Kuaförlük, berberlık, saç bakım', 'keywords' => 'kuaför, berber, saç, güzellik'],
            ['id' => 192, 'code' => 'nail_salon', 'category_id' => 14, 'name' => 'Nail Salon & Güzellik', 'emoji' => '💅', 'color' => 'rose', 'description' => 'Nail art, manikür, pedikür', 'keywords' => 'nail, manikür, pedikür, güzellik'],
            ['id' => 193, 'code' => 'massage_spa', 'category_id' => 14, 'name' => 'Masaj & SPA', 'emoji' => '💆', 'color' => 'green', 'description' => 'Masaj, spa, wellness hizmetleri', 'keywords' => 'masaj, spa, wellness, relax'],
            ['id' => 194, 'code' => 'tailor_alteration', 'category_id' => 14, 'name' => 'Terzi & Değişim', 'emoji' => '🧵', 'color' => 'amber', 'description' => 'Terzilik, değişim, ütü hizmetleri', 'keywords' => 'terzi, değişim, ütü, tamir'],
            ['id' => 195, 'code' => 'shoe_repair', 'category_id' => 14, 'name' => 'Ayakkabı Tamiri', 'emoji' => '👞', 'color' => 'brown', 'description' => 'Ayakkabı tamiri, çanta tamiri', 'keywords' => 'ayakkabı, tamir, çanta, deri'],
            ['id' => 196, 'code' => 'key_locksmith', 'category_id' => 18, 'name' => 'Anahtar & Çilingir', 'emoji' => '🔑', 'color' => 'yellow', 'description' => 'Anahtar çoğaltma, çilingir', 'keywords' => 'anahtar, çilingir, kilit, çoğaltma'],
            ['id' => 197, 'code' => 'watch_repair', 'category_id' => 14, 'name' => 'Saat Tamiri', 'emoji' => '⌚', 'color' => 'blue', 'description' => 'Saat tamiri, pil değişimi', 'keywords' => 'saat, tamir, pil, watch'],
            ['id' => 198, 'code' => 'phone_repair', 'category_id' => 18, 'name' => 'Telefon Tamiri', 'emoji' => '📱', 'color' => 'green', 'description' => 'Telefon tamiri, ekran değişimi', 'keywords' => 'telefon, tamir, ekran, phone'],
            ['id' => 199, 'code' => 'jewelry_gold', 'category_id' => 14, 'name' => 'Kuyumcu & Altın', 'emoji' => '💎', 'color' => 'yellow', 'description' => 'Altın, gümüş, mücevher satış', 'keywords' => 'altın, gümüş, mücevher, kuyumcu'],
            ['id' => 200, 'code' => 'optical_glasses', 'category_id' => 14, 'name' => 'Optik & Gözlük', 'emoji' => '👓', 'color' => 'blue', 'description' => 'Gözlük, lens, optik hizmetler', 'keywords' => 'gözlük, optik, lens, göz'],
        ];

        $addedCount = 0;
        foreach ($sectors as $sector) {
            try {
                // Sadece mevcut değilse ekle
                $existing = DB::table('ai_profile_sectors')->where('id', $sector['id'])->exists();
                if (!$existing) {
                    DB::table('ai_profile_sectors')->insert(array_merge($sector, [
                        'icon' => null,
                        'is_subcategory' => 0,
                        'is_active' => 1,
                        'sort_order' => $sector['id'] * 10,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                    $addedCount++;
                }
            } catch (\Exception $e) {
                echo "⚠️ Sektör atlandı: " . $e->getMessage() . "\n";
                continue;
            }
        }

        echo "📊 Part 3: {$addedCount} hizmet sektörü eklendi\n";
    }
    
    private function addSectorQuestions(): void
    {
        // ORGANIZE EDİLEN SEKTÖR SORULARI - PART 3
        
        // ===========================================
        // 1. İNŞAAT VE MİMARLIK SEKTÖRLERİ 🏗️
        // ===========================================
        
        // İNŞAAT
        $constructionQuestions = [
            [
                'sector_code' => 'construction', 'step' => 3, 'section' => null,
                'question_key' => 'construction_specific_services', 'question_text' => 'Hangi inşaat ve mimarlık hizmetlerini sunuyorsunuz?',
                'help_text' => 'İnşaat, mimarlık ve mühendislik alanındaki uzmanlaştığınız hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Konut inşaatı', 'value' => 'residential'],
                    ['label' => 'Ticari yapı', 'value' => 'commercial'],
                    ['label' => 'Endüstriyel yapı', 'value' => 'industrial'],
                    ['label' => 'Mimari tasarım', 'value' => 'architectural_design'],
                    ['label' => 'İç mimari', 'value' => 'interior_design'],
                    ['label' => 'Statik hesap', 'value' => 'structural_calculation'],
                    ['label' => 'Proje yönetimi', 'value' => 'project_management'],
                    ['label' => 'Renovasyon', 'value' => 'renovation'],
                    ['label' => 'Peyzaj', 'value' => 'landscaping'],
                    ['label' => 'Ruhsat işlemleri', 'value' => 'licensing'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'sector_code' => 'construction', 'step' => 3, 'section' => null,
                'question_key' => 'construction_project_types', 'question_text' => 'Hangi büyüklükteki projelerde çalışıyorsunuz?',
                'help_text' => 'Proje ölçeği ve karmaşıklığı açısından deneyim alanlarınız',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Villa projeleri', 'value' => 'villa'],
                    ['label' => 'Apartman projeleri', 'value' => 'apartment'],
                    ['label' => 'AVM & Plaza', 'value' => 'mall_plaza'],
                    ['label' => 'Fabrika & Depo', 'value' => 'factory_warehouse'],
                    ['label' => 'Okul & Hastane', 'value' => 'institutional'],
                    ['label' => 'Küçük tadilat', 'value' => 'small_renovation'],
                    ['label' => 'Büyük ölçekli projeler', 'value' => 'large_scale'],
                    ['label' => 'Altyapı projeleri', 'value' => 'infrastructure'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'project_types'
            ]
        ];

        // ===========================================
        // 2. FİNANS VE SİGORTA SEKTÖRLERİ 💰
        // ===========================================
        
        // FİNANS
        $financeQuestions = [
            [
                'sector_code' => 'finance', 'step' => 3, 'section' => null,
                'question_key' => 'finance_specific_services', 'question_text' => 'Hangi finans ve sigorta hizmetlerini sunuyorsunuz?',
                'help_text' => 'Finansal danışmanlık, sigorta ve yatırım alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Finansal danışmanlık', 'value' => 'financial_consulting'],
                    ['label' => 'Kredi danışmanlığı', 'value' => 'credit_consulting'],
                    ['label' => 'Sigorta aracılığı', 'value' => 'insurance_brokerage'],
                    ['label' => 'Yatırım danışmanlığı', 'value' => 'investment_advisory'],
                    ['label' => 'Emlak finansmanı', 'value' => 'real_estate_finance'],
                    ['label' => 'Bütçe planlaması', 'value' => 'budget_planning'],
                    ['label' => 'Emeklilik planı', 'value' => 'retirement_planning'],
                    ['label' => 'Vergi danışmanlığı', 'value' => 'tax_consulting'],
                    ['label' => 'İşletme finansı', 'value' => 'business_finance'],
                    ['label' => 'Crypto danışmanlığı', 'value' => 'crypto_advisory'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'sector_code' => 'finance', 'step' => 3, 'section' => null,
                'question_key' => 'finance_client_segments', 'question_text' => 'Hangi müşteri segmentlerine hizmet veriyorsunuz?',
                'help_text' => 'Hedef müşteri kitlesi ve hizmet verdiğiniz gruplar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Bireysel müşteriler', 'value' => 'individual'],
                    ['label' => 'KOBİ işletmeleri', 'value' => 'sme'],
                    ['label' => 'Büyük şirketler', 'value' => 'corporate'],
                    ['label' => 'Genç profesyoneller', 'value' => 'young_professionals'],
                    ['label' => 'Emekliler', 'value' => 'retirees'],
                    ['label' => 'Yüksek gelirli bireyler', 'value' => 'high_net_worth'],
                    ['label' => 'Ev alacaklar', 'value' => 'home_buyers'],
                    ['label' => 'Yatırımcılar', 'value' => 'investors'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'target_audience'
            ]
        ];

        // ===========================================
        // 3. HUKUK VE DANIŞMANLIK SEKTÖRLERİ ⚖️
        // ===========================================
        
        // HUKUK
        $legalQuestions = [
            [
                'sector_code' => 'legal', 'step' => 3, 'section' => null,
                'question_key' => 'legal_specific_services', 'question_text' => 'Hangi hukuk alanlarında hizmet sunuyorsunuz?',
                'help_text' => 'Uzmanlaştığınız hukuk dalları ve danışmanlık hizmetleri',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Ticaret hukuku', 'value' => 'commercial_law'],
                    ['label' => 'İş hukuku', 'value' => 'labor_law'],
                    ['label' => 'Aile hukuku', 'value' => 'family_law'],
                    ['label' => 'Ceza hukuku', 'value' => 'criminal_law'],
                    ['label' => 'Emlak hukuku', 'value' => 'real_estate_law'],
                    ['label' => 'Şirket kuruluşu', 'value' => 'company_formation'],
                    ['label' => 'Sözleşme hukuku', 'value' => 'contract_law'],
                    ['label' => 'İcra & iflas', 'value' => 'bankruptcy'],
                    ['label' => 'Vergi hukuku', 'value' => 'tax_law'],
                    ['label' => 'Siber hukuk', 'value' => 'cyber_law'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'sector_code' => 'legal', 'step' => 3, 'section' => null,
                'question_key' => 'legal_service_types', 'question_text' => 'Hangi tür hukuki hizmetler sunuyorsunuz?',
                'help_text' => 'Hizmet türü ve müvekkil ilişkisi açısından çalışma şekliniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Dava takibi', 'value' => 'litigation'],
                    ['label' => 'Hukuki danışmanlık', 'value' => 'legal_advisory'],
                    ['label' => 'Sözleşme hazırlama', 'value' => 'contract_drafting'],
                    ['label' => 'Arabuluculuk', 'value' => 'mediation'],
                    ['label' => 'Yasal compliance', 'value' => 'compliance'],
                    ['label' => 'Due diligence', 'value' => 'due_diligence'],
                    ['label' => 'Online danışmanlık', 'value' => 'online_consulting'],
                    ['label' => 'Acil hukuki destek', 'value' => 'emergency_support'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'service_types'
            ]
        ];

        // ===========================================
        // 4. SANAT VE TASARIM SEKTÖRLERİ 🎨
        // ===========================================
        
        // SANAT & TASARIM
        $artDesignQuestions = [
            [
                'sector_code' => 'art_design', 'step' => 3, 'section' => null,
                'question_key' => 'art_design_specific_services', 'question_text' => 'Hangi sanat ve tasarım hizmetlerini sunuyorsunuz?',
                'help_text' => 'Grafik tasarım, sanat eserleri ve kreatif hizmetler alanındaki uzmanlaştığınız alanlar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Grafik tasarım', 'value' => 'graphic_design'],
                    ['label' => 'Logo tasarımı', 'value' => 'logo_design'],
                    ['label' => 'Kurumsal kimlik', 'value' => 'corporate_identity'],
                    ['label' => 'Web tasarımı', 'value' => 'web_design'],
                    ['label' => 'Ambalaj tasarımı', 'value' => 'packaging_design'],
                    ['label' => 'İllüstrasyon', 'value' => 'illustration'],
                    ['label' => 'Fotoğrafçılık', 'value' => 'photography'],
                    ['label' => 'Video editing', 'value' => 'video_editing'],
                    ['label' => 'Motion graphics', 'value' => 'motion_graphics'],
                    ['label' => 'El sanatları', 'value' => 'handicrafts'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'sector_code' => 'art_design', 'step' => 3, 'section' => null,
                'question_key' => 'art_design_specialization', 'question_text' => 'Hangi tasarım alanlarında uzmanlaştınız?',
                'help_text' => 'Özel olarak odaklandığınız tasarım türleri ve tarzları',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Minimalist tasarım', 'value' => 'minimalist'],
                    ['label' => 'Modern tasarım', 'value' => 'modern'],
                    ['label' => 'Vintage/Retro', 'value' => 'vintage'],
                    ['label' => 'Typography', 'value' => 'typography'],
                    ['label' => 'Digital art', 'value' => 'digital_art'],
                    ['label' => 'Print tasarım', 'value' => 'print_design'],
                    ['label' => 'UI/UX Design', 'value' => 'ui_ux'],
                    ['label' => 'Marka tasarımı', 'value' => 'branding'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'specialization'
            ]
        ];

        // Temizlik Hizmetleri
        $cleaningQuestions = [
            [
                'sector_code' => 'cleaning_services', 'step' => 3, 'section' => null,
                'question_key' => 'cleaning_service_types', 'question_text' => 'Hangi temizlik hizmetlerini veriyorsunuz?',
                'help_text' => 'Sunduğunuz temizlik hizmet türleri',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Ev temizliği', 'value' => 'home_cleaning'],
                    ['label' => 'Ofis temizliği', 'value' => 'office_cleaning'],
                    ['label' => 'Cam temizliği', 'value' => 'window_cleaning'],
                    ['label' => 'Halı yıkama', 'value' => 'carpet_cleaning'],
                    ['label' => 'Koltuk temizliği', 'value' => 'furniture_cleaning'],
                    ['label' => 'İnşaat sonrası temizlik', 'value' => 'construction_cleanup'],
                    ['label' => 'Endüstriyel temizlik', 'value' => 'industrial_cleaning'],
                    ['label' => 'Dezenfeksiyon', 'value' => 'disinfection'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // Tüm soru gruplarını birleştir
        $allQuestions = array_merge(
            $constructionQuestions,
            $financeQuestions,
            $legalQuestions,
            $artDesignQuestions,
            $cleaningQuestions
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

        echo "❓ Part 3: " . count($allQuestions) . " organize edilmiş sektör sorusu eklendi\n";
    }
}