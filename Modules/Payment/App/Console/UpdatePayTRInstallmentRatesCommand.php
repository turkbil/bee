<?php

namespace Modules\Payment\App\Console;

use Illuminate\Console\Command;
use Modules\Payment\App\Services\PayTRInstallmentService;

class UpdatePayTRInstallmentRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:update-paytr-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PayTR taksit oranlarını günceller (günlük)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('PayTR taksit oranları güncelleniyor...');

        $service = app(PayTRInstallmentService::class);
        $result = $service->updateAllPayTRRates();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);

            if (isset($result['results'])) {
                $this->table(
                    ['Payment Method ID', 'Title', 'Status', 'Message'],
                    array_map(function ($item) {
                        return [
                            $item['payment_method_id'],
                            $item['title'],
                            $item['success'] ? '✅ Başarılı' : '❌ Hatalı',
                            $item['message']
                        ];
                    }, $result['results'])
                );
            }
        } else {
            $this->error('❌ ' . $result['message']);
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
