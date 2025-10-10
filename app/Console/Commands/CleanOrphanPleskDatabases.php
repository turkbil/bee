<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use App\Models\Tenant;

class CleanOrphanPleskDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plesk:clean-orphan-databases {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean orphan tenant databases from Plesk (databases that exist in Plesk but tenant deleted)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for orphan Plesk database records...');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️ DRY RUN MODE - No actual deletion will occur');
        }

        try {
            // Plesk'ten tüm tenant database'lerini al
            $result = Process::timeout(30)->run('plesk db "SELECT name FROM data_bases WHERE name LIKE \'tenant_%\'"');

            if (!$result->successful()) {
                $this->error('❌ Plesk query failed: ' . $result->errorOutput());
                return 1;
            }

            // Parse Plesk output
            $lines = array_filter(explode("\n", $result->output()));
            $pleskDatabases = [];

            foreach ($lines as $line) {
                if (preg_match('/\|\s*(tenant_\w+)\s*\|/', $line, $matches)) {
                    $pleskDatabases[] = $matches[1];
                }
            }

            if (empty($pleskDatabases)) {
                $this->info('✅ No tenant databases found in Plesk');
                return 0;
            }

            $this->info('Found ' . count($pleskDatabases) . ' tenant database(s) in Plesk');

            // Laravel'den mevcut tenant database'lerini al
            $existingDatabases = Tenant::pluck('tenancy_db_name')->toArray();
            $this->info('Found ' . count($existingDatabases) . ' active tenant(s) in Laravel');

            // Orphan database'leri bul
            $orphanDatabases = array_diff($pleskDatabases, $existingDatabases);

            if (empty($orphanDatabases)) {
                $this->info('✅ No orphan databases found. All Plesk records are valid.');
                return 0;
            }

            $this->warn('Found ' . count($orphanDatabases) . ' orphan database(s):');

            $deletedCount = 0;
            $failedCount = 0;

            foreach ($orphanDatabases as $dbName) {
                $this->line("  - {$dbName}");

                if (!$isDryRun) {
                    // Sudo ile sil (root yetkisi gerekiyor)
                    $deleteResult = Process::timeout(10)->run("sudo plesk db \"DELETE FROM data_bases WHERE name = '{$dbName}'\"");

                    if ($deleteResult->successful()) {
                        $this->info("    ✅ Deleted from Plesk");
                        $deletedCount++;
                    } else {
                        $this->error("    ❌ Failed to delete: " . substr($deleteResult->errorOutput(), 0, 100));
                        $failedCount++;
                    }
                }
            }

            if ($isDryRun) {
                $this->warn('⚠️ DRY RUN - Run without --dry-run to actually delete these databases');
            } else {
                $this->info("\n📊 Summary:");
                $this->info("  ✅ Deleted: {$deletedCount}");
                if ($failedCount > 0) {
                    $this->warn("  ❌ Failed: {$failedCount}");
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
