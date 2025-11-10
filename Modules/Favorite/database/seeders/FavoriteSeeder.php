<?php

namespace Modules\Favorite\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Favorite\App\Models\Favorite;
use App\Helpers\TenantHelpers;

/**
 * Favorite Module Master Seeder
 *
 * Context-aware orchestrator seeder.
 * Routes to appropriate seeder based on database context (Central or Tenant).
 *
 * Architecture:
 * - Central Database: FavoriteSeederCentral (tr, en, ar)
 * - Tenant 2: FavoriteSeederTenant2 (tr, en)
 * - Tenant 3: FavoriteSeederTenant3 (tr, en)
 * - Tenant 4: FavoriteSeederTenant4 (tr, en)
 *
 * @package Modules\Favorite\Database\Seeders
 */
class FavoriteSeeder extends Seeder
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
        $this->command->info('📄 FAVORITE MODULE SEEDER');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Central Database Seeding
        if ($isCentral) {
            $this->command->info('🌐 Context: CENTRAL DATABASE');
            $this->command->info('🗂️  Running: FavoriteSeederCentral');
            $this->command->info('🌍 Languages: Turkish, English, Arabic');
            $this->command->newLine();

            $this->call(FavoriteSeederCentral::class);

            $this->command->info('✅ Central database seeding completed');
            return;
        }

        // Tenant Database Seeding
        $this->command->info("🏢 Context: TENANT DATABASE (ID: {$tenantId})");

        switch ($tenantId) {
            case 2:
                $this->command->info('🗂️  Running: FavoriteSeederTenant2');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Digital Agency / E-Commerce');
                $this->command->newLine();

                $this->call(FavoriteSeederTenant2::class);
                break;

            case 3:
                $this->command->info('🗂️  Running: FavoriteSeederTenant3');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Corporate Business');
                $this->command->newLine();

                $this->call(FavoriteSeederTenant3::class);
                break;

            case 4:
                $this->command->info('🗂️  Running: FavoriteSeederTenant4');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Educational Institution');
                $this->command->newLine();

                $this->call(FavoriteSeederTenant4::class);
                break;

            default:
                $this->command->warn("⚠️  No specific seeder found for Tenant ID: {$tenantId}");
                $this->command->info('💡 Creating default favorites with factory...');
                $this->command->newLine();

                $this->createDefaultPages();
                break;
        }

        $this->command->info('✅ Tenant database seeding completed');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Create default favorites for unknown tenants
     * Uses factory to generate basic favorite structure
     */
    private function createDefaultPages(): void
    {
        // Duplicate check
        if (Favorite::count() > 0) {
            $this->command->info('📋 Pages already exist, skipping default creation');
            return;
        }

        // Create homefavorite
        $homefavorite = Favorite::factory()
            ->active()
            ->create();

        $this->command->info('✓ Homefavorite created');

        // Create basic favorites
        $favorites = [
            'about' => Favorite::factory()->aboutPage(),
            'contact' => Favorite::factory()->contactPage(),
            'privacy' => Favorite::factory()->privacyPage(),
            'terms' => Favorite::factory()->termsPage(),
        ];

        foreach ($favorites as $type => $factory) {
            $factory->create();
            $this->command->info("✓ {$type} favorite created");
        }

        // Create additional random favorites for development
        Favorite::factory()
            ->simple()
            ->count(10)
            ->create();

        $this->command->info('✓ 10 random favorites created for development');
    }
}
