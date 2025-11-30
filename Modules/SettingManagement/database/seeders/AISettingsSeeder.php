<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AISettingsSeeder extends Seeder
{
    /**
     * ⚠️ ARTIK HARDCODE KULLANILMIYOR!
     *
     * AI ayarları artık FormBuilder ile yönetiliyor.
     * Layout JSON ve settings kayıtları DB'de mevcut.
     *
     * Bu seeder sadece "Yapay Zeka" grubunun varlığını kontrol eder.
     * Eğer grup yoksa uyarı verir.
     *
     * ❌ KALDIRILAN HARDCODE BLOKLARI (2025-11-30):
     * - $settings array (25 AI ayarı) → Kaldırıldı
     * - getAISettingsLayout() metodu (257 satır JSON) → Kaldırıldı
     * - updateOrInsert döngüsü → Kaldırıldı
     *
     * ✅ YENİ YÖNTEM:
     * Admin Panel → SettingManagement → Form Builder (Group 9)
     * https://tuufi.com/admin/settingmanagement/form-builder/9
     */
    public function run(): void
    {
        // "Yapay Zeka" grubunun var olduğunu kontrol et
        $aiGroup = DB::table('settings_groups')
            ->where('name', 'Yapay Zeka')
            ->where('parent_id', 1) // Genel Sistem grubunun altındaki Yapay Zeka
            ->first();

        if (!$aiGroup) {
            $this->command->error('"Yapay Zeka" grubu bulunamadı. Lütfen önce SettingsGroupsTableSeeder çalıştırın');
            return;
        }

        // Layout var mı kontrol et
        if (!$aiGroup->layout) {
            $this->command->warn('⚠️ Yapay Zeka grubunun layout\'u yok! FormBuilder ile oluşturun.');
            $this->command->info('👉 https://tuufi.com/admin/settingmanagement/form-builder/' . $aiGroup->id);
            return;
        }

        $this->command->info('✅ Yapay Zeka grubu mevcut. Layout ve settings FormBuilder ile yönetiliyor.');
    }
}
