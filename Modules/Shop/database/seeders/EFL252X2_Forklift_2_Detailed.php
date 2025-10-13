<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EFL252X2_Forklift_2_Detailed extends Seeder
{
    public function run(): void
    {
        $p = DB::table('shop_products')->where('sku', 'EFL-252X2')->first();
        if (!$p) {
            $this->command->error('❌ Master bulunamadı: EFL-252X2');
            return;
        }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'body' => json_encode(['tr' => '<section><h2>İXTİF EFL252X2 - 2.5 Ton Li-Ion Denge Ağırlıklı Forklift: Basit, Güçlü ve Her Yerde Hazır</h2><p>Depo kapıları açıldığında ilk hareket eden ekipman güven verir. Li-ion çekirdeğiyle anında uyanan bu model, dahili şarj cihazı sayesinde vardiya aralarında prize dokunup fırsat şarjı alır; bakım ve gaz emisyonu derdini ortadan kaldırır. Yüksek yerden açıklık ve büyük lastikler bozuk zeminleri sorun olmaktan çıkarırken, oransal kaldırma sistemi istifte milimetre hassasiyet sağlar. Ayarlanabilir direksiyon ve süspansiyonlu koltuk ise uzun vardiyalarda operatör konforunu üst seviyeye taşır.</p></section><section><h3>Teknik Güç ve Ölçüler</h3><p>2500 kg nominal kapasite ve 500 mm yük merkez mesafesi ile sınıfının çoğu uygulamasını karşılar. 80V 100Ah Li-Ion batarya, 8 kW sürüş ve 16 kW kaldırma motorlarıyla stabil güç çıkışı sağlar. Maksimum hız 11/12 km/h, yükle/boş 0.29/0.36 m/s kaldırma ve 0.4/0.43 m/s indirme hızları, rutin iş akışlarında net verimlilik kazandırır. 15/15 % eğim tırmanma kabiliyeti rampalarda güven verir. Şasi ölçülerinde 2528 mm çatal yüzüne uzunluk, 1154 mm toplam genişlik ve 2258 mm dönüş yarıçapı dar koridorlarda çeviklik sunar. Direk kapalı yüksekliği 2090 mm, kaldırma yüksekliği 3000 mm ve tepe noktası 4025 mm ile raf yapılarına uyumlu çalışır.</p><p>Pnömatik lastikler (Ön 7.00-12-16PR, Arka 18x7-8-14PR), hidrolik direksiyon ve 74 dB(A) altında sürücü kulağı gürültü seviyesi, operasyon alanlarında konforlu ve kontrollü sürüş sağlar. Sürüş kontrolünde PMSM mimarisi, enerji verimliliğini ve tepkiselliği artırır.</p></section><section><h3>Sonuç ve İletişim</h3><p>İçten yanmalı muadillerine ekonomik bir alternatif arayan işletmeler için, sürdürülebilir ve çevik bir çözüm sunar. Basit mimarisi, dahili şarjı ve suya dayanıklı gövdesiyle saha gerçeklerine göre tasarlanmıştır. Daha fazla bilgi ve keşif görüşmesi için bizi arayın: <strong>0216 755 3 555</strong>.</p></section>'], JSON_UNESCAPED_UNICODE),
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
                        "value": "80V 100Ah Li-Ion"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "11/12 km/h"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "2258 mm"
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
                        "title": "80V Li-Ion Enerji",
                        "description": "80V/100Ah paket ile stabil güç ve vardiya arası hızlı şarj"
                    },
                    {
                        "icon": "bolt",
                        "title": "PMSM Tahrik",
                        "description": "Çekiş ve kaldırmada yüksek verim ve ani tepki"
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Suya Dayanıklı Şasi",
                        "description": "İç ve dış mekân uygulamalarında güvenli çalışma"
                    },
                    {
                        "icon": "couch",
                        "title": "Operatör Konforu",
                        "description": "Ayarlanabilir direksiyon ve süspansiyonlu koltuk"
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Oransal Kaldırma",
                        "description": "Hassas istif ve yumuşak hareket kontrolü"
                    },
                    {
                        "icon": "star",
                        "title": "Düşük TCO",
                        "description": "Bakım gerektirmeyen batarya ile toplam sahip olma maliyeti düşer"
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
                        "text": "Yüksek devirli depo içi transfer ve rampa yaklaşma operasyonları"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende dağıtım merkezlerinde raf besleme ve cross-dock"
                    },
                    {
                        "icon": "box-open",
                        "text": "E-ticaret fulfillment hatlarında EUR/ISO palet akışı"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Açık alan ve yağışlı zeminlerde suya dayanıklı şasi ile çalışma"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yedek parça alanlarında ağır kasaların taşınması"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç/kozmetik depolarında hassas ürün hareketi"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya tesislerinde sızdırmaz paletli yük taşımaları"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim hücreleri arasında WIP akışı ve hat besleme"
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
                        "text": "Giriş seviği fiyat/performans: ICE muadillerine ekonomik alternatif"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Li-ion çekirdek ile sıfır emisyon ve düşük bakım maliyeti"
                    },
                    {
                        "icon": "star",
                        "text": "Online satış ve kiralama kanalları için basit ve uygun mimari"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Suya dayanıklı şasi ve yüksek yerden açıklık ile güvenli operasyon"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "Oransal kaldırma sayesinde milimetrik istifleme kontrolü"
                    },
                    {
                        "icon": "trophy",
                        "text": "Enerji tasarrufu %10-15 ve %10’a kadar uzun çalışma süresi"
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
                        "text": "Perakende Zincir Depoları"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "FMCG Dağıtım Ağları"
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
                        "text": "Elektronik/Telekom"
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
                        "text": "Pet Ürünleri ve Yem"
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(
                <<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "Dahili 35A Şarj Cihazı",
                        "description": "80V sistemle uyumlu, fişe tak-şarj kullanım kolaylığı",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "certificate",
                        "name": "LED Ön Aydınlatma",
                        "description": "Enerji verimli LED farlar ile daha iyi görüş",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "cog",
                        "name": "Blue/Area Uyarı Lambaları",
                        "description": "Yaya güvenliği için mavi ve alan uyarı projektörleri",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "wrench",
                        "name": "Yan Kaydırıcı (Sideshifter)",
                        "description": "Dahili veya harici yan kaydırıcı ile hassas konumlama",
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
                        "question": "Gerçek taşıma kapasitesi farklı direk yüksekliklerinde nasıl değişir?",
                        "answer": "Nominal kapasite 500 mm yük merkezinde korunur; yüksek direklerde kapasiteler eğriye göre azalır. Yan kaydırıcı kullanımında tipik 100 kg düşüm uygulanır."
                    },
                    {
                        "question": "Dahili şarj cihazı ile kaç amperde şarj olur ve priz gereksinimi nedir?",
                        "answer": "Entegre şarj cihazı 35A seviyesinde AC girişle çalışır. Standart endüstriyel prizle fırsat şarjına uygundur; harici yüksek akım opsiyonları mevcuttur."
                    },
                    {
                        "question": "PMSM tahrik mimarisi klasik AC sistemlere göre ne kazandırır?",
                        "answer": "Sabit mıknatıslı senkron motor, düşük hız torku ve enerji verimiyle günlük işlerde %10-15 tasarruf ve daha uzun çalışma süresi sağlar."
                    },
                    {
                        "question": "Suya dayanıklı tasarım hangi koşullarda operasyona imkân verir?",
                        "answer": "Dış saha geçişleri, ıslak zeminler ve yağışlı hava gibi senaryolarda elektriksel ve mekanik bileşenler korumalıdır; planlı bakım aralıkları korunmalıdır."
                    },
                    {
                        "question": "Operatör alanı ve ergonomi uzun vardiyalarda konforu nasıl etkiler?",
                        "answer": "Ayarlanabilir direksiyon, geniş diz alanı ve süspansiyonlu koltuk titreşim ve yorgunluğu azaltır; üretkenliği vardiya sonunda dahi yüksek tutar."
                    },
                    {
                        "question": "Yürüme yollarında yaya güvenliği için ne tür uyarı sistemleri önerilir?",
                        "answer": "Mavi ışık ve alan uyarı lambaları yayaları forklift yaklaşımına karşı görsel olarak bilgilendirir; ters koridorlarda görüş artışı sağlar."
                    },
                    {
                        "question": "Bakım planı ve sarf giderleri nasıl şekillenir?",
                        "answer": "Li-ion batarya gaz çıkarmadığı için su tamamlama gerekmez; fren ve tahrik bileşenleri planlı aralıklarla kontrol edilir, genel TCO düşer."
                    },
                    {
                        "question": "Rampalarda performans ve park güvenliği nasıl sağlanır?",
                        "answer": "15% eğim değerine kadar çıkış ve iniş stabil kalır; hidrolik servis ve mekanik park freni güvenli duruş sağlar."
                    },
                    {
                        "question": "Direk opsiyonları ve serbest kaldırma gereksinimi hangi durumlarda tercih edilir?",
                        "answer": "Kapalı kapılardan geçiş, tavan yüksekliği kısıtı veya raf arası çalışma varsa serbest kaldırmalı direkler tercih edilmelidir."
                    },
                    {
                        "question": "Lastik tipleri ve zemin koşulları arasında seçim nasıl yapılmalı?",
                        "answer": "Pürüzlü ve dış saha için pnömatik lastik standarttır; iz bırakmayan zeminler için non-marking seçenekler opsiyoneldir."
                    },
                    {
                        "question": "Telematics ve hız azaltma gibi opsiyonların faydası nedir?",
                        "answer": "Kullanım verisi, dar alan güvenliği ve yasal gerekliliklere uyum için telematics ve yükseltilmiş direkte hız sınırlama yararlıdır."
                    },
                    {
                        "question": "Garanti kapsamı ve servis desteği hakkında nasıl bilgi alırım?",
                        "answer": "Makine 12 ay, Li-Ion batarya 24 ay garantilidir. Satış ve servis için İXTİF destek hattı 0216 755 3 555 üzerinden bilgi alabilirsiniz."
                    }
                ]
JSON,
                true
            ), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
        $this->command->info("ℹ️ Detailed güncellendi: EFL-252X2");
    }
}
