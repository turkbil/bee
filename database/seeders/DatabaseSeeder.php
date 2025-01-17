<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tenants')->insert([
            'id'         => 1, // tenant_id yerine id kullanılıyor
            'data' => json_encode(['name' => 'Tenant 1']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('domains')->insert([
            'id'         => 1,
            'domain'     => 'a.test',
            'tenant_id'  => 1, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name'       => 'Nurullah',
            'email'      => 'nurullah@nurullah.net',
            'password'   => Hash::make('nurullah'),
            'tenant_id'  => 1, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tenant 2
        DB::table('tenants')->insert([
            'id'         => 2, // tenant_id yerine id kullanılıyor
            'data' => json_encode(['name' => 'Tenant 2']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('domains')->insert([
            'id'         => 2,
            'domain'     => 'b.test',
            'tenant_id'  => 2, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name'       => 'Nurullah2',
            'email'      => 'nurullah2@nurullah.net',
            'password'   => Hash::make('nurullah2'),
            'tenant_id'  => 2, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tenant 3
        DB::table('tenants')->insert([
            'id'         => 3, // tenant_id yerine id kullanılıyor
            'data' => json_encode(['name' => 'Tenant 3']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('domains')->insert([
            'id'         => 3,
            'domain'     => 'c.test',
            'tenant_id'  => 3, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name'       => 'Nurullah3',
            'email'      => 'nurullah3@nurullah.net',
            'password'   => Hash::make('nurullah3'),
            'tenant_id'  => 3, // tenant_id kullanılıyor
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call([
            UserSeeder::class, // Kullanıcı seed işlemi
            \Modules\Page\Database\Seeders\PageSeeder::class, 
            \Modules\Portfolio\Database\Seeders\PortfolioCategorySeeder::class,
            \Modules\Portfolio\Database\Seeders\PortfolioSeeder::class
        ]);
    }
}
