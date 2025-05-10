<?php

namespace Modules\AI\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\MarkdownService;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $deepSeekService;
    protected $markdownService;
    protected $aiService;

    public function __construct(DeepSeekService $deepSeekService, MarkdownService $markdownService, AIService $aiService = null)
    {
        $this->deepSeekService = $deepSeekService;
        $this->markdownService = $markdownService;
        $this->aiService = $aiService ?? app(AIService::class);
    }

    public function index()
    {
        return view('ai::ai.chat');
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
                    $conversation->save();
                }
            } else {
                // Yeni konuşma oluştur
                $conversation = new Conversation();
                $conversation->title = substr($message, 0, 30) . '...';
                $conversation->user_id = Auth::id();
                $conversation->save();
            }
            
            // Kullanıcı mesajını kaydet
            $userMessage = new Message();
            $userMessage->conversation_id = $conversation->id;
            $userMessage->role = 'user';
            $userMessage->content = $message;
            $userMessage->tokens = strlen($message) / 4;
            $userMessage->save();
            
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
                    'status' => 'error',
                    'message' => 'AI yanıt üretemedi. Lütfen tekrar deneyin.',
                ], 500);
            }
            
            // AI yanıtını kaydet
            $aiMessage = new Message();
            $aiMessage->conversation_id = $conversation->id;
            $aiMessage->role = 'assistant';
            $aiMessage->content = $response['content'];
            $aiMessage->tokens = strlen($response['content']) / 4;
            $aiMessage->save();
            
            // Geriye uyumluluk için Redis cache'ini de güncelle
            $this->updateRedisCache($conversation->id, $conversationHistory, $response['content']);
            
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
            
            return response()->json([
                'status' => 'success',
                'content' => $content,
                'has_markdown' => $hasMarkdown,
                'html_content' => $hasMarkdown ? $this->markdownService->convertToHtml($content) : null,
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Exception $e) {
            Log::error('AI mesaj gönderme hatası: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
        
    public function streamResponse(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|string',
            'prompt_id' => 'nullable|integer',
        ]);

        return response()->stream(function () use ($request) {
            try {
                $message = trim($request->message);
                $conversationId = $request->conversation_id;
                $promptId = $request->prompt_id;
                
                // Konuşma nesnesini bul veya oluştur
                if ($conversationId && is_numeric($conversationId) && Conversation::find($conversationId)) {
                    $conversation = Conversation::find($conversationId);
                } else {
                    // Yeni konuşma oluştur
                    $conversation = new Conversation();
                    $conversation->title = substr($message, 0, 30) . '...';
                    $conversation->user_id = Auth::id();
                    $conversation->prompt_id = $promptId;
                    $conversation->save();
                }
                
                Log::info('Stream API isteği başlatılıyor', [
                    'conversation_id' => $conversation->id,
                    'message_length' => strlen($message),
                    'has_api_key' => !empty($this->deepSeekService->getApiKey()),
                    'prompt_id' => $promptId,
                ]);
                
                // Kullanıcı mesajını kaydet
                $userMessage = new Message();
                $userMessage->conversation_id = $conversation->id;
                $userMessage->role = 'user';
                $userMessage->content = $message;
                $userMessage->tokens = strlen($message) / 4;
                $userMessage->save();
                
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
                $aiMessage->tokens = strlen($fullResponse) / 4;
                $aiMessage->save();
                
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
}