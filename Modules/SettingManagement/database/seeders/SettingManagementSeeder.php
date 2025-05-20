<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        
        // Tenant veritabanında çalışırken hiçbir şey yapma
        if (app()->bound('tenancy.tenant')) {
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