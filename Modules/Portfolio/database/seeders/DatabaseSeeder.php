<?php
namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            \Modules\Portfolio\Database\Seeders\DatabaseSeeder::class, // Modules/Portfolio içindeki DatabaseSeeder
        ]);
    }
}
