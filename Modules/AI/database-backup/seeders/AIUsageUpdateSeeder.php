<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TenantHelpers;

class AIUsageUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('Bu seeder sadece central veritabanında çalışır.');
            return;
        }

        // Mevcut kullanım verilerini temizle
        DB::table('ai_token_usage')->delete();

        $this->command->info('AI Token kullanım örnek verileri temizlendi.');
    }

}