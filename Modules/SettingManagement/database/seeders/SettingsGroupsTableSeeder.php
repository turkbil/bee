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
           // Ana Gruplar
           ['id' => 1, 'name' => 'Sistem', 'parent_id' => null],
           ['id' => 2, 'name' => 'Modül', 'parent_id' => null], 
           ['id' => 3, 'name' => 'Tema', 'parent_id' => null],
           ['id' => 4, 'name' => 'Eklenti', 'parent_id' => null],
           ['id' => 5, 'name' => 'Kullanıcı', 'parent_id' => null],

           // Alt Gruplar
           // Sistem altında
           ['id' => 6, 'name' => 'Site Ayarları', 'parent_id' => 1],
           ['id' => 7, 'name' => 'İletişim Bilgileri', 'parent_id' => 1],
           ['id' => 8, 'name' => 'Sosyal Medya Bilgileri', 'parent_id' => 1],
           
           // Modül altında
           ['id' => 9, 'name' => 'Sayfa', 'parent_id' => 2],
           ['id' => 10, 'name' => 'Portfolyo', 'parent_id' => 2],
           
           // Tema altında
           ['id' => 11, 'name' => 'Default Tema', 'parent_id' => 3],
           
           // Kullanıcı altında
           ['id' => 12, 'name' => 'Yönetici Ayarları', 'parent_id' => 5],
           ['id' => 13, 'name' => 'Kullanıcı Ayarları', 'parent_id' => 5],
           
           // Yeni Eklenen Örnekler Grubu
           ['id' => 14, 'name' => 'Örnekler', 'parent_id' => 1, 'icon' => 'fas fa-flask'],
       ];

       foreach ($groups as $group) {
           DB::table('settings_groups')->insert([
               'id' => $group['id'],
               'name' => $group['name'],
               'slug' => Str::slug($group['name']),
               'parent_id' => $group['parent_id'],
               'icon' => $group['icon'] ?? null,
               'is_active' => true,
               'created_at' => now(),
               'updated_at' => now(),
           ]);
       }
   }
}