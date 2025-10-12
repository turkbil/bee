<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JX1_Siparis_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'JX1')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (JX1)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section class=intro">
  <h2>İXTİF JX1: Raf Arası Hız, Operatör İçin Konfor</h2>
  <p>Depoda sabah ilk vardiya başlarken, raf araları henüz boş ve sipariş listeleri uzundur. JX1, kompakt gövdesi ve geniş çalışma platformu ile operatörün yükseğe güvenle erişmesini, ürünleri ön platformda düzenlemesini ve hat beslemeyi kesintisiz sürdürmesini sağlar. 180° eklemli tahrik hattı, dar dönüşlerde raftan rafa akıcı manevra imkânı verir; sezgisel kumandalar ve anti‑yorgunluk mat ise gün boyu konforu standart hâle getirir. İç mekân kullanımına özel tasarlanan yapı, düz ve pürüzsüz zeminlerde risksiz çalışmayı hedefler; hız profili 3.4 mph standart, 5 mph opsiyon ile güvenlikten ödün vermeden tempoyu yükseltir.</p>
</section>
<section class=technical">
  <h3>Teknik Güç ve Mühendislik</h3>
  <p>JX1’in 24V elektrik mimarisi, 1.7 kW sürüş ve 2.2 kW kaldırma motorlarıyla dengeli bir performans sunar. Platform kaldırma hızları 33.5/41.3 fpm, indirme hızları 68.9/51.2 fpm seviyesindedir; rejeneratif elektromanyetik fren sistemi ile yavaşlama anlarında enerji geri kazanımı ve hassas duruş sağlanır. Üç farklı direk yüksekliği ile 126”, 162” veya 192” platform seviyesine erişilir; buna bağlı olarak mast tam yükselmiş yükseklikleri 178.6”, 235” ve 265” değerlerine ulaşır. Dingil aralıkları 45.2” veya 49”, dönüş yarıçapları 52.5” ya da 56” olarak belirlenmiştir. Servis ağırlığı konfigürasyona göre 2617, 2992 veya 3696 lb’dir. Tekerlek yapısı poli tahrik ve döner kasnak ile arka lastik teker kombinasyonunu kullanır; ön 9”x3”, arka 8”x3” ve ek teker 3”x2” ölçülerindedir. Enerji tarafında 24V/224Ah AGM, 24V/340Ah kurşun asit veya 24V/205Ah Li‑ion seçenekleri yer alır; uygun şarj yönetimiyle birden çok vardiyada esnek kullanım hedeflenir. Sistemin kullanım kısıtı gereği yalnızca düz ve düzgün zeminli iç alanlarda operasyon önerilir; azami eğim kabiliyeti %0’dır.</p>
</section>
<section class=closing">
  <h3>Operasyonel Sonuç ve Destek</h3>
  <p>JX1, toplama hızını artırırken operatör yorgunluğunu azaltır; güvenliği önceleyen hız profili ve sıfır dönüş kabiliyeti sayesinde dar alanlarda bile hatasız akış sağlar. Depo büyüklüğü ve sipariş hacmine göre farklı direk yükseklikleri ve akü seçenekleriyle yapılandırılabilir. Doğru konfigürasyon seçimi ve sahaya uyarlama için teknik ekibimizle görüşebilir, test planı ve TCO hesabını birlikte oluşturabilirsiniz. Detaylı bilgi ve teklif için: 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Platform Kapasitesi', 'value' => 'Ön platform 500 lb, arka tepsi 200 lb, operatör bölmesi 300 lb'],
                ['icon' => 'battery-full', 'label' => 'Akü Seçenekleri', 'value' => '24V/224Ah AGM | 24V/340Ah Kurşun Asit | 24V/205Ah Li‑ion'],
                ['icon' => 'gauge', 'label' => 'Sürüş Hızı', 'value' => '3.4 mph standart, 5 mph opsiyon'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '52.5” / 56” (konfigürasyona bağlı)']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'arrows-alt', 'title' => 'Sıfır Dönüş Kabiliyeti', 'description' => '180° eklemli tahrik hattı ile en dar koridorlarda bile çevik manevra.'],
                ['icon' => 'battery-full', 'title' => 'Esnek Enerji Seçenekleri', 'description' => 'AGM, kurşun asit veya Li‑ion ile operasyon planına uygun kapasite.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Yavaşlama', 'description' => 'Rejeneratif elektromanyetik frenleme ile kontrollü duruş.'],
                ['icon' => 'star', 'title' => 'Ergonomik Kontroller', 'description' => 'Sezgisel kumandalar ve anti‑yorgunluk mat ile konfor.'],
                ['icon' => 'industry', 'title' => 'Kompakt + Geniş', 'description' => 'Küçük şasi, geniş platform: alan verimliliği artar.'],
                ['icon' => 'bolt', 'title' => 'Dengeli Performans', 'description' => '1.7 kW sürüş, 2.2 kW kaldırma ile kesintisiz akış.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'Parça toplama ve küçük koli hareketlerinde hız ve erişim kolaylığı'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım merkezinde raf arası ürün yerleştirme'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında sipariş birleştirme ve çapraz sevkiyat'],
                ['icon' => 'snowflake', 'text' => 'Sabit sıcaklıklı iç alanlarda ürün sayımı ve etiketleme'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas sipariş toplama işlemleri'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça raflarında hızlı erişim ve ikmal'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde hat besleme ve üst raf erişimi'],
                ['icon' => 'flask', 'text' => 'Kimya ve laboratuvar depolarında güvenli iç taşıma']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Mühendisliği kanıtlı 24V aktarma ve rejeneratif fren ile enerji verimliliği'],
                ['icon' => 'battery-full', 'text' => 'Birden çok akü teknolojisiyle farklı vardiya senaryolarına uyum'],
                ['icon' => 'arrows-alt', 'text' => 'Sıfır dönüş ve kompakt şasi sayesinde dar koridor hakimiyeti'],
                ['icon' => 'shield-alt', 'text' => 'Operatör güvenliğini önceleyen hız profili ve kontrollü kaldırma'],
                ['icon' => 'star', 'text' => 'Sezgisel kontroller, daha kısa eğitim süresi ve daha az hata'],
                ['icon' => 'briefcase', 'text' => 'Çok işlevli platform: toplama, itme/çekme ve hat besleme bir arada']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincirler'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir İç Alanları'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Ürün Depoları'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'industry', 'text' => 'Endüstriyel Üretim Tesisleri'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'building', 'text' => 'Bölgesel Dağıtım Merkezleri'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Arşiv ve Dosyalama'],
                ['icon' => 'cart-shopping', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'box-open', 'text' => 'Kargo Ayıklama Merkezleri'],
                ['icon' => 'store', 'text' => 'DIY / Yapı Market Backstore'],
                ['icon' => 'box-open', 'text' => 'Kitap-Kırtasiye Depoları'],
                ['icon' => 'industry', 'text' => 'Yedek Parça Distribütörleri'],
                ['icon' => 'warehouse', 'text' => 'Bölmeli Raf Sistemli Depolar'],
                ['icon' => 'cart-shopping', 'text' => 'Promosyon & Mevsimsel Stok Alanları'],
                ['icon' => 'building', 'text' => 'Kurumsal Tedarik Depoları']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine satın alım tarihinden itibaren 12 ay fabrika garantisi kapsamındadır. Li‑Ion batarya modülleri satın alım tarihinden itibaren 24 ay garanti altındadır. Garanti normal kullanımda üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Akıllı Şarj Ünitesi', 'description' => 'Enerji seçeneğine uygun otomatik şarj profilleri ile güvenli ve verimli şarj.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Li‑Ion Akü Paketi 24V/205Ah', 'description' => 'Düşük bakım ihtiyacı ve hızlı şarj kabiliyetiyle çok vardiyaya uygun.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Anti‑Yorgunluk Zemin Matı', 'description' => 'Operatör konforunu artıran kaymaz ve darbe sönümleyici platform kaplaması.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'bolt', 'name' => 'Ön Platform Bariyer Kiti', 'description' => 'Yük güvenliği için ilave bariyer ve bağlama noktaları seti.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'SGS']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'JX1 hangi zemin şartları için uygundur ve dış mekânda kullanılabilir mi?', 'answer' => 'Yalnızca düz ve pürüzsüz iç mekân zeminlerinde çalışacak şekilde tasarlanmıştır; dış mekân ve eğimli alanlarda kullanım önerilmez.'],
                ['question' => 'Operatör platformunun güvenlik kapasitesi ve limitleri nelerdir?', 'answer' => 'Operatör bölmesi 300 lb, ön platform 500 lb ve arka tepsi 200 lb taşıma limitlerine sahiptir; >126” seviyede toplam yük 450 lb veya 400 lb ile sınırlandırılmalıdır.'],
                ['question' => 'Maksimum erişim yüksekliği ve mast tam yükselmiş ölçüleri nedir?', 'answer' => 'Platform yüksekliği 126”, 162” veya 192” seçeneklidir; buna karşılık mast tam yükselmiş yükseklikleri sırasıyla 178.6”, 235” ve 265” değerleridir.'],
                ['question' => 'Sürüş ve kaldırma hızları operasyon verimliliğini nasıl etkiler?', 'answer' => 'Standart 3.4 mph sürüş ve 33.5/41.3 fpm kaldırma hızları, güvenlik limitlerine bağlı kalarak toplama süresini optimize eder; 5 mph opsiyon tempoyu artırır.'],
                ['question' => 'Akü seçenekleri arasında performans ve bakım farkı var mı?', 'answer' => 'AGM ve kurşun asit seçenekleri ekonomik ve yaygındır; Li‑ion düşük bakım, hızlı şarj ve yüksek çevrim ömrü sunar. Doğru seçim vardiya düzenine göre yapılmalıdır.'],
                ['question' => 'Tekerlek ve şasi yapısı dar koridorlarda nasıl avantaj sağlar?', 'answer' => 'Poli tahrik ve kasnak teker kombinasyonu ile kompakt şasi, 52.5” veya 56” dönüş yarıçapı sayesinde dar alanlarda çeviklik sunar.'],
                ['question' => 'Azami eğim kabiliyeti neden %0 olarak verilmiştir?', 'answer' => 'Task support mimarisinde ağırlık merkezi ve platform güvenliği sebebiyle eğimli yüzeylerde operasyon önerilmez; yalnızca düz iç zemin hedeflenir.'],
                ['question' => 'Hız opsiyonu seçildiğinde güvenlik parametreleri nasıl korunur?', 'answer' => '5 mph opsiyonu, kontrol yazılımı ve frenleme ile birlikte gelir; rejeneratif fren ve hız yönetimi operatör güvenliğini korur.'],
                ['question' => 'Servis ağırlığı ve dingil aralığındaki farklar nelere bağlıdır?', 'answer' => 'Direk yüksekliği ve akü paketine göre servis ağırlığı 2617–3696 lb aralığında değişir; dingil aralığı 45.2” veya 49” olabilir.'],
                ['question' => 'Bakım aralıkları ve günlük kontrol listesinde neler olmalı?', 'answer' => 'Akü seviyesi, lastik ve kasnak durumu, fren ve yükseltme fonksiyon kontrolleri günlük yapılmalı; periyodik bakım planı üretim yüküne göre düzenlenmelidir.'],
                ['question' => 'Operatör eğitimi ve devreye alma süreci nasıl ilerler?', 'answer' => 'Sezgisel kumandalar kısa eğitim süreleri sağlar; yine de güvenlik prosedürleri, hız profilleri ve yük limitleri uygulamalı anlatılmalıdır.'],
                ['question' => 'Garanti kapsamı ve servis iletişim adımları nelerdir?', 'answer' => 'Makine 12 ay, akü 24 ay garanti altındadır. İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: JX1');
    }
}
