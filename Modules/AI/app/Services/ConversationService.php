<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Auth;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\DeepSeekService;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
// Removed Tenancy facade - using tenancy() helper instead

class ConversationService
{
    protected $deepSeekService;
    protected $aiTokenService;

    public function __construct(DeepSeekService $deepSeekService, AITokenService $aiTokenService = null)
    {
        $this->deepSeekService = $deepSeekService;
        $this->aiTokenService = $aiTokenService ?? app(AITokenService::class);
    }

    /**
     * Tenant'ın mevcut token bakiyesini kontrol et
     */
    public function checkTenantTokenBalance(int $requiredTokens = 0): bool
    {
        // Helper function kullanarak token kontrolü yap
        $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
        
        Log::info('Token kontrolü yapılıyor', [
            'tenant_id' => $tenantId,
            'required_tokens' => $requiredTokens,
            'is_central' => \App\Helpers\TenantHelpers::isCentral()
        ]);
        
        // AI token helper'ını kullan
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            Log::warning('Tenant bulunamadı', ['tenant_id' => $tenantId]);
            return false;
        }
        
        $canUse = can_use_ai_tokens($requiredTokens, $tenant);
        Log::info('Token kontrolü sonucu', ['can_use' => $canUse, 'balance' => ai_token_balance($tenant)]);
        
        return $canUse;
    }

    /**
     * Tenant'ın token bakiyesini getir
     */
    public function getTenantTokenBalance(?int $tenantId = null): int
    {
        $tenantId = $tenantId ?: \App\Helpers\TenantHelpers::getCurrentTenantId();
        
        // Admin sayfalarında tenant_id null olabilir, 1 numaralı tenant'ı kullan
        if (!$tenantId) {
            $tenantId = 1; // Default tenant for admin pages
        }

        // Tenant'ın toplam satın aldığı tokenlar
        $purchasedTokens = \Modules\AI\App\Models\AITokenPurchase::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->sum('token_amount');

        // Tenant'ın kullandığı tokenlar
        $usedTokens = \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenantId)
            ->sum('tokens_used');

        return max(0, $purchasedTokens - $usedTokens);
    }

    /**
     * Token kullanımını kaydet
     */
    public function recordTokenUsage(Conversation $conversation, Message $message, int $promptTokens, int $completionTokens, string $model = null): void
    {
        $totalTokens = $promptTokens + $completionTokens;
        
        // Helper kullanarak token kullanımını kaydet
        $tenant = \App\Models\Tenant::find($conversation->tenant_id);
        if ($tenant) {
            use_ai_tokens(
                $totalTokens, 
                'chat', 
                'AI Chat: ' . \Str::limit($message->content, 50),
                $conversation->id,
                $tenant
            );
            
            Log::info('Token kullanımı kaydedildi', [
                'tenant_id' => $conversation->tenant_id,
                'tokens_used' => $totalTokens,
                'remaining_balance' => ai_token_balance($tenant)
            ]);
        }

        // Ayrıca AI modülünün kendi tablosuna da kaydet
        \Modules\AI\App\Models\AITokenUsage::create([
            'tenant_id' => $conversation->tenant_id,
            'user_id' => $conversation->user_id,
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'tokens_used' => $totalTokens,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'usage_type' => 'chat',
            'model' => $model ?: 'deepseek-chat',
            'purpose' => $conversation->type ?? 'chat',
            'description' => 'AI Chat: ' . \Str::limit($message->content, 50),
            'used_at' => now(),
        ]);
    }

    public function createConversation(string $title, ?int $promptId = null): Conversation
    {
        $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1; // Admin için default tenant
        $conversation = Conversation::create([
            'title' => $title,
            'user_id' => Auth::id(),
            'tenant_id' => $tenantId,
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

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'tokens' => $tokens,
        ]);
        
        // Mesaj ekleme log'u
        if (function_exists('log_activity')) {
            log_activity($message, 'oluşturuldu');
        }
        
        return $message;
    }

    public function getAIResponse(Conversation $conversation, string $userMessage, bool $stream = false)
    {
        // Token kontrolü
        if (!$this->checkTenantTokenBalance(1000)) {
            return "Üzgünüm, tenant token bakiyeniz yetersiz. Lütfen token satın alınız.";
        }
    
        $userMsg = $this->addMessage($conversation, $userMessage, 'user');
    
        $messages = $this->deepSeekService->formatConversationMessages($conversation);
    
        if ($stream) {
            return $this->deepSeekService->ask($messages, true);
        } else {
            $aiResponse = $this->deepSeekService->ask($messages);
    
            if ($aiResponse) {
                $promptTokens = (int) (strlen($userMessage) / 4);
                $completionTokens = (int) (strlen($aiResponse) / 4);
                $totalTokens = $promptTokens + $completionTokens;
                
                $aiMessage = $this->addMessage($conversation, $aiResponse, 'assistant', $totalTokens);
                $aiMessage->prompt_tokens = $promptTokens;
                $aiMessage->completion_tokens = $completionTokens;
                $aiMessage->save();
                
                // Token kullanımını kaydet
                $this->recordTokenUsage($conversation, $aiMessage, $promptTokens, $completionTokens);
            }
    
            return $aiResponse;
        }
    }

    public function getStreamingAIResponse(Conversation $conversation, string $userMessage, callable $callback): Message
    {
        // Token kontrolü - tahmini 1000 token
        if (!$this->checkTenantTokenBalance(1000)) {
            $errorMsg = "Üzgünüm, tenant token bakiyeniz yetersiz. Lütfen token satın alınız.";
            $callback($errorMsg);
            return $this->addMessage($conversation, $errorMsg, 'assistant');
        }

        $userMsg = $this->addMessage($conversation, $userMessage, 'user');

        $messages = $this->deepSeekService->formatConversationMessages($conversation);

        $aiMessage = $this->addMessage($conversation, "", 'assistant', 0);
        
        $fullContent = '';
        $promptTokens = 0;
        $completionTokens = 0;
        
        $streamFunction = $this->deepSeekService->ask($messages, true);
        
        if (is_callable($streamFunction)) {
            $streamFunction(function ($content) use (&$fullContent, $callback, $aiMessage) {
                $fullContent .= $content;
                $callback($content);
                
                $aiMessage->content = $fullContent;
                $aiMessage->completion_tokens = (int) (strlen($fullContent) / 4);
                $aiMessage->save();
            });
        }
        
        if (empty($fullContent)) {
            $errorMsg = "Yanıt üretilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            $callback($errorMsg);
            
            $aiMessage->content = $errorMsg;
            $aiMessage->save();
            
            if (function_exists('log_activity')) {
                log_activity($aiMessage, 'hata');
            }
        } else {
            // Token kullanımını kaydet
            $promptTokens = (int) (strlen($userMessage) / 4);
            $completionTokens = (int) (strlen($fullContent) / 4);
            
            $aiMessage->prompt_tokens = $promptTokens;
            $aiMessage->completion_tokens = $completionTokens;
            $aiMessage->tokens = $promptTokens + $completionTokens;
            $aiMessage->save();
            
            // Token usage kayıt
            $this->recordTokenUsage($conversation, $aiMessage, $promptTokens, $completionTokens);
        }
        
        return $aiMessage;
    }

    public function updateConversation(Conversation $conversation, array $data): Conversation
    {
        $conversation->update($data);
        
        // Konuşma güncelleme log'u
        if (function_exists('log_activity')) {
            log_activity($conversation, 'güncellendi');
        }
        
        return $conversation;
    }
    
    public function getConversations(?int $limit = null)
    {
        $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
        
        $query = Conversation::where('user_id', Auth::id())
            ->where('tenant_id', $tenantId)
            ->orderBy('updated_at', 'desc');
            
        if ($limit) {
            return $query->take($limit)->get();
        }
        
        return $query->get();
    }
    
    public function deleteConversation(Conversation $conversation): bool
    {
        // Konuşma silme log'u
        if (function_exists('log_activity')) {
            log_activity($conversation, 'silindi');
        }
        
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