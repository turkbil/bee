<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Veritabanını doldur.
     */
    public function run(): void
    {
        // Rastgele 100 kullanıcı oluştur
        User::factory()->count(200)->create();
    }
}
