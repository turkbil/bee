<?php
namespace Modules\AI\App\Http\Controllers\Admin\Chat;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('ai::admin.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'context' => 'nullable|string',
            'module' => 'nullable|string',
            'entity_id' => 'nullable|integer',
            'prompt_id' => ['nullable', Rule::exists('central.ai_prompts', 'id')]
        ]);
        
        $options = [
            'context' => $request->context,
            'module' => $request->module,
            'entity_id' => $request->entity_id,
            'prompt_id' => $request->prompt_id
        ];
        
        $response = $this->aiService->ask($request->prompt, $options);
        
        if (!$response) {
            return response()->json([
                'success' => false,
                'message' => 'Yanıt alınamadı. Lütfen daha sonra tekrar deneyin veya yöneticinize başvurun.'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'response' => $response
        ]);
    }

    public function updateConversationPrompt(Request $request)
    {
        try {
            // JSON isteklerini işlemek için
            if ($request->isJson()) {
                $data = $request->json()->all();
                $conversationId = $data['conversation_id'] ?? null;
                $promptId = $data['prompt_id'] ?? null;
            } else {
                $conversationId = $request->input('conversation_id');
                $promptId = $request->input('prompt_id');
            }
            
            // Parametreleri kontrol et
            if (!$conversationId || !$promptId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konuşma ID ve Prompt ID zorunludur.'
                ], 400);
            }
            
            Log::info('Konuşma promptu güncelleme isteği', [
                'conversation_id' => $conversationId,
                'prompt_id' => $promptId,
                'user_id' => Auth::id(),
                'request_format' => $request->isJson() ? 'JSON' : 'FORM'
            ]);
            
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
            
            // Promptu bul
            $prompt = Prompt::find($promptId);
            
            if (!$prompt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen prompt bulunamadı.'
                ], 404);
            }
            
            // Aktif olmayan promptları kullanma
            if (!$prompt->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen prompt aktif değil.'
                ], 400);
            }
            
            // Konuşmayı güncelle
            $conversation->prompt_id = $promptId;
            $result = $conversation->save();
            
            Log::info('Konuşma promptu güncellendi', [
                'conversation_id' => $conversation->id,
                'prompt_id' => $promptId,
                'user_id' => Auth::id(),
                'result' => $result
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Konuşma promptu güncellendi.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Konuşma promptu güncellenirken hata', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Konuşma promptu güncellenirken bir hata oluştu.'
            ], 500);
        }
    }
}
