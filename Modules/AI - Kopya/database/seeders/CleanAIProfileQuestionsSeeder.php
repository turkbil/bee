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
            ],
            // Step 4 Questions
            [
                'id' => 7, 'sector_code' => null, 'step' => 4, 'section' => 'company_info',
                'question_key' => 'share_founder_info', 'question_text' => 'Kurucu hakkında bilgi paylaşmak ister misiniz?',
                'help_text' => 'Kurucu bilgileri AI\'ın daha kişisel ve samimi yanıtlar vermesini sağlar',
                'input_type' => 'radio',
                'options' => json_encode([
                    ['label' => 'Evet, bilgilerimi paylaşmak istiyorum', 'value' => 'evet'],
                    ['label' => 'Hayır, kurumsal kalmasını tercih ederim', 'value' => 'hayir']
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 3, 'ai_weight' => 80,
                'category' => 'founder', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'founder_permission'
            ],
            [
                'id' => 8, 'sector_code' => null, 'step' => 4, 'section' => 'founder_info',
                'question_key' => 'founder_name', 'question_text' => 'Kurucu/Müdür Adı Soyadı',
                'help_text' => 'AI size hitap ederken kullanacağı isim', 'input_type' => 'text',
                'options' => null, 'validation_rules' => json_encode(['nullable', 'string', 'max:100']),
                'is_required' => false, 'sort_order' => 10, 'priority' => 3, 'ai_weight' => 70,
                'category' => 'founder', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'founder_identity'
            ],
            [
                'id' => 9, 'sector_code' => null, 'step' => 4, 'section' => 'founder_info',
                'question_key' => 'founder_role', 'question_text' => 'Kurucu Ünvanı/Pozisyonu',
                'help_text' => 'Şirketteki rolünüz', 'input_type' => 'radio',
                'options' => json_encode([
                    ['label' => 'Kurucu', 'value' => 'founder'],
                    ['label' => 'CEO/Genel Müdür', 'value' => 'ceo'],
                    ['label' => 'Ortak/Partner', 'value' => 'partner'],
                    ['label' => 'Diğer', 'value' => 'other', 'has_custom_input' => true, 'custom_placeholder' => 'Ünvanınızı yazınız...']
                ]),
                'validation_rules' => json_encode(['nullable', 'string']),
                'is_required' => false, 'sort_order' => 20, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'founder', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'founder_role'
            ],
            [
                'id' => 10, 'sector_code' => null, 'step' => 4, 'section' => 'founder_info',
                'question_key' => 'founder_additional_info', 'question_text' => 'Kurucu hakkında eklemek istedikleriniz',
                'help_text' => 'Kurucu/sahip hakkında Yapay Zeka\'nın bilmesini istediğiniz ek bilgileri yazabilirsiniz (deneyim, uzmanlık, başarılar, özel durumlar vb.)',
                'input_type' => 'textarea', 'options' => null,
                'validation_rules' => json_encode(['nullable', 'string', 'max:1000']),
                'is_required' => false, 'sort_order' => 40, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'founder', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'founder_additional'
            ]
        ];

        // Sektör özel sorular
        $sectorQuestions = [
            // Web Design & Development
            [
                'id' => 3015, 'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_specific_services', 'question_text' => 'Hangi web hizmetlerini sunuyorsunuz?',
                'help_text' => 'Web tasarım ve geliştirme alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Kurumsal web sitesi', 'value' => 'kurumsal_web'],
                    ['label' => 'E-ticaret sitesi', 'value' => 'eticaret'],
                    ['label' => 'Blog/portfolio', 'value' => 'blog_portfolio'],
                    ['label' => 'Landing page', 'value' => 'landing_page'],
                    ['label' => 'Laravel', 'value' => 'laravel'],
                    ['label' => 'React/Vue', 'value' => 'react_vue'],
                    ['label' => 'SEO optimizasyonu', 'value' => 'seo'],
                    ['label' => 'Hosting/domain', 'value' => 'hosting'],
                    ['label' => 'Bakım/güncelleme', 'value' => 'bakim'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'company', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Health & Medical
            [
                'id' => 3020, 'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_specific_services', 'question_text' => 'Hangi sağlık hizmetlerini sunuyorsunuz?',
                'help_text' => 'Sağlık ve tıp alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Genel pratisyen', 'value' => 'general_practice'],
                    ['label' => 'Uzman doktor', 'value' => 'specialist'],
                    ['label' => 'Diş hekimliği', 'value' => 'dentistry'],
                    ['label' => 'Fizik tedavi', 'value' => 'physiotherapy'],
                    ['label' => 'Laboratuvar', 'value' => 'laboratory'],
                    ['label' => 'Radyoloji', 'value' => 'radiology'],
                    ['label' => 'Acil servis', 'value' => 'emergency'],
                    ['label' => 'Ameliyathane', 'value' => 'surgery'],
                    ['label' => 'Ebe/doğum', 'value' => 'midwifery'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer sağlık hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Education & Teaching
            [
                'id' => 3025, 'sector_code' => 'education', 'step' => 3, 'section' => null,
                'question_key' => 'education_specific_services', 'question_text' => 'Hangi eğitim hizmetlerini sunuyorsunuz?',
                'help_text' => 'Eğitim ve öğretim alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Anaokulu/kreş', 'value' => 'preschool'],
                    ['label' => 'İlkokul', 'value' => 'primary'],
                    ['label' => 'Ortaokul', 'value' => 'middle_school'],
                    ['label' => 'Lise', 'value' => 'high_school'],
                    ['label' => 'Üniversite', 'value' => 'university'],
                    ['label' => 'Dil kursu', 'value' => 'language'],
                    ['label' => 'Meslek kursu', 'value' => 'vocational'],
                    ['label' => 'Online eğitim', 'value' => 'online'],
                    ['label' => 'Özel ders', 'value' => 'tutoring'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer eğitim hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Food & Beverage
            [
                'id' => 3030, 'sector_code' => 'food', 'step' => 3, 'section' => null,
                'question_key' => 'food_specific_services', 'question_text' => 'Hangi yiyecek-içecek hizmetlerini sunuyorsunuz?',
                'help_text' => 'Yiyecek ve içecek alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Restoran', 'value' => 'restaurant'],
                    ['label' => 'Kafe', 'value' => 'cafe'],
                    ['label' => 'Fast food', 'value' => 'fastfood'],
                    ['label' => 'Pastane/fırın', 'value' => 'bakery'],
                    ['label' => 'Bar/pub', 'value' => 'bar'],
                    ['label' => 'Catering', 'value' => 'catering'],
                    ['label' => 'Gıda üretimi', 'value' => 'food_production'],
                    ['label' => 'Healthy/vegan', 'value' => 'healthy_food'],
                    ['label' => 'Toplu yemek', 'value' => 'mass_catering'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer yiyecek-içecek hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // E-commerce & Retail
            [
                'id' => 3035, 'sector_code' => 'retail', 'step' => 3, 'section' => null,
                'question_key' => 'retail_specific_services', 'question_text' => 'Hangi perakende hizmetlerini sunuyorsunuz?',
                'help_text' => 'E-ticaret ve perakende alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Online mağaza', 'value' => 'online_store'],
                    ['label' => 'Fiziki mağaza', 'value' => 'physical_store'],
                    ['label' => 'Giyim & moda', 'value' => 'fashion'],
                    ['label' => 'Elektronik & teknoloji', 'value' => 'electronics'],
                    ['label' => 'Ev & yaşam', 'value' => 'home_living'],
                    ['label' => 'Kozmetik & güzellik', 'value' => 'beauty'],
                    ['label' => 'Spor & outdoor', 'value' => 'sports'],
                    ['label' => 'Kitap & kırtasiye', 'value' => 'books'],
                    ['label' => 'Marketplace', 'value' => 'marketplace'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer perakende hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Construction & Real Estate
            [
                'id' => 3040, 'sector_code' => 'construction', 'step' => 3, 'section' => null,
                'question_key' => 'construction_specific_services', 'question_text' => 'Hangi inşaat hizmetlerini sunuyorsunuz?',
                'help_text' => 'İnşaat ve emlak alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Konut inşaatı', 'value' => 'residential'],
                    ['label' => 'Ticari inşaat', 'value' => 'commercial'],
                    ['label' => 'Altyapı inşaatı', 'value' => 'infrastructure'],
                    ['label' => 'Tadilat & renovasyon', 'value' => 'renovation'],
                    ['label' => 'İnşaat malzemesi', 'value' => 'materials'],
                    ['label' => 'Mimarlık & tasarım', 'value' => 'architecture'],
                    ['label' => 'Gayrimenkul', 'value' => 'realestate'],
                    ['label' => 'Peyzaj & bahçe', 'value' => 'landscape'],
                    ['label' => 'Proje yönetimi', 'value' => 'project_management'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer inşaat hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Finance & Accounting
            [
                'id' => 3045, 'sector_code' => 'finance', 'step' => 3, 'section' => null,
                'question_key' => 'finance_specific_services', 'question_text' => 'Hangi finans hizmetlerini sunuyorsunuz?',
                'help_text' => 'Finans ve muhasebe alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Muhasebe', 'value' => 'accounting'],
                    ['label' => 'Bankacılık', 'value' => 'banking'],
                    ['label' => 'Sigorta', 'value' => 'insurance'],
                    ['label' => 'Yatırım danışmanlığı', 'value' => 'investment'],
                    ['label' => 'Finansal danışmanlık', 'value' => 'financial_consulting'],
                    ['label' => 'Leasing & factoring', 'value' => 'leasing'],
                    ['label' => 'Kripto para', 'value' => 'crypto'],
                    ['label' => 'Forex & borsa', 'value' => 'forex'],
                    ['label' => 'Mali müşavirlik', 'value' => 'tax_consulting'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer finans hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
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
            ],
            // Art & Design
            [
                'id' => 3050, 'sector_code' => 'art_design', 'step' => 3, 'section' => null,
                'question_key' => 'art_design_specific_services', 'question_text' => 'Hangi sanat & tasarım hizmetlerini sunuyorsunuz?',
                'help_text' => 'Sanat ve tasarım alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Grafik tasarım', 'value' => 'graphic_design'],
                    ['label' => 'Web tasarım', 'value' => 'web_design'],
                    ['label' => 'İç mimarlık', 'value' => 'interior_design'],
                    ['label' => 'Fotoğrafçılık', 'value' => 'photography'],
                    ['label' => 'Video prodüksiyon', 'value' => 'video_production'],
                    ['label' => 'Müzik prodüksiyon', 'value' => 'music_production'],
                    ['label' => 'El sanatları', 'value' => 'handcraft'],
                    ['label' => 'Sanat galerisi', 'value' => 'art_gallery'],
                    ['label' => 'Logo tasarım', 'value' => 'logo_design'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer sanat & tasarım hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Sports & Fitness
            [
                'id' => 3055, 'sector_code' => 'sports', 'step' => 3, 'section' => null,
                'question_key' => 'sports_specific_services', 'question_text' => 'Hangi spor & fitness hizmetlerini sunuyorsunuz?',
                'help_text' => 'Spor ve fitness alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Fitness & spor salonu', 'value' => 'fitness_gym'],
                    ['label' => 'Pilates & yoga', 'value' => 'pilates_yoga'],
                    ['label' => 'Dövüş sanatları', 'value' => 'martial_arts'],
                    ['label' => 'Su sporları & yüzme', 'value' => 'swimming'],
                    ['label' => 'Takım sporları', 'value' => 'team_sports'],
                    ['label' => 'Kişisel antrenörlük', 'value' => 'personal_training'],
                    ['label' => 'Outdoor & macera sporları', 'value' => 'outdoor_sports'],
                    ['label' => 'Dans & hareket', 'value' => 'dance'],
                    ['label' => 'Spor akademisi', 'value' => 'sports_academy'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer spor & fitness hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Automotive
            [
                'id' => 3060, 'sector_code' => 'automotive', 'step' => 3, 'section' => null,
                'question_key' => 'automotive_specific_services', 'question_text' => 'Hangi otomotiv hizmetlerini sunuyorsunuz?',
                'help_text' => 'Otomotiv alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Otomobil galeri & bayi', 'value' => 'auto_dealer'],
                    ['label' => 'Otomotiv servis & tamirci', 'value' => 'auto_service'],
                    ['label' => 'Yedek parça & aksesuar', 'value' => 'spare_parts'],
                    ['label' => 'Rent a car & araç kiralama', 'value' => 'rent_car'],
                    ['label' => 'Lastik & jant', 'value' => 'tire_rim'],
                    ['label' => 'Oto yıkama & detailing', 'value' => 'car_wash'],
                    ['label' => 'Kurtarma & çekici', 'value' => 'car_rescue'],
                    ['label' => 'Sürücü kursu & ehliyet', 'value' => 'driving_school'],
                    ['label' => 'Oto ekspertiz', 'value' => 'auto_expertise'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer otomotiv hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => null
            ],
            // Legal & Consulting
            [
                'id' => 3065, 'sector_code' => 'legal', 'step' => 3, 'section' => null,
                'question_key' => 'legal_specific_services', 'question_text' => 'Hangi hukuk & danışmanlık hizmetlerini sunuyorsunuz?',
                'help_text' => 'Hukuk ve danışmanlık alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Avukatlık & hukuk bürosu', 'value' => 'law_office'],
                    ['label' => 'Kurumsal hukuk & ticaret hukuku', 'value' => 'corporate_law'],
                    ['label' => 'Emlak hukuku & gayrimenkul', 'value' => 'real_estate_law'],
                    ['label' => 'Aile hukuku & boşanma', 'value' => 'family_law'],
                    ['label' => 'İş hukuku & işçi hakları', 'value' => 'labor_law'],
                    ['label' => 'Bilişim hukuku & kişisel veri', 'value' => 'cyber_law'],
                    ['label' => 'Trafik hukuku & sigorta', 'value' => 'traffic_law'],
                    ['label' => 'İdare hukuku & kamu', 'value' => 'administrative_law'],
                    ['label' => 'Ceza hukuku', 'value' => 'criminal_law'],
                    ['label' => 'Diğer', 'value' => 'custom', 'has_custom_input' => true, 'custom_placeholder' => 'Diğer hukuk & danışmanlık hizmetinizi belirtiniz...']
                ]),
                'validation_rules' => null, 'is_required' => false, 'sort_order' => 5, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
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