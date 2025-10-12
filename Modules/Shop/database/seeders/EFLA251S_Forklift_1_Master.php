<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFLA251S_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFLA251S';
        $titleTr = 'İXTİF EFLA251S - 2.5 Ton Li-Ion Cushion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'İç mekanlar için yastık lastikli kompakt elektrikli forklift. 1485 mm dingil açıklığı ve 1092 mm genişlik sayesinde dar koridorlarda çevik manevra; 17 km/s hız, 0.61/0.64 m/s kaldırma/indirme ve 80V Li-Ion (230Ah, opsiyon 460Ah) ile yüksek verim.'], JSON_UNESCAPED_UNICODE),
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
            'technical_specs' => json_encode([
                'Sürüş' => 'Elektrik',
                'Kapasite' => '2500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Servis Ağırlığı' => '4510 kg',
                'Dingil Mesafesi (y)' => '1485 mm',
                'Tekerlek Türü' => 'Katı (Cushion)',
                'Ön Tekerlek' => '21×7-15',
                'Arka Tekerlek' => '16×6-10.5',
                'Ön/Arka İz Genişliği (b10/b11)' => '914 / 900 mm',
                'Direk Eğim (α/β)' => '6° / 5°',
                'Direk Kapalı Yükseklik (h1)' => '2110 mm',
                'Serbest Kaldırma (h2)' => '1468 mm',
                'Kaldırma Yüksekliği (h3)' => '4800 mm',
                'Direk Açık Yükseklik (h4)' => '5838 mm',
                'Üst Koruma Yüksekliği (h6)' => '2105 mm',
                'Koltuk Yüksekliği (h7)' => '1105 mm',
                'Çeki Kancası Yüksekliği (h10)' => '280 mm',
                'Toplam Uzunluk (l1)' => '3399 mm',
                'Forka Kadar Uzunluk (l2)' => '2329 mm',
                'Toplam Genişlik (b1/b2)' => '1092 mm',
                'Çatal Ölçüleri (s/e/l)' => '40 / 122 / 1070 mm',
                'Fork Taşıyıcı Genişliği (b3)' => '1040 mm',
                'Yerden Yükseklik (m1/m2)' => '95 / 110 mm',
                'Koridor Genişliği (Ast 1000×1200 / 800×1200)' => '3664 / 3864 mm',
                'Dönüş Yarıçapı (Wa)' => '1990 mm',
                'Sürüş Hızı (yüklü/boş)' => '17 / 17 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.61 / 0.64 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.5 / 0.5 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '30% / 17%',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60 dk)' => '15 kW',
                'Kaldırma Motoru (S3 15%)' => '26 kW',
                'Batarya' => '80V / 230Ah (opsiyon 80V / 460Ah)',
                'Batarya Ağırlığı' => '295 kg',
                'Sürücü Kontrolü' => 'AC',
                'Direksiyon' => 'Hidrolik',
                'Sürücü Gürültü Seviyesi' => '68 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'compress', 'text' => '1485 mm kısa dingil mesafesi ve 1092 mm genişlikle kompakt şasi'],
                ['icon' => 'arrows-alt', 'text' => '1990 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'battery-full', 'text' => '80V Li-Ion batarya: 230Ah standart, 460Ah opsiyon'],
                ['icon' => 'bolt', 'text' => 'Optimize hidrolik valf ile hızlı kaldırma/indirme'],
                ['icon' => 'weight-hanging', 'text' => '6.55 m’de 1000 kg residual kapasite ile yüksek raf kullanımı'],
                ['icon' => 'shield-alt', 'text' => 'Standart hız kontrolü ve opsiyonel mast buffer ile güvenlik'],
                ['icon' => 'eye', 'text' => 'Geliştirilmiş görüş ve süspansiyon koltuk ile konforlu sürüş'],
                ['icon' => 'cog', 'text' => 'Cıvatasız ön zemin ve sökülebilir paspas ile kolay bakım']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
