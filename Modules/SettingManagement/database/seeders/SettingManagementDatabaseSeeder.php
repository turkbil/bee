<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\TenantHelpers;

class SettingManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Bu seeder TENANT veritabanında çalışır
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Bu seeder sadece tenant veritabanında çalışmalı
        if (TenantHelpers::isCentral()) {
            $this->command->info('SettingManagementDatabaseSeeder sadece tenant veritabanında çalışır.');
            return;
        }

        // Tenant-specific seeder'lar
        $this->call([
            AIKnowledgeBaseSeeder::class,
        ]);
    }
}
