<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class F4_201_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. MARKA KONTROL / OLUŞTUR
        $brandId = DB::table('shop_brands')
            ->where('slug->tr', 'ixtif')
            ->value('brand_id');

        if (!$brandId) {
            $brandId = DB::table('shop_brands')->insertGetId([
                'title' => json_encode(['tr' => 'İXTİF'], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => 'ixtif'], JSON_UNESCAPED_UNICODE),
                'description' => json_encode(['tr' => 'İXTİF İç ve Dış Ticaret A.Ş.'], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. KATEGORİ ID BUL (DİNAMİK)
        $categoryId = DB::table('shop_categories')
            ->where('slug->tr', 'transpalet')
            ->value('category_id');

        if (!$categoryId) {
            $this->command->error('❌ Transpalet kategorisi bulunamadı! Önce ShopCategorySeeder çalıştırın.');
            return;
        }

        // 3. ESKİ KAYITLARI TEMİZLE
        DB::table('shop_products')
            ->where('sku', 'LIKE', 'F4-201%')
            ->delete();

        // 4. MASTER PRODUCT EKLE
        $productId = DB::table('shop_products')->insertGetId([
            'sku' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,

            // Temel Bilgiler
            'title' => json_encode(['tr' => 'F4 201 Li-Ion Akülü Transpalet 2.0 Ton'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-201-2-ton-48v-li-ion-transpalet'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '48V Li-Ion güç platformu ile 2 ton taşıma kapasitesi sunan F4 201, tak-çıkar batarya sistemi ve 140 kg ultra hafif gövdesiyle dar koridor operasyonlarında yeni standartlar belirler.'], JSON_UNESCAPED_UNICODE),

            // Long Description (Marketing Content)
            'long_description' => json_encode(['tr' => <<<'HTML'
<section class="marketing-intro">
<p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz "Bu transpaleti nereden aldınız?" diye soracak.</strong></p>
<p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı. 48V Li-Ion güç platformu ile 2 ton yükü adeta havada yürüyor gibi taşıyan F4 201, dar koridorlarda bile vitrinde bir süperstar gibi parlar.</p>
<ul>
<li><strong>Bir vardiyada iki kat iş</strong> – Tak-çıkar batarya sistemi ile şarj molasına son verin, lojistik maliyetleriniz %50'ye kadar düşsün.</li>
<li><strong>Showroom etkisi</strong> – Ultra kompakt 400 mm şasi, dar koridorlarda bile operatörlerinize benzersiz özgürlük sağlar.</li>
<li><strong>140 kg hafiflik şampiyonu</strong> – Segmentindeki en hafif gövde, rampalarda inanılmaz kontrol ve enerji verimliliği demektir.</li>
<li><strong>Stabilizasyon tekerleği opsiyonu</strong> – Bozuk zeminlerde bile devrilme riskini sıfırlayarak yatırımınızı korur.</li>
</ul>
</section>

<section class="marketing-body">
<h3>Depoda Hız, Sahada Prestij: F4 201 ile Dar Koridorlara Hükmedin</h3>
<p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur. Her modül çıkarılabilir, dolayısıyla vardiya ortasında boş bataryayı çıkarıp dolu olanı takarak operasyonunuz hiç durmaz. Dilerseniz 4 adede kadar modül ekleyerek uzun vardiya performansını artırabilirsiniz.</p>

<p><strong>48V BLDC motorlu sürüş sistemi</strong> sayesinde F4 201, %8 rampalarda yükle bile zorlanmadan çıkar. 0.9 kW sürüş motoru ve 0.7 kW kaldırma motoru kombinasyonu, elektromanyetik fren ile birleşerek size acil durumlarda bile tam kontrol sağlar.</p>

<p><strong>Poliüretan çift sıra yük tekerleri</strong> ve 210×70 mm sürüş tekerleri, dar koridorlarda bile pürüzsüz hareket ve uzun ömür garanti eder. 1360 mm dönüş yarıçapı sayesinde standart paletlerinizi 2160 mm koridor genişliğinde rahatça döndürebilirsiniz.</p>

<h4>İXTİF Farkı: Yatırımınıza 360° Koruma</h4>
<p>İXTİF'in <strong>ikinci el, kiralık, yedek parça ve teknik servis</strong> ekosistemi ile F4 201 yatırımınız tam koruma altında. Türkiye genelinde mobil teknik servis ekiplerimiz 7/24 sahadır. İster alın, ister kiralayın, ister ikinci el seçeneği değerlendirin – İXTİF her senaryoda yanınızda.</p>

<ul>
<li><strong>İkinci El Güvencesi:</strong> Garanti belgeleriyle yenilenmiş F4 201 modelleri mevcut.</li>
<li><strong>Kiralık Filolar:</strong> Kısa ve orta vadeli kiralama seçenekleri, operasyonel esneklik sağlar.</li>
<li><strong>Yedek Parça Stoku:</strong> Orijinal EP parçaları İXTİF depolarında stoktan hemen temin edilebilir.</li>
<li><strong>Teknik Servis:</strong> 0216 755 3 555 numaralı hattımızdan acil servis taleplerinizi iletebilirsiniz.</li>
</ul>

<h4>SEO Anahtar Kelimeleri</h4>
<p><strong>F4 201 transpalet, 48V Li-Ion transpalet, 2 ton akülü transpalet, İXTİF transpalet, dar koridor transpalet, çıkarılabilir bataryalı transpalet, ultra hafif transpalet, poliüretan tekerlekli transpalet, elektrikli transpalet fiyatları, akülü transpalet servisi.</strong></p>

<h4>Şimdi İXTİF'i Arayın</h4>
<p><strong>Telefon:</strong> 0216 755 3 555<br>
<strong>E-posta:</strong> info@ixtif.com<br>
<strong>Firma:</strong> İXTİF İç ve Dış Ticaret A.Ş.</p>
<p>F4 201 ile deponuzun prestijini yükseltin, operasyonel maliyetlerinizi dramatik şekilde düşürün ve lojistik ekibinize bir showroom şampiyonu hediye edin.</p>
</section>
HTML
], JSON_UNESCAPED_UNICODE),

            // Features
            'features' => json_encode([
                'list' => [
                    'F4 201 transpalet 48V Li-Ion güç platformu ile 2 ton taşıma kapasitesini dar koridor operasyonlarına taşır',
                    'Tak-çıkar 24V/20Ah Li-Ion bataryalarla vardiya ortasında şarj molasına son verin, 4 adede kadar genişletilebilir modül sistemi',
                    '140 kg ultra hafif servis ağırlığı ve 400 mm kompakt şasi uzunluğu sayesinde dar koridorlarda benzersiz çeviklik',
                    'Stabilizasyon tekerleği opsiyonu ile bozuk zeminlerde bile devrilme riskini sıfırlar',
                    '0.9 kW BLDC sürüş motoru ve elektromanyetik fren sistemi ile %8 rampalarda bile tam kontrol',
                    'İXTİF ikinci el, kiralık, yedek parça ve 7/24 teknik servis ekosistemi ile yatırımınıza 360° koruma',
                    'Poliüretan çift sıra yük tekerleri ile uzun ömür ve düşük bakım maliyeti',
                    '1360 mm dönüş yarıçapı ile standart paletleri 2160 mm koridor genişliğinde rahatça döndürme',
                ],
                'branding' => [
                    'slogan' => 'Depoda hız, sahada prestij: F4 201 ile dar koridorlara hükmedin',
                    'motto' => 'İXTİF farkı ile 2 tonluk yükler bile hafifler',
                    'technical_summary' => 'F4 201, 48V Li-Ion güç paketi, 0.9 kW BLDC sürüş motoru ve 400 mm ultra kompakt şasi kombinasyonuyla dar koridor ortamlarında yüksek tork, düşük bakım ve sürekli çalışma sunar',
                ],
            ], JSON_UNESCAPED_UNICODE),

            // Primary Specs (4 Kart - Transpalet Kategorisi)
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
                ['label' => 'Çatal Uzunluğu', 'value' => '1150 mm'],
                ['label' => 'Denge Tekeri', 'value' => 'Opsiyonel'],
            ], JSON_UNESCAPED_UNICODE),

            // Highlighted Features (4 Özellik Kartı)
            'highlighted_features' => json_encode([
                [
                    'icon' => 'bolt',
                    'priority' => 1,
                    'title' => '48V Güç Paketi',
                    'description' => '0.9 kW BLDC sürüş motoru ve elektromanyetik fren ile 2 tonluk yükte bile yüksek tork',
                ],
                [
                    'icon' => 'battery-full',
                    'priority' => 2,
                    'title' => 'Tak-Çıkar Li-Ion',
                    'description' => '2x 24V/20Ah modül standart, 4 modüle kadar genişletilebilir hızlı şarj sistemi',
                ],
                [
                    'icon' => 'arrows-alt',
                    'priority' => 3,
                    'title' => 'Ultra Kompakt Şasi',
                    'description' => '400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı ile dar koridor çözümü',
                ],
                [
                    'icon' => 'shield-alt',
                    'priority' => 4,
                    'title' => 'Stabilizasyon Opsiyonu',
                    'description' => 'Bozuk zeminlerde devrilme riskini sıfırlayan güvenlik sistemi',
                ],
            ], JSON_UNESCAPED_UNICODE),

            // Use Cases (8 Senaryo)
            'use_cases' => json_encode([
                'E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat operasyonları – dar koridorlarda yüksek verimlilik',
                'Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma işlemleri',
                'Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz malzeme taşıma – Li-Ion batarya avantajı',
                'İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi ve rampa operasyonları',
                'Dış saha rampalarda stabilizasyon tekerleği opsiyonu ile güvenli taşıma – bozuk zeminlerde devrilme riski sıfır',
                'Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri – düşük bakım maliyeti',
                'Küçük ve orta ölçekli işletmelerde tek operator ile çok vardiya operasyonu – tak-çıkar batarya sistemi',
                'Darbe dayanıklı poliüretan tekerlekler ile uzun ömürlü fabrika içi malzeme taşıma',
            ], JSON_UNESCAPED_UNICODE),

            // Competitive Advantages (7 Avantaj)
            'competitive_advantages' => json_encode([
                '48V Li-Ion güç platformu ile segmentindeki en agresif hızlanma ve rampa performansı – %8 eğimde bile tam güç',
                '140 kg ultra hafif servis ağırlığı sayesinde lojistik maliyetlerinde dramatik düşüş – taşıma ve kurulum kolaylığı',
                'Tak-çıkar batarya konsepti ile 7/24 operasyonda sıfır bekleme, sıfır bakım maliyeti – vardiya ortasında değişim',
                'Stabilizasyon tekerleği opsiyonu sayesinde bozuk zeminlerde bile devrilme riskini sıfırlar – yatırım güvenliği',
                'İXTİF stoktan hızlı teslimat ve yerinde devreye alma ile son kullanıcıyı bekletmez – Türkiye genelinde servis',
                '400 mm kompakt şasi ile segmentindeki en dar dönüş yarıçapı – 2160 mm koridorda 1000x1200 mm palet manevra',
                'Poliüretan çift sıra yük tekerleri ile segmentindeki en uzun tekerlek ömrü – 5 yıla kadar değişim gerektirmez',
            ], JSON_UNESCAPED_UNICODE),

            // Target Industries (24 Sektör)
            'target_industries' => json_encode([
                'E-ticaret ve fulfillment merkezleri',
                'Perakende zincir depoları',
                'Soğuk zincir ve gıda lojistiği',
                'İçecek ve FMCG dağıtım şirketleri',
                'Endüstriyel üretim tesisleri',
                '3PL ve 4PL lojistik firmaları',
                'İlaç ve sağlık ürünleri depoları',
                'Elektronik dağıtım merkezleri',
                'Mobilya ve beyaz eşya depolama',
                'Otomotiv yedek parça depoları',
                'Tekstil ve hazır giyim depoları',
                'Kozmetik ve kişisel bakım dağıtım',
                'Yapı market ve hırdavat zincirleri',
                'Kitap ve kırtasiye dağıtım şirketleri',
                'Oyuncak ve hobi ürünleri depoları',
                'Tarım ürünleri ve tohum depoları',
                'Kimyasal ve endüstriyel malzeme depolama',
                'Cam ve seramik ürün depoları',
                'Metal işleme ve döküm tesisleri',
                'Plastik ve ambalaj malzemesi üretim tesisleri',
                'Kağıt ve karton ürünleri depoları',
                'Boya ve yapı kimyasalları depoları',
                'Medikal cihaz ve ekipman depoları',
                'Spor malzemeleri dağıtım merkezleri',
            ], JSON_UNESCAPED_UNICODE),

            // FAQ Data (12 Soru-Cevap)
            'faq_data' => json_encode([
                [
                    'question' => 'F4 201 transpalet bir vardiyada ne kadar süre çalışır?',
                    'answer' => 'Standart 2x 24V/20Ah Li-Ion batarya paketiyle F4 201, orta yoğunluklu kullanımda 6-8 saat kesintisiz çalışabilir. Dilerseniz 4 adede kadar modül ekleyerek vardiya süresini ikiye katlayabilirsiniz. Tak-çıkar batarya sistemi sayesinde boş modülü çıkarıp dolu olanı takarak operasyonunuz hiç durmaz.',
                    'sort_order' => 1,
                    'category' => 'usage',
                    'is_highlighted' => true,
                ],
                [
                    'question' => 'Dar koridorlarda F4 201 ne kadar manevra kabiliyeti sunar?',
                    'answer' => 'F4 201\'in 1360 mm dönüş yarıçapı ve 400 mm kompakt şasi uzunluğu sayesinde 2160 mm koridor genişliğinde 1000x1200 mm paletleri rahatça döndürebilirsiniz. 140 kg ultra hafif gövdesi ile operatörleriniz dar geçişlerde bile tam kontrol sağlar. Bu segmentindeki en dar dönüş yarıçapıdır.',
                    'sort_order' => 2,
                    'category' => 'technical',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'Stabilizasyon tekerleği nedir, ne işe yarar?',
                    'answer' => 'Stabilizasyon tekerleği, bozuk zeminlerde veya rampalarda ağır yüklerle çalışırken transpaleti dengede tutan opsiyonel bir aksesuardır. Büyük yüklerde devrilme riskini sıfırlar ve operatör güvenliğini artırır. F4 201\'de fabrikadan veya sonradan retrofit olarak eklenebilir.',
                    'sort_order' => 3,
                    'category' => 'technical',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'F4 201 batarya şarj süresi ne kadardır?',
                    'answer' => 'Standart 2x 24V-5A harici şarj ünitesiyle her 24V/20Ah modül 4-5 saatte tam dolum sağlar. Opsiyonel 2x 24V-10A hızlı şarj ünitesiyle bu süreyi 2-3 saate düşürebilirsiniz. Li-Ion teknolojisi sayesinde ara şarj yapabilir, batarya hafızası sorunu yaşamazsınız.',
                    'sort_order' => 4,
                    'category' => 'technical',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'İXTİF garanti ve teknik servis kapsamı nedir?',
                    'answer' => 'F4 201, standart üretici garantisi ile teslim edilir. İXTİF, Türkiye genelinde 7/24 mobil teknik servis hizmeti sunar. Yedek parçalar stoktan hemen temin edilebilir. Acil durumlarda 0216 755 3 555 numaralı hattımızdan servis talebi oluşturabilirsiniz. Ek garanti paketleri ve bakım sözleşmeleri de mevcuttur.',
                    'sort_order' => 5,
                    'category' => 'warranty',
                    'is_highlighted' => true,
                ],
                [
                    'question' => 'İkinci el veya kiralık F4 201 seçeneği var mı?',
                    'answer' => 'Evet, İXTİF hem ikinci el garanti belgeleriyle yenilenmiş F4 201 modelleri, hem de kısa ve orta vadeli kiralama seçenekleri sunar. Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri, operasyonel esneklik sağlar. Detaylı teklif için 0216 755 3 555 numaralı hattımızdan veya info@ixtif.com e-posta adresimizden bizimle iletişime geçebilirsiniz.',
                    'sort_order' => 6,
                    'category' => 'pricing',
                    'is_highlighted' => true,
                ],
                [
                    'question' => 'F4 201 standart çatal boyutları dışında seçenek var mı?',
                    'answer' => 'Evet, standart 1150x560 mm çatal dışında 900, 1000, 1220, 1350, 1500 mm uzunluklarında ve 685 mm genişliğinde çatal seçenekleri mevcuttur. Çatal kalınlığı 50 mm, genişliği 150 mm standarttır. İhtiyacınıza göre fabrikadan sipariş verebilir veya sonradan değiştirebilirsiniz.',
                    'sort_order' => 7,
                    'category' => 'technical',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'Poliüretan tekerlekler ne kadar dayanıklıdır?',
                    'answer' => 'F4 201\'in poliüretan çift sıra yük tekerleri (80x60 mm) ve sürüş tekerleri (210x70 mm) segmentindeki en uzun tekerlek ömrünü sunar. Normal kullanımda 5 yıla kadar değişim gerektirmez. Darbe dayanıklı yapısı sayesinde fabrika içi malzeme taşımada pürüzsüz hareket ve düşük bakım maliyeti sağlar.',
                    'sort_order' => 8,
                    'category' => 'technical',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'F4 201 rampalarda ne kadar performans gösterir?',
                    'answer' => '48V BLDC motorlu sürüş sistemi sayesinde F4 201, yükle %8, yüksüz %16 rampa eğiminde zorlanmadan çıkar. 0.9 kW sürüş motoru ve elektromanyetik fren kombinasyonu, acil durumlarda bile tam kontrol sağlar. Stabilizasyon tekerleği opsiyonu ile rampalarda devrilme riski sıfırlanır.',
                    'sort_order' => 9,
                    'category' => 'performance',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'Yedek parça temini ne kadar hızlıdır?',
                    'answer' => 'İXTİF, orijinal EP yedek parçalarını Türkiye genelindeki depolarında stokta tutar. Acil ihtiyaçlarınızda aynı gün veya ertesi gün teslimat sağlanır. Rutin bakım parçaları için stok bulundurma önerileri ve toplu paket fiyatları mevcuttur. info@ixtif.com adresine veya 0216 755 3 555 hattına parça talebi iletebilirsiniz.',
                    'sort_order' => 10,
                    'category' => 'service',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'F4 201 operatör eğitimi gerekir mi?',
                    'answer' => 'F4 201, yaya tipi transpalet kategorisindedir ve yasal olarak sürücü belgesi gerektirmez. Ancak İXTİF, ürün teslimatı sırasında temel operatör eğitimi ve güvenlik brifingi sunar. Ek eğitim talepleri için 0216 755 3 555 numaralı hattımızdan randevu alabilirsiniz.',
                    'sort_order' => 11,
                    'category' => 'usage',
                    'is_highlighted' => false,
                ],
                [
                    'question' => 'F4 201\'in slogan ve mottosu nedir?',
                    'answer' => 'F4 201\'in sloganı: "Depoda hız, sahada prestij: F4 201 ile dar koridorlara hükmedin." Mottosu ise: "İXTİF farkı ile 2 tonluk yükler bile hafifler." Bu model, yalnızca yük taşımak için değil, deponuzun prestijini parlatmak için tasarlandı.',
                    'sort_order' => 12,
                    'category' => 'branding',
                    'is_highlighted' => false,
                ],
            ], JSON_UNESCAPED_UNICODE),

            // Technical Specs (DİNAMİK: Her section'da _title ve _icon kullanılıyor)
            'technical_specs' => json_encode([
                'capacity' => [
                    '_title' => 'Kapasite ve Ağırlık',
                    '_icon' => 'weight-hanging',
                    'Yük Kapasitesi' => '2000 kg',
                    'Yük Merkez Mesafesi' => '600 mm',
                    'Servis Ağırlığı' => '140 kg',
                ],
                'dimensions' => [
                    '_title' => 'Boyutlar',
                    '_icon' => 'ruler-combined',
                    'Toplam Uzunluk' => '1550 mm',
                    'Toplam Genişlik' => '590 mm',
                    'Çatal Kalınlığı' => '50 mm',
                    'Çatal Genişliği' => '150 mm',
                    'Çatal Uzunluğu' => '1150 mm',
                    'Dönüş Yarıçapı' => '1360 mm',
                    'Kaldırma Yüksekliği' => '105 mm',
                ],
                'electrical' => [
                    '_title' => 'Elektrik Sistemi',
                    '_icon' => 'battery-full',
                    'Voltaj' => '48V',
                    'Kapasite' => '20 Ah',
                    'Akü Tipi' => 'Li-Ion',
                    'Batarya Sistemi' => '2x 24V/20Ah çıkarılabilir Li-Ion modül (4 adede kadar genişletilebilir)',
                    'Standart Şarj Ünitesi' => '2x 24V-5A harici şarj ünitesi',
                    'Opsiyonel Hızlı Şarj' => '2x 24V-10A harici hızlı şarj ünitesi',
                    'Sürüş Motoru Gücü' => '0.9 kW',
                    'Kaldırma Motoru Gücü' => '0.7 kW',
                ],
                'performance' => [
                    '_title' => 'Performans Verileri',
                    '_icon' => 'gauge-high',
                    'Hız (Yüklü)' => '4.5 km/h',
                    'Hız (Boş)' => '5.0 km/h',
                    'Rampa Tırmanma (Yüklü)' => '%8',
                    'Rampa Tırmanma (Boş)' => '%16',
                    'Servis Freni' => 'Elektromanyetik',
                ],
                'tyres' => [
                    '_title' => 'Tekerlekler',
                    '_icon' => 'circle-dot',
                    'Tekerlek Tipi' => 'Poliüretan',
                    'Sürüş Tekerleği' => '210 × 70 mm Poliüretan',
                    'Yük Tekerleği' => '80 × 60 mm Poliüretan çift sıra',
                ],
                'options' => [
                    '_title' => 'Opsiyonlar ve Seçenekler',
                    '_icon' => 'sliders',
                    'Stabilizasyon Tekerlekleri' => 'Opsiyonel',
                    'Çatal Uzunluk Seçenekleri' => '900, 1000, 1150, 1220, 1350, 1500 mm',
                ],
            ], JSON_UNESCAPED_UNICODE),

            // Accessories & Certifications
            'accessories' => json_encode([
                ['name' => 'Stabilizasyon Tekerlekleri', 'description' => 'Bozuk zeminlerde devrilme riskini sıfırlar'],
                ['name' => 'Hızlı Şarj Ünitesi (2x 24V-10A)', 'description' => 'Şarj süresini 2-3 saate düşürür'],
                ['name' => 'Ekstra Li-Ion Batarya Modülü (24V/20Ah)', 'description' => 'Vardiya süresini uzatır'],
                ['name' => 'Geniş Çatal (685 mm)', 'description' => 'Euro paletler için ideal'],
                ['name' => 'Uzun Çatal (1500 mm)', 'description' => 'Özel boyutlu yükler için'],
                ['name' => 'Kısa Çatal (900 mm)', 'description' => 'Dar alanlar için kompakt çözüm'],
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['name' => 'CE Sertifikası', 'description' => 'Avrupa Birliği uygunluk sertifikası'],
                ['name' => 'ISO 9001', 'description' => 'Kalite yönetim sistemi sertifikası'],
                ['name' => 'IP54', 'description' => 'Toz ve su geçirmezlik sertifikası'],
            ], JSON_UNESCAPED_UNICODE),

            // Tags
            'tags' => json_encode(['transpalet', 'li-ion', '48v', '2-ton', 'kompakt', 'ixtif', 'dar-koridor'], JSON_UNESCAPED_UNICODE),

            // Warranty
            'warranty_info' => json_encode([
                'duration_months' => 24,
                'coverage' => 'Şasi, elektrik, hidrolik ve Li-Ion batarya dahil tam garanti',
                'support' => 'İXTİF Türkiye geneli mobil servis ağı ile 7/24 destek',
            ], JSON_UNESCAPED_UNICODE),

            // Pricing
            'price_on_request' => true,
            'base_price' => null,
            'currency' => 'TRY',

            // Stock
            'stock_tracking' => true,
            'current_stock' => 0,
            'low_stock_threshold' => 1,

            // Status
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->command->info("✅ Master Product eklendi: F4-201 (ID: {$productId})");

        // 5. CHILD PRODUCTS (VARYANTLAR)
        // ✅ YENİ STRATEJİ: Varyantlara ÖZEL long_description + use_cases yazılır (UNIQUE CONTENT = Google SEO)
        // 🔗 Master'dan INHERIT edilen: features, faq_data, technical_specs, competitive_advantages, target_industries, warranty_info, accessories, certifications, highlighted_features
        // ❌ EP KULLANMA → ✅ İXTİF KULLAN (Markamız İXTİF!)
        $variants = [
            [
                'sku' => 'F4-201-1150',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 201 - 1150mm Çatal',
                'short_description' => 'Standart 1150mm çatal uzunluğu ile EUR palet (1200x800mm) taşımada maksimum verimlilik. Dar koridor operasyonlarında ideal dönüş yarıçapı ve manevra özgürlüğü sunan, endüstride en yaygın tercih edilen çatal boyutu.',
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>1150mm çatal uzunluğu, F4 201 transpalet ailesinin en popüler ve yaygın kullanılan varyantıdır.</strong></p>
<p>Standart 1200x800 mm EUR palet taşımada ideal olan 1150mm çatal, dar koridor operasyonlarında maksimum manevra kabiliyeti sağlar. E-ticaret fulfillment merkezlerinden perakende zincir depolarına, soğuk zincir lojistiğinden üretim tesislerine kadar geniş yelpazede kullanım alanına sahiptir.</p>
<ul>
<li><strong>Standart EUR palet uyumu</strong> – 1200x800 mm paletleri güvenli ve dengeli taşır</li>
<li><strong>Dar koridor çözümü</strong> – 2160 mm koridor genişliğinde rahat dönüş ve manevra</li>
<li><strong>Evrensel uyumluluk</strong> – Çoğu depo ve fabrikada modifikasyon gerektirmez</li>
<li><strong>Hızlı stoktan teslimat</strong> – En yaygın varyant olarak İXTİF depolarında sürekli stokta</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden 1150mm Çatal Seçmelisiniz?</h3>
<p>1150mm çatal uzunluğu, Avrupa lojistik standartlarına tam uyumlu olup endüstride en yaygın kullanılan boyuttur. Dar koridorlu depolarda bu uzunluk, hem yeterli yük taşıma kapasitesi hem de manevra özgürlüğü sunar. Özellikle e-ticaret fulfillment merkezlerinde sipariş hazırlama hızını artırır.</p>

<p><strong>140 kg ultra hafif gövde</strong> ve <strong>1360 mm dönüş yarıçapı</strong> ile F4 201, 1150mm çatal seçeneğinde dar geçişlerde bile operatör yorgunluğunu minimize eder. 48V Li-Ion güç platformu sayesinde rampalarda ve bozuk zeminlerde bile tam kontrol sağlar.</p>

<h4>İXTİF Stoktan Hızlı Teslimat</h4>
<p>1150mm varyantı, en popüler model olduğu için İXTİF depolarında sürekli stokta tutulur. Acil ihtiyaçlarda 24-48 saat içinde teslim edilebilir. İkinci el ve kiralık seçenekleri de bu varyant için en geniş yelpazede mevcuttur.</p>

<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,
                'use_cases' => [
                    'E-ticaret fulfillment merkezlerinde standart EUR palet (1200x800mm) sevkiyat operasyonları',
                    'Perakende zincir depolarında dar koridor raf arası malzeme transferi ve stok yönetimi',
                    'Soğuk zincir lojistiğinde 1150mm çatal ile kompakt palet taşıma ve stok rotasyonu',
                    'Üretim tesislerinde hat besleme ve mamul ürün taşıma operasyonları',
                    'Küçük ve orta ölçekli işletmelerde genel amaçlı palet taşıma çözümü',
                    '3PL lojistik firmalarında çoklu müşteri operasyonlarında evrensel uyumluluk',
                ],
            ],
            [
                'sku' => 'F4-201-1220',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 201 - 1220mm Çatal',
                'short_description' => 'Endüstriyel palet (1200x1000mm) ve IBC tank taşıma için özel tasarlanmış 1220mm uzun çatal. Ağır sanayi, kimyasal depolama ve inşaat malzemesi lojistiğinde ekstra yük dengesi ve güvenlik sağlayan profesyonel çözüm.',
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>1220mm çatal uzunluğu, standart EUR paletlerin ötesinde daha derin yükleri güvenli taşımak için tasarlanmıştır.</strong></p>
<p>1200x1000 mm endüstriyel paletler ve özel boyutlu yükler için ideal olan 1220mm çatal, özellikle ağır sanayi, kimyasal depolama ve inşaat malzemesi lojistiğinde tercih edilir. F4 201'in güçlü 48V Li-Ion platformu, uzun çatal ile bile 2 ton yükü güvenle taşır.</p>
<ul>
<li><strong>Endüstriyel palet uyumu</strong> – 1200x1000 mm ve daha derin paletleri dengeli taşır</li>
<li><strong>Ağır yük güvenliği</strong> – Uzun çatalla bile %8 rampa performansı</li>
<li><strong>Kimyasal depolama</strong> – IBC tanklar ve özel boyutlu konteynerler için ideal</li>
<li><strong>Özel uygulama çözümü</strong> – Standart dışı lojistik ihtiyaçları için mühendislik desteği</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden 1220mm Çatal Seçmelisiniz?</h3>
<p>1220mm çatal uzunluğu, özellikle ağır sanayi ve kimyasal depolama operasyonlarında standart 1150mm'nin yetersiz kaldığı noktalarda devreye girer. IBC tanklı kimyasal nakliyede, inşaat malzemesi paketlerinde ve büyük boy ambalajlı ürünlerde ekstra çatal boyu, yük dengesini ve güvenliği artırır.</p>

<p><strong>Stabilizasyon tekerleği opsiyonu</strong> ile 1220mm çatal varyantı, bozuk zeminlerde ve rampalarda bile devrilme riskini sıfırlar. 48V BLDC motor sistemi, uzun çatal ile artan ağırlık merkezini kompanze ederek operatöre tam kontrol sağlar.</p>

<h4>İXTİF Özel Uygulama Desteği</h4>
<p>1220mm varyantı, özel lojistik ihtiyaçları için İXTİF mühendislik ekibi tarafından operasyon danışmanlığı ile desteklenir. Yük dağılımı analizi, rampa güvenlik testleri ve operatör eğitimi paket içinde sunulabilir.</p>

<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,
                'use_cases' => [
                    'Kimyasal ve endüstriyel malzeme depolarında IBC tank (1000L) ve büyük konteyner taşıma',
                    'İnşaat malzemesi lojistiğinde büyük boy palet ve özel boyutlu yük operasyonları',
                    'Ağır sanayi tesislerinde döküm parçaları ve metal blok taşıma işlemleri',
                    'Boya ve yapı kimyasalları depolarında 1200x1000mm endüstriyel palet taşıma',
                    'Büyük boy ambalajlı ürünlerde (beyaz eşya, mobilya, vb.) ekstra çatal boyu ile güvenli taşıma',
                    'Özel lojistik projelerinde standart dışı yük boyutları için mühendislik çözümü',
                ],
            ],
            [
                'sku' => 'F4-201-685',
                'variant_type' => 'fork-width',
                'title' => 'İXTİF F4 201 - 685mm Geniş Çatal',
                'short_description' => 'Standart 560mm yerine 685mm geniş çatal ile %22 daha fazla temas yüzeyi. Euro palet (800x600mm) operasyonlarında yüksek istif güvenliği, dar profilli yüklerde devrilme riskini minimize eden mühendislik çözümü.',
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>685mm geniş çatal, standart 560mm genişlikten %22 daha fazla temas yüzeyi sunarak yük stabilitesini maksimize eder.</strong></p>
<p>Euro paletler (800x600mm) ve dar yükler için özel olarak tasarlanan 685mm geniş çatal, yük merkezini alçaltarak devrilme riskini azaltır. Özellikle yüksek istif operasyonlarında, bozuk zeminlerde ve rampa çıkışlarında kritik güvenlik avantajı sağlar.</p>
<ul>
<li><strong>%22 daha fazla temas yüzeyi</strong> – Yük dengesini artırır, devrilme riskini azaltır</li>
<li><strong>Euro palet optimizasyonu</strong> – 800x600mm paletler için mükemmel uyum</li>
<li><strong>Yüksek istif güvenliği</strong> – Dar yüklerde bile stabil taşıma</li>
<li><strong>Rampa performansı</strong> – Geniş temas sayesinde %8 eğimde bile tam kontrol</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden 685mm Geniş Çatal Seçmelisiniz?</h3>
<p>685mm geniş çatal, özellikle yüksek istif yapılan depolarda ve dar yük profilli ürünlerde güvenlik standardını yükseltir. Standart 560mm genişlik yerine 685mm kullanarak yük merkezini alçaltır, böylece rampa çıkışlarında ve dönüş manevralarında devrilme riski minimize olur.</p>

<p><strong>Poliüretan çift sıra yük tekerlekleri</strong>, geniş çatal ile artan temas yüzeyini optimize eder. 48V Li-Ion güç platformu, geniş çatal ile artan ağırlığı kolaylıkla kompanze ederek %8 rampa performansını korur.</p>

<h4>İXTİF Euro Palet Lojistik Çözümü</h4>
<p>685mm geniş çatal varyantı, Avrupa standartlarında çalışan lojistik firmalar için İXTİF'in özel çözümüdür. Euro palet (800x600mm) operasyonlarında maksimum verimlilik ve güvenlik sağlar. İkinci el ve kiralık seçenekler mevcuttur.</p>

<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,
                'use_cases' => [
                    'Euro palet (800x600mm) operasyonlarında yüksek istif güvenliği ve stabil taşıma',
                    'İhracat lojistik firmalarında Avrupa standartlarına uyumlu operasyon çözümü',
                    'Dar profilli yüklerde (içecek kasaları, küçük paketler) devrilme riskini minimize etme',
                    'Rampa ve bozuk zeminlerde geniş temas yüzeyi ile güvenlik artırma',
                    'Yüksek raflı depolarda yük dengesini optimize etme ve operatör güvenliği',
                    'Perakende zincirlerinde Euro palet standardına geçiş projelerinde çözüm',
                ],
            ],
            [
                'sku' => 'F4-201-TANDEM',
                'variant_type' => 'wheel-type',
                'title' => 'İXTİF F4 201 - Tandem Tekerlek',
                'short_description' => 'Tek tekerlek yerine çift denge tekeri konfigürasyonu ile yük ağırlığını geniş yüzeye dağıtan stabilite sistemi. Bozuk beton, çatlak zemin, dış saha rampaları ve eşitsiz yüzeylerde devrilme riskini sıfırlayan İSG uyumlu güvenlik çözümü.',
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>Tandem tekerlek sistemi, F4 201'in stabilite ve güvenlik standardını bozuk zeminlerde bile üst seviyeye çıkarır.</strong></p>
<p>Çift denge tekeri konfigürasyonu, özellikle dış saha rampalarda, bozuk beton zeminlerde ve eşitsiz yüzeylerde operasyon yapan işletmeler için kritik bir güvenlik özelliğidir. Standart tek tekerlek yerine tandem sistem, yük ağırlığını daha geniş yüzeye dağıtarak devrilme riskini sıfırlar.</p>
<ul>
<li><strong>Çift tekerlek dengesi</strong> – Yük ağırlığını eşit dağıtarak stabiliteyi artırır</li>
<li><strong>Bozuk zemin performansı</strong> – Çatlak ve eşitsiz yüzeylerde bile pürüzsüz hareket</li>
<li><strong>Rampa güvenliği</strong> – %8 eğimde bile devrilme riski sıfır</li>
<li><strong>Operatör güvenliği</strong> – Yüksek yüklerde bile kontrol kaybı yaşanmaz</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden Tandem Tekerlek Seçmelisiniz?</h3>
<p>Tandem tekerlek sistemi, özellikle dış saha operasyonlarında ve endüstriyel ortamlarda güvenlik standartlarını karşılamak için tasarlanmıştır. Standart tek denge tekeri, bozuk zeminlerde ve rampalarda bazen yetersiz kalabilir. Tandem sistem, çift tekerlek ile yük ağırlığını daha geniş yüzeye dağıtarak bu riski ortadan kaldırır.</p>

<p><strong>Poliüretan tandem tekerlekler</strong>, darbe dayanıklı yapısı sayesinde çatlak beton, asfalt ve kırık fayans gibi bozuk zeminlerde uzun ömür sunar. 48V Li-Ion güç platformu, tandem sistem ile artan ağırlığı kolaylıkla taşır.</p>

<h4>İXTİF Güvenlik Odaklı Çözüm</h4>
<p>Tandem tekerlek varyantı, İş Sağlığı ve Güvenliği standartlarına tam uyumlu olup özellikle inşaat sahaları, liman operasyonları ve dış mekan lojistiğinde tercih edilir. İXTİF, bu varyantı operatör güvenlik eğitimi ile birlikte sunar.</p>

<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,
                'use_cases' => [
                    'İnşaat sahalarında bozuk beton ve toprak zemin üzerinde güvenli malzeme taşıma',
                    'Liman ve rıhtım operasyonlarında eşitsiz yüzeylerde ağır yük taşıma güvenliği',
                    'Dış mekan rampalarda %8 eğimde bile devrilme riskini sıfırlama',
                    'Fabrika içi çatlak ve tamir görmüş beton zeminlerde uzun ömürlü operasyon',
                    'İş Sağlığı ve Güvenliği standartları gerektiren operasyonlarda yasal uyumluluk',
                    'Yüksek sigorta primi olan işletmelerde risk azaltma ve prim indirimi sağlama',
                ],
            ],
            [
                'sku' => 'F4-201-EXT-BAT',
                'variant_type' => 'battery',
                'title' => 'İXTİF F4 201 - Extended Battery',
                'short_description' => 'Standart 2 modül yerine 4x 24V/20Ah Li-Ion batarya kapasitesi ile 12-16 saat kesintisiz çalışma garantisi. E-ticaret fulfillment, havaalanı kargo ve 7/24 operasyonlarda şarj molası vermeden tam gün verimlilik sağlayan premium güç çözümü.',
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>Extended Battery varyantı, 4x 24V/20Ah Li-Ion modül ile 12-16 saat kesintisiz çalışma sunar – tek vardiyada çift verimlilik.</strong></p>
<p>Standart 2 modül yerine 4 modül Li-Ion batarya sistemi ile donatılan bu varyant, 7/24 operasyon gerektiren işletmeler için tasarlanmıştır. E-ticaret fulfillment merkezleri, havaalanı kargo terminalleri ve yoğun vardiya operasyonlarında şarj molası vermeden tam gün çalışabilir.</p>
<ul>
<li><strong>4x 24V/20Ah Li-Ion modül</strong> – Standart 2 modülün iki katı kapasite</li>
<li><strong>12-16 saat çalışma</strong> – Tek vardiyada çift operasyon kapasitesi</li>
<li><strong>Sıfır bekleme süresi</strong> – Tak-çıkar sistem ile ara şarj gerektirmez</li>
<li><strong>Yüksek ROI</strong> – Lojistik maliyetlerini %60'a kadar düşürür</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden Extended Battery Seçmelisiniz?</h3>
<p>Extended Battery varyantı, özellikle yüksek sipariş hacimli e-ticaret depolarında ve 7/24 operasyon gerektiren lojistik merkezlerinde ROI'yi dramatik şekilde artırır. Standart 2 modül ile 6-8 saat çalışma süresi yetmiyorsa, 4 modül ile 12-16 saat kesintisiz operasyon mümkün hale gelir.</p>

<p><strong>Tak-çıkar Li-Ion sistemi</strong> sayesinde, 4 modülden 2'si boşaldığında diğer 2 modül ile çalışmaya devam edebilir, aynı anda boşalan modüller şarj edilebilir. Bu sistem, vardiya ortasında şarj molası vermeden tam gün çalışmayı garantiler.</p>

<h4>İXTİF 7/24 Operasyon Çözümü</h4>
<p>Extended Battery varyantı, İXTİF'in yüksek hacimli lojistik operasyonlar için geliştirdiği premium çözümdür. 4 modül Li-Ion batarya, opsiyonel hızlı şarj ünitesi (2x 24V-10A) ile birlikte sunularak vardiya verimliliğini maksimize eder. Kiralama ve leasing seçenekleri mevcuttur.</p>

<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,
                'use_cases' => [
                    'E-ticaret fulfillment merkezlerinde 7/24 sipariş hazırlama ve sevkiyat operasyonları',
                    'Havaalanı kargo terminallerinde kesintisiz yükleme boşaltma ve palet transferi',
                    'Yüksek vardiya lojistiğinde (3 vardiya) tek transpalet ile çoklu operatör kullanımı',
                    '3PL ve 4PL firmalarında yoğun müşteri trafiğinde kesintisiz hizmet sağlama',
                    'Soğuk zincir depolarında uzun vardiya operasyonlarında Li-Ion avantajı (düşük sıcaklık performansı)',
                    'Kiralama filolarında yüksek kullanım oranı ile ROI maksimizasyonu',
                ],
            ],
        ];

        foreach ($variants as $v) {
            $childId = DB::table('shop_products')->insertGetId([
                'sku' => $v['sku'],
                'parent_product_id' => $productId,
                'is_master_product' => false,
                'variant_type' => $v['variant_type'],
                'category_id' => $categoryId,
                'brand_id' => $brandId,

                // ✅ VARYANTA ÖZEL UNIQUE CONTENT (Google SEO için)
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),

                // 🔗 Master'dan INHERIT edilen alanlar: features, faq_data, technical_specs, competitive_advantages, target_industries, warranty_info, accessories, certifications, highlighted_features

                'price_on_request' => true,
                'is_active' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->command->info("  ✅ Varyant eklendi: {$v['sku']} (ID: {$childId})");
        }

        // 6. İSTATİSTİK
        $totalProducts = 1 + count($variants); // Master + Varyantlar
        $this->command->info("\n🎉 F4 201 Transpalet Seeder Tamamlandı!");
        $this->command->info("📊 İstatistik:");
        $this->command->info("   - Master Product: 1");
        $this->command->info("   - Varyantlar: " . count($variants));
        $this->command->info("   - Toplam: {$totalProducts} ürün");
        $this->command->info("   - Kategori: Transpalet (ID: {$categoryId})");
        $this->command->info("   - Marka: İXTİF (ID: {$brandId})");
        $this->command->info("\n📞 İletişim: 0216 755 3 555 | info@ixtif.com");
    }
}
