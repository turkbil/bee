<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleSlugService;

class ClearModuleCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all module slug caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing module slug caches...');
        
        ModuleSlugService::clearCache();
        
        $this->info('✅ Module slug caches cleared successfully!');
        
        // Test current values
        $this->info('');
        $this->info('Current Portfolio slugs:');
        $this->info('- Index: ' . ModuleSlugService::getSlug('Portfolio', 'index'));
        $this->info('- Show: ' . ModuleSlugService::getSlug('Portfolio', 'show'));
        $this->info('- Category: ' . ModuleSlugService::getSlug('Portfolio', 'category'));
        
        return Command::SUCCESS;
    }
}