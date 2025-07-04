<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SettingManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('SettingManagementSeeder sadece central veritabanında çalışır.');
            return;
        }
        
        // Sadece central veritabanında çalıştır
        $this->call([
            SettingsGroupsTableSeeder::class,
            SettingsTableSeeder::class,
            ThemeSettingsSeeder::class
        ]);
    }
}