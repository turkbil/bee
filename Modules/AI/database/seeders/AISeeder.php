<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;

class AISeeder extends Seeder
{
    /**
     * AI Modülü Ana Seeder'ı - AIDatabaseSeeder'ını çağırır
     */
    public function run(): void
    {
        $this->call(AIDatabaseSeeder::class);
    }
}