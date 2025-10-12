<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX2_4_SiparisToplama_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'JX2-4')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (JX2-4)'); return; }

        $variants = [
            [
                'sku' => 'JX2-4-AGM-224',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF JX2-4 AGM 24V 224Ah',
                'short_description' => 'Bakım gerektirmeyen AGM 24V/224Ah batarya ile planlı mola şarjına uygun, 4.5 mph sabit hız ve 192” erişim sunan dar koridor sipariş toplayıcı. İç mekân zeminlerinde sessiz ve verimli çalışır.',
                'long_description' => '<section><h3>AGM ile Bakımsız Enerji</h3><p>AGM konfigürasyonu, bakım gereksinimini minimize ederek depolarda öngörülebilir çalışma akışı kurar. 24V platform JX2-4, 224Ah AGM ile fırsat şarjına uygun döngüler kurmanızı kolaylaştırır; 35A/40A şarj akımı seçenekleri mola aralarında dolum planlarını destekler. 4 kW sürüş ve 3 kW kaldırma motoru, 4.5 mph sabit seyir ve 25.6/31.5 fpm kaldırma hızlarını stabil tutar. 65” dönüş yarıçapı, 36” genişlik ve 108.3” toplam uzunluk kombinasyonu dar koridor kısıtlarını pratik manevralarla aşar.</p></section><section><h3>Dikey Erişim ve Güvenlik</h3><p>192” maksimum çatal yüksekliği ve 295” mast tam yükseklik, üst raflarda güvenli erişim sağlar. Rejeneratif servis freninin getirdiği enerji geri kazanımı, elektromanyetik park freni ile birleşerek dur-kalk senaryolarında güven hissi verir. Poly tekerlek seti (10.25 x 5 in sürüş, 6.5 x 4.7 in yük) sessiz ve temiz iç mekân operasyonlarını destekler. 70 dB(A) gürültü seviyesi uzun vardiyalarda yorgunluğu azaltır.</p></section><section><h3>Planlı Operasyon ve Maliyet</h3><p>AGM, bakımsız yapısı ve kapalı hücre kimyasıyla teknik personel yükünü hafifletir. Şarj altyapısı kuruluyken vardiya sonu tam şarj veya gün içi kısa fırsat şarjları ile yüksek kullanılabilirlik elde edilir. İç mekân, düz ve pürüzsüz zemin şartına uygun işletmelerde, e-ticaret ve 3PL gibi yoğun toplama hatlarında verimli sonuçlar sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL merkezlerinde planlı mola şarjıyla çok vardiyalı sipariş toplama'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret raflarında hızlı SKU değişimlerinde stabil hız kontrolü'],
                    ['icon' => 'store', 'text' => 'Perakende kampanya dönemlerinde bakımsız enerjiyle kesintisiz akış'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk depoda kısa dur-kalk çevrimlerinde düşük bakım gereksinimi'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında güvenli üst raf erişimi ve sessiz çalışma'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında molalarda fırsat şarjıyla yüksek vardiya kullanılabilirliği']
                ]
            ],
            [
                'sku' => 'JX2-4-LA-340',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF JX2-4 Kurşun-Asit 24V 340Ah',
                'short_description' => 'Ekonomik 24V/340Ah kurşun-asit batarya ile uzun süreli çalışma döngülerine uygun; 192” kaldırma ve 65” dönüş yarıçapıyla dar koridor performansı sunar. Rejeneratif fren ve elektromanyetik park frenine sahiptir.',
                'long_description' => '<section><h3>Uygun Maliyetli Süreklilik</h3><p>Kurşun-asit 340Ah batarya, yaygın servis altyapısıyla düşük toplam sahip olma maliyeti hedefleyen depolar için idealdir. Şarj odaları ve akü değiştirme istasyonları bulunan tesislerde JX2-4, 4.5 mph hızını ve kaldırma performansını gün boyu korur. 117.5” sağ açılı istif koridoru ve 42” çatal, standart 48” paletlerle hızlı yaklaşım ve ayrılma hareketleri sağlar.</p></section><section><h3>Güvenli Dikey Operasyon</h3><p>192” maksimum erişim, 95.5” kapalı direk yüksekliği ve 295” tam mast geometrisiyle yüksek lokasyonlara kontrollü erişim mümkündür. Rejeneratif frenleme ve elektromanyetik park freni, iniş ve duruş anlarında operatör güvenini pekiştirir. Poly tekerlek seti zeminde düşük iz bırakır ve titreşimi kontrol etmeye yardımcı olur.</p></section><section><h3>Yoğun Hatlarda Kararlı Akış</h3><p>Kurşun-asit konfigürasyonu, uzun vardiya planlarında akü değişim rutiniyle eşleştirildiğinde yüksek kullanılabilirlik sunar. E-ticaret sipariş dalgalanmalarında, 3PL toplama adalarında ve perakende dağıtım merkezlerinde çoklu operatörlü vardiyalarda tutarlı performans elde edilir. JX2-4’ün AC sürüş mimarisi ve 4 kW motoru, sık dur-kalk senaryolarında akıcı kontrol sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Akü değişim istasyonlu depolarda uzun vardiyalı toplama'],
                    ['icon' => 'box-open', 'text' => 'Yüksek hacimli e-ticaret siparişlerinde sabit hız ve tekrar edilebilirlik'],
                    ['icon' => 'industry', 'text' => 'Endüstriyel yedek parça raflarında yüksek erişim'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalaj depolarında kontrollü manevra'],
                    ['icon' => 'car', 'text' => 'Otomotiv lojistiğinde koridor ve rampa geçişleri'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil koli akışında standart 48” palet uyumu']
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
            $this->command->info("✅ Variant: {$v['sku']}");
        }
    }
}
