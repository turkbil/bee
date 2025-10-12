<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES18_40WA_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'ES18-40WA')->first();
        if (!$p) {$this->command->error('❌ Master ürün bulunamadı: ES18-40WA'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => "<section><h2>ES18-40WA ile Güvenli İstifin Akıllı Yolu</h2>\n<p>Depo ritmi gün boyunca hiç durmaz; dar koridorlar, farklı ölçülerde paletler ve zamanla yarışan ekipler. ES18-40WA tam bu gerçekliğe göre tasarlandı. Geniş bacaklı straddle şasi, değişken palet tabanlarına uyum sağlarken, kaplumbağa hızı modu operatöre raf ağzında hassas kontrol verir. Oransal kaldırma sayesinde milimetre seviyesinde konumlandırma mümkün olur, elektromanyetik fren sistemi de rampalarda güvenli iniş garantiler. 24V/280Ah akü ve Li‑Ion seçenek, vardiya akışınızı kesmeden esnek bir enerji yönetimi sunar.</p></section>\n<section><h3>Teknik Güç ve Operasyonel Verim</h3>\n<p>ES18-40WA, 600 mm yük merkezinde 1800 kg kapasiteyi AC çekiş sistemi ile birleştirir. 1.1 kW sürüş ve 3.0 kW kaldırma motoru, yüklüde 4.5 km/s, yüksüzde 5.0 km/s hız değerlerine ulaşır; 0.127/0.23 m/s kaldırma ve 0.26/0.20 m/s indirme hızlarıyla dengeli bir hız/denge profili sunar. 1645 mm dönüş yarıçapı ve 2560 mm koridor gereksinimi, dar alanlarda bile kontrollü manevra sağlar. Poliüretan tekerlekler titreşimi azaltıp zemini korur; 990–1500 mm arası ayarlanabilir tiller kolu ise farklı boydaki operatörlere ergonomi sağlar. Çatal ölçüleri 45/100/1070 mm’dir; 920/1150/1220 mm alternatifleri ve 1070/1170/1270 mm teker kolu aralıklarıyla farklı paketleme standartlarına uyarlanabilir. Mast tarafında 3.0 m’den 5.0 m’ye kadar h3 yükseklik seçenekleri mevcuttur; buna bağlı olarak kapalı (h1) ve açık (h4) mast yükseklikleri de değişir.</p>\n<p>Enerji tarafında 24V/280Ah kurşun-asit standart paket kullanım sürekliliği sağlar; 205Ah Li‑Ion seçenek ise fırsat şarjı, daha hızlı toparlanma ve düşük bakım sayesinde toplam sahip olma maliyetini düşürür. Harici 24V-30A/50A şarj çözümleri günlük planlara uyum sağlarken, Li‑Ion için 24V-100A hızlı şarj opsiyonu yoğun vardiyalar için yüksek çevrim verimi sunar. Elektromanyetik servis freni, eğimde park ve ani duruşlarda güvenliği artırır.</p></section>\n<section><h3>Sonuç ve İletişim</h3>\n<p>Sonuç olarak ES18-40WA, farklı palet ölçülerini aynı sahada yöneten işletmeler için güvenli, uyarlanabilir ve ekonomik bir istif çözümüdür. Operasyon planınıza uygun mast ve çatal kombinasyonlarıyla esneklik kazanır; AC sürüş ve oransal hidrolik ise üretkenliği yukarı taşır. Detaylı teknik bilgi ve keşif için bizi arayın: 0216 755 3 555.</p></section>"], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "1800 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V / 280Ah (Li‑Ion opsiyon)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "4.5 / 5.0 km/s (yüklü/boş)"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "1645 mm dönüş yarıçapı"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "Li‑Ion Esnekliği",
                        "description": "205Ah Li‑Ion seçenekle hızlı şarj ve ara molalarda takviye."
                    },
                    {
                        "icon": "weight-scale",
                        "title": "1.8 Ton Güç",
                        "description": "600 mm yük merkezinde 1800 kg güvenli kaldırma."
                    },
                    {
                        "icon": "compress",
                        "title": "Oransal Hidrolik",
                        "description": "Hassas kaldırma/indirme ile raf arası kontrol."
                    },
                    {
                        "icon": "circle-notch",
                        "title": "PU Teker Sistem",
                        "description": "Sessiz çalışma ve düşük zemine aşınma."
                    },
                    {
                        "icon": "hand",
                        "title": "Ergonomik Tiller",
                        "description": "990–1500 mm aralığında konforlu kullanım."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Elektromanyetik Fren",
                        "description": "İnişlerde kontrollü ve güvenli duruş."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "EUR/ISO paletlerde raf içi istifleme ve transfer"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL depolarda dar koridor operasyonları"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezlerinde günlük replenishment"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkışlarında hassas manevra (opsiyonel donanım ile)"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç/kozmetik depolarında kırılgan yüklerin taşınması"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça lojistiğinde hat besleme"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve hazır giyimde koli istifleme"
                    },
                    {
                        "icon": "industry",
                        "text": "Yarı mamul (WIP) akışlarında hücre içi taşıma"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "AC sürüş sistemi ile yüksek verim ve düşük bakım"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Straddle gövde ile farklı palet tabanlarına uyum"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Li‑Ion ile hızlı şarj ve fırsat şarjı imkânı"
                    },
                    {
                        "icon": "star",
                        "text": "Oransal hidrolik sayesinde istifte milimetrik kontrol"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik fren; eğimde güvenli park"
                    },
                    {
                        "icon": "layer-group",
                        "text": "Geniş mast ve çatal opsiyon yelpazesi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret ve Fulfillment"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL ve Depolama Hizmetleri"
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
                        "text": "Kimyasal Depolama ve Dağıtım"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve Kişisel Bakım"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve Yarı İletken"
                    },
                    {
                        "icon": "tv",
                        "text": "Dayanıklı Tüketim/Beyaz Eşya"
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
                        "text": "Yapı Market (DIY)"
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
                        "text": "Tarım ve Bahçe Ürünleri"
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
                        "text": "Kurumsal Depolama ve Arşiv"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(<<<'JSON'
                {
                    "coverage": "Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li‑Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.",
                    "duration_months": 12,
                    "battery_warranty_months": 24
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "Harici Şarj Cihazı 24V-30A",
                        "description": "Standart bakım kolaylığı sağlayan harici şarj cihazı ile dengeli şarj döngüsü.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "charging-station",
                        "name": "Hızlı Şarj Ünitesi 24V-50A",
                        "description": "Yoğun vardiyalarda daha kısa şarj süresi sağlayan harici ünite.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "battery-full",
                        "name": "Li‑Ion Batarya Paketi 205Ah",
                        "description": "Bakım gerektirmeyen hücre yapısı ve ara molalarda fırsat şarjı.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "circle-notch",
                        "name": "Poliüretan Tandem Yük Tekerlekleri",
                        "description": "Düşük gürültü ve daha az zemin aşınması için tandem konfigürasyon.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "certificate",
                        "name": "CE",
                        "year": "2024",
                        "authority": "EU"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "question": "1800 kg kapasite hangi yük merkezinde ve güvenlik payı nedir?",
                        "answer": "Kapasite 600 mm yük merkezinde 1800 kg olarak tanımlanır. Ağırlık merkezi kaydığında değerler düşer; üretici eğim ve yük profiline göre sınırlama önerir."
                    },
                    {
                        "question": "Dar koridorlarda kaplumbağa hızı tam olarak nasıl çalışıyor?",
                        "answer": "Operatör bir düğme ile hız eğrisini kısıtlar. Maksimum hız düşerken tork yönetimi korunur; raf ağzında milimetrik ilerleme sağlanır."
                    },
                    {
                        "question": "Oransal kaldırma valfi hassasiyeti hangi durumda avantaj sağlar?",
                        "answer": "Hassas indirme ve kaldırma, kırılgan yüklerde ve yüksek raflara yerleştirmede isabeti artırır; yük salınımını azaltır."
                    },
                    {
                        "question": "Mast seçenekleri arasında 3.2 m üzeri yükseltiler mevcut mu?",
                        "answer": "Evet, 3.0 m’den 5.0 m’ye kadar farklı h3 yükseklik opsiyonları bulunur; açık mast yüksekliği h4 değeri seçime göre değişir."
                    },
                    {
                        "question": "Dönüş yarıçapı 1645 mm operasyon planlamasını nasıl etkiler?",
                        "answer": "1645 mm dönüş yarıçapı, 2.56 m koridor genişliği ile uyumludur; raf arası planlamada palet boyutlarına göre manevra alanı hesaplanmalıdır."
                    },
                    {
                        "question": "Akü kapasitesi 280Ah ile kaç vardiya çalışılabilir?",
                        "answer": "Kullanım profilinize bağlıdır. Orta yoğunluklu tek vardiyada konforlu süre sağlar; Li‑Ion ile fırsat şarjı yapılarak süre uzatılabilir."
                    },
                    {
                        "question": "Eğim performansı 6/10% değerleri pratikte ne anlama gelir?",
                        "answer": "Yüklüde %6, yüksüzde %10 eğim tırmanabilir. Rampalarda yüklü inişlerde elektromanyetik fren destek sunar."
                    },
                    {
                        "question": "PU tekerlekler zeminde iz bırakır mı ve titreşim seviyesi nasıldır?",
                        "answer": "Poliüretan tekerlekler düşük iz ve gürültüyle çalışır. Zemin kaplamasına bağlı olarak titreşim düşüktür."
                    },
                    {
                        "question": "Bakım aralıkları ve AC sürüş sisteminin katkısı nedir?",
                        "answer": "Fırçasız AC motor yapısı bakım ihtiyacını azaltır. Periyodik kontroller hidrolik ve fren sistemi odaklıdır."
                    },
                    {
                        "question": "Soğuk oda gibi düşük sıcaklıklarda çalışma önerileri nelerdir?",
                        "answer": "Uygun yağ ve conta seçimiyle düşük sıcaklık seti önerilir. Li‑Ion paketlerde termal yönetim avantaj sağlar."
                    },
                    {
                        "question": "Standart çatal ölçüsü ve alternatif uzunluklar nelerdir?",
                        "answer": "Standart çatal 45/100/1070 mm’dir. Uygulamaya göre 920, 1150 veya 1220 mm seçenekleri tercih edilebilir."
                    },
                    {
                        "question": "Garanti kapsamı ve iletişim için yetkili kanalınız nedir?",
                        "answer": "Makine 12 ay, batarya 24 ay garanti kapsamındadır. Satış ve servis için İXTİF 0216 755 3 555 hattından ulaşabilirsiniz."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: ES18-40WA");
    }
}
