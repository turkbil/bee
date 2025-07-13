<?php

namespace Modules\AI\App\Http\Livewire\Admin\Chat;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
// Removed Tenancy facade - using tenancy() helper instead

#[Layout('admin.layout')]
class ChatPanel extends Component
{
    public $message = '';
    public $conversation;
    public $messages = [];
    public $conversations = [];
    public $prompts = [];
    public $selectedPromptId = null;
    public $conversationTitle = '';
    public $isStreaming = false;
    public $streamContent = '';
    
    protected $conversationService;
    protected $aiService;
    
    protected $listeners = [
        'conversationCreated' => 'handleConversationCreated',
        'conversationSelected' => 'loadConversation',
        'promptSelected' => 'setPrompt'
    ];

    public function boot(ConversationService $conversationService, AIService $aiService)
    {
        $this->conversationService = $conversationService;
        $this->aiService = $aiService;
    }

    public function mount()
    {
        $this->loadConversations();
        $this->loadPrompts();
        
        // PERFORMANCE: Find default prompt from cached collection instead of new query
        $defaultPrompt = collect($this->prompts)->firstWhere('is_default', true);
        if ($defaultPrompt) {
            $this->selectedPromptId = $defaultPrompt['id'];
        }
    }

    public function render()
    {
        return view('ai::admin.chat.chat-panel')
            ->extends('admin.layout')
            ->section('content');
    }

    public function loadConversations()
    {
        try {
            // PERFORMANCE: Cache conversations for 2 minutes
            $userId = Auth::id();
            $tenantId = tenancy()->tenant?->id ?? 1;
            $cacheKey = "ai_conversations_user_{$userId}_tenant_{$tenantId}";
            
            $this->conversations = Cache::remember($cacheKey, 120, function() {
                return $this->conversationService->getConversations(10);
            });
        } catch (\Exception $e) {
            Log::error('Konuşmalar yüklenirken hata: ' . $e->getMessage());
            $this->conversations = [];
        }
    }

    public function loadPrompts()
    {
        try {
            // PERFORMANCE: Cache prompts with Redis for 5 minutes
            $cacheKey = 'ai_prompts_active_' . (tenancy()->tenant?->id ?? 'central');
            $this->prompts = Cache::remember($cacheKey, 300, function() {
                return Prompt::where('is_active', true)
                    ->select('id', 'name', 'content', 'is_default', 'prompt_type') // Correct field names
                    ->orderBy('is_default', 'desc')
                    ->orderBy('name')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::error('Promptlar yüklenirken hata: ' . $e->getMessage());
            $this->prompts = [];
        }
    }

    public function createNewConversation()
    {
        try {
            $title = $this->conversationTitle ?: 'Yeni Konuşma';
            $this->conversation = $this->conversationService->createConversation($title, $this->selectedPromptId);
            $this->messages = [];
            $this->conversationTitle = '';
            $this->loadConversations();
            
            $this->dispatch('conversationCreated', $this->conversation->id);
        } catch (\Exception $e) {
            Log::error('Konuşma oluşturulurken hata: ' . $e->getMessage());
            session()->flash('error', 'Konuşma oluşturulamadı: ' . $e->getMessage());
        }
    }

    public function loadConversation($conversationId)
    {
        try {
            // Tenant-aware conversation loading
            $this->conversation = Conversation::with('messages')
                ->where('tenant_id', tenancy()->tenant?->id)
                ->find($conversationId);
            if ($this->conversation) {
                $this->messages = $this->conversation->messages->toArray();
                $this->selectedPromptId = $this->conversation->prompt_id;
            }
        } catch (\Exception $e) {
            Log::error('Konuşma yüklenirken hata: ' . $e->getMessage());
            session()->flash('error', 'Konuşma yüklenemedi.');
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->message))) {
            return;
        }

        if (!$this->conversation) {
            $this->createNewConversation();
        }

        try {
            $this->isStreaming = true;
            $this->streamContent = '';
            
            // User mesajını ekle
            $userMessage = $this->conversationService->addMessage(
                $this->conversation, 
                $this->message, 
                'user'
            );
            
            $this->messages[] = $userMessage->toArray();
            $userMessageContent = $this->message;
            $this->message = '';

            // AI yanıtını al (streaming)
            $aiMessage = $this->conversationService->getStreamingAIResponse(
                $this->conversation,
                $userMessageContent,
                function($content) {
                    $this->streamContent .= $content;
                    $this->dispatch('contentStreamed', $this->streamContent);
                }
            );

            $this->messages[] = $aiMessage->toArray();
            $this->isStreaming = false;
            $this->streamContent = '';
            
        } catch (\Exception $e) {
            $this->isStreaming = false;
            Log::error('Mesaj gönderilirken hata: ' . $e->getMessage());
            session()->flash('error', 'Mesaj gönderilemedi: ' . $e->getMessage());
        }
    }

    public function setPrompt($promptId)
    {
        $this->selectedPromptId = $promptId;
    }

    public function deleteConversation($conversationId)
    {
        try {
            // Tenant-aware conversation deletion
            $conversation = Conversation::where('tenant_id', tenancy()->tenant?->id)
                ->find($conversationId);
            if ($conversation) {
                $this->conversationService->deleteConversation($conversation);
                
                if ($this->conversation && $this->conversation->id == $conversationId) {
                    $this->conversation = null;
                    $this->messages = [];
                }
                
                $this->loadConversations();
                session()->flash('success', 'Konuşma silindi.');
            }
        } catch (\Exception $e) {
            Log::error('Konuşma silinirken hata: ' . $e->getMessage());
            session()->flash('error', 'Konuşma silinemedi.');
        }
    }
}