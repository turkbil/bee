<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES12_25WA_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES12-25WA')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => "\n<section><h2>ES12-25WA: Dar Koridorlarda Güçlü ve Hassas İstif</h2><p>Sabah vardiyası başlarken rafların arasında ilk hareketi başlatan makine çoğu zaman bir istif makinesidir. İXTİF ES12-25WA tam da bu anda devreye girer: kompakt gövdesi, dengeli şasisi ve iki kademeli indirme sistemiyle her paleti olması gereken yüksekliğe en güvenli biçimde taşır. 1.200 kg kapasitesi ve 3.280 mm’ye ulaşan kaldırma kabiliyeti, onu hem e-ticaret merkezlerinde hem de üretim hücrelerinde vazgeçilmez kılar. Operatör yorgunluğunu azaltan mekanik direksiyon, pürüzsüz hızlanma sunuan AC sürüş kontrolü ve sessiz PU tekerlekler; gün boyu akıcı, öngörülebilir bir kullanım deneyimi yaratır. Depo düzeni sıkışık, koridorlar dar olduğunda dahi 1.490 mm dönüş yarıçapı ve farklı şasi genişliği seçenekleri ile operasyon akışını kesintisiz sürdürür.</p></section>\n<section><h3>Teknik Güç ve Mühendislik</h3><p>Teknik tarafta ES12-25WA; 24V/210Ah enerji paketiyle uzun vardiyalara uygun çalışma sağlar. Standart kurşun-asit yapı, kurumunuzun mevcut şarj altyapısıyla uyumludur; daha ileri verimlilik için Li-ion batarya seçeneği, yüksek akımlı 24V-100A hızlı şarj ve telematik entegrasyonu ile sunulur. 1.1 kW sürüş motoru (S2 60 dk) ve 2.2 kW kaldırma motoru (S3 %15), 5.0/5.5 km/s seyir ve 0.127/0.23 m/s kaldırma hızlarına ulaşır. İki kademeli indirme (0.26/0.20 m/s) özellikle rafların üst katlarında yük yerleştirirken stabilite ve doğruluk getirir; paletin son 10–15 cm’lik hareketi daha kontrollü gerçekleşir. 600 mm yük merkezinde 1.200 kg taşıma, 1947 mm toplam uzunluk ve 877 mm yüze kadar ölçüsü; dar çalışma alanlarında manevrayı kolaylaştırır. 1120–1420 mm arasında değişen şasi genişlikleri, 800 mm taşıyıcı ve 200–760 mm ayarlanabilir çatallar arası ile farklı palet tiplerine uyum sağlar. Elektromanyetik servis freni eğimli zeminlerde güven verir; PU tekerlek malzemesi ise zemine zarar vermeden sessiz ilerleme sunar. 74 dB(A) seviyesindeki gürültü değeri operatör konforunu destekler.</p></section>\n<section><h3>Sonuç ve İletişim</h3><p>Operasyonel açıdan bakıldığında, ES12-25WA mal kabulden sevkiyata kadar tüm istif adımlarını standardize eder. Kısa eğitimle kullanılabilen ergonomik tiller kolu, 715–1200 mm aralığında konforlu bir çalışma yüksekliği sağlar. Bakım tarafında ise mekanik direksiyon ve erişilebilir komponent yerleşimi servis sürelerini kısaltır, toplam sahip olma maliyetini aşağı çeker. İster mevcut hatlarınızı iyileştirmek, ister yeni bir depo devreye almak isteyin; model, modüler seçenekleriyle büyüyen işinize kolayca ayak uydurur. Bugün doğru istif makinesini seçmek, yarının verimliliğini belirler. Teknik değerlendirme ve fiyatlandırma için 0216 755 3 555 numaralı hattan ekibimizle iletişime geçebilirsiniz.</p></section>\n"], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "1200 kg @ 600 mm"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V / 210Ah (Li-ion opsiyon)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Seyir Hızı",
                        "value": "5.0 / 5.5 km/s (yüklü/yüksüz)"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş Yarıçapı",
                        "value": "1490 mm"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "arrows-alt",
                        "title": "İki Kademeli İndirme",
                        "description": "Yük istiflerken dengeyi artırır, palet hizalamada hassasiyet sağlar."
                    },
                    {
                        "icon": "battery-full",
                        "title": "Esnek Enerji",
                        "description": "210Ah kurşun-asit standart, Bluetooth destekli Li-ion seçenek mevcuttur."
                    },
                    {
                        "icon": "circle-notch",
                        "title": "Kompakt Şasi",
                        "description": "1120–1420 mm genişlik seçenekleriyle dar koridorlarda çevik hareket."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenli Fren",
                        "description": "Elektromanyetik frenleme ile eğimde kontrollü duruş ve kalkış."
                    },
                    {
                        "icon": "cog",
                        "title": "Düşük Bakım",
                        "description": "Mekanik direksiyon ve AC sürüş sayesinde uzun servis aralıkları."
                    },
                    {
                        "icon": "bolt",
                        "title": "AC Sürüş",
                        "description": "Pürüzsüz hızlanma ve verimli tırmanma kabiliyeti (8/16%)."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "warehouse",
                        "text": "Dar koridorlu depo raflarında paletli ürünleri katlara kaldırma ve indirme"
                    },
                    {
                        "icon": "box-open",
                        "text": "E-ticaret hatlarında mal kabul ve sevkiyat öncesi tampon alan yönetimi"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezlerinde toplama istasyonlarına besleme"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkışlarında kısa mesafe istif operasyonları"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç/kozmetik depolarında hassas ürünlerin güvenli istiflenmesi"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça depolarında rampa etrafı malzeme akışı"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücreleri arasında WIP taşıma ve tampon stok yönetimi"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal ürünlerin PU tekerleklerle zemin dostu hareketi"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "AC tahrik ile daha verimli hızlanma ve düşük enerji tüketimi"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Li-ion seçeneğinde hızlı şarj ve telematik entegrasyonu"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "İki kademeli indirme sayesinde istifte hassas kontrol"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik fren ile güvenli duruş ve eğim kontrolü"
                    },
                    {
                        "icon": "layer-group",
                        "text": "Çoklu şasi/direk seçenekleriyle esnek konfigürasyon"
                    },
                    {
                        "icon": "star",
                        "text": "PU tekerlekler ile düşük titreşim ve sessiz çalışma"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret ve Fulfillment"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL ve Kontrat Lojistiği"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende Dağıtım Merkezleri"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Hızlı Tüketim (FMCG)"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Gıda ve Soğuk Zincir"
                    },
                    {
                        "icon": "wine-bottle",
                        "text": "İçecek Lojistiği"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve Medikal"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal Depolama"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve Kişisel Bakım"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve Yüksek Teknoloji"
                    },
                    {
                        "icon": "tv",
                        "text": "Dayanıklı Tüketim ve Beyaz Eşya"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv Yedek Parça"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve Hazır Giyim"
                    },
                    {
                        "icon": "shoe-prints",
                        "text": "Ayakkabı ve Aksesuar"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya ve Ev Dekorasyonu"
                    },
                    {
                        "icon": "hammer",
                        "text": "Yapı Market ve DIY"
                    },
                    {
                        "icon": "print",
                        "text": "Matbaa ve Ambalaj"
                    },
                    {
                        "icon": "book",
                        "text": "Yayıncılık ve Kırtasiye"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım, Bahçe ve Seracılık"
                    },
                    {
                        "icon": "paw",
                        "text": "Evcil Hayvan Ürünleri"
                    },
                    {
                        "icon": "building",
                        "text": "Tesis Yönetimi ve Entegre Lojistik"
                    },
                    {
                        "icon": "briefcase",
                        "text": "B2B Toptan Dağıtım"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(
                <<<'JSON'
                {
                    "coverage": "Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.",
                    "duration_months": 12,
                    "battery_warranty_months": 24
                }
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "24V-30A Harici Şarj Cihazı",
                        "description": "Standart kurşun-asit batarya için verimli ve güvenilir şarj çözümü.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "PU Tandem Yük Tekerlek Seti",
                        "description": "Düşük titreşim ve düzgün yuvarlanma için çift teker seti.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "charging-station",
                        "name": "24V-100A Li-ion Hızlı Şarj",
                        "description": "Li-ion bataryalar için hızlı şarj altyapısı ve akıllı kontrol.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "grip-lines-vertical",
                        "name": "Su Otomatik Doldurma Sistemi",
                        "description": "Kurşun-asit akülerde pratik bakım için yarı otomatik dolum aparatı.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "certificate",
                        "name": "CE",
                        "year": "2024",
                        "authority": "EU"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "question": "600 mm yük merkezinde 1.200 kg kapasite hangi palet tiplerinde güvenlidir?",
                        "answer": "EUR ve benzeri standart paletlerde yük ağırlığı merkezdeyken tam kapasite sunar. Dengesiz, yüksek ağırlık merkezli yüklerde güvenlik için kapasite düşümü hesaplanmalıdır."
                    },
                    {
                        "question": "İki kademeli indirme raf üstü hizalamada nasıl bir avantaj sağlar?",
                        "answer": "İlk aşamada hızlı inişle zaman kazanılır; paletin rafa yaklaşmasında ikinci aşama devreye girerek hız düşer ve operatör milimetrik yerleştirme yapabilir."
                    },
                    {
                        "question": "AC sürüş kontrolü enerji verimliliğine nasıl katkı yapar?",
                        "answer": "Daha pürüzsüz hızlanma ve rejeneratif frenleme ile enerji kullanımı optimize edilir, batarya çevrim ömrü desteklenir ve ısı oluşumu azalır."
                    },
                    {
                        "question": "24V/210Ah batarya ile tipik vardiya süresi ne kadar olur?",
                        "answer": "Kullanım yoğunluğuna bağlıdır; tek vardiyada 6–8 saat arası operasyon tipiktir. Kaldırma-durma sıklığı ve rampa kullanımı süreyi etkiler."
                    },
                    {
                        "question": "Li-ion seçenek alındığında şarj altyapısında neler değişir?",
                        "answer": "24V-100A hızlı şarj ve akıllı BMS ile fırsat şarjı mümkün olur. Telematik opsiyonu ile batarya sağlığı uzaktan izlenebilir."
                    },
                    {
                        "question": "Dönüş yarıçapı 1.490 mm olan makine ne kadar dar koridorda çalışabilir?",
                        "answer": "Koridor genişliği, palet boyu ve direk konfigürasyonuna bağlıdır; tipik 2.4 m koridorlar için uygun olup, daha dar alanlarda hız limitleriyle çalışılmalıdır."
                    },
                    {
                        "question": "Elektromanyetik fren eğimde geri kaçmayı nasıl engeller?",
                        "answer": "Sürüş kumandası bırakıldığında fren devreye girer, motoru kilitleyerek kontrolsüz hareketi önler; kalkışta ise kontrollü tork verilir."
                    },
                    {
                        "question": "PU tekerleklerin zemine etkisi ve gürültü seviyesi nasıldır?",
                        "answer": "PU malzeme beton ve epoksi zeminlerde iz bırakmaz, titreşimi sönümler ve 74 dB(A) seviyesinde sessiz çalışma sağlar."
                    },
                    {
                        "question": "Bakım aralıkları ve servis erişilebilirliği nasıl planlanmıştır?",
                        "answer": "Mekanik direksiyon ve modüler komponent yerleşimi hızlı erişim sağlar. Günlük kontroller için yağlama ve görsel kontrol adımları yeterlidir."
                    },
                    {
                        "question": "Direk yükseklik seçenekleri ve serbest kaldırma ne sağlar?",
                        "answer": "150 mm serbest kaldırma ile kapı/kapak altında paleti kaldırabilir; farklı direk yükseklikleri farklı raf seviyelerine erişim sunar."
                    },
                    {
                        "question": "Standart aksesuarlarla birlikte gelen şarj çözümü hangi aküler için uygundur?",
                        "answer": "24V-30A harici şarj cihazı kurşun-asit aküler için uygundur; Li-ion paketlerde 24V-100A hızlı şarj tercih edilir."
                    },
                    {
                        "question": "Garanti kapsamı ve satış sonrası destek kanalları nelerdir?",
                        "answer": "Makine 12 ay, Li-Ion batarya 24 ay garantilidir. İXTİF satış, servis ve yedek parça desteği için 0216 755 3 555 üzerinden bize ulaşabilirsiniz."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: ES12-25WA");
    }
}
