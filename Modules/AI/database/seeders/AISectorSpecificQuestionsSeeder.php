<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AISectorSpecificQuestionsSeeder extends Seeder
{
    /**
     * SEKTÖRE ÖZEL SORULAR - SADELEŞTIRILMIŞ VERSİYON
     * 
     * Her sektör için 2 soru (somut ve değerli)
     * 1. Sektöre özel hizmet sorusu 
     * 2. Ana hizmet açıklama sorusu (tüm sektörler için aynı)
     */
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Yapay Zeka Sektör Özel Sorular Yükleniyor (Sadeleştirilmiş)...\n";
        
        // Mevcut sektör özel sorularını temizle (ID aralığı ile)
        AIProfileQuestion::where('id', '>=', 3000)->delete();
        
        // Ana sektörler için özel sorular
        $this->createSectorSpecificQuestions();
        
        echo "\n🎯 Tüm sektör özel sorular tamamlandı! (Her sektör için 2 soru)\n";
    }
    
    /**
     * Sektöre özel sorular oluştur - Sadeleştirilmiş
     */
    private function createSectorSpecificQuestions(): void
    {
        $questionId = 3001;
        
        // Ana sektörler ve sadeleştirilmiş sorular - Her sektör için sadece 2 soru
        $sectors = [
            'technology' => [
                'name' => 'Teknoloji & Yazılım',
                'main_service_question' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet portföyünüze özel içerik üretsin',
                'main_service_options' => [
                    'Web sitesi geliştirme',
                    'Mobil uygulama',
                    'E-ticaret sistemi', 
                    'CRM/ERP yazılımı',
                    'Veri tabanı yönetimi',
                    'Siber güvenlik',
                    'IT danışmanlığı',
                    'Yazılım bakım/destek',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Teknoloji hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'health' => [
                'name' => 'Sağlık & Tıp',
                'main_service_question' => 'Hangi sağlık hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Genel muayene',
                    'Uzman doktor muayenesi',
                    'Laboratuvar testleri',
                    'Radyoloji/görüntüleme',
                    'Ameliyat/cerrahi',
                    'Fizik tedavi',
                    'Diyet/beslenme danışmanlığı',
                    'Acil tıp',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Sağlık hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'education' => [
                'name' => 'Eğitim & Öğretim',
                'main_service_question' => 'Hangi eğitim hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka eğitim alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Okul öncesi eğitim',
                    'İlkokul eğitimi',
                    'Lise eğitimi',
                    'Üniversite eğitimi',
                    'Dil kursları',
                    'Mesleki kurslar',
                    'Online eğitim',
                    'Özel ders',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Eğitim hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'food' => [
                'name' => 'Yiyecek & İçecek',
                'main_service_question' => 'Hangi yiyecek-içecek hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka işletme türünüze özel içerik üretsin',
                'main_service_options' => [
                    'Restoran hizmeti',
                    'Cafe/kahvehane',
                    'Fast food',
                    'Catering/organizasyon',
                    'Ev yemekleri',
                    'Pastane/fırın',
                    'Gıda üretimi',
                    'Paket servis',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Yiyecek-içecek hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'retail' => [
                'name' => 'E-ticaret & Perakende',
                'main_service_question' => 'Hangi ürünleri satıyorsunuz?',
                'main_service_help' => 'Yapay Zeka ürün kategorinize özel içerik üretsin',
                'main_service_options' => [
                    'Giyim ve aksesuar',
                    'Elektronik cihazlar',
                    'Ev ve yaşam',
                    'Spor ve outdoor',
                    'Kitap ve hobi',
                    'Kozmetik ve kişisel bakım',
                    'Gıda ve içecek',
                    'Çocuk ürünleri',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Ürün kategorinizi belirtiniz'
                    ]
                ]
            ],
            'construction' => [
                'name' => 'İnşaat & Emlak',
                'main_service_question' => 'Hangi inşaat-emlak hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Konut inşaatı',
                    'Ticari bina inşaatı',
                    'Tadilat ve renovasyon',
                    'Emlak danışmanlığı',
                    'Emlak değerleme',
                    'İnşaat malzemesi',
                    'Mimari tasarım',
                    'İnşaat müteahhitliği',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'İnşaat-emlak hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'finance' => [
                'name' => 'Finans & Muhasebe',
                'main_service_question' => 'Hangi finans-muhasebe hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Muhasebe ve defter tutma',
                    'Vergi danışmanlığı',
                    'Mali müşavirlik',
                    'Bağımsız denetim',
                    'Finansal danışmanlık',
                    'Yatırım danışmanlığı',
                    'Sigorta aracılığı',
                    'Kredit değerlendirme',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Finans-muhasebe hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'web_design' => [
                'name' => 'Web Tasarım & Dijital Ajans',
                'main_service_question' => 'Hangi dijital hizmetleri sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet portföyünüze özel içerik üretsin',
                'main_service_options' => [
                    'Web sitesi tasarımı',
                    'E-ticaret sitesi',
                    'SEO ve Google optimizasyonu',
                    'Google Ads reklamları',
                    'Sosyal medya yönetimi',
                    'Logo ve kurumsal kimlik',
                    'Mobil uygulama tasarımı',
                    'Dijital pazarlama danışmanlığı',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Dijital hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'law' => [
                'name' => 'Hukuk & Avukatlık',
                'main_service_question' => 'Hangi hukuki hizmetleri sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Hukuki danışmanlık',
                    'Sözleşme hazırlama',
                    'Dava takibi',
                    'Arabuluculuk',
                    'Şirket kuruluşu',
                    'Emlak hukuku',
                    'İş hukuku',
                    'Ceza hukuku',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Hukuki hizmetinizi belirtiniz'
                    ]
                ]
            ],
            'beauty' => [
                'name' => 'Güzellik & Estetik',
                'main_service_question' => 'Hangi güzellik hizmetlerini sunuyorsunuz?',
                'main_service_help' => 'Yapay Zeka hizmet alanınıza özel içerik üretsin',
                'main_service_options' => [
                    'Saç kesimi ve şekillendirme',
                    'Cilt bakımı',
                    'Makyaj hizmeti',
                    'Manikür ve pedikür',
                    'Kaş ve kirpik bakımı',
                    'Masaj ve spa',
                    'Gelin güzelliği',
                    'Estetik işlemler',
                    [
                        'value' => 'custom',
                        'label' => 'Diğer (belirtiniz)',
                        'has_custom_input' => true,
                        'custom_placeholder' => 'Güzellik hizmetinizi belirtiniz'
                    ]
                ]
            ]
        ];
        
        foreach ($sectors as $sectorCode => $sectorData) {
            $sortOrder = 5;
            
            // 1. Sektöre özel hizmet sorusu
            $question1 = [
                'id' => $questionId++,
                'step' => 3,
                'sector_code' => $sectorCode,
                'question_key' => $sectorCode . '_specific_services',
                'question_text' => $sectorData['main_service_question'],
                'help_text' => $sectorData['main_service_help'],
                'input_type' => 'checkbox',
                'options' => json_encode($sectorData['main_service_options'], JSON_UNESCAPED_UNICODE),
                'is_required' => false,
                'is_active' => true,
                'sort_order' => $sortOrder++
            ];
            
            AIProfileQuestion::create($question1);
            
            // 2. Ana hizmet açıklama sorusu (her sektör için aynı)
            $question2 = [
                'id' => $questionId++,
                'step' => 3,
                'sector_code' => $sectorCode,
                'question_key' => $sectorCode . '_main_service_detailed',
                'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'help_text' => 'Yukarıdakilere ek olarak, genel olarak ne yapıyorsunuz?',
                'input_type' => 'textarea',
                'options' => '[]',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => $sortOrder++
            ];
            
            AIProfileQuestion::create($question2);
            
            echo "✅ {$sectorData['name']} sektörü soruları eklendi (2 soru)\n";
        }
    }
}