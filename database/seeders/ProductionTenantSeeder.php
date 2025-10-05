<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\TenantHelpers;

class ProductionTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central context'te çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('ProductionTenantSeeder sadece central veritabanında çalışır.');
            return;
        }

        $this->command->info('🏭 Production Central Tenant oluşturuluyor...');

        // Mevcut tenant'ları kontrol et
        $existingTenant = DB::table('tenants')->where('central', true)->first();
        if ($existingTenant) {
            $this->command->info('✅ Central tenant zaten var, atlanıyor...');
            return;
        }

        // Central tenant'ı oluştur (mevcut database kullan)
        DB::table('tenants')->insert([
            'id' => 1,
            'title' => 'Laravel',
            'fullname' => 'Nurullah Okatan',
            'email' => 'nurullah@nurullah.net',
            'phone' => '+90 533 123 45 67',
            'tenancy_db_name' => config('database.connections.mysql.database', 'laravel'),
            'is_active' => true,
            'central' => true,
            'theme_id' => 1,
            'tenant_default_locale' => 'tr',
            'data' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Domain ekle (with www variant)
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'laravel.test';
        DB::table('domains')->insert([
            [
                'domain' => $domain,
                'tenant_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'domain' => 'www.' . $domain,
                'tenant_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Mevcut kullanıcılar varsa atlama
        if (User::count() > 0) {
            $this->command->info('✅ Kullanıcılar zaten var, atlanıyor...');
        } else {
            // Central tenant için kullanıcılar oluştur
            User::create([
                'name' => 'Nurullah Okatan',
                'email' => 'nurullah@nurullah.net',
                'password' => Hash::make('test'),
                'email_verified_at' => now(),
            ]);

            User::create([
                'name' => 'Türk Bilişim',
                'email' => 'info@turkbilisim.com.tr',
                'password' => Hash::make('test'),
                'email_verified_at' => now(),
            ]);

            User::create([
                'name' => 'Laravel Admin',
                'email' => 'laravel@test',
                'password' => Hash::make('test'),
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Central tenant kullanıcıları oluşturuldu');
        }

        $this->command->info('🎉 Production Central Tenant başarıyla oluşturuldu!');
    }
}