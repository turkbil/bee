<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CPD20TVL_Forklift_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'CPD20TVL')->first();
        DB::table('shop_products')->where('product_id', $p->product_id)->update([
            'long_description' => json_encode(['tr' => '<section><h2>İXTİF CPD20TVL - Li-Ion Forklift</h2><p>Kompakt gövdeli, Li-Ion bataryalı üç tekerlekli elektrikli forklift; dar koridorlarda yüksek manevra ve gün boyu verimlilik için tasarlandı. Kapasite 2000 kg, yük merkezi 500 mm, standart kaldırma yüksekliği 3000 mm.</p></section><section><h3>Teknik</h3><p>AC tahrik ve hassas kontrol ile yük kaldırma ve indirme hareketleri yumuşaktır. Enerji yönetim sistemi akü sağlığını korurken, rejeneratif frenleme menzili uzatır. Operatör bölgesi, titreşimi azaltan yerleşim ve ergonomik kumandalarla uzun vardiyalarda konfor sağlar.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "weight-hanging",
                        "label": "Kapasite",
                        "value": "2000 kg"
                    },
                    {
                        "icon": "battery-full",
                        "label": "Batarya",
                        "value": "48 V / 360 Ah"
                    },
                    {
                        "icon": "gauge",
                        "label": "Hız",
                        "value": "12 km/h"
                    },
                    {
                        "icon": "arrows-turn-right",
                        "label": "Dönüş",
                        "value": "1500 mm"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "title": "Hızlı Şarj",
                        "description": "Li-Ion akü yapısı sayesinde kısa molalarda dahi dolum yapılabilir."
                    },
                    {
                        "icon": "bolt",
                        "title": "Güçlü Sürüş",
                        "description": "AC tahrik, zor rampalarda yüksek tork sunar."
                    },
                    {
                        "icon": "shield-alt",
                        "title": "Güvenlik",
                        "description": "Standart hız sınırlama ve acil durdurma ile güvenli kullanım."
                    },
                    {
                        "icon": "microchip",
                        "title": "Akıllı Kontrol",
                        "description": "Kabin göstergesinde enerji ve hata kodları net şekilde gösterilir."
                    },
                    {
                        "icon": "arrows-alt",
                        "title": "Kompakt Tasarım",
                        "description": "Dar alanlarda palet işleminde üstün çeviklik."
                    },
                    {
                        "icon": "briefcase",
                        "title": "Bakım Kolaylığı",
                        "description": "Modüler yapı ile servis süreleri kısalır."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "Paletli ürünlerin istif ve taşıması"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende arka depo yükleme/boşaltma"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL depo toplama ve raf besleme"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv yan sanayi hat besleme"
                    },
                    {
                        "icon": "industry",
                        "text": "Üretim içi malzeme akışı"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "E-ticaret sipariş hazırlama"
                    },
                    {
                        "icon": "building",
                        "text": "Soğuk oda giriş-çıkış operasyonları"
                    },
                    {
                        "icon": "star",
                        "text": "Kısa mesafe yük transferi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "bolt",
                        "text": "Li-Ion ile akü değişimi olmadan 24/7 operasyon"
                    },
                    {
                        "icon": "star",
                        "text": "Kompakt gövdeyle rakiplerine göre daha küçük dönüş yarıçapı"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Gelişmiş güvenlik fonksiyonları standart"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Düşük toplam sahip olma maliyeti"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Enerji geri kazanımı ile daha uzun sürüş süresi"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "box-open",
                        "text": "E-ticaret"
                    },
                    {
                        "icon": "warehouse",
                        "text": "3PL"
                    },
                    {
                        "icon": "store",
                        "text": "Perakende"
                    },
                    {
                        "icon": "snowflake",
                        "text": "Gıda"
                    },
                    {
                        "icon": "pills",
                        "text": "İlaç"
                    },
                    {
                        "icon": "car",
                        "text": "Otomotiv"
                    },
                    {
                        "icon": "tshirt",
                        "text": "Tekstil"
                    },
                    {
                        "icon": "industry",
                        "text": "Sanayi"
                    },
                    {
                        "icon": "flask",
                        "text": "Kimya"
                    },
                    {
                        "icon": "microchip",
                        "text": "Elektronik"
                    },
                    {
                        "icon": "building",
                        "text": "Yapı Market"
                    },
                    {
                        "icon": "cart-shopping",
                        "text": "Dağıtım"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Lojistik"
                    },
                    {
                        "icon": "star",
                        "text": "Hızlı Tüketim"
                    },
                    {
                        "icon": "bolt",
                        "text": "Enerji Depolama"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Savunma Tedarik"
                    },
                    {
                        "icon": "award",
                        "text": "Ambalaj"
                    },
                    {
                        "icon": "battery-full",
                        "text": "Beyaz Eşya"
                    },
                    {
                        "icon": "cog",
                        "text": "Makine İmalat"
                    },
                    {
                        "icon": "plug",
                        "text": "Telekom Depolama"
                    },
                    {
                        "icon": "certificate",
                        "text": "Kamu Depoları"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(json_decode(<<<'JSON'
                {
                    "coverage": "Makine 12 ay, Li-Ion batarya 24 ay garanti.",
                    "duration_months": 12,
                    "battery_warranty_months": 24
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "plug",
                        "name": "Harici Şarj Cihazı",
                        "description": "Hızlı dolum için endüstriyel şarj ünitesi",
                        "is_standard": true,
                        "price": null
                    },
                    {
                        "icon": "battery-full",
                        "name": "Yedek Li-Ion Akü",
                        "description": "Yüksek kapasiteli yedek batarya",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "arrows-alt",
                        "name": "Yan Kaydırma Ataçmanı",
                        "description": "Dar raf aralarında hassas konumlama",
                        "is_standard": false,
                        "price": "Talep üzerine"
                    },
                    {
                        "icon": "cog",
                        "name": "Kabinsiz/Emniyet Tavanı Seçenekleri",
                        "description": "Uygulama ihtiyacına göre yapılandırma",
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
                        "question": "Şarj süresi nedir?",
                        "answer": "Li-Ion teknoloji ile ara şarj mümkündür; tam şarj süresi şarj cihazına göre değişir."
                    },
                    {
                        "question": "Rampa performansı nasıldır?",
                        "answer": "AC tahrik ve denge merkezi ile standart rampalarda stabil çekiş sunar."
                    },
                    {
                        "question": "Bakım aralıkları?",
                        "answer": "Elektrikli yapı sayesinde yağ ve filtre bakımları yoktur; periyodik kontroller yeterlidir."
                    },
                    {
                        "question": "İç/dış mekân kullanımı?",
                        "answer": "Düz zeminli iç mekân için idealdir; dış mekânda kuru ve düzgün zeminde kullanılabilir."
                    },
                    {
                        "question": "Güvenlik özellikleri?",
                        "answer": "Acil durdurma, hız sınırlama ve eğimde geri kaymayı önleme standarttır."
                    },
                    {
                        "question": "Şarj altyapısı gerekli mi?",
                        "answer": "Endüstriyel priz ile harici şarj cihazı yeterlidir."
                    },
                    {
                        "question": "Garanti kapsamı?",
                        "answer": "Makine 12 ay, Li-Ion batarya 24 ay garanti altındadır."
                    },
                    {
                        "question": "Yedek parça bulunurluğu?",
                        "answer": "Kritik sarf malzemeler stoktan temin edilir."
                    },
                    {
                        "question": "Operatör eğitimi gerekir mi?",
                        "answer": "Teslimat sonrası temel kullanım ve güvenlik eğitimi önerilir."
                    },
                    {
                        "question": "Teslim süresi?",
                        "answer": "Stok durumuna ve özel opsiyonlara bağlıdır."
                    },
                    {
                        "question": "Garanti?",
                        "answer": "Makine 12 ay, akü 24 ay. İXTİF 0216 755 3 555."
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
}
