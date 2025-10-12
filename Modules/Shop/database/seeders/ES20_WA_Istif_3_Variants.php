<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES20_WA_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'ES20-WA')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (ES20-WA)'); return; }

        $variants = [
            [
                'sku' => 'ES20-WA-STD3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES20-WA - 3000 mm Direk',
                'short_description' => '3000 mm kaldırma, 2020 mm kapalı ve 3465 mm açık direk ölçüleriyle dar koridorlarda raf istifi için dengeli çözüm. İki kademeli indirme ve 24V sistem konforu.',
                'long_description' => '<section><h3>3000 mm Direk ile Kompakt Güç</h3><p>ES20-WA 3000 mm konfigürasyonu, depo genelinde en çok tercih edilen raf yüksekliklerine odaklı optimizasyon sunar. 2020 mm kapalı ve 3465 mm açık direk, raflar arası görüşü korurken kaldırma fazında stabilite sağlar. 2000 kg kapasite ve 600 mm yük merkezi kombinasyonu, karışık ürün gruplarında güvenilir performans verir. 1.1 kW AC sürüş motoru ve 3.0 kW kaldırma motoru, 4.5/5.0 km/s sürüş hızlarıyla birlikte 0.11/0.16 m/s kaldırma hızını destekler. Bu sayede operatör, yoğun bir vardiyada bile konveyör, cross-dock ve raf besleme işlerini aralıksız sürdürebilir.</p><p>İki kademeli indirme fonksiyonu bu varyantın öne çıkan özelliğidir. Raf önüne yaklaşıldığında sistem, indirme hızını otomatik olarak yumuşatır; böylece cam şişe, kozmetik, elektronik bileşen kutuları gibi hassas içerikli koliler sarsıntısız biçimde yerleştirilir. 0.32/0.23 m/s indirme profili, operatöre tolerans tanıyarak hatalı bırakma riskini azaltır. Elektromanyetik frenleme, dur-kalk yoğunluklu alanlarda güvenli pozisyon alma ve rampa üstünde sabitleme için etkilidir.</p><p>Şasi ve teker kombinasyonu (PU ⌀230×75 tahrik; ⌀85×70 yük; ⌀130×55 denge) zeminde iz bırakmayan, sessiz ve kontrollü hareket sağlar. 18 mm yerden açıklık ve 88 mm çatal alt yüksekliği, rampalara yaklaşırken kontrollü geçişe izin verir. 60×190×1150 mm çatal ölçüsü ve 600 mm aralık, EUR paletlerle yüksek uyumluluk sağlar. 800 mm toplam genişlik ve 1589 mm dönüş yarıçapı, 2440–2465 mm koridorlarda rahat manevra kabiliyeti sunar.</p></section><section><h3>Enerji ve Altyapı</h3><p>24V/280Ah akü, tek vardiya boyunca kesintisiz çalışma için idealdir. Tesisin enerji politikalarına göre Li‑ion 205Ah veya 150Ah paketler tercih edilerek bakım azaltılabilir ve ara şarj pencereleri kısaltılabilir. Harici 24V-30A standart şarjın yanı sıra 50A/100A seçenekleri pik dönemlerde hızlı toparlanma sunar.</p><p>Sonuç olarak 3000 mm direk varyantı, raf yüksekliği 2.5–3.0 metre bandında yoğun istif yapan depoların “standart” çözümüdür. Esnek hız profilleri, sağlam frenleme ve kompakt şasi, hatasız ve hızlı bir operasyon ritmi kurmanıza yardım eder.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo raflarında 3 metre seviyesine düzenli yükleme'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret outbound hatlarında son raf istifi'],
                    ['icon' => 'store', 'text' => 'Perakende dağıtımda mağaza sipariş konsolidasyonu'],
                    ['icon' => 'industry', 'text' => 'Üretim alanında hat sonu palet istifi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında yüksek devirli raf replenishment'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında kontrollü ve yavaş indirme gerektiren işler']
                ]
            ],
            [
                'sku' => 'ES20-WA-FM3600',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES20-WA - 3600 mm Direk (Free Mast)',
                'short_description' => '3600 mm kaldırma ve serbest kaldırma ile kapı/kapak altında rahat çalışma. Daha yüksek raflar için kontrollü indirme ve güçlü 3.0 kW kaldırma.',
                'long_description' => '<section><h3>3600 mm Free Mast ile Esnek Çalışma</h3><p>3600 mm kaldırma seviyesi, 2320 mm kapalı ve 4065 mm açık direk değerleriyle yüksek raflı alanlarda güçlü bir alternatiftir. Serbest kaldırma, alan kısıtı bulunan kapı, rulo kapı veya mezzanine altında çatalı yükseltip direği kapalı tutmanıza imkân vererek taşıma ve konumlandırma adımlarını hızlandırır. İki kademeli indirme, raf hizasında hız profilini yumuşatır ve yük stabilitesini artırır. 2000 kg kapasite, 600 mm yük merkezi ve 1.1 kW AC sürüş + 3.0 kW kaldırma motoru kombinasyonu, yoğun sevkiyat dönemlerinde bile gereken moment rezervini sağlar.</p><p>Teker seti ve şasi, PU malzemenin zemin dostu doğasını kullanarak titreşim ve gürültüyü sınırlar. 18 mm yerden açıklık ve 88 mm çatal alt yüksekliği, rampalarda ve dock seviyelerinde kontrollü yaklaşım sunar. 60×190×1150 mm çatal ölçüsü ile 600 mm aralık, yaygın palet tiplerinde zahmetsiz giriş/çıkış sağlar. 800 mm gövde genişliği ve 1589 mm dönüş yarıçapı, 2440–2465 mm koridorlarda akıcı hareket olanağı tanır.</p></section><section><h3>Enerji Esnekliği ve Bakım</h3><p>Standart 24V/280Ah kurulum tek vardiya için idealdir; Li‑ion 205Ah/150Ah alternatifleri, yoğun çalışma saatlerinde ara şarjı kolaylaştırarak planlı duruşları kısaltır. Harici 24V-30A/50A/100A şarj seçenekleri, tesisin enerji altyapısına göre ölçeklenebilir. Elektromanyetik frenleme ve AC sürüş kontrolü, operatörün güvenliğini ve manevra hassasiyetini destekler.</p><p>Bu varyant, kapı altı çalışma zorunluluğu olan üretim hücreleri, soğuk oda girişleri ve mezzanine altı alanlar için ideal bir çözümdür. Daha yüksek raflara erişim ihtiyacını, dar koridor manevra kabiliyetinden ödün vermeden karşılar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Mezzanine kat altlarında kapalı direkle istif'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment alanında yüksek raf besleme'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda kapı girişlerinde serbest kaldırma ile çalışma'],
                    ['icon' => 'industry', 'text' => 'Üretim hattan çıkan paletlerin ara stok istifi'],
                    ['icon' => 'car', 'text' => 'Rampa yakını alanlarda kontrollü yük indirme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal riskli alanlarda titreşimi azaltılmış bırakma']
                ]
            ],
            [
                'sku' => 'ES20-WA-LI205',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF ES20-WA - Li-ion 205Ah',
                'short_description' => 'Li-ion 205Ah enerji ile hızlı ara şarj ve düşük bakım. Aynı şasi, 2.0 ton kapasite ve iki kademeli indirme ile hassas istifleme.',
                'long_description' => '<section><h3>Li-ion 205Ah: Bakım Derdini Azaltın</h3><p>Li‑ion 205Ah varyantı, akü değişim ve su dolum süreçlerini ortadan kaldırarak bakım programınızı sadeleştirir. BMS ile korunan hücre yapısı, deşarj eğrisini stabil tutar ve performans düşüşlerini minimize eder. 24V mimari üzerinde çalışan bu çözüm, ara şarj desteğiyle pik saatlerde kapasitenizi hızla toparlamanıza imkân verir. Aynı 2000 kg kapasite ve 600 mm yük merkezi korunurken 4.5/5.0 km/s sürüş hızı, 0.11/0.16 m/s kaldırma ve 0.32/0.23 m/s indirme değerleriyle verimlilikten ödün verilmez.</p><p>İki kademeli indirme, Li‑ion kullanımının sağladığı hızlı tepki ile birleştiğinde raf yerleştirmede arzu edilen hassas profili yaratır. PU teker seti ve 1170 kg servis ağırlığı, yük devrilmesine karşı kütlesel denge sağlar. Elektromanyetik frenleme yokuşta güvenli park ve rampa üstünde sabitleme sunar; AC sürüş kontrolü ise hızlanma ve yavaşlamaları çizgisel ve öngörülebilir kılar.</p></section><section><h3>Uygulama ve Altyapı</h3><p>Enerji altyapısı uygun tesislerde 24V-50A veya 24V-100A harici şarj çözümleri ile kısa pencerelerde hızlı kapasite geri kazanımı mümkündür. Telematik seçeneği ile akü döngüleri, şarj alışkanlıkları ve kullanım profilleri takip edilerek bakım öngörüleri güçlendirilebilir. Bu yaklaşım, çok vardiyalı e‑ticaret, 3PL ve FMCG depolarında kesintisiz akışa katkı verir.</p><p>Sonuç olarak Li‑ion 205Ah varyantı, düşük bakım ve yüksek çevrim kabiliyeti arayan operasyonlar için toplam sahip olma maliyetini aşağı çekerken, ES20‑WA şasisinin kanıtlanmış manevra ve güvenlik yeteneklerini korur.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E-ticarette çok vardiyalı akışlarda ara şarj ile kesintisiz operasyon'],
                    ['icon' => 'warehouse', 'text' => '3PL’de pik saatlerde enerji sürekliliği'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG hat beslemede hızlı dönüş'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de slot zamanlarına uyum'],
                    ['icon' => 'industry', 'text' => 'Üretim hattında enerji planlı ara şarj stratejisi'],
                    ['icon' => 'flask', 'text' => 'Kimya deposunda düşük bakım ve kontrollü süreç']
                ]
            ]
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->updateOrInsert(['sku' => $v['sku']], [
                'sku' => $v['sku'],
                'parent_product_id' => $m->product_id,
                'variant_type' => $v['variant_type'],
                'category_id' => $m->category_id,
                'brand_id' => $m->brand_id,
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant: {$v['sku']}");
        }
    }
}