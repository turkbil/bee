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
        $questions = [
            // HİZMET SEKTÖRÜ SORULARI
            [
                'sector_code' => 'cleaning_services', 'step' => 3, 'section' => null,
                'question_key' => 'cleaning_service_types', 'question_text' => 'Hangi temizlik hizmetlerini veriyorsunuz?',
                'help_text' => 'Sunduğunuz temizlik hizmet türleri',
                'input_type' => 'checkbox',
                'options' => '["Ev temizliği", "Ofis temizliği", "Cam temizliği", "Halı yıkama", "Koltuk temizliği", "İnşaat sonrası temizlik", "Endüstriyel temizlik", "Dezenfeksiyon", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'security_services', 'step' => 3, 'section' => null,
                'question_key' => 'security_service_types', 'question_text' => 'Hangi güvenlik hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Güvenlik hizmetleriniz ve teknik ekipmanlar',
                'input_type' => 'checkbox',
                'options' => '["Fiziki güvenlik", "Kamera sistemi", "Alarm sistemi", "Yangın söndürme", "Access kontrol", "Güvenlik danışmanlığı", "Gece güvenliği", "Etkinlik güvenliği", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // ESNAF SEKTÖRÜ SORULARI
            [
                'sector_code' => 'hairdresser_barber', 'step' => 3, 'section' => null,
                'question_key' => 'hairdresser_services', 'question_text' => 'Hangi kuaförlük hizmetlerini sunuyorsunuz?',
                'help_text' => 'Kuaför ve güzellik hizmet çeşitleriniz',
                'input_type' => 'checkbox',
                'options' => '["Kadın kesim", "Erkek kesim", "Saç boyama", "Fön", "Saç bakımı", "Perma", "Kaynak", "Gelin başı", "Makyaj", "Kaş-kirpik", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],
            
            [
                'sector_code' => 'phone_repair', 'step' => 3, 'section' => null,
                'question_key' => 'phone_repair_services', 'question_text' => 'Hangi telefon tamir hizmetlerini veriyorsunuz?',
                'help_text' => 'Telefon tamir ve teknik hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Ekran değişimi", "Batarya değişimi", "Şarj girişi tamiri", "Kamera tamiri", "Hoparlör tamiri", "Software güncelleme", "Su hasarı tamiri", "Unlock işlemleri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EKSİK HİZMET SEKTÖRÜ SORULARI
            [
                'sector_code' => 'logistics_cargo', 'step' => 3, 'section' => null,
                'question_key' => 'logistics_services', 'question_text' => 'Hangi lojistik ve kargo hizmetlerini veriyorsunuz?',
                'help_text' => 'Kargo, nakliye ve depolama hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Şehir içi kargo", "Şehirler arası kargo", "Uluslararası kargo", "Express teslimat", "Nakliye", "Depolama", "Ambalajlama", "Sigortalı kargo", "Ağır yük taşıma", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'wedding_events', 'step' => 3, 'section' => null,
                'question_key' => 'event_services', 'question_text' => 'Hangi düğün ve etkinlik hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Düğün organizasyonu ve etkinlik hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Düğün organizasyonu", "Nişan organizasyonu", "Doğum günü", "Kurumsal etkinlikler", "Kına gecesi", "Bebekek shower", "Müzik & DJ", "Fotoğraf & video", "Catering", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'translation_services', 'step' => 3, 'section' => null,
                'question_key' => 'translation_languages', 'question_text' => 'Hangi dillerde çeviri hizmeti veriyorsunuz?',
                'help_text' => 'Çeviri ve tercümanlık dil seçenekleriniz',
                'input_type' => 'checkbox',
                'options' => '["İngilizce", "Almanca", "Fransızca", "İtalyanca", "İspanyolca", "Rusça", "Arapça", "Çince", "Japonca", "Teknik çeviri", "Yasal çeviri", "Tıbbi çeviri", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'consulting_services', 'step' => 3, 'section' => null,
                'question_key' => 'consulting_areas', 'question_text' => 'Hangi alanlarda danışmanlık hizmeti veriyorsunuz?',
                'help_text' => 'İş danışmanlığı ve strateji uzmanlık alanlarınız',
                'input_type' => 'checkbox',
                'options' => '["İş stratejisi", "Mali müşavirlik", "İnsan kaynakları", "Pazarlama", "IT danışmanlığı", "Hukuki danışmanlık", "Yönetim danışmanlığı", "Proje yönetimi", "Kalite yönetimi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'repair_technical', 'step' => 3, 'section' => null,
                'question_key' => 'repair_services', 'question_text' => 'Hangi cihazların teknik servis ve tamirini yapıyorsunuz?',
                'help_text' => 'Teknik servis ve tamir hizmet alanlarınız',
                'input_type' => 'checkbox',
                'options' => '["Beyaz eşya", "Elektronik cihazlar", "Bilgisayar", "Telefon & tablet", "TV & ses sistemleri", "Klima servisi", "Kombi servisi", "Elektronik kart tamiri", "Yazılım kurulumu", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'photography_video', 'step' => 3, 'section' => null,
                'question_key' => 'photography_services', 'question_text' => 'Hangi fotoğraf ve video hizmetlerini sunuyorsunuz?',
                'help_text' => 'Fotoğrafçılık ve video çekim hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Düğün fotoğrafı", "Doğum fotoğrafı", "Ürün fotoğrafı", "Kurumsal fotoğraf", "Video çekim", "Drone çekim", "Video montaj", "Fotoğraf retüş", "Stüdyo kiralama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'advertising_agency', 'step' => 3, 'section' => null,
                'question_key' => 'advertising_services', 'question_text' => 'Hangi reklam ve marka yönetimi hizmetlerini veriyorsunuz?',
                'help_text' => 'Reklam ajansı hizmet türleriniz ve uzmanlık alanları',
                'input_type' => 'checkbox',
                'options' => '["Marka kimliği", "Logo tasarım", "Web tasarım", "Sosyal medya yönetimi", "Google Ads", "Facebook Ads", "Video reklam", "Basılı reklam", "Kreatif tasarım", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'printing_design', 'step' => 3, 'section' => null,
                'question_key' => 'printing_services', 'question_text' => 'Hangi matbaa ve tasarım hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Matbaa, baskı ve tasarım hizmet çeşitleriniz',
                'input_type' => 'checkbox',
                'options' => '["Dijital baskı", "Offset baskı", "Büyük format baskı", "Kartvizit", "Broşür & katalog", "Afiş & poster", "Banner & tabela", "Ambalaj tasarımı", "Ciltsiz/Ciltli kitap", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            // EKSİK ESNAF SEKTÖRÜ SORULARI
            [
                'sector_code' => 'nail_salon', 'step' => 3, 'section' => null,
                'question_key' => 'nail_services', 'question_text' => 'Hangi nail salon ve güzellik hizmetlerini sunuyorsunuz?',
                'help_text' => 'Nail art, manikür ve güzellik hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Manikür", "Pedikür", "Nail art", "Jel oje", "Protez tırnak", "Tırnak bakımı", "El bakımı", "Ayak bakımı", "Nail piercing", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'massage_spa', 'step' => 3, 'section' => null,
                'question_key' => 'spa_services', 'question_text' => 'Hangi masaj ve SPA hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Masaj, spa ve wellness hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Klasik masaj", "İsveç masajı", "Aromaterapi", "Refleksoloji", "Hamam", "Sauna", "Cilt bakımı", "Vücut bakımı", "Çift masajı", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'tailor_alteration', 'step' => 3, 'section' => null,
                'question_key' => 'tailor_services', 'question_text' => 'Hangi terzilik ve değişim hizmetlerini yapıyorsunuz?',
                'help_text' => 'Terzilik, değişim ve ütü hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Pantolon paça", "Etek boyu", "Ceket değişimi", "Fermuar tamiri", "Düğme değişimi", "Dikiş tamiri", "Ütü hizmeti", "Kuru temizleme", "Özel dikim", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'shoe_repair', 'step' => 3, 'section' => null,
                'question_key' => 'shoe_repair_services', 'question_text' => 'Hangi ayakkabı ve çanta tamir hizmetlerini yapıyorsunuz?',
                'help_text' => 'Ayakkabı tamiri ve deri eşya tamir hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Topuk tamiri", "Taban değişimi", "Fermuar tamiri", "Dikiş tamiri", "Boyama", "Cila hizmeti", "Çanta tamiri", "Deri onarımı", "Ayakkabı boyama", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'key_locksmith', 'step' => 3, 'section' => null,
                'question_key' => 'locksmith_services', 'question_text' => 'Hangi anahtar ve çilingir hizmetlerini veriyorsunuz?',
                'help_text' => 'Anahtar çoğaltma ve çilingir hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Anahtar çoğaltma", "Kapı açma", "Kilit değişimi", "Güvenlik kilidi", "Otomobil anahtarı", "Alarm sistemi", "Kasa açma", "Çelik kapı kilidi", "İmmobilizer", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'watch_repair', 'step' => 3, 'section' => null,
                'question_key' => 'watch_repair_services', 'question_text' => 'Hangi saat tamir ve hizmetlerini yapıyorsunuz?',
                'help_text' => 'Saat tamiri ve bakım hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Pil değişimi", "Cam değişimi", "Kayış değişimi", "Mekanizma tamiri", "Su alma tamiri", "Kalibre etme", "Antika saat tamiri", "Duvar saati tamiri", "Garantili tamir", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'jewelry_gold', 'step' => 3, 'section' => null,
                'question_key' => 'jewelry_services', 'question_text' => 'Hangi kuyumcu ve altın hizmetlerini sunuyorsunuz?',
                'help_text' => 'Altın, gümüş ve mücevher hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Altın satış", "Gümüş satış", "Pırlanta", "Değerli taş", "Özel tasarım", "Tamir hizmeti", "Altın değişim", "Takı temizleme", "Ekspertiz", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_portfolio'
            ],

            [
                'sector_code' => 'optical_glasses', 'step' => 3, 'section' => null,
                'question_key' => 'optical_services', 'question_text' => 'Hangi optik ve gözlük hizmetlerini sağlıyorsunuz?',
                'help_text' => 'Gözlük, lens ve optik hizmet türleriniz',
                'input_type' => 'checkbox',
                'options' => '["Göz muayenesi", "Reçeteli gözlük", "Güneş gözlüğü", "Kontakt lens", "Çocuk gözlüğü", "Spor gözlüğü", "Bilgisayar gözlüğü", "Gözlük tamiri", "Lens değişimi", {"label": "Diğer", "value": "custom", "has_custom_input": true}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 5,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
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

        echo "❓ Part 3: " . count($questions) . " özel soru eklendi\n";
    }
}