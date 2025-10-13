<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SettingManagement\App\Models\AIKnowledgeBase;

class AIKnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tenant ID al - tenant context'inden veya default tenant (1)
        $tenantId = tenant('id') ?? 1;

        if (!$tenantId) {
            $this->command->warn('⚠️  Tenant ID bulunamadı');
            return;
        }

        $this->command->info('📝 Tenant ID: ' . $tenantId);

        $knowledgeItems = [
            // === FİRMA HAKKINDA ===
            [
                'category' => 'Firma Hakkında',
                'question' => 'İxtif kimdir, ne yapar?',
                'answer' => 'İxtif, "Türkiye\'nin İstif Pazarı" sloganıyla depolama ve istif ekipmanları alanında lider bir firmadır. Forklift satışı, kiralama, teknik servis, yedek parça tedariki ve 2. el ürün hizmetleri sunuyoruz. Elektrikli forkliftler, dizel forkliftler, LPG forkliftler, transpaletler, istif makineleri, reach truck\'lar ve AMR otonom mobil robotlar gibi geniş bir ürün yelpazesine sahibiz.',
                'metadata' => [
                    'tags' => ['ixtif', 'firma tanıtımı', 'hakkımızda'],
                    'internal_note' => 'Ana tanıtım mesajı - her yeni müşteriye bu bilgi verilebilir.',
                    'icon' => 'fas fa-building',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'Firma Hakkında',
                'question' => 'İxtif\'in vizyonu nedir?',
                'answer' => 'Vizyonumuz, Türkiye\'nin en güvenilir istif ve intralojiştik markası olmaktır. Müşterilerimize yenilikçi, erişilebilir ve şeffaf hizmet sunarak sektörde standart belirlemeyi hedefliyoruz. Güvenilirlik, yenilikçilik, erişilebilirlik ve şeffaflık değerlerimizle hareket ediyoruz.',
                'metadata' => [
                    'tags' => ['vizyon', 'misyon', 'değerler'],
                    'internal_note' => 'Firma vizyonu ve değerleri.',
                    'icon' => 'fas fa-eye',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'Firma Hakkında',
                'question' => 'Hangi sektörlere hizmet veriyorsunuz?',
                'answer' => 'Lojistik, e-ticaret, üretim, perakende, gıda, soğuk zincir, otomotiv, tekstil ve inşaat sektörlerine özel çözümler sunuyoruz. Her sektörün kendine özgü ihtiyaçlarını anlıyor ve en uygun istif ekipmanı çözümünü öneriyoruz.',
                'metadata' => [
                    'tags' => ['sektörler', 'lojistik', 'e-ticaret', 'üretim'],
                    'internal_note' => 'Sektörel yelpaze - müşterinin sektörüne göre özelleştirebilirsin.',
                    'icon' => 'fas fa-industry',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],

            // === ÜRÜNLER ===
            [
                'category' => 'Ürünler',
                'question' => 'Hangi forklift türleri var?',
                'answer' => 'Elektrikli forkliftler (kapalı alan kullanımı için çevre dostu, sessiz), dizel forkliftler (açık alan ve ağır işler için güçlü), LPG forkliftler (hem kapalı hem açık alan için hibrit çözüm) sunuyoruz. Her birinin kapasitesi, kaldırma yüksekliği ve kullanım alanı farklıdır.',
                'metadata' => [
                    'tags' => ['forklift türleri', 'elektrikli', 'dizel', 'lpg'],
                    'internal_note' => 'Forklift türleri - müşterinin kullanım alanına göre yönlendir.',
                    'icon' => 'fas fa-truck-loading',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'category' => 'Ürünler',
                'question' => 'Transpalet nedir, ne işe yarar?',
                'answer' => 'Transpalet, paletli yüklerin kısa mesafeli taşınması için kullanılan manuel veya elektrikli istif ekipmanıdır. Depolarda, market ve mağazalarda, yükleme rampalarında sıkça kullanılır. Elektrikli transpaletler operatör yorgunluğunu azaltır ve iş verimliliğini artırır.',
                'metadata' => [
                    'tags' => ['transpalet', 'palet taşıma', 'elektrikli transpalet'],
                    'internal_note' => 'Transpalet tanımı ve kullanım alanları.',
                    'icon' => 'fas fa-pallet',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'category' => 'Ürünler',
                'question' => 'Reach truck nedir?',
                'answer' => 'Reach truck (uzanma maşası), dar koridorlarda yüksek raflara yük yerleştirmek için tasarlanmış özel bir forklift türüdür. Direklerini öne doğru uzatabileceği için yüksek depolama verimliliği sağlar. E-ticaret ve lojistik depolarında çok yaygındır.',
                'metadata' => [
                    'tags' => ['reach truck', 'dar koridor', 'yüksek raf'],
                    'internal_note' => 'Reach truck açıklaması - depo verimliliği vurgusu.',
                    'icon' => 'fas fa-warehouse',
                ],
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'category' => 'Ürünler',
                'question' => 'İstif makineleri nasıl çalışır?',
                'answer' => 'İstif makineleri (stacker), paletli yükleri yerden kaldırarak raflara istiflemek için kullanılır. Manuel (hidrolik pompalı), yarı elektrikli (kaldırma elektrikli, hareket manuel) ve tam elektrikli modelleri vardır. Küçük depolarda, dar alanlarda ideal çözümdür.',
                'metadata' => [
                    'tags' => ['istif makinesi', 'stacker', 'elektrikli stacker'],
                    'internal_note' => 'İstif makineleri - küçük işletmeler için uygun maliyetli seçenek.',
                    'icon' => 'fas fa-level-up-alt',
                ],
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'category' => 'Ürünler',
                'question' => 'Elektrikli forklift mi dizel mi almalıyım?',
                'answer' => 'Kapalı alanlarda (depo, fabrika) elektrikli forklift idealdir: egzoz gazı yok, sessiz, bakım maliyeti düşük. Açık alanlarda veya zorlu koşullarda dizel forklift daha güçlüdür. LPG forkliftler ise her iki ortamda da kullanılabilir. İhtiyacınıza göre en uygun modeli önerebiliriz.',
                'metadata' => [
                    'tags' => ['elektrikli vs dizel', 'forklift karşılaştırma'],
                    'internal_note' => 'Müşterinin kullanım ortamını sor ve ona göre yönlendir.',
                    'icon' => 'fas fa-balance-scale',
                ],
                'is_active' => true,
                'sort_order' => 8,
            ],

            // === HİZMETLER ===
            [
                'category' => 'Hizmetler',
                'question' => 'Hangi hizmetleri sunuyorsunuz?',
                'answer' => 'İxtif olarak forklift satışı, kiralama, teknik servis, yedek parça tedariki ve 2. el ürün alım-satımı hizmetleri veriyoruz. Ayrıca operatör eğitimi, periyodik bakım paketleri ve 7/24 teknik destek sunuyoruz.',
                'metadata' => [
                    'tags' => ['hizmetler', 'satış', 'kiralama', 'servis'],
                    'internal_note' => 'Tüm hizmetlerin özeti.',
                    'icon' => 'fas fa-cogs',
                ],
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'category' => 'Hizmetler',
                'question' => 'Teknik servis hizmetiniz nasıl çalışır?',
                'answer' => 'Teknik servis ekibimiz, tüm marka ve modellerde periyodik bakım, arıza onarımı ve acil müdahale hizmeti sunar. Orijinal yedek parça kullanırız, işlerimiz garanti kapsamındadır. Anlaşmalı müşterilerimize öncelikli servis ve indirimli yedek parça hizmeti sağlıyoruz.',
                'metadata' => [
                    'tags' => ['teknik servis', 'bakım', 'onarım'],
                    'internal_note' => 'Servis kalitesi ve orijinal yedek parça kullanımını vurgula.',
                    'icon' => 'fas fa-wrench',
                ],
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'category' => 'Hizmetler',
                'question' => 'Yedek parça temin edebiliyor musunuz?',
                'answer' => 'Evet, tüm istif ekipmanları için orijinal ve yan sanayi yedek parça tedariki yapıyoruz. Geniş stok ağımızla hızlı teslimat sağlıyoruz. Acil parça ihtiyaçlarında aynı gün kargo seçeneğimiz mevcuttur.',
                'metadata' => [
                    'tags' => ['yedek parça', 'stok', 'hızlı teslimat'],
                    'internal_note' => 'Yedek parça stoku ve hızlı tedarik avantajı.',
                    'icon' => 'fas fa-box-open',
                ],
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'category' => 'Hizmetler',
                'question' => 'Operatör eğitimi veriyor musunuz?',
                'answer' => 'Evet, forklift ve diğer istif ekipmanları için sertifikalı operatör eğitimi sunuyoruz. İş Sağlığı ve Güvenliği (İSG) mevzuatına uygun teorik ve pratik eğitim veriyoruz. Eğitim sonunda katılımcılar belgelerini alırlar.',
                'metadata' => [
                    'tags' => ['operatör eğitimi', 'forklift sertifikası', 'isg'],
                    'internal_note' => 'Eğitim hizmeti - iş güvenliği ve yasal uyumluluk vurgusu.',
                    'icon' => 'fas fa-user-graduate',
                ],
                'is_active' => true,
                'sort_order' => 12,
            ],

            // === TEKNİK ===
            [
                'category' => 'Teknik',
                'question' => 'Forklift kapasitesi nasıl belirlenir?',
                'answer' => 'Forklift kapasitesi, kaldırabileceği maksimum ağırlığı (ton cinsinden) ifade eder. 1.5 ton, 2 ton, 3 ton, 5 ton gibi değişir. Kaldırma yüksekliği arttıkça kapasite azalır (moment etkisi). İhtiyacınızı belirlerken taşıyacağınız en ağır yükü ve kaldırma yüksekliğini dikkate almalısınız.',
                'metadata' => [
                    'tags' => ['kapasite', 'tonaj', 'kaldırma yüksekliği'],
                    'internal_note' => 'Kapasite seçimi - müşterinin yük ağırlığını sor.',
                    'icon' => 'fas fa-weight-hanging',
                ],
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'category' => 'Teknik',
                'question' => 'Elektrikli forklift şarj süresi ne kadardır?',
                'answer' => 'Standart elektrikli forkliftlerde tam şarj süresi 6-8 saat arasındadır. Fırsat şarjı (ara şarj) özelliği olan modellerde mola zamanlarında kısa şarjlar yapılabilir. Hızlı şarj sistemleriyle bu süre 2-3 saate düşebilir.',
                'metadata' => [
                    'tags' => ['şarj süresi', 'elektrikli forklift', 'batarya'],
                    'internal_note' => 'Şarj süresi bilgisi - vardiya sistemine göre öneri yapabilirsin.',
                    'icon' => 'fas fa-battery-full',
                ],
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'category' => 'Teknik',
                'question' => 'Forklift bakımı ne sıklıkla yapılmalı?',
                'answer' => 'Rutin bakım 250-500 çalışma saatinde bir (yaklaşık 3-6 ayda bir) yapılmalıdır. Yoğun kullanımda daha sık bakım gerekir. Günlük kontroller (fren, direksiyon, hidrolik sızıntı) operatör tarafından yapılmalıdır. Periyodik bakım ile ekipmanın ömrü uzar ve arızalar önlenir.',
                'metadata' => [
                    'tags' => ['bakım', 'periyodik bakım', 'çalışma saati'],
                    'internal_note' => 'Bakım sıklığı - düzenli bakımın önemini vurgula.',
                    'icon' => 'fas fa-calendar-alt',
                ],
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'category' => 'Teknik',
                'question' => 'Forklift güvenlik önlemleri nelerdir?',
                'answer' => 'Operatör sertifikası zorunludur. Emniyet kemeri takılmalı, hız limitlerine uyulmalı, yük dengesi kontrol edilmeli. Forklift lastikleri, frenler ve farlar düzenli kontrol edilmelidir. Arka görüş aynası, sesli/ışıklı uyarıcılar bulunmalıdır. İSG mevzuatına uygun kullanım esastır.',
                'metadata' => [
                    'tags' => ['güvenlik', 'isg', 'forklift emniyeti'],
                    'internal_note' => 'Güvenlik standartları - İSG mevzuatı vurgusu.',
                    'icon' => 'fas fa-hard-hat',
                ],
                'is_active' => true,
                'sort_order' => 16,
            ],

            // === KİRALAMA ===
            [
                'category' => 'Kiralama',
                'question' => 'Forklift kiralama avantajları nelerdir?',
                'answer' => 'Kiralama ile sermaye yatırımı yapmazsınız, nakit akışınızı korursunuz. Bakım ve onarım firmaya aittir. Sezonluk ihtiyaçlarda veya kısa süreli projelerde çok mantıklıdır. Esnek kiralama süreleri (günlük, haftalık, aylık, yıllık) sunuyoruz. İhtiyaç değiştiğinde ekipman değişikliği kolayca yapılabilir.',
                'metadata' => [
                    'tags' => ['kiralama', 'avantajlar', 'esneklik'],
                    'internal_note' => 'Kiralama avantajları - nakit akışı ve esneklik vurgusu.',
                    'icon' => 'fas fa-handshake',
                ],
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'category' => 'Kiralama',
                'question' => 'Hangi sürelerde kiralama yapıyorsunuz?',
                'answer' => 'Günlük, haftalık, aylık ve uzun süreli (1-5 yıl) kiralama seçeneklerimiz vardır. Sezonluk ihtiyaçlar için özel kampanyalar düzenliyoruz. Kısa süreli acil ihtiyaçlarda aynı gün teslimat sağlıyoruz.',
                'metadata' => [
                    'tags' => ['kiralama süresi', 'günlük', 'aylık', 'yıllık'],
                    'internal_note' => 'Kiralama süre seçenekleri - müşterinin projesine göre öner.',
                    'icon' => 'fas fa-calendar-check',
                ],
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'category' => 'Kiralama',
                'question' => 'Kiralık forkliftler hangi durumdadır?',
                'answer' => 'Kiralık filomuz düzenli bakımlı, güvenlik sertifikalı ve çalışır durumdadır. Her ekipman teslim öncesi teknik kontrolden geçer. Kiralama süresi boyunca bakım ve onarım hizmetimiz dahildir. Arızada yedek ekipman desteği sağlıyoruz.',
                'metadata' => [
                    'tags' => ['kiralık ekipman', 'bakımlı', 'garanti'],
                    'internal_note' => 'Kiralık ekipman kalitesi - güven verici mesaj.',
                    'icon' => 'fas fa-certificate',
                ],
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'category' => 'Kiralama',
                'question' => 'Satın alma mı kiralama mı daha avantajlı?',
                'answer' => 'Kısa vadeli (1 yıla kadar) veya sezonluk kullanımda kiralama avantajlıdır. 3 yıl ve üzeri sürekli kullanımda satın almak daha ekonomik olabilir. Nakit akışınızı korumak, bakım yükünden kurtulmak istiyorsanız kiralama idealdir. Uzun vadeli yatırım yapmak, ekipmanı kendinize ait görmek istiyorsanız satın alma uygundur.',
                'metadata' => [
                    'tags' => ['satın alma vs kiralama', 'karşılaştırma'],
                    'internal_note' => 'Müşterinin kullanım süresini ve bütçesini sor, ona göre yönlendir.',
                    'icon' => 'fas fa-balance-scale',
                ],
                'is_active' => true,
                'sort_order' => 20,
            ],

            // === 2. EL ===
            [
                'category' => '2. El',
                'question' => 'İkinci el forklift güvenilir midir?',
                'answer' => 'Kaliteli 2. el forklift, düzenli bakımı yapılmış ve düşük çalışma saatine sahipse çok güvenilirdir. Biz tüm 2. el ekipmanları uzman teknisyenlerimize kontrol ettiririz, gerekli bakımları yaparız ve garanti ile satarız. Müşterilerimize ekipmanın servis geçmişini ve durum raporunu sunuyoruz.',
                'metadata' => [
                    'tags' => ['2. el', 'güvenilirlik', 'kontrol'],
                    'internal_note' => '2. el kalite standartları - güven verici mesaj.',
                    'icon' => 'fas fa-shield-alt',
                ],
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'category' => '2. El',
                'question' => 'İkinci el alırken nelere dikkat etmeliyim?',
                'answer' => 'Çalışma saati (5000 saatten az ideal), servis geçmişi (düzenli bakım yapılmış mı), ekipman durumu (motor, hidrolik, fren, lastik kontrolü), garanti süresi (en az 6 ay), satıcının güvenilirliği. İxtif olarak tüm 2. el ekipmanlarımızda bu kriterleri sağlıyor ve detaylı durum raporu sunuyoruz.',
                'metadata' => [
                    'tags' => ['2. el alım', 'dikkat edilecekler', 'çalışma saati'],
                    'internal_note' => 'İkinci el alım rehberi - kriterlerimizi vurgula.',
                    'icon' => 'fas fa-clipboard-check',
                ],
                'is_active' => true,
                'sort_order' => 22,
            ],
            [
                'category' => '2. El',
                'question' => 'İkinci el forklift garanti veriyor musunuz?',
                'answer' => 'Evet, sattığımız tüm 2. el ekipmanlara minimum 6 ay garanti veriyoruz. Garanti kapsamında motor, hidrolik sistem ve elektrik arızalarını karşılıyoruz. Garanti sonrası da uygun ücretli servis desteğimiz devam eder.',
                'metadata' => [
                    'tags' => ['2. el garanti', 'garanti süresi'],
                    'internal_note' => '2. el garanti bilgisi - güven verici.',
                    'icon' => 'fas fa-award',
                ],
                'is_active' => true,
                'sort_order' => 23,
            ],
            [
                'category' => '2. El',
                'question' => 'Eski ekipmanımı size satabilir miyim?',
                'answer' => 'Evet, kullanılmış forklift, transpalet ve diğer istif ekipmanlarınızı değerlendirip satın alabiliriz. Uzman ekibimiz yerinde inceleme yapar, ekipmanın durumuna ve piyasa koşullarına göre adil bir teklif sunarız. Takas imkanlarımız da mevcuttur.',
                'metadata' => [
                    'tags' => ['2. el alım', 'takas', 'satış'],
                    'internal_note' => 'Eski ekipman alımı - takas seçeneği vurgula.',
                    'icon' => 'fas fa-recycle',
                ],
                'is_active' => true,
                'sort_order' => 24,
            ],

            // === SEKTÖREL ÇÖZÜMLER ===
            [
                'category' => 'Sektörel Çözümler',
                'question' => 'Lojistik depolar için hangi ekipmanları önerirsiniz?',
                'answer' => 'Lojistik depolar için yüksek kapasiteli forkliftler, reach truck\'lar (dar koridorlar için), elektrikli transpaletler ve AMR otonom robotlar öneriyoruz. Yüksek raf sistemlerinde reach truck, yoğun paletleme işlerinde elektrikli forklift idealdir. Depo büyüklüğüne ve iş yoğunluğuna göre ekipman planlaması yapabiliriz.',
                'metadata' => [
                    'tags' => ['lojistik', 'depo çözümleri', 'reach truck'],
                    'internal_note' => 'Lojistik sektörü özel çözümler - depo verimliliği vurgusu.',
                    'icon' => 'fas fa-shipping-fast',
                ],
                'is_active' => true,
                'sort_order' => 25,
            ],
            [
                'category' => 'Sektörel Çözümler',
                'question' => 'E-ticaret firmaları için ne önerirsiniz?',
                'answer' => 'E-ticaret depoları hızlı sipariş hazırlama gerektirir. Elektrikli transpaletler (hızlı paket toplama), istif makineleri (raflardan çekme) ve AMR robotlar (otomatik taşıma) öneriyoruz. Dar koridorlu depolarda reach truck verimliliği artırır. Sezonluk yoğunluklar için esnek kiralama paketlerimiz var.',
                'metadata' => [
                    'tags' => ['e-ticaret', 'sipariş hazırlama', 'amr'],
                    'internal_note' => 'E-ticaret sektörü - hız ve otomasyon vurgusu.',
                    'icon' => 'fas fa-shopping-cart',
                ],
                'is_active' => true,
                'sort_order' => 26,
            ],
            [
                'category' => 'Sektörel Çözümler',
                'question' => 'Gıda sektörü için özel çözümleriniz var mı?',
                'answer' => 'Gıda ve soğuk zincir depoları için paslanmaz çelik ekipmanlar, soğuk hava deposu uyumlu forkliftler ve hijyen standartlarına uygun transpaletler sunuyoruz. Elektrikli forkliftler egzoz gazı çıkarmadığı için gıda depolarında tercih edilir. HACCP standartlarına uygun ekipman sağlıyoruz.',
                'metadata' => [
                    'tags' => ['gıda', 'soğuk zincir', 'hijyen', 'haccp'],
                    'internal_note' => 'Gıda sektörü - hijyen ve soğuk zincir uyumluluğu vurgula.',
                    'icon' => 'fas fa-snowflake',
                ],
                'is_active' => true,
                'sort_order' => 27,
            ],

            // === AMR & OTOMASYON ===
            [
                'category' => 'AMR & Otomasyon',
                'question' => 'AMR otonom mobil robot nedir?',
                'answer' => 'AMR (Autonomous Mobile Robot), yapay zeka ile kendi yolunu bulabilen, insan müdahalesi olmadan yük taşıyabilen robotlardır. Depoda palet ve malzeme taşıma işlerini otomatik yapar. Operatör ihtiyacını azaltır, hata oranını düşürür, 7/24 çalışabilir. Endüstri 4.0 dönüşümünün önemli bir parçasıdır.',
                'metadata' => [
                    'tags' => ['amr', 'otonom robot', 'endüstri 4.0'],
                    'internal_note' => 'AMR tanımı - otomasyon ve verimlilik vurgusu.',
                    'icon' => 'fas fa-robot',
                ],
                'is_active' => true,
                'sort_order' => 28,
            ],
            [
                'category' => 'AMR & Otomasyon',
                'question' => 'AMR robotlar hangi işletmelere uygundur?',
                'answer' => 'Yüksek iş hacmi olan lojistik merkezleri, e-ticaret depoları, üretim tesisleri ve büyük perakende depoları için idealdir. Tekrarlayan taşıma işlerinin olduğu, operatör bulmanın zorlaştığı, 7/24 operasyon gereken yerlerde AMR büyük verimlilik sağlar. Küçük ve orta ölçekli işletmeler için kiralama seçeneği de mevcuttur.',
                'metadata' => [
                    'tags' => ['amr kullanım', 'lojistik otomasyon'],
                    'internal_note' => 'AMR hedef kitle - işletme büyüklüğüne göre yönlendir.',
                    'icon' => 'fas fa-industry',
                ],
                'is_active' => true,
                'sort_order' => 29,
            ],
            [
                'category' => 'AMR & Otomasyon',
                'question' => 'AMR entegrasyonu nasıl yapılır?',
                'answer' => 'AMR sistemleri mevcut depo altyapınıza kolayca entegre edilir. Önce depo haritalaması yapılır, rotalar belirlenir, yazılım konfigürasyonu tamamlanır. Mevcut WMS (Warehouse Management System) sisteminize bağlanabilir. Kurulum süresi depo büyüklüğüne göre 2-6 hafta arası sürer. Eğitim ve teknik destek hizmetimiz dahildir.',
                'metadata' => [
                    'tags' => ['amr entegrasyon', 'kurulum', 'wms'],
                    'internal_note' => 'AMR entegrasyon süreci - kolay kurulum vurgusu.',
                    'icon' => 'fas fa-network-wired',
                ],
                'is_active' => true,
                'sort_order' => 30,
            ],
        ];

        foreach ($knowledgeItems as $item) {
            // Tenant ID ekle
            $item['tenant_id'] = $tenantId;

            AIKnowledgeBase::withoutGlobalScope('tenant')->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'question' => $item['question'],
                ],
                $item
            );
        }

        $this->command->info('✅ AI Knowledge Base seeded successfully! (' . count($knowledgeItems) . ' items)');
    }
}
