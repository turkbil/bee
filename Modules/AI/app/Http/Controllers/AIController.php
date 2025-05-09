<?php

namespace Modules\AI\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
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
            $conversationId = $request->conversation_id ?? md5(time() . rand(1000, 9999));
            
            // Tenant bazlı önbellekleme anahtarı oluştur
            $currentTenant = null;
            $cachePrefix = 'ai_conversation:';
            
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $currentTenant = tenant();
                $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
            }
            
            // Önceki konuşma geçmişini al
            $conversationHistory = Cache::store('redis')->get($cachePrefix . $conversationId, []);
            
            // Yeni kullanıcı mesajını konuşma geçmişine ekle
            $conversationHistory[] = [
                'role' => 'user',
                'content' => $message,
                'timestamp' => now()->toIso8601String(),
            ];
            
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
            
            // AI yanıtını konuşma geçmişine ekle
            $conversationHistory[] = [
                'role' => 'assistant',
                'content' => $response['content'],
                'timestamp' => now()->toIso8601String(),
            ];
            
            // Konuşma geçmişini önbelleğe kaydet (24 saat süreyle)
            Cache::store('redis')->put($cachePrefix . $conversationId, $conversationHistory, 86400);
            
            // İşlemi kaydet
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'conversation_id' => $conversationId,
                    'tenant_id' => $currentTenant?->id,
                    'message_length' => strlen($message),
                    'response_length' => strlen($response['content']),
                ])
                ->log('ai_message_sent');
            
            return response()->json([
                'status' => 'success',
                'content' => $response['content'],
                'conversation_id' => $conversationId,
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
        ]);

        return response()->stream(function () use ($request) {
            try {
                $message = trim($request->message);
                $conversationId = $request->conversation_id ?? md5(time() . rand(1000, 9999));
                
                // Tenant bazlı önbellekleme anahtarı oluştur
                $currentTenant = null;
                $cachePrefix = 'ai_conversation:';
                
                if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                    $currentTenant = tenant();
                    $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
                }
                
                Log::info('Stream API isteği başlatılıyor', [
                    'tenant_id' => $currentTenant?->id, 
                    'message_length' => strlen($message),
                    'has_api_key' => !empty($this->deepSeekService->getApiKey())
                ]);
                
                // Önceki konuşma geçmişini al
                $conversationHistory = Cache::store('redis')->get($cachePrefix . $conversationId, []);
                
                // Yeni kullanıcı mesajını konuşma geçmişine ekle
                $conversationHistory[] = [
                    'role' => 'user',
                    'content' => $message,
                    'timestamp' => now()->toIso8601String(),
                ];
                
                // DeepSeek API Stream yanıtını al
                $this->deepSeekService->streamCompletion($message, $conversationHistory, function ($chunk) {
                    echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                    ob_flush();
                    flush();
                });
                
                // Son AI yanıtını konuşma geçmişine ekleyebilmek için tüm yanıtı toplama
                $fullResponse = $this->deepSeekService->getLastFullResponse();
                
                // AI yanıtını konuşma geçmişine ekle
                $conversationHistory[] = [
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'timestamp' => now()->toIso8601String(),
                ];
                
                // Konuşma geçmişini önbelleğe kaydet (24 saat süreyle)
                Cache::store('redis')->put($cachePrefix . $conversationId, $conversationHistory, 86400);
                
                // İşlemi kaydet
                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'conversation_id' => $conversationId,
                        'tenant_id' => $currentTenant?->id,
                        'message_length' => strlen($message),
                        'response_length' => strlen($fullResponse),
                    ])
                    ->log('ai_message_streamed');
                
                echo "event: complete\n";
                echo "data: " . json_encode(['conversation_id' => $conversationId]) . "\n\n";
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
            
            // Tenant bazlı önbellekleme anahtarı oluştur
            $cachePrefix = 'ai_conversation:';
            
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                $currentTenant = tenant();
                $cachePrefix = "tenant:{$currentTenant->id}:ai_conversation:";
            }
            
            // Konuşma geçmişini önbellekten sil
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
}