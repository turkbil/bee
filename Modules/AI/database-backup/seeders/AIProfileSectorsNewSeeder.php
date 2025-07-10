<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\app\Models\AIProfileSector;
use App\Helpers\TenantHelpers;

class AIProfileSectorsNewSeeder extends Seeder
{
    /**
     * AI PROFİL SEKTÖR VERİLERİ - DEMO'DAN AKTARIM
     * 
     * Demo sistemindeki tüm sektör ve alt kategorileri
     * gerçek AI Profile sistemine aktarır.
     * 
     * Toplam: 16 ana sektör + 100+ alt kategori
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🚀 AI Profile Sectors - Demo'dan Gerçek Sisteme Aktarım Başlıyor...\n";
        
        // Önce mevcut verileri temizle
        AIProfileSector::truncate();
        
        $sectors = $this->getSectorData();
        $sectorCount = 0;
        $subcategoryCount = 0;
        
        foreach ($sectors as $sectorCode => $sectorData) {
            // Ana sektörü oluştur
            $mainSector = AIProfileSector::create([
                'code' => $sectorCode,
                'category_id' => null, // Ana kategori için null
                'name' => $sectorData['title'],
                'emoji' => $sectorData['emoji'],
                'color' => $sectorData['color'],
                'description' => "Ana sektör: {$sectorData['title']}",
                'keywords' => $sectorData['keywords'],
                'is_active' => true,
                'sort_order' => $sectorCount * 10
            ]);
            
            $sectorCount++;
            echo "✅ Ana Sektör: {$sectorData['title']} ({$sectorCode}) - ID: {$mainSector->id}\n";
            
            // Alt kategorileri oluştur
            foreach ($sectorData['items'] as $index => $item) {
                $subcategoryCode = $sectorCode . '_' . ($index + 1);
                
                AIProfileSector::create([
                    'code' => $subcategoryCode,
                    'category_id' => $mainSector->id, // Ana kategorinin ID'si
                    'name' => $item['title'],
                    'emoji' => $item['emoji'] ?? '📋',
                    'color' => $sectorData['color'],
                    'description' => $item['description'],
                    'keywords' => $item['keywords'],
                    'is_active' => true,
                    'sort_order' => $index * 10
                ]);
                
                $subcategoryCount++;
            }
            
            echo "   → " . count($sectorData['items']) . " alt kategori eklendi\n";
        }
        
        echo "\n🎯 ÖZET:\n";
        echo "📊 Ana Sektör: {$sectorCount}\n";
        echo "📋 Alt Kategori: {$subcategoryCount}\n";
        echo "🎉 Toplam: " . ($sectorCount + $subcategoryCount) . " kayıt eklendi!\n";
    }
    
    private function extractEmoji(string $title): string
    {
        preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $title, $matches);
        return $matches[0] ?? '📝';
    }
    
    private function getSectorData(): array
    {
        return [
            'technology' => [
                'title' => 'Teknoloji & Bilişim',
                'emoji' => '💻',
                'color' => 'blue',
                'keywords' => 'teknoloji, bilişim, yazılım, software, development, programming, coding, IT, bilgisayar, computer, sistem, system, web, app, mobile, developer, geliştirici, programcı, coder, teknisyen, otomasyon, automation, digital, dijital, backend, frontend, fullstack, database, veritabanı, API, framework, laravel, react, angular, vue, python, java, php, javascript, css, html, node, .net, c#, hosting, domain, server, sunucu, network, ağ, internet, online, website, site, platform',
                'items' => [
                    [
                        'title' => 'Teknoloji & Yazılım Geliştirme',
                        'description' => 'Web, mobil, desktop uygulamalar, sistem geliştirme, özel yazılım çözümleri',
                        'keywords' => 'teknoloji yazılım software development programming kodlama app uygulama web geliştirme mobile app desktop backend frontend fullstack laravel php python java javascript react angular vue node.js .net c# developer geliştirici programcı coder coding sistem system enterprise kurumsal çözüm solution custom özel proje project agile scrum devops framework api database mysql postgresql mongodb veritabanı mvc oop'
                    ],
                    [
                        'title' => 'BT Danışmanlığı & Sistem Entegrasyonu',
                        'description' => 'IT altyapı, sistem kurulumu, teknik danışmanlık, network çözümleri',
                        'keywords' => 'IT bilişim sistem danışmanlık entegrasyon consulting system infrastructure altyapı network ağ server sunucu kurulum installation migration taşıma technical teknik support destek maintenance bakım troubleshooting sorun çözme windows linux unix cisco microsoft enterprise kurumsal domain controller active directory vpn firewall switch router kablolama structured cabling wifi wireless'
                    ],
                    [
                        'title' => 'Web Tasarım & Dijital Ajans',
                        'description' => 'Website tasarımı, e-ticaret, dijital pazarlama, SEO hizmetleri',
                        'keywords' => 'web website tasarım design dijital digital ajans agency e-ticaret ecommerce pazarlama marketing SEO SEM sosyal medya social media reklam advertising google ads facebook instagram grafik graphic ui ux kullanıcı deneyimi user experience responsive mobil uyumlu cms wordpress drupal seo optimizasyon content marketing içerik pazarlama'
                    ],
                    [
                        'title' => 'Mobil Uygulama Geliştirme',
                        'description' => 'Android, iOS, hybrid mobil uygulamalar, app store optimizasyonu',
                        'keywords' => 'mobil mobile app uygulama android ios telefon phone tablet ipad native hybrid cross platform react native flutter xamarin swift kotlin java objective-c app store google play store app development mobile development aso app store optimization push notification api integration backend mobile ui ux'
                    ],
                    [
                        'title' => 'Yapay Zeka & Makine Öğrenmesi',
                        'description' => 'AI çözümleri, chatbot, otomasyon sistemleri, veri analizi',
                        'keywords' => 'AI yapay zeka makine öğrenmesi machine learning chatbot otomasyon artificial intelligence deep learning neural network veri analizi data analysis big data nlp natural language processing computer vision image recognition automation rpa robotic process automation python tensorflow pytorch scikit-learn'
                    ],
                    [
                        'title' => 'Bulut Bilişim & DevOps',
                        'description' => 'Cloud hosting, sunucu yönetimi, altyapı hizmetleri, migration',
                        'keywords' => 'cloud bulut hosting server sunucu devops infrastructure aws azure google cloud digitalocean kubernetes docker containerization microservices ci cd continuous integration deployment monitoring backup security ssl certificate migration taşıma scalability ölçeklenebilirlik'
                    ],
                    [
                        'title' => 'Siber Güvenlik & Veri Koruma',
                        'description' => 'Sistem güvenliği, veri koruma, siber tehdit önleme, güvenlik denetimi',
                        'keywords' => 'güvenlik security siber cyber veri data koruma protection firewall antivirus malware penetration testing ethical hacking vulnerability assessment security audit iso 27001 gdpr kvkk compliance uyumluluk encryption şifreleme backup yedekleme incident response'
                    ],
                    [
                        'title' => 'Veri Analizi & İş Zekası',
                        'description' => 'Büyük veri analizi, raporlama, karar destek sistemleri, BI çözümleri',
                        'keywords' => 'veri data analiz analysis iş zekası business intelligence BI big data analytics reporting dashboard visualization görselleştirme tableau power bi excel sql nosql data warehouse etl data mining statistics istatistik predictive analytics machine learning'
                    ]
                ]
            ],
            'health' => [
                'title' => 'Sağlık & Tıp',
                'emoji' => '🏥',
                'color' => 'green',
                'keywords' => 'sağlık, health, tıp, medical, doktor, doctor, hekim, hastane, hospital, klinik, clinic, tedavi, treatment, hasta, patient, sağlık hizmetleri, healthcare, tıbbi, medicine, ilaç, pharmacy, eczane, hasta bakımı, patient care, tanı, diagnosis, muayene, examination, ameliyat, surgery, cerrahi, surgical, acil, emergency, ambulans, ambulance, hemşire, nurse, sağlık personeli, medical staff, laboratuvar, laboratory, test, tahlil, görüntüleme, imaging, radyoloji, radiology, mri, ct, ultrasound, röntgen, x-ray, kan, blood, idrar, urine, biyopsi, biopsy, konsültasyon, consultation, randevu, appointment, reçete, prescription, ilaç, medication, tedavi planı, treatment plan',
                'items' => [
                    [
                        'title' => 'Hastane & Sağlık Merkezi',
                        'description' => 'Genel hastane, devlet hastanesi, özel hastane, sağlık kompleksi, acil servis',
                        'keywords' => 'hastane hospital sağlık merkezi health center tıp merkezi medical center özel private devlet public acil emergency servis service yoğun bakım intensive care ameliyathane operating room poliklinik outpatient clinic'
                    ],
                    [
                        'title' => 'Özel Muayenehane & Klinik',
                        'description' => 'Özel doktor muayenehanesi, uzman kliniği, poliklinik, check-up merkezi',
                        'keywords' => 'muayenehane klinik clinic özel private doktor doctor hekim physician uzman specialist check-up medical checkup konsültasyon consultation'
                    ],
                    [
                        'title' => 'Diş Hekimliği & Ağız Sağlığı',
                        'description' => 'Diş tedavisi, implant, ortodonti, ağız cerrahisi, estetik diş hekimliği',
                        'keywords' => 'diş dental ağız oral implant ortodonti diş hekimi dentist ağız cerrahisi oral surgery diş beyazlatma whitening kanal tedavi root canal'
                    ],
                    [
                        'title' => 'Göz Sağlığı & Optisyenlik',
                        'description' => 'Göz muayenesi, gözlük, lens, görme bozuklukları, lazer göz ameliyatı',
                        'keywords' => 'göz eye optisyen optician gözlük lens görme vision göz doktoru ophthalmologist lazer laser göz ameliyatı eye surgery'
                    ],
                    [
                        'title' => 'Eczane & İlaç Sektörü',
                        'description' => 'Reçeteli ilaç, OTC ürünler, sağlık malzemeleri, vitamin takviyesi',
                        'keywords' => 'eczane pharmacy ilaç medicine farmasötik pharmaceutical reçete prescription otc over the counter vitamin supplement'
                    ],
                    [
                        'title' => 'Laboratuvar & Tıbbi Testler',
                        'description' => 'Kan tahlili, görüntüleme, patoloji, mikrobiyoloji, genetik testler',
                        'keywords' => 'laboratuvar lab tıbbi test medical test kan tahlil blood test patoloji pathology mikrobiyoloji microbiology genetik genetic'
                    ],
                    [
                        'title' => 'Estetik & Plastik Cerrahi',
                        'description' => 'Estetik operasyonlar, botox, dolgu, güzellik, anti-aging',
                        'keywords' => 'estetik aesthetic plastik cerrahi plastic surgery güzellik beauty botox filler dolgu anti-aging yaşlanma karşıtı'
                    ],
                    [
                        'title' => 'Alternatif Tıp & Wellness',
                        'description' => 'Homeopati, akupunktur, fitoterapii, yoga terapisi, reiki',
                        'keywords' => 'alternatif tıp alternative medicine wellness homeopati homeopathy akupunktur acupuncture fitoterapi herbal therapy yoga reiki'
                    ],
                    [
                        'title' => 'Fizyoterapi & Rehabilitasyon',
                        'description' => 'Fizik tedavi, manuel terapi, spor yaralanmaları, ortez protez',
                        'keywords' => 'fizyoterapi physiotherapy rehabilitasyon rehabilitation fizik tedavi physical therapy manuel terapi manual therapy spor yaralanmaları sports injuries'
                    ]
                ]
            ],
            'education' => [
                'title' => 'Eğitim & Öğretim',
                'emoji' => '🎓',
                'color' => 'yellow',
                'keywords' => 'eğitim, education, öğretim, teaching, okul, school, ders, lesson, kurs, course, öğretmen, teacher, akademi, academy, öğrenci, student, öğrenme, learning, ders verme, tutoring, özel ders, private lesson, online eğitim, online education, uzaktan eğitim, distance learning, e-learning, digital education, dijital eğitim, kurs, training, seminer, seminar, workshop, atölye, sertifika, certificate, diploma, mezuniyet, graduation, müfredat, curriculum, ders planı, lesson plan, öğretim materyali, educational material, kitap, book, not, note, sınav, exam, test, quiz, ödev, homework, proje, project',
                'items' => [
                    [
                        'title' => 'Okul & Eğitim Kurumları',
                        'description' => 'Anaokulu, ilkokul, ortaokul, lise, üniversite, meslek okulu',
                        'keywords' => 'okul school eğitim education kurum institution anaokulu kindergarten ilkokul elementary ortaokul middle school lise high school üniversite university kolej college'
                    ],
                    [
                        'title' => 'Özel Ders & Koçluk',
                        'description' => 'Birebir özel ders, grup dersleri, akademik koçluk, sınav hazırlık',
                        'keywords' => 'özel ders private lesson koçluk coaching mentoring tutor öğretmen teacher grup dersi group lesson akademik academic sınav exam hazırlık preparation'
                    ],
                    [
                        'title' => 'Online Eğitim Platformları',
                        'description' => 'E-learning, uzaktan eğitim, video dersler, interaktif kurslar',
                        'keywords' => 'online eğitim online education uzaktan eğitim distance learning e-learning platform video ders video lesson interaktif interactive'
                    ],
                    [
                        'title' => 'Dil Eğitimi & Çeviri',
                        'description' => 'İngilizce, Almanca, Fransızca, çeviri hizmetleri, dil kursları',
                        'keywords' => 'dil language İngilizce english almanca german fransızca french çeviri translation yabancı dil foreign language kurs course'
                    ],
                    [
                        'title' => 'Mesleki Eğitim & Sertifikasyon',
                        'description' => 'Teknik eğitim, meslek kursları, sertifika programları, kariyer geliştirme',
                        'keywords' => 'mesleki vocational sertifika certificate diploma kariyer career teknik technical meslek profession'
                    ],
                    [
                        'title' => 'Sanat & Yaratıcılık Eğitimi',
                        'description' => 'Müzik, resim, dans, tiyatro, yaratıcı yazarlık, sanat terapisi',
                        'keywords' => 'sanat art müzik music resim painting dans dance tiyatro theater yaratıcı creative yazarlık writing'
                    ],
                    [
                        'title' => 'Kişisel Gelişim & Yaşam Koçluğu',
                        'description' => 'Yaşam koçluğu, motivasyon, liderlik, iletişim, stres yönetimi',
                        'keywords' => 'kişisel gelişim personal development yaşam koçluğu life coaching motivasyon motivation liderlik leadership iletişim communication stres stress yönetim management'
                    ],
                    [
                        'title' => 'Çocuk Gelişimi & Aile Eğitimi',
                        'description' => 'Çocuk gelişimi, aile danışmanlığı, ebeveyn eğitimi, oyun terapisi',
                        'keywords' => 'çocuk gelişimi child development aile family danışmanlık counseling ebeveyn parent eğitim education oyun terapisi play therapy'
                    ]
                ]
            ]
        ];
    }
}