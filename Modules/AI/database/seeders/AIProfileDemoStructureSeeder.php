<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileDemoStructureSeeder extends Seeder
{
    /**
     * AI PROFILE DEMO YAPISINI GERÇEK SİSTEME UYGULAMAK
     * 
     * Demo sayfasındaki soruları aynen AI Profile Wizard'a aktarır
     * Step yapısı: 1=Sektör, 2=İş Bilgileri, 3=Marka, 4=Kurucu, 5=AI Ayarları
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 AI Profile Demo Yapısı - Real Sisteme Aktarılıyor...\n";
        
        // Önce tüm questions'ları sil
        AIProfileQuestion::truncate();
        
        $questions = $this->getDemoQuestions();
        $questionCount = 0;
        
        foreach ($questions as $question) {
            AIProfileQuestion::create($question);
            $questionCount++;
        }
        
        echo "\n🎉 {$questionCount} demo sorusu gerçek sisteme aktarıldı!\n";
    }
    
    private function getDemoQuestions(): array
    {
        return [
            // ========================================
            // STEP 1: SEKTÖR SEÇİMİ
            // ========================================
            [
                'step' => 1,
                'section' => 'sector',
                'question_key' => 'sector',
                'question_text' => '🏪 Sektörünüzü Seçin',
                'help_text' => 'Hangi sektörde faaliyet gösteriyorsunuz?',
                'input_type' => 'select',
                'is_required' => true,
                'sort_order' => 10,
                'ai_priority' => 1,
                'always_include' => true,
                'context_category' => 'business'
            ],

            // ========================================
            // STEP 2: İŞ BİLGİLERİ
            // ========================================
            [
                'step' => 2,
                'section' => 'company_info',
                'question_key' => 'brand_name',
                'question_text' => '🏷️ Firma/Marka Adınız',
                'help_text' => 'Firma adınızı tam olarak yazın',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 10,
                'ai_priority' => 1,
                'always_include' => true,
                'context_category' => 'business'
            ],
            [
                'step' => 2,
                'section' => 'company_info',
                'question_key' => 'city',
                'question_text' => '📍 Şehriniz',
                'help_text' => 'Hangi şehirde hizmet veriyorsunuz?',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 20,
                'ai_priority' => 4,
                'always_include' => false,
                'context_category' => 'location'
            ],
            [
                'step' => 2,
                'section' => 'company_info',
                'question_key' => 'main_service',
                'question_text' => '⚙️ Ana Hizmetiniz',
                'help_text' => 'Ne iş yapıyorsunuz? Ana hizmetinizi kısaca belirtin',
                'input_type' => 'text',
                'is_required' => true,
                'sort_order' => 30,
                'ai_priority' => 1,
                'always_include' => true,
                'context_category' => 'business'
            ],
            [
                'step' => 2,
                'section' => 'company_info',
                'question_key' => 'employee_count',
                'question_text' => '👥 Çalışan Sayınız',
                'help_text' => 'Kaç kişilik bir ekibiniz var?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'solo', 'label' => '🤵 Sadece ben', 'description' => 'Tek kişi'],
                    ['value' => 'small', 'label' => '👥 2-5 kişi', 'description' => 'Küçük ekip'],
                    ['value' => 'medium', 'label' => '🏢 6-20 kişi', 'description' => 'Orta ölçekli'],
                    ['value' => 'large', 'label' => '🏭 21-50 kişi', 'description' => 'Büyük ekip'],
                    ['value' => 'corporate', 'label' => '🏗️ 50+ kişi', 'description' => 'Kurumsal şirket'],
                    ['value' => 'other', 'label' => '📝 Diğer', 'has_custom_input' => true, 'custom_placeholder' => 'Çalışan sayınızı belirtin...']
                ],
                'is_required' => false,
                'sort_order' => 40,
                'ai_priority' => 3,
                'always_include' => false,
                'context_category' => 'business'
            ],
            [
                'step' => 2,
                'section' => 'company_info',
                'question_key' => 'target_audience',
                'question_text' => '👥 Hedef Kitleniz Kimdir?',
                'help_text' => 'Hangi müşteri grubuna hizmet veriyorsunuz?',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'b2c-individual', 'label' => '👤 Bireysel Müşteriler', 'description' => 'Kişisel ihtiyaçlar'],
                    ['value' => 'b2c-family', 'label' => '👨‍👩‍👧‍👦 Aileler', 'description' => 'Aile ihtiyaçları'],
                    ['value' => 'b2c-young', 'label' => '🧑‍🎓 Gençler (18-30)', 'description' => 'Genç demografik'],
                    ['value' => 'b2c-middle', 'label' => '👔 Orta Yaş (30-50)', 'description' => 'Orta yaş demografik'],
                    ['value' => 'b2c-senior', 'label' => '👴 Yaşlılar (50+)', 'description' => 'Senior demografik'],
                    ['value' => 'b2b-sme', 'label' => '🏪 KOBİ\'ler', 'description' => 'Küçük-orta işletmeler'],
                    ['value' => 'b2b-corporate', 'label' => '🏢 Büyük Şirketler', 'description' => 'Kurumsal firmalar'],
                    ['value' => 'b2b-startup', 'label' => '🚀 Startup\'lar', 'description' => 'Yeni girişimler'],
                    ['value' => 'economy', 'label' => '💰 Ekonomik Segment', 'description' => 'Fiyat odaklı'],
                    ['value' => 'premium', 'label' => '💎 Premium Segment', 'description' => 'Kalite odaklı'],
                    ['value' => 'other', 'label' => '⚪ Diğer Hedef Kitle', 'has_custom_input' => true, 'custom_placeholder' => 'Özel hedef kitlenizi tanımlayın...']
                ],
                'is_required' => true,
                'sort_order' => 50,
                'ai_priority' => 2,
                'always_include' => true,
                'context_category' => 'business'
            ],

            // ========================================
            // STEP 3: MARKA KİMLİĞİ
            // ========================================
            [
                'step' => 3,
                'section' => 'brand_details',
                'question_key' => 'brand_personality',
                'question_text' => '🎭 Marka Kişiliğiniz Nasıl?',
                'help_text' => 'Markanızın karakterini belirleyin (Çoklu seçim)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'professional', 'label' => '💼 Profesyonel', 'description' => 'Ciddi, güvenilir, kurumsal'],
                    ['value' => 'friendly', 'label' => '😊 Samimi', 'description' => 'Dostane, yakın, sıcak'],
                    ['value' => 'innovative', 'label' => '🚀 Yenilikçi', 'description' => 'Çağdaş, öncü, yaratıcı'],
                    ['value' => 'reliable', 'label' => '🛡️ Güvenilir', 'description' => 'Sağlam, istikrarlı, emin'],
                    ['value' => 'energetic', 'label' => '⚡ Dinamik', 'description' => 'Hızlı, aktif, canlı'],
                    ['value' => 'expert', 'label' => '🎓 Uzman', 'description' => 'Bilgili, deneyimli, otorite'],
                    ['value' => 'local', 'label' => '🏡 Yerel', 'description' => 'Mahalli, bölgesel, yakın'],
                    ['value' => 'luxury', 'label' => '💎 Premium', 'description' => 'Lüks, kaliteli, seçkin'],
                    ['value' => 'affordable', 'label' => '💰 Uygun', 'description' => 'Ekonomik, erişilebilir'],
                    ['value' => 'creative', 'label' => '🎨 Yaratıcı', 'description' => 'Sanatsal, özgün, estetik'],
                    ['value' => 'traditional', 'label' => '🏛️ Geleneksel', 'description' => 'Köklü, klasik, deneyimli'],
                    ['value' => 'modern', 'label' => '📱 Modern', 'description' => 'Çağdaş, teknolojik, güncel'],
                    ['value' => 'other', 'label' => '⚪ Diğer Kişilik', 'has_custom_input' => true, 'custom_placeholder' => 'Markanızın özel kişilik özelliğini tanımlayın...']
                ],
                'is_required' => true,
                'sort_order' => 10,
                'ai_priority' => 1,
                'always_include' => true,
                'context_category' => 'brand'
            ],
            [
                'step' => 3,
                'section' => 'brand_details',
                'question_key' => 'competitive_advantage',
                'question_text' => '⭐ Rekabet Avantajınız Nedir?',
                'help_text' => 'Rakiplerinizden farkınız ne? (Çoklu seçim)',
                'input_type' => 'checkbox',
                'options' => [
                    ['value' => 'price', 'label' => '💰 Fiyat Avantajı', 'description' => 'Uygun fiyatlı, ekonomik'],
                    ['value' => 'quality', 'label' => '⭐ Yüksek Kalite', 'description' => 'Premium malzeme/hizmet'],
                    ['value' => 'speed', 'label' => '⚡ Hız', 'description' => 'Hızlı teslimat/hizmet'],
                    ['value' => 'experience', 'label' => '🎯 Deneyim', 'description' => 'Uzun yıllık tecrübe'],
                    ['value' => 'innovation', 'label' => '🚀 Yenilik', 'description' => 'En son teknoloji'],
                    ['value' => 'service', 'label' => '🤝 Müşteri Hizmeti', 'description' => 'Üstün hizmet kalitesi'],
                    ['value' => 'locality', 'label' => '🏡 Yerellik', 'description' => 'Bölgesel yakınlık'],
                    ['value' => 'expertise', 'label' => '🎓 Uzmanlık', 'description' => 'Alanda uzmanlaşma'],
                    ['value' => 'flexibility', 'label' => '🔄 Esneklik', 'description' => 'Özel çözümler'],
                    ['value' => 'trust', 'label' => '🛡️ Güven', 'description' => 'Güvenilir marka'],
                    ['value' => 'other', 'label' => '⚪ Diğer Avantaj', 'has_custom_input' => true, 'custom_placeholder' => 'Özel rekabet avantajınızı belirtin...']
                ],
                'is_required' => false,
                'sort_order' => 20,
                'ai_priority' => 2,
                'always_include' => true,
                'context_category' => 'brand'
            ],
            [
                'step' => 3,
                'section' => 'brand_details',
                'question_key' => 'additional_info',
                'question_text' => '✨ Markanızla İlgili Eklemek İstedikleriniz',
                'help_text' => 'Referanslar, başarı hikayeleri, ödüller, özel projeler vb.',
                'input_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 30,
                'ai_priority' => 3,
                'always_include' => false,
                'context_category' => 'brand'
            ]
        ];
    }
}