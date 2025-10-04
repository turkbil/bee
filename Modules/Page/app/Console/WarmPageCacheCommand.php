<?php

namespace Modules\Page\App\Console;

use Illuminate\Console\Command;
use Modules\Page\App\Repositories\PageRepository;

class WarmPageCacheCommand extends Command
{
    protected $signature = 'page:cache:warm';
    protected $description = 'Warm up page cache';

    public function __construct(
        private readonly PageRepository $pageRepository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Warming up page cache...');

        // Active pages cache
        $this->pageRepository->getActive();
        $this->info('✅ Active pages cached');

        // Homepage cache
        $homepage = $this->pageRepository->getHomepage();
        if ($homepage) {
            $this->info('✅ Homepage cached');
        } else {
            $this->warn('⚠️  No homepage found');
        }

        $this->info('Page cache warmed successfully!');

        return self::SUCCESS;
    }
}
