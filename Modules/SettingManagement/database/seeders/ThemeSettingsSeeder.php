<?php
namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSettingsSeeder extends Seeder
{
    /**
     * ⚠️ ARTIK HARDCODE KULLANILMIYOR!
     *
     * Tema ayarları artık FormBuilder ile yönetiliyor.
     * Layout JSON ve settings kayıtları DB'de mevcut.
     *
     * Bu seeder sadece Tema grubunun varlığını kontrol eder.
     * Eğer grup yoksa uyarı verir.
     *
     * ❌ KALDIRILAN HARDCODE BLOKLARI (2025-11-30):
     * - $settings array (12 renk ayarı) → Kaldırıldı
     * - getThemeSettingsLayout() metodu (200+ satır JSON) → Kaldırıldı
     * - updateOrInsert döngüsü → Kaldırıldı
     *
     * ✅ YENİ YÖNTEM:
     * Admin Panel → SettingManagement → Form Builder (Group 7)
     * https://tuufi.com/admin/settingmanagement/form-builder/7
     */
    public function run(): void
    {
        // "Tema" grubunun var olduğunu kontrol et
        $themeGroup = DB::table('settings_groups')
            ->where('name', 'Tema')
            ->where('parent_id', 5) // Site grubunun altındaki Tema
            ->first();

        if (!$themeGroup) {
            $this->command->error('"Tema" grubu bulunamadı. Lütfen önce SettingsGroupsTableSeeder çalıştırın');
            return;
        }

        // Layout var mı kontrol et
        if (!$themeGroup->layout) {
            $this->command->warn('⚠️ Tema grubunun layout\'u yok! FormBuilder ile oluşturun.');
            $this->command->info('👉 https://tuufi.com/admin/settingmanagement/form-builder/' . $themeGroup->id);
            return;
        }

        $this->command->info('✅ Tema grubu mevcut. Layout ve settings FormBuilder ile yönetiliyor.');
    }
}
