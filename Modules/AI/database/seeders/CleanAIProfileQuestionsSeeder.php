<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class CleanAIProfileQuestionsSeeder extends Seeder
{
    /**
     * TEMİZ AI PROFILE QUESTIONS SEEDER
     * SQL export'undan oluşturulmuş temiz ve optimize seeder
     * Genel + Sektör özel sorular dahil
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Temiz AI Profile Questions yükleniyor...\n";

        // Mevcut soruları temizle
        AIProfileQuestion::truncate();
        
        // Genel sorular (tüm sektörler için)
        $generalQuestions = [
            [
                'id' => 1, 'sector_code' => null, 'step' => 1, 'section' => null,
                'question_key' => 'sector_selection', 'question_text' => 'Sektörünüzü seçin',
                'help_text' => 'İşletmenizin faaliyet gösterdiği ana sektörü seçerek size özel AI profili oluşturalım',
                'input_type' => 'select',
                'options' => json_encode([
                    'source' => 'ai_profile_sectors',
                    'instruction' => 'Aşağıdaki sektörlerden birini seçin. Seçiminize göre size özel sorular yüklenecektir.',
                    'placeholder' => 'Sektörünüzü seçin...',
                    'value_field' => 'id',
                    'display_field' => 'name'
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 3, 'ai_weight' => 100,
                'category' => 'sector', 'ai_priority' => 1, 'always_include' => true,
                'context_category' => 'business_foundation'
            ],
            [
                'id' => 2, 'sector_code' => null, 'step' => 2, 'section' => null,
                'question_key' => 'brand_name', 'question_text' => 'Marka/Firma Adı',
                'help_text' => 'Resmi firma adınızı yazın', 'input_type' => 'text',
                'options' => null, 'validation_rules' => json_encode(['required', 'string', 'min:2', 'max:100']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 3, 'ai_weight' => 150,
                'category' => 'company', 'ai_priority' => 1, 'always_include' => true,
                'context_category' => 'brand_identity'
            ],
            [
                'id' => 3, 'sector_code' => null, 'step' => 2, 'section' => null,
                'question_key' => 'city', 'question_text' => 'Hangi şehirdesiniz?',
                'help_text' => 'Ana faaliyet şehrinizi belirtin', 'input_type' => 'text',
                'options' => null, 'validation_rules' => json_encode(['required', 'string', 'min:2', 'max:50']),
                'is_required' => true, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 40,
                'category' => 'company', 'ai_priority' => 4, 'always_include' => false,
                'context_category' => 'location_info'
            ],
            [
                'id' => 4, 'sector_code' => null, 'step' => 2, 'section' => null,
                'question_key' => 'business_start_year', 'question_text' => 'Hangi yıldan beri bu işi yapıyorsunuz?',
                'help_text' => 'İşe başladığınız yılı yazın veya deneyim sürenizi belirtin (Örn: 2020, 2015 yılından beri, 10+ yıllık deneyim, aile işi vb.)',
                'input_type' => 'text', 'options' => null,
                'validation_rules' => json_encode(['required', 'string', 'min:4', 'max:50']),
                'is_required' => true, 'sort_order' => 3, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'experience_foundation'
            ],
            [
                'id' => 5, 'sector_code' => null, 'step' => 3, 'section' => null,
                'question_key' => 'main_business_activities', 'question_text' => 'Yaptığınız ana iş kolları nelerdir?',
                'help_text' => 'İşletmenizin sunduğu hizmetleri veya ürünleri detaylı olarak açıklayın',
                'input_type' => 'textarea', 'options' => null, 'validation_rules' => null,
                'is_required' => true, 'sort_order' => 1, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            [
                'id' => 6, 'sector_code' => null, 'step' => 3, 'section' => null,
                'question_key' => 'target_customers', 'question_text' => 'Ana müşteri kitleniz kimler?',
                'help_text' => 'Öncelikli hedef müşterilerinizi seçin (çoklu seçim)', 'input_type' => 'checkbox',
                'options' => json_encode([
                    'diger' => [
                        'label' => 'Diğer (belirtiniz)', 'value' => 'diger',
                        'has_custom_input' => true, 'custom_placeholder' => 'Özel müşteri kitlenizi belirtiniz...'
                    ],
                    'buyuk_sirketler' => ['label' => 'Büyük şirketler', 'value' => 'buyuk_sirketler'],
                    'kucuk_isletmeler' => ['label' => 'Küçük işletmeler', 'value' => 'kucuk_isletmeler'],
                    'bireysel_musteriler' => ['label' => 'Bireysel müşteriler', 'value' => 'bireysel_musteriler']
                ]),
                'validation_rules' => null, 'is_required' => true, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            [
                'id' => 12, 'sector_code' => null, 'step' => 5, 'section' => null,
                'question_key' => 'brand_character', 'question_text' => 'Marka karakteriniz nasıl?',
                'help_text' => 'Markanızın benzersiz kişilik özelliklerini seçin', 'input_type' => 'checkbox',
                'options' => json_encode([
                    'diger' => [
                        'label' => 'Diğer (belirtiniz)', 'value' => 'diger',
                        'has_custom_input' => true, 'custom_placeholder' => 'Özel marka karakterinizi belirtiniz...'
                    ],
                    'ciddi_kurumsal' => ['label' => 'Ciddi ve kurumsal', 'value' => 'ciddi_kurumsal'],
                    'sakin_temkinli' => ['label' => 'Sakin ve temkinli', 'value' => 'sakin_temkinli'],
                    'samimi_dostane' => ['label' => 'Samimi ve dostane', 'value' => 'samimi_dostane'],
                    'yenilikci_cesur' => ['label' => 'Yenilikçi ve cesur', 'value' => 'yenilikci_cesur'],
                    'geleneksel_koklu' => ['label' => 'Geleneksel ve köklü', 'value' => 'geleneksel_koklu'],
                    'enerjik_heyecanli' => ['label' => 'Enerjik ve heyecanlı', 'value' => 'enerjik_heyecanli'],
                    'eglenceli_yaratici' => ['label' => 'Eğlenceli ve yaratıcı', 'value' => 'eglenceli_yaratici'],
                    'pratik_cozum_odakli' => ['label' => 'Pratik ve çözüm odaklı', 'value' => 'pratik_cozum_odakli']
                ]),
                'validation_rules' => json_encode(['required', 'array', 'min:1']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 3, 'ai_weight' => 120,
                'category' => 'ai', 'ai_priority' => 1, 'always_include' => true,
                'context_category' => 'ai_personality'
            ],
            [
                'id' => 13, 'sector_code' => null, 'step' => 5, 'section' => null,
                'question_key' => 'writing_style', 'question_text' => 'Genel yazım tavırınız nasıl olsun?',
                'help_text' => 'Web sitesi, blog, sosyal medya - her yerde kullanılacak genel dil tavrı',
                'input_type' => 'checkbox',
                'options' => json_encode([
                    'diger' => [
                        'label' => 'Diğer (belirtiniz)', 'value' => 'diger',
                        'has_custom_input' => true, 'custom_placeholder' => 'Özel yazım tarzınızı belirtiniz...'
                    ],
                    'kisa_net' => ['label' => 'Kısa ve net ifadeler', 'value' => 'kisa_net'],
                    'gunluk_konusma' => ['label' => 'Günlük konuşma tarzında', 'value' => 'gunluk_konusma'],
                    'sade_anlasilir' => ['label' => 'Sade ve anlaşılır dil', 'value' => 'sade_anlasilir'],
                    'teknik_bilimsel' => ['label' => 'Teknik ve bilimsel yaklaşım', 'value' => 'teknik_bilimsel'],
                    'detayli_kapsamli' => ['label' => 'Detaylı ve kapsamlı anlatım', 'value' => 'detayli_kapsamli'],
                    'formal_profesyonel' => ['label' => 'Formal ve profesyonel', 'value' => 'formal_profesyonel'],
                    'duygusal_etkileyici' => ['label' => 'Duygusal ve etkileyici', 'value' => 'duygusal_etkileyici']
                ]),
                'validation_rules' => json_encode(['required', 'array', 'min:1']),
                'is_required' => true, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 110,
                'category' => 'ai', 'ai_priority' => 1, 'always_include' => true,
                'context_category' => 'ai_communication'
            ]
        ];

        // Sektör özel sorular
        $sectorQuestions = [
            // Web Design
            [
                'id' => 3015, 'sector_code' => 'web_design', 'step' => 3, 'section' => null,
                'question_key' => 'web_design_specific_services', 'question_text' => 'Hangi dijital hizmetleri sunuyorsunuz?',
                'help_text' => 'Yapay Zeka hizmet portföyünüze özel içerik üretsin', 'input_type' => 'checkbox',
                'options' => json_encode([
                    'Web sitesi tasarımı', 'E-ticaret sitesi', 'SEO ve Google optimizasyonu',
                    'Google Ads reklamları', 'Sosyal medya yönetimi', 'Logo ve kurumsal kimlik',
                    'Mobil uygulama tasarımı', 'Dijital pazarlama danışmanlığı',
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Dijital hizmetinizi belirtiniz']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            [
                'id' => 3016, 'sector_code' => 'web_design', 'step' => 3, 'section' => null,
                'question_key' => 'web_design_main_service_detailed', 'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'help_text' => 'Yukarıdakilere ek olarak, genel olarak ne yapıyorsunuz?', 'input_type' => 'textarea',
                'options' => json_encode([]), 'validation_rules' => null,
                'is_required' => false, 'sort_order' => 6, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Technology
            [
                'id' => 3001, 'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'technology_specific_services', 'question_text' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'help_text' => 'Yapay Zeka hizmet portföyünüze özel içerik üretsin', 'input_type' => 'checkbox',
                'options' => json_encode([
                    'Web sitesi geliştirme', 'Mobil uygulama', 'E-ticaret sistemi',
                    'CRM/ERP yazılımı', 'Veri tabanı yönetimi', 'Siber güvenlik',
                    'IT danışmanlığı', 'Yazılım bakım/destek',
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Teknoloji hizmetinizi belirtiniz']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            [
                'id' => 3002, 'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'technology_main_service_detailed', 'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'help_text' => 'Yukarıdakilere ek olarak, genel olarak ne yapıyorsunuz?', 'input_type' => 'textarea',
                'options' => json_encode([]), 'validation_rules' => null,
                'is_required' => false, 'sort_order' => 6, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ]
        ];

        // Tüm soruları oluştur
        $allQuestions = array_merge($generalQuestions, $sectorQuestions);
        
        foreach ($allQuestions as $question) {
            AIProfileQuestion::create([
                'id' => $question['id'],
                'sector_code' => $question['sector_code'],
                'step' => $question['step'],
                'section' => $question['section'],
                'question_key' => $question['question_key'],
                'question_text' => $question['question_text'],
                'help_text' => $question['help_text'],
                'input_type' => $question['input_type'],
                'options' => $question['options'],
                'validation_rules' => $question['validation_rules'],
                'depends_on' => $question['depends_on'] ?? null,
                'show_if' => $question['show_if'] ?? null,
                'is_required' => $question['is_required'],
                'is_active' => true,
                'sort_order' => $question['sort_order'],
                'priority' => $question['priority'],
                'ai_weight' => $question['ai_weight'],
                'category' => $question['category'],
                'ai_priority' => $question['ai_priority'],
                'always_include' => $question['always_include'],
                'context_category' => $question['context_category']
            ]);
        }

        echo "✅ Temiz AI Profile Questions yüklendi!\n";
        echo "📋 Genel sorular: " . count($generalQuestions) . "\n";
        echo "🎯 Sektör özel sorular: " . count($sectorQuestions) . "\n";
        echo "📊 Toplam soru: " . count($allQuestions) . "\n";
    }
}