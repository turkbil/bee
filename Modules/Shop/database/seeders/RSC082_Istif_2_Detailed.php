<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RSC082_Istif_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'RSC082')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: RSC082'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => "\n<section><h2>RSC082 ile Dar Alanda Büyük İş: Kompakt, Güçlü, Hassas</h2>\n<p>Depolarda palet akışının hızlandığı, operatör verimliliğinin her geçen gün daha kritik hale geldiği bir dönemde, RSC082 kompakt gövdesi ve karşı ağırlıklı yapısıyla öne çıkar. 800 kg kaldırma kapasitesi ve 400 mm yük merkezi sayesinde farklı ebat ve tipte paletlerin güvenli istiflenmesini kolaylaştırır. 24V/210Ah akü altyapısı ile gün içindeki vardiyalarda istikrarlı performans verir; AC sürüş ve elektronik direksiyon kombinasyonuysa dar koridorlarda titreşimsiz, dengeli ve öngörülebilir kontrol sunar. 3000 mm kaldırma yüksekliği ve oransal kaldırma özelliği, raf giriş-çıkışlarında milimetrik hassasiyeti mümkün kılar.</p></section>\n<section><h3>Teknik Güç ve Şasi Dengesi</h3>\n<p>RSC082’nin çekirdeğinde 1.6 kW AC sürüş motoru ve 2.2 kW kaldırma motoru bulunur. Bu ikili, 5.5/6 km/sa yürüyüş hızlarına ulaşırken yük altında bile kontrollü hızlanma sağlar. 1760 kg servis ağırlığı ve karşı ağırlıklı şasi mimarisi, değişken yüklerde arka tekerlek tutuşunu artırır. Polüretan tekerlekler (230×90 mm ön ve 180×65 mm arka) sessiz çalışır, zeminde iz bırakmaz ve bakım ihtiyacını düşürür. 1250 mm dönüş yarıçapı ve 2404 mm toplam uzunluk, dar koridor planlarında yüksek manevra sağlar. 1334 mm çatala kadar uzunluk ve 900 mm toplam genişlik, hem EUR 1200×800 hem de 1000×1200 paletlerle uyumluluğu destekler. 60 mm mast altı boşluk ve 116 mm dingil merkezi boşluk, eşiksiz geçişlerde pürüzsüz sürüşe yardım eder.</p>\n<p>Direk kapalı yüksekliği 2061 mm ve açık yüksekliği 3908 mm olan standart 3000 mm kaldırma takımı, çok katlı raflara erişimi kolaylaştırır. Oransal kaldırma özelliği, özellikle kararsız yüklerde hassas seviyelendirme sağlar. Elektromanyetik servis/park freni, ani duruşlarda yük güvenliğini destekler. Sürüş kontrolünün AC oluşu, daha az ısı üretimi ve yüksek enerji verimi ile sonuçlanır; bu da akü döngülerinin verimli kullanılmasına yardımcı olur.</p></section>\n<section><h3>Operasyon Verimliliği ve Kullanıcı Deneyimi</h3>\n<p>Elektronik direksiyon tasarımıyla operatör kolundan gelen komutlar gecikmesiz ve öngörülebilir bir şekilde şasiye aktarılır. Bu sayede özellikle kalabalık raf aralarında hassas manevralar yapılabilir. Katlanır platform ve yan korkuluk opsiyonları, uzun mesafe sürüşlerde operatör yorgunluğunu azaltır. Ses basınç seviyesi 74 dB(A) olduğundan, yoğun vardiyalarda dahi konfor korunur. 30 A şarj cihazı akımı ile standart şarj altyapısına uyum gösterir; vardiya planlamasında esneklik yaratır. RSC082; e-ticaret, perakende dağıtım, 3PL, otomotiv yedek parça ve kimya gibi sektörlerde, inbound ve outbound akışlar arasında ara taşıma ve raf beslemede çok yönlü bir yardımcıdır.</p>\n<p>Günlük kullanımda operatör hatalarını azaltan unsurların başında tutarlı frenleme ve tahmin edilebilir hızlanma gelir. Elektromanyetik fren sistemi ve AC sürüş mimarisi birlikte çalışarak, rampa yaklaşımı ve palet hizalama gibi hassas anlarda güvenli duruş sağlar. PU tekerleklerin zemin dostu yapısı, beton zeminlerde düşük titreşim ve düşük gürültü profili ile ekipman ve operatör yorgunluğunu azaltır. Bakım tarafında, AC motorlar fırçasız yapıları sayesinde daha seyrek bakım ihtiyacı duyar.</p></section>\n<section><h3>Sonuç</h3>\n<p>Özetle RSC082, kompakt bir kasada dengeli güç, yüksek manevra ve hassas istiflemeyi birleştirir. 800 kg kapasite, 3000 mm kaldırma, 24V/210Ah akü ve AC sürüş ile depo operasyonlarınızı hızlandırırken güvenlikten ödün vermez. Detaylı bilgi ve stok durumu için 0216 755 3 555 numaralı hattan satış ekibimizle görüşebilirsiniz.</p></section>\n"], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "800 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V / 210Ah"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "5.5/6 km/sa"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "1250 mm"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "24V Li-Ion Seçeneği",
                        "description": "Yüksek enerji yoğunluğu ve hızlı şarj ile kesintisiz vardiya."
                    },
                    {
                        "icon": "bolt",
                        "title": "AC Sürüş Teknolojisi",
                        "description": "1.6 kW motor ile akıcı hızlanma ve verimli sürüş."
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt Dönüş Yarıçapı",
                        "description": "1250 mm dönüş ile dar koridorlarda rahat manevra."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenli Frenleme",
                        "description": "Elektromanyetik fren sistemi ile kontrollü duruş."
                    },
                    {
                        "icon": "warehouse",
                        "title": "3000 mm Kaldırma",
                        "description": "Çok seviyeli raflarda hassas istifleme kabiliyeti."
                    },
                    {
                        "icon": "cog",
                        "title": "Düşük Bakım PU Teker",
                        "description": "Sessiz çalışma ve zemin dostu yapı."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "Koli ve kasaların raf arası taşınması ve istiflenmesi"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezlerinde sipariş toplama destek operasyonları"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL depolarda inbound-outbound akışlarında ara taşıma"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkış noktalarında kısa mesafe aktarma"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve kozmetik ürünlerde hassas ve sarsıntısız istifleme"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça paletlerinin üretim hattına beslenmesi"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücreleri arasında yarı mamul WIP taşıma"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya depolarında farklı boy paletlerin güvenli yönetimi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "AC tahrik ve güçlü kaldırma motoru ile dengeli performans"
                    },
                    {
                        "icon": "battery-full",
                        "text": "210Ah akü ile uzun çalışma süresi ve hızlı şarj uyumluluğu"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ile dar alanlarda üstün manevra"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Elektromanyetik fren, elektronik direksiyon ve güvenli kontrol"
                    },
                    {
                        "icon": "star",
                        "text": "Hassas oransal kaldırma ile raf giriş-çıkışlarında nazik hareket"
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
                        "text": "Perakende dağıtım"
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
                        "text": "İçecek depolama"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve medikal"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal depolama"
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
                        "icon": "print",
                        "text": "Matbaa ve ambalaj"
                    },
                    {
                        "icon": "book",
                        "text": "Yayıncılık ve kırtasiye"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya ve ev dekorasyonu"
                    },
                    {
                        "icon": "hammer",
                        "text": "Yapı market ve DIY"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım ve bahçe ürünleri"
                    },
                    {
                        "icon": "paw",
                        "text": "Evcil hayvan ürünleri"
                    },
                    {
                        "icon": "briefcase",
                        "text": "B2B toptan dağıtım"
                    },
                    {
                        "icon": "building",
                        "text": "Tesis içi malzeme hareketi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(<<<'JSON'
                {
                    "coverage": "Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise 24 ay garanti kapsamındadır. Garanti normal kullanım koşullarında üretim kaynaklı arızaları kapsar.",
                    "duration_months": 12,
                    "battery_warranty_months": 24
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "Dahili Şarj Cihazı",
                        "description": "24V akü için 30A uyumlu şarj ünitesi",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "PU Tahrik ve Yük Tekerleri",
                        "description": "Düşük gürültü ve zemin koruma sağlayan teker seti",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "battery-full",
                        "name": "210Ah Akü Paketi",
                        "description": "Uzun vardiya için yüksek kapasiteli enerji",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Katlanır Platform ve Korkuluk",
                        "description": "Uzun mesafe sürüşte operatör konforu için ekipman",
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
                        "question": "800 kg kapasite altında şasi dengesi ve mast rijitliği nasıldır?",
                        "answer": "Karşı ağırlıklı yapı ve 1760 kg servis ağırlığı, 3000 mm’ye kadar kaldırmada mast rijitliğini destekler; oransal kaldırma salınımı azaltır."
                    },
                    {
                        "question": "RSC082 dar koridorlarda minimum dönüş alanı olarak ne sağlar?",
                        "answer": "1250 mm dönüş yarıçapı ve 2404 mm toplam uzunluk ile 2828 mm koridor genişliğinde boyuna paletleme yapılabilir."
                    },
                    {
                        "question": "Standart çatal ölçüleri ve palet uyumluluğu nedir?",
                        "answer": "35×100×1070 mm çatallar standarttır; 800×1200 ve 1000×1200 paletlerle raf içi operasyonlarda uyumludur."
                    },
                    {
                        "question": "Yürüyüş ve kaldırma hızları hangi seviyededir?",
                        "answer": "Yürüyüş hızı 5.5/6 km/sa, kaldırma hızı 0.13/0.20 m/sn ve indirme hızı 0.16/0.15 m/sn’dir."
                    },
                    {
                        "question": "Elektronik direksiyonun kullanım avantajı nedir?",
                        "answer": "Hassas yönlendirme sağlar, düşük hızda mikromanipülasyon yeteneği sunar ve operatör yorgunluğunu azaltır."
                    },
                    {
                        "question": "Eğim performansı ve rampa yaklaşımı nasıl yönetilir?",
                        "answer": "Elektromanyetik fren ve AC tahrik rampa yaklaşımında kontrollü duruş sunar; kısa rampalarda stabil ilerleme sağlar."
                    },
                    {
                        "question": "Bakım aralıkları ve parça ömrü açısından AC sürüşün etkisi nedir?",
                        "answer": "Fırçasız yapı nedeniyle bakım aralıkları uzar; ısı üretimi düşük olduğundan güç aktarımı verimlidir."
                    },
                    {
                        "question": "Operatör platformu ve yan korkuluk opsiyonları hangi durumlarda önerilir?",
                        "answer": "Uzun mesafeli depo içi transferler ve sık vardiya değişimlerinde yorgunluğu azaltmak için önerilir."
                    },
                    {
                        "question": "Akü kapasitesi ve şarj altyapısı vardiya planını nasıl etkiler?",
                        "answer": "24V/210Ah kapasite ve 30 A şarj, günlük vardiya planlarında esneklik sağlar; hızlı şarj opsiyonlarıyla kesinti azalır."
                    },
                    {
                        "question": "Zemin türlerine göre tekerlek seçiminin etkisi nedir?",
                        "answer": "PU tekerlekler beton ve epoksi zeminlerde sessiz ve iz bırakmayan çalışma sağlayarak vibrasyonu düşürür."
                    },
                    {
                        "question": "Gürültü seviyesi operatör konforu üzerinde nasıl bir etki yapar?",
                        "answer": "74 dB(A) seviyesinde çalışır; yoğun depo ortamında konforu korur ve iletişimi kolaylaştırır."
                    },
                    {
                        "question": "Satın alma ve satış sonrası destek için kiminle iletişim kurabilirim?",
                        "answer": "Teknik bilgi, stok ve teklif için İXTİF satış ekibine 0216 755 3 555 üzerinden ulaşabilirsiniz."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: RSC082");
    }
}
