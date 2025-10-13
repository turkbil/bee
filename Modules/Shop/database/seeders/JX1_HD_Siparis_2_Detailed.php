<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JX1_HD_Siparis_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'JX1-HD')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı (JX1-HD)');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section>
  <h2>JX1-HD ile Yüksek Raflara Güvenli, Hızlı ve Kontrollü Erişim</h2>
  <p>JX1-HD, gün boyu yoğun sipariş toplama görevlerinde <strong>48V güç mimarisi</strong> ve akıllı şasi dengesiyle verimi yükseltmek için tasarlanmış süper görev destek aracıdır. İç mekânda, pürüzsüz ve düz zeminlerde çalışmak üzere optimize edilmiş yapı LED farlar, elektronik direksiyon ve hassas tahrik kontrolüyle birleşir; böylece operatör, 210 inç yüksekliğe güvenle çıkar, ürüne erişir ve aşağı inişi aynı akıcılıkla tamamlar. 6.5 mph yol hızı koridorlar arası geçişleri hızlandırırken, 3,600 lb çekme kapasitesi sipariş toplamanın ötesinde hat besleme ve yardımcı çekme görevlerinde de esneklik sağlar. Dar alanlarda 63 inç dönüş yarıçapı çeviklik sunar; 51.2 in dingil mesafesi ve 36 in kabin genişliği raf aralarında dengeli bir hız/denge karışımı yaratır.</p>
</section>
<section>
  <h3>Teknik Güç ve Operasyonel Tutarlılık</h3>
  <p>Makine, <strong>AC sürüş kontrolü</strong> ve <strong>rejeneratif fren</strong> ile enerji verimliliğini korurken elektromanyetik park freniyle güvenli duruş sağlar. 4 kW sürüş ve 4 kW kaldırma motorları, 39.4/55.1 fpm kaldırma ve 68.9/59.1 fpm indirme hızlarına ulaşır; bu değerler farklı yük durumlarında tutarlı çevrim süreleri üretir. 48V/360Ah Li-ion paket uzun vardiya aralıklarında hızlı fırsat şarjı ve düşük bakım avantajı sağlarken, istenirse 48V/210Ah kurşun-asit akü konfigürasyonu da sunulur. Operatör bölmesi 300 lb, ön çalışma platformu 700 lb ve arka tepsi 200 lb kapasiteyle toplamda 1200 lb taşıma değerine ulaşır; bu denge, sepet yerleşimi değişse bile kontrol hissini korur. 88.6 in kapalı yükseklik, 288.8 in mast tam açık yüksekliği ve 248.8 in kaldırma yüksekliği; koridor standardı ve raf yüksekliği senaryolarında planlamayı kolaylaştırır.</p>
  <p>Şasi; poly tekerlekler ile sessiz, titreşimi azaltılmış bir hareket sunar. Zemin boşluğu 2 inç olduğundan platform, belirtilen <em>iç mekân ve düzgün yüzey</em> koşullarında en iyi sonucu verir. Elektronik direksiyon, milimetre hassasiyetinde raf yaklaşımı sağlar. 36 in toplam genişlik ve 66.3 in toplam uzunluk, tipik depo koridorlarında manevrayı basitleştirir. 30/50/60/160 A şarj akımı seçenekleri, filo altyapılarına ve vardiya stratejilerine uyum verir.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>JX1-HD, yüksek erişim, dengeli hız ve enerji verimliliğini bir araya getirerek sipariş toplamada tutarlı çevrim süreleri ve güvenli çalışma sağlar. Teknik detaylar, konfigürasyon seçenekleri ve projenize özel varyantlar için bizi arayın: <strong>0216 755 3 555</strong>.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => 'Toplam 1200 lb (Q1 300 / Q2 700 / Q3 200)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48V/360Ah Li-ion veya 48V/210Ah Kurşun-asit'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '6.5 mph yol hızı'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '63 in dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '48V Güç Verimliliği', 'description' => 'Kaldırma/indirme ve sürüşte dengeli performans ve düşük tüketim'],
                ['icon' => 'bolt', 'title' => '3,600 lb Çekiş', 'description' => 'Hat besleme ve yardımcı çekme görevlerinde esneklik sağlar'],
                ['icon' => 'star', 'title' => 'LED Aydınlatma', 'description' => 'İleri görüş sağlayan farlarla güvenli operasyon'],
                ['icon' => 'cog', 'title' => 'Elektronik Direksiyon', 'description' => 'Dar raf aralarında hassas yaklaşım ve yön hakimiyeti'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Frenleme', 'description' => 'Rejeneratif sürüş + elektromanyetik park kombinasyonu'],
                ['icon' => 'industry', 'title' => 'Ağır Hizmet Şasi', 'description' => 'İç mekân, düz zemin için optimize edilmiş dengeli yapı']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret raflarında hızlı tekli ürün toplama ve kutulama'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında koridorlar arası toplama turları'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezinde SKU yoğun lokasyonlara erişim'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında çok duraklı sipariş konsolidasyonu'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında kontrollü, düz zeminli iç mekân taşımaları'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik raflarında hassas ve güvenli erişim'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında hızlı yeniden ikmal'],
                ['icon' => 'industry', 'text' => 'Üretim besleme hatlarında komponent toplama ve taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Yüksek çekiş ve dengeli hız ile çevrim sürelerini kısaltır'],
                ['icon' => 'battery-full', 'text' => 'Li-ion seçeneğiyle hızlı fırsat şarjı ve düşük bakım'],
                ['icon' => 'arrows-alt', 'text' => '210” erişim ile çok katlı raflara tek araçla çözüm'],
                ['icon' => 'shield-alt', 'text' => 'Rejeneratif + elektromanyetik frenle güvenli duruş'],
                ['icon' => 'star', 'text' => 'LED far ve görünürlük donanımlarıyla güvenli sürüş'],
                ['icon' => 'cog', 'text' => 'Elektronik direksiyon ile milimetrik raf yaklaşımı']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimya ve Tehlikesiz Maddeler'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Endüstriyel Üretim Besleme'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv ve Döküman'],
                ['icon' => 'building', 'text' => 'Yedek Parça Bölge Depoları'],
                ['icon' => 'microchip', 'text' => 'Elektronik Bileşen Depoları'],
                ['icon' => 'box-open', 'text' => 'Kargo Aktarma Merkezleri'],
                ['icon' => 'warehouse', 'text' => 'Yedek Parça Konsolidasyon'],
                ['icon' => 'cart-shopping', 'text' => 'Market Dağıtım Depoları'],
                ['icon' => 'store', 'text' => 'Ev-Ofis Perakende Lojistiği'],
                ['icon' => 'briefcase', 'text' => 'Bayi Lojistik Merkezleri'],
                ['icon' => 'building', 'text' => 'Bölgesel Mikro Depolar'],
                ['icon' => 'industry', 'text' => 'Hafif Montaj Hücreleri'],
                ['icon' => 'box-open', 'text' => 'Dropshipping Toplama Noktaları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Standart Şarj Cihazı', 'description' => '48V sisteme uyumlu, filo kullanımına uygun güvenilir şarj ünitesi.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Opsiyonel Hızlı Şarj', 'description' => 'Vardiya aralarında yüksek akım ile hızlı fırsat şarjı sağlayan ünite.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Poly Teker Seti', 'description' => 'Düşük gürültü ve düşük yuvarlanma direnci için optimize edilmiş set.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'battery-full', 'name' => 'Ek Li-Ion Paket (360Ah)', 'description' => 'Yoğun kullanımda uzun vardiyalar için yüksek kapasiteli modül.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO'],
                ['icon' => 'certificate', 'name' => 'EN 16796', 'year' => '2024', 'authority' => 'CEN']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '210 inç yüksekliğe çıkarken platform stabilitesi nasıl korunuyor?', 'answer' => 'Elektronik direksiyon, AC sürüş ve mast geometrisi bir arada çalışarak yalpalamayı sınırlar; 36 in genişlik ve 51.2 in dingil mesafesi yapısal denge sağlar.'],
                ['question' => 'Toplam 1200 lb kapasite nasıl dağıtılıyor ve hangi senaryolarda kullanılır?', 'answer' => 'Operatör 300 lb, ön platform 700 lb, arka tepsi 200 lb’dir. Karma sipariş toplama ve hafif ekipman taşıma için uygundur.'],
                ['question' => '6.5 mph hız gerçek operasyonda ne ifade eder?', 'answer' => 'Koridor geçişleri hızlanır ve toplama döngüleri kısalır; hız kontrolü ve fren sistemleri güvenli limitler içinde ivmeyi yönetir.'],
                ['question' => 'Li-ion ve kurşun-asit seçenekleri arasında seçim yaparken neye bakmalıyım?', 'answer' => 'Li-ion 360Ah, fırsat şarjı ve düşük bakım sunar; kurşun-asit 210Ah düşük ilk yatırım sağlar. Vardiya düzeni belirleyicidir.'],
                ['question' => 'Frenleme sistemi enerji geri kazanımı sağlıyor mu?', 'answer' => 'Evet, rejeneratif sürüş frenlemesi enerji geri kazanımı yapar ve elektromanyetik park freni güvenli sabitleme sağlar.'],
                ['question' => 'İç mekân ve düz zemin kısıtı ne anlama geliyor?', 'answer' => '2 inç zemin boşluğu ve tekerlek yapısı nedeniyle pürüzsüz, düz yüzeyler için tasarlanmıştır; rampasız, sevk koridorlarında en iyi sonucu verir.'],
                ['question' => 'Mast tam açık yüksekliği 288.8 in olduğunda tavan açıklığı nasıl planlanır?', 'answer' => 'Tesis tavanı ve sprinkler altına en az 4-6 in emniyet payı önerilir; yükleme noktalarında ek boşluk planlanmalıdır.'],
                ['question' => 'Operatör alanındaki ergonomi ve görüş için neler var?', 'answer' => 'İleri yön LED farlar, net kontrol düzeni ve elektronik direksiyonla hassas konumlama ve görünürlük desteklenir.'],
                ['question' => 'Şarj altyapısında 30/50/60/160A seçeneklerini nasıl seçeriz?', 'answer' => 'Filo büyüklüğü, vardiya süresi ve şarj pencerelerine göre akım kapasitesi belirlenir; Li-ion için fırsat şarjı planlanabilir.'],
                ['question' => 'Çekme kapasitesi hangi yardımcı görevlerde işime yarar?', 'answer' => 'Küçük römorklar veya besleme arabalarının çekilmesi gibi intralojistikte yan görevlerde verim sağlar.'],
                ['question' => 'Bakım aralıkları ve sarf malzeme tüketimi hakkında ne beklemeliyim?', 'answer' => 'AC tahrik ve poly tekerlekler düşük bakım profili sunar; düzenli kontrol ve batarya yönetimi ile ömür uzar.'],
                ['question' => 'Garanti ve satış sonrası desteğe nasıl ulaşırım?', 'answer' => 'Makine 12 ay, akü 24 ay garanti kapsamındadır. İXTİF satış, servis ve yedek parça için 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed: JX1-HD');
    }
}
