<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ES14_14WA_Istif_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'ES14-14WA')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: ES14-14WA');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>Operasyonların Ritmini Yükselten Akıllı İstif</h2>
<p>Günün ilk sevkiyatı kapıya yanaşırken, ekip raf yenilemeye çoktan başlar. İXTİF ES14-14WA dar koridorlarda sakin ama
emin adımlarla ilerler; AC sürüş sistemi yük altındayken bile akıcı hızlanma sağlar. 600&nbsp;mm yük merkezi ve
1400 kg nominal kapasite dengeli istifi mümkün kılar. İki kademeli indirme, üst raflara yaklaşırken
hassasiyet kazandırır ve yükün yumuşak bırakılmasını sağlar. Kompakt gövde genişliği (800&nbsp;mm) ve kısa dönüş
yarıçapı, 90° köşe dönüşlerini kolaylaştırır ve operatör yorgunluğunu azaltır.</p></section><section><h3>Teknik Tasarım: Güç, Kontrol ve Dayanıklılık</h3>
<p>1.1 kW (S2 60 dk) sürüş motoru, vardiya boyunca tutarlı performans sunar. Kaldırma görevlerini
3.0 kW (S3 15%) gücündeki hidrolik ünite üstlenir; bu sayede yüklü/boş kaldırma hızları 0.13 / 0.16 m/s (yüklü/boş)
seviyesinde kalır. Seyir hızları 5.0 / 5.5 km/s (yüklü/boş) olup AC kontrol, rampa çıkışlarında çekişi optimize eder.
Poliüretan sürüş ve yük tekerlekleri beton zeminlerde sessiz hareket ve daha düşük yuvarlanma direnci getirir.
Direk konfigürasyonları 2500–4800&nbsp;mm aralığını kapsar; standart 3000&nbsp;mm yükseklikte direk kapalıyken
2030 mm ve açıkken 3465 mm değerlerine ulaşır. Gövde yerden yüksekliği (m2)
30 mm düzeyinde olduğundan rampalarda paletin sürtünmesini azaltır. Ana şasi ölçüleri (l1)
1940 mm ve (l2) 790 mm, koridorda konumlamayı kolaylaştırır. Elektromanyetik park
freni konforlu duruş sağlarken, eğimde geri kaçmayı önleyen mantıklarla güvenliği destekler. Akü tarafında
24V 210Ah (190 kg) yapılandırmaları desteklenir; entegre göstergede kalan süre takibi yapılabilir. Bakım erişim
noktaları yalındır; mekanik direksiyon tertibatı ve modüler kapaklar servis süresini kısaltır.</p></section><section><h3>Saha Operasyonlarında Fark Yaratan Detaylar</h3>
<p>Standart EUR paletlerde çift yük tekerlekleri yük dağılımını iyileştirir ve rampalara giriş-çıkışlarda darbeyi azaltır.
Manevra sırasında hız kesme, dar raf aralarında güvenli yaklaşma sağlar. 550/685&nbsp;mm çatal aralığı seçenekleri farklı
palet tiplerine uyum verir. Opsiyon listesinde 1150/1220&nbsp;mm çatal uzunluğu, iz bırakmayan Trace PU sürüş tekeri,
230Ah yüksek kapasiteli akü ve 24V/30A harici şarj cihazı bulunur. Tüm konfigürasyonlarda iki kademeli indirme ile ürün
hasar riskini düşürür, istif tekrarlarını azaltırsınız.</p></section><section><h3>Sonuç</h3>
<p>İş gücü verimliliği artık saniyelerle ölçülüyor. İXTİF ES14-14WA, güvenlik ve hız arasında ideal dengeyi kurarak toplam
sahip olma maliyetini düşürür. Doğru konfigürasyon için ekibimizle görüşün: <strong>0216 755 3 555</strong>.</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "1400 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "24V 210Ah (190 kg)"
                    },
                    {
                        "icon": "gauge",
                        "label": "Seyir Hızı",
                        "value": "5.0 / 5.5 km/s (yüklü/boş)"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş Yarıçapı",
                        "value": "1589 mm"
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
                        "title": "Uzun Vardiya Enerjisi",
                        "description": "210–230Ah akü seçenekleriyle tek vardiyada kesintisiz çalışma sağlar."
                    },
                    {
                        "icon": "weight-hanging",
                        "title": "Dengeli Kaldırma",
                        "description": "600 mm yük merkezi ile istifleme sırasında stabilite artar."
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt Gövde",
                        "description": "800 mm genişlik ve kısa dönüş yarıçapı ile dar alanlarda çevik."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenlik Odaklı Kontrol",
                        "description": "Elektromanyetik fren ve hız sınırlama ile emniyetli kullanım."
                    },
                    {
                        "icon": "warehouse",
                        "title": "Esnek Direk Seçenekleri",
                        "description": "2500–4800 mm aralığında farklı istif yükseklikleri."
                    },
                    {
                        "icon": "cog",
                        "title": "Düşük Bakım",
                        "description": "Mekanik direksiyon ve modüler tasarım ile hızlı servis."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret depolarında inbound ayrıştırma ve raf besleme"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL operasyonlarında yoğun vardiya içi transfer ve istifleme"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezlerinde raf arası toplama sonrası geri istif"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Gıda depolarında soğuk oda giriş-çıkış işlemleri"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç/kozmetik depolarında hassas palet hareketleri ve düşük sarsıntı"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça hatlarında WIP taşıma ve tampon stok yönetimi"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil ve hazır giyim kolilerinde üst raf istifleri"
                    },
                    {
                        "icon": "industry",
                        "text": "Endüstriyel üretim hücrelerinde paletli yarı mamul akışı"
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
                        "text": "AC sürüş + elektromanyetik fren ile güvenli ve verimli hız yönetimi"
                    },
                    {
                        "icon": "battery-full",
                        "text": "210–230Ah akü seçenekleri ile vardiya planına uyum"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Kompakt şasi sayesinde dar koridor ve rampa yaklaşım kolaylığı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "İki kademeli indirme ile ürün hasar riskinin azaltılması"
                    },
                    {
                        "icon": "star",
                        "text": "Modüler servis erişimi ile daha kısa bakım süreleri"
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
                        "text": "FMCG Dağıtım"
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
                        "text": "Kimya ve Tehlikesiz Kimyasallar"
                    },
                    {
                        "icon": "spray-can",
                        "text": "Kozmetik ve Kişisel Bakım"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik ve Bileşen"
                    },
                    {
                        "icon": "tv",
                        "text": "Beyaz Eşya ve Tüketici Elektroniği"
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
                        "text": "Tarım ve Bahçe Ürünleri"
                    },
                    {
                        "icon": "paw",
                        "text": "Evcil Hayvan Ürünleri"
                    },
                    {
                        "icon": "industry",
                        "text": "Ağır Sanayi Yardımcı Depoları"
                    },
                    {
                        "icon": "building",
                        "text": "İnşaat Malzemeleri Depoları"
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
                        "name": "24V-30A Harici Şarj Cihazı",
                        "description": "Standart, güvenli ve dengeli şarj profili ile akü ömrünü korur.",
                        "is_standard": true,
                        "is_optional": false,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Tandem Yük Tekerleği",
                        "description": "Çift yük tekeri ile rampa ve eşik geçişlerinde darbe azaltma.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "plug",
                        "name": "PU Sürüş Tekerleği (Trace)",
                        "description": "İz bırakmayan sürüş tekerleği ile hijyenik zeminlerde kullanım.",
                        "is_standard": false,
                        "is_optional": true,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "charging-station",
                        "name": "230Ah Yüksek Kapasiteli Akü",
                        "description": "Uzun vardiyalarda daha az şarj molası için geniş kapasite.",
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
                        "question": "Hangi mast yükseklikleri mevcut ve kapalı/açık yükseklikler nasıl değişir?",
                        "answer": "2500–4800 mm aralığında seçenekler bulunur. 3000 mm mast kapalıyken yaklaşık 1970–2030 mm, açıkken 3420–3465 mm seviyesine ulaşır."
                    },
                    {
                        "question": "Yüklü ve yüksüz durumda tipik seyir ve kaldırma hızları nedir?",
                        "answer": "Seyir hızı 5.0/5.5 km/s ve kaldırma hızı modeline göre 0.10–0.13/0.16 m/s aralığındadır. Bu değerler operasyon verimliliğini dengeler."
                    },
                    {
                        "question": "Standart çatal boyutları ve çatallar arası açıklık seçenekleri nelerdir?",
                        "answer": "1150 veya 1220 mm çatal uzunlukları ve 550 ya da 685 mm çatallar arası açıklık seçenekleri mevcuttur."
                    },
                    {
                        "question": "Yük tekerlek konfigürasyonu ve zemin uygunluğu nasıl belirlenir?",
                        "answer": "Poliüretan yük tekerlekleri çift formdadır. Beton ve epoksi zeminlerde sessiz ve düşük yuvarlanma dirençli kullanım sunar."
                    },
                    {
                        "question": "İki kademeli indirme hangi senaryolarda avantaj sağlar?",
                        "answer": "Üst raflarda son yaklaşmada yumuşak bırakma sağlar, ürün hasarını ve operatör düzeltme ihtiyacını azaltır."
                    },
                    {
                        "question": "Akü kapasite seçenekleri ve tipik ağırlıkları nelerdir?",
                        "answer": "24V 210Ah (yaklaşık 190 kg) ve 24V 230Ah (yaklaşık 210 kg) seçenekleri sunulur; vardiya süresine göre seçim yapılır."
                    },
                    {
                        "question": "Sürüş kontrol sistemi yokuş performansını nasıl etkiler?",
                        "answer": "AC kontrol, eğimde torku yönetir; %8 yüklü, %16 yüksüz tırmanma kabiliyeti ile rampa geçişleri stabildir."
                    },
                    {
                        "question": "Direksiyon sistemi ve frenleme bileşenleri bakım açısından nasıldır?",
                        "answer": "Mekanik direksiyon basit ve dayanıklıdır. Elektromanyetik park freni düşük bakım ister ve servis erişimi kolaydır."
                    },
                    {
                        "question": "Koridor genişliği ve dönüş yarıçapı dar alan kullanımını nasıl etkiler?",
                        "answer": "Ast ve Wa değerleri dar raf aralarında tek hamlede konumlanmaya imkân tanır, manevra sayısını azaltır."
                    },
                    {
                        "question": "Opsiyon listesindeki “Trace PU” teker seçeneği ne kazandırır?",
                        "answer": "İz bırakmayan yapı özellikle gıda ve ilaç depolarında hijyen standartlarını destekler, estetik iz oluşumunu engeller."
                    },
                    {
                        "question": "Gürültü seviyesi ve vardiya ergonomisi hangi metriklerle iyileştirilmiştir?",
                        "answer": "74 dB(A) kulak seviyesi, titreşim ve gürültü maruziyetini düşürür; operatör yorgunluğu azalır."
                    },
                    {
                        "question": "Garanti kapsamı ve satış-sonrası destek nasıl sağlanır?",
                        "answer": "Makine 12 ay, batarya 24 ay garantilidir. İXTİF servis, yedek parça ve kiralama için 0216 755 3 555 üzerinden destek verir."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info('✅ Detailed güncellendi: ES14-14WA');
    }
}
