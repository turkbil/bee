<?php
namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            \Modules\Page\Database\Seeders\DatabaseSeeder::class, // Modules/Page içindeki DatabaseSeeder
        ]);
    }
}
