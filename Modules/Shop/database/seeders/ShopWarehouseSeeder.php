<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

/**
 * Shop Warehouse Seeder
 *
 * İXTİF depo lokasyonlarını oluşturur.
 * NOT: Bu seeder sadece CENTRAL database'de çalışmalıdır!
 */
class ShopWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant context'te çalışmayı engelle
        if (!TenantHelpers::isCentral()) {
            $this->command->warn('⚠️  ShopWarehouseSeeder sadece central database için, atlanıyor...');
            return;
        }

        $this->command->info('🏭 Shop Warehouse Seeder başlatılıyor...');

        // 1. İXTİF ANA DEPO - TUZLA
        DB::table('shop_warehouses')->insert([
            'warehouse_id' => 1,
            'title' => json_encode([
                'tr' => 'İXTİF Tuzla Ana Depo',
                'en' => 'IXTIF Tuzla Main Warehouse',
            ], JSON_UNESCAPED_UNICODE),
            'code' => 'IXTIF-TZL-001',
            'description' => json_encode([
                'tr' => 'Ana depo ve dağıtım merkezi - Satış, kiralama, ikinci el, teknik servis ve yedek parça hizmetleri',
                'en' => 'Main warehouse and distribution center - Sales, rental, second hand, technical service and spare parts',
            ], JSON_UNESCAPED_UNICODE),
            'warehouse_type' => 'main',
            'contact_person' => 'İXTİF Müşteri Hizmetleri',
            'phone' => '0216 755 3 555',
            'email' => 'info@ixtif.com',
            'address_line_1' => 'Tuzla, İstanbul - Anadolu Yakası',
            'address_line_2' => null,
            'city' => 'İstanbul',
            'postal_code' => '34940',
            'country_code' => 'TR',
            'latitude' => 40.8185,
            'longitude' => 29.2953,
            'total_area' => 5000.00, // m²
            'total_capacity' => 10000, // ürün adedi
            'used_capacity' => 0,
            'is_active' => true,
            'is_default' => true,
            'allow_backorders' => true,
            'allow_shipping' => true,
            'allow_pickup' => true,
            'operating_hours' => json_encode([
                'monday' => '08:00-18:00',
                'tuesday' => '08:00-18:00',
                'wednesday' => '08:00-18:00',
                'thursday' => '08:00-18:00',
                'friday' => '08:00-18:00',
                'saturday' => '09:00-14:00',
                'sunday' => 'Closed',
            ], JSON_UNESCAPED_UNICODE),
            'priority' => 1,
            'sort_order' => 1,
            'notes' => 'Ana depo - Tüm hizmetler mevcut',
            'metadata' => json_encode([
                'services' => ['sales', 'rental', 'second_hand', 'technical_service', 'spare_parts'],
                'languages' => ['tr', 'en'],
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ İXTİF Tuzla Ana Depo eklendi!');
    }
}
