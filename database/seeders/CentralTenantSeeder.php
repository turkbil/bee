<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;
use App\Helpers\TenantHelpers;

class CentralTenantSeeder extends Seeder
{
    /**
     * Production için basitleştirilmiş Central Tenant Seeder
     *
     * CREATE DATABASE yapmaz, sadece central tenant ve domain kaydı oluşturur
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('CentralTenantSeeder sadece central veritabanında çalışır.');
            return;
        }

        // Mevcut central tenant varsa atla
        if (Tenant::find(1)) {
            $this->command->info('✅ Central tenant zaten mevcut (ID: 1)');
            return;
        }

        $this->command->info('🏗️  Creating Central Tenant (Production Mode - No Database Creation)...');

        // Central tenant'ı ekle
        DB::table('tenants')->insert([
            'title' => config('app.name', 'Laravel'),
            'fullname' => 'Admin User',
            'email' => 'admin@' . env('APP_DOMAIN', 'laravel.test'),
            'phone' => '',
            'address' => '',
            'tax_office' => '',
            'tax_number' => '',
            'tenant_type' => 'central',
            'tenant_default_locale' => 'tr',
            'tenant_ai_provider_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $centralTenantId = DB::table('tenants')->where('title', config('app.name', 'Laravel'))->value('id');

        // Domain ekle
        DB::table('domains')->insert([
            'domain' => env('APP_DOMAIN', 'laravel.test'),
            'tenant_id' => $centralTenantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin kullanıcısı oluştur
        User::create([
            'name' => 'Admin',
            'email' => 'admin@' . env('APP_DOMAIN', 'laravel.test'),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Central Tenant Created:');
        $this->command->info('   - Tenant ID: ' . $centralTenantId);
        $this->command->info('   - Domain: ' . env('APP_DOMAIN', 'laravel.test'));
        $this->command->info('   - Admin: admin@' . env('APP_DOMAIN', 'laravel.test') . ' / password');
    }
}
