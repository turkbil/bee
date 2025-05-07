<?php
namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $aiService = app(AIService::class);
            $this->conversations = $aiService->conversations()->getConversations(20);
        } catch (\Exception $e) {
            $this->conversations = [];
            $this->addError('error', 'Konuşmalar yüklenirken bir sorun oluştu: ' . $e->getMessage());
            Log::error('Konuşmalar yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function loadPrompts()
    {
        try {
            $aiService = app(AIService::class);
            $this->prompts = $aiService->prompts()->getAllPrompts();
        } catch (\Exception $e) {
            $this->prompts = [];
            $this->addError('error', 'Promptlar yüklenirken bir sorun oluştu: ' . $e->getMessage());
            Log::error('Promptlar yüklenirken hata: ' . $e->getMessage());
        }
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
        
        try {
            $conversation = Conversation::where('id', $this->conversationId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$conversation) {
                $this->messages = [];
                return;
            }
            
            $this->messages = $conversation->messages()->orderBy('created_at')->get()->toArray();
        } catch (\Exception $e) {
            $this->messages = [];
            $this->addError('error', 'Mesajlar yüklenirken bir sorun oluştu: ' . $e->getMessage());
            Log::error('Mesajlar yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function createConversation()
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ]);
        
        try {
            $aiService = app(AIService::class);
            $conversation = $aiService->conversations()->createConversation(
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
        } catch (\Exception $e) {
            $this->addError('error', 'Konuşma oluşturulurken bir sorun oluştu: ' . $e->getMessage());
            Log::error('Konuşma oluşturulurken hata: ' . $e->getMessage());
        }
    }
    
    public function deleteConversation($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($conversation) {
            try {
                $aiService = app(AIService::class);
                $aiService->conversations()->deleteConversation($conversation);
                
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
            } catch (\Exception $e) {
                $this->addError('error', 'Konuşma silinirken bir sorun oluştu: ' . $e->getMessage());
                Log::error('Konuşma silinirken hata: ' . $e->getMessage());
            }
        }
    }
    
    public function sendMessage()
    {
        try {
            // Doğrulama kurallarının kontrolü
            $validatedData = $this->validate([
                'message' => 'required|string',
            ]);
            
            // Mesaj içeriğini kaydet ve temizle
            $userMessage = $validatedData['message'];
            $this->message = '';
            
            // İstek başladı, yükleniyor durumunu aktif et
            $this->loading = true;
            
            // Servis örneğini al
            $aiService = app(AIService::class);
            
            // Eğer konuşma ID yoksa, yeni bir konuşma oluştur
            if (!$this->conversationId) {
                $title = substr($userMessage, 0, 30) . '...';
                $conversation = $aiService->conversations()->createConversation($title, $this->promptId);
                $this->conversationId = $conversation->id;
                $this->loadConversations();
            } else {
                $conversation = Conversation::find($this->conversationId);
                
                if (!$conversation || $conversation->user_id != Auth::id()) {
                    throw new \Exception('Konuşma bulunamadı veya erişim izniniz yok.');
                }
            }
            
            // Kullanıcı mesajını ekle
            $message = new Message([
                'conversation_id' => $this->conversationId,
                'role' => 'user',
                'content' => $userMessage,
                'tokens' => strlen($userMessage) / 4,
            ]);
            $message->save();
            
            // AI yanıtını al
            $response = $aiService->conversations()->getAIResponse($conversation, $userMessage);
            
            if (!$response) {
                $this->dispatch('toast', [
                    'title' => 'Uyarı!',
                    'message' => 'AI servisinden yanıt alınamadı. Lütfen daha sonra tekrar deneyin.',
                    'type' => 'warning'
                ]);
            }
            
            // Mesajları yenile (dizi olarak)
            $this->loadMessages();
            
            // Yükleniyor durumunu kapat
            $this->loading = false;
            
            // Mesajlar güncellendiğinde scroll down yapmak için event fırlat
            $this->dispatch('messagesUpdated');
            
        } catch (\Exception $e) {
            $this->loading = false;
            Log::error('Mesaj gönderilirken hata: ' . $e->getMessage());
            $this->addError('error', 'Mesaj gönderilirken bir sorun oluştu: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Mesaj işlenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            // Yine de UI'ı güncelleyelim
            $this->dispatch('messagesUpdated');
        }
    }
    
    public function render()
    {
        $remainingDaily = 0;
        $remainingMonthly = 0;
        
        try {
            $aiService = app(AIService::class);
            $remainingDaily = $aiService->limits()->getRemainingDailyLimit();
            $remainingMonthly = $aiService->limits()->getRemainingMonthlyLimit();
        } catch (\Exception $e) {
            // Limitleri alırken hata oluştu, varsayılan değerleri kullan
            Log::error('Limit bilgileri alınırken hata: ' . $e->getMessage());
        }
        
        return view('ai::admin.livewire.chat-panel', [
            'remainingDaily' => $remainingDaily,
            'remainingMonthly' => $remainingMonthly,
        ]);
    }
}