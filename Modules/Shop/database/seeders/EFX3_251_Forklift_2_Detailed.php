<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFX3_251_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFX3-251')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFX3-251');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section>\n    <h2>İXTİF EFX3 251 ile güçlü denge, akıllı enerji</h2>\n    <p>Günün ilk sevkiyatı kapıya yanaştığında, operatörün ihtiyacı olan şey güvenilir bir kalkış, tutarlı hızlanma ve \n    dar alanlarda stres yaratmayan manevradır. İXTİF EFX3 251 tam burada öne çıkar. Yüksek yerden \n    yükseklik ve iri lastikler, çukur ve bozuk zeminlerde bile titreşimi azaltır. Suya karşı korumalı gövde, hava \n    şartları değişse de işin kesintisiz devam etmesini sağlar. Çıkarılabilir Li‑Ion batarya ise gün boyu operasyonların \n    ritmine uyar; vardiya aralarında fırsat şarjı yaparak plan dışı duruşları azaltır. Ergonomik bölge tasarımı, \n    ayarlanabilir direksiyon ve rahat pedal yerleşimiyle operatör yorgunluğunu düşürür; sadeleştirilmiş panel ise \n    sık kullanılan işlevlere hızlı erişim sunar.</p>\n</section>\n<section>\n    <h3>Teknik gücün özeti</h3>\n    <p>2500 kg nominal kapasite ve 80V/150Ah (opsiyon 80V/280Ah) Li‑Ion enerji mimarisi, yüksek performansı düşük işletme \n    maliyeti ile birleştirir. PMSM tahrik, mekanik sürtünmeyi azaltan yapısı sayesinde daha yüksek verim ve daha uzun \n    çalışma süresi sağlar. 2217 mm dönüş yarıçapı, raf aralarında ve dar koridorlarda akıcı yön \n    değiştirmeye olanak verir. 3000 mm kaldırma yüksekliği ile tipik stok yüksekliklerini güvenle \n    karşılar. Yürüyüş hızı 11/12 km/s (yük/boş), kaldırma hızı 0.29/0.36 m/s ve indirme \n    hızı 0.4/0.43 m/s değerleri, ritmik yükleme/boşaltma döngülerine uyum sağlar. Li‑Ion kimya, bakım \n    gerektirmeyen yapısı ve hızlı şarj kabiliyetiyle, kurşun-asit sistemlere göre daha kararlı bir işletim vaat eder. \n    Entegre tek faz 16A şarj cihazı, ilave altyapı ihtiyacını azaltır ve sahada pratik enerji erişimi sunar.</p>\n</section>\n<section>\n    <h3>Sonuç</h3>\n    <p>İşletmeler, kaliteyi ve çevikliği aynı platformda arıyor. İXTİF EFX3 251; enerji verimli \n    PMSM motoru, çıkarılabilir Li‑Ion bataryası ve iç/dış mekâna uygun gövde korumasıyla, farklı vardiya düzenlerinde \n    güvenilir bir üretkenlik sağlar. Doğru konfigürasyon, doğru ekipman ve doğru servis ile operasyonlarınızın temposunu \n    yükseltmek için bizi arayın: 0216 755 3 555</p>\n</section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "2500 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "80V/150Ah (opsiyon 80V/280Ah)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "11/12 km/s (yük/boş)"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "2217 mm yarıçap"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "Li‑Ion enerji mimarisi",
                        "description": "Bakım gerektirmez; hızlı ve fırsat şarjı ile çok vardiya desteği"
                    },
                    {
                        "icon": "bolt",
                        "title": "PMSM tahrik verimliliği",
                        "description": "Daha az kayıp ile %10’a varan enerji tasarrufu ve uzun çalışma süresi"
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt manevra",
                        "description": "Dar alanlarda 2217 mm dönüş ile akıcı yön değişimi"
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Suya dayanıklı gövde",
                        "description": "Açık alanda yağmur altında güvenli işletim için koruma"
                    },
                    {
                        "icon": "star",
                        "title": "Ergonomik bölge",
                        "description": "Ayarlanabilir direksiyon, rahat pedallar ve sade panel ile konfor"
                    },
                    {
                        "icon": "plug",
                        "title": "Entegre şarj kolaylığı",
                        "description": "Tek faz 16A fiş ile altyapı gereksinimini azaltan çözüm"
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
                        "text": "Açık alan stok sahalarında palet yükleme/boşaltma ve kamyon yanaşma operasyonları"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır sanayi tesislerinde yarı mamul ve kalıp transferi"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende DC’lerinde raf arası replenishment ve çapraz sevkiyat"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Yağmurlu/soğuk hava koşullarında dış saha taşıma görevleri"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça depolarında rampa besleme ve hat içi lojistik"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya üretiminde güvenli konteyner ve IBC palet hareketi"
                    },
                    {
                        "icon": "pills",
                        "text": "Medikal & ilaç depolarında hassas ürün palet akışı"
                    },
                    {
                        "icon": "box-open",
                        "text": "E‑ticaret fulfillment merkezlerinde yüksek devirli çıkış hattı"
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
                        "text": "PMSM motor verimi ile daha düşük enerji tüketimi ve uzun vardiya süresi"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Çıkarılabilir Li‑Ion batarya: yan çekme ile hızlı değişim, minimum duruş"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi ve küçük dönüş yarıçapı ile dar alan performansı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Su korumalı yapı; iç/dış mekân çok yönlülüğü"
                    },
                    {
                        "icon": "star",
                        "text": "Operatör konforu odaklı ergonomik yerleşim"
                    },
                    {
                        "icon": "cog",
                        "text": "Tek faz entegre şarj ile düşük altyapı ihtiyacı"
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
                        "text": "E‑ticaret ve Fulfillment"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL ve Lojistik Hizmetleri"
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
                        "text": "İçecek ve Şişeleme"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve Medikal"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya ve Boya"
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
                        "text": "Beyaz Eşya ve Tüketici Elektroniği"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv ve Tedarikçileri"
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
                        "text": "Tarım ve Bahçe Ürünleri"
                    },
                    {
                        "icon": "paw",
                        "text": "Evcil Hayvan Ürünleri"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(
                <<<'JSON'
                {
                    "coverage": "Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi; Li‑Ion batarya modülleri 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarındaki üretim kusurlarını kapsar.",
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
                        "name": "Entegre 16A Şarj Cihazı",
                        "description": "Tek faz 16A giriş ile sahada pratik enerji beslemesi.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Yandan Çekilebilir Batarya Rayı",
                        "description": "Bataryanın hızlı değişimi için kızak/ray sistemi.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "wheelchair",
                        "name": "Lastik Seçenekleri",
                        "description": "Bozuk zeminler için farklı dolgu lastik bileşenleri.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "charging-station",
                        "name": "80V/280Ah Batarya Paketi",
                        "description": "Uzatılmış menzil için yüksek kapasiteli Li‑Ion paket.",
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
                        "question": "PMSM motorun verim avantajı işletme maliyetini nasıl etkiler?",
                        "answer": "Daha düşük kayıplar sayesinde enerji tüketimi azalır, aynı vardiyada daha fazla çevrim yapılır ve şarj aralığı uzar; toplam sahip olma maliyeti düşer."
                    },
                    {
                        "question": "Li‑Ion batarya ile fırsat şarjı güvenli midir ve hücre ömrüne etkisi nedir?",
                        "answer": "Gömülü BMS, akım ve sıcaklığı yöneterek güvenli fırsat şarjına izin verir; kısmi şarjlar döngü ömrünü olumsuz etkilemeden vardiya planını esnekleştirir."
                    },
                    {
                        "question": "Suya dayanıklı tasarımın açık alanda operasyonlara katkısı nedir?",
                        "answer": "Yağmur altında bile fren, tahrik ve elektronik bileşenlerin korunması sayesinde uçtan uca operasyon devam eder; plan dışı duruşlar azalır."
                    },
                    {
                        "question": "Dönüş yarıçapı dar raf koridorlarında ne kazandırır?",
                        "answer": "Küçük yarıçap palet konumlandırmayı kolaylaştırır, düzeltme manevrası sayısını azaltır ve çevrim süresini kısaltır."
                    },
                    {
                        "question": "Ergonomik sürüş pozisyonu operatör yorgunluğunu nasıl azaltır?",
                        "answer": "Ayarlanabilir direksiyon ve pedallar, bilek ve diz açılarının doğal kalmasını sağlar; gün sonu yorgunluğu ve hata riski azalır."
                    },
                    {
                        "question": "Bakım periyotları Li‑Ion sistemle nasıl değişir?",
                        "answer": "Su ekleme, dengeleme ve havalandırma gerektirmediği için planlı bakım listesi kısalır; duruş süresi ve maliyet düşer."
                    },
                    {
                        "question": "Entegre tek faz şarj altyapı gereksinimini nasıl sadeleştirir?",
                        "answer": "Harici endüstriyel şarj istasyonlarına bağımlılığı azaltır; sahada standart prizle besleme mümkün olur."
                    },
                    {
                        "question": "Kaldırma ve indirme hızları hassas yüklerde kontrol edilebilir mi?",
                        "answer": "Oransal hidrolik kontrol ile küçük joystick hareketlerinde bile hassas akış sağlanır; hassas yüklerde güven artar."
                    },
                    {
                        "question": "Bozuk zeminlerde dolgu lastiklerin avantajı nedir?",
                        "answer": "Delinmez yapı ve yüksek darbe sönümleme ile sarsıntı azalır; ekipman ve yük korunur."
                    },
                    {
                        "question": "Standart çatal ölçüleri değiştirilerek farklı uygulamalar desteklenebilir mi?",
                        "answer": "Evet, farklı çatal uzunlukları ve ataşmanlar ile uzun/kısa yük senaryolarına uyarlanabilir."
                    },
                    {
                        "question": "Güvenlik donanımları operatör farkındalığını nasıl artırır?",
                        "answer": "Sesli-ışıklı uyarılar ve görüş alanını artıran donanımlar kaza riskini azaltır; operasyon güvenliği yükselir."
                    },
                    {
                        "question": "Garanti kapsamı nedir ve servis nasıl sağlanır?",
                        "answer": "Makine 12 ay, Li‑Ion batarya 24 ay garanti kapsamındadır. Satış ve servis için İXTİF 0216 755 3 555 ile iletişime geçebilirsiniz."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("ℹ️ Detailed güncellendi: {$p->sku}");
    }
}
