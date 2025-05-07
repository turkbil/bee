<?php
namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Illuminate\Support\Facades\Auth;

#[Layout('admin.layout')]
class ChatPanel extends Component
{
    use WithPagination;
    
    public $conversationId = null;
    public $message = '';
    public $messages = [];
    public $conversations = [];
    public $loading = false;
    public $title = '';
    public $promptId = null;
    public $prompts = [];
    
    protected $rules = [
        'message' => 'required|string',
        'title' => 'nullable|string|max:255',
        'promptId' => 'nullable|exists:ai_prompts,id'
    ];
    
    public function mount()
    {
        $this->loadConversations();
        $this->loadPrompts();
    }
    
    public function loadConversations()
    {
        $this->conversations = app(AIService::class)->conversations()->getConversations(20);
    }
    
    public function loadPrompts()
    {
        $this->prompts = app(AIService::class)->prompts()->getAllPrompts();
    }
    
    public function selectConversation($id)
    {
        $this->conversationId = $id;
        $this->loadMessages();
    }
    
    public function loadMessages()
    {
        if (!$this->conversationId) {
            $this->messages = [];
            return;
        }
        
        $conversation = Conversation::where('id', $this->conversationId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$conversation) {
            $this->messages = [];
            return;
        }
        
        $this->messages = $conversation->messages()->orderBy('created_at')->get();
    }
    
    public function createConversation()
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $conversation = app(AIService::class)->conversations()->createConversation(
            $this->title,
            $this->promptId
        );
        
        $this->conversationId = $conversation->id;
        $this->loadConversations();
        $this->loadMessages();
        $this->title = '';
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Yeni konuşma başlatıldı',
            'type' => 'success'
        ]);
    }
    
    public function deleteConversation($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($conversation) {
            app(AIService::class)->conversations()->deleteConversation($conversation);
            
            if ($this->conversationId == $id) {
                $this->conversationId = null;
                $this->messages = [];
            }
            
            $this->loadConversations();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Konuşma silindi',
                'type' => 'success'
            ]);
        }
    }
    
    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string',
        ]);
        
        $this->loading = true;
        
        // Eğer konuşma ID yoksa, yeni bir konuşma oluştur
        if (!$this->conversationId) {
            $title = substr($this->message, 0, 30) . '...';
            $conversation = app(AIService::class)->conversations()->createConversation($title, $this->promptId);
            $this->conversationId = $conversation->id;
            $this->loadConversations();
        } else {
            $conversation = Conversation::find($this->conversationId);
        }
        
        // Kullanıcı mesajını ekle ve AI yanıtını al
        $aiService = app(AIService::class);
        $response = $aiService->conversations()->getAIResponse($conversation, $this->message);
        
        $this->message = '';
        $this->loading = false;
        $this->loadMessages();
        
        if (!$response) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Yanıt alınamadı. Lütfen daha sonra tekrar deneyin.',
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        $remainingDaily = app(AIService::class)->limits()->getRemainingDailyLimit();
        $remainingMonthly = app(AIService::class)->limits()->getRemainingMonthlyLimit();
        
        return view('ai::admin.livewire.chat-panel', [
            'remainingDaily' => $remainingDaily,
            'remainingMonthly' => $remainingMonthly,
        ]);
    }
}