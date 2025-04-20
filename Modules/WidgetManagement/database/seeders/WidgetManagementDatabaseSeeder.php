<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class WidgetManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(WidgetCategorySeeder::class);
        $this->call(FileWidgetSeeder::class);
        $this->call(SliderWidgetSeeder::class);
    }
}