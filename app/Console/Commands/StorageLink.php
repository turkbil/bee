<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link
                            {--relative : Create the symbolic link using relative paths}
                            {--force : Recreate existing symbolic links}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create symbolic links configured in filesystems.php with automatic tenant owner fix';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $relative = $this->option('relative');

        foreach ($this->links() as $link => $target) {
            if (file_exists($link) && !$this->option('force')) {
                $this->components->error("The [$link] link already exists.");
                continue;
            }

            if (is_link($link)) {
                File::delete($link);
            }

            if ($relative) {
                File::relativeLink($target, $link);
            } else {
                File::link($target, $link);
            }

            $this->components->info("The [$link] link has been connected to [$target].");
        }

        // 🔧 AUTOMATIC FIX: Tenant symlink owners
        $this->fixTenantSymlinkOwners();

        $this->components->info('The links have been created.');

        return 0;
    }

    /**
     * Get the symbolic links configured for the application.
     *
     * @return array
     */
    protected function links()
    {
        return $this->laravel['config']->get('filesystems.links') ??
            [public_path('storage') => storage_path('app/public')];
    }

    /**
     * Fix tenant symlink owners to match nginx disable_symlinks if_not_owner requirement.
     *
     * @return void
     */
    protected function fixTenantSymlinkOwners()
    {
        $publicStoragePath = public_path('storage');

        if (!is_dir($publicStoragePath)) {
            return;
        }

        // Get the owner of the target directory (e.g., tuufi.com_:psaserv)
        $targetOwner = posix_getpwuid(fileowner($publicStoragePath))['name'] ?? null;
        $targetGroup = posix_getgrgid(filegroup($publicStoragePath))['name'] ?? null;

        if (!$targetOwner || !$targetGroup) {
            $this->components->warn('Could not determine target owner/group for symlink fix.');
            return;
        }

        // Find all tenant* symlinks
        $tenantSymlinks = glob($publicStoragePath . '/tenant*', GLOB_ONLYDIR);

        if (empty($tenantSymlinks)) {
            return;
        }

        $this->components->info('');
        $this->components->info('🔧 Fixing tenant symlink owners...');

        foreach ($tenantSymlinks as $symlink) {
            if (!is_link($symlink)) {
                continue;
            }

            $symlinkName = basename($symlink);

            // Change symlink owner (not the target)
            $command = "chown -h {$targetOwner}:{$targetGroup} " . escapeshellarg($symlink);
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->components->info("  ✅ Fixed owner for: {$symlinkName} → {$targetOwner}:{$targetGroup}");
            } else {
                $this->components->warn("  ⚠️  Could not fix owner for: {$symlinkName}");
            }
        }

        $this->components->info('🎯 Tenant symlink owners fixed successfully!');
    }
}
