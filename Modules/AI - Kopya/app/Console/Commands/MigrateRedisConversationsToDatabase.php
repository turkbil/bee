<?php

namespace Modules\AI\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\AI\App\Services\ConversationService;

class MigrateRedisConversationsToDatabase extends Command
{
    protected $signature = 'ai:migrate-conversations';
    
    protected $description = 'Redis\'teki konuşmaları veritabanına aktarır';
    
    protected $conversationService;
    
    public function __construct(ConversationService $conversationService)
    {
        parent::__construct();
        $this->conversationService = $conversationService;
    }
    
    public function handle()
    {
        $this->info('Redis konuşmaları veritabanına aktarılıyor...');
        
        $report = $this->conversationService->migrateAllRedisConversationsToDatabase();
        
        $this->info("İşlem tamamlandı:");
        $this->info("Toplam: {$report['total']}");
        $this->info("Başarılı: {$report['success']}");
        $this->info("Başarısız: {$report['failed']}");
        
        return 0;
    }
}