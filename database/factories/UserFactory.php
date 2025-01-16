<?php
namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User; // Tenant modelini ekliyoruz
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
// Faker'ı doğru şekilde ekliyoruz

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
                                                   // tenants tablosundaki id değerlerini al
        $siteIds = Tenant::pluck('id')->toArray(); // tenant_id yerine id alıyoruz

        // Eğer tenants tablosunda tenant_id yoksa hata fırlat
        if (empty($siteIds)) {
            throw new \Exception('tenants tablosunda tenant_id bulunamadı.');
        }

        return [
            'name'      => $this->faker->name, // $faker kullanımı
            'email' => $this->faker->unique()->safeEmail,
            'password'  => bcrypt('password'),
            'tenant_id' => $this->faker->randomElement($siteIds), // tenant_id'yi rastgele bir tenant'tan alıyoruz
        ];
    }
}
