<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileQuestion;
use App\Helpers\TenantHelpers;

class AISectorSpecificQuestionsSeeder extends Seeder
{
    /**
     * SEKTÖRE ÖZEL SORULAR - Yapay Zeka FEATURES İÇİN ÖZELLEŞTİRME
     * 
     * Her sektör için 8 soru (80 soru hedefine doğru)
     * Bu sorular AI'ın o sektöre özel içerik üretmesini sağlar
     * Adım 3'te marka detaylarına eklenir
     */
    public function run(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Yapay Zeka Sektör Özel Sorular Yükleniyor...\n";
        
        // Mevcut sektör özel sorularını temizle (ID aralığı ile)
        AIProfileQuestion::where('id', '>=', 3000)->delete();
        
        // Ana sektörler için özel sorular
        $this->createSectorSpecificQuestions();
        
        echo "\n🎯 Tüm sektör özel sorular tamamlandı!\n";
    }
    
    /**
     * Sektöre özel sorular oluştur
     */
    private function createSectorSpecificQuestions(): void
    {
        $questionId = 3001;
        
        // Ana sektörler ve sorular
        $sectors = [
            'technology' => [
                'name' => 'Teknoloji & Yazılım',
                'questions' => [
                    [
                        'question_key' => 'tech_client_sectors',
                        'question_text' => 'Hangi sektörlere hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka müşteri sektörlerinize özel örnekler versin',
                        'options' => [
                            'Sağlık ve tıbbi teknoloji',
                            'E-ticaret ve perakende', 
                            'Finans ve bankacılık',
                            'Eğitim ve online kurslar',
                            'Üretim ve sanayi',
                            'Emlak ve inşaat',
                            'Turizm ve otelcilik',
                            'Kamu ve belediye',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hizmet verdiğiniz sektörü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_daily_work',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş rutininize göre içerik üretsin',
                        'options' => [
                            'Yazılım kodlama ve geliştirme',
                            'Proje yönetimi ve planlama',
                            'Müşteri toplantıları ve demo',
                            'Sistem kurulum ve bakım',
                            'Hata giderme ve destek',
                            'Veritabanı yönetimi',
                            'Güvenlik testleri',
                            'Mobil uygulama geliştirme',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_project_size',
                        'question_text' => 'Hangi büyüklükteki projelerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka proje ölçeğinize uygun örnekler versin',
                        'options' => [
                            'Küçük bireysel projeler',
                            'Orta ölçekli işletme sistemleri',
                            'Büyük kurumsal projeler',
                            'Startup ve girişim projeleri',
                            'Kamu projeleri',
                            'Uluslararası projeler',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Proje büyüklüğünü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_specialization',
                        'question_text' => 'Hangi teknoloji alanında uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel teknik içerik üretsin',
                        'options' => [
                            'Web geliştirme',
                            'Mobil uygulama geliştirme',
                            'Veri analizi ve Yapay Zeka',
                            'Siber güvenlik',
                            'Bulut sistemleri',
                            'E-ticaret sistemleri',
                            'Oyun geliştirme',
                            'IoT ve donanım',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_work_style',
                        'question_text' => 'Nasıl çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka çalışma şeklinize uygun öneriler versin',
                        'options' => [
                            'Bireysel freelance',
                            'Küçük ekip (2-5 kişi)',
                            'Orta ekip (6-15 kişi)',
                            'Büyük ekip (15+ kişi)',
                            'Uzaktan çalışma',
                            'Hibrit çalışma',
                            'Ofis tabanlı',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Çalışma şeklinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_challenges',
                        'question_text' => 'En çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara özel çözümler önersin',
                        'options' => [
                            'Proje zaman yönetimi',
                            'Müşteri beklenti yönetimi',
                            'Teknik karmaşıklık',
                            'Takım koordinasyonu',
                            'Bütçe ve maliyet kontrolü',
                            'Teknoloji güncellemeleri',
                            'Kalite kontrol',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Yaşadığınız zorluğu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'tech_pricing_model',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma stratejinize uygun öneriler versin',
                        'options' => [
                            'Saatlik ücretlendirme',
                            'Proje bazlı sabit fiyat',
                            'Aylık abonelik',
                            'Komisyon bazlı',
                            'Karma fiyatlandırma',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma modelinizi belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'health' => [
                'name' => 'Sağlık & Tıp',
                'questions' => [
                    [
                        'question_key' => 'health_daily_services',
                        'question_text' => 'Günlük olarak hangi hizmetleri veriyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş akışınıza göre içerik üretsin',
                        'options' => [
                            'Muayene ve teşhis',
                            'Tedavi ve ilaç reçetesi',
                            'Kontrol ve takip',
                            'Diş temizliği ve dolgu',
                            'Estetik işlem ve bakım',
                            'Fizik tedavi seansı',
                            'Laboratuvar test sonuçları',
                            'Acil müdahale',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Verdiğiniz hizmeti belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_patient_age',
                        'question_text' => 'Hangi yaş grubundaki hastalara hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka yaş grubuna özel içerik üretsin',
                        'options' => [
                            'Bebek ve çocuk (0-12 yaş)',
                            'Genç ve yetişkin (13-65 yaş)',
                            'Yaşlı hasta (65+ yaş)',
                            'Tüm yaş grupları',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Yaş grubunu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_specialization',
                        'question_text' => 'Hangi alanda uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel tıbbi içerik üretsin',
                        'options' => [
                            'Genel pratisyen',
                            'Dahiliye',
                            'Pediatri',
                            'Kadın doğum',
                            'Diş hekimliği',
                            'Fizik tedavi',
                            'Psikoloji/Psikiyatri',
                            'Estetik ve güzellik',
                            'Eczacılık',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_facility_type',
                        'question_text' => 'Hangi tür sağlık tesisinde çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka çalışma ortamınıza uygun öneriler versin',
                        'options' => [
                            'Özel muayenehane',
                            'Özel hastane',
                            'Devlet hastanesi',
                            'Üniversite hastanesi',
                            'Sağlık ocağı',
                            'Evde bakım hizmeti',
                            'Online danışmanlık',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Çalışma ortamınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_common_problems',
                        'question_text' => 'En sık hangi sağlık sorunlarıyla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaygın problemlerinize özel çözümler önersin',
                        'options' => [
                            'Grip ve soğuk algınlığı',
                            'Ağrı ve inflamasyon',
                            'Kronik hastalıklar',
                            'Diyabet ve metabolik hastalıklar',
                            'Kalp ve damar hastalıkları',
                            'Diş ve ağız sağlığı',
                            'Cilt problemleri',
                            'Stres ve mental sorunlar',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Yaygın problemleri belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_appointment_type',
                        'question_text' => 'Nasıl randevu sistemi kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka randevu sisteminize uygun öneriler versin',
                        'options' => [
                            'Yüz yüze randevular',
                            'Online randevular',
                            'Acil durumlar',
                            'Evde ziyaret',
                            'Telefon danışmanlığı',
                            'Karma sistem',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Randevu sisteminizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'health_pricing_system',
                        'question_text' => 'Nasıl ücretlendirme yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma sisteminize uygun öneriler versin',
                        'options' => [
                            'Muayene başına sabit ücret',
                            'Sigorta anlaşmaları',
                            'Paket hizmetler',
                            'Seans bazlı ücretlendirme',
                            'Aylık abonelik',
                            'Karma sistem',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Ücretlendirme sisteminizi belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'education' => [
                'name' => 'Eğitim & Öğretim',
                'questions' => [
                    [
                        'question_key' => 'education_daily_activities',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek eğitim rutininize göre içerik üretsin',
                        'options' => [
                            'Ders anlatımı ve sunum',
                            'Ödev kontrolü ve değerlendirme',
                            'Sınav hazırlığı ve soru çözümü',
                            'Bireysel öğrenci görüşmesi',
                            'Veli toplantısı ve görüşme',
                            'Etkinlik ve gezi organizasyonu',
                            'Online canlı ders',
                            'Müfredat ve materyal hazırlığı',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_subjects',
                        'question_text' => 'Hangi konularda eğitim veriyorsunuz?',
                        'help_text' => 'Yapay Zeka konu alanınıza özel içerik üretsin',
                        'options' => [
                            'Matematik ve fen bilimleri',
                            'Dil ve edebiyat',
                            'Sosyal bilimler',
                            'Sanat ve müzik',
                            'Spor ve beden eğitimi',
                            'Bilgisayar ve teknoloji',
                            'Mesleki beceriler',
                            'Kişisel gelişim',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Konu alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_student_level',
                        'question_text' => 'Hangi seviyede öğrencilerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka seviyeye uygun içerik üretsin',
                        'options' => [
                            'Anaokulu (3-6 yaş)',
                            'İlkokul (7-10 yaş)',
                            'Ortaokul (11-14 yaş)',
                            'Lise (15-18 yaş)',
                            'Üniversite öğrencileri',
                            'Yetişkin eğitimi',
                            'Kurumsal eğitim',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Öğrenci seviyesini belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_teaching_method',
                        'question_text' => 'Hangi öğretim yöntemlerini kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka öğretim tarzınıza uygun öneriler versin',
                        'options' => [
                            'Geleneksel sınıf dersi',
                            'Etkileşimli öğretim',
                            'Proje tabanlı öğrenme',
                            'Grup çalışmaları',
                            'Bireysel öğretim',
                            'Online/uzaktan eğitim',
                            'Karma öğretim',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Öğretim yönteminizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_class_size',
                        'question_text' => 'Kaç kişilik sınıflarla çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka sınıf büyüklüğünüze uygun stratejiler önersin',
                        'options' => [
                            'Bireysel ders (1 kişi)',
                            'Küçük grup (2-5 kişi)',
                            'Orta sınıf (6-15 kişi)',
                            'Büyük sınıf (16-30 kişi)',
                            'Çok büyük sınıf (30+ kişi)',
                            'Değişken grup boyutu',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Sınıf büyüklüğünü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_challenges',
                        'question_text' => 'Eğitimde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Öğrenci motivasyon eksikliği',
                            'Dikkat dağınıklığı',
                            'Farklı öğrenme hızları',
                            'Disiplin problemleri',
                            'Veli ilgisizliği',
                            'Kaynak yetersizliği',
                            'Teknoloji kullanım zorlukları',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'education_goals',
                        'question_text' => 'Eğitim hedefleriniz nelerdir?',
                        'help_text' => 'Yapay Zeka hedeflerinize uygun stratejiler önersin',
                        'options' => [
                            'Akademik başarı artırma',
                            'Karakter gelişimi',
                            'Yaratıcılık geliştirme',
                            'Sosyal beceri kazandırma',
                            'Mesleki hazırlık',
                            'Kişisel gelişim',
                            'Eleştirel düşünme',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hedeflerinizi belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'food' => [
                'name' => 'Yiyecek & İçecek',
                'questions' => [
                    [
                        'question_key' => 'food_daily_operations',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş akışınıza göre içerik üretsin',
                        'options' => [
                            'Yemek hazırlığı ve pişirme',
                            'Müşteri karşılama ve sipariş alma',
                            'Servis ve masa düzeni',
                            'Kasa ve hesap işlemleri',
                            'Temizlik ve hijyen',
                            'Malzeme alışverişi',
                            'Menü planlama',
                            'Paket servis hazırlığı',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_atmosphere',
                        'question_text' => 'Mekanınızın atmosferi nasıl?',
                        'help_text' => 'Yapay Zeka mekan karakterinize uygun içerik üretsin',
                        'options' => [
                            'Aile dostu ve rahat',
                            'Romantik ve samimi',
                            'Modern ve şık',
                            'Geleneksel ve otantik',
                            'Hızlı ve pratik',
                            'Lüks ve prestijli',
                            'Gençlik ve eğlence',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Atmosferinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_cuisine_type',
                        'question_text' => 'Hangi tür yemekler sunuyorsunuz?',
                        'help_text' => 'Yapay Zeka mutfak tarzınıza özel içerik üretsin',
                        'options' => [
                            'Türk mutfağı',
                            'İtalyan mutfağı',
                            'Dünya mutfağı',
                            'Fast food',
                            'Vegan/vejetaryen',
                            'Deniz ürünleri',
                            'Et yemekleri',
                            'Tatlı ve pasta',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Mutfak türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_service_type',
                        'question_text' => 'Nasıl hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka hizmet tarzınıza uygun öneriler versin',
                        'options' => [
                            'Restoran/masa servisi',
                            'Cafe/self servis',
                            'Paket servis',
                            'Online sipariş',
                            'Catering hizmeti',
                            'Sokak lezzeti',
                            'Karma hizmet',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hizmet türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_customer_type',
                        'question_text' => 'Müşteri kitleniz kimler?',
                        'help_text' => 'Yapay Zeka müşteri profilinize uygun içerik üretsin',
                        'options' => [
                            'Aileler',
                            'Genç çiftler',
                            'İş insanları',
                            'Öğrenciler',
                            'Turistler',
                            'Mahalle sakinleri',
                            'Özel etkinlik müşterileri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri tipinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_meal_times',
                        'question_text' => 'Hangi öğünlerde hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka öğün saatlerinize uygun öneriler versin',
                        'options' => [
                            'Kahvaltı',
                            'Öğle yemeği',
                            'Akşam yemeği',
                            'Aperatif/meze',
                            'Tatlı/içecek',
                            '24 saat',
                            'Karma öğünler',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Öğün saatlerinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'food_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Malzeme maliyetleri',
                            'Personel bulma',
                            'Hijyen standartları',
                            'Müşteri memnuniyeti',
                            'Rekabet',
                            'Stok yönetimi',
                            'Sezon değişiklikleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'retail' => [
                'name' => 'E-ticaret & Perakende',
                'questions' => [
                    [
                        'question_key' => 'retail_daily_tasks',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş rutininize göre içerik üretsin',
                        'options' => [
                            'Müşteri karşılama ve danışmanlık',
                            'Sipariş alma ve kargo hazırlığı',
                            'Ürün fotoğrafı ve tanıtım',
                            'Stok kontrolü ve sayımı',
                            'İade ve değişim işlemleri',
                            'Fiyat güncellemeleri',
                            'Satış raporları hazırlama',
                            'Tedarikçi görüşmeleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_price_range',
                        'question_text' => 'Hangi fiyat segmentinde satış yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyat politikanıza uygun içerik üretsin',
                        'options' => [
                            'Ekonomik ve bütçe dostu',
                            'Orta segment ve kaliteli',
                            'Premium ve lüks',
                            'Değişken fiyat aralığı',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyat segmentinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_product_category',
                        'question_text' => 'Hangi ürün kategorilerinde satış yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka ürün kategorinize özel içerik üretsin',
                        'options' => [
                            'Giyim ve aksesuar',
                            'Elektronik',
                            'Ev eşyası',
                            'Kozmetik ve kişisel bakım',
                            'Spor ve outdoor',
                            'Bebek ve çocuk',
                            'Hobi ve el işi',
                            'Yiyecek ve içecek',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Ürün kategorinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_sales_channel',
                        'question_text' => 'Hangi satış kanallarını kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka satış kanallarınıza uygun öneriler versin',
                        'options' => [
                            'Fiziksel mağaza',
                            'Online mağaza',
                            'Sosyal medya satış',
                            'Pazaryeri (Trendyol, Hepsiburada)',
                            'Toptan satış',
                            'Bayilik sistemi',
                            'Karma kanal',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Satış kanallarınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_customer_support',
                        'question_text' => 'Müşteri hizmetleri nasıl veriyorsunuz?',
                        'help_text' => 'Yapay Zeka müşteri hizmet tarzınıza uygun öneriler versin',
                        'options' => [
                            'Telefon desteği',
                            'WhatsApp destek',
                            'E-posta desteği',
                            'Canlı chat',
                            'Sosyal medya cevap',
                            'Yüz yüze destek',
                            'Karma destek',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Destek türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_inventory_size',
                        'question_text' => 'Stok büyüklüğünüz nasıl?',
                        'help_text' => 'Yapay Zeka stok durumunuza uygun öneriler versin',
                        'options' => [
                            'Küçük stok (50-200 ürün)',
                            'Orta stok (200-1000 ürün)',
                            'Büyük stok (1000+ ürün)',
                            'Dropshipping',
                            'Stoksuz satış',
                            'Mevsimlik stok',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Stok durumunuzu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'retail_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Rekabet ve fiyat savaşı',
                            'Stok yönetimi',
                            'Kargo maliyetleri',
                            'Müşteri kazanma',
                            'İade ve değişim işlemleri',
                            'Tedarikçi sorunları',
                            'Kanal yönetimi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'construction' => [
                'name' => 'İnşaat & Emlak',
                'questions' => [
                    [
                        'question_key' => 'construction_daily_work',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş akışınıza göre içerik üretsin',
                        'options' => [
                            'Müşteri görüşmeleri ve keşif',
                            'Proje çizimi ve tasarım',
                            'Metraj ve maliyet hesabı',
                            'Malzeme tedarik ve kontrol',
                            'Şantiye denetimi',
                            'İşçi koordinasyonu',
                            'Belediye izin takibi',
                            'Emlak değerlendirmesi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_scale',
                        'question_text' => 'Hangi ölçekteki projelerde çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka proje büyüklüğünüze uygun içerik üretsin',
                        'options' => [
                            'Küçük konut projeleri',
                            'Büyük konut kompleksleri',
                            'Ticari ve ofis binaları',
                            'Endüstriyel tesisler',
                            'Kamu binaları',
                            'Villa ve lüks konut',
                            'Restorasyon projeleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Proje türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_specialization',
                        'question_text' => 'Hangi inşaat alanında uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel içerik üretsin',
                        'options' => [
                            'Konut inşaatı',
                            'Ticari bina inşaatı',
                            'Sanayi tesisleri',
                            'İç mimari ve dekorasyon',
                            'Peyzaj ve dış mekan',
                            'Yenileme ve tadilat',
                            'Altyapı projeleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_team_size',
                        'question_text' => 'Kaç kişilik ekiplerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka ekip büyüklüğünüze uygun öneriler versin',
                        'options' => [
                            'Bireysel çalışan',
                            'Küçük ekip (2-5 kişi)',
                            'Orta ekip (6-15 kişi)',
                            'Büyük ekip (16-50 kişi)',
                            'Çok büyük ekip (50+ kişi)',
                            'Proje bazlı değişken',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Ekip büyüklüğünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_pricing_method',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma sisteminize uygun öneriler versin',
                        'options' => [
                            'Metrekare başına fiyat',
                            'Proje bazlı sabit fiyat',
                            'Maliyet + kar oranı',
                            'Gün/saat bazlı ücret',
                            'Malzeme + işçilik ayrı',
                            'Karma fiyatlandırma',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma metodunuzu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Malzeme fiyat artışları',
                            'Nitelikli işçi bulma',
                            'Hava şartları',
                            'Belediye izin süreçleri',
                            'Proje gecikmesi',
                            'Müşteri beklenti yönetimi',
                            'Kalite kontrol',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'construction_materials',
                        'question_text' => 'Hangi yapı malzemelerini kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka kullandığınız malzemelere özel öneriler versin',
                        'options' => [
                            'Beton ve çelik',
                            'Doğal taş',
                            'Ahşap',
                            'Cam ve alüminyum',
                            'Çelik konstrüksiyon',
                            'Hafif çelik',
                            'Kompozit malzemeler',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Malzeme türünüzü belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'finance' => [
                'name' => 'Finans & Muhasebe',
                'questions' => [
                    [
                        'question_key' => 'finance_daily_tasks',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş rutininize göre içerik üretsin',
                        'options' => [
                            'Defter tutma ve kayıt işlemleri',
                            'Vergi beyannamesi hazırlama',
                            'Müşteri mali danışmanlığı',
                            'Belge kontrolü ve evrak düzenleme',
                            'Banka mutabakatı',
                            'Bordro ve SGK işlemleri',
                            'Mali rapor hazırlama',
                            'Müşteri görüşmeleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_client_type',
                        'question_text' => 'Hangi tür müşterilerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka müşteri profilinize uygun içerik üretsin',
                        'options' => [
                            'Bireysel müşteriler',
                            'Küçük işletmeler',
                            'Orta ölçekli şirketler',
                            'Büyük kurumlar',
                            'Emlak sektörü',
                            'Serbest meslek erbabı',
                            'Yabancı yatırımcılar',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_specialization',
                        'question_text' => 'Hangi finans alanında uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel içerik üretsin',
                        'options' => [
                            'Genel muhasebe',
                            'Vergi danışmanlığı',
                            'Mali müşavirlik',
                            'Yeminli mali müşavirlik',
                            'Dış ticaret',
                            'Emlak finansmanı',
                            'Kurumsal finans',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_service_frequency',
                        'question_text' => 'Hangi sıklıkla hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka hizmet sıklığınıza uygun öneriler versin',
                        'options' => [
                            'Günlük işlem takibi',
                            'Haftalık denetim',
                            'Aylık kapanış',
                            'Üç aylık raporlama',
                            'Yıllık beyanname',
                            'Proje bazlı danışmanlık',
                            'Sürekli takip',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hizmet sıklığınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_software_tools',
                        'question_text' => 'Hangi yazılımları kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka kullandığınız araçlara özel öneriler versin',
                        'options' => [
                            'Muhasebe paket programları',
                            'Excel ve hesap tabloları',
                            'e-Fatura/e-Defter',
                            'Bordro programları',
                            'Bulut tabanlı sistemler',
                            'ERP sistemleri',
                            'Elle kayıt',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Yazılım türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Vergi mevzuatı değişiklikleri',
                            'Müşteri belge eksiklikleri',
                            'Yasal süreçler',
                            'Teknoloji adaptasyonu',
                            'Rekabet baskısı',
                            'Müşteri eğitimi',
                            'Zaman yönetimi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'finance_pricing_model',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma sisteminize uygun öneriler versin',
                        'options' => [
                            'Aylık sabit ücret',
                            'İşlem bazlı fiyat',
                            'Saat bazlı ücret',
                            'Proje bazlı fiyat',
                            'Yıllık kontrat',
                            'Karma fiyatlandırma',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma modelinizi belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'web_design' => [
                'name' => 'Web Tasarım & Dijital Ajans',
                'questions' => [
                    [
                        'question_key' => 'digital_services',
                        'question_text' => 'Hangi dijital hizmetleri sunuyorsunuz?',
                        'help_text' => 'Yapay Zeka hizmet portföyünüze özel içerik üretsin',
                        'options' => [
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
                                'custom_placeholder' => 'Hizmet alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_project_types',
                        'question_text' => 'Hangi tür projeler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka size gerçek proje deneyimlerinize göre örnekler versin',
                        'options' => [
                            'Kurumsal web sitesi',
                            'E-ticaret mağaza',
                            'Mobil uygulama',
                            'SEO danışmanlığı',
                            'Sosyal medya kampanyası',
                            'Logo ve marka tasarımı',
                            'Google Ads yönetimi',
                            'Dijital pazarlama stratejisi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Proje türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_client_size',
                        'question_text' => 'Hangi büyüklükteki müşterilerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka müşteri büyüklüğünüze uygun öneriler versin',
                        'options' => [
                            'Bireysel girişimciler',
                            'Küçük işletmeler (1-10 kişi)',
                            'Orta işletmeler (11-50 kişi)',
                            'Büyük şirketler (50+ kişi)',
                            'Kamu kurumları',
                            'Uluslararası şirketler',
                            'Karma müşteri portföyü',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri büyüklüğünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_technologies',
                        'question_text' => 'Hangi teknolojileri kullanıyorsunuz?',
                        'help_text' => 'Yapay Zeka teknoloji yetkinliğinize uygun içerik üretsin',
                        'options' => [
                            'WordPress',
                            'Laravel/PHP',
                            'React/Vue.js',
                            'Photoshop/Illustrator',
                            'Google Analytics',
                            'Facebook/Instagram Ads',
                            'Shopify/WooCommerce',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Teknoloji yetkinliğinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_project_duration',
                        'question_text' => 'Projeleriniz ne kadar sürede tamamlanıyor?',
                        'help_text' => 'Yapay Zeka proje sürenize uygun öneriler versin',
                        'options' => [
                            '1 hafta içinde',
                            '2-4 hafta',
                            '1-3 ay',
                            '3-6 ay',
                            '6 ay ve üzeri',
                            'Sürekli devam eden',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Proje sürenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_pricing_structure',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma yapınıza uygun öneriler versin',
                        'options' => [
                            'Proje bazlı sabit fiyat',
                            'Saatlik ücretlendirme',
                            'Aylık bakım ücreti',
                            'Performans bazlı komisyon',
                            'Paket fiyatlandırma',
                            'Karma fiyatlandırma',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma yapınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'digital_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Müşteri beklenti yönetimi',
                            'Teknoloji güncellemeleri',
                            'Rekabet baskısı',
                            'Proje gecikmesi',
                            'Revizyon talepleri',
                            'Bütçe kısıtlamaları',
                            'Müşteri eğitimi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'law' => [
                'name' => 'Hukuk & Avukatlık',
                'questions' => [
                    [
                        'question_key' => 'law_daily_activities',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş akışınıza göre içerik üretsin',
                        'options' => [
                            'Müşteri görüşmeleri ve danışmanlık',
                            'Dava dosyası hazırlama',
                            'Mahkeme ve duruşma takibi',
                            'Sözleşme inceleme ve düzenleme',
                            'Hukuki araştırma',
                            'Dilekçe ve evrak hazırlama',
                            'İcra takibi',
                            'Arabuluculuk',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_service_type',
                        'question_text' => 'Hangi türde hukuki hizmetler sunuyorsunuz?',
                        'help_text' => 'Yapay Zeka hizmet türünüze özel içerik üretsin',
                        'options' => [
                            'Danışmanlık ve görüş',
                            'Sözleşme hazırlama',
                            'Dava ve yargılama',
                            'Arabuluculuk',
                            'Şirket kuruluşu',
                            'Emlak işlemleri',
                            'İcra takibi',
                            'Hukuki inceleme',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hizmet türünüzü belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_specialization',
                        'question_text' => 'Hangi hukuk alanında uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel içerik üretsin',
                        'options' => [
                            'Ticaret hukuku',
                            'Aile hukuku',
                            'Ceza hukuku',
                            'İş hukuku',
                            'Emlak hukuku',
                            'Vergi hukuku',
                            'İdare hukuku',
                            'Borçlar hukuku',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_client_profile',
                        'question_text' => 'Hangi tür müşterilerle çalışıyorsunuz?',
                        'help_text' => 'Yapay Zeka müşteri profilinize uygun içerik üretsin',
                        'options' => [
                            'Bireysel müşteriler',
                            'Küçük işletmeler',
                            'Büyük şirketler',
                            'Kamu kurumları',
                            'Yabancı yatırımcılar',
                            'Dernekler ve vakıflar',
                            'Karma müşteri portföyü',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri profilinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_case_complexity',
                        'question_text' => 'Hangi karmaşıklıktaki davalarla uğraşıyorsunuz?',
                        'help_text' => 'Yapay Zeka dava karmaşıklığınıza uygun öneriler versin',
                        'options' => [
                            'Basit hukuki işlemler',
                            'Orta karmaşıklıkta davalar',
                            'Karmaşık ticari davalar',
                            'Temyiz ve istinaf',
                            'Uluslararası hukuk',
                            'Toplu davalar',
                            'Değişken karmaşıklık',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Dava karmaşıklığınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_pricing_method',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma sisteminize uygun öneriler versin',
                        'options' => [
                            'Saatlik ücret',
                            'Dava bazlı sabit ücret',
                            'Başarı primi',
                            'Aylık danışmanlık ücreti',
                            'Karma fiyatlandırma',
                            'Avukatlık tarifesi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma metodunuzu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'law_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Yasal düzenleme değişiklikleri',
                            'Müşteri beklenti yönetimi',
                            'Dava süreci uzunluğu',
                            'Rekabet baskısı',
                            'Tahsilat sorunları',
                            'İş yoğunluğu',
                            'Teknoloji adaptasyonu',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ]
                ]
            ],
            'beauty' => [
                'name' => 'Güzellik & Estetik',
                'questions' => [
                    [
                        'question_key' => 'beauty_daily_services',
                        'question_text' => 'Günlük olarak neler yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka gerçek iş akışınıza göre içerik üretsin',
                        'options' => [
                            'Saç kesimi ve şekillendirme',
                            'Boyama ve renklendirme',
                            'Cilt bakımı ve temizlik',
                            'Makyaj ve gelin hazırlığı',
                            'Manikür ve pedikür',
                            'Kaş ve kirpik bakımı',
                            'Masaj ve rahatlama',
                            'Müşteri randevu yönetimi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Günlük aktivitenizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_client_profile',
                        'question_text' => 'Müşteri kitleniz nasıl?',
                        'help_text' => 'Yapay Zeka müşteri profilinize uygun içerik üretsin',
                        'options' => [
                            'Kadın müşteriler',
                            'Erkek müşteriler',
                            'Gençler (18-35 yaş)',
                            'Orta yaş (35-55 yaş)',
                            'Gelin ve düğün',
                            'Kurumsal müşteriler',
                            'Özel etkinlikler',
                            'Tüm yaş grupları',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri profilinizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_specialization',
                        'question_text' => 'Hangi güzellik alanında uzmanlaştınız?',
                        'help_text' => 'Yapay Zeka uzmanlık alanınıza özel içerik üretsin',
                        'options' => [
                            'Saç tasarımı',
                            'Cilt bakımı',
                            'Makyaj sanatı',
                            'Tırnak bakımı',
                            'Kaş ve kirpik',
                            'Masaj ve spa',
                            'Gelin güzelliği',
                            'Erkek bakımı',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Uzmanlık alanınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_service_location',
                        'question_text' => 'Nerede hizmet veriyorsunuz?',
                        'help_text' => 'Yapay Zeka hizmet lokasyonunuza uygun öneriler versin',
                        'options' => [
                            'Kendi salonumda',
                            'Müşterinin evinde',
                            'Düğün ve etkinliklerde',
                            'Güzellik merkezinde',
                            'Hastane/klinik',
                            'Online danışmanlık',
                            'Karma hizmet',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Hizmet lokasyonunuzu belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_appointment_frequency',
                        'question_text' => 'Müşterileriniz ne sıklıkla geliyor?',
                        'help_text' => 'Yapay Zeka müşteri sıklığınıza uygun öneriler versin',
                        'options' => [
                            'Haftalık düzenli',
                            'Aylık bakım',
                            'Özel durum/etkinlik',
                            'Sezon değişimi',
                            'Tek seferlik',
                            'Değişken sıklık',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Müşteri sıklığınızı belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_pricing_structure',
                        'question_text' => 'Nasıl fiyatlandırma yapıyorsunuz?',
                        'help_text' => 'Yapay Zeka fiyatlandırma sisteminize uygun öneriler versin',
                        'options' => [
                            'Hizmet bazlı sabit fiyat',
                            'Paket fiyatlandırma',
                            'Süre bazlı ücret',
                            'Malzeme + işçilik',
                            'Özel etkinlik fiyatı',
                            'Üyelik sistemi',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Fiyatlandırma sisteminizi belirtiniz'
                            ]
                        ]
                    ],
                    [
                        'question_key' => 'beauty_challenges',
                        'question_text' => 'Sektörde en çok hangi zorluklarla karşılaşıyorsunuz?',
                        'help_text' => 'Yapay Zeka yaşadığınız sorunlara çözüm önersin',
                        'options' => [
                            'Müşteri memnuniyeti',
                            'Randevu yönetimi',
                            'Trendleri takip etme',
                            'Rekabet baskısı',
                            'Ürün maliyetleri',
                            'Müşteri sadakati',
                            'Sezon değişiklikleri',
                            [
                                'value' => 'custom',
                                'label' => 'Diğer (belirtiniz)',
                                'has_custom_input' => true,
                                'custom_placeholder' => 'Zorluğu belirtiniz'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        foreach ($sectors as $sectorCode => $sectorData) {
            $sortOrder = 30;
            foreach ($sectorData['questions'] as $questionData) {
                $question = [
                    'id' => $questionId++,
                    'step' => 3,
                    'sector_code' => $sectorCode,
                    'question_key' => $questionData['question_key'],
                    'question_text' => $questionData['question_text'],
                    'help_text' => $questionData['help_text'],
                    'input_type' => $questionData['input_type'] ?? 'checkbox',
                    'options' => json_encode($questionData['options'], JSON_UNESCAPED_UNICODE),
                    'is_required' => false,
                    'is_active' => true,
                    'sort_order' => $sortOrder++
                ];
                
                AIProfileQuestion::create($question);
            }
            
            // Her sektör için ayrı main_service sorusu
            $question = [
                'id' => $questionId++,
                'step' => 3,
                'sector_code' => $sectorCode,
                'question_key' => $sectorCode . '_main_service',
                'question_text' => 'Ana hizmetiniz/ürününüz nedir?',
                'help_text' => 'Yukarıdakilere ek olarak, genel olarak ne yapıyorsunuz?',
                'input_type' => 'textarea',
                'options' => json_encode([], JSON_UNESCAPED_UNICODE),
                'is_required' => false,
                'is_active' => true,
                'sort_order' => $sortOrder++ // En sonda
            ];
            
            AIProfileQuestion::create($question);
            
            echo "✅ {$sectorData['name']} sektörü soruları eklendi\n";
        }
    }
}