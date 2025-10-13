<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WSA161_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'WSA161')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: WSA161');
            return;
        }
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '
<section><h2>İXTİF WSA161: Ağır Hizmette Hız, Hassasiyet ve Li‑Ion Konforu</h2>
<p>Depo koridorları daralırken sipariş hacmi artıyor. WSA161, Li‑Ion teknolojisiyle tasarlanmış ağır hizmet
yaya kumandalı istifleyici konseptini yeni bir verim standardına taşıyor. Entegre şarj, hızlı kaldırma/indirme
hızları ve oransal kaldırma sistemi sayesinde operatör, paleti raf gözünde milimetrik doğrulukla konumlandırır.
Kompakt gövde ve kısa l2 ölçüsü manevrayı kolaylaştırırken, iyileştirilmiş kapak yapısı üzerindeki evrak cebi ve
USB çıkışı günlük akışa pratiklik katar. 1600 kg nominal kapasite ile yoğun vardiyalarda stabil performans üretir.</p></section>
<section><h3>Teknik Güç ve Performans</h3>
<p>WSA161’de AC sürüş motoru ve güçlü kaldırma motoru birlikte çalışarak istif süresini kısaltır. Yüklü/boş
seyir hızları 5/5.5 km/s olup, hızlı hızlanma ve dengeli yavaşlama ile hassas yaklaşma manevraları desteklenir.
Kaldırma hızları ve indirme hızları, yük ve raf senaryolarına uygun şekilde optimize edilmiştir; oransal kaldırma
sayesinde palet üst seviye raflarda dahi sarsıntısız ve kontrollü hareket eder. Kompakt mimariyle kısa l2 = 774 mm
ve dar dönüş yarıçapı (Wa = 1506 mm) dar koridorlarda akıcı palet akışı sağlar. Direk kapalı yüksekliği h1 = 2015 mm,
nominal kaldırma yüksekliği h3 = 2915 mm ile dengeli ağırlık dağılımı ve görüş alanı hedeflenir. Elektromanyetik
frenleme, rampalarda ve platform kenarlarında güvenli duruş yaratır. Li‑Ion batarya paketi (24V/100Ah (205Ah opsiyonel)) vardiya
içinde mola şarjlarını mümkün kılar; bellek etkisi yoktur, bakım gerektirmez.</p></section>
<section><h3>Sonuç</h3>
<p>Yoğun sipariş rejiminde her saniye kıymetli. WSA161, hız ve hassasiyeti bir araya getirip iş güvenliğini
önceliklerken toplam sahip olma maliyetini düşürür. Mevcut raf düzenine uyum sağlayan boyutları, entegre şarj ve
Li‑Ion ekosistemi ile yatırımı hızla geri döndüren akıllı bir tercihtir. Detaylı bilgi ve teklif için 0216 755 3 555.</p></section>
'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "1600 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V/100Ah (205Ah opsiyonel)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Seyir Hızı",
                        "value": "5/5.5 km/s"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş Yarıçapı",
                        "value": "1506 mm"
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
                        "title": "Li‑Ion Enerji Mimarı",
                        "description": "Hızlı ara şarj ve sıfır bakım ile kesintisiz operasyon"
                    },
                    {
                        "icon": "bolt",
                        "title": "Hızlı İstif Döngüsü",
                        "description": "Yüksek kaldırma/indirme hızlarında seri yığma"
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt Şasi",
                        "description": "Kısa l2 ile dar koridorlarda çevik manevra"
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenli Kontrol",
                        "description": "Elektromanyetik fren ve oransal kaldırma"
                    },
                    {
                        "icon": "building",
                        "title": "Operatör Konforu",
                        "description": "Evrak cebi ve USB çıkışlı güçlü kapak"
                    },
                    {
                        "icon": "cart-shopping",
                        "title": "Depo Verimliliği",
                        "description": "Raf içi akışta milimetrik hassas konumlama"
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
                        "text": "Dar koridorlu depolarda raf içi istif ve toplama besleme"
                    },
                    {
                        "icon": "box-open",
                        "text": "E-ticaret merkezlerinde çapraz sevkiyat ve sipariş konsolidasyonu"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtımda mağaza palet akışı ve rampa yaklaşmaları"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş-çıkış hatlarında hızlı istif döngüsü"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve kozmetik depolarında hassas ürün yerleştirme"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça raflarında WIP ve tampon stok yönetimi"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır sanayi üretim adalarında yarımamul akışı"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal depo alanlarında kontrollü ve hassas istif"
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
                        "text": "Kaldırma/indirme hızlarında sınıfında iddialı çevrim süreleri"
                    },
                    {
                        "icon": "battery-full",
                        "text": "24V Li‑Ion paket ve entegre şarj ile esnek enerji yönetimi"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kısa l2 ve dar Wa ile yüksek manevra kabiliyeti"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Oransal kaldırma ve elektromanyetik fren ile güvenli kullanım"
                    },
                    {
                        "icon": "star",
                        "text": "Modern tasarım ve kullanıcı odaklı detaylarla verimli operasyon"
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
                        "icon": "pills",
                        "text": "İlaç ve Medikal"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal Depolama"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve Teknoloji"
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
                        "icon": "briefcase",
                        "text": "B2B Toptan Dağıtım"
                    },
                    {
                        "icon": "building",
                        "text": "Endüstriyel Üretim Tesisleri"
                    },
                    {
                        "icon": "award",
                        "text": "Kalite Odaklı Üretim Yapıları"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Market ve Zincir Mağaza Lojistiği"
                    },
                    {
                        "icon": "store",
                        "text": "DIY/Yapı Market Depoları"
                    },
                    {
                        "icon": "box-open",
                        "text": "Kargo ve Paketleme Merkezleri"
                    },
                    {
                        "icon": "warehouse",
                        "text": "Yedek Parça Depolama"
                    },
                    {
                        "icon": "flask",
                        "text": "Boya ve Kimyasal Lojistiği"
                    },
                    {
                        "icon": "microchip",
                        "text": "Cep telefonu ve aksesuar dağıtımı"
                    },
                    {
                        "icon": "car",
                        "text": "Lastik ve jant depolama operasyonları"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(
                <<<'JSON'
                {
                    "coverage": "Makine 12 ay, Li‑Ion batarya 24 ay garanti.",
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
                        "name": "Entegre Şarj Cihazı",
                        "description": "Dahili şarj ünitesi ile esnek ara şarj",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "PU Tekerlek Takımı",
                        "description": "Düşük gürültü ve düşük zemin aşınması",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Tandem Arka Teker Seti",
                        "description": "Yüksek stabilite için opsiyonel çift teker",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "battery-full",
                        "name": "205Ah Li‑Ion Paket",
                        "description": "Uzun çalışma süresi için artırılmış kapasite",
                        "is_standard": false,
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
                        "question": "Dar koridorlarda minimum dönüş alanı ne kadar olmalı?",
                        "answer": "Minimum dönüş yarıçapı 1506 mm değerini dikkate alarak koridor genişliği planlanmalıdır; palet ve güvenlik payı eklenmelidir."
                    },
                    {
                        "question": "Ara şarjın batarya ömrüne etkisi var mı, sınırlama gerekir mi?",
                        "answer": "Li‑Ion kimya hafıza etkisi yapmaz; kısmi şarj-döngüleri ömrü olumsuz etkilemez. Sıcaklık aralığında kullanım verimlidir."
                    },
                    {
                        "question": "Rampa çıkışlarında hız ve fren kontrolü nasıl sağlanıyor?",
                        "answer": "AC sürüş ve elektromanyetik fren, yokuşta kontrollü hızlanma/yavaşlama sağlar; yüklü/boş hız limitleri korunur."
                    },
                    {
                        "question": "Oransal kaldırma raf üst seviyede ne tür avantaj sağlar?",
                        "answer": "Milimetrik konumlama ile palet ve raf hasar riski azalır; operatör daha hızlı ve güvenli yerleştirir."
                    },
                    {
                        "question": "Soğuk oda giriş-çıkışlarında performans düşer mi?",
                        "answer": "PU teker ve kapalı tahrik mimarisiyle standart uygulamalarda verim korunur; düşük sıcaklıkta uygun yağ ve bakım önerilir."
                    },
                    {
                        "question": "Bakım periyotları ve aşınan parçalar nelerdir?",
                        "answer": "Fırçasız AC sürüş düşük bakım ister; teker ve fren balataları kullanım yoğunluğuna göre periyodik kontrol edilir."
                    },
                    {
                        "question": "Maksimum kaldırma yüksekliği ve h1/h3 kombinasyonları nelerdir?",
                        "answer": "Çeşitli direk opsiyonları mevcuttur; 3.0 m’den 4.5 m’ye seçeneklerle farklı koridor ve raf yapılarına uyar."
                    },
                    {
                        "question": "Operatör konforuna yönelik hangi detaylar bulunuyor?",
                        "answer": "Geliştirilmiş kapak, evrak cebi ve USB çıkışı; ergonomik kumanda kolu ile günlük kullanım kolaylaşır."
                    },
                    {
                        "question": "Yedek parça ve servis erişimi nasıl sağlanır?",
                        "answer": "Modüler yapı, hızlı parça değişimi ve erişilebilir kablo yolları ile servis süresi kısalır."
                    },
                    {
                        "question": "Güvenlik açısından iniş kontrolü ve frenleme nasıl çalışır?",
                        "answer": "İndirme hızları kontrollüdür; elektromanyetik fren devredeyken istenmeyen hareket engellenir."
                    },
                    {
                        "question": "Opsiyonel yüksek kapasiteli batarya seçeneği mevcut mu?",
                        "answer": "Uzun vardiyalar için 205Ah Li‑Ion paket seçeneği sunulur; şarj altyapınıza göre önerilir."
                    },
                    {
                        "question": "Garanti kapsamı ve iletişim bilgisi nedir?",
                        "answer": "Makine 12 ay, Li‑Ion batarya 24 ay garantilidir. İXTİF satış ve teknik destek: 0216 755 3 555."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed: WSA161 güncellendi');
    }
}
