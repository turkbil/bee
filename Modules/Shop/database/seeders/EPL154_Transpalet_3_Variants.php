<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL154_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPL154')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (EPL154)'); return; }

        $variants = [
            [
                'sku' => 'EPL154-1150x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL154 - 1150×540 mm Çatal',
                'short_description' => 'Standart 1150×540 mm çatal ile EUR palete tam uyum, dar koridorlarda l2=400 mm gövde ve 1330 mm dönüş yarıçapı avantajı.',
                'long_description' => '<section><h3>1150×540 mm: Evrensel uyumlu standart</h3><p>1150×540 mm çatal konfigürasyonu, Avrupa standardı EUR paletlerle doğrudan uyum sağlayarak depolarda en çok tercih edilen kombinasyonu oluşturur. İXTİF EPL154’nin kompakt gövdesi (l2=400 mm) ve yalnızca 160 kg servis ağırlığı, tipik e-ticaret ve 3PL operasyonlarında raf arası hareketi hızlandırır. 24V/30Ah Li‑Ion batarya BMS sayesinde güvenle yönetilir, entegre 24V‑10A şarj cihazı ile fırsat şarjı yapılabilir. PU sürüş ve yük tekerleri düşük gürültü ve zemin dostu ilerleme sunarken, endüstriyel yüzer denge tekerleri eşik ve rampa geçişlerinde stabil tutuş sağlar.</p><p>Bu varyant, cross‑dock alanlarında kısa mesafe taşımalarda akıcı hızlanma ve hassas duruş sunar. Mekanik direksiyon ile birlikte creep modu, kapı eşikleri ve rampa başlarında milimetrik konumlandırma sağlar. 1330 mm dönüş yarıçapı raf girişlerinde ikinci manevraya olan ihtiyacı azaltır; 115 mm kaldırma yüksekliği ve 80 mm alçaltılmış çatal yüksekliği palet kanallarına sorunsuz giriş çıkış verir. Kısacası, standart EUR palet akışının yoğun olduğu depolarda optimum denge bu varyantla yakalanır.</p><h3>Operasyonel kazanımlar</h3><p>Kısa eğitim süresi, düşük bakım ihtiyacı ve modüler enerji yapısı toplam sahip olma maliyetini aşağı çeker. Yedek 30Ah batarya ile değiş-tokuş kullanımı vardiya sürekliliği sağlar. Tandem yük teker seçeneği düzensiz zeminlerde yük dağılımını iyileştirir. İXTİF EPL154 - 1150×540 mm, ergonomi ve dayanıklılığı bir araya getirerek depo verimliliğinde güçlü bir temel sunar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletli e‑ticaret sipariş toplama ve konsolidasyon'],
                    ['icon' => 'warehouse', 'text' => '3PL raf arası besleme ve çıkış hazırlığı'],
                    ['icon' => 'store', 'text' => 'Perakende dağıtımda mağaza bazlı sevkiyat hazırlığı'],
                    ['icon' => 'industry', 'text' => 'Üretim hücreleri arasında WIP palet taşıma'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG koli paletlerinin hat başına transferi'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça koridor içi akış yönetimi']
                ]
            ],
            [
                'sku' => 'EPL154-1220x540',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EPL154 - 1220×540 mm Çatal',
                'short_description' => '1220×540 mm çatal uzun yüklerde ekstra tutuş sağlar; Li‑Ion enerji ve PU tekerler ile sessiz ve verimli operasyon.',
                'long_description' => '<section><h3>1220×540 mm: Uzun paletlerde daha fazla destek</h3><p>1220×540 mm çatal, standart EUR palete ek olarak uzun ve hacimli yüklerde daha dengeli bir temas yüzeyi sağlar. İXTİF EPL154’nin yüzer denge tekerleri ve PU tekerlek yapısı, bu ek uzunlukla birlikte rampalarda ve eşiklerde tutarlı çekiş üretir. 24V/30Ah Li‑Ion bataryanın çıkarılabilir tasarımı, değişim süresini kısaltırken entegre şarj cihazı vardiya arasında enerji takviyesine izin verir. Metal korumalı batarya kapağı; servis erişimini hızlandırır ve güç konnektörünün kazara çıkmasını önlemeye yardımcı olur.</p><p>Kompakt l2=400 mm ölçüsü korunurken, daha uzun çatal boyu palet kanallarına tam girdide denge sağlar. 1330 mm dönüş yarıçapı ve creep modu sayesinde, raf ağzında palet hizalama işlemi risksiz ve düzenli yapılır. Gürültü seviyesi 74 dB(A) altındadır; bu, kapalı alan iş güvenliği ve konfor kriterlerine katkı sunar. 6% yüklü eğim kabiliyeti, yükleme alanına çıkışlarda yeterli performansı sağlar; yüksüz %16 ile iniş‑çıkış manevraları kontrollü ilerler.</p><h3>Hangi operasyonlara uygun?</h3><p>Uzun paletli beyaz eşya kutuları, mobilya parçaları veya baskı‑ambalaj rulosu taşıyan hatlarda bu varyantın ekstra boyu, palet üzerine daha geniş yayılım ve güven verir. Vardiyalar arasında akışı bozmadan fırsat şarjı yapılabilir; gerekirse yedek batarya ile çalışma penceresi uzatılır. Sonuç: uzun ve hacimli paletlerde denge, dar alanlarda ise manevra.</p></section>',
                'use_cases' => [
                    ['icon' => 'tv', 'text' => 'Beyaz eşya kutulu paletlerin depo içi hareketi'],
                    ['icon' => 'couch', 'text' => 'Mobilya parçaları ve aksesuar paletleri'],
                    ['icon' => 'print', 'text' => 'Matbaa ve ambalaj rulosu transferleri'],
                    ['icon' => 'warehouse', 'text' => 'Uzun paletli 3PL müşteri operasyonları'],
                    ['icon' => 'cart-shopping', 'text' => 'Büyük hacimli FMCG promosyon sevkiyat hazırlığı'],
                    ['icon' => 'building', 'text' => 'Kampüs içi malzeme lojistiği ve bakım depoları']
                ]
            ],
            [
                'sku' => 'EPL154-1150x685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF EPL154 - 1150×685 mm Geniş Çatal',
                'short_description' => '1150×685 mm geniş çatal, geniş tabanlı paletlerde dengeyi artırır; 1330 mm dönüş ve creep modu hassas konumlandırma sunar.',
                'long_description' => '<section><h3>1150×685 mm: Geniş tabanda güven</h3><p>Geniş tabanlı paletler için 1150×685 mm çatal aralığı, yük merkezinin daha dengeli dağılmasını sağlayarak taşıma sırasında yalpalamayı azaltır. İXTİF EPL154’nin endüstriyel yüzer denge tekerleri ve PU lastik kombinasyonu, geniş palet yüzeylerinde dahi zemine nazik ve sessiz hareket üretir. 24V/30Ah Li‑Ion batarya, BMS kontrollü güvenlik ve performansla desteklenir; entegre şarj cihazı gün içinde kısa molalarda enerji takviyesi yapılmasına olanak tanır.</p><p>Kompakt gövde (l2=400 mm) sayesinde geniş çatal olmasına rağmen raf arası yaklaşım kabiliyeti korunur. 1330 mm dönüş yarıçapı ve mekanik direksiyonun doğal geri bildirimi, dar dönüşlerde paletin köşe çarpma riskini azaltır. Metal kapaklı batarya yuvası bakım ve kontrolü kolaylaştırır. 115 mm kaldırma, 80 mm alçaltma yüksekliği ve 50/150/1150 mm çatal geometrisi, palet giriş‑çıkışlarındaki sürtünmeyi minimize eder.</p><h3>Operasyon türleri</h3><p>İçecek kasaları, evcil hayvan mamaları, ev‑ofis mobilya setleri ve geniş tabanlı taşımalarda bu varyantın sağladığı denge, ürün hasarını azaltmaya yardımcı olur. Düşük gürültü seviyesi ve hassas creep kontrolü, gece vardiyalarında konfor sunar. Filoda farklı palet tipleri kullanılıyorsa, 685 mm aralık standart 540 mm’ye göre daha geniş bir tolerans penceresi sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve geniş tabanlı paletler'],
                    ['icon' => 'paw', 'text' => 'Evcil hayvan maması paletlerinin taşınması'],
                    ['icon' => 'couch', 'text' => 'Ev‑ofis mobilya setlerinde depo içi akış'],
                    ['icon' => 'cart-shopping', 'text' => 'Hacimli perakende kampanya sevkiyatları'],
                    ['icon' => 'warehouse', 'text' => 'Karma paletli 3PL operasyonlarında genel kullanım'],
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş‑çıkışları']
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
        }

        $this->command->info('✅ Variants eklendi: EPL154 (3 varyant)');
    }
}
