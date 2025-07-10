<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AIProfileSectoralQuestionsSeeder extends Seeder
{
    /**
     * AI PROFİL SEKTÖREL SORULAR - DEMO'DAN AKTARIM
     * 
     * Demo sistemindeki sektörel sorular gerçek AI Profile sistemine aktarılır.
     * Her sektörün kendine özel soruları olur.
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 AI Profile Sektörel Sorular - Demo'dan Gerçek Sisteme Aktarım...\n";
        
        // Önce sektörel soruları sil
        AIProfileQuestion::where('step', 2)->where('section', 'like', 'sector_%')->delete();
        
        // Bu seeder'da oluşturacağımız tüm question_key'leri de sil (güvenlik için)
        $allQuestionKeys = [];
        $allQuestions = $this->getSectoralQuestions();
        foreach ($allQuestions as $sectorQuestions) {
            foreach ($sectorQuestions as $question) {
                $allQuestionKeys[] = $question['key'];
            }
        }
        AIProfileQuestion::whereIn('question_key', $allQuestionKeys)->delete();
        
        $questions = $this->getSectoralQuestions();
        $questionCount = 0;
        
        foreach ($questions as $sectorCode => $sectorQuestions) {
            echo "📋 Sektör: {$sectorCode}\n";
            
            foreach ($sectorQuestions as $index => $question) {
                // Question key duplicate kontrolü
                $existingQuestion = AIProfileQuestion::where('question_key', $question['key'])->first();
                if (!$existingQuestion) {
                    AIProfileQuestion::create([
                        'step' => 2, // Step 2'de görünecek
                        'section' => "sector_{$sectorCode}",
                        'question_key' => $question['key'],
                        'question_text' => $question['title'],
                        'help_text' => "Sektörünüze özel bu bilgiyi belirtin",
                        'input_type' => $question['type'],
                        'options' => json_encode($question['options']),
                        'is_required' => false,
                        'sort_order' => ($index + 1) * 10,
                        'sector_code' => $sectorCode,
                        'ai_priority' => 3, // Normal priority
                        'always_include' => false,
                        'context_category' => 'sector_specific'
                    ]);
                    
                    $questionCount++;
                } else {
                    echo "   ⚠️ Question key '{$question['key']}' zaten mevcut, atlanıyor...\n";
                }
            }
            
            echo "   → " . count($sectorQuestions) . " soru eklendi\n";
        }
        
        echo "\n🎉 Toplam {$questionCount} sektörel soru eklendi!\n";
    }
    
    private function getSectoralQuestions(): array
    {
        return [
            'technology' => [
                [
                    'key' => 'tech_services',
                    'title' => '💻 Hangi teknoloji hizmetlerini sunuyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'web_development', 'label' => 'Web Geliştirme'],
                        ['value' => 'mobile_apps', 'label' => 'Mobil Uygulamalar'],
                        ['value' => 'system_integration', 'label' => 'Sistem Entegrasyonu'],
                        ['value' => 'cloud_services', 'label' => 'Bulut Hizmetleri'],
                        ['value' => 'cybersecurity', 'label' => 'Siber Güvenlik'],
                        ['value' => 'data_analytics', 'label' => 'Veri Analizi'],
                        ['value' => 'ai_ml', 'label' => 'Yapay Zeka & ML'],
                        ['value' => 'consulting', 'label' => 'IT Danışmanlığı'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'tech_platforms',
                    'title' => '⚙️ Hangi teknoloji platformlarında uzmanınız?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'laravel', 'label' => 'Laravel/PHP'],
                        ['value' => 'react', 'label' => 'React/Node.js'],
                        ['value' => 'dotnet', 'label' => '.NET/C#'],
                        ['value' => 'python', 'label' => 'Python/Django'],
                        ['value' => 'java', 'label' => 'Java/Spring'],
                        ['value' => 'vue', 'label' => 'Vue.js'],
                        ['value' => 'angular', 'label' => 'Angular'],
                        ['value' => 'aws', 'label' => 'AWS/Azure'],
                        ['value' => 'other', 'label' => 'Diğer Platform']
                    ]
                ]
            ],
            'health' => [
                [
                    'key' => 'health_specialties',
                    'title' => '🩺 Hangi tıp uzmanlık alanlarınız var?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'internal_medicine', 'label' => 'İç Hastalıkları'],
                        ['value' => 'cardiology', 'label' => 'Kardiyoloji'],
                        ['value' => 'dermatology', 'label' => 'Dermatoloji'],
                        ['value' => 'orthopedics', 'label' => 'Ortopedi'],
                        ['value' => 'neurology', 'label' => 'Nöroloji'],
                        ['value' => 'pediatrics', 'label' => 'Pediatri'],
                        ['value' => 'gynecology', 'label' => 'Jinekologi'],
                        ['value' => 'dentistry', 'label' => 'Diş Hekimliği'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'health_services',
                    'title' => '🏥 Hangi sağlık hizmetlerini sunuyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'checkup', 'label' => 'Genel Check-up'],
                        ['value' => 'surgery', 'label' => 'Cerrahi İşlemler'],
                        ['value' => 'emergency', 'label' => 'Acil Servis'],
                        ['value' => 'laboratory', 'label' => 'Laboratuvar'],
                        ['value' => 'imaging', 'label' => 'Görüntüleme'],
                        ['value' => 'physiotherapy', 'label' => 'Fizyoterapi'],
                        ['value' => 'home_care', 'label' => 'Evde Bakım'],
                        ['value' => 'aesthetic', 'label' => 'Estetik'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ]
            ],
            'education' => [
                [
                    'key' => 'education_levels',
                    'title' => '🎓 Hangi eğitim seviyelerinde hizmet veriyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'preschool', 'label' => 'Okul Öncesi'],
                        ['value' => 'primary', 'label' => 'İlkokul'],
                        ['value' => 'secondary', 'label' => 'Ortaokul'],
                        ['value' => 'high_school', 'label' => 'Lise'],
                        ['value' => 'university', 'label' => 'Üniversite'],
                        ['value' => 'postgraduate', 'label' => 'Lisansüstü'],
                        ['value' => 'vocational', 'label' => 'Mesleki Eğitim'],
                        ['value' => 'adult', 'label' => 'Yetişkin Eğitimi'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'education_subjects',
                    'title' => '📚 Hangi konularda eğitim veriyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'mathematics', 'label' => 'Matematik'],
                        ['value' => 'science', 'label' => 'Fen Bilimleri'],
                        ['value' => 'languages', 'label' => 'Dil Eğitimi'],
                        ['value' => 'arts', 'label' => 'Sanat'],
                        ['value' => 'music', 'label' => 'Müzik'],
                        ['value' => 'sports', 'label' => 'Spor'],
                        ['value' => 'technology', 'label' => 'Teknoloji'],
                        ['value' => 'business', 'label' => 'İş/Kariyer'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ]
            ],
            'legal' => [
                [
                    'key' => 'legal_specialties',
                    'title' => '⚖️ Hangi hukuk alanlarında uzmanınız?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'corporate_law', 'label' => 'Kurumsal Hukuk'],
                        ['value' => 'family_law', 'label' => 'Aile Hukuku'],
                        ['value' => 'real_estate_law', 'label' => 'Emlak Hukuku'],
                        ['value' => 'labor_law', 'label' => 'İş Hukuku'],
                        ['value' => 'criminal_law', 'label' => 'Ceza Hukuku'],
                        ['value' => 'tax_law', 'label' => 'Vergi Hukuku'],
                        ['value' => 'it_law', 'label' => 'Bilişim Hukuku'],
                        ['value' => 'traffic_law', 'label' => 'Trafik Hukuku'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'legal_services',
                    'title' => '📋 Hangi hukuki hizmetleri sunuyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'litigation', 'label' => 'Dava Takibi'],
                        ['value' => 'consulting', 'label' => 'Hukuki Danışmanlık'],
                        ['value' => 'contract_drafting', 'label' => 'Sözleşme Hazırlama'],
                        ['value' => 'mediation', 'label' => 'Arabuluculuk'],
                        ['value' => 'legal_research', 'label' => 'Hukuki Araştırma'],
                        ['value' => 'document_review', 'label' => 'Belge İnceleme'],
                        ['value' => 'compliance', 'label' => 'Uygunluk Denetimi'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ]
            ],
            'environment' => [
                [
                    'key' => 'environment_services',
                    'title' => '♻️ Hangi çevre hizmetlerini sunuyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'recycling', 'label' => 'Geri Dönüşüm'],
                        ['value' => 'waste_management', 'label' => 'Atık Yönetimi'],
                        ['value' => 'cleaning_services', 'label' => 'Temizlik Hizmetleri'],
                        ['value' => 'environmental_consulting', 'label' => 'Çevre Danışmanlığı'],
                        ['value' => 'renewable_energy', 'label' => 'Yenilenebilir Enerji'],
                        ['value' => 'water_treatment', 'label' => 'Su Arıtma'],
                        ['value' => 'landscaping', 'label' => 'Peyzaj'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'environment_scale',
                    'title' => '🏭 Hangi ölçekte hizmet veriyorsunuz?',
                    'type' => 'radio',
                    'options' => [
                        ['value' => 'residential', 'label' => 'Konut & Bireysel'],
                        ['value' => 'commercial', 'label' => 'Ticari & Ofis'],
                        ['value' => 'industrial', 'label' => 'Endüstriyel & Fabrika'],
                        ['value' => 'municipal', 'label' => 'Belediye & Kamu'],
                        ['value' => 'all_scales', 'label' => 'Tüm Ölçekler'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ]
            ],
            'metallurgy' => [
                [
                    'key' => 'metal_products',
                    'title' => '🔩 Hangi metal ürünleri üretiyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'steel_production', 'label' => 'Çelik Üretimi'],
                        ['value' => 'metal_processing', 'label' => 'Metal İşleme'],
                        ['value' => 'fasteners', 'label' => 'Bağlantı Elemanları'],
                        ['value' => 'construction_steel', 'label' => 'Metal Konstrüksiyon'],
                        ['value' => 'welding', 'label' => 'Kaynak İşleri'],
                        ['value' => 'coating', 'label' => 'Metal Kaplama'],
                        ['value' => 'packaging', 'label' => 'Metal Ambalaj'],
                        ['value' => 'scrap_recycling', 'label' => 'Metal Hurda'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ],
                [
                    'key' => 'metal_materials',
                    'title' => '⚙️ Hangi metallerle çalışıyorsunuz?',
                    'type' => 'checkbox',
                    'options' => [
                        ['value' => 'steel', 'label' => 'Çelik'],
                        ['value' => 'iron', 'label' => 'Demir'],
                        ['value' => 'aluminum', 'label' => 'Alüminyum'],
                        ['value' => 'copper', 'label' => 'Bakır'],
                        ['value' => 'stainless_steel', 'label' => 'Paslanmaz Çelik'],
                        ['value' => 'galvanized', 'label' => 'Galvanizli'],
                        ['value' => 'bronze_brass', 'label' => 'Bronz & Pirinç'],
                        ['value' => 'other', 'label' => 'Diğer']
                    ]
                ]
            ]
        ];
    }
}