<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsGroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            // Ana Gruplar (önem sırasına göre)
            ['id' => 1, 'name' => 'Sistem', 'parent_id' => null, 'icon' => 'fas fa-cogs'],
            ['id' => 2, 'name' => 'Tenant', 'parent_id' => null, 'icon' => 'fas fa-building'],
            ['id' => 3, 'name' => 'Kullanıcı', 'parent_id' => null, 'icon' => 'fas fa-users'],
            ['id' => 4, 'name' => 'Modül', 'parent_id' => null, 'icon' => 'fas fa-puzzle-piece'],
            ['id' => 5, 'name' => 'Tema', 'parent_id' => null, 'icon' => 'fas fa-paint-brush'],
            ['id' => 6, 'name' => 'Eklenti', 'parent_id' => null, 'icon' => 'fas fa-plug'],
            
            // Alt Gruplar
            // Sistem altında
            ['id' => 7, 'name' => 'Site Ayarları', 'parent_id' => 1, 'icon' => 'fas fa-sliders-h'],
            ['id' => 8, 'name' => 'İletişim Bilgileri', 'parent_id' => 1, 'icon' => 'fas fa-address-card'],
            ['id' => 9, 'name' => 'Sosyal Medya Bilgileri', 'parent_id' => 1, 'icon' => 'fas fa-share-alt'],
            ['id' => 10, 'name' => 'Örnekler', 'parent_id' => 1, 'icon' => 'fas fa-flask'],
            
            // Tenant altında
            ['id' => 11, 'name' => 'Tenant Özellikleri', 'parent_id' => 2, 'icon' => 'fas fa-cog'],
            ['id' => 12, 'name' => 'Tenant Özel Ayarları', 'parent_id' => 2, 'icon' => 'fas fa-sliders-h'],
            
            // Modül altında
            ['id' => 13, 'name' => 'Sayfa', 'parent_id' => 4, 'icon' => 'fas fa-file'],
            ['id' => 14, 'name' => 'Portfolyo', 'parent_id' => 4, 'icon' => 'fas fa-images'],
            
            // Tema altında
            ['id' => 15, 'name' => 'Default Tema', 'parent_id' => 5, 'icon' => 'fas fa-palette'],
            
            // Kullanıcı altında
            ['id' => 16, 'name' => 'Yönetici Ayarları', 'parent_id' => 3, 'icon' => 'fas fa-user-shield'],
            ['id' => 17, 'name' => 'Kullanıcı Ayarları', 'parent_id' => 3, 'icon' => 'fas fa-user-cog'],
        ];
        
        foreach ($groups as $group) {
            DB::table('settings_groups')->insert([
                'id' => $group['id'],
                'name' => $group['name'],
                'slug' => Str::slug($group['name']),
                'parent_id' => $group['parent_id'],
                'icon' => $group['icon'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}