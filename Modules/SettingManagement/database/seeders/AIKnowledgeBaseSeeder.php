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
                'answer' => 'İxtif olarak şu hizmetleri sunuyoruz: **Kiralama Hizmetleri:** Günlük, haftalık, aylık ve yıllık kiralama seçeneklerimiz vardır. **İkinci El Alım-Satım:** Kullanılmış ekipman alım-satımı ve takas hizmetleri. **Teknik Servis:** Tüm marka ve modellerde periyodik bakım, arıza onarımı ve 7/24 acil müdahale. **Yedek Parça:** Orijinal ve yan sanayi yedek parça tedariki, hızlı teslimat. Ayrıca operatör eğitimi, devreye alma ve danışmanlık hizmetlerimiz de mevcuttur.',
                'metadata' => [
                    'tags' => ['hizmetler', 'satış', 'kiralama', 'servis', 'günlük', 'haftalık', 'aylık', 'yıllık', '2. el'],
                    'internal_note' => 'Tüm hizmetlerin detaylı özeti - kiralama sürelerini vurgula.',
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

            // === FİNANSMAN & ÖDEME ===
            [
                'category' => 'Finansman & Ödeme',
                'question' => 'Hangi ödeme seçenekleri sunuyorsunuz?',
                'answer' => 'Nakit, havale/EFT, çek ve kredi kartı ile ödeme kabul ediyoruz. Kurumsal müşterilerimize 30-60-90 gün vadeli ödeme seçenekleri sunuyoruz. Büyük alımlarda taksitli ödeme planları yapabiliriz. Leasing ve finansman kuruluşlarıyla anlaşmalıyız.',
                'metadata' => [
                    'tags' => ['ödeme', 'taksit', 'vade'],
                    'internal_note' => 'Ödeme seçenekleri - esneklik vurgusu.',
                    'icon' => 'fas fa-credit-card',
                ],
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'category' => 'Finansman & Ödeme',
                'question' => 'Leasing ile satın alabilir miyim?',
                'answer' => 'Evet, iş ortağımız olan leasing şirketleri (ING Leasing, Garanti Leasing, Finans Leasing vb.) aracılığıyla forklift ve diğer ekipmanları finansal kiralama ile alabilirsiniz. Leasing avantajları: KDV ertelemesi, vergi avantajı, düşük peşinat, esnek vade seçenekleri. Leasing başvurunuzu hızlandırmak için size destek oluyoruz.',
                'metadata' => [
                    'tags' => ['leasing', 'finansal kiralama', 'kdv avantajı'],
                    'internal_note' => 'Leasing detayları - vergi avantajı vurgula.',
                    'icon' => 'fas fa-file-contract',
                ],
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'category' => 'Finansman & Ödeme',
                'question' => 'Kurumsal müşteriler için cari hesap açılıyor mu?',
                'answer' => 'Evet, sürekli alışveriş yapan kurumsal müşterilerimize özel cari hesap açıyoruz. Cari hesap ile vadeli alım yapabilir, toplu alımlarda indirimlerden faydalanabilirsiniz. Müşteri temsilciniz size özel fiyat listesi ve ödeme koşulları sunar. Referanslarınızı değerlendirdikten sonra cari hesap açılışı hızlıca tamamlanır.',
                'metadata' => [
                    'tags' => ['cari hesap', 'kurumsal', 'vadeli ödeme'],
                    'internal_note' => 'Cari hesap açma - kurumsal avantajlar.',
                    'icon' => 'fas fa-building',
                ],
                'is_active' => true,
                'sort_order' => 33,
            ],

            // === TESLİMAT & MONTAJ ===
            [
                'category' => 'Teslimat & Montaj',
                'question' => 'Teslimat süresi ne kadardır?',
                'answer' => 'Stokta bulunan ekipmanlar 1-3 iş günü içinde teslim edilir. Özel sipariş ürünlerde teslimat süresi 2-4 hafta arasında değişir. Acil ihtiyaçlarda aynı gün teslimat sağlayabiliriz (İstanbul ve çevre iller için). Teslimat öncesi size bilgilendirme yapılır, randevu alınır.',
                'metadata' => [
                    'tags' => ['teslimat', 'teslimat süresi', 'kargo'],
                    'internal_note' => 'Teslimat süreleri - stok durumuna göre bilgi ver.',
                    'icon' => 'fas fa-truck',
                ],
                'is_active' => true,
                'sort_order' => 34,
            ],
            [
                'category' => 'Teslimat & Montaj',
                'question' => 'Teslimat ücreti var mı?',
                'answer' => 'Belirli bir tutarın üzerindeki alımlarda (genelde 50.000 TL+) teslimat ücretsizdir. Küçük alımlarda ve uzak bölgelere teslimat için nakliye ücreti uygulanır. Teslimat ücreti mesafe ve ekipman büyüklüğüne göre belirlenir. Kiralama hizmetlerinde teslimat-montaj genelde ücretsizdir.',
                'metadata' => [
                    'tags' => ['teslimat ücreti', 'ücretsiz kargo'],
                    'internal_note' => 'Teslimat ücreti - alım tutarına göre yönlendir.',
                    'icon' => 'fas fa-shipping-fast',
                ],
                'is_active' => true,
                'sort_order' => 35,
            ],
            [
                'category' => 'Teslimat & Montaj',
                'question' => 'Kurulum ve devreye alma hizmeti veriyor musunuz?',
                'answer' => 'Evet, tüm ekipmanlar için profesyonel kurulum ve devreye alma hizmeti sunuyoruz. Teknik ekibimiz ekipmanı yerinde kurar, test eder, çalışır durumda teslim eder. Operatörlerinize temel kullanım eğitimi verilir. Kurulum sonrası ilk bakım ve kontrol hizmetimiz ücretsizdir.',
                'metadata' => [
                    'tags' => ['kurulum', 'montaj', 'devreye alma'],
                    'internal_note' => 'Kurulum hizmeti - eksiksiz hizmet vurgusu.',
                    'icon' => 'fas fa-tools',
                ],
                'is_active' => true,
                'sort_order' => 36,
            ],
            [
                'category' => 'Teslimat & Montaj',
                'question' => 'Tüm Türkiye\'ye teslimat yapıyor musunuz?',
                'answer' => 'Evet, Türkiye\'nin her yerine teslimat yapıyoruz. Merkez ofisimiz İstanbul, bölge ofislerimiz Ankara, İzmir, Bursa\'da bulunuyor. Uzak bölgelere kargo firmaları ile güvenli teslimat sağlıyoruz. Büyük ekipmanlar için özel nakliye araçları kullanılır. Teslimat sonrası teknik destek hizmetimiz tüm Türkiye\'yi kapsar.',
                'metadata' => [
                    'tags' => ['tüm türkiye', 'teslimat ağı', 'kargo'],
                    'internal_note' => 'Teslimat coğrafyası - yaygın hizmet ağı vurgula.',
                    'icon' => 'fas fa-map-marked-alt',
                ],
                'is_active' => true,
                'sort_order' => 37,
            ],

            // === GARANTİ & İADE ===
            [
                'category' => 'Garanti & İade',
                'question' => 'Garanti süresi ne kadardır?',
                'answer' => 'Yeni ekipmanlarda 12-24 ay (üretici garantisi), 2. el ekipmanlarda 6 ay garanti veriyoruz. Garanti kapsamında motor, hidrolik sistem, elektrik arızaları, kaynak ve yapısal problemler karşılanır. Kullanım hatası, bakım eksikliği ve kaza sonucu hasarlar garanti kapsamı dışındadır. Garanti süresi boyunca yedek parça ve işçilik ücretsizdir.',
                'metadata' => [
                    'tags' => ['garanti', 'garanti süresi', 'üretici garantisi'],
                    'internal_note' => 'Garanti detayları - kapsam ve süreyi açıkla.',
                    'icon' => 'fas fa-shield-alt',
                ],
                'is_active' => true,
                'sort_order' => 38,
            ],
            [
                'category' => 'Garanti & İade',
                'question' => 'Aldığım ürünü iade edebilir miyim?',
                'answer' => 'Ürün teslim alındığında hasarlı veya hatalı çıkarsa 7 gün içinde iade kabul edilir. Kullanılmamış ve ambalajı açılmamış ekipmanlarda 14 gün iade hakkı vardır. İade koşulları: ekipman kullanılmamış, hasarsız ve eksiksiz olmalı. Müşteri memnuniyetsizliğinde değişim veya ürün değiştirme seçenekleri sunuyoruz.',
                'metadata' => [
                    'tags' => ['iade', 'iade koşulları', 'değişim'],
                    'internal_note' => 'İade politikası - müşteri memnuniyeti odaklı.',
                    'icon' => 'fas fa-undo',
                ],
                'is_active' => true,
                'sort_order' => 39,
            ],
            [
                'category' => 'Garanti & İade',
                'question' => 'Garanti kapsamı dışında arıza olursa ne olur?',
                'answer' => 'Garanti kapsamı dışı arızalarda ücretli servis hizmeti sunuyoruz. Önce arıza tespiti yapılır, yedek parça ve işçilik maliyeti bildirilir, onayınızdan sonra tamir edilir. Anlaşmalı müşterilerimize özel servis indirimleri uygulanır. Acil arıza durumlarında öncelikli müdahale sağlıyoruz.',
                'metadata' => [
                    'tags' => ['garanti dışı', 'ücretli servis', 'tamir'],
                    'internal_note' => 'Garanti dışı servis - şeffaf fiyatlama vurgusu.',
                    'icon' => 'fas fa-wrench',
                ],
                'is_active' => true,
                'sort_order' => 40,
            ],

            // === İLETİŞİM & DESTEK ===
            [
                'category' => 'İletişim & Destek',
                'question' => 'Size nasıl ulaşabilirim?',
                'answer' => 'Telefon, WhatsApp, e-posta ve web sitemizden canlı destek ile bize ulaşabilirsiniz. Merkez ofis İstanbul\'da, bölge ofislerimiz Ankara, İzmir, Bursa\'da bulunuyor. Çalışma saatlerimiz: Hafta içi 08:00-18:00, Cumartesi 09:00-14:00. Acil teknik destek için 7/24 acil hat hizmetimiz mevcuttur.',
                'metadata' => [
                    'tags' => ['iletişim', 'destek', 'çalışma saatleri'],
                    'internal_note' => 'İletişim kanalları - erişilebilirlik vurgula.',
                    'icon' => 'fas fa-headset',
                ],
                'is_active' => true,
                'sort_order' => 41,
            ],
            [
                'category' => 'İletişim & Destek',
                'question' => 'Teknik destek hattınız var mı?',
                'answer' => 'Evet, 7/24 acil teknik destek hattımız mevcuttur. Ekipman arızası, acil yedek parça ihtiyacı veya teknik soru için destek hattımızı arayabilirsiniz. Deneyimli teknisyenlerimiz telefonda çözüm sunuyor, gerektiğinde yerinde müdahale ekibi gönderiyor. Anlaşmalı müşterilerimize özel destek numarası ve öncelikli hizmet sağlıyoruz.',
                'metadata' => [
                    'tags' => ['teknik destek', '7/24', 'acil hat'],
                    'internal_note' => 'Teknik destek - 7/24 erişim vurgula.',
                    'icon' => 'fas fa-phone-volume',
                ],
                'is_active' => true,
                'sort_order' => 42,
            ],
            [
                'category' => 'İletişim & Destek',
                'question' => 'Showroom\'unuzu ziyaret edebilir miyim?',
                'answer' => 'Elbette! Showroom\'larımızda tüm ürün gruplarımızı görebilir, test edebilirsiniz. Randevu alarak gelmenizi öneririz, böylece uzman ekibimiz size özel zaman ayırabilir. Ekipman demonstrasyonu yapar, ihtiyaçlarınıza uygun çözümler öneririz. Showroom adreslerimiz: İstanbul (Tuzla), Ankara, İzmir, Bursa.',
                'metadata' => [
                    'tags' => ['showroom', 'ziyaret', 'test sürüşü'],
                    'internal_note' => 'Showroom ziyareti - randevu almanın önemini vurgula.',
                    'icon' => 'fas fa-store',
                ],
                'is_active' => true,
                'sort_order' => 43,
            ],
            [
                'category' => 'İletişim & Destek',
                'question' => 'Online satın alma yapabilir miyim?',
                'answer' => 'Küçük ekipmanlar, yedek parça ve aksesuarlar için web sitemizden online sipariş verebilirsiniz. Forklift ve büyük ekipmanlar için önce ihtiyaç analizi yapıyoruz, danışmanlık hizmeti sunuyoruz. Online fiyat teklifi alabilir, ardından müşteri temsilcimizle iletişime geçerek siparişinizi tamamlayabilirsiniz.',
                'metadata' => [
                    'tags' => ['online satış', 'e-ticaret', 'sipariş'],
                    'internal_note' => 'Online satış - danışmanlık sürecini açıkla.',
                    'icon' => 'fas fa-shopping-cart',
                ],
                'is_active' => true,
                'sort_order' => 44,
            ],

            // === TEKNİK BİLGİLER (ÜRÜN ÖZELLİKLERİ) ===
            [
                'category' => 'Teknik Bilgiler',
                'question' => 'AGM batarya nedir, avantajları nelerdir?',
                'answer' => 'AGM (Absorbent Glass Mat) batarya, kurşun-asit bataryaya göre daha gelişmiş bir teknolojidir. Avantajları: Bakım gerektirmez (sulanmaz), daha uzun ömürlü (2-3 kat), hızlı şarj olur, derin deşarj hasarına karşı dayanıklıdır. Elektrikli forklift ve transpalet kullanıcıları için ideal, işletme maliyetini düşürür.',
                'metadata' => [
                    'tags' => ['agm batarya', 'batarya türleri', 'elektrikli forklift'],
                    'internal_note' => 'AGM batarya - uzun ömür ve düşük maliyet vurgula.',
                    'icon' => 'fas fa-battery-full',
                ],
                'is_active' => true,
                'sort_order' => 45,
            ],
            [
                'category' => 'Teknik Bilgiler',
                'question' => 'Li-Ion batarya mı AGM mi tercih etmeliyim?',
                'answer' => 'Li-Ion batarya: Fırsat şarjı yapılabilir, çok hızlı şarj (2-3 saat), çok uzun ömür (5000+ döngü), hafif. Ancak ilk yatırım maliyeti yüksek. AGM batarya: Orta maliyet, uzun ömür (1500+ döngü), güvenilir, standart şarj. Yoğun vardiyalı çalışmada Li-Ion, tek vardiyada AGM ekonomik olabilir. Kullanım profilinize göre danışmanlık veriyoruz.',
                'metadata' => [
                    'tags' => ['li-ion', 'agm', 'batarya karşılaştırma'],
                    'internal_note' => 'Batarya karşılaştırma - kullanım profiline göre yönlendir.',
                    'icon' => 'fas fa-balance-scale-right',
                ],
                'is_active' => true,
                'sort_order' => 46,
            ],
            [
                'category' => 'Teknik Bilgiler',
                'question' => 'Duplex, Triplex mast nedir, farkları nelerdir?',
                'answer' => 'Mast, forkliftin kaldırma direkidir. Duplex (2 kademeli): Standart kaldırma, 3-4m yükseklik. Triplex (3 kademeli): Yüksek kaldırma, 4.5-6m yükseklik, dar alanlarda tavan yüksekliği sınırlı ise tercih edilir. Standart mast: Tek kademe, düşük kaldırma. Yüksek raf kullanan depolarda Triplex önerilir.',
                'metadata' => [
                    'tags' => ['duplex', 'triplex', 'mast türleri', 'kaldırma yüksekliği'],
                    'internal_note' => 'Mast türleri - depo yüksekliğine göre yönlendir.',
                    'icon' => 'fas fa-arrows-alt-v',
                ],
                'is_active' => true,
                'sort_order' => 47,
            ],
            [
                'category' => 'Teknik Bilgiler',
                'question' => 'Soğuk hava deposu için hangi ekipmanları kullanmalıyım?',
                'answer' => 'Soğuk hava deposu (-30°C\'ye kadar) için özel soğuk ortam uyumlu elektrikli forkliftler ve transpaletler öneriyoruz. Bu ekipmanlarda özel yalıtımlı batarya, donmaya karşı dayanıklı hidrolik sıvı ve güçlendirilmiş elektronik sistem vardır. Paslanmaz çelik gövdeli modeller gıda hijyeni için idealdir. Seri numarasında "ETC" (Extreme Temperature Conditions) olan modelleri tercih edin.',
                'metadata' => [
                    'tags' => ['soğuk depo', 'etc', 'soğuk ortam ekipman'],
                    'internal_note' => 'Soğuk depo - ETC serisi ürünleri öner.',
                    'icon' => 'fas fa-snowflake',
                ],
                'is_active' => true,
                'sort_order' => 48,
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
