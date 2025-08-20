<?php

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;

class MenuManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SimpleMenuSeeder::class,
        ]);
    }
}
