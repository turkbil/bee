<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

/**
 * Shop Settings Seeder
 *
 * Genel shop ayarlarını oluşturur (para birimi, teslimat, iletişim, vb.)
 * NOT: Bu seeder sadece CENTRAL database'de çalışmalıdır!
 */
class ShopSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant context'te çalışmayı engelle
        if (!TenantHelpers::isCentral()) {
            $this->command->warn('⚠️  ShopSettingsSeeder sadece central database için, atlanıyor...');
            return;
        }

        $this->command->info('⚙️  Shop Settings Seeder başlatılıyor...');

        $settings = [
            // PARA BİRİMİ AYARLARI
            [
                'group' => 'currency',
                'key' => 'currency_primary',
                'value' => json_encode(['code' => 'TRY', 'symbol' => '₺', 'name' => 'Türk Lirası'], JSON_UNESCAPED_UNICODE),
                'value_type' => 'json',
                'description' => 'Ana para birimi',
            ],
            [
                'group' => 'currency',
                'key' => 'currency_secondary',
                'value' => json_encode(['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'], JSON_UNESCAPED_UNICODE),
                'value_type' => 'json',
                'description' => 'İkincil para birimi',
            ],
            [
                'group' => 'currency',
                'key' => 'currency_mode',
                'value' => 'hybrid', // hybrid, single, multi
                'value_type' => 'string',
                'description' => 'Para birimi modu (Hibrit: Bazı ürünler TRY, bazıları USD)',
            ],

            // FİYATLANDIRMA AYARLARI
            [
                'group' => 'pricing',
                'key' => 'price_display_mode',
                'value' => 'hybrid', // show, hide, request, hybrid
                'value_type' => 'string',
                'description' => 'Fiyat gösterim modu (Hibrit: Bazı ürünler fiyat gösterir, bazıları "Fiyat sorunuz")',
            ],
            [
                'group' => 'pricing',
                'key' => 'price_includes_tax',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'Fiyatlar KDV dahil mi?',
            ],
            [
                'group' => 'pricing',
                'key' => 'deposit_required',
                'value' => 'hybrid', // true, false, hybrid
                'value_type' => 'string',
                'description' => 'Peşinat zorunluluğu (Hibrit: Ürüne göre değişir)',
            ],
            [
                'group' => 'pricing',
                'key' => 'deposit_percentage_default',
                'value' => '30',
                'value_type' => 'integer',
                'description' => 'Varsayılan peşinat oranı (%)',
            ],

            // TAKSİT AYARLARI
            [
                'group' => 'payment',
                'key' => 'installment_enabled',
                'value' => 'hybrid', // true, false, hybrid
                'value_type' => 'string',
                'description' => 'Taksit seçeneği (Hibrit: Ürüne göre değişir)',
            ],
            [
                'group' => 'payment',
                'key' => 'installment_max_default',
                'value' => '12',
                'value_type' => 'integer',
                'description' => 'Maksimum taksit sayısı',
            ],
            [
                'group' => 'payment',
                'key' => 'leasing_enabled',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'Leasing/Finansman seçeneği aktif mi?',
            ],

            // TESLİMAT AYARLARI
            [
                'group' => 'shipping',
                'key' => 'delivery_time_default',
                'value' => '7', // gün
                'value_type' => 'integer',
                'description' => 'Standart teslimat süresi (gün)',
            ],
            [
                'group' => 'shipping',
                'key' => 'free_shipping_enabled',
                'value' => 'false',
                'value_type' => 'boolean',
                'description' => 'Ücretsiz kargo var mı?',
            ],
            [
                'group' => 'shipping',
                'key' => 'installation_included',
                'value' => 'false',
                'value_type' => 'boolean',
                'description' => 'Kurulum/Montaj dahil mi?',
            ],

            // SİPARİŞ LİMİTLERİ
            [
                'group' => 'order',
                'key' => 'order_min_quantity',
                'value' => '1',
                'value_type' => 'integer',
                'description' => 'Minimum sipariş adedi',
            ],
            [
                'group' => 'order',
                'key' => 'order_max_quantity',
                'value' => 'contact', // unlimited, contact, number
                'value_type' => 'string',
                'description' => 'Maksimum sipariş adedi (contact: Bize ulaşın)',
            ],

            // İLETİŞİM BİLGİLERİ
            [
                'group' => 'contact',
                'key' => 'company_name',
                'value' => json_encode([
                    'tr' => 'İXTİF İç ve Dış Ticaret A.Ş.',
                    'en' => 'IXTIF Import Export Inc.',
                ], JSON_UNESCAPED_UNICODE),
                'value_type' => 'json',
                'description' => 'Firma adı',
            ],
            [
                'group' => 'contact',
                'key' => 'company_phone',
                'value' => '0216 755 3 555',
                'value_type' => 'string',
                'description' => 'Ana telefon numarası',
            ],
            [
                'group' => 'contact',
                'key' => 'company_email',
                'value' => 'info@ixtif.com',
                'value_type' => 'string',
                'description' => 'Ana e-posta adresi',
            ],
            [
                'group' => 'contact',
                'key' => 'company_address',
                'value' => json_encode([
                    'tr' => 'Tuzla, İstanbul',
                    'en' => 'Tuzla, Istanbul',
                ], JSON_UNESCAPED_UNICODE),
                'value_type' => 'json',
                'description' => 'Firma adresi',
            ],

            // ÜRÜN ÖZELLIKLERI
            [
                'group' => 'product',
                'key' => 'rental_enabled',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'Kiralama seçeneği aktif mi?',
            ],
            [
                'group' => 'product',
                'key' => 'second_hand_enabled',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'İkinci el ürün satışı aktif mi?',
            ],
            [
                'group' => 'product',
                'key' => 'second_hand_as_variant',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'İkinci el ürünler varyant olarak mı gösterilir?',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('shop_settings')->insert([
                'group' => $setting['group'],
                'key' => $setting['key'],
                'value' => $setting['value'],
                'value_type' => $setting['value_type'],
                'description' => $setting['description'],
                'is_visible' => true,
                'is_editable' => true,
                'is_cached' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ ' . count($settings) . ' adet shop ayarı eklendi!');
    }
}
