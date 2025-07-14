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
        $questions = [
            // YİYECEK SEKTÖRÜ SORULARI
            [
                'sector_code' => 'organic_food', 'step' => 3, 'section' => null,
                'question_key' => 'organic_food_certifications', 'question_text' => 'Hangi organik sertifikalarınız var?',
                'help_text' => 'Organik gıda üretimi için sahip olduğunuz sertifikalar',
                'input_type' => 'checkbox',
                'options' => '["BRC Sertifikası", "ISO 22000", "HACCP", "Organik Tarım Sertifikası", "Helal Sertifikası", "Vegan Sertifikası", "Gluten-Free", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'street_food', 'step' => 3, 'section' => null,
                'question_key' => 'street_food_specialties', 'question_text' => 'Hangi sokak lezzetlerinde uzmanlaştınız?',
                'help_text' => 'Döner, lahmacun gibi sokak yemekleriniz',
                'input_type' => 'checkbox',
                'options' => '["Et döner", "Tavuk döner", "Lahmacun", "Pide", "Durum", "Iskender", "Köfte", "Tantuni", "Çiğ köfte", "Kokoreç", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // E-TİCARET SEKTÖRÜ SORULARI
            [
                'sector_code' => 'beauty_cosmetics', 'step' => 3, 'section' => null,
                'question_key' => 'beauty_product_categories', 'question_text' => 'Hangi güzellik ürünlerini satıyorsunuz?',
                'help_text' => 'Kozmetik ve güzellik ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => '["Makyaj", "Cilt bakımı", "Saç bakımı", "Parfüm", "Nail art", "Organik kozmetik", "Erkek bakım", "Anti-aging", "Güneş koruma", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'sports_outdoor', 'step' => 3, 'section' => null,
                'question_key' => 'sports_equipment_types', 'question_text' => 'Hangi spor dallarında ekipman sağlıyorsunuz?',
                'help_text' => 'Satış yaptığınız spor dalları ve ekipmanlar',
                'input_type' => 'checkbox',
                'options' => '["Futbol", "Basketbol", "Tenis", "Fitness", "Yoga", "Koşu", "Bisiklet", "Kamp-doğa", "Su sporları", "Kış sporları", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EKSİK YİYECEK SEKTÖRÜ SORULARI
            [
                'sector_code' => 'dessert_shop', 'step' => 3, 'section' => null,
                'question_key' => 'dessert_specialties', 'question_text' => 'Hangi tatlı çeşitlerinde uzmanlaştınız?',
                'help_text' => 'Tatlı evi ve dondurma ürün çeşitleriniz',
                'input_type' => 'checkbox',
                'options' => '["Geleneksel tatlılar", "Modern patisserie", "Dondurma", "Frozen yogurt", "Milkshake", "Pasta & kek", "Çikolata", "Şeker & lokum", "Diyet tatlılar", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'wine_shop', 'step' => 3, 'section' => null,
                'question_key' => 'wine_categories', 'question_text' => 'Hangi içki kategorilerinde satış yapıyorsunuz?',
                'help_text' => 'Şarap evi ve içki satış ürün gruplarınız',
                'input_type' => 'checkbox',
                'options' => '["Yerli şaraplar", "İthal şaraplar", "Craft beer", "Viski", "Votka", "Rakı", "Likör", "Şampanya", "Bar malzemeleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'spice_shop', 'step' => 3, 'section' => null,
                'question_key' => 'spice_categories', 'question_text' => 'Hangi baharat ve kuruyemiş çeşitleriniz var?',
                'help_text' => 'Baharat, kuruyemiş ve aktariye ürün gruplarınız',
                'input_type' => 'checkbox',
                'options' => '["Geleneksel baharatlar", "Organik baharatlar", "Kuruyemiş", "Kuru meyve", "Çay & bitki çayları", "Bal & polen", "Aktariye ürünleri", "Tahıllar", "Granola & müsli", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EKSİK E-TİCARET SEKTÖRÜ SORULARI
            [
                'sector_code' => 'baby_kids', 'step' => 3, 'section' => null,
                'question_key' => 'baby_product_categories', 'question_text' => 'Hangi bebek ve çocuk ürünlerini satıyorsunuz?',
                'help_text' => 'Bebek, çocuk ürün kategorileriniz ve yaş grupları',
                'input_type' => 'checkbox',
                'options' => '["Bebek giyim (0-2 yaş)", "Çocuk giyim (3-12 yaş)", "Bebek beslenme", "Bebek bakım", "Oyuncaklar", "Bebek mobilyası", "Emzirme ürünleri", "Bebek güvenlik", "Çocuk kitapları", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'pet_supplies', 'step' => 3, 'section' => null,
                'question_key' => 'pet_categories', 'question_text' => 'Hangi hayvan türleri için ürün satıyorsunuz?',
                'help_text' => 'Pet shop hayvan türleri ve ürün kategorileriniz',
                'input_type' => 'checkbox',
                'options' => '["Köpek ürünleri", "Kedi ürünleri", "Kuş ürünleri", "Balık & akvaryum", "Hamster & kemirgen", "Tavşan ürünleri", "Kedi kumu", "Pet maması", "Veteriner ürünleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'books_media', 'step' => 3, 'section' => null,
                'question_key' => 'book_categories', 'question_text' => 'Hangi kitap ve medya türlerinde satış yapıyorsunuz?',
                'help_text' => 'Kitap kategorileriniz ve medya ürün türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Roman & edebiyat", "Çocuk kitapları", "Eğitim kitapları", "Hobi & yaşam", "Kişisel gelişim", "E-kitap", "Dergi & gazete", "Müzik CD/DVD", "Film DVD/Blu-ray", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'gift_souvenir', 'step' => 3, 'section' => null,
                'question_key' => 'gift_categories', 'question_text' => 'Hangi hediye ve hediyelik kategorilerinde uzmanlaştınız?',
                'help_text' => 'Hediye çeşitleriniz ve özel tasarım hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Doğum günü hediyeleri", "Düğün hediyeleri", "Kurumsal hediyeler", "Hediyelik eşya", "Özel tasarım", "Kişiye özel baskı", "Antika & koleksiyon", "El yapımı ürünler", "Çiçek & sepet", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'handicrafts', 'step' => 3, 'section' => null,
                'question_key' => 'handicraft_types', 'question_text' => 'Hangi el sanatları dallarında ürün yapıyorsunuz?',
                'help_text' => 'El yapımı ürün türleriniz ve hobi malzemeleriniz',
                'input_type' => 'checkbox',
                'options' => '["Örgü & örme", "Dikiş & nakış", "Seramik & çömlek", "Ahşap işleri", "Takı yapımı", "Resin sanat", "Boyama & ressam", "Hobi malzemeleri", "DIY setleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'vintage_antique', 'step' => 3, 'section' => null,
                'question_key' => 'vintage_categories', 'question_text' => 'Hangi vintage ve antika ürün kategorilerinde uzmanlaştınız?',
                'help_text' => 'Antika, vintage ürün türleriniz ve dönem uzmanlıklarınız',
                'input_type' => 'checkbox',
                'options' => '["Mobilya & dekor", "Mücevher & saat", "Kitap & basılı eser", "Porselen & seramik", "Vintage giyim", "Koleksiyon eşyaları", "Sanat eserleri", "Ev eşyaları", "Restorasyon hizmeti", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
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

        echo "❓ Part 2: " . count($questions) . " özel soru eklendi\n";
    }
}