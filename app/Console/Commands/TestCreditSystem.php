<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\AI\App\Services\AICreditService;
use App\Models\User;

class TestCreditSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:credit-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AI Credit System Integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing AI Credit System Integration...');

        try {
            $creditService = app(AICreditService::class);
            $this->info('✅ AICreditService instance created successfully');

            // Test getCurrentBalance method
            $balance = $creditService->getCurrentBalance(1);
            $this->info("✅ getCurrentBalance(1): {$balance} credits");

            // Test consumeCredits method with translation category
            $this->info('Testing credit consumption for translation...');
            
            $creditService->consumeCredits(1, 2.5, 'translation', [
                'operation' => 'field_translation',
                'field' => 'test_field',
                'source_lang' => 'tr',
                'target_lang' => 'en'
            ]);
            
            $this->info('✅ Credits consumed successfully!');
            
            // Check balance after consumption
            $newBalance = $creditService->getCurrentBalance(1);
            $this->info("✅ New balance: {$newBalance} credits");
            
            $consumed = $balance - $newBalance;
            $this->info("✅ Credits consumed: {$consumed}");
            
            $this->info('🎉 AI Credit System Integration Test PASSED!');
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
