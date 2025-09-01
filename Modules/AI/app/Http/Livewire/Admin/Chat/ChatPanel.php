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
        
        // Varsayılan prompt'ı seç (is_default = true), yoksa ilkini al
        if (!empty($this->prompts)) {
            $defaultPrompt = $this->prompts->where('is_default', true)->first();
            $this->selectedPromptId = $defaultPrompt ? $defaultPrompt->id : $this->prompts[0]->id;
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
            // PERFORMANCE: Cache chat prompts with Redis for 5 minutes
            $cacheKey = 'ai_chat_prompts_' . (tenancy()->tenant?->id ?? 'central');
            $this->prompts = Cache::remember($cacheKey, 300, function() {
                return \Modules\AI\App\Models\Prompt::where('is_active', true)
                    ->where('prompt_type', 'chat') // Sadece chat prompt'ları
                    ->select('id', 'name', 'content', 'is_default')
                    ->orderBy('is_default', 'desc') // Varsayılan prompt'lar önce
                    ->orderBy('name')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::error('Chat promptları yüklenirken hata: ' . $e->getMessage());
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

            // Conversation history hazırla (son 50 mesaj - DÜZELTME)
            $conversationHistory = $this->conversation->messages()
                ->latest() // En son mesajları al (created_at desc)
                ->limit(100) // Son 100 mesaj - doğal sohbet için
                ->get()
                ->reverse() // AI için kronolojik sıraya çevir
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })->toArray();

            // AI yanıtını al (conversation history ile)
            $aiMessage = $this->conversationService->getStreamingAIResponse(
                $this->conversation,
                $userMessageContent,
                function($content) {
                    $this->streamContent .= $content;
                    $this->dispatch('contentStreamed', $this->streamContent);
                },
                [
                    'conversation_history' => $conversationHistory,
                    'prompt_id' => $this->selectedPromptId
                ]
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