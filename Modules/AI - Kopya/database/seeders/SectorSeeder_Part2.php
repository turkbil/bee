<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part2 extends Seeder
{
    /**
     * SECTOR SEEDER PART 2 (ID 163+)
     * SQL'den gelen 162 sektöre ek olarak E-ticaret özelleşmiş sektörler + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 2 yükleniyor (ID 163+)...\n";

        // Eksik özelleşmiş sektörleri ekle (ID 163+)
        $this->addMissingSpecializedSectors();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();

        echo "✅ Part 2 tamamlandı! (Özelleşmiş E-ticaret & Yemek sektörleri)\n";
    }
    
    private function addMissingSpecializedSectors(): void
    {
        // SQL'de olmayan özelleşmiş sektörler (ID 163'ten başlayarak)
        $sectors = [
            // ÖZELLEŞMİŞ YİYECEK SEKTÖRLERI (ID 163-170)
            ['id' => 163, 'code' => 'organic_food', 'category_id' => 4, 'name' => 'Organik & Doğal Gıda', 'emoji' => '🌱', 'color' => 'green', 'description' => 'Organik gıda üretimi ve satışı', 'keywords' => 'organik, doğal, sağlıklı, gıda'],
            ['id' => 164, 'code' => 'street_food', 'category_id' => 4, 'name' => 'Sokak Lezzetleri', 'emoji' => '🌮', 'color' => 'orange', 'description' => 'Döner, lahmacun, sokak yemekleri', 'keywords' => 'döner, lahmacun, sokak, fast'],
            ['id' => 165, 'code' => 'dessert_shop', 'category_id' => 4, 'name' => 'Tatlı & Dondurma', 'emoji' => '🍦', 'color' => 'pink', 'description' => 'Tatlı evi, dondurma, şekerci', 'keywords' => 'tatlı, dondurma, şeker, dessert'],
            ['id' => 166, 'code' => 'wine_shop', 'category_id' => 4, 'name' => 'Şarap & İçki', 'emoji' => '🍷', 'color' => 'red', 'description' => 'Şarap evi, içki satış, bar malzemeleri', 'keywords' => 'şarap, içki, alkol, wine'],
            ['id' => 167, 'code' => 'spice_shop', 'category_id' => 4, 'name' => 'Baharat & Kuruyemiş', 'emoji' => '🌶️', 'color' => 'amber', 'description' => 'Baharat satış, kuruyemiş, aktariye', 'keywords' => 'baharat, kuruyemiş, aktariye, spice'],
            
            // ÖZELLEŞMİŞ E-TİCARET SEKTÖRLERI (ID 171-178)
            ['id' => 171, 'code' => 'beauty_cosmetics', 'category_id' => 5, 'name' => 'Güzellik & Kozmetik', 'emoji' => '💄', 'color' => 'rose', 'description' => 'Kozmetik, parfüm, güzellik ürünleri', 'keywords' => 'kozmetik, güzellik, parfüm, makyaj'],
            ['id' => 172, 'code' => 'baby_kids', 'category_id' => 5, 'name' => 'Bebek & Çocuk', 'emoji' => '👶', 'color' => 'blue', 'description' => 'Bebek ürünleri, çocuk giyim, oyuncak', 'keywords' => 'bebek, çocuk, oyuncak, giyim'],
            ['id' => 173, 'code' => 'sports_outdoor', 'category_id' => 5, 'name' => 'Spor & Outdoor', 'emoji' => '⚽', 'color' => 'green', 'description' => 'Spor malzemeleri, outdoor equipment', 'keywords' => 'spor, outdoor, malzeme, ekipman'],
            ['id' => 174, 'code' => 'pet_supplies', 'category_id' => 5, 'name' => 'Pet Shop & Hayvan', 'emoji' => '🐕', 'color' => 'amber', 'description' => 'Pet malzemeleri, hayvan bakım', 'keywords' => 'pet, hayvan, kedi, köpek'],
            ['id' => 175, 'code' => 'books_media', 'category_id' => 5, 'name' => 'Kitap & Medya', 'emoji' => '📚', 'color' => 'blue', 'description' => 'Kitap satış, e-kitap, medya', 'keywords' => 'kitap, e-kitap, medya, yayın'],
            ['id' => 176, 'code' => 'gift_souvenir', 'category_id' => 5, 'name' => 'Hediye & Hediyelik', 'emoji' => '🎁', 'color' => 'purple', 'description' => 'Hediye eşya, hediyelik, özel tasarım', 'keywords' => 'hediye, hediyelik, tasarım, özel'],
            ['id' => 177, 'code' => 'handicrafts', 'category_id' => 5, 'name' => 'El Sanatları & Hobi', 'emoji' => '🎨', 'color' => 'pink', 'description' => 'El yapımı ürünler, hobi malzemeleri', 'keywords' => 'el sanatı, hobi, handmade, craft'],
            ['id' => 178, 'code' => 'vintage_antique', 'category_id' => 5, 'name' => 'Vintage & Antika', 'emoji' => '🏺', 'color' => 'amber', 'description' => 'Antika eşya, vintage ürünler', 'keywords' => 'antika, vintage, eski, koleksiyon'],
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

        echo "📊 Part 2: {$addedCount} özelleşmiş sektör eklendi\n";
    }
    
    private function addSectorQuestions(): void
    {
        // ORGANIZE EDİLEN SEKTÖR SORULARI - PART 2 (YİYECEK & E-TİCARET)
        
        // ===========================================
        // 1. YİYECEK VE İÇECEK SEKTÖRLERI 🍽️
        // ===========================================
        
        // RESTORAN VE YEMEK HİZMETİ
        $foodQuestions = [
            [
                'sector_code' => 'food', 'step' => 3, 'section' => null,
                'question_key' => 'food_specific_services', 'question_text' => 'Hangi yemek ve içecek hizmetlerini sunuyorsunuz?',
                'help_text' => 'Restoran, kafe ve yemek hizmetlerinizdeki uzmanlaştığınız alanlar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Restoran yemek servisi', 'value' => 'restaurant'],
                    ['label' => 'Cafe & kahve', 'value' => 'cafe'],
                    ['label' => 'Fast food', 'value' => 'fast_food'],
                    ['label' => 'Catering hizmeti', 'value' => 'catering'],
                    ['label' => 'Ev yemekleri', 'value' => 'home_cooking'],
                    ['label' => 'Organik gıda', 'value' => 'organic'],
                    ['label' => 'Vegan menü', 'value' => 'vegan'],
                    ['label' => 'Glutensiz menü', 'value' => 'gluten_free'],
                    ['label' => 'Paket servis', 'value' => 'takeaway'],
                    ['label' => 'Online sipariş', 'value' => 'online_order'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // ORGANİK GIDA
        $organicFoodQuestions = [
            [
                'sector_code' => 'organic_food', 'step' => 3, 'section' => null,
                'question_key' => 'organic_food_certifications', 'question_text' => 'Hangi organik sertifikalarınız var?',
                'help_text' => 'Organik gıda üretimi için sahip olduğunuz sertifikalar',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'BRC Sertifikası', 'value' => 'brc'],
                    ['label' => 'ISO 22000', 'value' => 'iso_22000'],
                    ['label' => 'HACCP', 'value' => 'haccp'],
                    ['label' => 'Organik Tarım Sertifikası', 'value' => 'organic_cert'],
                    ['label' => 'Helal Sertifikası', 'value' => 'halal'],
                    ['label' => 'Vegan Sertifikası', 'value' => 'vegan'],
                    ['label' => 'Gluten-Free', 'value' => 'gluten_free'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 85,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];
        
        // SOKAK LEZZETLERİ
        $streetFoodQuestions = [
            [
                'sector_code' => 'street_food', 'step' => 3, 'section' => null,
                'question_key' => 'street_food_specialties', 'question_text' => 'Hangi sokak lezzetlerinde uzmanlaştınız?',
                'help_text' => 'Döner, lahmacun gibi sokak yemekleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Et döner', 'value' => 'meat_doner'],
                    ['label' => 'Tavuk döner', 'value' => 'chicken_doner'],
                    ['label' => 'Lahmacun', 'value' => 'lahmacun'],
                    ['label' => 'Pide', 'value' => 'pide'],
                    ['label' => 'Durum', 'value' => 'wrap'],
                    ['label' => 'Iskender', 'value' => 'iskender'],
                    ['label' => 'Köfte', 'value' => 'meatball'],
                    ['label' => 'Tantuni', 'value' => 'tantuni'],
                    ['label' => 'Çiğ köfte', 'value' => 'raw_meatball'],
                    ['label' => 'Kokoreç', 'value' => 'kokorec'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // RESTORAN
        $restaurantQuestions = [
            [
                'sector_code' => 'restaurant', 'step' => 3, 'section' => null,
                'question_key' => 'restaurant_cuisine_types', 'question_text' => 'Restoranınızın mutfak türü ve özellikleri nelerdir?',
                'help_text' => 'Sunduğunuz mutfak türleri ve özel menü seçenekleri',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Türk mutfağı', 'value' => 'turkish'],
                    ['label' => 'İtalyan mutfağı', 'value' => 'italian'],
                    ['label' => 'Uzak Doğu', 'value' => 'asian'],
                    ['label' => 'Fast food', 'value' => 'fast_food'],
                    ['label' => 'Seafood', 'value' => 'seafood'],
                    ['label' => 'Vejetaryen', 'value' => 'vegetarian'],
                    ['label' => 'Vegan', 'value' => 'vegan'],
                    ['label' => 'Organik', 'value' => 'organic'],
                    ['label' => 'Fine dining', 'value' => 'fine_dining'],
                    ['label' => 'Aile restoranı', 'value' => 'family'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // KAFE
        $cafeQuestions = [
            [
                'sector_code' => 'cafe', 'step' => 3, 'section' => null,
                'question_key' => 'cafe_services', 'question_text' => 'Kafenizde hangi ürün ve hizmetleri sunuyorsunuz?',
                'help_text' => 'Kahve çeşitleri, yiyecekler ve ek hizmetler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Espresso kahveler', 'value' => 'espresso'],
                    ['label' => 'Filtre kahve', 'value' => 'filter_coffee'],
                    ['label' => 'Soğuk kahveler', 'value' => 'cold_coffee'],
                    ['label' => 'Çay çeşitleri', 'value' => 'tea'],
                    ['label' => 'Tatlılar', 'value' => 'desserts'],
                    ['label' => 'Sandviç & salata', 'value' => 'sandwich_salad'],
                    ['label' => 'WiFi', 'value' => 'wifi'],
                    ['label' => 'Çalışma alanı', 'value' => 'workspace'],
                    ['label' => 'Etkinlik alanı', 'value' => 'events'],
                    ['label' => 'Takeaway', 'value' => 'takeaway'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // ===========================================
        // 2. PERAKENDE VE E-TİCARET SEKTÖRLERI 🛒
        // ===========================================
        
        // PERAKENDE GENEL
        $retailQuestions = [
            [
                'sector_code' => 'retail', 'step' => 3, 'section' => null,
                'question_key' => 'retail_specific_services', 'question_text' => 'Hangi perakende ve e-ticaret hizmetlerini sunuyorsunuz?',
                'help_text' => 'Mağaza işletmeciliği ve e-ticaret alanındaki hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Online mağaza', 'value' => 'online_store'],
                    ['label' => 'Fiziki mağaza', 'value' => 'physical_store'],
                    ['label' => 'Toptan satış', 'value' => 'wholesale'],
                    ['label' => 'Perakende satış', 'value' => 'retail_sales'],
                    ['label' => 'Kargo & teslimat', 'value' => 'shipping'],
                    ['label' => 'Müşteri hizmetleri', 'value' => 'customer_service'],
                    ['label' => 'İade & değişim', 'value' => 'returns'],
                    ['label' => 'Ödeme sistemleri', 'value' => 'payment_systems'],
                    ['label' => 'Mobil uygulama', 'value' => 'mobile_app'],
                    ['label' => 'Sadakat programı', 'value' => 'loyalty_program'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ]
        ];

        // GÜZELLİK & KOZMETİK
        $beautyQuestions = [
            [
                'sector_code' => 'beauty_cosmetics', 'step' => 3, 'section' => null,
                'question_key' => 'beauty_product_categories', 'question_text' => 'Hangi güzellik ve kozmetik ürün kategorilerinde faaliyet gösteriyorsunuz?',
                'help_text' => 'Kozmetik, parfüm ve güzellik ürünleri alanındaki uzmanlaştığınız kategoriler',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Makyaj ürünleri', 'value' => 'makeup'],
                    ['label' => 'Cilt bakım', 'value' => 'skincare'],
                    ['label' => 'Parfüm & koku', 'value' => 'perfume'],
                    ['label' => 'Saç bakım', 'value' => 'haircare'],
                    ['label' => 'Nail art & ojeler', 'value' => 'nails'],
                    ['label' => 'Erkek bakım', 'value' => 'mens_care'],
                    ['label' => 'Organik kozmetik', 'value' => 'organic'],
                    ['label' => 'Anti-aging', 'value' => 'anti_aging'],
                    ['label' => 'Güneş koruma', 'value' => 'sun_protection'],
                    ['label' => 'Makyaj fırçaları', 'value' => 'brushes'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // BEBEK & ÇOCUK
        $babyKidsQuestions = [
            [
                'sector_code' => 'baby_kids', 'step' => 3, 'section' => null,
                'question_key' => 'baby_kids_categories', 'question_text' => 'Bebek ve çocuk ürünlerinde hangi kategorilerde hizmet veriyorsunuz?',
                'help_text' => 'Bebek bakımı, çocuk giyim ve oyuncak kategorilerindeki ürün gamınız',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Bebek bakım ürünleri', 'value' => 'baby_care'],
                    ['label' => 'Bebek giyim', 'value' => 'baby_clothing'],
                    ['label' => 'Çocuk giyim', 'value' => 'kids_clothing'],
                    ['label' => 'Oyuncaklar', 'value' => 'toys'],
                    ['label' => 'Bebek arabası & equipment', 'value' => 'baby_gear'],
                    ['label' => 'Emzirme ürünleri', 'value' => 'nursing'],
                    ['label' => 'Çocuk odası mobilya', 'value' => 'furniture'],
                    ['label' => 'Eğitici oyuncaklar', 'value' => 'educational_toys'],
                    ['label' => 'Güvenlik ürünleri', 'value' => 'safety'],
                    ['label' => 'Bebek maması & gıda', 'value' => 'baby_food'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'service_portfolio'
            ]
        ];

        // Tüm soru gruplarını birleştir
        $allQuestions = array_merge(
            $foodQuestions,
            $organicFoodQuestions,
            $streetFoodQuestions,
            $restaurantQuestions,
            $cafeQuestions,
            $retailQuestions,
            $beautyQuestions,
            $babyKidsQuestions
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

        echo "❓ Part 2: " . count($allQuestions) . " organize edilmiş sektör sorusu eklendi\n";
    }
}
