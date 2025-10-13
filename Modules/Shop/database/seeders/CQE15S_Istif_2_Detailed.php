<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CQE15S_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'CQE15S')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: CQE15S');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF CQE15S: Dar Koridorlarda Uzakları Yakın Eden Walkie Reach</h2><p>Depoda koridorların daraldığı, stok çeşitliliğinin arttığı bir dönemde esneklik her zamankinden daha değerlidir. İXTİF CQE15S, pantograf erişimi sayesinde straddle bacaklarını aşarak çift derin palet konumlarına güvenle uzanır. Standart güç direksiyonu, yanak kaydırma ve tilt fonksiyonları ile operatör yükü azaltır; ofset tiller tasarımı ise ileri görüş açısı sağlayarak güveni yükseltir. 3000 lb temel kapasite ve 189 inçe kadar kaldırma seçenekleriyle CQE15S, e-ticaretten 3PL depolarına kadar farklı operasyonların ortak çözüm ortağıdır.</p></section><section><h3>Teknik</h3><p>Elektrikli tahrik ile yürüyen tip bir reach istif makinesi olan CQE15S, 24 inç yük merkezinde 3000 lb taşıma kapasitesi sunar. Direğe bağlı olarak maksimum çatal yüksekliği 126, 157 veya 189 inçtir; direk yükseldiğinde yükseklik sırasıyla 162.8, 195.25 ve 227 inç değerlerine ulaşır. Şasi uzunluğu 88.6 inç, yük yüzüne kadar uzunluk 46.6 inç ve dönüş yarıçapı 62.6 inçtir. 23 inç erişim mesafesi (pantograf) ile çift derine uzanırken, 3.1/3.4 mph yüklü/boş seyir hızı, 20/26 fpm kaldırma ve 52/33 fpm indirme hızları akıcı operasyon sağlar. S2 60 dk sürüş motoru 4.4 HP, S3 15% kaldırma motoru ise 5.4 kW gücündedir. Enerji tarafında 24V/170Ah veya 205Ah Li-ion, 24V/224Ah AGM ve 24V/255Ah ya da 510Ah kurşun-asit seçenekleri bulunur. Elektromanyetik servis ve park frenleri, poliüretan teker yapısı ve elektrik direksiyon, yaya tipi kullanımda güven ve kontrolü bir araya getirir.</p></section><section><h3>Sonuç</h3><p>İXTİF CQE15S, dar alanlarda yüksek raf erişimi gerektiren dağıtım merkezleri için pratik bir çözümdür. Esnek akü seçenekleri sayesinde farklı vardiya yapılarıyla uyum sağlar; standart yanak kaydırma ve tilt sayesinde palet hizalama süresini kısaltır. Çift derin erişim ve 189 inçe uzanan kaldırma ile daha az manevrada daha fazla iş çıkarırsınız. Tüm teknik sorularınız ve fiyatlandırma için 0216 755 3 555 üzerinden satış ekibimize ulaşabilirsiniz.</p></section>'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'industry', 'label' => 'Kapasite', 'value' => '3000 lb (24 inç LC)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V Li-ion/AGM/Kurşun-asit'],
                ['icon' => 'star', 'label' => 'Hız', 'value' => '3.1/3.4 mph (yüklü/boş)'],
                ['icon' => 'arrows-alt', 'label' => 'Dönüş', 'value' => '62.6 inç (Wa)']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'Pantograf Erişim', 'description' => 'Straddle bacaklarını aşarak çift derin paletlere uzanır, raf yoğunluğunu artırır.'],
                ['icon' => 'cog', 'title' => 'Güç Direksiyonu', 'description' => 'Yorulmayı azaltır, yaya operatörde hassas ve güvenli manevra sağlar.'],
                ['icon' => 'cart-shopping', 'title' => '189”e Kadar Kaldırma', 'description' => 'Yüksek raflara erişim ile dikey depo hacmini verimli kullanır.'],
                ['icon' => 'battery-full', 'title' => 'Geniş Akü Seçenekleri', 'description' => '24V Li-ion, AGM ve sulu akü alternatifleri ile esnek enerji yapısı.'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Servis ve park frenleriyle kontrollü duruş ve güvenli park.'],
                ['icon' => 'building', 'title' => 'Ofset Tiller Görüşü', 'description' => 'Operatöre daha iyi görüş çizgisi vererek çarpışma riskini düşürür.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => 'Dar koridor raf aralarında palet yerleştirme ve toplama'],
                ['icon' => 'box-open', 'text' => 'Çift derin raflarda palet erişimi ve dönüşümlü slot kullanımı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezlerinde sipariş konsolidasyonu'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında yoğun vardiya içi besleme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış operasyonlarında kontrollü istifleme'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik stoklarında hassas palet konumlandırma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça bölgelerinde raf besleme'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerine WIP taşıma ve tampon alan yönetimi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Çift derin palet erişimi ile daha az koridor, daha çok raf yoğunluğu'],
                ['icon' => 'battery-full', 'text' => 'Li-ion dahil çoklu akü opsiyonu ile kesintisiz vardiya yapısı'],
                ['icon' => 'arrows-alt', 'text' => '62.6 inç dönüş yarıçapıyla kompakt manevra ve dar alan kabiliyeti'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve elektrik direksiyon ile güvenli kullanım'],
                ['icon' => 'building', 'text' => 'Ofset tiller görüş hattı ile operatör hatalarını azaltma']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Operasyonlar'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimya Depolama ve Dağıtım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yedek Parça'],
                ['icon' => 'car', 'text' => 'Otomotiv Tedarik Zinciri'],
                ['icon' => 'building', 'text' => 'İnşaata Yardımcı Malzeme Depoları'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv ve Depolama'],
                ['icon' => 'industry', 'text' => 'Genel Endüstriyel Üretim'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Paket Konsolidasyon'],
                ['icon' => 'warehouse', 'text' => 'Bölgesel Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Market Zinciri Arka Depoları'],
                ['icon' => 'flask', 'text' => 'Boya ve Kimyasal Hammadde Lojistiği'],
                ['icon' => 'microchip', 'text' => 'Telekom ve IT Donanım Depoları'],
                ['icon' => 'pills', 'text' => 'Kozmetik ve Kişisel Bakım Depoları'],
                ['icon' => 'car', 'text' => 'Lastik ve Jant Depolama Alanları'],
                ['icon' => 'industry', 'text' => 'Makine Yedek Parça Stok Sahaları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarındaki üretim hatalarını kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Dahili/Harici Şarj Cihazı', 'description' => '24V sistem için uyumlu şarj çözümleri; Li-ion ve kurşun-asit alternatifleriyle.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'PU Teker Seti', 'description' => 'Poliüretan tekerlek takımıyla titreşim azaltma ve sessiz çalışma.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Ek Li-ion Batarya', 'description' => '24V/170Ah veya 205Ah Li-ion modül ile uzun vardiya esnekliği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'shield-alt', 'name' => 'Soğuk Depo Paketi', 'description' => 'Düşük sıcaklık için kablo ve sızdırmazlık iyileştirmeleri.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Pantograf erişim çift derin paletlerde hangi mesafeye kadar uzanır?', 'answer' => 'Erişim mesafesi 23 inç olarak belirtilmiştir; bu sayede straddle bacaklarını aşarak çift derin konumlarda palet alma mümkündür.'],
                ['question' => 'Maksimum kaldırma yüksekliği seçenekleri nelerdir ve raf planına etkisi nedir?', 'answer' => '126, 157 ve 189 inç seçenekleri bulunur. Üst seviyelerde 3000 lb kapasite üzerinde derate uygulanır; raf yüksekliği seçimi buna göre yapılmalıdır.'],
                ['question' => 'Yüklü ve boş durumda seyir hızları hangi aralıktadır?', 'answer' => 'Seyir hızları 3.1 mph (yüklü) ve 3.4 mph (boş) değerlerindedir; bu da özellikle uzun koridorlarda akıcı trafik sağlar.'],
                ['question' => 'Kaldırma ve indirme hızları performansı nasıl etkiler?', 'answer' => 'Kaldırma 20/26 fpm, indirme 52/33 fpm’dir. Yük geçişlerinde dengeli hızlar güvenli ve kontrollü operasyon sunar.'],
                ['question' => 'Dönüş yarıçapı ve koridor gereksinimi dar alanlarda yeterli mi?', 'answer' => 'Dönüş yarıçapı 62.6 inçtir. Minimum koridor genişliği direğe göre 105.9–138.7 inç aralığındadır; dar alanlarda avantaj sağlar.'],
                ['question' => 'Standart donanımda hangi kontrol fonksiyonları sunuluyor?', 'answer' => 'Güç direksiyonu, yanak kaydırma, tilt ve oransal kaldır/indir, uzat/geri çek fonksiyonları standarttır.'],
                ['question' => 'Hangi akü teknolojileri ile kullanılabilir?', 'answer' => '24V Li-ion (170/205Ah), 24V/224Ah AGM ve 24V/255–510Ah kurşun-asit seçenekleri mevcuttur; vardiya ihtiyacına göre seçilir.'],
                ['question' => 'Tekerlek ve fren sistemi operatör güvenliğine nasıl katkı sağlar?', 'answer' => 'Poliüretan tekerler sessiz ve titreşimi düşük çalışma sunar; elektromanyetik servis ve park freni kontrollü duruş sağlar.'],
                ['question' => 'Ofset tiller tasarımının saha performansına etkisi nedir?', 'answer' => 'Ofset yerleşim operatörün yük ve çevreyi daha iyi görmesini sağlar; dar alanlarda hizalama hatalarını azaltır.'],
                ['question' => 'Çift derin kullanımda kapasite düşüşü nasıl yönetilir?', 'answer' => '126 inç üzerindeki yüksekliklerde kapasite derate edilir; palet ağırlığı ve yük merkezi doğrulanarak operasyon planlanmalıdır.'],
                ['question' => 'Bakım periyotları ve işletim maliyetleri açısından öne çıkan noktalar nelerdir?', 'answer' => 'Elektrik tahrik ve elektromanyetik fren yapısı düşük bakım gerektirir; Li-ion batarya seçeneği günlük şarj kolaylığı sağlar.'],
                ['question' => 'Garanti kapsamı ve destek kanallarına nasıl ulaşırım?', 'answer' => 'Makine 12 ay, akü 24 ay garantilidir. Teknik destek ve satış için İXTİF 0216 755 3 555 üzerinden iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("🧩 Detailed güncellendi: CQE15S");
    }
}
