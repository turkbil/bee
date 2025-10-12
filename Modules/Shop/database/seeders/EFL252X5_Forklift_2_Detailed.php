<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL252X5_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'EFL252X5')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı: EFL252X5'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>Akıcı Güç, Akıllı Enerji</h2><p>Sabah erken saatlerde rampa kapıları açılırken, EFL252X5 sessizce devreye girer. Elektrikli karşı
denge mimarisi ve Li‑ion enerji sistemi sayesinde gün boyunca dur‑kalklarda hız kaybetmeden akıcı
bir akış sağlar. 2.5 ton kapasite, kompakt gövde ve modern ergonomi bir araya gelerek depo içi ve
açık saha operasyonlarında aynı ölçüde çeviklik ve güç sunar. Operatör için geniş görüş alanı ve
sezgisel kumandalar, ilk dakikadan itibaren kontrol hissini artırır; sessiz çalışma ise ekip içi
iletişimi kolaylaştırır.</p></section><section><h3>Teknik Güç ve Verimlilik</h3><p>EFL252X5’in performans kalbinde 16 kW tahrik ve 24 kW kaldırma motoru bulunur. 16/17 km/s sürüş hızı
ile 0.38/0.45 m/s kaldırma hızı, yük akışını hızlandırırken 17/25% tırmanma kabiliyeti rampalı
tesislerde esneklik sağlar. Li‑ion mimaride 80V 280Ah tek modül standarttır; yoğun dönemlerde ikinci
modül ile 560Ah kapasiteye geçilerek kesintisiz vardiya sağlanır. 150 mm yerden yükseklik ve
pnömatik lastikler, eşiksiz kapılar, çukurlar ve pürüzlü saha geçişlerinde ekipmanı korur. Direk
mimarisi 3000 mm kaldırma ve 4065 mm açık yükseklikte yüksek görüş sunar. 2270 mm dönüş yarıçapı dar
koridorlarda manevrayı kolaylaştırır; ≤74 dB(A) gürültü seviyesi ise konforu yükseltir.</p></section><section><h3>Sonuç</h3><p>Enerji verimliliği, hız ve güvenliği tek gövdede birleştiren EFL252X5; e‑ticaret, 3PL ve üretim gibi
farklı sektörlerde çevrim sürelerini kısaltmak için tasarlandı. Modüler batarya ve fırsat şarjı, pik
dönemlerde bile ritminizi korumanıza yardım eder. Uygulamanız için en doğru direk, ataşman ve
batarya kombinasyonunu birlikte seçmek üzere ekibimizle görüşün. Detaylı bilgi ve demo planlamak
için 0216 755 3 555 numaralı hattan bize ulaşın.</p><p>0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "2500 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "80V 280Ah (opsiyon 560Ah)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "16/17 km/s (yükle/yüksüz)"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "2270 mm yarıçap"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "Modüler Li‑ion Enerji",
                        "description": "Tek modül 280Ah, çift modül 560Ah ile vardiyalar arasında fırsat şarjı"
                    },
                    {
                        "icon": "bolt",
                        "title": "Performans Odaklı Sürüş",
                        "description": "16 kW sürüş ve 24 kW kaldırma motoru ile yüksek ivmelenme"
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "150 mm Yerden Yükseklik",
                        "description": "Rampalar ve çukurlarda takılmadan akıcı ilerleme"
                    },
                    {
                        "icon": "star",
                        "title": "Yüksek Görüşlü Triplex Direk",
                        "description": "Hassas istif için iyileştirilmiş görünürlük"
                    },
                    {
                        "icon": "hand",
                        "title": "Ergonomi ve Konfor",
                        "description": "Geniş ekran, kol dayamalı koltuk ve geniş pedal"
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenlik Odaklı Mimari",
                        "description": "Hidrolik servis freni ve mekanik park freni"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "warehouse",
                        "text": "Yoğun vardiyalı depolarda palet giriş‑çıkış ve hat besleme"
                    },
                    {
                        "icon": "box-open",
                        "text": "E‑ticaret operasyonlarında çapraz sevkiyat ve konsolidasyon"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende DC’lerde ürün toplama sonrası yükleme"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça depolarında rampa yaklaşımı ve transfer"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal ambalajlı ürünlerin güvenli taşıması"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk oda giriş‑çıkış trafiğinde hızlı manevra"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücreleri arasında WIP hareketi"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç ve medikal kutu/palet taşıması için hassas kontrol"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "Yük altında dahi dengeli ivmelenme ve kısa çevrim süresi"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Fırsat şarjı ile planlı duruşları azaltan Li‑ion mimari"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "150 mm yerden yükseklik ile dış saha kullanım esnekliği"
                    },
                    {
                        "icon": "star",
                        "text": "Yeni aydınlatmalar ve geniş ekranla güvenli, sezgisel kullanım"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "≤74 dB(A) gürültü ile konforlu çalışma ortamı"
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
                        "text": "3PL ve sözleşmeli lojistik depoları"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım ve DC operasyonları"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Hızlı tüketim (FMCG) dağıtım ağları"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Soğuk zincir ve gıda depolama"
                    },
                    {
                        "icon": "wine-bottle",
                        "text": "İçecek ve meşrubat lojistiği"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç, medikal ve sağlık tedarik zinciri"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimyasal hammadde ve ara mamul depoları"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve kişisel bakım lojistiği"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve yüksek katma değerli ürünler"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv tedarikçileri ve yedek parça depoları"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve hazır giyim üretim/dağıtım"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır sanayi ve genel üretim tesisleri"
                    },
                    {
                        "icon": "briefcase",
                        "text": "B2B toptan ve bölgesel dağıtım merkezleri"
                    },
                    {
                        "icon": "building",
                        "text": "Şehir içi konsolidasyon ve mikro hub noktaları"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Market zincirleri arası palet transferi"
                    },
                    {
                        "icon": "book",
                        "text": "Yayıncılık ve kırtasiye dağıtımı"
                    },
                    {
                        "icon": "tv",
                        "text": "Beyaz eşya ve tüketici elektroniği"
                    },
                    {
                        "icon": "paw",
                        "text": "Evcil hayvan mamaları ve aksesuar depoları"
                    },
                    {
                        "icon": "print",
                        "text": "Ambalaj, matbaa ve karton üretimi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(<<<'JSON'
                {
                    "coverage": "Makine 12 ay, Li‑ion batarya modülleri 24 ay garanti kapsamındadır. Garanti üretim hatalarını kapsar ve yetkili servislerce yürütülür.",
                    "duration_months": 12,
                    "battery_warranty_months": 24
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "80V Endüstriyel Şarj Cihazı",
                        "description": "Li‑ion batarya için yüksek verimli şarj ünitesi",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "İkinci Batarya Modülü (560Ah)",
                        "description": "Çift modülle uzatılmış çalışma süresi",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "wrench",
                        "name": "Yan Kaydırıcı Ataşmanı",
                        "description": "Hassas yük merkezleme; kapasitede 100 kg düşüş gerekir",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "award",
                        "name": "Tam LED Aydınlatma Paketi",
                        "description": "Ön LED farlar ve ikaz seti, düşük tüketim",
                        "is_standard": true,
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
                    },
                    {
                        "icon": "award",
                        "name": "ISO 9001",
                        "year": "2023",
                        "authority": "SGS"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "question": "Günlük çift vardiya için tek batarya modülü yeterli olur mu, şarj stratejisi nasıl olmalı?",
                        "answer": "Tek modül 280Ah hafif‑orta yoğunlukta fırsat şarjı ile çalışabilir. Yoğun kullanımda öğle arasında 30‑45 dakikalık şarj planlanmalıdır."
                    },
                    {
                        "question": "EFL252X5 dış sahada rampalar ve bozuk zeminlerde nasıl performans gösterir?",
                        "answer": "150 mm yerden yükseklik ve pnömatik lastikler rampalarda takılmayı azaltır; 17/25% tırmanma ile yük altında dahi dengeli hareket sağlar."
                    },
                    {
                        "question": "Operatör görünürlüğü ve güvenlik ekipmanları hangi iyileştirmeleri içeriyor?",
                        "answer": "Yeni triplex direk yüksek görüş sunar, LED farlar ve genişletilmiş fren pedalı operatör güvenini ve kontrolü artırır."
                    },
                    {
                        "question": "Bakım aralıkları ve tüketim parçaları Li‑ion sistemde nasıl değişir?",
                        "answer": "Li‑ion batarya sıfır bakım gerektirir; su ekleme veya hücre dengeleme gerekmez. Planlı kontroller elektrikli sistem odaklıdır."
                    },
                    {
                        "question": "Yan kaydırıcı takıldığında kapasitede neden düşüş olur?",
                        "answer": "Ataşmanın ağırlığı ve yük merkezi sapması sebebiyle nominal kapasiteden yaklaşık 100 kg düşüş gerekir; bu üretici grafikleriyle uyumludur."
                    },
                    {
                        "question": "Hız ve kaldırma değerleri gerçek operasyonda ne ifade eder?",
                        "answer": "16/17 km/s sürüş ve 0.38/0.45 m/s kaldırma; çevrim süresini kısaltır, rampa ve kapı geçişlerinde akıcılık sağlar."
                    },
                    {
                        "question": "Şarj altyapısı için hangi elektriksel gereksinimler gerekir?",
                        "answer": "80V Li‑ion şarj cihazı trifaze besleme ile çalışır; tesisinizin güç ve priz konumları şarj istasyonuna göre planlanmalıdır."
                    },
                    {
                        "question": "Kabin ve koltuk ergonomisi uzun vardiyalarda yorgunluğu nasıl azaltır?",
                        "answer": "Koldayamalı koltuk, geniş ekran ve kumandalar doğal vücut pozisyonunu destekler; düşük titreşim ve ≤74 dB(A) gürültü konfor sağlar."
                    },
                    {
                        "question": "Yedek parça ve servis sürekliliği operasyon kesintilerini nasıl önler?",
                        "answer": "Modüler batarya ve standartlaştırılmış ana bileşenler hızlı değişim ve stok yönetimi kolaylığı sağlar."
                    },
                    {
                        "question": "Garanti kapsamı ve batarya için farklı süre var mı, uzatılabilir mi?",
                        "answer": "Makine için 12 ay, Li‑ion batarya modülleri için 24 ay garanti bulunur; talebe göre uzatılmış paketler sunulabilir."
                    },
                    {
                        "question": "İXTİF hangi şehirlerde kurulum ve kullanıcı eğitimini sağlayabiliyor?",
                        "answer": "Kurulum, devreye alma ve operatör eğitimi ülke çapında planlanır. Detay ve takvim için İXTİF 0216 755 3 555 hattını arayabilirsiniz."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed güncellendi: EFL252X5");
    }
}