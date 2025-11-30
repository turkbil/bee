<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * ⚠️ ARTIK HARDCODE KULLANILMIYOR!
     *
     * Settings artık FormBuilder ile yönetiliyor.
     * Layout JSON ve settings kayıtları DB'de mevcut.
     *
     * Bu seeder sadece grup varlığını kontrol eder.
     * Eğer gruplar yoksa uyarı verir.
     *
     * ❌ KALDIRILAN HARDCODE BLOKLARI (2025-11-30):
     * - $settings array (7 ayar: site_title, company_name, vb.) → Kaldırıldı
     * - updateOrInsert döngüsü → Kaldırıldı
     *
     * ✅ YENİ YÖNTEM:
     * Admin Panel → SettingManagement → Form Builder
     * - Group 6 (Site Bilgileri): https://tuufi.com/admin/settingmanagement/form-builder/6
     * - Group 8 (SEO & Analitik): https://tuufi.com/admin/settingmanagement/form-builder/8
     */
    public function run(): void
    {
        // Kontrol edilecek gruplar
        $groups = [
            6 => 'Site Bilgileri',
            8 => 'SEO & Analitik',
        ];

        $allOk = true;

        foreach ($groups as $groupId => $groupName) {
            $group = DB::table('settings_groups')
                ->where('id', $groupId)
                ->first();

            if (!$group) {
                $this->command->error("\"$groupName\" grubu bulunamadı (ID: $groupId). Lütfen önce SettingsGroupsTableSeeder çalıştırın");
                $allOk = false;
                continue;
            }

            if (!$group->layout) {
                $this->command->warn("⚠️ \"$groupName\" grubunun layout'u yok! FormBuilder ile oluşturun.");
                $this->command->info("👉 https://tuufi.com/admin/settingmanagement/form-builder/$groupId");
                $allOk = false;
                continue;
            }

            $this->command->info("✅ \"$groupName\" grubu mevcut. Layout ve settings FormBuilder ile yönetiliyor.");
        }

        if ($allOk) {
            $this->command->info("\n✅ Tüm gruplar hazır! Settings FormBuilder ile yönetiliyor.");
        }
    }
}
