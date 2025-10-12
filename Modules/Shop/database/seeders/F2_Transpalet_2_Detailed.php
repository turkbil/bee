<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F2_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'F2')->first();
        if (!$p) { echo "❌ Master bulunamadı\n"; return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF F2: Perakende koridorlarında akışkanlık ve verimlilik</h2>
  <p>Sabah açılışından kapanışa kadar, dar koridorlarda palet akışını duraksatmadan sürdüren kompakt bir yardımcınız olduğunu hayal edin. İXTİF F2, 1.5 ton kapasiteli elektrikli transpalet mimarisini hafif bir şasi (120 kg) ve akıllı Li‑Ion batarya ile birleştirerek market, mağaza ve küçük depolarda kesintisiz operasyon sağlar. Avuç içiyle yönetime izin veren yeni tiller başı ergonomisi sayesinde kullanıcıların başparmaklarını zorlamadan, gün boyu rahat ve tekrarlanabilir hareketlerle malzeme transferi yapmasına olanak verir. Platform F tabanlı tasarım, ekipmanın yapılandırmasını basitleştirir ve farklı şasi seçenekleri ile uygulamaya göre uyarlama yapmayı mümkün kılar.</p>
</section>
<section>
  <h3>Teknik güç ve ölçüler</h3>
  <p>F2, 24V/20Ah Li‑Ion enerji sistemiyle fırsat şarja elverişli ve bakım gerektirmeyen bir batarya mimarisi sunar; 0.75 kW sürüş ve 0.5 kW kaldırma motorlarıyla yük altında 4.0 km/s, boşta 4.5 km/s hıza ulaşır. 1360 mm dönüş yarıçapı, 1550 mm toplam uzunluk ve 695/590 mm toplam genişlik kombinasyonu, sıkışık alanlarda çevik manevrayı destekler. 55/150/1150 mm çatal ölçüsü, 685/560 mm çatallar arası mesafe seçenekleri ve 105 mm kaldırma yüksekliği ile EUR paletlerde standart kullanımı hedefler. 25 mm şase altı yerden yükseklik, rampalarda kontrollü geçişe katkı sağlarken elektromanyetik servis freni güvenli duruşu garanti eder. Poliüretan tahrik ve yük tekerleri sessiz ve iz bırakmayan bir çalışma karakteri sunar. Akustik gürültü seviyesi <74 dB(A) düzeyinde olup market ve mağaza içi konforu korur.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>Platform F ile kutu başına 4 ünite tedarik ve 40’ konteynerde 176 üniteye varan yükleme sayesinde lojistik maliyetlerinde ciddi bir azalma sağlanır; bu da toplam sahip olma maliyetini düşürürken daha çok noktaya hızlı dağıtım avantajı yaratır. Perakende ve hafif dağıtım operasyonlarında F2, basit, güvenli ve ekonomik bir akış sağlar. Teknik detaylar ve teklif için bizi arayın: 0216 755 3 555</p>
</section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V / 20Ah Li‑Ion'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1360 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Akıllı Li‑Ion Enerji', 'description' => 'Fırsat şarj, bakım gerektirmeyen ve hafif yapı'],
                ['icon' => 'hand', 'title' => 'Ergonomik Tiller', 'description' => 'Avuç içiyle rahat kontrol, azaltılmış sıkışma kuvveti'],
                ['icon' => 'layer-group', 'title' => 'Platform F Tasarımı', 'description' => 'Yapılandırmayı basitleştirir, çoklu şasi seçeneği'],
                ['icon' => 'cart-shopping', 'title' => 'Perakendeye Uygun Şasi', 'description' => 'Süpermarket ve mağaza içi dar koridor kullanım'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Güvenli ve tekrarlanabilir duruş performansı'],
                ['icon' => 'bolt', 'title' => 'Verimli Sürüş', 'description' => '0.75 kW sürüş, 0.5 kW kaldırma motoru']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'store', 'text' => 'Süpermarket ve mağaza içi koridorlarda paletli ürün transferi'],
                ['icon' => 'cart-shopping', 'text' => 'Perakende geri oda ve stok sahasında replenishment'],
                ['icon' => 'warehouse', 'text' => 'Mikro depo ve karanlık mağaza operasyonlarında sipariş besleme'],
                ['icon' => 'box-open', 'text' => 'E‑ticaret paketleme alanlarında kısa mesafe malzeme akışı'],
                ['icon' => 'industry', 'text' => 'Hafif üretim hücrelerinde WIP taşıma ve hat besleme'],
                ['icon' => 'building', 'text' => 'Dağıtım merkezlerinde çapraz sevkiyat (cross‑dock) hatları'],
                ['icon' => 'briefcase', 'text' => 'Küçük 3PL sahalarında toplama ve sevk öncesi düzenleme'],
                ['icon' => 'arrows-alt', 'text' => 'Dar koridorlu raf sistemlerinde manevra gerektiren işler']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '120 kg şasi ile sınıfında hafif; hız ve çeviklikte üstünlük'],
                ['icon' => 'battery-full', 'text' => 'Li‑Ion 24V/20Ah ile fırsat şarj ve bakım gerektirmeyen enerji'],
                ['icon' => 'arrows-alt', 'text' => '1360 mm dönüş yarıçapı ile dar alanlarda yüksek manevra'],
                ['icon' => 'layer-group', 'text' => 'Platform F mimarisi ile 4 farklı şasi seçeneği ve esneklik'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve düşük gürültü ile güvenli çalışma']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincirler'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya Dağıtım'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet Ürünleri ve Yem']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay fabrika garantisi altındadır. Li‑Ion batarya modülü normal kullanım koşullarında 24 ay garanti kapsamındadır. Garanti üretim hatalarını kapsar, sarf ve kötü kullanım kapsama dahil değildir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V‑5A Harici Şarj Cihazı', 'description' => 'Standart harici şarj cihazı ile hızlı ve güvenli şarj imkanı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'charging-station', 'name' => '24V‑10A Harici Şarj Cihazı', 'description' => 'Yoğun vardiyalar için daha hızlı şarj kapasitesi sunar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Batarya Göstergesi', 'description' => 'Enerji seviyesi takibi için sürüşte kolay okunur gösterge.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => 'Tandem Yük Tekerleri (PU)', 'description' => 'Tandem yük tekerleriyle daha dengeli yük taşıma.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Günlük perakende kullanımında tek batarya ile kaç vardiya destekler?', 'answer' => '24V/20Ah Li‑Ion batarya fırsat şarja uygundur; kısa molalarda takviye şarj ile gün boyu operasyonu destekler. Vardiya kurgusuna bağlıdır.'],
                ['question' => 'Dönüş yarıçapı dar koridorlarda yeterli manevra sağlar mı?', 'answer' => '1360 mm dönüş yarıçapı ve kompakt şasi, raf araları ve kasa arkası gibi dar alanlarda çevik manevra sağlar.'],
                ['question' => 'Hız değerleri yüklü ve boş durumda nedir?', 'answer' => 'Yüklü 4.0 km/s, boşta 4.5 km/s hız sunar; bu sayede kısa mesafe iç lojistik akışı hızlanır.'],
                ['question' => 'Maksimum eğim performansı ve kullanım önerisi nedir?', 'answer' => 'Maksimum eğim yüklü %5, boş %16’dır. Rampalarda kontrollü kullanım ve yük dağılımına dikkat önerilir.'],
                ['question' => 'Standart çatal ölçüsü ve palet uyumluluğu nasıldır?', 'answer' => '55/150/1150 mm çatal ve 685/560 mm çatallar arası değerleri EUR paletlerle uyumludur.'],
                ['question' => 'Tekerlek malzemesi hangi zeminler için uygundur?', 'answer' => 'PU tahrik ve yük tekerleri sessiz, iz bırakmayan yapısıyla market ve depo zeminlerinde uygundur.'],
                ['question' => 'Fren sistemi ve güvenlik avantajları nelerdir?', 'answer' => 'Elektromanyetik servis freni ve düşük gürültü seviyesi güvenli ve konforlu kullanım sağlar.'],
                ['question' => 'Bakım gereksinimleri ve planı nasıl olmalı?', 'answer' => 'Li‑Ion batarya bakım gerektirmez; periyodik görsel kontroller, tekerlek ve fren bakımı yeterlidir.'],
                ['question' => 'Toplam uzunluk ve genişlik değerleri nelerdir?', 'answer' => 'Toplam uzunluk 1550 mm, toplam genişlik 695/590 mm’dir; alan kullanımını optimize eder.'],
                ['question' => 'Hangi aksesuarları tercih etmeliyim?', 'answer' => 'Vardiya yoğunluğuna göre 24V‑10A şarj cihazı ve tandem yük tekeri, enerji takibi için batarya göstergesi önerilir.'],
                ['question' => 'Garanti kapsamı ve süresi nasıl işler?', 'answer' => 'Makine 12 ay, Li‑Ion batarya modülü 24 ay garanti kapsamındadır; normal kullanım koşullarında üretim hatalarını kapsar.'],
                ['question' => 'Satış, servis ve yedek parça desteğini kim sağlıyor?', 'answer' => 'Tüm satış, servis, kiralama ve yedek parça desteği İXTİF tarafından sağlanır. Detay için 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        echo "✅ Detailed güncellendi: F2\n";
    }
}
