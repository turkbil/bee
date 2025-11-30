<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSettingsSeeder extends Seeder
{
    /**
     * ⚠️ ARTIK HARDCODE KULLANILMIYOR!
     *
     * İletişim ayarları artık FormBuilder ile yönetiliyor.
     * Layout JSON ve settings kayıtları DB'de mevcut.
     *
     * Bu seeder sadece "İletişim Bilgileri" grubunun varlığını kontrol eder.
     * Eğer grup yoksa uyarı verir.
     *
     * ❌ KALDIRILAN HARDCODE BLOKLARI (2025-11-30):
     * - $settings array (25 iletişim ayarı) → Kaldırıldı
     * - getLayout() metodu (192 satır JSON) → Kaldırıldı
     * - updateOrCreate döngüsü → Kaldırıldı
     *
     * ✅ YENİ YÖNTEM:
     * Admin Panel → SettingManagement → Form Builder (Group 10)
     * https://tuufi.com/admin/settingmanagement/form-builder/10
     */
    public function run(): void
    {
        // "İletişim Bilgileri" grubunun var olduğunu kontrol et
        $contactGroup = DB::table('settings_groups')
            ->where('slug', 'iletisim-bilgileri')
            ->where('parent_id', 1) // Genel Sistem grubunun altındaki İletişim
            ->first();

        if (!$contactGroup) {
            $this->command->error('"İletişim Bilgileri" grubu bulunamadı. Lütfen önce SettingsGroupsTableSeeder çalıştırın');
            return;
        }

        // Layout var mı kontrol et
        if (!$contactGroup->layout) {
            $this->command->warn('⚠️ İletişim Bilgileri grubunun layout\'u yok! FormBuilder ile oluşturun.');
            $this->command->info('👉 https://tuufi.com/admin/settingmanagement/form-builder/' . $contactGroup->id);
            return;
        }

        $this->command->info('✅ İletişim Bilgileri grubu mevcut. Layout ve settings FormBuilder ile yönetiliyor.');
    }
}
