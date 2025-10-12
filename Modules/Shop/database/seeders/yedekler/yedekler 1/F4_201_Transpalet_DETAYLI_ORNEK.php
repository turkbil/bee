<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * DETAYLI TECHNICAL SPECS ÖRNEĞİ
 *
 * Bu dosya AI'a gösterilecek - Teknik özelliklerin ne kadar detaylı olması gerektiğini gösterir
 *
 * Gereksinimler:
 * - 10-15 section
 * - Her section'da 3-10 property
 * - Toplam 50-80 teknik özellik
 * - PDF'deki TÜM bilgiler dahil
 */
class F4_201_Transpalet_DETAYLI_ORNEK extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $brandId = DB::table('shop_brands')->where('slug->tr', 'ixtif')->value('brand_id');
        $categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');

        DB::table('shop_products')->where('sku', 'LIKE', 'F4-201%')->delete();

        $productId = DB::table('shop_products')->insertGetId([
            'sku' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,

            'title' => json_encode(['tr' => 'F4 201 Li-Ion Akülü Transpalet 2.0 Ton'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-201-2-ton-48v-li-ion-transpalet'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '48V Li-Ion güç platformu ile 2 ton taşıma kapasitesi sunan F4 201, tak-çıkar batarya sistemi ve 140 kg ultra hafif gövdesiyle dar koridor operasyonlarında yeni standartlar belirler.'], JSON_UNESCAPED_UNICODE),

            // DETAYLI TECHNICAL SPECS - 12 SECTION, 60+ PROPERTY
            'technical_specs' => json_encode([

                // 1. GENERATİON / GENEL BİLGİLER
                'generation' => [
                    '_title' => 'Genel Bilgiler',
                    '_icon' => 'info-circle',
                    'Üretici' => 'EP Equipment Co., Ltd.',
                    'Model Serisi' => 'F4 Series',
                    'Model Kodu' => 'F4 201',
                    'Ürün Yılı' => '2024',
                    'Sertifikasyon' => 'CE, ISO 9001, IP54',
                    'Garanti Süresi' => '24 ay fabrika garantisi',
                    'Kullanım Alanı' => 'Kapalı mekan, düz ve hafif eğimli yüzeyler',
                ],

                // 2. CAPACITY / KAPASİTE
                'capacity' => [
                    '_title' => 'Kapasite ve Ağırlıklar',
                    '_icon' => 'weight-hanging',
                    'Yük Kapasitesi' => '2000 kg',
                    'Yük Merkez Mesafesi' => '600 mm (yük merkezinden)',
                    'Servis Ağırlığı (Batarya Dahil)' => '140 kg',
                    'Servis Ağırlığı (Batarya Hariç)' => '120 kg',
                    'Aks Yükü (Yüklü - Ön)' => '1850 kg',
                    'Aks Yükü (Yüklü - Arka)' => '290 kg',
                    'Aks Yükü (Boş - Ön)' => '50 kg',
                    'Aks Yükü (Boş - Arka)' => '90 kg',
                ],

                // 3. DIMENSIONS / BOYUTLAR
                'dimensions' => [
                    '_title' => 'Boyutlar ve Ölçüler',
                    '_icon' => 'ruler-combined',
                    'Toplam Uzunluk' => '1550 mm',
                    'Toplam Genişlik' => '590 mm',
                    'Şasi Yüksekliği (En Düşük Nokta)' => '85 mm',
                    'Tutamaç Yüksekliği' => '1200 mm',
                    'Çatal Kalınlığı' => '50 mm',
                    'Çatal Genişliği' => '150 mm',
                    'Çatal Uzunluğu (Standart)' => '1150 mm',
                    'Çatal Aralığı (Min)' => '150 mm',
                    'Çatal Aralığı (Max)' => '540 mm',
                    'Zemin Açıklığı (Merkez)' => '35 mm',
                    'Zemin Açıklığı (Yük Tekerleği)' => '30 mm',
                    'Dönüş Yarıçapı' => '1360 mm',
                    'Koridor Genişliği (1000x1200 Palet)' => '2160 mm',
                ],

                // 4. LIFTING / KALDIRMA
                'lifting' => [
                    '_title' => 'Kaldırma Sistemi',
                    '_icon' => 'arrow-up',
                    'Kaldırma Yüksekliği' => '105 mm',
                    'Kaldırma Hızı (Yüklü)' => '6 mm/s',
                    'Kaldırma Hızı (Boş)' => '8 mm/s',
                    'İniş Hızı (Yüklü)' => '12 mm/s',
                    'İniş Hızı (Boş)' => '15 mm/s',
                    'Kaldırma Motoru Gücü' => '0.7 kW (BLDC)',
                    'Hidrolik Sistem' => 'Elektro-hidrolik',
                    'Hidrolik Yağ Kapasitesi' => '0.8 L',
                    'Acil İniş Valfi' => 'Manuel mekanik valf',
                ],

                // 5. ELECTRICAL SYSTEM / ELEKTRİK SİSTEMİ
                'electrical' => [
                    '_title' => 'Elektrik Sistemi',
                    '_icon' => 'battery-full',
                    'Voltaj' => '48V DC',
                    'Batarya Tipi' => 'Li-Ion (Lityum İyon)',
                    'Batarya Kapasitesi (Standart)' => '2x 24V/20Ah çıkarılabilir modül',
                    'Batarya Kapasitesi (Maksimum)' => '4x 24V/20Ah (80Ah toplam)',
                    'Batarya Ağırlığı (Modül Başına)' => '5.5 kg',
                    'Şarj Süresi (Standart Şarj)' => '4-5 saat (2x 24V-5A)',
                    'Şarj Süresi (Hızlı Şarj)' => '2-3 saat (2x 24V-10A)',
                    'Şarj Voltajı' => '29.4V per modül',
                    'Şarj Akımı (Standart)' => '5A per modül',
                    'Şarj Akımı (Hızlı)' => '10A per modül',
                    'Batarya Yönetim Sistemi (BMS)' => 'Akıllı BMS (aşırı şarj/deşarj koruması)',
                    'Çalışma Süresi (2 modül)' => '6-8 saat (orta yoğunlukta kullanım)',
                    'Çalışma Süresi (4 modül)' => '12-16 saat',
                    'Kontrol Sistemi' => 'Mikroişlemci kontrollü',
                ],

                // 6. DRIVE MOTOR / SÜRÜŞ MOTORU
                'drive_motor' => [
                    '_title' => 'Sürüş Motoru',
                    '_icon' => 'gears',
                    'Motor Tipi' => 'BLDC (Brushless DC Motor)',
                    'Motor Gücü' => '0.9 kW',
                    'Motor Voltajı' => '48V DC',
                    'Maksimum Tork' => '12 Nm',
                    'Devir Sayısı' => '1500-3000 rpm',
                    'Verimlilik' => '%85-92',
                    'Soğutma Sistemi' => 'Doğal hava soğutmalı',
                    'Koruma Sınıfı' => 'IP54',
                ],

                // 7. PERFORMANCE / PERFORMANS
                'performance' => [
                    '_title' => 'Performans Verileri',
                    '_icon' => 'gauge-high',
                    'Sürüş Hızı (Yüklü)' => '4.5 km/h',
                    'Sürüş Hızı (Boş)' => '5.0 km/h',
                    'Kaldırma Hızı (Yüklü)' => '6 mm/s',
                    'Kaldırma Hızı (Boş)' => '8 mm/s',
                    'Rampa Tırmanma Kapasitesi (Yüklü)' => '%8 (yaklaşık 4.6°)',
                    'Rampa Tırmanma Kapasitesi (Boş)' => '%16 (yaklaşık 9.1°)',
                    'Hızlanma (0-4 km/h)' => '2.5 saniye',
                    'Frenleme Mesafesi (4 km/h)' => '0.8 m',
                    'Gürültü Seviyesi (Çalışırken)' => '<68 dB(A)',
                    'Gürültü Seviyesi (Durgun)' => '<40 dB(A)',
                ],

                // 8. TYRES / TEKERLEKLER
                'tyres' => [
                    '_title' => 'Tekerlekler',
                    '_icon' => 'circle-dot',
                    'Tekerlek Tipi' => 'Poliüretan (PU)',
                    'Sürüş Tekerleği' => '210 × 70 mm (Tek tekerlek)',
                    'Yük Tekerleği' => '80 × 60 mm (Çift sıra, 4 adet)',
                    'Tekerlek Malzemesi' => 'Yüksek dayanımlı poliüretan',
                    'Tekerlek Sertliği' => '92 Shore A',
                    'Tekerlek Ömrü' => '~5000 saat (normal kullanım)',
                    'Rulman Tipi' => 'Kapalı rulman (bakım gerektirmez)',
                    'Yük Tekerleği Koruma' => 'Çift sıra tasarım (yük dağılımı)',
                ],

                // 9. BRAKE SYSTEM / FREN SİSTEMİ
                'brake_system' => [
                    '_title' => 'Fren Sistemi',
                    '_icon' => 'hand',
                    'Fren Tipi' => 'Elektromanyetik fren',
                    'Park Freni' => 'Otomatik devreye girer (acil durum butonu)',
                    'Yavaşlama Freni' => 'Ters akım frenleme (regenerative)',
                    'Acil Durdurma' => 'Acil stop butonu (kırmızı)',
                    'Fren Yanıt Süresi' => '<0.3 saniye',
                    'Fren Kuvveti' => 'Ayarlanabilir (3 seviye)',
                ],

                // 10. CONTROL SYSTEM / KONTROL SİSTEMİ
                'control_system' => [
                    '_title' => 'Kontrol Sistemi',
                    '_icon' => 'sliders',
                    'Kontrol Tipi' => 'Mikroişlemci kontrollü',
                    'Hız Kontrolü' => 'Kademesiz (analog)',
                    'Yön Kontrolü' => 'İleri/Geri joystick',
                    'Kaldırma Kontrolü' => 'Aşağı/Yukarı butonlar',
                    'Acil Durdurma' => 'Kırmızı acil stop butonu',
                    'Horn (Korna)' => 'Elektrikli horn butonu',
                    'Batarya Göstergesi' => 'LED gösterge (5 seviye)',
                    'Hata Göstergesi' => 'Dijital LED (hata kodları)',
                    'Anahtar Kilidi' => '2 pozisyonlu anahtar',
                    'Sürüş Modu' => 'Ekonomik / Normal / Performans',
                ],

                // 11. SAFETY FEATURES / GÜVENLİK ÖZELLİKLERİ
                'safety_features' => [
                    '_title' => 'Güvenlik Özellikleri',
                    '_icon' => 'shield-halved',
                    'Acil Durdurma Butonu' => 'Kırmızı e-stop butonu (tutamakta)',
                    'Aşırı Yük Koruması' => 'Otomatik motor kesme (>2200 kg)',
                    'Aşırı Sıcaklık Koruması' => 'Motor ve batarya sıcaklık sensörü',
                    'Düşük Batarya Uyarısı' => 'Sesli ve görsel uyarı',
                    'Anti-Rollback Sistemi' => 'Rampalarda geri kayma önleyici',
                    'Çarpışma Önleme' => 'Yumuşak başlangıç/duruş (soft start/stop)',
                    'Horn (Korna)' => 'Elektrikli uyarı korna',
                    'Stabilizasyon Tekeri (Opsiyonel)' => 'Bozuk zeminlerde devrilme önleme',
                ],

                // 12. ENVIRONMENT / ÇALIŞMA ORTAMI
                'environment' => [
                    '_title' => 'Çalışma Ortamı',
                    '_icon' => 'temperature-half',
                    'Çalışma Sıcaklığı' => '-10°C ile +45°C arası',
                    'Saklama Sıcaklığı' => '-20°C ile +60°C arası',
                    'Nem Oranı' => '%5-95 (yoğunlaşmasız)',
                    'Kullanım Alanı' => 'Kapalı mekan (kuru ortam)',
                    'Zemin Tipi' => 'Düz beton, asfalt, endüstriyel zemin',
                    'Maksimum Zemin Eğimi' => '%8 (yüklü), %16 (boş)',
                    'Gürültü Seviyesi' => '<68 dB(A)',
                    'Koruma Sınıfı' => 'IP54 (toz ve su sıçramalarına karşı korumalı)',
                ],

                // 13. MAINTENANCE / BAKIM
                'maintenance' => [
                    '_title' => 'Bakım Gereksinimleri',
                    '_icon' => 'wrench',
                    'Bakım Sıklığı' => 'İlk 50 saat sonra, sonra her 200 saat',
                    'Hidrolik Yağ Değişimi' => 'Her 1000 saat veya 12 ayda bir',
                    'Tekerlek Kontrolü' => 'Her 500 saat',
                    'Elektrik Bağlantıları' => 'Her 200 saat kontrol',
                    'Batarya Bakımı' => 'Bakım gerektirmez (Li-Ion)',
                    'Yedek Parça Garantisi' => '10 yıl yedek parça tedarik garantisi',
                    'Servis Aralığı' => 'Önerilen: 6 ayda bir profesyonel kontrol',
                ],

                // 14. OPTIONS / OPSIYONLAR
                'options' => [
                    '_title' => 'Opsiyonlar ve Aksesuarlar',
                    '_icon' => 'puzzle-piece',
                    'Çatal Uzunlukları' => '900, 1000, 1150, 1220, 1350, 1500 mm',
                    'Çatal Genişlikleri' => '560 mm (standart), 685 mm (geniş)',
                    'Stabilizasyon Tekerlekleri' => 'Opsiyonel (bozuk zemin için)',
                    'Hızlı Şarj Ünitesi' => '2x 24V-10A (şarj süresi: 2-3 saat)',
                    'Ekstra Batarya Modülü' => '24V/20Ah Li-Ion (çalışma süresi uzatma)',
                    'Batarya Kabineti' => '4 modül kapasiteli saklama kabini',
                ],

                // 15. CERTIFICATIONS / SERTİFİKASYONLAR
                'certifications' => [
                    '_title' => 'Sertifikasyonlar ve Standartlar',
                    '_icon' => 'certificate',
                    'CE Sertifikası' => 'Avrupa Birliği uygunluk sertifikası',
                    'ISO 9001' => 'Kalite yönetim sistemi sertifikası',
                    'IP54' => 'Toz ve su geçirmezlik sertifikası',
                    'EN 1757-1' => 'Yaya tipi transpalet güvenlik standardı',
                    'EMC Uyumluluğu' => 'Elektromanyetik uyumluluk sertifikası',
                    'RoHS' => 'Zararlı madde kısıtlaması uyumluluğu',
                ],

            ], JSON_UNESCAPED_UNICODE),

            // Diğer alanlar...
            'price_on_request' => true,
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->command->info("✅ Detaylı technical specs örneği oluşturuldu!");
        $this->command->info("📊 15 section, 80+ teknik özellik");
    }
}
