<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\SimpleFlowCopyService;

class SetupTenantAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-ai
                            {tenant : Target tenant ID}
                            {--from=2 : Source tenant ID to copy from (default: 2)}
                            {--overwrite : Overwrite existing directives}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup AI for a tenant by copying flow and directives from another tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetTenantId = (int) $this->argument('tenant');
        $sourceTenantId = (int) $this->option('from');
        $overwrite = $this->option('overwrite');

        $this->info("Setting up AI for Tenant {$targetTenantId}");
        $this->info("Copying from Tenant {$sourceTenantId}");

        $copyService = new SimpleFlowCopyService();

        // 1. Copy flow
        $this->info('Copying flow...');
        $flowCopied = $copyService->copyFlow($sourceTenantId, $targetTenantId);

        if ($flowCopied) {
            $this->info('✅ Flow copied successfully');
        } else {
            $this->error('❌ Failed to copy flow');
        }

        // 2. Copy directives
        $this->info('Copying directives...');
        $directivesCopied = $copyService->copyDirectives($sourceTenantId, $targetTenantId, $overwrite);

        $this->info("✅ {$directivesCopied} directives copied");

        // 3. Summary
        $this->newLine();
        $this->table(
            ['Item', 'Status'],
            [
                ['Flow', $flowCopied ? '✅ Copied' : '❌ Failed'],
                ['Directives', "{$directivesCopied} copied"],
            ]
        );

        $this->info("AI setup completed for Tenant {$targetTenantId}!");

        return Command::SUCCESS;
    }
}