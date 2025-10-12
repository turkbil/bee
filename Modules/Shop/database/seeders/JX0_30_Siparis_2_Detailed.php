<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JX0_30_Siparis_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'JX0-30')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: JX0-30'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>Dar Koridorlarda Hızlı ve Güvenli Toplama</h2><p>Gün boyu süren toplama operasyonlarında merdiven ve platformların yerini alan JX0 ailesi, sezgisel sürüş sistemi, mini direksiyon ve hız/yön kumandaları ile operatöre zahmetsiz bir deneyim sağlar. 1260&nbsp;mm dönüş yarıçapı ve kompakt şasi sayesinde kalabalık raf aralarında bile seri manevra yapılır. Yük tepsisi elektrik tahrikli olduğundan, operatörün üst seviye raflardan ürünü alıp tepsiye güvenle yerleştirmesi kolaylaşır. 3615&nbsp;mm kaldırma ve 4090&nbsp;mm maksimum direk yüksekliği ile 4,5&nbsp;metreye kadar etkin toplama yüksekliği sunar.</p></section><section><h3>Teknik Mimari ve Performans</h3><p>Elektrik tahrik sistemi; S2 60&nbsp;dk’da 0,65&nbsp;kW sürüş motoru ve S3&nbsp;%15 döngüde 2,2&nbsp;kW kaldırma motoru ile dengeli hızlanma ve kararlı kaldırma sağlar. 6/6,5&nbsp;km/saat seyir hızları, yoğun vardiyalar için ideal dengeyi kurar. Kaldırma (0,22/0,27&nbsp;m/s) ve indirme (0,31/0,25&nbsp;m/s) değerleri, operatörün yükseklikte geçirdiği süreyi kısaltırken yükün güvenli şekilde konumlandırılmasına imkân verir. Poliüretan/katı lastik kombinasyonu, φ210×70 ön ve φ250×100 arka teker ölçüleri ile titreşimi azaltır; φ74×48 destek tekerleri stabiliteyi artırır. 1095&nbsp;mm tekerlek tabanı ve 1440×750&nbsp;mm kompakt ölçüler, dar alanlarda riskleri minimize eder. Batarya yapılandırması 24V/135Ah Li-Ion olup, fırsat şarjını destekler. Sensörler, operatör duruş pozisyonunu ve kapı kilit durumunu izleyerek yanlış kullanımları engeller; mavi uyarı ışığı ve buzzer çevre farkındalığını artırır.</p></section><section><h3>Sonuç ve İletişim</h3><p>Operasyonunuzun hızını artırmak, iş güvenliğini yükseltmek ve bakım maliyetlerini azaltmak için JX0 ailesi ideal bir yardımcıdır. Günlük toplama akışınıza entegre edildiğinde taşıma adımlarını azaltır, zaman kazandırır ve verimliliği ölçülebilir şekilde yükseltir. Detaylı yapılandırma, demo ve fiyatlandırma için hemen arayın: 0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "200 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V/135Ah Li-Ion"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "6 / 6.5 km/saat"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "1260 mm yarıçap"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "Fırsat şarjı ve uzun ömür",
                        "description": "Bakım gerektirmeyen enerji yapısı, vardiya içi hızlı takviye imkânı sunar."
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt ve çevik şasi",
                        "description": "Narrow-aisle raflarda 1260 mm dönüş yarıçapı ile manevra kolaylığı."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Sensörlü güvenlik",
                        "description": "Operatör varlığı ve kapı kilidi sensörleri hatalı kullanımı engeller."
                    },
                    {
                        "icon": "box-open",
                        "title": "Elektrikli yük tepsisi",
                        "description": "Üst raflardan ürünü güvenle yerleştirmek için güçlü tepsi tahriki."
                    },
                    {
                        "icon": "hand",
                        "title": "Sezgisel kumandalar",
                        "description": "Mini direksiyon ve düğme yerleşimi doğal bir kullanım hissi verir."
                    },
                    {
                        "icon": "star",
                        "title": "Görünürlük artırıcı donanım",
                        "description": "Blue spot ve buzzer ile çevreyi uyarır, iş güvenliğini destekler."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "Online sipariş toplama hatlarında hızlı ürün toplama"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende geri dolum ve raf arası ikmal operasyonları"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL merkezlerinde toplama ve bölge besleme görevleri"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkışlarında kısa mesafe iş istasyonu geçişleri"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve kozmetik raflarında hassas ürün toplama"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça raflarında hızlı SKU erişimi"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal sarf malzeme raflarında güvenli toplama"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücresi içi yarı mamul (WIP) taşıma ve toplama"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "Senkron sürüş ve kaldırma ile zaman kazandıran iş akışı"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V enerji mimarisi ve entegre şarj ile yüksek vardiya süresi"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt ölçüler ve 1260 mm dönüş yarıçapı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Operatör sensörleri ve otomatik kapı kilidiyle yüksek güvenlik"
                    },
                    {
                        "icon": "layer-group",
                        "text": "Standart ve CE yapılandırmalarıyla esnek platform"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret ve fulfillment merkezleri"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL lojistik ve depolama hizmetleri"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım depoları"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Hızlı tüketim ürünleri (FMCG)"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk zincir ve gıda depoları"
                    },
                    {
                        "icon": "wine-bottle",
                        "text": "İçecek ve alkolsüz içecek lojistiği"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve medikal depolama"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal hammadde ve yarı mamul"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve kişisel bakım ürünleri"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve komponent dağıtımı"
                    },
                    {
                        "icon": "tv",
                        "text": "Beyaz eşya ve dayanıklı tüketim"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça ve servis ağları"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil, hazır giyim ve aksesuar"
                    },
                    {
                        "icon": "shoe-prints",
                        "text": "Ayakkabı ve deri ürünleri"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya ve ev dekorasyon depoları"
                    },
                    {
                        "icon": "hammer",
                        "text": "Yapı market ve DIY perakendesi"
                    },
                    {
                        "icon": "print",
                        "text": "Matbaa, ambalaj ve etiketleme"
                    },
                    {
                        "icon": "book",
                        "text": "Yayıncılık ve kırtasiye lojistiği"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım ve bahçe ürünleri tedariki"
                    },
                    {
                        "icon": "paw",
                        "text": "Pet shop ve hayvancılık ürünleri"
                    },
                    {
                        "icon": "industry",
                        "text": "Endüstriyel üretim ve WIP taşıma"
                    },
                    {
                        "icon": "building",
                        "text": "Kurumsal arşiv ve dokümantasyon"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "24V-30A Entegre Şarj Cihazı",
                        "description": "Makine üzerinde, vardiya aralarında hızlı takviye şarj imkânı sağlar.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Kurşun Asit Akü Paketi 24V/120Ah",
                        "description": "Bütçe odaklı kullanım için alternatif enerji paketi.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "grip-lines-vertical",
                        "name": "Poliüretan Teker Seti",
                        "description": "Düşük gürültü ve düşük yuvarlanma direnci için uygundur.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "star",
                        "name": "Blue Spot & Buzzer Yedek Kiti",
                        "description": "Uyarı görünürlüğünü artıran yedek parça seti.",
                        "is_standard": false,
                        "is_optional": true,
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
                        "question": "Dar koridorlarda minimum dönüş açıklığı nedir ve raf aralarında nasıl avantaj sağlar?",
                        "answer": "1260 mm dönüş yarıçapı, 1440×750 mm gövde ile birleşerek dar raf aralarında seri manevra ve pozisyonlamayı mümkün kılar."
                    },
                    {
                        "question": "Toplama yüksekliği ve gerçek erişim seviyesi operasyon için yeterli midir?",
                        "answer": "3615 mm kaldırma ve 4090 mm direk yüksekliği, ~4,5 m picking yüksekliği ile küçük-orta ölçekli raf sistemlerini kapsar."
                    },
                    {
                        "question": "Sürüş motoru ve kaldırma motoru süreklilik değerleri nelerdir?",
                        "answer": "Sürüş motoru S2 60 dk için 0,65 kW, kaldırma motoru S3 %15 çevrim için 2,2 kW kapasiteye sahiptir."
                    },
                    {
                        "question": "Hızlar yüke göre nasıl değişir ve güvenlik açısından ne olur?",
                        "answer": "Seyir hızı 6/6,5 km/saat; kaldırma yüksekliği arttığında hız otomatik düşürülerek denge ve güvenlik korunur."
                    },
                    {
                        "question": "Lastik ve tekerlek kombinasyonu zeminde nasıl performans verir?",
                        "answer": "Poliüretan/katı yapı, φ210×70 ön ve φ250×100 arka tekerler ile titreşimi azaltır; destek tekerleri stabilite katar."
                    },
                    {
                        "question": "Batarya türleri ve kapasite seçenekleri nelerdir?",
                        "answer": "24V/135Ah Li-Ion bakım gerektirmeyen çözüm sunar; 24V/120Ah kurşun asit paketi bütçe dostu alternatiftir."
                    },
                    {
                        "question": "Eğim kabiliyeti operasyon sahasında yeterli midir?",
                        "answer": "Maksimum eğim kabiliyeti yüklü/boş %5/%8’dir; iç mekân düz zeminler için optimize edilmiştir."
                    },
                    {
                        "question": "Güvenlik sensörleri ve kapı kilidi hangi durumlarda devreye girer?",
                        "answer": "Operatör varlığı ve kapı konumunu izleyerek uygunsuz sürüşe izin vermez; kaldırma konumuna göre kilitleme/serbest bırakma yapılır."
                    },
                    {
                        "question": "Bakım periyotları ve sarf malzeme değişimleri nasıl planlanır?",
                        "answer": "Li-Ion yapı bakım ihtiyacını azaltır; periyodik kontrollerde tekerlek ve fren sistemleri gözden geçirilir."
                    },
                    {
                        "question": "Entegre şarj cihazı ile tipik şarj stratejisi nasıl olmalı?",
                        "answer": "Vardiya aralarında kısa fırsat şarjlarıyla kapasite tazelenir; bu sayede gün içinde yüksek kullanılabilirlik korunur."
                    },
                    {
                        "question": "CE uygunluğu ve standart güvenlik donanımı neleri içerir?",
                        "answer": "CE uygunluğu, acil stop, buzzer, mavi uyarı ışığı ve sensörlü kapı kilidi gibi temel koruma fonksiyonlarını kapsar."
                    },
                    {
                        "question": "Garanti kapsamı nedir ve servis desteğine nasıl ulaşırım?",
                        "answer": "Makine 12 ay, Li-Ion batarya 24 ay garanti kapsamındadır. Satış ve servis için İXTİF 0216 755 3 555 ile iletişime geçebilirsiniz."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now()
        ]);
        $this->command->info("✅ Detailed güncellendi: {$p->product_id }");
    }
}
