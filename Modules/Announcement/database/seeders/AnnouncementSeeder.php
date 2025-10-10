<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use App\Helpers\TenantHelpers;

/**
 * Announcement Module Master Seeder
 *
 * Context-aware orchestrator seeder.
 * Routes to appropriate seeder based on database context (Central or Tenant).
 *
 * Architecture:
 * - Central Database: AnnouncementSeederCentral (tr, en, ar)
 * - Tenant 2: AnnouncementSeederTenant2 (tr, en)
 * - Tenant 3: AnnouncementSeederTenant3 (tr, en)
 * - Tenant 4: AnnouncementSeederTenant4 (tr, en)
 *
 * @package Modules\Announcement\Database\Seeders
 */
class AnnouncementSeeder extends Seeder
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
        $this->command->info('📄 ANNOUNCEMENT MODULE SEEDER');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Central Database Seeding
        if ($isCentral) {
            $this->command->info('🌐 Context: CENTRAL DATABASE');
            $this->command->info('🗂️  Running: AnnouncementSeederCentral');
            $this->command->info('🌍 Languages: Turkish, English, Arabic');
            $this->command->newLine();

            $this->call(AnnouncementSeederCentral::class);

            $this->command->info('✅ Central database seeding completed');
            return;
        }

        // Tenant Database Seeding
        $this->command->info("🏢 Context: TENANT DATABASE (ID: {$tenantId})");

        switch ($tenantId) {
            case 2:
                $this->command->info('🗂️  Running: AnnouncementSeederTenant2');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Digital Agency / E-Commerce');
                $this->command->newLine();

                $this->call(AnnouncementSeederTenant2::class);
                break;

            case 3:
                $this->command->info('🗂️  Running: AnnouncementSeederTenant3');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Corporate Business');
                $this->command->newLine();

                $this->call(AnnouncementSeederTenant3::class);
                break;

            case 4:
                $this->command->info('🗂️  Running: AnnouncementSeederTenant4');
                $this->command->info('🌍 Languages: Turkish, English');
                $this->command->info('🎨 Theme: Educational Institution');
                $this->command->newLine();

                $this->call(AnnouncementSeederTenant4::class);
                break;

            default:
                $this->command->warn("⚠️  No specific seeder found for Tenant ID: {$tenantId}");
                $this->command->info('💡 Creating default announcements with factory...');
                $this->command->newLine();

                $this->createDefaultPages();
                break;
        }

        $this->command->info('✅ Tenant database seeding completed');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    /**
     * Create default announcements for unknown tenants
     * Uses factory to generate basic announcement structure
     */
    private function createDefaultPages(): void
    {
        // Duplicate check
        if (Announcement::count() > 0) {
            $this->command->info('📋 Pages already exist, skipping default creation');
            return;
        }

        // Create homeannouncement
        $homeannouncement = Announcement::factory()
            ->active()
            ->create();

        $this->command->info('✓ Homeannouncement created');

        // Create basic announcements
        $announcements = [
            'about' => Announcement::factory()->aboutPage(),
            'contact' => Announcement::factory()->contactPage(),
            'privacy' => Announcement::factory()->privacyPage(),
            'terms' => Announcement::factory()->termsPage(),
        ];

        foreach ($announcements as $type => $factory) {
            $factory->create();
            $this->command->info("✓ {$type} announcement created");
        }

        // Create additional random announcements for development
        Announcement::factory()
            ->simple()
            ->count(10)
            ->create();

        $this->command->info('✓ 10 random announcements created for development');
    }
}
