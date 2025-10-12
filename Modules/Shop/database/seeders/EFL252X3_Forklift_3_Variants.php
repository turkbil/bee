<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252X3_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL252X3')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL252X3'); return; }

        $variants = [
            [
                'sku' => 'EFL252X3-3000MM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL252X3 - 3000 mm Direk',
                'short_description' => 'Standart 3.0 m kaldırma yüksekliği ile depo içi raf operasyonlarında dengeli görüş, hızlı çevrim ve düşük tavanlı alanlara uyum sağlayan kompakt çözüm.',
                'long_description' => '<section><h3>3000 mm Standart Direk</h3><p>3.0 metre kaldırma yüksekliği; düşük tavanlı depolarda, sevkiyat alanlarında ve standart raf yüksekliklerinde optimum denge sunar. 2070 mm kapalı direk yüksekliği, kapı geçişlerinde ve konteyner içi çalışmalarda avantaj sağlar. PMSM sürüş ve 80V Li‑Ion enerji, 11/12 km/sa yürüyüş hızlarını güvenli şekilde destekler.</p></section><section><h3>Teknik Odak</h3><p>50×122×1070 mm çatal, sınıf 2A taşıyıcı ve 1040 mm taşıyıcı genişliği ile Euro paletlerde hızlı giriş‑çıkış yapılır. 2250 mm dönüş yarıçapı dar koridorlarda çevik manevra sağlar; 0.28/0.36 m/sn kaldırma ve 0.40/0.43 m/sn indirme hızları istif verimliliğini artırır.</p></section><section><h3>Kullanım</h3><p>Yoğun vardiyalarda tek faz 16A entegre şarj cihazı ile fırsat şarjları yapılabilir. Yüksek yerden açıklık ve katı lastikler, bozuk zeminli rampa ve saha geçişlerinde süreklilik sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Düşük tavanlı depolarda kapı geçişi ve istif'],
                    ['icon' => 'box-open', 'text' => 'Sevkiyat ön hazırlığı ve çapraz yükleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf arası taşıma'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresinde WIP besleme'],
                    ['icon' => 'car', 'text' => 'Rampada kamyon yükleme/boşaltma'],
                    ['icon' => 'snowflake', 'text' => 'Açık sahada kısa mesafe transfer']
                ]
            ],
            [
                'sku' => 'EFL252X3-3300MM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL252X3 - 3300 mm Direk',
                'short_description' => '3300 mm kaldırma; standart raf seviyelerinin bir üst diliminde daha esnek istif imkânı sunar. Görüş ve stabilite korunurken yük erişimi genişler.',
                'long_description' => '<section><h3>3300 mm Direk</h3><p>3300 mm’ye uzatılan kaldırma yüksekliği, depo tasarımını değiştirmeden raf kullanımını optimize eder. 2240 mm kapalı direk yüksekliği, giriş‑çıkış ve dolaşım sırasında engellere çarpmayı önlemeye yardımcı olur.</p></section><section><h3>Performans</h3><p>PMSM tahrik, yüzdelik bazda daha yüksek verim sunarken, 80V Li‑Ion batarya fırsat şarjıyla uzun vardiyalarda süreklilik sağlar. 15/15% eğim kabiliyeti ve katı lastikler, rampa ve bozuk zeminlerde güven verir.</p></section><section><h3>Uygulamalar</h3><p>Yoğun sipariş dalgalanmalarında istif yüksekliği esnekliği ve dar alan manevrası ile çevrim süreleri düşer.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Raf yüksekliği artan koridorlarda istif'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli erişim'],
                    ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas depolama'],
                    ['icon' => 'box-open', 'text' => 'Sevkiyat hazır stokların düzeni'],
                    ['icon' => 'store', 'text' => 'Perakende DC stok optimizasyonu'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası tampon depolama']
                ]
            ],
            [
                'sku' => 'EFL252X3-4500MM-FREE',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFL252X3 - 4500 mm Serbest Kaldırma Direk',
                'short_description' => '4500 mm serbest kaldırma; tavan aparatı ve sprinkler bulunan depolarda ilk hareketten itibaren yükü yükseltirken taşıyıcı yüksekliğini sınırlı tutar.',
                'long_description' => '<section><h3>4500 mm Free Lift</h3><p>Serbest kaldırma, direğin ilk aşamada genişlemeyen iç kademeleriyle çalışarak, tavan kısıtlarının yoğun olduğu alanlarda güvenli başlangıç hareketleri sağlar. 2115 mm kapalı direk yüksekliği, düşük açıklıklı koridorlarda avantaj sunar.</p></section><section><h3>Teknik</h3><p>0.28/0.36 m/sn kaldırma, 0.40/0.43 m/sn indirme hızları ve 2250 mm dönüş yarıçapı; yüksek istiflerde bile kontrollü ve çevik operasyon sağlar. Sınıf 2A taşıyıcı ve 50×122×1070 mm çatal ölçüleri geniş palet uyumluluğu sağlar.</p></section><section><h3>Operasyon</h3><p>Li‑Ion batarya lateral çekilerek hızlı değiştirilebilir; tek faz 16A şarj ile fırsat şarjı kolaydır. Hidrolik hizmet freni ve mekanik park freni rampalarda güven verir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Sprinkler’lı düşük tavanlı raf sahaları'],
                    ['icon' => 'building', 'text' => 'Mezzanine altında istif/çekme'],
                    ['icon' => 'cart-shopping', 'text' => 'Yüksek istifte hızlı toplama'],
                    ['icon' => 'star', 'text' => 'Değişken sipariş yoğunluğu yönetimi'],
                    ['icon' => 'shield-alt', 'text' => 'Güvenlik mesafesi gerektiren alanlar'],
                    ['icon' => 'box-open', 'text' => 'Zaman hassas sevkiyat hazırlığı']
                ]
            ],
            [
                'sku' => 'EFL252X3-80V280AH',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL252X3 - 80V/280Ah Batarya',
                'short_description' => '280Ah yüksek kapasiteli Li‑Ion paket; çok vardiyalı tesislerde duruşları azaltır, fırsat şarjlarıyla gün boyu kesintisiz operasyon sağlar.',
                'long_description' => '<section><h3>Yüksek Kapasite Enerji</h3><p>80V/280Ah paket, standart 150Ah’a göre önemli menzil artışı sağlar. Lateral çek‑tak tasarım, vardiya arasında hızlı batarya değişimine olanak tanır. PMSM sürüş ile birleşince enerji tüketimi düşer, çevrim başına daha fazla iş yapılır.</p></section><section><h3>Verim ve TCO</h3><p>Fırsat şarjına tam uyumlu Li‑Ion kimya, bekleme sürelerini işe çevirir. Daha az bakım, daha yüksek erişilebilirlik ve düşük toplam sahip olma maliyeti sağlar.</p></section><section><h3>Uygulama Alanları</h3><p>Yoğun stok devir hızında çalışan 3PL merkezleri, çok vardiyalı üretim hatları ve geniş açık saha operasyonları için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL merkezlerinde uzun vardiya'],
                    ['icon' => 'industry', 'text' => 'Çok vardiyalı üretim hatları'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG yüksek devir operasyonları'],
                    ['icon' => 'car', 'text' => 'Otomotiv rampalarında yoğun trafik'],
                    ['icon' => 'box-open', 'text' => 'E‑ticaret pik dönemleri'],
                    ['icon' => 'briefcase', 'text' => 'B2B konsolidasyon sahaları']
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

        $this->command->info('✅ Variants eklendi: EFL252X3 (4 varyant)');
    }
}
