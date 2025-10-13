<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES12_12ES_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES12-12ES')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ES12-12ES');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section><h2>İXTİF ES12-12ES: Dar Alanlarda Güçlü ve Kontrollü İstif</h2><p>Depo kapıları açıldığında, ilk hareketin akıcı ve güvenli olması tüm günün ritmini belirler. İXTİF ES12-12ES, yaya kumandalı tasarımıyla operatörün doğal yürüyüş tempo­suna uyum sağlar ve 1.2 ton kapasiteyi 600 mm yük merkezinde güvenle yönetir. 3015 mm’ye ulaşan maksimum kaldırma yüksekliği sayesinde orta ve yüksek raf uygulamalarında etkili, 1408 mm dönüş yarıçapı ile de dar koridorlarda pratik çözümler sunar. Ergonomik tiller kolu, dengeli hızlanma karakteristiği ve elektromanyetik fren, ürün akışını güvenle hızlandırır.</p></section>
<section><h3>Teknik Yetkinlik ve Dayanıklılık</h3><p>ES12-12ES, DC sürüş kontrol mimarisiyle 4.0/4.5 km/s hız aralığında stabil ve öngörülebilir bir sürüş sağlar. 0.65 kW S2 sürüş motoru ve 2.2 kW S3 kaldırma motoru, 1200 kg yüke dahi kontrollü kaldırma/indirme hızları (0.12/0.22 m/s ve 0.12/0.11 m/s) sunar. 2×12V/105Ah akü paketiyle beslenen sistem, vardiya boyunca planlı operasyonu destekler; poliüretan tekerlek seti (Ø210×70 ön, Ø80×60 arka, Ø130×55 destek) düşük zemin aşınması ile sessiz ve titreşimsiz bir hareket sağlar. 1740 mm toplam uzunluk, 800 mm genişlik ve 590 mm yük yüzüne kadar uzunluk ölçüleri; 2225 mm (1000×1200) ve 2150 mm (800×1200) koridor gereksinimleri ile raf arası akışlarda optimum dengeyi kurar. 2056 mm kapalı direk yüksekliği, 3521 mm açık direk yüksekliği ve 88 mm alçaltılmış çatal seviyesi, platformlar ve kapılar arasında engelsiz geçişe yardım eder.</p></section>
<section><h3>Sonuç</h3><p>ES12-12ES, güvenli frenleme, öngörülebilir manevra ve bakımı kolay tasarımıyla depo operasyonlarının temposuna uyum sağlar. Proje keşfi, test ve teklif talepleriniz için bizi arayın: 0216 755 3 555.</p></section>
            '], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '1200 kg @ 600 mm'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '2×12V / 105Ah (kurşun-asit)'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.0 / 4.5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '1408 mm dönüş yarıçapı']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '105Ah Çift Akü Paketi', 'description' => 'Gün boyu dengeli enerji ve planlı şarj periyotları.'],
                ['icon' => 'weight-hanging', 'title' => '1.2 Ton Kapasite', 'description' => '600 mm yük merkezinde güvenli kaldırma ve istifleme.'],
                ['icon' => 'arrows-alt', 'title' => 'Dar Koridor Manevrası', 'description' => '1408 mm yarıçap ile sık raf aralarında pratik dönüş.'],
                ['icon' => 'shield-alt', 'title' => 'Elektromanyetik Fren', 'description' => 'Hızlı tepki, kontrollü duruş ve emniyet.'],
                ['icon' => 'warehouse', 'title' => '3015 mm Erişim', 'description' => 'Orta-yüksek raf uygulamalarına uygun direk geometrisi.'],
                ['icon' => 'cog', 'title' => 'Düşük Bakım', 'description' => 'Poliüretan tekerlek ve modüler şasi ile basit servis.']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret sipariş toplama alanlarında koli/palet besleme'],
                ['icon' => 'warehouse', 'text' => '3PL depolarında cross-dock ve raf içi transfer'],
                ['icon' => 'store', 'text' => 'Perakende DC’lerde inbound ve replenishment akışları'],
                ['icon' => 'snowflake', 'text' => 'Gıda depolarında soğuk oda giriş-çıkış tampon alanı'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik lojistiğinde hassas ürün taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça ve CKD hat beslemeleri'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyim SKU konsolidasyonu'],
                ['icon' => 'industry', 'text' => 'Üretim hücreleri arası WIP taşımaları']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '2.2 kW kaldırma motoru ile sınıfında güçlü kaldırma performansı'],
                ['icon' => 'battery-full', 'text' => '105Ah akü kapasitesiyle vardiya dostu çalışma'],
                ['icon' => 'arrows-alt', 'text' => 'Kısa şasi ve dar dönüş yarıçapı ile yüksek manevra'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve mekanik direksiyon güveni'],
                ['icon' => 'warehouse', 'text' => '3015 mm erişimle çoklu raf senaryolarını kapsama'],
                ['icon' => 'cog', 'text' => 'Basit bakım, düşük toplam sahip olma maliyeti']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve İçecek'],
                ['icon' => 'pills', 'text' => 'İlaç ve Kozmetik'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'tshirt', 'text' => 'Tekstil'],
                ['icon' => 'industry', 'text' => 'Genel Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Dağıtım'],
                ['icon' => 'building', 'text' => 'İmalat Tesisleri'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim'],
                ['icon' => 'award', 'text' => 'Ambalaj'],
                ['icon' => 'bolt', 'text' => 'Enerji ve Yedek Parça'],
                ['icon' => 'warehouse', 'text' => 'Soğuk Depo'],
                ['icon' => 'store', 'text' => 'DIY ve Hırdavat'],
                ['icon' => 'flask', 'text' => 'Temizlik Kimyasalları'],
                ['icon' => 'briefcase', 'text' => 'Distribütör Depoları'],
                ['icon' => 'industry', 'text' => 'Metal ve Makine']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion veya kurşun-asit akü modülleri 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim kaynaklı arızaları kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'plug', 'name' => 'Akü Şarj Cihazı (24V)', 'description' => 'Standart akü şarj çözümü, planlı bakım dostu.', 'is_standard' => true, 'price' => null],
                ['icon' => 'cog', 'name' => 'Tandem Arka Makaralar', 'description' => 'Pürüzlü zeminlerde daha stabil iniş/çıkış.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Yüksek Kapasiteli Akü', 'description' => 'Daha uzun vardiyalar için artırılmış Ah seçeneği.', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'warehouse', 'name' => 'Yük Koruma Izgarası', 'description' => 'Palet üstü yüklerde ek güvenlik bariyeri.', 'is_standard' => false, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => 'ES12-12ES dar koridorlarda minimum hangi koridor genişliğinde çalışır?', 'answer' => 'Standart EUR ve ISO paletler için önerilen koridor genişlikleri sırasıyla 2150 mm ve 2225 mm’dir; proje şartlarına göre raf ve palet tipine bakılmalıdır.'],
                ['question' => 'Nominal kapasite hangi yük merkezinde korunur ve hangi limitte azalır?', 'answer' => 'Nominal 1200 kg kapasite 600 mm yük merkezinde geçerlidir. Yük merkezinin artması, özellikle maksimum kaldırma seviyesine yakınken kaldırma diyagramını etkiler.'],
                ['question' => 'Kaldırma ve indirme hızları operasyon süresine nasıl etki eder?', 'answer' => '0.12/0.22 m/s kaldırma ve 0.12/0.11 m/s indirme hızları, hassas yığma ve hızlı beslemeyi dengeleyerek vardiya verimliliğini artırır.'],
                ['question' => 'Hangi zemin koşullarında poliüretan tekerlekler önerilir?', 'answer' => 'Düz, kapalı ve endüstriyel epoksi/Beton zeminlerde düşük yuvarlanma direnci ve sessizlik sağlar; dış mekân ve pürüzlü zeminler için uygun değildir.'],
                ['question' => 'Fren sistemi yokuşta yükle durdurma performansını nasıl etkiler?', 'answer' => 'Elektromanyetik fren, 3% eğimde dahi kontrollü duruş sunar; eğim geçişlerinde hız kontrolü ve önleyici kullanım önerilir.'],
                ['question' => 'Akü bakımı ve şarj rutinini nasıl planlamalıyım?', 'answer' => 'Planlı şarj periyotları, günlük kullanım profiline göre belirlenmeli; uygun şarj cihazı ile aşırı deşarjdan kaçınılmalıdır. Havalandırma ve bakım güvenliği önemlidir.'],
                ['question' => 'Direk yüksekliği ve kapı geçişi arasında nelere dikkat etmeliyim?', 'answer' => 'Kapalı direk yüksekliği 2056 mm’dir. Rampalar ve kapı eşikleri için alçaltılmış çatal yüksekliği 88 mm ve şasi boşluğu 30 mm dikkate alınmalıdır.'],
                ['question' => 'Sürüş kontrolünün DC olması kullanım hissini nasıl etkiler?', 'answer' => 'DC kontrol lineer hızlanma ve öngörülebilir tepki sunar; yeni operatörler için öğrenmesi kolay ve stabildir.'],
                ['question' => 'Standart çatal ölçüleri proje gereksinimime uymuyorsa seçenek var mı?', 'answer' => '1150 mm standarttır. Proje bazlı çatal uzunluğu ve taşıyıcı genişlik opsiyonları sunulabilir; üretim-konfigürasyon teyidi gerekir.'],
                ['question' => 'Günlük bakımda hangi noktalar kontrol edilmeli?', 'answer' => 'Tiller, fren fonksiyonu, zincir-gergi ve tekerlek aşınması, hidrolik kaçak ve akü bağlantıları günlük checklist ile doğrulanmalıdır.'],
                ['question' => 'Garanti koşulları nelerdir ve lokal servis desteği mevcut mu?', 'answer' => 'Makine için 12 ay, akü modülleri için 24 ay garanti sağlanır. Yetkili servis ve yedek parça için İXTİF 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: ES12-12ES');
    }
}
