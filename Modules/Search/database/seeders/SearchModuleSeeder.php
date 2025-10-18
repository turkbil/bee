<?php

declare(strict_types=1);

namespace Modules\Search\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Search module already exists
        $existingModule = DB::table('modules')
            ->where('name', 'search')
            ->first();

        if ($existingModule) {
            $this->command->info('Search module already exists. Updating...');

            DB::table('modules')
                ->where('name', 'search')
                ->update([
                    'display_name' => 'Arama Sistemi',
                    'description' => 'Universal search system with analytics - Meilisearch powered',
                    'version' => '1.0.0',
                    'type' => 'system',
                    'is_active' => true,
                    'settings' => null,
                    'updated_at' => now(),
                ]);
        } else {
            $this->command->info('Creating Search module...');

            DB::table('modules')->insert([
                'name' => 'search',
                'display_name' => 'Arama Sistemi',
                'description' => 'Universal search system with analytics - Meilisearch powered',
                'version' => '1.0.0',
                'type' => 'system',
                'is_active' => true,
                'settings' => null, // Settings alan\u0131 bigint tipinde, JSON de\u011fil
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Search module seeded successfully!');
    }
}
