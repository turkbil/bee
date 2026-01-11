<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Async Message Saver Job
 *
 * Kullanıcıya yanıt verildikten SONRA mesajı kaydet
 * afterResponse() ile çalışır
 */
class SaveConversationMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversationId;
    protected $messageData;

    public function __construct(int $conversationId, array $messageData)
    {
        $this->conversationId = $conversationId;
        $this->messageData = $messageData;
    }

    public function handle(): void
    {
        try {
            Log::info('💾 Saving message (async)', [
                'conversation_id' => $this->conversationId
            ]);

            // Mesajı kaydet
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $this->conversationId,
                'role' => $this->messageData['role'],
                'content' => $this->messageData['content'],
                'tokens_used' => $this->messageData['tokens_used'] ?? null,
                'model' => $this->messageData['model'] ?? null,
                'created_at' => now()
            ]);

            Log::info('✅ Message saved (async)');

        } catch (\Exception $e) {
            Log::error('❌ Failed to save message (async)', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
