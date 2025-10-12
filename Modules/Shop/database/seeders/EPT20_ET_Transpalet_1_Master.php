<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_ET_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EPT20-ET';
        $titleTr = 'İXTİF EPT20 ET - 2.0 Ton Akülü Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '48V elektronik sistem ve bakım gerektirmeyen DC fırçasız mıknatıslı motorla güçlenen İXTİF EPT20 ET, 2.0 ton kapasite, 80 mm alçaltılmış yükseklik ve 140 mm kaldırma ile dar alanlarda çevik, dış sahada engel aşmada güvenli performans sunar.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2,
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
                'Üretici' => 'İXTİF',
                'Model' => 'EPT20-ET',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Sürüş Aksına Mesafe (x)' => '874/962 mm',
                'Dingil Mesafesi (y)' => '1248/1336 mm',
                'Servis Ağırlığı' => '220 kg',
                'Lastik Tipi' => 'Poliüretan (PU)',
                'Tahrik Tekerleği (ön) ölçüsü' => 'Ø230×74 mm',
                'Yük Tekerleği (arka) ölçüsü' => '2× Ø78×60 mm',
                'Kaster Tekerlek' => 'Ø74×30 mm',
                'Tekerlek adedi (x=tahrik)' => '1x + (2/4) yük',
                'Ön İz Genişliği (b10)' => '435 mm',
                'Arka İz Genişliği (b11)' => '410 / 535 mm',
                'Kaldırma Yüksekliği (h3)' => '140 mm',
                'Sürüş Kolu Yüksekliği min./maks. (h14)' => '750 / 1170 mm',
                'Alçaltılmış Yükseklik (h13)' => '80 mm',
                'Toplam Uzunluk (l1)' => '1685 mm',
                'Çatala Kadar Uzunluk (l2)' => '535 mm',
                'Toplam Genişlik (b1/b2)' => '560 / 685 mm',
                'Çatal Ölçüleri (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal Kolları Arası Mesafe (b5)' => '560 / 685 mm',
                'Şasi Orta Noktası Yerden Yükseklik (m2)' => '30 mm',
                'Koridor Genişliği Ast 1000×1200 enine' => '2304 mm',
                'Koridor Genişliği Ast 800×1200 enine' => '2371 mm',
                'Dönüş Yarıçapı (Wa)' => '1550 mm',
                'Yürüyüş Hızı yüklü/ yüksüz' => '4 / 5.5 km/s',
                'Kaldırma Hızı yüklü/ yüksüz' => '0.018 / 0.037 m/s',
                'İndirme Hızı yüklü/ yüksüz' => '0.032 / 0.038 m/s',
                'Azami Eğimi yüklü/ yüksüz' => '8% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.84 kW',
                'Akü (volt/kapasite)' => '48V (12V×4) / 30Ah AGM',
                'Akü Ağırlığı' => '9.5 kg ×4',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses Basınç Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Silindir yapılı şasi ile ağırlığın alt şasiye dağıtıldığı yenilikçi gövde'],
                ['icon' => 'arrows-alt', 'text' => '100 mm engel aşımı için döner yük tekeri ve büyük tahrik tekeri ile yüksek geçiş kabiliyeti'],
                ['icon' => 'arrows-left-right-to-line', 'text' => '80 mm alçaltılmış, 140 mm kaldırma yüksekliği ile esnek palet uyumu'],
                ['icon' => 'circle-notch', 'text' => 'Bağlantı sistemi ile darbe/bozuk zeminde sürüş farkını azaltan şok emici şasi'],
                ['icon' => 'plug', 'text' => 'Pimli sökülebilir tiller: sevkiyat ve montaj süresini ciddi azaltır'],
                ['icon' => 'battery-full', 'text' => '48V elektronik sistem: verimlilik ve güvenilirlik artışı'],
                ['icon' => 'cog', 'text' => 'Fırçasız kalıcı mıknatıslı DC motor: ömür boyu bakım gerektirmez'],
                ['icon' => 'toolbox', 'text' => 'Tek dokunuş kilit ile tüm elektronik ve akülere erişim, tek somunla teker değişimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info('✅ Master oluşturuldu: ' . $sku);
    }
}
