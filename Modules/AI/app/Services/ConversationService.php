<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Auth;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\LimitService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    protected $deepSeekService;
    protected $limitService;

    public function __construct(DeepSeekService $deepSeekService, LimitService $limitService)
    {
        $this->deepSeekService = $deepSeekService;
        $this->limitService = $limitService;
    }

    public function createConversation(string $title, ?int $promptId = null): Conversation
    {
        $conversation = Conversation::create([
            'title' => $title,
            'user_id' => Auth::id(),
            'prompt_id' => $promptId,
        ]);
        
        if (function_exists('log_activity')) {
            log_activity($conversation, 'oluşturuldu');
        }
        
        return $conversation;
    }

    public function addMessage(Conversation $conversation, string $content, string $role = 'user', ?int $tokens = null): Message
    {
        if ($tokens === null) {
            $tokens = (int) (strlen($content) / 4);
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'tokens' => $tokens,
        ]);
    }

    public function getAIResponse(Conversation $conversation, string $userMessage, bool $stream = false)
    {
        if (!$this->limitService->checkLimits()) {
            return "Üzgünüm, kullanım limitinize ulaştınız.";
        }
    
        $this->addMessage($conversation, $userMessage, 'user');
    
        $messages = $this->deepSeekService->formatConversationMessages($conversation);
    
        if ($stream) {
            return $this->deepSeekService->ask($messages, true);
        } else {
            $aiResponse = $this->deepSeekService->ask($messages);
    
            if ($aiResponse) {
                $tokens = $this->deepSeekService->estimateTokens([['role' => 'assistant', 'content' => $aiResponse]]);
                
                $this->addMessage($conversation, $aiResponse, 'assistant', $tokens);
                
                $this->limitService->incrementUsage($tokens);
            }
    
            return $aiResponse;
        }
    }

    public function getStreamingAIResponse(Conversation $conversation, string $userMessage, callable $callback): Message
    {
        if (!$this->limitService->checkLimits()) {
            $callback("Üzgünüm, kullanım limitinize ulaştınız.");
            return $this->addMessage($conversation, "Üzgünüm, kullanım limitinize ulaştınız.", 'assistant');
        }

        $this->addMessage($conversation, $userMessage, 'user');

        $messages = $this->deepSeekService->formatConversationMessages($conversation);

        $aiMessage = $this->addMessage($conversation, "", 'assistant', 0);
        
        $fullContent = '';
        
        $streamFunction = $this->deepSeekService->ask($messages, true);
        
        if (is_callable($streamFunction)) {
            $streamFunction(function ($content) use (&$fullContent, $callback, $aiMessage) {
                $fullContent .= $content;
                $callback($content);
                
                $aiMessage->content = $fullContent;
                $aiMessage->tokens = (int) (strlen($fullContent) / 4);
                $aiMessage->save();
            });
        }
        
        if (empty($fullContent)) {
            $errorMsg = "Yanıt üretilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            $callback($errorMsg);
            
            $aiMessage->content = $errorMsg;
            $aiMessage->save();
        }
        
        $this->limitService->incrementUsage($aiMessage->tokens);
        
        return $aiMessage;
    }

    public function updateConversation(Conversation $conversation, array $data): Conversation
    {
        $conversation->update($data);
        return $conversation;
    }
    
    public function getConversations(?int $limit = null)
    {
        $query = Conversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
            
        if ($limit) {
            return $query->take($limit)->get();
        }
        
        return $query->get();
    }
    
    public function deleteConversation(Conversation $conversation): bool
    {
        return $conversation->delete();
    }
    
    /**
     * Veritabanından konuşma geçmişini alır
     * 
     * @param int $conversationId
     * @return array
     */
    public function getConversationHistory(int $conversationId): array
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();
            
        $history = [];
        
        foreach ($messages as $message) {
            $history[] = [
                'role' => $message->role,
                'content' => $message->content,
                'timestamp' => $message->created_at->toIso8601String(),
            ];
        }
        
        return $history;
    }
    
    /**
     * Redis önbelleğindeki konuşma geçmişini veritabanına aktarır
     * 
     * @param string $cacheKey
     * @param string $conversationId
     * @return bool
     */
    public function migrateRedisConversationToDatabase(string $cacheKey, string $conversationId): bool
    {
        try {
            $conversationHistory = Cache::store('redis')->get($cacheKey, []);
            
            if (empty($conversationHistory)) {
                return false;
            }
            
            // Konuşma nesnesini bul veya oluştur
            $conversation = Conversation::find($conversationId);
            
            if (!$conversation) {
                // Yeni konuşma oluştur
                $conversationTitle = "Konuşma";
                if (!empty($conversationHistory)) {
                    $firstMessage = $conversationHistory[0]['content'] ?? '';
                    $conversationTitle = substr($firstMessage, 0, 30) . '...';
                }
                
                $conversation = $this->createConversation($conversationTitle);
            }
            
            // Mesajları veritabanına aktar
            DB::beginTransaction();
            
            foreach ($conversationHistory as $message) {
                $this->addMessage(
                    $conversation,
                    $message['content'],
                    $message['role'],
                    strlen($message['content']) / 4
                );
            }
            
            DB::commit();
            
            // Redis önbelleğini temizle
            Cache::store('redis')->forget($cacheKey);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Redis konuşması aktarılırken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tüm Redis konuşmalarını veritabanına aktarır
     * 
     * @return array
     */
    public function migrateAllRedisConversationsToDatabase(): array
    {
        $report = [
            'success' => 0,
            'failed' => 0,
            'total' => 0,
        ];
        
        // Redis anahtarlarını al (ai_conversation:* ile başlayanlar)
        $keys = Cache::store('redis')->getRedis()->keys('ai_conversation:*');
        
        $report['total'] = count($keys);
        
        foreach ($keys as $key) {
            // Anahtar formatı: ai_conversation:{conversation_id}
            $conversationId = str_replace('ai_conversation:', '', $key);
            
            $success = $this->migrateRedisConversationToDatabase($key, $conversationId);
            
            if ($success) {
                $report['success']++;
            } else {
                $report['failed']++;
            }
        }
        
        // Tenant konuşmalarını da kontrol et
        $tenantKeys = Cache::store('redis')->getRedis()->keys('tenant:*:ai_conversation:*');
        
        $report['total'] += count($tenantKeys);
        
        foreach ($tenantKeys as $key) {
            // Anahtar formatı: tenant:{tenant_id}:ai_conversation:{conversation_id}
            $parts = explode(':', $key);
            $conversationId = end($parts);
            
            $success = $this->migrateRedisConversationToDatabase($key, $conversationId);
            
            if ($success) {
                $report['success']++;
            } else {
                $report['failed']++;
            }
        }
        
        return $report;
    }
}