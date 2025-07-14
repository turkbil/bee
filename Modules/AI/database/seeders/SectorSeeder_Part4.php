<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part4 extends Seeder
{
    /**
     * SECTOR SEEDER PART 4 (ID 201+)
     * Özel Türk esnaf sektörleri ve niş alanlar + özel sorular
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 4 yükleniyor (ID 201+)...\n";

        // Özel Türk esnaf ve niş sektörleri ekle (ID 201+)
        $this->addSpecialTurkishSectors();
        
        // Bu sektörlere özel sorular ekle
        $this->addSectorQuestions();

        echo "✅ Part 4 tamamlandı! (Özel Türk Esnaf & Niş sektörler)\n";
    }
    
    private function addSpecialTurkishSectors(): void
    {
        // Özel Türk esnaf ve niş sektörler (ID 201'den başlayarak)
        $sectors = [
            // ÖZEL TÜRK ESNAF SEKTÖRLERI (ID 201-215)
            ['id' => 201, 'code' => 'wedding_dress', 'category_id' => 14, 'name' => 'Gelinlik & Abiye', 'emoji' => '👰', 'color' => 'rose', 'description' => 'Gelinlik, abiye, düğün kıyafetleri', 'keywords' => 'gelinlik, abiye, düğün, gelin'],
            ['id' => 202, 'code' => 'flower_shop', 'category_id' => 14, 'name' => 'Çiçekçi & Bahçıvanlık', 'emoji' => '🌹', 'color' => 'green', 'description' => 'Çiçekçilik, peyzaj, bahçıvanlık', 'keywords' => 'çiçek, bahçe, peyzaj, orkide'],
            ['id' => 203, 'code' => 'carpet_rug', 'category_id' => 14, 'name' => 'Halı & Kilim', 'emoji' => '🪺', 'color' => 'amber', 'description' => 'El dokuması halı, kilim, duvar halısı', 'keywords' => 'halı, kilim, dokuması, antika'],
            ['id' => 204, 'code' => 'market_grocery', 'category_id' => 5, 'name' => 'Market & Bakkal', 'emoji' => '🏪', 'color' => 'green', 'description' => 'Mahalle marketi, bakkal, şarküteri', 'keywords' => 'market, bakkal, şarküteri, mahalle'],
            ['id' => 205, 'code' => 'gas_station', 'category_id' => 10, 'name' => 'Benzinlik & Akaryakıt', 'emoji' => '⛽', 'color' => 'red', 'description' => 'Benzin istasyonu, LPG, oto yıkama', 'keywords' => 'benzin, akaryakıt, LPG, yakıt'],
            ['id' => 206, 'code' => 'stationery_shop', 'category_id' => 5, 'name' => 'Kırtasiye & Okul', 'emoji' => '📚', 'color' => 'blue', 'description' => 'Kırtasiye, okul malzemeleri, fotokopi', 'keywords' => 'kırtasiye, okul, fotokopi, kalem'],
            ['id' => 207, 'code' => 'toy_shop', 'category_id' => 5, 'name' => 'Oyuncak & Bebek', 'emoji' => '🧸', 'color' => 'pink', 'description' => 'Oyuncak, bebek, çocuk ürünleri', 'keywords' => 'oyuncak, bebek, çocuk, eğlence'],
            ['id' => 208, 'code' => 'furniture_maker', 'category_id' => 18, 'name' => 'Marangoz & Mobilya', 'emoji' => '🪑', 'color' => 'brown', 'description' => 'Mobilya yapımı, marangozluk, ahşap', 'keywords' => 'marangoz, mobilya, ahşap, masa'],
            ['id' => 209, 'code' => 'blacksmith_metal', 'category_id' => 17, 'name' => 'Demirci & Metal İşleri', 'emoji' => '🔨', 'color' => 'gray', 'description' => 'Demircilik, metal işleme, kaynak', 'keywords' => 'demirci, metal, kaynak, demir'],
            ['id' => 210, 'code' => 'curtain_blind', 'category_id' => 14, 'name' => 'Perde & Jaluzici', 'emoji' => '🪟', 'color' => 'blue', 'description' => 'Perde, jaluzi, ev tekstili', 'keywords' => 'perde, jaluzi, ev tekstili, cam'],
            
            // NİŞ VE ÖZEL ALANLAR (ID 211-220)
            ['id' => 211, 'code' => 'traditional_crafts', 'category_id' => 8, 'name' => 'Geleneksel Sanatlar', 'emoji' => '🏺', 'color' => 'amber', 'description' => 'Çini, seramik, geleneksel el sanatları', 'keywords' => 'çini, seramik, geleneksel, sanat'],
            ['id' => 212, 'code' => 'musical_instruments', 'category_id' => 8, 'name' => 'Müzik Aletleri', 'emoji' => '🎸', 'color' => 'purple', 'description' => 'Enstrüman satış, tamir, müzik', 'keywords' => 'enstrüman, müzik, gitar, piyano'],
            ['id' => 213, 'code' => 'second_hand', 'category_id' => 5, 'name' => 'İkinci El & Antika', 'emoji' => '🕰️', 'color' => 'amber', 'description' => 'İkinci el eşya, antika, koleksiyon', 'keywords' => 'ikinci el, antika, eski, koleksiyon'],
            ['id' => 214, 'code' => 'hobby_collection', 'category_id' => 5, 'name' => 'Hobi & Koleksiyon', 'emoji' => '🎯', 'color' => 'indigo', 'description' => 'Hobi malzemeleri, koleksiyon eşyaları', 'keywords' => 'hobi, koleksiyon, maket, oyun'],
            ['id' => 215, 'code' => 'party_organization', 'category_id' => 18, 'name' => 'Parti & Organizasyon', 'emoji' => '🎉', 'color' => 'pink', 'description' => 'Doğum günü, parti, etkinlik organizasyonu', 'keywords' => 'parti, doğum günü, etkinlik, balon'],
            ['id' => 216, 'code' => 'fishing_hunting', 'category_id' => 9, 'name' => 'Balık & Avcılık', 'emoji' => '🎣', 'color' => 'green', 'description' => 'Balık malzemeleri, avcılık, outdoor', 'keywords' => 'balık, avcılık, olta, doğa'],
            ['id' => 217, 'code' => 'camping_outdoor', 'category_id' => 9, 'name' => 'Kamp & Doğa Sporları', 'emoji' => '🏕️', 'color' => 'green', 'description' => 'Kamp malzemeleri, doğa sporları', 'keywords' => 'kamp, çadır, doğa, outdoor'],
            ['id' => 218, 'code' => 'religious_items', 'category_id' => 14, 'name' => 'Dini Eşya & Kitap', 'emoji' => '📿', 'color' => 'green', 'description' => 'Dini kitap, tesbih, hac-umre', 'keywords' => 'dini, kitap, tesbih, hac'],
            ['id' => 219, 'code' => 'occult_spiritual', 'category_id' => 14, 'name' => 'Metafizik & Ruhani', 'emoji' => '🔮', 'color' => 'purple', 'description' => 'Tarot, kristal, ruhani danışmanlık', 'keywords' => 'tarot, kristal, ruhani, metafizik'],
            ['id' => 220, 'code' => 'funeral_cemetery', 'category_id' => 18, 'name' => 'Cenaze & Mezarlık', 'emoji' => '⚱️', 'color' => 'gray', 'description' => 'Cenaze hizmetleri, mezar taşları', 'keywords' => 'cenaze, mezar, tabut, defin'],
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
        $questions = [
            // TÜRK ESNAF SEKTÖRÜ SORULARI
            [
                'sector_code' => 'wedding_dress', 'step' => 3, 'section' => null,
                'question_key' => 'wedding_dress_services', 'question_text' => 'Hangi gelinlik hizmetlerini sunuyorsunuz?',
                'help_text' => 'Gelinlik ve düğün kıyafeti hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Gelinlik satış", "Gelinlik kiralama", "Abiye satış", "Smokin kiralama", "Düğün aksesuarları", "Ölçü alımı", "Prova hizmeti", "Tamir-değişim", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'flower_shop', 'step' => 3, 'section' => null,
                'question_key' => 'flower_services', 'question_text' => 'Hangi çiçekçilik hizmetlerini veriyorsunuz?',
                'help_text' => 'Çiçek ve bahçıvanlık hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Kesme çiçek", "Gelin buketi", "Cenaze çelengi", "Saksı çiçekleri", "Orkide bakımı", "Bahçe düzenleme", "Peyzaj tasarımı", "Çiçek abonesi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // NİŞ SEKTÖR SORULARI
            [
                'sector_code' => 'traditional_crafts', 'step' => 3, 'section' => null,
                'question_key' => 'traditional_craft_types', 'question_text' => 'Hangi geleneksel sanat dalında çalışıyorsunuz?',
                'help_text' => 'Uzmanlık alanınızdaki geleneksel sanat türleri',
                'input_type' => 'checkbox',
                'options' => '["Çini", "Seramik", "Ebru", "Hat sanatı", "Minyatür", "Kalem işi", "Tezhip", "El dokuması", "Ahşap oyma", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 3, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 3, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'market_grocery', 'step' => 3, 'section' => null,
                'question_key' => 'market_product_categories', 'question_text' => 'Marketinizde hangi ürün grupları bulunuyor?',
                'help_text' => 'Satış yaptığınız temel ürün kategorileri',
                'input_type' => 'checkbox',
                'options' => '["Gıda ürünleri", "Temizlik malzemeleri", "Kişisel bakım", "Bebek ürünleri", "Dondurulmuş gıda", "Et-şarküteri", "Süt ürünleri", "Meyve-sebze", "İçecekler", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EKSİK TÜRK ESNAF SEKTÖRÜ SORULARI
            [
                'sector_code' => 'carpet_rug', 'step' => 3, 'section' => null,
                'question_key' => 'carpet_services', 'question_text' => 'Hangi halı ve kilim hizmetlerini sunuyorsunuz?',
                'help_text' => 'El dokuması halı, kilim ve duvar halısı hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["El dokuması halı", "Makine halısı", "Kilim", "Yolluk", "Duvar halısı", "Antika halı", "Halı yıkama", "Halı tamiri", "Özel sipariş", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'gas_station', 'step' => 3, 'section' => null,
                'question_key' => 'gas_station_services', 'question_text' => 'Hangi benzinlik ve akaryakıt hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Benzin istasyonu, LPG ve oto yıkama hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Benzin", "Motorin", "LPG", "AdBlue", "Oto yıkama", "Lastik tamiri", "Yağ değişimi", "Market", "Cafe", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'stationery_shop', 'step' => 3, 'section' => null,
                'question_key' => 'stationery_categories', 'question_text' => 'Hangi kırtasiye ve okul malzemelerini satıyorsunuz?',
                'help_text' => 'Kırtasiye, okul malzemeleri ve fotokopi hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Okul malzemeleri", "Ofis malzemeleri", "Sanat malzemeleri", "Fotokopi", "Baskı hizmeti", "Spiral ciltleme", "Laminasyon", "Kalem & silgi", "Defter & blok", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'toy_shop', 'step' => 3, 'section' => null,
                'question_key' => 'toy_categories', 'question_text' => 'Hangi oyuncak ve bebek ürünlerini satıyorsunuz?',
                'help_text' => 'Oyuncak, bebek ve çocuk ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => '["Bebek oyuncakları", "Eğitici oyuncaklar", "Aksiyon figürleri", "Puzzle", "Lego & yapı", "Peluş oyuncaklar", "Elektronik oyuncaklar", "Spor oyuncakları", "Kız oyuncakları", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'furniture_maker', 'step' => 3, 'section' => null,
                'question_key' => 'furniture_services', 'question_text' => 'Hangi marangoz ve mobilya hizmetlerini yapıyorsunuz?',
                'help_text' => 'Mobilya yapımı, marangozluk ve ahşap hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Mutfak dolabı", "Yatak odası", "Oturma grubu", "Özel mobilya", "Ahşap merdiven", "Kapı & pencere", "Mobilya tamiri", "Ahşap işleme", "Restorasyon", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'blacksmith_metal', 'step' => 3, 'section' => null,
                'question_key' => 'metal_services', 'question_text' => 'Hangi demirci ve metal işleri hizmetlerini yapıyorsunuz?',
                'help_text' => 'Demircilik, metal işleme ve kaynak hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Demir korkuluk", "Bahçe kapısı", "Merdiven korkuluğu", "Metal kapı", "Kaynak işleri", "Saç kesim", "Büküm işleri", "Tamir işleri", "Özel tasarım", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'curtain_blind', 'step' => 3, 'section' => null,
                'question_key' => 'curtain_services', 'question_text' => 'Hangi perde ve jaluzici hizmetlerini sunuyorsunuz?',
                'help_text' => 'Perde, jaluzi ve ev tekstili hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Fon perde", "Jaluzi", "Zebra perde", "Stor perde", "Tül perde", "Panel perde", "Motorlu perde", "Ölçü & montaj", "Perde yıkama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // NİŞ SEKTÖR SORULARI EKSİKLER
            [
                'sector_code' => 'musical_instruments', 'step' => 3, 'section' => null,
                'question_key' => 'instrument_categories', 'question_text' => 'Hangi müzik aletlerinde satış ve tamir yapıyorsunuz?',
                'help_text' => 'Enstrüman satış, tamir ve müzik hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Gitar", "Piyano & klavye", "Davul", "Keman", "Flüt", "Saz & bağlama", "Ses sistemi", "Enstrüman tamiri", "Müzik dersi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'second_hand', 'step' => 3, 'section' => null,
                'question_key' => 'second_hand_categories', 'question_text' => 'Hangi ikinci el ve antika ürün kategorilerinde çalışıyorsunuz?',
                'help_text' => 'İkinci el eşya, antika ve koleksiyon ürün türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Mobilya", "Elektronik", "Giyim", "Kitap", "Ev eşyası", "Koleksiyon", "Antika", "Motor & oto", "Müzik aletleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'hobby_collection', 'step' => 3, 'section' => null,
                'question_key' => 'hobby_categories', 'question_text' => 'Hangi hobi ve koleksiyon alanlarında ürün satıyorsunuz?',
                'help_text' => 'Hobi malzemeleri ve koleksiyon eşya türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Maket & RC", "Pul koleksiyonu", "Para koleksiyonu", "Kartpostal", "Oyun kartları", "Figür koleksiyonu", "Hobi boyama", "El işi malzemeleri", "Puzzle & bulmaca", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'party_organization', 'step' => 3, 'section' => null,
                'question_key' => 'party_services', 'question_text' => 'Hangi parti ve organizasyon hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Doğum günü, parti ve etkinlik organizasyon hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Doğum günü partisi", "Bebek partisi", "Çocuk etkinlikleri", "Tema partiler", "Balon süsleme", "Animasyon", "Müzik & DJ", "Catering", "Parti malzemeleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'fishing_hunting', 'step' => 3, 'section' => null,
                'question_key' => 'fishing_categories', 'question_text' => 'Hangi balık ve avcılık malzemelerini satıyorsunuz?',
                'help_text' => 'Balık malzemeleri, avcılık ve outdoor ürün türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Olta & makine", "Balık yemi", "Avcılık tüfeği", "Av malzemeleri", "Kamp malzemeleri", "Outdoor giyim", "Balık bulma cihazı", "Avcı giyimi", "Doğa spor malzemeleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'camping_outdoor', 'step' => 3, 'section' => null,
                'question_key' => 'camping_categories', 'question_text' => 'Hangi kamp ve doğa sporları malzemelerini satıyorsunuz?',
                'help_text' => 'Kamp malzemeleri ve doğa sporları ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => '["Çadır", "Uyku tulumu", "Kamp sandalyesi", "Outdoor giyim", "Tırmanış malzemeleri", "Doğa yürüyüşü", "Kamp ocağı", "Su ürünleri", "Outdoor ayakkabı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'religious_items', 'step' => 3, 'section' => null,
                'question_key' => 'religious_categories', 'question_text' => 'Hangi dini eşya ve kitap kategorilerinde satış yapıyorsunuz?',
                'help_text' => 'Dini kitap, tesbih ve hac-umre ürün türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Kuran-ı Kerim", "Dini kitaplar", "Tesbih", "Seccade", "Hac & umre malzemeleri", "İslami takı", "Dini hediyeler", "Çocuk dini kitapları", "Dua kitapları", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'occult_spiritual', 'step' => 3, 'section' => null,
                'question_key' => 'spiritual_services', 'question_text' => 'Hangi metafizik ve ruhani hizmetleri sunuyorsunuz?',
                'help_text' => 'Tarot, kristal ve ruhani danışmanlık hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Tarot falı", "Kahve falı", "Kristal terapi", "Enerji temizleme", "Reiki", "Meditasyon", "Çakra çalışması", "Ruhani danışmanlık", "Astroloji", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 3, 'ai_weight' => 60, 'category' => 'company',
                'ai_priority' => 3, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'funeral_cemetery', 'step' => 3, 'section' => null,
                'question_key' => 'funeral_services', 'question_text' => 'Hangi cenaze ve mezarlık hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Cenaze hizmetleri ve mezar taşları hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Cenaze organizasyonu", "Mezar taşı", "Mezar kazımı", "Cenaze aracı", "Defin işlemleri", "Mevlit organizasyonu", "Mezar bakımı", "Mezarlık işleri", "Cenaze çelengi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 3, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 3, 'always_include' => 0, 'context_category' => 'service_portfolio'
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

        echo "❓ Part 4: " . count($questions) . " özel soru eklendi\n";
    }
}