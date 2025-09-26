<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting E-commerce database seeding...');

        // Seed in order of dependencies
        $this->call([
            EcommerceCategorySeeder::class,
            EcommerceBrandSeeder::class,
            EcommerceProductSeeder::class,
        ]);

        $this->command->info('✅ E-commerce database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Seeded data:');
        $this->command->info('   - Categories: 6 main + 12 sub categories');
        $this->command->info('   - Brands: 1 main brand (iXtif) + 3 sub-brands');
        $this->command->info('   - Products: 3 forklift products (CPD15/18/20TVL)');
        $this->command->info('   - Variants: Multiple mast height variants');
        $this->command->info('');
        $this->command->info('🎯 Ready for PDF processing!');
    }
}