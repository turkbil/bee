<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFX5_301_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFX5-301')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFX5-301');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section>
  <h2>İXTİF EFX5 301: Modüler Li-Ion ile kesintisiz verim</h2>
  <p>Depo kapıları açıldığında iş temposu başlar ve durmaz. İXTİF EFX5 301, modüler 80V Li-Ion akü mimarisi, güçlü
  AC sürüş ve yüksek görüşlü direk yapısıyla gün boyu aynı ritimde kalır. Tek modül 280Ah ile hafif-orta operasyonlar,
  çift modül 560Ah ile yoğun vardiyalar… Fırsat şarjı sayesinde plan dışı duruşlar azalır, operatör yeni koltuk ve geniş
  pedal düzeniyle gün boyu konforu korur. 150 mm yerden yükseklik ve büyük lastikler; rampa, çukur ve pürüzlü zeminlerde
  takılmayı önleyerek akışı sürdürür. Bu bütünlük, malzeme akışınızı öngörülebilir ve güvenli kılar.</p>
</section>
<section>
  <h3>Teknik</h3>
  <p>EFX5 platformu 16 kW S2 sürüş motoru ve 24 kW kaldırma motoruyla sınıfının çevikliği ve hızını birleştirir.
  16/17 km/s seyir, 3000 kg kg taşıma ve AC tahrik kontrolü; hızlanma ve hassas manevrada tutarlı sonuç verir.
  2498 mm dönüş yarıçapı dar koridorlarda etkili konumlanma sağlar. Triplex mastın artırılmış görüş alanı ve
  LED aydınlatmalar ile yük konumlandırma netleşir. Hidrolik servis freni ve mekanik park freni güvenli duruş sunarken,
  ≤74 dB(A) ses seviyesi operatör yorgunluğunu azaltır. 80V/280Ah standart modül, ikinci bir modülle 560Ah’a yükselir;
  bu sayede sezonluk piklerde dayanım kolayca artırılır.</p>
</section>
<section>
  <h3>Sonuç</h3>
  <p>İş hacminiz değişse bile EFX5 301 esner: tek bataryayla ekonomik kullanım, çift bataryayla uzun dayanım.
  Depo düzeniniz, güvenlik standartlarınız ve vardiya planınız ne olursa olsun, EFX5 modern Li-Ion mimarisiyle
  yakıt/akü bakımı gerektirmeyen temiz bir operasyon sağlar. Teklif ve demo için 0216 755 3 555.</p>
</section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "3000 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "80V Li-Ion (280Ah/560Ah)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "16/17 km/s"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "2498 mm"
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
                        "title": "Modüler Li-Ion mimari",
                        "description": "Tek modül 280Ah, çift modül 560Ah seçenekleri"
                    },
                    {
                        "icon": "bolt",
                        "title": "Yüksek performans",
                        "description": "16 kW sürüş + 24 kW kaldırma ile hızlı tepki"
                    },
                    {
                        "icon": "warehouse",
                        "title": "150 mm yerden yükseklik",
                        "description": "Rampa ve pürüzlü zeminde takılmadan ilerler"
                    },
                    {
                        "icon": "star",
                        "title": "Geliştirilmiş görünürlük",
                        "description": "Yeni triplex direk ve LED farlar"
                    },
                    {
                        "icon": "cog",
                        "title": "Düşük bakım",
                        "description": "Li-Ion’da su tamamlama ve periyodik bakım yok"
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenlik",
                        "description": "Hidrolik servis + mekanik park freni"
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
                        "text": "Yoğun 3PL depolarında cross-dock ve rampa transferleri"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende DC’lerde raf besleme ve palet toplama"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Gıda depolarında giriş-çıkış ve zemin geçişleri"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç lojistiğinde hassas palet hareketleri"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yan sanayi hat beslemeleri"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır sanayide kalıp-çelik palet taşıma"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya sahalarında kapalı alan içi forklift operasyonu"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik üretimde WIP taşıma ve sevkiyat hazırlığı"
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
                        "text": "AC tahrik kontrolüyle kararlı hızlanma ve eğimde çekiş"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Fırsat şarjı ile vardiya içi esneklik ve kesintisiz operasyon"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt ölçüler ve dar dönüş yarıçapı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Gelişmiş frenleme ve düşük gürültü seviyesi"
                    },
                    {
                        "icon": "star",
                        "text": "Yeni direk ve koltuk ile operatör verimi"
                    },
                    {
                        "icon": "building",
                        "text": "Modüler stok yönetimi: tek gövdede farklı dayanım"
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
                        "icon": "car",
                        "text": "Otomotiv ve Yedek Parça"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır Sanayi ve Metal"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve Hazır Giyim"
                    },
                    {
                        "icon": "building",
                        "text": "İnşaat Malzemeleri"
                    },
                    {
                        "icon": "briefcase",
                        "text": "B2B Toptan Dağıtım"
                    },
                    {
                        "icon": "print",
                        "text": "Matbaa ve Ambalaj"
                    },
                    {
                        "icon": "tv",
                        "text": "Beyaz Eşya ve Elektrikli Ev Aletleri"
                    },
                    {
                        "icon": "seedling",
                        "text": "Tarım ve Hasat Sezonu Lojistiği"
                    },
                    {
                        "icon": "couch",
                        "text": "Mobilya ve Ahşap"
                    },
                    {
                        "icon": "ship",
                        "text": "Liman ve İç Lojistik"
                    },
                    {
                        "icon": "train",
                        "text": "Demiryolu Depoları"
                    },
                    {
                        "icon": "plane",
                        "text": "Hava Kargo Ambarları"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "Yerleşik Li-Ion Şarj Cihazı",
                        "description": "Hızlı fırsat şarjı için optimize edilmiş",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Yan Kaydırma (Side Shifter)",
                        "description": "Yük konumlandırmada hassasiyet",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "wrench",
                        "name": "Tam Bakım Paketi",
                        "description": "Planlı bakım ve kontrol seti",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "circle-notch",
                        "name": "Lastik Opsiyonları",
                        "description": "Saha koşuluna uygun lastik seçimi",
                        "is_standard": true,
                        "price": null
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
                        "question": "Modüler Li-Ion batarya tek ve çift modül arasında nasıl fark yaratıyor?",
                        "answer": "Tek modül 280Ah, orta yoğunluk için yeterli dayanım sunar. Çift modül 560Ah ise uzun vardiya ve tepe sezonlarında şarj molalarını azaltır."
                    },
                    {
                        "question": "Fırsat şarjı günlük operasyonda akü ömrünü olumsuz etkiler mi?",
                        "answer": "Li-Ion kimya ve entegre BMS, kısa aralıklı şarjları destekler. Döngü ömrünü korurken çalışma süresini uzatır, bakım gerektirmez."
                    },
                    {
                        "question": "150 mm yerden yükseklik saha geçişlerinde ne kazandırır?",
                        "answer": "Rampa, eşik ve bozuk zeminlerde şase sürtmesini önler; forkliftin takılmadan ilerlemesini ve yük akışının kesintisiz kalmasını sağlar."
                    },
                    {
                        "question": "Direk görünürlüğü ve LED aydınlatma güvenliği nasıl etkiler?",
                        "answer": "Triplex direk profili ve LED farlar, yük ve çatalların net görülmesini sağlar. Bu sayede çarpmalar ve palette hasar riski azalır."
                    },
                    {
                        "question": "Sürüş ve kaldırma motor güçleri performansa nasıl yansıyor?",
                        "answer": "16 kW AC sürüş, hızlı tepki ve eğimde çekiş sağlar. 24 kW kaldırma, tam kapasitede bile seri yük kaldırma ve indirme hızlarını korur."
                    },
                    {
                        "question": "Hidrolik servis freni ile mekanik park freninin avantajı nedir?",
                        "answer": "Operasyon sırasında güçlü ve dozajlanabilir duruş sağlanır. Park freni devredeyken rampada sabitleme güvenilirdir."
                    },
                    {
                        "question": "Gürültü seviyesi operatör yorgunluğunu nasıl etkiler?",
                        "answer": "≤74 dB(A) seviyesinde çalışan sistem, uzun vardiyalarda operatör konforu ve dikkatini destekler."
                    },
                    {
                        "question": "Bakım aralıkları ve periyodik kontroller nasıl yönetilir?",
                        "answer": "Li-Ion bataryada su tamamlama yoktur. Planlı bakım ağırlıkla güvenlik ve sistem kontrollerinden oluşur, duruş süreleri kısadır."
                    },
                    {
                        "question": "Yan kaydırma ve ataşmanlar kapasiteyi ne kadar etkiler?",
                        "answer": "Standart grafiğe göre yan kaydırma ile nominal kapasiteden yaklaşık 100 kg düşüş dikkate alınmalıdır."
                    },
                    {
                        "question": "Yedek parça ve servis erişimi nasıl planlanır?",
                        "answer": "Yaygın sarf ve ana komponentler stoklu tutulur; uzaktan destek ve yerinde servis ile arıza süreleri minimize edilir."
                    },
                    {
                        "question": "Garanti kapsamı nedir ve batarya için özel koşul var mı?",
                        "answer": "Makine 12 ay, Li-Ion batarya modülleri 24 ay garanti kapsamındadır. Kapsam üretim hatalarını içerir, sarf ve darbe kaynaklı hasarlar hariçtir."
                    },
                    {
                        "question": "Satın alma ve teklif süreci için kime ulaşabilirim?",
                        "answer": "İXTİF satış ekibi ile iletişime geçebilirsiniz: 0216 755 3 555. Size en uygun konfigürasyonu birlikte belirleyelim."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: EFX5-301");
    }
}
