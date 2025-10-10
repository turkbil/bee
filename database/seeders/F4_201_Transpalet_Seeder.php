<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F4 201 - 2 Ton 48V Li-Ion Transpalet
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/02_F4-201-brochure-CE.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 */
class F4_201_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F4 201 Li-Ion Transpalet ekleniyor...');

        // 1) Marka bilgisi (İXTİF)
        DB::table('shop_brands')->updateOrInsert(
            ['brand_id' => 1],
            [
                'title' => json_encode(['tr' => 'İXTİF', 'en' => 'İXTİF', 'vs.' => '...'], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => 'ixtif', 'en' => 'ixtif', 'vs.' => '...'], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'tr' => "İXTİF - Türkiye'nin İstif Pazarı! Forklift, transpalet ve depolama ekipmanlarında 360° çözüm sunuyoruz.",
                    'en' => "İXTİF - Türkiye'nin İstif Pazarı! Forklift, transpalet ve depolama ekipmanlarında 360° çözüm sunuyoruz.",
                    'vs.' => '...'
                ], JSON_UNESCAPED_UNICODE),
                'logo_url' => 'brands/ixtif-logo.png',
                'website_url' => 'https://www.ixtif.com',
                'country_code' => 'TR',
                'founded_year' => 1995,
                'headquarters' => 'İstanbul, Türkiye',
                'certifications' => json_encode([
                    ['name' => 'CE', 'year' => 2010],
                    ['name' => 'ISO 9001', 'year' => 2012]
                ], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'is_featured' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ İXTİF markası güncellendi');

        // 2) Kategori bilgisi (Transpaletler)
        DB::table('shop_categories')->updateOrInsert(
            ['category_id' => 165],
            [
                'parent_id' => null,
                'title' => json_encode(['tr' => 'Transpaletler', 'en' => 'Transpaletler', 'vs.' => '...'], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => 'transpaletler', 'en' => 'transpaletler', 'vs.' => '...'], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'tr' => 'Li-Ion teknolojili kompakt transpalet çözümleri ile hızlı ve güvenli taşıma.',
                    'en' => 'Li-Ion teknolojili kompakt transpalet çözümleri ile hızlı ve güvenli taşıma.',
                    'vs.' => '...'
                ], JSON_UNESCAPED_UNICODE),
                'image_url' => 'categories/transpaletler.jpg',
                'icon_class' => 'fa-solid fa-dolly',
                'level' => 1,
                'path' => '165',
                'sort_order' => 3,
                'is_active' => 1,
                'show_in_menu' => 1,
                'show_in_homepage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Transpalet kategorisi hazır');

        // 3) Mevcut kayıtları temizle
        DB::table('shop_products')->whereIn('product_id', [1101])->delete();
        DB::table('shop_products')->where('sku', 'F4-201')->delete();

        $this->command->info('🧹 Eski F4 201 kayıtları temizlendi');

        // 4) Ürün verileri
        $title = [
            'tr' => 'F4 201 - 2 Ton 48V Li-Ion Transpalet',
            'en' => 'F4 201 - 2 Ton 48V Li-Ion Transpalet',
            'vs.' => '...'
        ];

        $slug = [
            'tr' => 'f4-201-2-ton-48v-li-ion-transpalet',
            'en' => 'f4-201-2-ton-48v-li-ion-transpalet',
            'vs.' => '...'
        ];

        $shortDescription = [
            'tr' => "F4 201 transpalet; 48V Li-Ion güç paketi, 2 ton akülü taşıma kapasitesi ve 400 mm ultra kompakt şasi ile dar koridorlarda bile hız rekoru kıran İXTİF transpalet çözümüdür.",
            'en' => "F4 201 transpalet; 48V Li-Ion güç paketi, 2 ton akülü taşıma kapasitesi ve 400 mm ultra kompakt şasi ile dar koridorlarda bile hız rekoru kıran İXTİF transpalet çözümüdür.",
            'vs.' => '...'
        ];

        $longDescription = [
            'tr' => <<<HTML
<section class="marketing-intro">
    <p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz “Bu transpaleti nereden aldınız?” diye soracak.</strong> Gücünü 48V Li-Ion sistemden alan bu yıldız, 140 kg gibi inanılmaz hafif servis ağırlığıyla 2 tonluk yükleri çocuk oyuncağına çeviriyor. Sadece butona basarak bataryayı sökün, yenisini takın, sahaya geri dönün – kesintisiz performansın adı artık F4 201.</p>
    <p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı. Göz alıcı kırmızı çizgiler, yeni nesil kare profilli tiller başı ve premium dokunuşlu kontrol elemanlarıyla operatörünüz kendini özel hissederken, müşterileriniz markanızın gücünü sahada yaşayacak.</p>
    <ul>
        <li><strong>Bir vardiyada iki kat iş</strong> – 164 adet/40’ konteyner yerleşimi ile lojistik maliyetleriniz %50’ye kadar düşsün.</li>
        <li><strong>Showroom etkisi</strong> – Ultra kompakt 400 mm şasi dar koridorlarda bile vitrinde yürür gibi ilerler.</li>
        <li><strong>Prestijli hizmet sözü</strong> – Placebo değil, gerçek sonuç: Türkiye genelinde 7/24 İXTİF servis ağı yanınızda.</li>
    </ul>
</section>
<section class="marketing-body">
    <p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur; isterseniz 4 modüle kadar yükselterek vardiya planınızı baştan yazabilirsiniz. Stabilizasyon tekerleği opsiyonu ve elektromanyetik fren sistemi, en zorlu rampalarda bile “kayma” kelimesini sözlüğünüzden siler.</p>
    <p>F4 201’i tercih eden işletmeler; sessiz çalışma, sıfır bakım gerektiren Li-Ion teknolojisi ve kablolara bağlı kalmadan şarj edilebilme özgürlüğü sayesinde rakiplerinin birkaç adım önünde ilerliyor.</p>
    <p>İXTİF'in ikinci el, kiralık, yedek parça ve teknik servis programları ile F4 201 yatırımınız tam koruma altında; Türkiye genelinde mobil ekipler dakikalar içinde yanınızda.</p>
    <p><strong>SEO Anahtar Kelimeleri:</strong> F4 201 transpalet, 48V Li-Ion transpalet, 2 ton akülü transpalet, İXTİF transpalet, dar koridor transpalet çözümleri.</p>
    <p><strong>Şimdi İXTİF’i arayın:</strong> 0216 755 3 555 veya <strong>info@ixtif.com</strong>. Bugün teklif alın, yarın F4 201’i sahaya indirin. Türkiye’nin İstif Pazarı sizi bekliyor.</p>
</section>
HTML,
            'en' => <<<HTML
<section class="marketing-intro">
    <p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz “Bu transpaleti nereden aldınız?” diye soracak.</strong> Gücünü 48V Li-Ion sistemden alan bu yıldız, 140 kg gibi inanılmaz hafif servis ağırlığıyla 2 tonluk yükleri çocuk oyuncağına çeviriyor. Sadece butona basarak bataryayı sökün, yenisini takın, sahaya geri dönün – kesintisiz performansın adı artık F4 201.</p>
    <p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı. Göz alıcı kırmızı çizgiler, yeni nesil kare profilli tiller başı ve premium dokunuşlu kontrol elemanlarıyla operatörünüz kendini özel hissederken, müşterileriniz markanızın gücünü sahada yaşayacak.</p>
    <ul>
        <li><strong>Bir vardiyada iki kat iş</strong> – 164 adet/40’ konteyner yerleşimi ile lojistik maliyetleriniz %50’ye kadar düşsün.</li>
        <li><strong>Showroom etkisi</strong> – Ultra kompakt 400 mm şasi dar koridorlarda bile vitrinde yürür gibi ilerler.</li>
        <li><strong>Prestijli hizmet sözü</strong> – Placebo değil, gerçek sonuç: Türkiye genelinde 7/24 İXTİF servis ağı yanınızda.</li>
    </ul>
</section>
<section class="marketing-body">
    <p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur; isterseniz 4 modüle kadar yükselterek vardiya planınızı baştan yazabilirsiniz. Stabilizasyon tekerleği opsiyonu ve elektromanyetik fren sistemi, en zorlu rampalarda bile “kayma” kelimesini sözlüğünüzden siler.</p>
    <p>F4 201’i tercih eden işletmeler; sessiz çalışma, sıfır bakım gerektiren Li-Ion teknolojisi ve kablolara bağlı kalmadan şarj edilebilme özgürlüğü sayesinde rakiplerinin birkaç adım önünde ilerliyor.</p>
    <p>İXTİF'in ikinci el, kiralık, yedek parça ve teknik servis programları ile F4 201 yatırımınız tam koruma altında; Türkiye genelinde mobil ekipler dakikalar içinde yanınızda.</p>
    <p><strong>SEO Anahtar Kelimeleri:</strong> F4 201 transpalet, 48V Li-Ion transpalet, 2 ton akülü transpalet, İXTİF transpalet, dar koridor transpalet çözümleri.</p>
    <p><strong>Şimdi İXTİF’i arayın:</strong> 0216 755 3 555 veya <strong>info@ixtif.com</strong>. Bugün teklif alın, yarın F4 201’i sahaya indirin. Türkiye’nin İstif Pazarı sizi bekliyor.</p>
</section>
HTML,
            'vs.' => '...'
        ];

        $features = [
            'tr' => [
                'F4 201 transpalet 48V Li-Ion güç platformu ile 2 ton akülü taşıma kapasitesini dar koridor operasyonlarına taşır.',
                'Tak-çıkar 24V/20Ah Li-Ion bataryalarla vardiya ortasında şarj molasına son verin.',
                '140 kg servis ağırlığı ve 400 mm şasi uzunluğu sayesinde dar koridorlarda benzersiz çeviklik sağlar.',
                'Stabilizasyon tekerleği opsiyonu, bozuk zeminlerde bile yükünüzü sarsmadan ilerletir.',
                'İXTİF stoktan hızlı teslimat ve yerinde kurulum desteğiyle son kullanıcıya anında çözüm sağlar.',
                'İXTİF ikinci el, kiralık, yedek parça ve teknik servis ekosistemi ile yatırımınıza 360° koruma sağlar.'
            ],
            'en' => [
                'F4 201 transpalet 48V Li-Ion güç platformu ile 2 ton akülü taşıma kapasitesini dar koridor operasyonlarına taşır.',
                'Tak-çıkar 24V/20Ah Li-Ion bataryalarla vardiya ortasında şarj molasına son verin.',
                '140 kg servis ağırlığı ve 400 mm şasi uzunluğu sayesinde dar koridorlarda benzersiz çeviklik sağlar.',
                'Stabilizasyon tekerleği opsiyonu, bozuk zeminlerde bile yükünüzü sarsmadan ilerletir.',
                'İXTİF stoktan hızlı teslimat ve yerinde kurulum desteğiyle son kullanıcıya anında çözüm sağlar.',
                'İXTİF ikinci el, kiralık, yedek parça ve teknik servis ekosistemi ile yatırımınıza 360° koruma sağlar.'
            ],
            'vs.' => ['...']
        ];

        $highlightedFeatures = [
            [
                'icon' => 'bolt',
                'priority' => 1,
                'title' => ['tr' => '48V Güç Paketi', 'en' => '48V Güç Paketi', 'vs.' => '...'],
                'description' => [
                    'tr' => '0.9 kW BLDC sürüş motoru ve elektromanyetik fren ile 2 tonluk yükte bile yüksek tork.',
                    'en' => '0.9 kW BLDC sürüş motoru ve elektromanyetik fren ile 2 tonluk yükte bile yüksek tork.',
                    'vs.' => '...'
                ]
            ],
            [
                'icon' => 'battery-full',
                'priority' => 2,
                'title' => ['tr' => 'Tak-Çıkar Li-Ion', 'en' => 'Tak-Çıkar Li-Ion', 'vs.' => '...'],
                'description' => [
                    'tr' => '2x 24V/20Ah modül standart, 4 modüle kadar genişletilebilir hızlı şarj sistemi.',
                    'en' => '2x 24V/20Ah modül standart, 4 modüle kadar genişletilebilir hızlı şarj sistemi.',
                    'vs.' => '...'
                ]
            ],
            [
                'icon' => 'arrows-alt',
                'priority' => 3,
                'title' => ['tr' => 'Ultra Kompakt Şasi', 'en' => 'Ultra Kompakt Şasi', 'vs.' => '...'],
                'description' => [
                    'tr' => '400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı ile dar koridor çözümü.',
                    'en' => '400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı ile dar koridor çözümü.',
                    'vs.' => '...'
                ]
            ]
        ];

        $mediaGallery = [
            ['type' => 'image', 'url' => 'products/f4-201/main.jpg', 'is_primary' => true, 'sort_order' => 1],
            ['type' => 'image', 'url' => 'products/f4-201/battery-module.jpg', 'is_primary' => false, 'sort_order' => 2],
            ['type' => 'image', 'url' => 'products/f4-201/stabilizer-wheel.jpg', 'is_primary' => false, 'sort_order' => 3],
        ];

        $useCases = [
            'tr' => [
                'E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat operasyonları',
                'Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma',
                'Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz malzeme taşıma',
                'İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi',
                'Dış saha rampalarda stabilizasyon tekerleği ile güvenli taşıma',
                'Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri'
            ],
            'en' => [
                'E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat operasyonları',
                'Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma',
                'Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz malzeme taşıma',
                'İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi',
                'Dış saha rampalarda stabilizasyon tekerleği ile güvenli taşıma',
                'Kiralama filolarında yüksek kârlılık sağlayan Li-Ion platform çözümleri'
            ],
            'vs.' => ['...']
        ];

        $competitiveAdvantages = [
            'tr' => [
                '48V Li-Ion güç platformu ile segmentindeki en agresif hızlanma ve rampa performansı',
                '140 kg’lık ultra hafif servis ağırlığı sayesinde lojistik maliyetlerinde dramatik düşüş',
                'Tak-çıkar batarya konsepti ile 7/24 operasyonda sıfır bekleme, sıfır bakım maliyeti',
                'Stabilizasyon tekerleği opsiyonu sayesinde bozuk zeminlerde bile devrilme riskini sıfırlar',
                'İXTİF stoktan hızlı teslimat ve yerinde devreye alma ile son kullanıcıyı bekletmez',
                'İXTİF’in Türkiye geneli mobil servis ağı, ikinci el & kiralama programları ve yedek parça tedarikiyle yatırımınızı 360° korur'
            ],
            'en' => [
                '48V Li-Ion güç platformu ile segmentindeki en agresif hızlanma ve rampa performansı',
                '140 kg’lık ultra hafif servis ağırlığı sayesinde lojistik maliyetlerinde dramatik düşüş',
                'Tak-çıkar batarya konsepti ile 7/24 operasyonda sıfır bekleme, sıfır bakım maliyeti',
                'Stabilizasyon tekerleği opsiyonu sayesinde bozuk zeminlerde bile devrilme riskini sıfırlar',
                'İXTİF stoktan hızlı teslimat ve yerinde devreye alma ile son kullanıcıyı bekletmez',
                'İXTİF’in Türkiye geneli mobil servis ağı, ikinci el & kiralama programları ve yedek parça tedarikiyle yatırımınızı 360° korur'
            ],
            'vs.' => ['...']
        ];

        $targetIndustries = [
            'tr' => [

                'E-ticaret & fulfillment merkezleri',
                'Perakende zincir depoları',
                'Soğuk zincir ve gıda lojistiği',
                'İçecek ve FMCG dağıtım şirketleri',
                'Endüstriyel üretim tesisleri',
                '3PL lojistik firmaları',
                'İlaç ve sağlık depoları',
                'Elektronik dağıtım merkezleri',
                'Mobilya & beyaz eşya depolama',
                'Otomotiv yedek parça depoları',
                'Tarım ve tohum depolama tesisleri',
                'Yerel belediye depoları',
                'Enerji ve altyapı malzeme depoları',
                'Perakende hızlı tüketim zincirleri',
                'Liman içi malzeme taşıma operasyonları',
                'Havaalanı kargo terminalleri',
                'Küçük ve orta ölçekli üretim atölyeleri',
                'Lüks perakende backstore yönetimi',
                'Ev & yapı market stok sahaları',
                'Kargo ve kurye transfer merkezleri',
            ],
            'en' => [

                'E-ticaret & fulfillment merkezleri',
                'Perakende zincir depoları',
                'Soğuk zincir ve gıda lojistiği',
                'İçecek ve FMCG dağıtım şirketleri',
                'Endüstriyel üretim tesisleri',
                '3PL lojistik firmaları',
                'İlaç ve sağlık depoları',
                'Elektronik dağıtım merkezleri',
                'Mobilya & beyaz eşya depolama',
                'Otomotiv yedek parça depoları',
                'Tarım ve tohum depolama tesisleri',
                'Yerel belediye depoları',
                'Enerji ve altyapı malzeme depoları',
                'Perakende hızlı tüketim zincirleri',
                'Liman içi malzeme taşıma operasyonları',
                'Havaalanı kargo terminalleri',
                'Küçük ve orta ölçekli üretim atölyeleri',
                'Lüks perakende backstore yönetimi',
                'Ev & yapı market stok sahaları',
                'Kargo ve kurye transfer merkezleri',
            ],
            'vs.' => ['...']
        ];

        $faqData = [
            [
                'question' => [
                    'tr' => 'F4 201 bir vardiyada kaç saate kadar çalışabilir?',
                    'en' => 'F4 201 bir vardiyada kaç saate kadar çalışabilir?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => 'Standart pakette gelen 2 adet 24V/20Ah Li-Ion modül ile tek şarjda 6 saate kadar kesintisiz çalışır. Yedek modüller ile batarya değişim süresi 60 saniyeden kısa olduğu için vardiya boyunca enerji kaybı yaşamazsınız.',
                    'en' => 'Standart pakette gelen 2 adet 24V/20Ah Li-Ion modül ile tek şarjda 6 saate kadar kesintisiz çalışır. Yedek modüller ile batarya değişim süresi 60 saniyeden kısa olduğu için vardiya boyunca enerji kaybı yaşamazsınız.',
                    'vs.' => '...'
                ],
                'sort_order' => 1
            ],
            [
                'question' => [
                    'tr' => 'Dar koridorlarda manevra kabiliyeti nasıldır?',
                    'en' => 'Dar koridorlarda manevra kabiliyeti nasıldır?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => '400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı sayesinde 3,2 metreye kadar dar koridorlarda bile rahatlıkla döner. Özellikle yoğun raflı depolarda palet değişimini hızlandırır.',
                    'en' => '400 mm gövde uzunluğu ve 1360 mm dönüş yarıçapı sayesinde 3,2 metreye kadar dar koridorlarda bile rahatlıkla döner. Özellikle yoğun raflı depolarda palet değişimini hızlandırır.',
                    'vs.' => '...'
                ],
                'sort_order' => 2
            ],
            [
                'question' => [
                    'tr' => 'Stabilizasyon tekerlekleri hangi durumlarda gerekli?',
                    'en' => 'Stabilizasyon tekerlekleri hangi durumlarda gerekli?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => 'Düz olmayan zeminlerde, rampa giriş çıkışlarında veya 2 ton üzeri ağır yüklerde stabilizasyon tekerleği opsiyonu tavsiye edilir. Bu sayede şasi salınımı azalır ve yük güvenliği üst seviyeye çıkar.',
                    'en' => 'Düz olmayan zeminlerde, rampa giriş çıkışlarında veya 2 ton üzeri ağır yüklerde stabilizasyon tekerleği opsiyonu tavsiye edilir. Bu sayede şasi salınımı azalır ve yük güvenliği üst seviyeye çıkar.',
                    'vs.' => '...'
                ],
                'sort_order' => 3
            ],
            [
                'question' => [
                    'tr' => 'Bataryalar nasıl şarj edilir, özel altyapı gerekir mi?',
                    'en' => 'Bataryalar nasıl şarj edilir, özel altyapı gerekir mi?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => 'Harici 24V/5A şarj üniteleri ile normal 220V priz üzerinden bataryaları dışarıda şarj edebilirsiniz. Özel altyapıya veya endüstriyel prizlere gerek yoktur. Hızlı şarj isteyen filolar için 24V/10A seçenekleri sunulur.',
                    'en' => 'Harici 24V/5A şarj üniteleri ile normal 220V priz üzerinden bataryaları dışarıda şarj edebilirsiniz. Özel altyapıya veya endüstriyel prizlere gerek yoktur. Hızlı şarj isteyen filolar için 24V/10A seçenekleri sunulur.',
                    'vs.' => '...'
                ],
                'sort_order' => 4
            ],
            [
                'question' => [
                    'tr' => 'Garantisi ve servis desteği nasıl işliyor?',
                    'en' => 'Garantisi ve servis desteği nasıl işliyor?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => 'F4 201 için 24 ay tam kapsamlı garanti sunuyoruz; şasi, motor, elektronik ve Li-Ion bataryalar bu kapsamda. İXTİF Türkiye genelinde mobil servis araçları ile 7/24 destek sağlar, Türkiye genelinde uzman teknik servis ağımızla hızlı destek sağlanır.',
                    'en' => 'F4 201 için 24 ay tam kapsamlı garanti sunuyoruz; şasi, motor, elektronik ve Li-Ion bataryalar bu kapsamda. İXTİF Türkiye genelinde mobil servis araçları ile 7/24 destek sağlar, Türkiye genelinde uzman teknik servis ağımızla hızlı destek sağlanır.',
                    'vs.' => '...'
                ],
                'sort_order' => 5
            ],
            [
                'question' => [
                    'tr' => 'İkinci el, kiralık veya finansman seçenekleri mevcut mu?',
                    'en' => 'İkinci el, kiralık veya finansman seçenekleri mevcut mu?',
                    'vs.' => '...'
                ],
                'answer' => [
                    'tr' => 'Evet, İXTİF olarak sıfır satışın yanı sıra ikinci el, kiralık ve operasyonel leasing çözümleri sunuyoruz. Filonuzun büyüklüğüne göre 12-36 ay arası ödeme planları hazırlıyor, yedek parça ve teknik servis paketlerini birlikte planlıyoruz. Detaylı teklif için 0216 755 3 555 numarasını arayabilir veya info@ixtif.com adresine yazabilirsiniz.',
                    'en' => 'Evet, İXTİF olarak sıfır satışın yanı sıra ikinci el, kiralık ve operasyonel leasing çözümleri sunuyoruz. Filonuzun büyüklüğüne göre 12-36 ay arası ödeme planları hazırlıyor, yedek parça ve teknik servis paketlerini birlikte planlıyoruz. Detaylı teklif için 0216 755 3 555 numarasını arayabilir veya info@ixtif.com adresine yazabilirsiniz.',
                    'vs.' => '...'
                ],
                'sort_order' => 6
            ]
        ];

        $technicalSpecs = [
            'capacity' => [
                'load_capacity' => ['value' => 2000, 'unit' => 'kg'],
                'load_center_distance' => ['value' => 600, 'unit' => 'mm'],
                'service_weight' => ['value' => 140, 'unit' => 'kg'],
                'axle_load_laden' => ['front' => 620, 'rear' => 1520, 'unit' => 'kg'],
                'axle_load_unladen' => ['front' => 100, 'rear' => 40, 'unit' => 'kg'],
            ],
            'dimensions' => [
                'overall_length' => ['value' => 1550, 'unit' => 'mm'],
                'length_to_face_of_forks' => ['value' => 400, 'unit' => 'mm'],
                'overall_width' => ['standard' => 590, 'wide' => 695, 'unit' => 'mm'],
                'fork_dimensions' => ['thickness' => 50, 'width' => 150, 'length' => 1150, 'unit' => 'mm'],
                'fork_spread' => ['standard' => 560, 'wide' => 685, 'unit' => 'mm'],
                'ground_clearance' => ['value' => 30, 'unit' => 'mm'],
                'turning_radius' => ['value' => 1360, 'unit' => 'mm'],
                'aisle_width_1000x1200' => ['value' => 2160, 'unit' => 'mm'],
                'aisle_width_800x1200' => ['value' => 2025, 'unit' => 'mm'],
                'lift_height' => ['value' => 105, 'unit' => 'mm'],
                'lowered_height' => ['value' => 85, 'unit' => 'mm'],
                'tiller_height' => ['min' => 750, 'max' => 1190, 'unit' => 'mm'],
            ],
            'performance' => [
                'travel_speed' => ['laden' => 4.5, 'unladen' => 5.0, 'unit' => 'km/h'],
                'lift_speed' => ['laden' => 0.016, 'unladen' => 0.020, 'unit' => 'm/s'],
                'lowering_speed' => ['laden' => 0.058, 'unladen' => 0.046, 'unit' => 'm/s'],
                'max_gradeability' => ['laden' => 8, 'unladen' => 16, 'unit' => '%'],
                'turnover_output' => ['value' => 88, 'unit' => 't/h'],
                'turnover_efficiency' => ['value' => 473.12, 'unit' => 't/kWh'],
            ],
            'electrical' => [
                'drive_motor_rating' => ['value' => 0.9, 'unit' => 'kW', 'duty' => 'S2 60 min'],
                'lift_motor_rating' => ['value' => 0.7, 'unit' => 'kW', 'duty' => 'S3 15%'],
                'battery_system' => [
                    'voltage' => 48,
                    'capacity' => 20,
                    'unit' => 'V/Ah',
                    'configuration' => '2x 24V/20Ah değiştirilebilir Li-Ion modül (4 adede kadar genişletilebilir)'
                ],
                'battery_weight' => ['value' => 10, 'unit' => 'kg', 'note' => 'Her bir Li-Ion modül için'],
                'charger_options' => [
                    'standard' => '2x 24V-5A harici şarj ünitesi',
                    'optional' => ['2x 24V-10A hızlı şarj ünitesi']
                ],
                'energy_consumption' => ['value' => 0.18, 'unit' => 'kWh/h'],
                'drive_control' => 'BLDC sürüş kontrolü',
                'steering_design' => 'Mekanik',
                'noise_level' => ['value' => 74, 'unit' => 'dB(A)'],
            ],
            'tyres' => [
                'type' => 'Poliüretan',
                'drive_wheel' => '210 × 70 mm Poliüretan',
                'load_wheel' => '80 × 60 mm Poliüretan (çift sıra standart)',
                'caster_wheel' => '74 × 30 mm Poliüretan',
                'wheel_configuration' => '1x / 4 (çekiş/yük)'
            ],
            'options' => [
                'stabilizing_wheels' => ['standard' => false, 'optional' => true],
                'fork_lengths_mm' => [900, 1000, 1150, 1220, 1350, 1500],
                'fork_spreads_mm' => [560, 685],
                'battery_expansion' => ['standard' => '2x 24V/20Ah', 'max' => '4x 24V/20Ah']
            ]
        ];

        $warrantyInfo = [
            'tr' => [
                'duration_months' => 24,
                'coverage' => 'Şasi, elektrik, hidrolik ve Li-Ion batarya dahil tam garanti.',
                'support' => 'İXTİF Türkiye geneli mobil servis ağı ile 7/24 destek.'
            ],
            'en' => [
                'duration_months' => 24,
                'coverage' => 'Şasi, elektrik, hidrolik ve Li-Ion batarya dahil tam garanti.',
                'support' => 'İXTİF Türkiye geneli mobil servis ağı ile 7/24 destek.'
            ],
            'vs.' => '...'
        ];

        DB::table('shop_products')->insert([
            'product_id' => 1101,
            'category_id' => 165,
            'brand_id' => 1,
            'sku' => 'F4-201',
            'model_number' => 'F4 201',
            'barcode' => null,
            'title' => json_encode($title, JSON_UNESCAPED_UNICODE),
            'slug' => json_encode($slug, JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode($shortDescription, JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode($longDescription, JSON_UNESCAPED_UNICODE),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'base_price' => null,
            'compare_at_price' => null,
            'cost_price' => null,
            'currency' => 'TRY',
            'deposit_required' => 1,
            'deposit_amount' => null,
            'deposit_percentage' => 30,
            'installment_available' => 1,
            'max_installments' => 12,
            'stock_tracking' => 1,
            'current_stock' => 0,
            'low_stock_threshold' => 1,
            'allow_backorder' => 0,
            'lead_time_days' => 45,
            'weight' => 140,
            'dimensions' => json_encode(['length' => 1550, 'width' => 590, 'height' => 105, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode($technicalSpecs, JSON_UNESCAPED_UNICODE),
            'features' => json_encode($features, JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode($highlightedFeatures, JSON_UNESCAPED_UNICODE),
            'media_gallery' => json_encode($mediaGallery, JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode($useCases, JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode($competitiveAdvantages, JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode($targetIndustries, JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode($faqData, JSON_UNESCAPED_UNICODE),
            'video_url' => null,
            'manual_pdf_url' => null,
            'is_active' => 1,
            'is_featured' => 1,
            'is_bestseller' => 0,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),
            'warranty_info' => json_encode($warrantyInfo, JSON_UNESCAPED_UNICODE),
            'tags' => json_encode([
                'transpalet',
                'li-ion',
                '48v',
                '2-ton',
                'kompakt',
                'ixtif',
                'f4-201-transpalet',
                '48v-li-ion-transpalet',
                '2-ton-akulu-transpalet',
                'ixtif-transpalet',
                'dar-koridor-transpalet',
                'ikinci-el-transpalet',
                'kiralik-transpalet',
                'yedek-parca',
                'teknik-servis'
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('🎉 F4 201 Li-Ion Transpalet başarıyla eklendi!');
    }
}
