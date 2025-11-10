<?php

namespace Modules\ReviewSystem\database\seeders;

use Illuminate\Database\Seeder;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use App\Helpers\TenantHelpers;

/**
 * ReviewSystem Module Master Seeder
 *
 * Context-aware orchestrator seeder.
 * Routes to appropriate seeder based on database context (Central or Tenant).
 *
 * Architecture:
 * - Central Database: ReviewSystemSeederCentral (tr, en, ar)
 * - Tenant 2: ReviewSystemSeederTenant2 (tr, en)
 * - Tenant 3: ReviewSystemSeederTenant3 (tr, en)
 * - Tenant 4: ReviewSystemSeederTenant4 (tr, en)
 *
 * @package Modules\ReviewSystem\Database\Seeders
 */
class ReviewSystemSeeder extends Seeder
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
        $this->command->info('📄 REVIEWSYSTEM MODULE SEEDER');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Central Database Seeding
        if ($isCentral) {
            $this->command->info('🌐 Context: CENTRAL DATABASE');
            $this->command->info('🗂️  Running: ReviewSystemSeederCentral');
            $this->command->info('🌍 Languages: Turkish, English, Arabic');
            $this->command->newLine();

            $this->call(ReviewSystemSeederCentral::class);

            $this->command->info('✅ Central database seeding completed');
            return;
        }

        // Tenant Database Seeding
        $this->command->info("🏢 Context: TENANT DATABASE (ID: {$tenantId})");

        switch ($tenantId) {
            case 2:
                $this->command->info('🗂️  Running: ReviewSystemSeederTenant2');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Digital Agency / E-Commerce');
                $this->command->newLine();

                $this->call(ReviewSystemSeederTenant2::class);
                break;

            case 3:
                $this->command->info('🗂️  Running: ReviewSystemSeederTenant3');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Corporate Business');
                $this->command->newLine();

                $this->call(ReviewSystemSeederTenant3::class);
                break;

            case 4:
                $this->command->info('🗂️  Running: ReviewSystemSeederTenant4');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Educational Institution');
                $this->command->newLine();

                $this->call(ReviewSystemSeederTenant4::class);
                break;

            default:
                $this->command->warn("⚠️  No specific seeder found for Tenant ID: {$tenantId}");
                $this->command->info('💡 Creating default reviewsystems with factory...');
                $this->command->newLine();

                $this->createDefaultPages();
                break;
        }

        $this->command->info('✅ Tenant database seeding completed');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Create default reviewsystems for unknown tenants
     * Uses factory to generate basic reviewsystem structure
     */
    private function createDefaultPages(): void
    {
        // Duplicate check
        if (ReviewSystem::count() > 0) {
            $this->command->info('📋 Pages already exist, skipping default creation');
            return;
        }

        // Create homereviewsystem
        $homereviewsystem = ReviewSystem::factory()
            ->active()
            ->create();

        $this->command->info('✓ Homereviewsystem created');

        // Create basic reviewsystems
        $reviewsystems = [
            'about' => ReviewSystem::factory()->aboutPage(),
            'contact' => ReviewSystem::factory()->contactPage(),
            'privacy' => ReviewSystem::factory()->privacyPage(),
            'terms' => ReviewSystem::factory()->termsPage(),
        ];

        foreach ($reviewsystems as $type => $factory) {
            $factory->create();
            $this->command->info("✓ {$type} reviewsystem created");
        }

        // Create additional random reviewsystems for development
        ReviewSystem::factory()
            ->simple()
            ->count(10)
            ->create();

        $this->command->info('✓ 10 random reviewsystems created for development');
    }
}
