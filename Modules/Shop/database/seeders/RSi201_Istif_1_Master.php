<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSi201_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1;    // İXTİF
        $sku = 'RSi201';
        $titleTr = 'İXTİF RSi201 - 2.0 Ton Çift Katlı Li-Ion İstif Makinesi';

        $shortTr = 'RSi201, iki paleti aynı anda taşıyan çift katlı yapısı, 3 kW kaldırma motoru, 24V/205Ah Li-ion batarya ve 8 km/sa hızıyla yoğun hat besleme ve sipariş toplamada verimi ikiye katlar. Dar alanlarda katlanır platform ve elektrikli direksiyonla çevik manevra sunar.';

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
                'Model' => 'RSi201',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yürüyen/Platformlu',
                'Kapasite (Q)' => '2000 kg',
                'Mast ile yük kapasitesi' => '900 kg',
                'Destek kolu ile yük kapasitesi' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik aksı - yük mesafesi (x)' => '926 mm',
                'Dingil Mesafesi (y)' => '1550 mm',
                'Servis Ağırlığı' => '860 kg',
                'Lastik Tipi' => 'Polyurethane',
                'Ön Lastik Boyutu' => '230×75 mm',
                'Arka Lastik Boyutu' => '85×70 mm',
                'Ek Teker (Caster)' => '130×55 mm',
                'Tekerlek Düzeni (ön/arka)' => '1x,2/4',
                'Ön İz Genişliği' => '514 mm',
                'Arka İz Genişliği' => '385 mm',
                'Maks. Direk Yüksekliği (H)' => '1692 mm',
                'İçeri Toplanmış Direk Yüksekliği (h₁)' => '1316 mm',
                'Kaldırma Yüksekliği (h₃)' => '1600 mm',
                'Direk Açık Yükseklik (h₄)' => '2112 mm',
                'İlk Kaldırma (h₅)' => '120 mm',
                'Tiller Yüksekliği min./max. (h₁₄)' => '1190/1290 mm',
                'İnmiş Çatal Yüksekliği (h₁₅)' => '92 mm',
                'Toplam Uzunluk (l₁)' => '2120 mm',
                'Yük Yüzüne Kadar Uzunluk (l₂)' => '920 mm',
                'Toplam Genişlik (b₁/b₂)' => '734 mm',
                'Çatal Ölçüleri (s×e×l)' => '55×185×1150 mm',
                'Fork Carriage Genişliği' => '570 mm',
                'Zemin Boşluğu (mast altında/orta)' => '16 / 16 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2628 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2516 mm',
                'Dönüş Yarıçapı (Wa)' => '1920 mm',
                'Sürüş Hızı (yüklü/boş)' => '8 / 8 km/sa',
                'Kaldırma Hızı (yüklü/boş)' => '0.18 / 0.23 m/sn',
                'İndirme Hızı (yüklü/boş)' => '0.36 / 0.18 m/sn',
                'Maks. Eğim (yüklü/boş)' => '8% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '2.5 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Batarya (Volt/Ah)' => '24V / 205Ah Li-ion',
                'Batarya Ağırlığı' => '70 kg',
                'Direksiyon' => 'Elektrikli',
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Çift katlı taşıma ile aynı anda iki palet hareketi ve iki kat throughput'],
                ['icon' => 'battery-full', 'text' => '24V/205Ah Li-ion batarya ile fırsat şarjı ve yüksek vardiya süresi'],
                ['icon' => 'bolt', 'text' => '3 kW kaldırma motoru ve oransal kaldırma ile hızlı, hassas yük hakimiyeti'],
                ['icon' => 'arrows-alt', 'text' => 'İlk kaldırma (120 mm) ile rampalarda ve engebeli zeminde sorunsuz geçiş'],
                ['icon' => 'warehouse', 'text' => 'Kompakt şasi ve 1920 mm dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'hand', 'text' => 'Elektrikli direksiyon ve otomatik viraj yavaşlatma ile güvenli sürüş'],
                ['icon' => 'shoe-prints', 'text' => 'Süspansiyonlu, katlanır platform ve yaya modu arasında hızlı geçiş'],
                ['icon' => 'shield-alt', 'text' => 'Şeffaf mast kalkanı ve geniş görüş alanı ile güvenli operasyon']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master eklendi: {$sku}");
    }
}
