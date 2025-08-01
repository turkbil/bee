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
                    $conversation->title = mb_substr($message, 0, 25) . '...';
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
            $this->conversationService->recordCreditUsage($conversation, $userMessage, $userPromptTokens, 0, $this->getCurrentProviderModel());
            
            // Konuşma geçmişini oluştur (OpenAI formatına uygun)
            $conversationHistory = [];
            $messages = $conversation->messages()->orderBy('created_at')->get();
            foreach ($messages as $msg) {
                $conversationHistory[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            }
            
            // Modern AIService ile provider-aware request
            $response = $this->aiService->ask($message, ['source' => 'admin_chat']);
            
            if (empty($response) || is_string($response) && str_contains($response, 'üzgünüm')) {
                Log::error('AI API boş yanıt döndü', [
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
            $aiMessage->content = $response;
            $aiMessage->tokens = strlen($response) / 4;
            $aiMessage->save();
            
            // AI yanıtı için token kullanımını kaydet
            $aiCompletionTokens = (int) (strlen($response) / 4);
            $this->conversationService->recordCreditUsage($conversation, $aiMessage, 0, $aiCompletionTokens, $this->getCurrentProviderModel());
            
            // Geriye uyumluluk için Redis cache'ini de güncelle
            $this->updateRedisCache($conversation->id, $conversationHistory, $response);
            
            // Token bilgileri hesapla
            $totalTokensUsed = $userPromptTokens + $aiCompletionTokens;
            
            // ARTIK KULLANILMIYOR - ai_use_calculated_credits() AIService'de otomatik çalışıyor
            // Credit kullanımı AIService.ask() içinde gerçek token bilgileri ile yapılacak
            $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
            
            // İşlemi kaydet
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'conversation_id' => $conversation->id,
                    'message_length' => strlen($message),
                    'response_length' => strlen($response),
                ])
                ->log('ai_message_sent');
            
            // Markdown kontrolü ve dönüşüm
            $content = $response;
            $hasMarkdown = $this->markdownService->hasMarkdown($content);
            $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
            $newBalance = ai_get_credit_balance($tenantId);
            
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
                'new_balance_formatted' => number_format($newBalance, 4) . ' kredi'
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
                $conversation = null;
                if ($conversationId) {
                    $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1;
                    
                    // Numeric ID kontrolü (database ID)
                    if (is_numeric($conversationId)) {
                        $conversation = Conversation::where('tenant_id', $tenantId)
                            ->find($conversationId);
                    } else {
                        // String hash ID kontrolü (frontend session ID)
                        $conversation = Conversation::where('tenant_id', $tenantId)
                            ->where('session_id', $conversationId)
                            ->first();
                    }
                    
                    Log::info('Mevcut konuşma aranıyor', [
                        'conversation_id' => $conversationId,
                        'tenant_id' => $tenantId,
                        'id_type' => is_numeric($conversationId) ? 'database_id' : 'session_hash',
                        'found' => $conversation ? 'yes' : 'no',
                        'conversation_db_id' => $conversation?->id
                    ]);
                }
                
                if (!isset($conversation) || !$conversation) {
                    Log::info('Yeni konuşma oluşturuluyor');
                    // Yeni konuşma oluştur
                    $tenantId = \App\Helpers\TenantHelpers::getCurrentTenantId() ?: 1; // Admin için default tenant
                    $conversation = new Conversation();
                    $conversation->title = mb_substr($message, 0, 25) . '...';
                    $conversation->user_id = Auth::id();
                    $conversation->tenant_id = $tenantId;
                    $conversation->prompt_id = $promptId;
                    $conversation->session_id = $conversationId; // Frontend session ID'yi kaydet
                    $conversation->save();
                    Log::info('Yeni konuşma oluşturuldu', [
                        'id' => $conversation->id, 
                        'tenant_id' => $tenantId,
                        'session_id' => $conversationId
                    ]);
                }
                
                Log::info('Kredi kontrolü yapılıyor');
                // Kredi kontrolü yap
                if (!$this->conversationService->checkTenantCreditBalance(1.0)) {
                    Log::info('Kredi yetersiz');
                    echo "event: error\n";
                    echo "data: " . json_encode(['error' => 'Kredi bakiyeniz yetersiz. Lütfen kredi satın alınız.']) . "\n\n";
                    ob_flush();
                    flush();
                    return;
                }
                Log::info('Kredi kontrolü geçti');
                
                Log::info('Stream API isteği başlatılıyor', [
                    'conversation_id' => $conversation->id,
                    'message_length' => strlen($message),
                    'has_api_key' => false, // AIService otomatik provider seçecek
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
                $this->conversationService->recordCreditUsage($conversation, $userMessage, $userPromptTokens, 0, $this->getCurrentProviderModel());
                
                // Konuşma geçmişini oluştur (OpenAI formatına uygun)
                $conversationHistory = [];
                $messages = $conversation->messages()->orderBy('created_at')->get();
                foreach ($messages as $msg) {
                    $conversationHistory[] = [
                        'role' => $msg->role,
                        'content' => $msg->content,
                    ];
                }
                
                // AI Service ile Stream yanıtını al (provider otomatik seçilir)
                $fullResponse = $this->aiService->askStream($message, [
                    'conversation_history' => $conversationHistory,
                    'prompt_id' => $promptId
                ], function ($chunk) {
                    echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                    ob_flush();
                    flush();
                });
                
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
                $this->conversationService->recordCreditUsage($conversation, $aiMessage, $promptTokens, $completionTokens, $this->getCurrentProviderModel());
                
                // Geriye uyumluluk için Redis cache'ini de güncelle
                $conversationHistory[] = [
                    'role' => 'assistant',
                    'content' => $fullResponse,
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

    /**
     * Şu anda aktif olan provider'ın model bilgisini al
     */
    private function getCurrentProviderModel(): string
    {
        try {
            $defaultProvider = \Modules\AI\App\Models\AIProvider::getDefault();
            if ($defaultProvider) {
                return $defaultProvider->name . '/' . $defaultProvider->default_model;
            }
            
            return 'unknown/unknown';
        } catch (\Exception $e) {
            return 'unknown/error';
        }
    }
    
    /**
     * Execute AI Widget Feature
     * Global AI Widget System için
     */
    public function executeWidgetFeature(Request $request)
    {
        try {
            Log::info('🚀 AI Widget Feature Request', $request->all());
            
            $featureSlug = $request->input('feature_slug');
            $context = $request->input('context', 'page');
            $entityId = $request->input('entity_id');
            $entityType = $request->input('entity_type', 'page');
            $currentData = $request->input('current_data', []);
            
            // Validation
            if (!$featureSlug) {
                return response()->json([
                    'success' => false,
                    'error' => 'Feature slug gerekli'
                ], 400);
            }
            
            // Context-based data preparation
            $contextData = $this->prepareContextData($context, $entityId, $currentData);
            
            // Execute AI feature through repository
            $result = $this->aiResponseRepository->executeRequest('widget_feature', [
                'feature_slug' => $featureSlug,
                'context' => $context,
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'data' => $contextData,
                'user_id' => Auth::id()
            ]);
            
            if ($result['success']) {
                Log::info('✅ AI Widget Feature completed', [
                    'feature' => $featureSlug,
                    'context' => $context,
                    'tokens' => $result['tokens_used'] ?? 0
                ]);
                
                return response()->json([
                    'success' => true,
                    'response' => $result['response'],
                    'formatted_response' => $result['formatted_response'],
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'feature' => [
                        'slug' => $featureSlug,
                        'context' => $context
                    ],
                    'suggestions' => $this->extractSuggestions($result['response'])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'AI feature hatası'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('AI Widget Feature Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'AI widget servisi şu anda kullanılamıyor.'
            ], 500);
        }
    }
    
    /**
     * Prepare context-specific data
     */
    private function prepareContextData(string $context, $entityId, array $currentData): array
    {
        $contextData = $currentData;
        
        // Context-specific data enrichment
        switch ($context) {
            case 'page':
                if ($entityId) {
                    // Page specific data loading
                    try {
                        $page = \Modules\Page\App\Models\Page::find($entityId);
                        if ($page) {
                            $contextData['existing_content'] = $page->multilang_data ?? [];
                            $contextData['page_type'] = 'existing';
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not load page data: ' . $e->getMessage());
                    }
                } else {
                    $contextData['page_type'] = 'new';
                }
                break;
                
            case 'portfolio':
                // Portfolio specific data
                $contextData['context_type'] = 'portfolio';
                break;
                
            case 'blog':
                // Blog specific data  
                $contextData['context_type'] = 'blog';
                break;
        }
        
        return $contextData;
    }
    
    /**
     * Extract actionable suggestions from AI response
     */
    private function extractSuggestions(string $response): array
    {
        $suggestions = [];
        
        // Extract title suggestions
        if (preg_match('/(?:title|başlık)[:\s]*["\']?([^"\'\n]+)["\']?/i', $response, $matches)) {
            $suggestions['title'] = trim($matches[1]);
        }
        
        // Extract meta description suggestions
        if (preg_match('/(?:meta|açıklama)[:\s]*["\']?([^"\'\n]{50,160})["\']?/i', $response, $matches)) {
            $suggestions['meta_description'] = trim($matches[1]);
        }
        
        // Extract keywords
        if (preg_match_all('/(?:anahtar|kelime|keyword)[:\s]*["\']?([^"\'\n]+)["\']?/i', $response, $matches)) {
            $keywords = [];
            foreach ($matches[1] as $match) {
                $keywords = array_merge($keywords, array_map('trim', explode(',', $match)));
            }
            $suggestions['keywords'] = array_unique(array_filter($keywords));
        }
        
        return $suggestions;
    }
}