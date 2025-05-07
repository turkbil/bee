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
    public $streamResponse = true;
    public $streamResult = null;
    public $currentMessageId = null;
    
    protected $listeners = [
        'streamProcess' => 'processStream',
        'endStream' => 'finishStream'
    ];
    
    protected $rules = [
        'message' => 'nullable|string', 
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
            // Kullanıcı mesajını al (boş ise null olacak)
            $userMessage = trim($this->message ?? '');
            
            // Eğer mesaj boşsa işlem yapma
            if (empty($userMessage)) {
                return;
            }
            
            // Mesajı temizle ve devam et
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
            $userMessageModel = new Message([
                'conversation_id' => $this->conversationId,
                'role' => 'user',
                'content' => $userMessage,
                'tokens' => strlen($userMessage) / 4,
            ]);
            $userMessageModel->save();
            
            // Boş AI mesajı oluştur
            $aiMessage = new Message([
                'conversation_id' => $this->conversationId,
                'role' => 'assistant',
                'content' => '',
                'tokens' => 0,
            ]);
            $aiMessage->save();
            $messageId = $aiMessage->id;
            
            // Mesajları yenile
            $this->loadMessages();
            
            // Stream başlangıç sinyali gönder
            $this->dispatch('streamStarted', ['messageId' => $messageId]);
            
            // Stream yanıtını hazırla
            $streamResult = $aiService->conversations()->getAIResponse($conversation, $userMessage, true);
            
            if (is_callable($streamResult)) {
                $fullContent = '';
                
                // Stream fonksiyonunu çalıştır
                $streamResult(function($content) use ($messageId, &$fullContent) {
                    $fullContent .= $content;
                    
                    // İçeriği frontend'e gönder
                    $this->dispatch('streamContent', [
                        'messageId' => $messageId,
                        'content' => $content
                    ]);
                    
                    // Veritabanını güncelle (her 50 karakterde bir)
                    if (strlen($content) >= 50) {
                        Message::where('id', $messageId)->update([
                            'content' => $fullContent,
                            'tokens' => (int)(strlen($fullContent) / 4)
                        ]);
                    }
                });
                
                // Son veritabanı güncellemesini yap
                Message::where('id', $messageId)->update([
                    'content' => $fullContent,
                    'tokens' => (int)(strlen($fullContent) / 4)
                ]);
                
                // Stream tamamlandı sinyali gönder
                $this->dispatch('streamComplete', ['messageId' => $messageId]);
                
                // Limiti güncelle
                $tokens = (int)(strlen($fullContent) / 4);
                $aiService->limits()->incrementUsage($tokens);
            } else {
                // Stream başlatılamadı, hata mesajı ekle
                Message::where('id', $messageId)->update([
                    'content' => 'AI yanıtı alınamadı. Lütfen daha sonra tekrar deneyin.',
                    'tokens' => 0
                ]);
                
                $this->dispatch('streamError', ['error' => 'AI yanıtı alınamadı.']);
            }
            
            // Yükleniyor durumunu kapat
            $this->loading = false;
            
        } catch (\Exception $e) {
            $this->loading = false;
            Log::error('Mesaj gönderilirken hata: ' . $e->getMessage());
            $this->addError('error', 'Mesaj gönderilirken bir sorun oluştu: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Mesaj işlenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
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