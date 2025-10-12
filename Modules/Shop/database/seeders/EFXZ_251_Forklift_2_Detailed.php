<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFXZ_251_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFXZ-251')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFXZ-251'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '
<section>
  <h2>İXTİF EFXZ 251: Yeniden Üretilmiş Güç, Elektriğin Verimliliği</h2>
  <p>İXTİF EFXZ 251, içten yanmalı forklift gövdesinin sağlamlığını modern lityum iyon tahrik sistemiyle buluşturan akıllı bir dönüşüm programının ürünüdür. Gövde, karşı ağırlık ve ön aks detaylı söküm, kumlama ve boya işlemlerinden geçirilir; motor, şanzıman ve yakıt sistemi çıkarılarak yerlerine 80V Li-Ion enerji merkezi ve elektrikli aktarma grubu yerleştirilir. Sonuç, 2.5 ton kapasiteli, sessiz, sıfır emisyonlu ve bakım ihtiyacı düşük bir iş makinesidir. EFXZ 251, 11/12 km/s seyir hızıyla akışı bozmadan hat besler, 3000 mm standart kaldırma yüksekliğiyle paletleri güvenle istifler ve yeniden üretim süreci sayesinde ilk yatırım ile günlük işletme maliyetlerinde tasarruf sağlar. Yenilenmiş görünüm, sıkı testlerden geçen güvenlik ve yeni eşdeğeri garanti standartları ile işletmenize hızlı ve sürdürülebilir bir çözüm sunar.</p>
</section>
<section>
  <h3>Teknik Güç ve Mimari</h3>
  <p>EFXZ 251, 2500 kg nominal kapasite ve 500 mm yük merkezi ile sınıfının ana görevlerini rahatlıkla karşılar. 1595 mm dingil mesafesi ve 2316 mm dönüş yarıçapı, dar koridorlarda çeviklik sağlarken 3900 kg servis ağırlığı ve 6°/10° mast eğimi kombinasyonu, yüklü operasyonlarda stabiliteyi artırır. 40×122×1070 mm çatal setiyle uyumlu olan taşıyıcı 2A sınıfındadır ve 1040 mm genişlik sunar. 2090 mm katlı mast yüksekliği, 120 mm serbest kaldırma ve 4025 mm açık mast yüksekliği; rampa yaklaşımı, araç yükleme boşaltma ve raf başlangıç seviyelerinde konforlu bir çalışma aralığı yaratır. Enerji tarafında 80V 150Ah Li-Ion akü, fırsat şarjı ve hızlı geri kazanım ile vardiya planlarını esnekleştirir; PMSM tahrik, 8 kW sürüş ve 16 kW kaldırma motoru ile verimli bir güç aktarımı sağlar. 11/12 km/s seyir, 0.29/0.36 m/s kaldırma ve 0.45/0.50 m/s indirme hızları operasyon temposunu desteklerken, %15/%20 eğim performansı yükleme rampalarında güven verir. Hidrolik hizmet freni ve mekanik park freni, 74 dB(A) sürücü kulağında ses seviyesiyle desteklenir; pnömatik lastikler (7.00-12-12PR ön, 18×7-8-14PR arka) farklı zeminlerde konfor sunar.</p>
  <p>Yeniden üretim süreci, mekaniğin ötesinde kalite güvence adımlarını içerir. Şasi ve karşı ağırlık yenilenir, kritik komponentler kondisyon ve tolerans açısından incelenir, montaj sonrası yük, fren ve dayanım testleri tamamlanmadan seri çıkış yapılmaz. Bu yaklaşım, elektrikliye dönüşümün yalnızca teknik güncelleme olmadığını; sürdürülebilirlik, toplam sahip olma maliyeti ve filo yenileme hızında stratejik avantaj yarattığını kanıtlar.</p>
</section>
<section>
  <h3>Sonuç ve İletişim</h3>
  <p>İXTİF EFXZ 251; sıfır emisyon, düşük bakım, hızlı şarj ve yenilenmiş gövde dayanımıyla, yoğun vardiya koşullarında dahi güven veren bir 2.5 ton forklift çözümüdür. Depo içi akışlarda hız, raf önlerinde hassasiyet ve rampalarda kuvvet isteyen her işletme için rasyonel bir tercih oluşturur. Doğru mast ve çatal varyantlarıyla uyarlanabilir, enerji yönetimiyle kesinti sürelerini azaltır. Detaylı teknik danışmanlık ve yerinde demo talepleriniz için 0216 755 3 555 numaralı hattan bize ulaşabilirsiniz.</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2500 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '80V 150Ah Li-Ion'],
                ['icon' => 'gauge', 'label' => 'Seyir Hızı', 'value' => '11/12 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş Yarıçapı', 'value' => '2316 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => 'Li-Ion Enerji Platformu', 'description' => '80V mimari ile hızlı ve fırsat şarjı; düşük bakım ve yüksek çevrim ömrü.'],
                ['icon' => 'bolt', 'title' => 'Gövde Dayanımı', 'description' => 'IC forklift şasisiyle ağır hizmet koşullarında uzun ömür ve sağlamlık.'],
                ['icon' => 'shield-alt', 'title' => 'Sıkı Test Süreçleri', 'description' => 'Yük, fren, dayanım ve güvenlik kontrolleriyle tutarlı kalite.'],
                ['icon' => 'star', 'title' => 'Yenilenmiş Görünüm', 'description' => 'Kumlama ve profesyonel boya ile estetik açıdan yeniye yakın.'],
                ['icon' => 'arrows-alt', 'title' => 'Çevik Manevra', 'description' => 'Kompakt ölçüler ve hidrolik direksiyonla dar alan kabiliyeti.'],
                ['icon' => 'cart-shopping', 'title' => 'Operasyonel Hız', 'description' => '11/12 km/s sürat ve 0.29/0.36 m/s kaldırma hızlarıyla akıcı akış.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'warehouse', 'text' => '3PL depolarda vardiya içi hat besleme ve çıkış konsolidasyonu'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde araç yükleme/boşaltma ve transfer'],
                ['icon' => 'box-open', 'text' => 'E-ticaret merkezlerinde yoğun palet sirkülasyonu'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas ürün istifi ve rampa yaklaşımı'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça hatlarında WIP taşıma'],
                ['icon' => 'industry', 'text' => 'Ağır sanayide üretim besleme ve mamul taşıma'],
                ['icon' => 'flask', 'text' => 'Kimya tesislerinde güvenli palet transferi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'Elektriğe dönüşümle yakıt ve bakım maliyetlerinde ciddi azalma'],
                ['icon' => 'battery-full', 'text' => 'Fırsat şarjıyla vardiya ortasında kesintisiz operasyon'],
                ['icon' => 'arrows-alt', 'text' => 'Dar koridorlarda çevik dönüş ve kontrollü hızlanma'],
                ['icon' => 'shield-alt', 'text' => 'Yük, fren ve güvenlik testlerinden geçen kalite güvencesi'],
                ['icon' => 'star', 'text' => 'Yenilenmiş gövdeyle estetik ve dayanıklılık bir arada']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim Ürünleri (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir Depoları'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği ve Dağıtımı'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama ve Üretim'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Yarı İletken'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Elektroniği'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar Lojistiği'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyonu'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'industry', 'text' => 'Genel Endüstriyel Üretim'],
                ['icon' => 'building', 'text' => 'Toptan Ticaret Depoları'],
                ['icon' => 'briefcase', 'text' => 'Proje ve Saha Lojistiği']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay, Li-Ion batarya modülleri 24 ay garanti kapsamında değerlendirilir. Garanti, normal kullanım koşullarında üretimden kaynaklanan kusurları kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '80V Akıllı Şarj Ünitesi', 'description' => 'Li-Ion akü için yüksek verimli şarj cihazı', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Geniş Çatal Seti', 'description' => 'Uygulamaya göre farklı çatal kesit/uzunluk seçenekleri', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'wrench', 'name' => 'Telematik Modül', 'description' => 'Kullanım verisi ve uzaktan takip için donanım', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'screwdriver', 'name' => 'Ek Koruyucu Ekipman', 'description' => 'Üst koruma, ışık ve ikaz paketleri', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'Yeniden üretim sürecinde hangi ana parçalar değiştirilir ve hangileri korunur?', 'answer' => 'İçten yanmalı motor, şanzıman ve yakıt sistemi sökülür; şasi, karşı ağırlık ve ön aks yenileme işleminden geçirilerek korunur. Enerji sistemi Li-Ion olarak güncellenir.'],
                ['question' => 'Li-Ion bataryanın günlük işletme maliyetlerine katkısı nasıl olur?', 'answer' => 'Fırsat şarjı ve yüksek verim sayesinde yakıt kalemi ortadan kalkar, planlı bakım azalır; toplam sahip olma maliyeti düşer ve makine daha fazla süre çalışır.'],
                ['question' => 'EFXZ 251 hangi tip lastikleri kullanır ve zemin uyumu nasıldır?', 'answer' => 'Ön 7.00-12-12PR, arka 18×7-8-14PR pnömatik lastikler farklı zeminlerde sarsıntıyı azaltır, kavrama ve konfor sağlar.'],
                ['question' => 'Standart mast ve çatal ölçüleri hangi uygulamalara uygundur?', 'answer' => '3000 mm kaldırma ve 40×122×1070 mm çatal, depo içi raf başlangıç seviyeleri ve araç üstü operasyonlar için dengeli bir çözümdür.'],
                ['question' => 'Maksimum eğim ve seyir hızları operasyon güvenliğini nasıl etkiler?', 'answer' => '11/12 km/s hız limitleri ve %15/%20 eğim kabiliyeti, yüklü-boş güvenli çalışma sınırlarını belirler ve operatör kontrolünü artırır.'],
                ['question' => 'Gürültü seviyesi operatör konforu açısından hangi değerdedir?', 'answer' => 'Sürücü kulağında 74 dB(A) seviyesinde ölçülür; bu değer kapalı alanlarda daha az yorgunluk ve daha iyi iletişim sağlar.'],
                ['question' => 'Fren sistem yapısı nasıldır ve bakım gereksinimi nedir?', 'answer' => 'Hidrolik hizmet freni ve mekanik park freni birlikte çalışır; elektrikli mimariyle fren aksamı bakım aralıkları uzar.'],
                ['question' => 'Dönüş yarıçapı ve koridor genişliği planlamasında hangi değerler dikkate alınmalı?', 'answer' => 'Dönüş yarıçapı 2316 mm, 1000×1200 palet için Ast 4011 mm; saha çizimlerinde bu referanslar kullanılmalıdır.'],
                ['question' => 'Enerji sistemi değişimi filo sürdürülebilirliğine nasıl katkı sağlar?', 'answer' => 'Sıfır emisyon, düşük enerji tüketimi ve daha düşük bakım atığı ile çevresel etki azalır; işletmenin sürdürülebilirlik hedefleri desteklenir.'],
                ['question' => 'Kaldırma ve indirme hızları üretim hatlarıyla senkron nasıl yönetilir?', 'answer' => '0.29/0.36 m/s kaldırma ve 0.45/0.50 m/s indirme profilleri, hat besleme aralıklarına uyumlu olacak şekilde kontrol edilir.'],
                ['question' => 'Teslimat ve devreye alma sürecinde hangi doğrulamalar yapılır?', 'answer' => 'Montaj sonrası yük, fren ve güvenlik testleri tamamlanır; saha devreye almada performans ve güvenlik kontrolleri doğrulanır.'],
                ['question' => 'Satış sonrası destek ve yedek parça temini için kiminle iletişime geçerim?', 'answer' => 'İXTİF satış sonrası ekibiyle 0216 755 3 555 numarasından iletişime geçebilir, teknik destek ve yedek parça taleplerinizi iletebilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EFXZ-251');
    }
}
