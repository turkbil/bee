<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_20WA_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EPT20-20WA')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>Depoda Ağır Hizmetin Yeni Düzeyi</h2><p>Yoğun vardiya temposunda, operatörün ilk hamlesi güven ve öngörülebilirliktir. İXTİF EPT20-20WA serisi bu beklentiyi karşılamakla kalmaz, dar koridorlarda bile çevikliğiyle iş akışını hızlandırır. Uzun tiller geometrisi, otomatik hız düşürme ve elektromanyetik fren ile operatör, yükün kontrolünü hassas bir şekilde korur. <strong>2000 kg</strong> kapasitesi, 600 mm yük merkezi ve 24V enerji yapısı; rampalar, transfer alanları ve ara stok bölgelerinde standart palet akışını güvenle destekler.</p></section><section><h3>Teknik ve Performans</h3><p>AC tahrik mimarisi; 1.1 kW sürüş motoru ve 0.84 kW kaldırma motoru ile yük altında dahi sabit hızlanma sunar. 5.0/5.5 km/s sürüş hızı, 0.051/0.060 m/s kaldırma ve 0.032/0.039 m/s indirme hızları, ritmik ve tekrarlanabilir bir operasyon sağlar. Kompakt 1748 mm toplam uzunluk ve 1600 mm dönüş yarıçapı, 2255 mm koridor genişliğinde 800×1200 paletlerle verimli dönüş imkânı tanır. 55×170×1150 mm çatal ölçüleri ve 685 mm çatallar arası mesafe, Avrupa palet standardına uyumludur. Poliüretan tekerlek seti (Ø85×70/Ø230×75) ve Ø85×48 caster tekerlekler, düşük gürültü ve titreşimle zeminde iz bırakmadan çalışır. S2 60 dk sınıfında 1.1 kW sürüş motoru, frenlemede elektromanyetik çözüme eşlik eder ve enerji geri kazanımıyla uzun devriye sürelerini destekler.</p></section><section><h3>Sonuç</h3><p>İşletmenizdeki dar koridorlar ve yoğun yük akışı için optimize edilen 2000 kg sınıfı İXTİF EPT20-20WA, toplam sahip olma maliyetini düşüren dayanıklı bir platform sunar. Teknik detaylar, keşif ve teklif için arayın: <strong>0216 755 3 555</strong>.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V Li-Ion / Kurşun-asit'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '5.0 / 5.5 km/s'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '1600 mm']
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Modüler enerji sistemi', 'description' => '24V yapı ile hızlı değişim ve düşük bekleme'],
                ['icon' => 'weight-scale', 'title' => 'Güçlü şasi', 'description' => 'Ağır hizmet çelik gövdeyle uzun ömür'],
                ['icon' => 'compress', 'title' => 'Kompakt ölçüler', 'description' => 'Dar koridorda çevik hareket kabiliyeti'],
                ['icon' => 'circle-notch', 'title' => 'Pürüzsüz sürüş', 'description' => 'Poliüretan tekerlekle sessiz ve dengeli'],
                ['icon' => 'hand', 'title' => 'Ergonomik tiller', 'description' => 'Uzun kol ve sezgisel kontrol tuşları'],
                ['icon' => 'dolly', 'title' => 'Standart palet uyumu', 'description' => '1150 mm çatal boyuyla hızlı giriş-çıkış']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Kargo ayırma alanlarında palet besleme'],
                ['icon' => 'store', 'text' => 'Perakende depo arası malzeme transferi'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde cross-dock hatları'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda antrepolarına giriş-çıkış'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik ürünlerinde hassas taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça palet akışı'],
                ['icon' => 'tshirt', 'text' => 'Tekstil kolileme ve hat besleme'],
                ['icon' => 'industry', 'text' => 'Ağır sanayi WIP taşıma']
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC tahrik ile yüksek tork ve tutarlı hızlanma'],
                ['icon' => 'battery-full', 'text' => '24V enerji ile uzun devriye ve hızlı toparlanma'],
                ['icon' => 'arrows-alt', 'text' => 'Kısa gövde/dar dönüş ile koridor verimliliği'],
                ['icon' => 'layer-group', 'text' => 'Li-Ion ve kurşun-asit seçenekli esnek platform'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve otomatik hız düşürme']
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistik'],
                ['icon' => 'store', 'text' => 'Perakende Zincir Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım Merkezleri'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek ve Şişeleme'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Lojistik'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarıiletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet ve Hayvancılık Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv Depolama'],
                ['icon' => 'building', 'text' => 'Endüstriyel Üretim Tesisleri']
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Akü Şarj Cihazı (standart)', 'description' => 'Şebeke uyumlu, güvenli şarj prosedürü', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Tandem poliüretan teker seti', 'description' => 'Zemin izini azaltan, sessiz operasyon', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yedek Li-Ion modül', 'description' => 'Vardiya arası hızlı değişim için modüler', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Bakım takımı', 'description' => 'Periyodik bakım el seti', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                ['question' => 'Bu model hangi koridor genişliklerinde verimli performans sağlar?', 'answer' => 'Toplam uzunluk 1748 mm ve 1600 mm dönüş yarıçapı sayesinde 2255 mm koridorda 800×1200 paletlerle verimli döner.'],
                ['question' => 'Sürüş ve kaldırma hızları vardiya planlamasını nasıl etkiler?', 'answer' => '5.0/5.5 km/s sürüş ve 0.051/0.060 m/s kaldırma hızları, tekrarlı taşımada çevrim sürelerini öngörülebilir kılar.'],
                ['question' => 'Standart çatal ölçüsü hangi paletlerle uyumludur?', 'answer' => '55×170×1150 mm çatal ve 685 mm çatallar arası genişlik, EUR palet ve yaygın endüstri paletlerine uygundur.'],
                ['question' => 'Eğim performansı rampa yaklaşımında yeterli mi?', 'answer' => 'Azami %8/%16 eğim değeri, yük durumuna göre kısa rampalarda güvenli yaklaşım sağlar.'],
                ['question' => 'Fren sistemi ve güvenlik özellikleri neler?', 'answer' => 'Elektromanyetik fren, tiller konumuna bağlı otomatik hız düşürme ve acil duruş işlevi birlikte çalışır.'],
                ['question' => 'Tekerlek malzemesi zemin aşınmasına etkisi nedir?', 'answer' => 'Poliüretan tekerlekler düşük gürültü ve iz bırakmama avantajı sağlar, beton ve epoksi zeminlerde idealdir.'],
                ['question' => 'Enerji sistemi modüler mi ve hızlı değişim mümkün mü?', 'answer' => '24V yapı Li-Ion ve kurşun-asit seçenekleri ile modülerdir; vardiya arasında hızlı değişime uygundur.'],
                ['question' => 'Bakım aralıkları ve erişilebilirlik nasıl tasarlandı?', 'answer' => 'Bakım noktaları erişilebilirdir; AC sürüş sistemi fırçasız yapı ile bakım ihtiyacını azaltır.'],
                ['question' => 'Operatör ergonomisi kullanım konforunu nasıl artırır?', 'answer' => 'Uzun tiller kolu, doğal el pozisyonu ve geniş dönüş açısı ile manevrada az efor gerektirir.'],
                ['question' => 'Gürültü seviyesi hangi sınırlar içindedir?', 'answer' => '74 dB(A) değerindeki ses basıncı seviyesi, kapalı alan iş güvenliği standartlarıyla uyumludur.'],
                ['question' => 'Hangi standartlara ve sertifikalara uygundur?', 'answer' => 'CE uygunluğu ile birlikte kalite ve iş güvenliği gerekliliklerini karşılayacak şekilde tasarlanmıştır.'],
                ['question' => 'Garanti ve satış sonrası destek nasıl sağlanır?', 'answer' => 'Makine 12 ay, akü 24 ay garantilidir. Yedek parça ve teknik destek için İXTİF 0216 755 3 555 ile iletişime geçebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EPT20-20WA');
    }
}
