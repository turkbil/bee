<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use App\Helpers\TenantHelpers;

/**
 * Page Module Master Seeder
 *
 * Context-aware orchestrator seeder.
 * Routes to appropriate seeder based on database context (Central or Tenant).
 *
 * Architecture:
 * - Central Database: PageSeederCentral (tr, en, ar)
 * - Tenant 2: PageSeederTenant2 (tr, en)
 * - Tenant 3: PageSeederTenant3 (tr, en)
 * - Tenant 4: PageSeederTenant4 (tr, en)
 *
 * @package Modules\Page\Database\Seeders
 */
class PageSeeder extends Seeder
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
        $this->command->info('📄 PAGE MODULE SEEDER');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Central Database Seeding
        if ($isCentral) {
            $this->command->info('🌐 Context: CENTRAL DATABASE');
            $this->command->info('🗂️  Running: PageSeederCentral');
            $this->command->info('🌍 Languages: Turkish, English, Arabic');
            $this->command->newLine();

            $this->call(PageSeederCentral::class);

            $this->command->info('✅ Central database seeding completed');
            return;
        }

        // Tenant Database Seeding
        $this->command->info("🏢 Context: TENANT DATABASE (ID: {$tenantId})");

        switch ($tenantId) {
            case 2:
                $this->command->info('🗂️  Running: PageSeederTenant2');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Digital Agency / E-Commerce');
                $this->command->newLine();

                $this->call(PageSeederTenant2::class);
                break;

            case 3:
                $this->command->info('🗂️  Running: PageSeederTenant3');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Corporate Business');
                $this->command->newLine();

                $this->call(PageSeederTenant3::class);
                break;

            case 4:
                $this->command->info('🗂️  Running: PageSeederTenant4');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Educational Institution');
                $this->command->newLine();

                $this->call(PageSeederTenant4::class);
                break;

            default:
                $this->command->warn("⚠️  No specific seeder found for Tenant ID: {$tenantId}");
                $this->command->info('💡 Creating default pages with factory...');
                $this->command->newLine();

                $this->createDefaultPages();
                break;
        }

        $this->command->info('✅ Tenant database seeding completed');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Create default pages for unknown tenants
     * Uses factory to generate basic page structure
     */
    private function createDefaultPages(): void
    {
        // Duplicate check
        if (Page::count() > 0) {
            $this->command->info('📋 Pages already exist, skipping default creation');
            return;
        }

        // Create homepage
        $homepage = Page::factory()
            ->homepage()
            ->create();

        $this->command->info('✓ Homepage created');

        // Create basic pages
        $pages = [
            'about' => Page::factory()->aboutPage(),
            'contact' => Page::factory()->contactPage(),
            'privacy' => Page::factory()->privacyPage(),
            'terms' => Page::factory()->termsPage(),
        ];

        foreach ($pages as $type => $factory) {
            $factory->create();
            $this->command->info("✓ {$type} page created");
        }

        // Create additional random pages for development
        Page::factory()
            ->simple()
            ->count(10)
            ->create();

        $this->command->info('✓ 10 random pages created for development');
    }
}