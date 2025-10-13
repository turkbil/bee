<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EPT20_18EA_Transpalet_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EPT20-18EA')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section><h2>İXTİF EPT20-18EA: Dar Koridorların Pratik Gücü</h2>
<p>Depo kapıları açıldığında öncelik hız ve güvenliktir. İXTİF EPT20-18EA, 1.800 kg kapasiteyi kompakt bir gövdede birleştirerek vardiya başından sonuna kadar akıcı malzeme akışı sağlar. 85 mm düşük çatal yüksekliği rampalarda ve yükleme alanlarında avantaj yaratırken, 1457 mm dönüş yarıçapı dar koridorlarda rahat manevra imkânı sunar. AC sürüş kontrolü ve elektromanyetik frenin uyumu, operatörün yorgunluğunu azaltır; Polyurethane tekerlekler titreşimi düşürerek sessiz ve kontrollü ilerleme sağlar. E-ticaret, perakende ve 3PL operasyonlarının “hızlı çevirim” ihtiyacı için tasarlanmış bu model, bakım kolaylığı ve güven veren performansıyla günlük operasyonun doğal bir uzantısı hâline gelir.</p></section>
<section><h3>Teknik Güç ve Verimlilik</h3>
<p>Elektrikli yaya kumandalı yapısıyla EPT20-18EA, 24V (2x12V) 85Ah akü düzeni üzerinden 1.1 kW AC sürüş motoru ve 0.84 kW kaldırma motorunu besler. PDF verilerine göre yüklü/yüksüz 4.5/5 km/s seviyesinde seyir hızı, 0.051/0.060 m/s kaldırma ve 0.032/0.039 m/s indirme hızları sağlar. 105 mm kaldırma yüksekliği, 85 mm çatal altı yüksekliği ve 50×150×1150 mm çatal ölçüleriyle standart EUR paletleri problemsiz taşır. 540/685 mm ayarlanabilir çatallar, farklı palet standardlarına uyum verir. 1270 mm dingil mesafesi ve 1625 mm toplam uzunluk; 645 mm toplam genişlik ile birleştiğinde, 2120–2258 mm koridor genişliği gereksinimleri içinde ergonomik yönlendirme sunar. 6%/16% eğim kabiliyeti, yükleme alanı geçişlerinde yeterli çekiş ve kontrol sağlar. Mekanik direksiyon yapısı operatörün makineyi hissederek yönlendirmesine olanak verirken, elektromanyetik servis fren sistemi ani duruşlarda güvenliği artırır.</p>
<p>Poliüretan (PU) tahrik ve yük tekerleri, zemine hassas yaklaşım ve düşük işletme gürültüsü getirir. 285 kg servis ağırlığı, özellikle rampa üstü transferlerde ve asansör içi hareketlerde önemli bir avantajdır. Akü göstergesi zaman fonksiyonuna sahiptir; harici 24V-15A/20A şarj seçenekleriyle farklı vardiya yoğunlukları için esnek çözüm sunar. Denge tekeri ve çoklu iz genişliği seçenekleri, zorlu manevra anlarında stabiliteyi destekler.</p></section>
<section><h3>Sonuç</h3><p>EPT20-18EA, pratikliğin ve verimliliğin kesiştiği noktada konumlanır: kompakt şasi, güvenli frenleme, AC tahrik ve sessiz PU tekerler bir araya gelerek yüksek devinimli depolarda sürdürülebilir performans üretir. Proje bazlı ihtiyaçlarınıza göre çatal uzunluğu ve genişliği varyantlarıyla tam uyum sağlayabilir. Teklif ve demo için 0216 755 3 555.</p></section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1800 kg'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '24V (2x12V) 85Ah'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5/5 km/s (yüklü/yüksüz)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1457 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '24V 85Ah Enerji', 'description' => 'Harici şarj seçenekleriyle vardiya içi esneklik ve süreklilik.'],
                ['icon' => 'bolt', 'title' => 'AC Sürüş Kontrolü', 'description' => 'Yumuşak kalkış, hassas hız ve daha iyi çekiş yönetimi.'],
                ['icon' => 'arrows-alt', 'title' => 'Kompakt Manevra', 'description' => '1457 mm dönüş yarıçapıyla dar koridor uyumu.'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Frenleme', 'description' => 'Elektromanyetik servis freni ile güven veren duruş.'],
                ['icon' => 'industry', 'title' => 'Hafif Şasi', 'description' => '285 kg ağırlıkla kolay sevk ve katlar arası kullanım.'],
                ['icon' => 'star', 'title' => 'PU Tekerler', 'description' => 'Sessiz, titreşimi azaltan ve zemin dostu teker seti.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret depolarında hızlı inbound–outbound palet akışı'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerinde raf arası replenishment görevleri'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında çapraz sevkiyat ve kısa mesafe transfer'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış ara taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik ürünlerinde hassas ve sessiz taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça akışında rampaya yaklaşma operasyonları'],
                ['icon' => 'industry', 'text' => 'Üretim hatlarında WIP taşıma ve hücre besleme'],
                ['icon' => 'box-open', 'text' => 'Kargo–kurye transfer merkezlerinde hızlı palet hareketi']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => 'AC tahrik ile daha verimli hızlanma ve çekiş kontrolü'],
                ['icon' => 'battery-full', 'text' => 'Esnek şarj seçenekleri ve zaman göstergeli batarya izleme'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve 1457 mm dönüş ile dar alan başarısı'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ile güvenli duruş ve düşük bakım'],
                ['icon' => 'star', 'text' => 'PU tekerler sayesinde sessiz ve titreşimsiz operasyon']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Dağıtım Merkezleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal Depolama'],
                ['icon' => 'flask', 'text' => 'Kimyasal Ürün Depoları'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi ve Üretim'],
                ['icon' => 'building', 'text' => 'İnşaat ve Yapı Market'],
                ['icon' => 'briefcase', 'text' => 'Kurumsal Dağıtım Ağları'],
                ['icon' => 'cart-shopping', 'text' => 'Toptan ve Cash&Carry'],
                ['icon' => 'store', 'text' => 'Ev Tekstili ve Perakende'],
                ['icon' => 'box-open', 'text' => 'Kargo ve Parsiyel Dağıtım'],
                ['icon' => 'warehouse', 'text' => 'Depolama–Antrepo Tesisleri'],
                ['icon' => 'flask', 'text' => 'Boya ve Kimyasal Lojistiği'],
                ['icon' => 'pills', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Beyaz Eşya–Elektronik Dağıtım'],
                ['icon' => 'industry', 'text' => 'Ambalaj ve Matbaa Lojistiği']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makine 12 ay üretim hatalarına karşı fabrikasyon garanti kapsamındadır. Li-Ion veya kurşun-asit batarya modülleri satın alım tarihinden itibaren 24 ay garantiye tabidir. Garanti normal kullanım koşullarında geçerlidir.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => '24V Harici Şarj Cihazı 15A', 'description' => 'Standart şarj çözümü; güvenli, stabil ve hızlı bağlantı.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'plug', 'name' => '24V Harici Şarj Cihazı 20A', 'description' => 'Daha yoğun vardiyalar için yüksek akım opsiyonu.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'cog', 'name' => 'Tandem Yük Teker Seti (PU)', 'description' => 'Zorlu zeminlerde daha stabil yük taşıma için çift teker.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => '120Ah Akü Paketi', 'description' => 'Yoğun kullanım için artırılmış kapasite akü seçeneği.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'EPT20-18EA düşük koridor genişliklerinde nasıl performans gösterir?', 'answer' => '1457 mm dönüş yarıçapı ve 645 mm gövde genişliği ile dar raf aralarında seri manevra yapar; 2120–2258 mm koridor gereksinimlerine uygundur.'],
                ['question' => 'Standart çatal ölçüleri ve palet uyumluluğu nedir?', 'answer' => '50×150×1150 mm çatal ve 540/685 mm ayarlanabilir çatallar EUR ve farklı palet tipleriyle uyumluluk sağlar, rampada yaklaşımı kolaylaştırır.'],
                ['question' => 'Maksimum eğim kabiliyeti hangi senaryolarda yeterlidir?', 'answer' => 'Yüklü %6, yüksüz %16 değerleri yükleme rampaları, seviye farkları ve dar geçişlerde güvenli çekiş sunar; hız kontrolü AC sürüşle desteklenir.'],
                ['question' => 'Enerji sistemi ve vardiya içi çalışma süresi nasıl yönetilir?', 'answer' => '24V 85Ah akü harici 15A/20A şarj ile desteklenebilir; batarya göstergesi zaman fonksiyonludur, aralıklı şarjlarla vardiya sürekliliği sağlanır.'],
                ['question' => 'Fren sistemi güvenlik açısından hangi avantajları sunar?', 'answer' => 'Elektromanyetik servis fren, eğimli zeminlerde bile hızlı ve kontrollü duruş sağlar; bakım ihtiyacı düşüktür.'],
                ['question' => 'Tekerlek malzemesi zeminlerde iz bırakır mı ve gürültü seviyesi nasıldır?', 'answer' => 'PU tekerler zemine dosttur, düşük titreşim ve sessiz çalışma sağlar; 74 dB(A) kulak seviyesi sınırındadır.'],
                ['question' => 'Toplam ağırlığın düşük olması operasyonu nasıl etkiler?', 'answer' => '285 kg servis ağırlığı, katlar arası taşıma, asansör ve rampa üzerinde çevik hareket ve düşük enerji tüketimi sağlar.'],
                ['question' => 'Direksiyon ve kontrol kolu ergonomisi operatör konforunu nasıl etkiler?', 'answer' => 'Mekanik direksiyon ve 715–1200 mm kol yüksekliği aralığı farklı operatörler için doğal ve yorulmayı azaltan kullanım sunar.'],
                ['question' => 'Bakım periyotları ve temel kontrol noktaları nelerdir?', 'answer' => 'Günlük teker ve fren kontrolü, periyodik akü bağlantı ve şarj ekipmanı denetimi önerilir; AC sürüşle mekanik aşınma düşüktür.'],
                ['question' => 'Çevresel koşullarda (soğuk/ıslak zemin) kullanım önerileri nelerdir?', 'answer' => 'PU tekerlerde kayma riskini azaltmak için kuru ve temiz zemin önerilir; soğuk alan girişlerinde hız kademeleri kontrollü seçilmelidir.'],
                ['question' => 'Opsiyonel ekipmanlarla kapasite veya hız değişir mi?', 'answer' => 'Çatal uzunluğu/genişliği ve teker opsiyonları kullanım ergonomisini etkiler; nominal kapasite 1800 kg olup sınırlar dahilinde korunur.'],
                ['question' => 'Garanti ve satış sonrası destek süreçleri nasıl işler?', 'answer' => 'Makine 12 ay, akü 24 ay garanti kapsamındadır. İXTİF satış, servis ve yedek parça desteği için 0216 755 3 555 numarasından ulaşabilirsiniz.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed güncellendi: EPT20-18EA');
    }
}
