<?php
namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('ai::admin.conversations.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->get();
        
        return view('ai::admin.conversations.show', compact('conversation', 'messages'));
    }

    public function delete($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        // İlişkili mesajları sil
        Message::where('conversation_id', $conversation->id)->delete();
        
        // Konuşmayı sil
        $conversation->delete();
        
        return redirect()->route('admin.ai.conversations.index')
            ->with('success', 'Konuşma başarıyla silindi.');
    }
}