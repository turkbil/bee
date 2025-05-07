<?php
namespace Modules\AI\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Conversation;
use App\Services\ThemeService;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $aiService;
    protected $themeService;

    public function __construct(AIService $aiService, ThemeService $themeService)
    {
        $this->aiService = $aiService;
        $this->themeService = $themeService;
    }

    public function index()
    {
        $conversations = $this->aiService->conversations()->getConversations(10);
        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'ai');
            return view($viewPath, compact('conversations'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('ai::front.index', compact('conversations'));
        }
    }

    public function chat($id = null)
    {
        $conversation = null;
        
        if ($id) {
            $conversation = Conversation::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }
        
        $conversations = $this->aiService->conversations()->getConversations(10);
        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('chat', 'ai');
            return view($viewPath, compact('conversation', 'conversations'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('ai::front.chat', compact('conversation', 'conversations'));
        }
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:ai_conversations,id'
        ]);
        
        $message = $request->message;
        $conversationId = $request->conversation_id;
        
        if ($conversationId) {
            $conversation = Conversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $response = $this->aiService->conversations()->getAIResponse($conversation, $message);
        } else {
            // Yeni konuşma oluştur
            $title = substr($message, 0, 30) . '...';
            $conversation = $this->aiService->conversations()->createConversation($title);
            
            $response = $this->aiService->conversations()->getAIResponse($conversation, $message);
        }
        
        return response()->json([
            'success' => true,
            'response' => $response,
            'conversation_id' => $conversation->id
        ]);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'prompt_id' => 'nullable|exists:ai_prompts,id'
        ]);
        
        $conversation = $this->aiService->conversations()->createConversation(
            $request->title,
            $request->prompt_id
        );
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }

    public function deleteConversation($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $this->aiService->conversations()->deleteConversation($conversation);
        
        return response()->json([
            'success' => true
        ]);
    }
}