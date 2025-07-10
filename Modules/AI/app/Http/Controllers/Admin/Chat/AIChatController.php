<?php

namespace Modules\AI\App\Http\Controllers\Admin\Chat;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIResponseRepository;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\MarkdownService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AIChatController extends Controller
{
    protected $deepSeekService;
    protected $markdownService;
    protected $aiService;
    protected $aiResponseRepository;
    protected $conversationService;

    public function __construct(DeepSeekService $deepSeekService, MarkdownService $markdownService, ConversationService $conversationService, AIService $aiService = null, AIResponseRepository $aiResponseRepository = null)
    {
        $this->deepSeekService = $deepSeekService;
        $this->markdownService = $markdownService;
        $this->conversationService = $conversationService;
        $this->aiService = $aiService ?? app(AIService::class);
        $this->aiResponseRepository = $aiResponseRepository ?? app(AIResponseRepository::class);
    }

    public function index()
    {
        return view('ai::admin.chat.index');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|string',
        ]);

        try {
            $message = trim($request->message);
            $conversationId = $request->conversation_id;
            
            // Konuşma nesnesini bul veya oluştur
            if ($conversationId) {
                $conversation = Conversation::find($conversationId);
                if (!$conversation) {
                    // Yoksa yeni konuşma oluştur
                    $conversation = new Conversation();
                    $conversation->title = substr($message, 0, 30) . '...';
                    $conversation->user_id = Auth::id();
                    $conversation->tenant_id = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
                    $conversation->save();
                }
            } else {
                // Yeni konuşma oluştur
                $conversation = new Conversation();
                $conversation->title = substr($message, 0, 30) . '...';
                $conversation->user_id = Auth::id();
                $conversation->tenant_id = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
                $conversation->save();
            }
            
            // Kullanıcı mesajını kaydet
            $userMessage = new Message();
            $userMessage->conversation_id = $conversation->id;
            $userMessage->role = 'user';
            $userMessage->content = $message;
            $userMessage->tokens = strlen($message) / 4;
            $userMessage->save();
            
            // Kullanıcı mesajı için token kullanımını kaydet
            $userPromptTokens = (int) (strlen($message) / 4);
            $this->conversationService->recordTokenUsage($conversation, $userMessage, $userPromptTokens, 0, 'deepseek-chat');
            
            // Konuşma geçmişini oluştur
            $conversationHistory = [];
            $messages = $conversation->messages()->orderBy('created_at')->get();
            foreach ($messages as $msg) {
                $conversationHistory[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'timestamp' => $msg->created_at->toIso8601String(),
                ];
            }
            
            // DeepSeek API'ye istek gönder
            $response = $this->deepSeekService->generateCompletion($message, $conversationHistory);
            
            if (empty($response) || !isset($response['content'])) {
                Log::error('DeepSeek API boş yanıt döndü', [
                    'request' => $message,
                    'response' => $response,
                ]);
                
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'AI yanıt üretemedi. Lütfen tekrar deneyin.',
                    'response' => 'AI yanıt üretemedi.'
                ], 500);
            }
            
            // AI yanıtını kaydet
            $aiMessage = new Message();
            $aiMessage->conversation_id = $conversation->id;
            $aiMessage->role = 'assistant';
            $aiMessage->content = $response['content'];
            $aiMessage->tokens = strlen($response['content']) / 4;
            $aiMessage->save();
            
            // AI yanıtı için token kullanımını kaydet
            $aiCompletionTokens = (int) (strlen($response['content']) / 4);
            $this->conversationService->recordTokenUsage($conversation, $aiMessage, 0, $aiCompletionTokens, 'deepseek-chat');
            
            // Geriye uyumluluk için Redis cache'ini de güncelle
            $this->updateRedisCache($conversation->id, $conversationHistory, $response['content']);
            
            // Token bilgileri hesapla
            $totalTokensUsed = $userPromptTokens + $aiCompletionTokens;
            
            // Token kullanımını AIHelper sistemi ile kaydet
            $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
            ai_use_tokens($totalTokensUsed, 'chat', 'general_chat', $tenantId, [
                'conversation_id' => $conversation->id,
                'user_message_length' => strlen($message),
                'ai_response_length' => strlen($response['content']),
                'source' => 'admin_chat'
            ]);
            
            // İşlemi kaydet
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'conversation_id' => $conversation->id,
                    'message_length' => strlen($message),
                    'response_length' => strlen($response['content']),
                ])
                ->log('ai_message_sent');
            
            // Markdown kontrolü ve dönüşüm
            $content = $response['content'];
            $hasMarkdown = $this->markdownService->hasMarkdown($content);
            $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
            $newBalance = ai_get_token_balance($tenantId);
            
            return response()->json([
                'success' => true,
                'status' => 'success',
                'response' => $content,
                'content' => $content,
                'has_markdown' => $hasMarkdown,
                'html_content' => $hasMarkdown ? $this->markdownService->convertToHtml($content) : null,
                'conversation_id' => $conversation->id,
                'tokens_used' => $totalTokensUsed,
                'tokens_used_formatted' => ai_format_token_count($totalTokensUsed) . ' kullanıldı',
                'new_balance' => $newBalance,
                'new_balance_formatted' => ai_format_token_count($newBalance)
            ]);
        } catch (\Exception $e) {
            Log::error('AI mesaj gönderme hatası: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
                'response' => 'Sistem hatası oluştu.'
            ], 500);
        }
    }
        
    public function streamResponse(Request $request)
    {
        Log::info('StreamResponse metodu çağrıldı', ['request' => $request->all()]);
        
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|string',
            'prompt_id' => 'nullable|integer',
        ]);

        return response()->stream(function () use ($request) {
            Log::info('Stream function başlatılıyor');
            try {
                $message = trim($request->message);
                $conversationId = $request->conversation_id;
                $promptId = $request->prompt_id;
                
                Log::info('Stream parametreleri', [
                    'message' => $message,
                    'conversationId' => $conversationId,
                    'promptId' => $promptId,
                    'tenant_id' => tenancy()->tenant?->id
                ]);
                
                // Konuşma nesnesini bul veya oluştur (tenant-aware)
                if ($conversationId && is_numeric($conversationId)) {
                    $conversation = Conversation::where('tenant_id', tenancy()->tenant?->id)
                        ->find($conversationId);
                    Log::info('Mevcut konuşma aranıyor', ['found' => $conversation ? 'yes' : 'no']);
                }
                
                if (!isset($conversation) || !$conversation) {
                    Log::info('Yeni konuşma oluşturuluyor');
                    // Yeni konuşma oluştur
                    $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1; // Admin için default tenant
                    $conversation = new Conversation();
                    $conversation->title = substr($message, 0, 30) . '...';
                    $conversation->user_id = Auth::id();
                    $conversation->tenant_id = $tenantId;
                    $conversation->prompt_id = $promptId;
                    $conversation->save();
                    Log::info('Yeni konuşma oluşturuldu', ['id' => $conversation->id, 'tenant_id' => $tenantId]);
                }
                
                Log::info('Token kontrolü yapılıyor');
                // Token kontrolü yap
                if (!$this->conversationService->checkTenantTokenBalance(1000)) {
                    Log::info('Token yetersiz');
                    echo "event: error\n";
                    echo "data: " . json_encode(['error' => 'Token bakiyeniz yetersiz. Lütfen token satın alınız.']) . "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                Log::info('Token kontrolü geçti');
                
                Log::info('Stream API isteği başlatılıyor', [
                    'conversation_id' => $conversation->id,
                    'message_length' => strlen($message),
                    'has_api_key' => !empty($this->deepSeekService->getApiKey()),
                    'prompt_id' => $promptId,
                    'timestamp' => now()->toIso8601String(),
                ]);
                
                // Kullanıcı mesajını kaydet
                $userMessage = new Message();
                $userMessage->conversation_id = $conversation->id;
                $userMessage->role = 'user';
                $userMessage->content = $message;
                $userMessage->tokens = strlen($message) / 4;
                $userMessage->save();
                
                // Kullanıcı mesajı için token kullanımını kaydet
                $userPromptTokens = (int) (strlen($message) / 4);
                $this->conversationService->recordTokenUsage($conversation, $userMessage, $userPromptTokens, 0, 'deepseek-chat');
                
                // Konuşma geçmişini oluştur
                $conversationHistory = [];
                $messages = $conversation->messages()->orderBy('created_at')->get();
                foreach ($messages as $msg) {
                    $conversationHistory[] = [
                        'role' => $msg->role,
                        'content' => $msg->content,
                        'timestamp' => $msg->created_at->toIso8601String(),
                    ];
                }
                
                // DeepSeek API Stream yanıtını al
                $this->deepSeekService->streamCompletion($message, $conversationHistory, function ($chunk) {
                    echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                    ob_flush();
                    flush();
                }, $promptId);
                
                // Son AI yanıtını kaydet
                $fullResponse = $this->deepSeekService->getLastFullResponse();
                
                // AI yanıtını veritabanına kaydet
                $aiMessage = new Message();
                $aiMessage->conversation_id = $conversation->id;
                $aiMessage->role = 'assistant';
                $aiMessage->content = $fullResponse;
                $promptTokens = (int) (strlen($message) / 4);
                $completionTokens = (int) (strlen($fullResponse) / 4);
                $aiMessage->prompt_tokens = $promptTokens;
                $aiMessage->completion_tokens = $completionTokens;
                $aiMessage->tokens = $promptTokens + $completionTokens;
                $aiMessage->save();
                
                // Token kullanımını kaydet
                $this->conversationService->recordTokenUsage($conversation, $aiMessage, $promptTokens, $completionTokens, 'deepseek-chat');
                
                // Geriye uyumluluk için Redis cache'ini de güncelle
                $conversationHistory[] = [
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'timestamp' => now()->toIso8601String(),
                ];
                $this->updateRedisCache($conversation->id, $conversationHistory);
                
                // İşlemi kaydet
                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'conversation_id' => $conversation->id,
                        'message_length' => strlen($message),
                        'response_length' => strlen($fullResponse),
                        'prompt_id' => $promptId,
                    ])
                    ->log('ai_message_streamed');
                
                // Markdown kontrolü
                $hasMarkdown = $this->markdownService->hasMarkdown($fullResponse);
                $htmlContent = $hasMarkdown ? $this->markdownService->convertToHtml($fullResponse) : null;
                
                echo "event: complete\n";
                echo "data: " . json_encode([
                    'conversation_id' => $conversation->id,
                    'has_markdown' => $hasMarkdown,
                    'html_content' => $htmlContent,
                    'prompt_id' => $promptId,
                ]) . "\n\n";
            } catch (\Exception $e) {
                Log::error('AI stream hatası: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => Auth::id(),
                    'request' => $request->all(),
                ]);
                
                echo "event: error\n";
                echo "data: " . json_encode(['message' => 'Bir hata oluştu: ' . $e->getMessage()]) . "\n\n";
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }
    
    public function resetConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|string',
        ]);
        
        try {
            $conversationId = $request->conversation_id;
            
            // Konuşmayı bul
            $conversation = Conversation::find($conversationId);
            
            if ($conversation && $conversation->user_id == Auth::id()) {
                // Konuşmaya ait tüm mesajları sil
                Message::where('conversation_id', $conversation->id)->delete();
                
                // Geriye uyumluluk için Redis'i de temizle
                $this->clearRedisCache($conversation->id);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Konuşma sıfırlandı.',
                ]);
            }
            
            // Eğer veritabanında yoksa, eski yöntemle Redis'te arama
            $cachePrefix = 'ai_conversation:';
            
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $currentTenant = tenant();
                $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
            }
            
            // Redis'ten sil
            Cache::store('redis')->forget($cachePrefix . $conversationId);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Konuşma sıfırlandı.',
            ]);
        } catch (\Exception $e) {
            Log::error('AI konuşma sıfırlama hatası: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'conversation_id' => $request->conversation_id,
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Redis önbelleğini güncelle (geriye uyumluluk için)
     */
    protected function updateRedisCache($conversationId, $conversationHistory, $newResponseContent = null)
    {
        try {
            $cachePrefix = 'ai_conversation:';
            
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $currentTenant = tenant();
                $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
            }
            
            // Eğer yeni yanıt varsa ekle
            if ($newResponseContent !== null) {
                $conversationHistory[] = [
                    'role' => 'assistant',
                    'content' => $newResponseContent,
                    'timestamp' => now()->toIso8601String(),
                ];
            }
            
            // Konuşma geçmişini önbelleğe kaydet (24 saat süreyle)
            Cache::store('redis')->put($cachePrefix . $conversationId, $conversationHistory, 86400);
        } catch (\Exception $e) {
            Log::error('Redis cache güncelleme hatası: ' . $e->getMessage());
        }
    }
    
    /**
     * Redis önbelleğini temizle
     */
    protected function clearRedisCache($conversationId)
    {
        try {
            $cachePrefix = 'ai_conversation:';
            
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $currentTenant = tenant();
                $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
            }
            
            Cache::store('redis')->forget($cachePrefix . $conversationId);
        } catch (\Exception $e) {
            Log::error('Redis cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Konuşma promptunu güncelle
     */
    public function updateConversationPrompt(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'prompt_id' => 'required|integer',
        ]);

        try {
            $conversationId = $request->conversation_id;
            $promptId = $request->prompt_id;
            
            // Konuşmayı bul
            $conversation = Conversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konuşma bulunamadı veya erişim izniniz yok.'
                ], 404);
            }
            
            // Prompt'u kontrol et
            $prompt = \Modules\AI\App\Models\Prompt::where('id', $promptId)
                ->where('is_active', true)
                ->first();
                
            if (!$prompt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen prompt bulunamadı veya aktif değil.'
                ], 404);
            }
            
            // Konuşmanın prompt_id'sini güncelle
            $conversation->prompt_id = $promptId;
            $conversation->save();
            
            Log::info('Konuşma promptu güncellendi', [
                'conversation_id' => $conversationId,
                'old_prompt_id' => $conversation->getOriginal('prompt_id'),
                'new_prompt_id' => $promptId,
                'prompt_name' => $prompt->name,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Konuşma promptu başarıyla güncellendi.',
                'prompt_name' => $prompt->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Konuşma promptu güncelleme hatası: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}