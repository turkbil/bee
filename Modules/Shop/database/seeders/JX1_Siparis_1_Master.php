<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX1_Siparis_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 4; // Sipariş Toplama
        $brandId = 1; // İXTİF
        $sku = 'JX1';
        $titleTr = 'İXTİF JX1 - Task Support Vehicle (192”a kadar operatör yükseltme)';
        $shortTr = 'İXTİF JX1, 180° eklemli tahrik hattı ile sıfır dönüş kabiliyeti, iç mekân kullanımına uygun kompakt şasi ve geniş çalışma platformunu birleştirir. 24V güç mimarisi, AGM/kurşun asit/Li-ion seçenekleri ve 3.4 mph standart (5 mph opsiyon) hız ile gün boyu güvenli toplama.';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
            'base_price' => 0.00,
            'price_on_request' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'is_master_product' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),

            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'JX1',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Sipariş toplayıcı / Task support vehicle',
                'Temel Kapasite (Q1)' => '300 lb (operatör bölmesi)',
                'Ön Platform Kapasitesi (Q2)' => '500 lb (ön çalışma platformu)',
                'Arka Tepsi Kapasitesi (Q3)' => '200 lb (arka tepsi)',
                'Toplam Yük (≥126” üzerinde)' => '450 lb (operatör + tepsi) @ >126” | 400 lb bazı konfigürasyonlarda',
                'Teker Tipi' => 'Poli tahrik tekeri, poli döner kasnak; arka lastik teker',
                'Teker Ölçüleri' => 'Ön 9”x3”, Arka 8”x3”, Ek teker 3”x2”',
                'Dingil Aralığı' => '45.2” / 45.2” / 49” (yükseklik varyantlarına göre)',
                'Servis Ağırlığı' => '2617 / 2992 / 3696 lb',
                'Geriye Katlı Direk Yüksekliği (h6)' => '62.6” / 82.75” / 83.25”',
                'Kaldırma Yüksekliği (Platform/Tepsi)' => '126”/159” | 162”/195” | 192”/225”',
                'Mast Tam Yükselmiş (h4)' => '178.6” / 235” / 265”',
                'Toplam Uzunluk' => '59” / 59” / 63.25”',
                'Toplam Genişlik' => '31.5” / 31.5” / 34”',
                'Dönüş Yarıçapı' => '52.5” / 52.5” / 56”',
                'Sürüş Hızı (yüklü/boş)' => '3.4 mph standart, 5 mph opsiyon',
                'Kaldırma Hızı (yüklü/boş)' => '33.5 / 41.3 fpm',
                'İndirme Hızı (yüklü/boş)' => '68.9 / 51.2 fpm',
                'Maks. Eğim' => '0% (yalnızca düz/ düzlemsel iç zemin)',
                'Servis Freni' => 'Rejeneratif, elektromanyetik',
                'Sürüş Motoru' => '1.7 kW (S2 60 dk)',
                'Kaldırma Motoru' => '2.2 kW (S3 %15)',
                'Akü Seçenekleri' => '24V/224Ah AGM | 24V/340Ah Kurşun Asit | 24V/205Ah Li-ion',
                'Akü Ağırlığı' => '345 lb (AGM/LA) / 660 lb (bazı konfigürasyonlar)',
                'Kullanım' => 'Sadece iç mekân, düzgün ve düz zemin',
                'Öne Çıkan Tasarım' => '180° eklemli tahrik hattı ile sıfır dönüş',
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => '180° eklemli tahrik ile sıfır dönüş manevrası dar koridorlarda çeviklik sağlar'],
                ['icon' => 'battery-full', 'text' => '24V mimaride AGM, kurşun asit veya Li‑ion akü seçenekleri ile esnek enerji'],
                ['icon' => 'shield-alt', 'text' => 'Rejeneratif elektromanyetik fren ile kontrollü yavaşlama ve güvenli duruş'],
                ['icon' => 'star', 'text' => 'Sezgisel sürüş ve kumanda düzeni operatör ergonomisini artırır'],
                ['icon' => 'industry', 'text' => 'Kompakt şasi ve geniş platform kombinasyonu üretkenliği yükseltir'],
                ['icon' => 'briefcase', 'text' => 'Operatör bölmesinde anti‑yorgunluk mat ile uzun vardiyada konfor'],
                ['icon' => 'bolt', 'text' => '2.2 kW kaldırma ve 1.7 kW sürüş motoru ile dengeli performans'],
                ['icon' => 'cart-shopping', 'text' => 'Toplama, taşıma ve itme/çekme görevlerini tek araçta birleştirir']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
