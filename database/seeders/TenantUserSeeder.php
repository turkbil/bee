<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Faker\Factory as Faker;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ana sistemde kullanıcı oluştur
        User::create([
            'name' => 'Nurullah Okatan',
            'email' => 'nurullah@nurullah.net',
            'password' => bcrypt('nurullah'),
        ]);

        $tenants = Tenant::all();
        $faker = Faker::create('tr_TR');

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($faker) {
                // Her tenant için nurullah@nurullah.net
                try {
                    User::create([
                        'name' => 'Nurullah Okatan',
                        'email' => 'nurullah@nurullah.net',
                        'password' => bcrypt('nurullah'),
                    ]);
                } catch (\Exception $e) {
                    // Duplicate varsa hata vermeden devam et
                }

                // 10 rastgele kullanıcı
                foreach(range(1, 10) as $index) {
                    $firstName = $faker->firstName;
                    $lastName = $faker->lastName;
                    
                    User::create([
                        'name' => $firstName . ' ' . $lastName,
                        'email' => strtolower($faker->unique()->userName) . '@' . $faker->freeEmailDomain,
                        'password' => bcrypt('password'),
                    ]);
                }
            });
        }
    }
}