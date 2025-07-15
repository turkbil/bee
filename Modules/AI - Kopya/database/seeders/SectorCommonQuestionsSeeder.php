<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorCommonQuestionsSeeder extends Seeder
{
    /**
     * SECTOR COMMON QUESTIONS SEEDER
     * Tüm sektörlere sorulacak ortak hizmet soruları
     * Genel işletme bilgileri ve hizmet kapsamı
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörel ortak sorular yükleniyor...\n";

        // Ortak sektörel soruları temizle (ID 5000-5999 arası)
        DB::table('ai_profile_questions')->whereBetween('id', [5000, 5999])->delete();
        
        $commonQuestions = [
            // ORTAK SORULAR - TÜM SEKTÖRLER İÇİN
            
            // Hizmet Alanları
            [
                'id' => 5001, 'sector_code' => null, 'step' => 3, 'section' => 'hizmet_alanlari',
                'question_key' => 'service_areas', 'question_text' => 'Hangi şehir/bölgelerde hizmet veriyorsunuz?',
                'help_text' => 'Hizmet verdiğiniz coğrafi alanları belirtin',
                'input_type' => 'checkbox',
                'options' => '["Aynı şehir", "İl içi tüm ilçeler", "Komşu şehirler", "Bölgesel hizmet", "Türkiye geneli", "Uluslararası", "Online/uzaktan", "Sadece merkez", "Ev/işyeri ziyareti", "Mobil hizmet", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Hizmet alanınızı belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 10,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'service_scope'
            ],

            // Çalışma Saatleri
            [
                'id' => 5002, 'sector_code' => null, 'step' => 3, 'section' => 'calisma_saatleri',
                'question_key' => 'working_hours', 'question_text' => 'Çalışma saatleriniz nasıl?',
                'help_text' => 'Müşterilerinizin sizi ne zaman bulabileceğini belirtin',
                'input_type' => 'checkbox',
                'options' => '["Mesai saatleri (09:00-18:00)", "Uzun mesai (08:00-20:00)", "Akşam geç saatlere kadar", "Hafta sonu açık", "7/24 hizmet", "Randevulu çalışma", "Esnek saatler", "Sadece hafta içi", "Vardiya sistemi", "Mevsimlik çalışma", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Çalışma saatinizi belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 11,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'operational_info'
            ],

            // Ödeme Seçenekleri
            [
                'id' => 5003, 'sector_code' => null, 'step' => 3, 'section' => 'odeme_secenekleri',
                'question_key' => 'payment_options', 'question_text' => 'Hangi ödeme yöntemlerini kabul ediyorsunuz?',
                'help_text' => 'Müşterilerinizin ödeme yapabileceği yöntemler',
                'input_type' => 'checkbox',
                'options' => '["Nakit", "Kredi kartı", "Banka kartı", "Havale/EFT", "Çek", "Taksitli ödeme", "Online ödeme", "Kapıda ödeme", "Mobil ödeme", "Kripto para", "Senet", "Vadeli ödeme", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Ödeme yönteminizi belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 12,
                'priority' => 2, 'ai_weight' => 70, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'payment_info'
            ],

            // Özel Hizmetler
            [
                'id' => 5004, 'sector_code' => null, 'step' => 3, 'section' => 'ozel_hizmetler',
                'question_key' => 'special_services', 'question_text' => 'Sunduğunuz özel hizmetler nelerdir?',
                'help_text' => 'Rakiplerinizden farklı kılan özel hizmetleriniz',
                'input_type' => 'checkbox',
                'options' => '["Ücretsiz danışmanlık", "Ücretsiz keşif", "Garanti hizmeti", "Servis sonrası takip", "Acil hizmet", "Ev/işyeri ziyareti", "Online destek", "24/7 müşteri hizmetleri", "Özel tasarım", "Kişiye özel çözüm", "Eğitim/seminer", "Teknik destek", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Özel hizmetinizi belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 13,
                'priority' => 2, 'ai_weight' => 80, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'special_features'
            ],

            // Müşteri Profili
            [
                'id' => 5005, 'sector_code' => null, 'step' => 3, 'section' => 'musteri_profili',
                'question_key' => 'customer_profile', 'question_text' => 'Müşteri profiliniz nasıl?',
                'help_text' => 'Genellikle hangi tür müşterilerle çalışıyorsunuz',
                'input_type' => 'checkbox',
                'options' => '["Bireysel müşteriler", "Aile müşterileri", "Genç müşteriler", "Orta yaş müşteriler", "Yaşlı müşteriler", "Şirket müşterileri", "KOBİ müşterileri", "Büyük firmalar", "Kurumsal müşteriler", "Yabancı müşteriler", "Turist müşteriler", "Düzenli müşteriler", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Müşteri profilinizi belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 14,
                'priority' => 2, 'ai_weight' => 75, 'category' => 'company',
                'ai_priority' => 2, 'always_include' => 0, 'context_category' => 'customer_base'
            ],

            // Deneyim ve Uzmanlık
            [
                'id' => 5006, 'sector_code' => null, 'step' => 3, 'section' => 'deneyim_uzmanlik',
                'question_key' => 'expertise_areas', 'question_text' => 'Öne çıkan uzmanlık alanlarınız nelerdir?',
                'help_text' => 'En iyi olduğunuz ve uzmanlaştığınız konular',
                'input_type' => 'checkbox',
                'options' => '["Yılların deneyimi", "Teknik uzmanlık", "Özel sertifikalar", "Akademik eğitim", "Sürekli eğitim", "Yenilikleri takip", "Kalite odaklı", "Hız ve verimlilik", "Müşteri memnuniyeti", "Problem çözme", "Yaratıcı çözümler", "Güvenilirlik", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Uzmanlık alanınızı belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 15,
                'priority' => 2, 'ai_weight' => 85, 'category' => 'company',
                'ai_priority' => 1, 'always_include' => 0, 'context_category' => 'expertise'
            ],

            // İş Kapasitesi
            [
                'id' => 5007, 'sector_code' => null, 'step' => 3, 'section' => 'is_kapasitesi',
                'question_key' => 'business_capacity', 'question_text' => 'İş kapasiteniz nasıl?',
                'help_text' => 'Aynı anda ne kadar iş/müşteri ile ilgilenebiliyorsunuz',
                'input_type' => 'checkbox',
                'options' => '["Küçük ölçekli işler", "Orta ölçekli projeler", "Büyük projeler", "Çoklu proje yönetimi", "Hızlı teslimat", "Kaliteli işçilik", "Detaylı çalışma", "Toplu siparişler", "Bireysel hizmet", "Seri üretim", "Özel tasarım", "Standart ürünler", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "İş kapasitenizi belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 16,
                'priority' => 3, 'ai_weight' => 65, 'category' => 'company',
                'ai_priority' => 3, 'always_include' => 0, 'context_category' => 'capacity'
            ],

            // İletişim Kanalları
            [
                'id' => 5008, 'sector_code' => null, 'step' => 3, 'section' => 'iletisim_kanallari',
                'question_key' => 'communication_channels', 'question_text' => 'Müşterilerinizle nasıl iletişim kuruyorsunuz?',
                'help_text' => 'Kullandığınız iletişim yöntemleri',
                'input_type' => 'checkbox',
                'options' => '["Telefon", "WhatsApp", "E-mail", "SMS", "Sosyal medya", "Web sitesi", "Online form", "Canlı destek", "Video görüşme", "Yüz yüze", "Mağaza ziyareti", "Ev/işyeri ziyareti", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "İletişim kanalınızı belirtiniz"}]',
                'validation_rules' => null, 'depends_on' => null, 'show_if' => null,
                'is_required' => 0, 'is_active' => 1, 'sort_order' => 17,
                'priority' => 3, 'ai_weight' => 60, 'category' => 'company',
                'ai_priority' => 3, 'always_include' => 0, 'context_category' => 'communication'
            ]
        ];

        // Soruları veritabanına ekle
        foreach ($commonQuestions as $question) {
            DB::table('ai_profile_questions')->insert(array_merge($question, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        echo "✅ " . count($commonQuestions) . " sektörel ortak soru eklendi!\n";
        echo "📋 Ortak sorular tüm sektörler için geçerli (ID 5000-5999)\n";
    }
}