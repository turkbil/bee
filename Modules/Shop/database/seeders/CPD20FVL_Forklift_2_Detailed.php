<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CPD20FVL_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'CPD20FVL')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (CPD20FVL)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İşletmenizin temposuna ayak uyduran kompakt enerji</h2><p>Sabah vardiyası başlarken kapılar açılır ve ilk paletler rampaya yaslanır. Operasyonun kalbinde, sürücünün oturduğu anda güven veren ergonomik alan ve hassas pedallar ile İXTİF CPD20FVL, dar koridorlardan geniş yükleme alanına kadar aynı akıcılıkta hareket eder. Kompakt gövde ve küçük dönüş yarıçapı sayesinde raf aralarında bekleme ve manevra süreleri kısalır; bu da sipariş çevrim hızına doğrudan yansır. Bakım gerektirmeyen Li‑Ion mimari ve entegre şarj cihazı, enerji planlamasını kolaylaştırır.</p></section><section><h3>Teknik güç ve batarya mimarisi</h3><p>80V/205Ah Li-Ion sistem, 2×5.0 kW AC tahrik ile birlikte yüksek torku sessiz ve verimli şekilde sunar. Tipik seyir hızı 13/14 km/s, kaldırma hızı 0.33/0.45 m/s aralığındadır ve fırsat şarjına uygun kimya sayesinde çoklu vardiya koşullarında kesintisiz akış yakalanır. Güçlendirilmiş mast tasarımı, tam görüş sağlayarak yükün üst seviyelere kaldırılmasında stabilite kazandırır. Opsiyonel non-marking lastikler, iç mekanda iz bırakmadan çalışmayı mümkün kılar; kabin seçenekleri hava koşullarına karşı koruma sunar. Entegre tek faz şarj, ek altyapı yatırımı olmadan kurulumu kolaylaştırır.</p></section><section><h3>Operasyonel sonuç ve desteğe erişim</h3><p>İXTİF mühendisliği, parça erişimi kolay komponent yerleşimi ve akıllı güvenlik donanımlarıyla toplam sahip olma maliyetini düşürmeye odaklanır. Depo çıkışından üretim hattına kadar farklı görevlerde aynı forklift ile çalışmak, filo standardizasyonu ve sürücü eğitiminde hız kazandırır. Bugün daha verimli bir akış için doğru konfigürasyonu seçin; detaylar ve uzman desteği için bizi arayın: 0216 755 3 555.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "2.0 Ton"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "80V/205Ah Li-Ion"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "13/14 km/s"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "1730 mm"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "80V Li-Ion verimlilik",
                        "description": "Fırsat şarjı ile vardiya arasında beklemeden devam."
                    },
                    {
                        "icon": "bolt",
                        "title": "Çift AC tahrik",
                        "description": "2×5.0 kW motorlar ile hızlı tepki ve güçlü tırmanma."
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt geometri",
                        "description": "Dar koridorlarda rahat manevra ve yerden kazanç."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güçlü mast yapısı",
                        "description": "Yük tam kalkıkken bile daha az yalpalama."
                    },
                    {
                        "icon": "briefcase",
                        "title": "Ergonomi",
                        "description": "Ayarlanabilir direksiyon ve konforlu sürücü alanı."
                    },
                    {
                        "icon": "plug",
                        "title": "Entegre şarj",
                        "description": "Tek faz 35A entegre çözümle kolay enerji yönetimi."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "warehouse",
                        "text": "Dar koridorlu depolarda palet iç lojistiği"
                    },
                    {
                        "icon": "box-open",
                        "text": "E-ticaret merkezlerinde çapraz sevkiyat ve WMS besleme"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende DC’lerinde rampa yükleme/boşaltma"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkış alanlarında kısa mesafe taşıma"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve kozmetik depolarında hassas ürün akışı"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça lojistiğinde hat besleme"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücreleri arasında yarı mamul transferi"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya ve temizlik ürünleri depolarında güvenli taşıma"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "İvmelenmede üstün performans ve hassas sürüş kontrolü"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Bakım gerektirmeyen Li-Ion ile daha düşük toplam sahip olma maliyeti"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi sayesinde daha küçük dönüş ve daha az alan ihtiyacı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "OPS ve uyarı aydınlatmaları ile artan güvenlik"
                    },
                    {
                        "icon": "star",
                        "text": "Yüksek verimlilik: 6.85 kWh/h tipik tüketim düzeyi (model bazlı)"
                    },
                    {
                        "icon": "plug",
                        "text": "Entegre şarj cihazı ile ek altyapı gerektirmeden kullanım"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret ve fulfillment"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL ve lojistik hizmetleri"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezleri"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Hızlı tüketim (FMCG)"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Gıda ve soğuk zincir"
                    },
                    {
                        "icon": "wine-bottle",
                        "text": "İçecek depolama ve dağıtım"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve medikal lojistik"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal depolama"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve kişisel bakım"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve teknoloji"
                    },
                    {
                        "icon": "tv",
                        "text": "Beyaz eşya ve dayanıklı tüketim"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve hazır giyim"
                    },
                    {
                        "icon": "shoe-prints",
                        "text": "Ayakkabı ve aksesuar"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya ve ev dekorasyon"
                    },
                    {
                        "icon": "hammer",
                        "text": "Yapı market ve DIY"
                    },
                    {
                        "icon": "print",
                        "text": "Matbaa ve ambalaj"
                    },
                    {
                        "icon": "book",
                        "text": "Yayıncılık ve kırtasiye"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım ve bahçe ürünleri"
                    },
                    {
                        "icon": "paw",
                        "text": "Pet ürünleri ve yem"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "80V/35A entegre şarj cihazı",
                        "description": "Standart tek faz entegre şarj",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "battery-full",
                        "name": "280Ah Li-Ion batarya paketi",
                        "description": "Uzun vardiya için yüksek kapasiteli modül",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "cog",
                        "name": "Non-marking lastik seti",
                        "description": "İz bırakmayan iç mekan lastikleri",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "building",
                        "name": "Yarı kapalı kabin",
                        "description": "Zorlu hava koşulları için kabin çözümü",
                        "is_standard": false,
                        "price": "Talep üzerine"
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
                        "question": "Dar koridorlarda minimum dönüş yarıçapı operasyonu nasıl etkiler?",
                        "answer": "1730 mm dönüş yarıçapı, raf aralarında daha az geri manevra ve daha hızlı konumlanma sağlar; palet döngüsü ve vardiya verimliliği artar."
                    },
                    {
                        "question": "Li-Ion bataryanın fırsat şarjı vardiya planını nasıl değiştirir?",
                        "answer": "Kısa molalarda şarj edilerek enerjinin gün içine yayılmasını sağlar; planlı pil değişimi ihtiyacını ortadan kaldırır ve ekipman kullanılabilirliği yükselir."
                    },
                    {
                        "question": "Çift AC tahrik tekerlekleri çekiş ve ivmeye ne katar?",
                        "answer": "İki bağımsız 5.0 kW motor, kalkışta yüksek tork ve eğimde dengeli çekiş sunar; rampalarda hız kaybı azalır ve hassas sürüş tepkileri elde edilir."
                    },
                    {
                        "question": "Güçlendirilmiş mast yapısının operatör güvenliğine etkisi nedir?",
                        "answer": "Yük altında salınımı azaltarak daha dengeli kaldırma sağlar; görüş açıklığı sayesinde çatal ucu ve yük kenarı daha net takip edilir."
                    },
                    {
                        "question": "Entegre şarj cihazı için ek elektrik altyapısı gerekir mi?",
                        "answer": "Standart tek faz 80V/35A çözüm çoğu tesis bağlantısına uygundur; ek harici şarj istasyonu zorunluluğu ortadan kalkar."
                    },
                    {
                        "question": "Non-marking lastikler hangi durumlarda önerilir?",
                        "answer": "Gıda, ilaç ve showroom gibi zemin temizliği kritik olan alanlarda iz bırakmamak ve daha temiz operasyon için tercih edilir."
                    },
                    {
                        "question": "OPS sistemi ve uyarı aydınlatmaları hangi riskleri azaltır?",
                        "answer": "Operatör koltuk sensörü, bölge ve mavi ışık uyarılarıyla yayaları bilgilendirir; geri sürüş ve kavşak geçiş riskleri azalır."
                    },
                    {
                        "question": "Sürüş hızı ve kaldırma hızı günlük kapasiteyi nasıl etkiler?",
                        "answer": "Seyir 13/14 km/s ve kaldırma 0.33/0.45 m/s değerleri, palet çevrim süresini kısaltıp vardiya başına taşınan palet sayısını artırır."
                    },
                    {
                        "question": "Kabin opsiyonları iklim koşullarına karşı ne sağlar?",
                        "answer": "Yarı kapalı veya tam kapalı kabin seçenekleri, rüzgâr ve yağışlı ortamlarda sürücü konforunu ve devamlılığı artırır."
                    },
                    {
                        "question": "Bakım aralıkları ve Li‑Ion teknolojisi TCO’yu nasıl düşürür?",
                        "answer": "Su ekleme, asit buharı ve düzenli dengeleme şarjı gibi işlemler gerekmediğinden bakım maliyeti ve duruş süresi azalır."
                    },
                    {
                        "question": "Hangi forklift ataşmanlarıyla uyumludur?",
                        "answer": "İhtiyaca göre dahili veya harici yana kaydırıcı ve çatal konumlandırıcı seçenekleri mevcuttur; yük tipine göre konfigüre edilir."
                    },
                    {
                        "question": "Satış sonrası destek ve parça temini için kime ulaşırım?",
                        "answer": "Kurulum, eğitim, orijinal yedek parça ve servis talepleriniz için İXTİF’i arayın: 0216 755 3 555."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed: CPD20FVL");
    }
}
