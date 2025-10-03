<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Helpers\TenantHelpers;

/**
 * Portfolio Module Master Seeder
 *
 * Context-aware orchestrator seeder.
 * Routes to appropriate seeder based on database context (Central or Tenant).
 *
 * Architecture:
 * - Central Database: PortfolioSeederCentral (tr, en)
 * - Tenant 2: PortfolioSeederTenant2 (tr, en)
 * - Tenant 3: PortfolioSeederTenant3 (tr, en)
 * - Tenant 4: PortfolioSeederTenant4 (tr, en)
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Automatically detects context and calls appropriate seeder.
     * Prevents duplicate seeding with smart checks.
     */
    public function run(): void
    {
        // Context detection
        $isCentral = TenantHelpers::isCentral();
        $tenantId = TenantHelpers::getCurrentTenantId();

        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📂 PORTFOLIO MODULE SEEDER');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Central Database Seeding
        if ($isCentral) {
            $this->command->info('🌐 Context: CENTRAL DATABASE');
            $this->command->info('🗂️  Running: PortfolioSeederCentral');
            $this->command->info('🌍 Languages: Turkish, English');
            $this->command->newLine();

            $this->call(PortfolioSeederCentral::class);

            $this->command->info('✅ Central database seeding completed');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected (Tenant ID: 1), portfolio tables do not exist in central!');
            return;
        }

        // Tenant Database Seeding
        $this->command->info("🏢 Context: TENANT DATABASE (ID: {$tenantId})");

        switch ($tenantId) {
            case 2:
                $this->command->info('🗂️  Running: PortfolioSeederTenant2');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Digital Agency / E-Commerce');
                $this->command->newLine();

                $this->call(PortfolioSeederTenant2::class);
                break;

            case 3:
                $this->command->info('🗂️  Running: PortfolioSeederTenant3');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Corporate Business');
                $this->command->newLine();

                $this->call(PortfolioSeederTenant3::class);
                break;

            case 4:
                $this->command->info('🗂️  Running: PortfolioSeederTenant4');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Creative Portfolio');
                $this->command->newLine();

                $this->call(PortfolioSeederTenant4::class);
                break;

            default:
                $this->command->warn("⚠️  No specific seeder found for Tenant ID: {$tenantId}");
                $this->command->info('💡 Creating default portfolios with factory...');
                $this->command->newLine();

                $this->createDefaultPortfolios();
                break;
        }

        $this->command->info('✅ Tenant database seeding completed');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Create default portfolios for unknown tenants
     * Uses factory to generate basic portfolio structure
     */
    private function createDefaultPortfolios(): void
    {
        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, portfolio tables do not exist in central!');
            return;
        }

        // Önce kategorileri oluştur
        $this->call(PortfolioCategorySeeder::class);

        // Duplicate check
        if (Portfolio::count() > 0) {
            $this->command->info('📋 Portfolios already exist, skipping default creation');
            return;
        }

        $categories = PortfolioCategory::all();

        if ($categories->isEmpty()) {
            $this->command->warn('⚠️  No categories found. Creating categories first...');
            $this->call(PortfolioCategorySeeder::class);
            $categories = PortfolioCategory::all();
        }

        // Her kategori için 2-3 portfolio oluştur
        foreach ($categories as $category) {
            Portfolio::factory()
                ->forCategory($category->category_id)
                ->active()
                ->count(rand(2, 3))
                ->create();

            $this->command->info("✓ Created portfolios for category: {$category->name['en']}");
        }

        // Ek rastgele portfoliolar (development için)
        Portfolio::factory()
            ->simple()
            ->count(5)
            ->create();

        $this->command->info('✓ 5 random portfolios created for development');

        $totalCount = Portfolio::count();
        $this->command->info("✅ Total {$totalCount} portfolios created");
    }
}
