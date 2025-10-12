<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252X5_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFL252X5';
        $titleTr = 'İXTİF EFL252X5 - 2.5 Ton Li-Ion Elektrikli Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'İXTİF EFL252X5, 2.5 ton kapasiteli, 80V Li‑ion modüler batarya mimarisine sahip elektrikli karşı denge forkliftidir. 16/17 km/s hız, 17/25% tırmanma ve 150 mm yerden yükseklikle iç‑dış saha operasyonlarında yüksek verim ve kesintisiz çalışma sunar.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(json_decode(<<<'JSON'
                {
                    "Model": "EFL252X5",
                    "Sürüş": "Elektrikli (AC sürüş kontrol)",
                    "Kapasite (Q)": "2500 kg",
                    "Yük Merkezi (c)": "500 mm",
                    "Servis Ağırlığı": "3835 kg",
                    "Dingil Yükü (yükle)": "5679 / 656 kg (ön/arka)",
                    "Dingil Yükü (yüksüz)": "1711 / 2124 kg (ön/arka)",
                    "Teker Tipi": "Pnömatik",
                    "Ön Teker": "7.00-12-12PR",
                    "Arka Teker": "18X7-8-14PR",
                    "Teker Sayısı (x=tahrik)": "2x / 2",
                    "Ön İz Genişliği": "975 mm",
                    "Arka İz Genişliği": "955 mm",
                    "Direk Eğim (ileri/geri)": "6° / 10°",
                    "Direk Kapalı Yükseklik (h1)": "2090 mm",
                    "Serbest Kaldırma (h2)": "195 mm",
                    "Kaldırma Yüksekliği (h3)": "3000 mm",
                    "Direk Açık Yükseklik (h4)": "4065 mm",
                    "Kabin Yüksekliği": "2165 mm",
                    "Oturma Yüksekliği": "1095 mm",
                    "Kanca Yüksekliği": "315 mm",
                    "Toplam Uzunluk": "3590 mm",
                    "Çatala Kadar Uzunluk (l2)": "2520 mm",
                    "Toplam Genişlik": "1154 mm",
                    "Çatal Ölçüleri (s×e×l)": "40×122×1070 mm",
                    "Ataşman Sınıfı": "2A",
                    "Çatal Taşıyıcı Genişliği": "1040 mm",
                    "Yerden Yükseklik (orta)": "150 mm",
                    "Rampa Altı Boşluk (mast altında)": "100 mm (yükle)",
                    "Koridor Genişliği 1000×1200 enine (Ast)": "3965 mm",
                    "Koridor Genişliği 800×1200 boyuna (Ast)": "4165 mm",
                    "Dönüş Yarıçapı (Wa)": "2270 mm",
                    "Sürüş Hızı (yükle/yüksüz)": "16 / 17 km/s",
                    "Kaldırma Hızı (yükle/yüksüz)": "0.38 / 0.45 m/s",
                    "İndirme Hızı (yükle/yüksüz)": "0.45 / 0.52 m/s",
                    "Tırmanma Kabiliyeti (yükle/yüksüz)": "17% / 25%",
                    "Sürüş Motoru (S2 60dk)": "16 kW",
                    "Kaldırma Motoru (S3 15%)": "24 kW",
                    "Batarya": "80V 280Ah (modüler, 1 modül)",
                    "Opsiyonel Batarya": "80V 560Ah (2 modül, çift)",
                    "Fren": "Hidrolik (park freni mekanik)",
                    "Zemin Boşluğu": "150 mm",
                    "Gürültü Seviyesi": "≤74 dB(A)"
                }
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'features' => json_encode(json_decode(<<<'JSON'
                [
                    {
                        "icon": "battery-full",
                        "text": "Modüler Li-ion batarya tasarımı (80V 280Ah, çift modülle 560Ah)"
                    },
                    {
                        "icon": "bolt",
                        "text": "Yüksek hız ve güçlü tırmanma ile verimlilik artışı"
                    },
                    {
                        "icon": "arrows-alt",
                        "text": "150 mm yerden yükseklik ve büyük lastikler ile zorlu zeminler"
                    },
                    {
                        "icon": "star",
                        "text": "Yeni nesil triplex direk yüksek görüş ile güvenli operasyon"
                    },
                    {
                        "icon": "briefcase",
                        "text": "Geniş LED ekran ve ergonomik koltuk ile konfor"
                    },
                    {
                        "icon": "shield-alt",
                        "text": "Güvenli hidrolik fren ve mekanik park freni"
                    },
                    {
                        "icon": "cog",
                        "text": "Sıfır bakım ve fırsat şarjı ile düşük işletme maliyeti"
                    },
                    {
                        "icon": "award",
                        "text": "İyileştirilmiş farlar, pedal ve kumandalar"
                    }
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}