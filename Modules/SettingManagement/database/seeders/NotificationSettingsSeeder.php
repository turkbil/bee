<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * ⚠️ ARTIK HARDCODE KULLANILMIYOR!
     *
     * Bildirim ayarları artık FormBuilder ile yönetiliyor.
     * Layout JSON ve settings kayıtları DB'de mevcut.
     *
     * Bu seeder sadece "Bildirim Ayarları" grubunun varlığını kontrol eder.
     * Eğer grup yoksa uyarı verir.
     *
     * ❌ KALDIRILAN HARDCODE BLOKLARI (2025-11-30):
     * - $settings array (10 bildirim ayarı) → Kaldırıldı
     * - getLayout() metodu (312 satır JSON) → Kaldırıldı
     * - updateOrCreate döngüsü → Kaldırıldı
     *
     * ✅ YENİ YÖNTEM:
     * Admin Panel → SettingManagement → Form Builder (Group 11)
     * https://tuufi.com/admin/settingmanagement/form-builder/11
     */
    public function run(): void
    {
        // "Bildirim Ayarları" grubunun var olduğunu kontrol et
        $notificationGroup = DB::table('settings_groups')
            ->where('slug', 'bildirim-ayarlari')
            ->where('parent_id', 1) // Genel Sistem grubunun altındaki Bildirim
            ->first();

        if (!$notificationGroup) {
            $this->command->error('"Bildirim Ayarları" grubu bulunamadı. Lütfen önce SettingsGroupsTableSeeder çalıştırın');
            return;
        }

        // Layout var mı kontrol et
        if (!$notificationGroup->layout) {
            $this->command->warn('⚠️ Bildirim Ayarları grubunun layout\'u yok! FormBuilder ile oluşturun.');
            $this->command->info('👉 https://tuufi.com/admin/settingmanagement/form-builder/' . $notificationGroup->id);
            return;
        }

        $this->command->info('✅ Bildirim Ayarları grubu mevcut. Layout ve settings FormBuilder ile yönetiliyor.');
    }
}
