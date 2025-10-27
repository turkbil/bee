<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part4 extends Seeder
{
    /**
     * SECTOR SEEDER PART 4 (ID 151-200)
     * Özel Türk esnaf sektörleri ve niş alanlar + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 4 yükleniyor (ID 151-200)...\n";

        // Özel Türk esnaf ve niş sektörleri ekle (ID 151-200)
        $this->addSpecialTurkishSectors();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();

        echo "✅ Part 4 tamamlandı! (ID 151-170: Özel Türk Esnaf & Niş sektörler)\n";
    }
    
    private function addSpecialTurkishSectors(): void
    {
        // Özel Türk esnaf ve niş sektörler (ID 151'den başlayarak)
        $sectors = [
            // ÖZEL TÜRK ESNAF SEKTÖRLERI (ID 151-165) 
            ['id' => 151, 'code' => 'wedding_dress', 'category_id' => 14, 'name' => 'Gelinlik & Abiye', 'emoji' => '👰', 'color' => 'rose', 'description' => 'Gelinlik, abiye, düğün kıyafetleri', 'keywords' => 'gelinlik, abiye, düğün, gelin'],
            ['id' => 152, 'code' => 'flower_shop', 'category_id' => 6, 'name' => 'Çiçekçi & Bahçıvanlık', 'emoji' => '🌹', 'color' => 'green', 'description' => 'Çiçekçilik, peyzaj, bahçıvanlık', 'keywords' => 'çiçek, bahçe, peyzaj, orkide'],
            ['id' => 153, 'code' => 'carpet_rug', 'category_id' => 8, 'name' => 'Halı & Kilim', 'emoji' => '🪺', 'color' => 'amber', 'description' => 'El dokuması halı, kilim, duvar halısı', 'keywords' => 'halı, kilim, dokuması, antika'],
            ['id' => 154, 'code' => 'market_grocery', 'category_id' => 6, 'name' => 'Market & Bakkal', 'emoji' => '🏪', 'color' => 'green', 'description' => 'Mahalle marketi, bakkal, şarküteri', 'keywords' => 'market, bakkal, şarküteri, mahalle'],
            ['id' => 155, 'code' => 'gas_station', 'category_id' => 17, 'name' => 'Benzinlik & Akaryakıt', 'emoji' => '⛽', 'color' => 'red', 'description' => 'Benzin istasyonu, LPG, oto yıkama', 'keywords' => 'benzin, akaryakıt, LPG, yakıt'],
            ['id' => 156, 'code' => 'stationery_shop', 'category_id' => 6, 'name' => 'Kırtasiye & Okul', 'emoji' => '📚', 'color' => 'blue', 'description' => 'Kırtasiye, okul malzemeleri, fotokopi', 'keywords' => 'kırtasiye, okul, fotokopi, kalem'],
            ['id' => 157, 'code' => 'toy_shop', 'category_id' => 6, 'name' => 'Oyuncak & Bebek', 'emoji' => '🧸', 'color' => 'pink', 'description' => 'Oyuncak, bebek, çocuk ürünleri', 'keywords' => 'oyuncak, bebek, çocuk, eğlence'],
            ['id' => 158, 'code' => 'furniture_maker', 'category_id' => 17, 'name' => 'Marangoz & Mobilya', 'emoji' => '🪑', 'color' => 'brown', 'description' => 'Mobilya yapımı, marangozluk, ahşap', 'keywords' => 'marangoz, mobilya, ahşap, masa'],
            ['id' => 159, 'code' => 'blacksmith_metal', 'category_id' => 17, 'name' => 'Demirci & Metal İşleri', 'emoji' => '🔨', 'color' => 'gray', 'description' => 'Demircilik, metal işleme, kaynak', 'keywords' => 'demirci, metal, kaynak, demir'],
            ['id' => 160, 'code' => 'curtain_blind', 'category_id' => 14, 'name' => 'Perde & Jaluzici', 'emoji' => '🪟', 'color' => 'blue', 'description' => 'Perde, jaluzi, ev tekstili', 'keywords' => 'perde, jaluzi, ev tekstili, cam'],
            
            // NİŞ VE ÖZEL ALANLAR (ID 161-170)
            ['id' => 161, 'code' => 'traditional_crafts', 'category_id' => 8, 'name' => 'Geleneksel Sanatlar', 'emoji' => '🏺', 'color' => 'amber', 'description' => 'Çini, seramik, geleneksel el sanatları', 'keywords' => 'çini, seramik, geleneksel, sanat'],
            ['id' => 162, 'code' => 'musical_instruments', 'category_id' => 8, 'name' => 'Müzik Aletleri', 'emoji' => '🎸', 'color' => 'purple', 'description' => 'Enstrüman satış, tamir, müzik', 'keywords' => 'enstrüman, müzik, gitar, piyano'],
            ['id' => 163, 'code' => 'second_hand', 'category_id' => 6, 'name' => 'İkinci El & Antika', 'emoji' => '🕰️', 'color' => 'amber', 'description' => 'İkinci el eşya, antika, koleksiyon', 'keywords' => 'ikinci el, antika, eski, koleksiyon'],
            ['id' => 164, 'code' => 'hobby_collection', 'category_id' => 6, 'name' => 'Hobi & Koleksiyon', 'emoji' => '🎯', 'color' => 'indigo', 'description' => 'Hobi malzemeleri, koleksiyon eşyaları', 'keywords' => 'hobi, koleksiyon, maket, oyun'],
            ['id' => 165, 'code' => 'party_organization', 'category_id' => 9, 'name' => 'Parti & Organizasyon', 'emoji' => '🎉', 'color' => 'pink', 'description' => 'Doğum günü, parti, etkinlik organizasyonu', 'keywords' => 'parti, doğum günü, etkinlik, balon'],
            ['id' => 166, 'code' => 'fishing_hunting', 'category_id' => 16, 'name' => 'Balık & Avcılık', 'emoji' => '🎣', 'color' => 'green', 'description' => 'Balık malzemeleri, avcılık, outdoor', 'keywords' => 'balık, avcılık, olta, doğa'],
            ['id' => 167, 'code' => 'camping_outdoor', 'category_id' => 16, 'name' => 'Kamp & Doğa Sporları', 'emoji' => '🏕️', 'color' => 'green', 'description' => 'Kamp malzemeleri, doğa sporları', 'keywords' => 'kamp, çadır, doğa, outdoor'],
            ['id' => 168, 'code' => 'religious_items', 'category_id' => 6, 'name' => 'Dini Eşya & Kitap', 'emoji' => '📿', 'color' => 'green', 'description' => 'Dini kitap, tesbih, hac-umre', 'keywords' => 'dini, kitap, tesbih, hac'],
            ['id' => 169, 'code' => 'occult_spiritual', 'category_id' => 9, 'name' => 'Metafizik & Ruhani', 'emoji' => '🔮', 'color' => 'purple', 'description' => 'Tarot, kristal, ruhani danışmanlık', 'keywords' => 'tarot, kristal, ruhani, metafizik'],
            ['id' => 170, 'code' => 'funeral_cemetery', 'category_id' => 9, 'name' => 'Cenaze & Mezarlık', 'emoji' => '⚱️', 'color' => 'gray', 'description' => 'Cenaze hizmetleri, mezar taşları', 'keywords' => 'cenaze, mezar, tabut, defin'],
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

        echo "📊 Part 4: {$addedCount} özel Türk esnaf sektörü eklendi\n";
    }
    
    private function addSectorQuestions(): void
    {
        // ORGANIZE EDİLEN SEKTÖR SORULARI - PART 4
        
        // ===========================================
        // 1. SPOR VE REKREASYON SEKTÖRLERİ ⚽
        // ===========================================
        
        // SPOR
        $sportsQuestions = [
            [
                'sector_code' => 'sports', 'step' => 3, 'section' => null,
                'question_key' => 'sports_specific_services', 'question_text' => 'Hangi spor ve rekreasyon hizmetlerini sunuyorsunuz?',
                'help_text' => 'Spor eğitimi, fitness ve rekreasyon alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Kişisel antrenörlük', 'value' => 'personal_training'],
                    ['label' => 'Grup dersleri', 'value' => 'group_classes'],
                    ['label' => 'Yoga & Pilates', 'value' => 'yoga_pilates'],
                    ['label' => 'Fitness eğitimi', 'value' => 'fitness_training'],
                    ['label' => 'Futbol antrenörlüğü', 'value' => 'football_coaching'],
                    ['label' => 'Basketbol antrenörlüğü', 'value' => 'basketball_coaching'],
                    ['label' => 'Yüzme eğitimi', 'value' => 'swimming_lessons'],
                    ['label' => 'Tenis dersleri', 'value' => 'tennis_lessons'],
                    ['label' => 'Dans dersleri', 'value' => 'dance_lessons'],
                    ['label' => 'Outdoor aktiviteler', 'value' => 'outdoor_activities'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // ===========================================
        // 2. OTOMOTİV SEKTÖRLERİ 🚗
        // ===========================================
        
        // OTOMOTİV
        $automotiveQuestions = [
            [
                'sector_code' => 'automotive', 'step' => 3, 'section' => null,
                'question_key' => 'automotive_specific_services', 'question_text' => 'Hangi otomotiv hizmetlerini sunuyorsunuz?',
                'help_text' => 'Araç satışı, tamiri ve bakımı alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Araç satışı', 'value' => 'car_sales'],
                    ['label' => 'Araç kiralama', 'value' => 'car_rental'],
                    ['label' => 'Otomobil tamiri', 'value' => 'car_repair'],
                    ['label' => 'Motor tamiri', 'value' => 'engine_repair'],
                    ['label' => 'Kaporta & boya', 'value' => 'bodywork_paint'],
                    ['label' => 'Lastik hizmetleri', 'value' => 'tire_services'],
                    ['label' => 'Fren sistemi', 'value' => 'brake_system'],
                    ['label' => 'Elektrik sistemi', 'value' => 'electrical_system'],
                    ['label' => 'Klima servisi', 'value' => 'ac_service'],
                    ['label' => 'Bakım & servis', 'value' => 'maintenance'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // ===========================================
        // 3. EĞİTİM SEKTÖRLERİ 📚
        // ===========================================
        
        // EĞİTİM
        $educationQuestions = [
            [
                'sector_code' => 'education', 'step' => 3, 'section' => null,
                'question_key' => 'education_specific_services', 'question_text' => 'Hangi eğitim hizmetlerini sunuyorsunuz?',
                'help_text' => 'Eğitim, kurs ve öğretim alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Özel ders', 'value' => 'private_tutoring'],
                    ['label' => 'Grup dersleri', 'value' => 'group_classes'],
                    ['label' => 'Online eğitim', 'value' => 'online_education'],
                    ['label' => 'Dil eğitimi', 'value' => 'language_training'],
                    ['label' => 'Matematik', 'value' => 'mathematics'],
                    ['label' => 'Fen bilimleri', 'value' => 'science'],
                    ['label' => 'Müzik eğitimi', 'value' => 'music_education'],
                    ['label' => 'Sanat eğitimi', 'value' => 'art_education'],
                    ['label' => 'Bilgisayar kursu', 'value' => 'computer_courses'],
                    ['label' => 'Mesleki eğitim', 'value' => 'vocational_training'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // ===========================================
        // 4. SAĞLIK VE WELLNESS SEKTÖRLERİ 🏥
        // ===========================================
        
        // SAĞLIK
        $healthQuestions = [
            [
                'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_specific_services', 'question_text' => 'Hangi sağlık ve wellness hizmetlerini sunuyorsunuz?',
                'help_text' => 'Sağlık bakımı, tedavi ve wellness alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Genel muayene', 'value' => 'general_examination'],
                    ['label' => 'Uzman doktor', 'value' => 'specialist_doctor'],
                    ['label' => 'Diş hekimliği', 'value' => 'dentistry'],
                    ['label' => 'Fizik tedavi', 'value' => 'physiotherapy'],
                    ['label' => 'Psikolog danışmanlık', 'value' => 'psychology'],
                    ['label' => 'Beslenme danışmanlığı', 'value' => 'nutrition_consulting'],
                    ['label' => 'Estetik hizmetler', 'value' => 'aesthetic_services'],
                    ['label' => 'Alternatif tıp', 'value' => 'alternative_medicine'],
                    ['label' => 'Lab testleri', 'value' => 'laboratory_tests'],
                    ['label' => 'Sağlık check-up', 'value' => 'health_checkup'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // Türk Esnaf Soruları
        $turkishCraftsQuestions = [
            [
                'sector_code' => 'wedding_dress', 'step' => 3, 'section' => null,
                'question_key' => 'wedding_dress_services', 'question_text' => 'Hangi gelinlik hizmetlerini sunuyorsunuz?',
                'help_text' => 'Gelinlik ve düğün kıyafeti hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Gelinlik satış', 'value' => 'wedding_dress_sales'],
                    ['label' => 'Gelinlik kiralama', 'value' => 'wedding_dress_rental'],
                    ['label' => 'Abiye satış', 'value' => 'evening_dress'],
                    ['label' => 'Smokin kiralama', 'value' => 'tuxedo_rental'],
                    ['label' => 'Düğün aksesuarları', 'value' => 'wedding_accessories'],
                    ['label' => 'Ölçü alımı', 'value' => 'measurements'],
                    ['label' => 'Prova hizmeti', 'value' => 'fitting_service'],
                    ['label' => 'Tamir & değişim', 'value' => 'alterations'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 85,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // Tüm soru gruplarını birleştir
        $allQuestions = array_merge(
            $sportsQuestions,
            $automotiveQuestions,
            $educationQuestions,
            $healthQuestions,
            $turkishCraftsQuestions
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

        echo "❓ Part 4: " . count($allQuestions) . " organize edilmiş sektör sorusu eklendi\n";
    }
}