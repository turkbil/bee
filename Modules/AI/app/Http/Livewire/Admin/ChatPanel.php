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
use Exception;

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
    public $currentMessageId = null;
    public $isStreaming = false;
    public $error = null;
    
    protected $listeners = [
        'sendMessageAction',
        'streamComplete',
        'retryLastMessage'
    ];
    
    protected $rules = [
        'message' => 'nullable|string', 
        'title' => 'nullable|string|max:255',
        'promptId' => 'nullable|exists:ai_prompts,id'
    ];
    
    public function boot()
    {
        try {
            Log::info('ChatPanel boot başladı');
            $this->loadConversations();
            $this->loadPrompts();
            Log::info('ChatPanel boot tamamlandı');
        } catch (Exception $e) {
            Log::error('ChatPanel boot hatası: ' . $e->getMessage());
        }
    }
    
    public function mount()
    {
        try {
            Log::info('ChatPanel mount başladı');
            
            if (request()->has('conversation')) {
                $this->conversationId = request()->get('conversation');
                $this->loadMessages();
            }
            
            Log::info('ChatPanel mount tamamlandı');
        } catch (Exception $e) {
            Log::error('ChatPanel mount hatası: ' . $e->getMessage());
        }
    }
    
    public function loadConversations()
    {
        try {
            Log::info('loadConversations başladı');
            
            $aiService = app(AIService::class);
            $this->conversations = $aiService->conversations()->getConversations(20);
            
            Log::info('loadConversations tamamlandı, konuşma sayısı: ' . count($this->conversations));
        } catch (Exception $e) {
            $this->conversations = [];
            Log::error('Konuşmalar yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function loadPrompts()
    {
        try {
            Log::info('loadPrompts başladı');
            
            $aiService = app(AIService::class);
            $this->prompts = $aiService->prompts()->getAllPrompts();
            
            Log::info('loadPrompts tamamlandı, prompt sayısı: ' . count($this->prompts));
        } catch (Exception $e) {
            $this->prompts = [];
            Log::error('Promptlar yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function selectConversation($id)
    {
        try {
            Log::info('selectConversation başladı, ID: ' . $id);
            
            $this->conversationId = $id;
            $this->loadMessages();
            
            Log::info('selectConversation tamamlandı');
        } catch (Exception $e) {
            Log::error('Konuşma seçilirken hata: ' . $e->getMessage());
        }
    }
    
    public function loadMessages()
    {
        try {
            Log::info('loadMessages başladı, conversation ID: ' . $this->conversationId);
            
            if (!$this->conversationId) {
                $this->messages = [];
                Log::info('Konuşma ID olmadığı için mesajlar yüklenmedi');
                return;
            }
            
            $conversation = Conversation::where('id', $this->conversationId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$conversation) {
                $this->messages = [];
                Log::warning('Konuşma bulunamadı veya kullanıcıya ait değil, ID: ' . $this->conversationId);
                return;
            }
            
            $this->messages = $conversation->messages()->orderBy('created_at')->get()->toArray();
            
            Log::info('loadMessages tamamlandı, mesaj sayısı: ' . count($this->messages));
        } catch (Exception $e) {
            $this->messages = [];
            Log::error('Mesajlar yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function createConversation()
    {
        try {
            Log::info('createConversation başladı');
            
            $this->validate([
                'title' => 'required|string|max:255',
            ]);
            
            $aiService = app(AIService::class);
            $conversation = $aiService->conversations()->createConversation(
                $this->title,
                $this->promptId
            );
            
            Log::info('Konuşma oluşturuldu, ID: ' . $conversation->id);
            
            $this->conversationId = $conversation->id;
            $this->loadConversations();
            $this->loadMessages();
            $this->title = '';
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Yeni konuşma başlatıldı'
            ]);
            
            Log::info('createConversation tamamlandı');
        } catch (Exception $e) {
            Log::error('Konuşma oluşturulurken hata: ' . $e->getMessage());
        }
    }
    
    public function deleteConversation($id)
    {
        try {
            Log::info('deleteConversation başladı, ID: ' . $id);
            
            $conversation = Conversation::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($conversation) {
                $aiService = app(AIService::class);
                $aiService->conversations()->deleteConversation($conversation);
                
                Log::info('Konuşma silindi, ID: ' . $id);
                
                if ($this->conversationId == $id) {
                    $this->conversationId = null;
                    $this->messages = [];
                }
                
                $this->loadConversations();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Konuşma silindi'
                ]);
            } else {
                Log::warning('Silinecek konuşma bulunamadı veya kullanıcıya ait değil, ID: ' . $id);
            }
            
            Log::info('deleteConversation tamamlandı');
        } catch (Exception $e) {
            Log::error('Konuşma silinirken hata: ' . $e->getMessage());
        }
    }
    
    public function sendMessageAction($message = null)
    {
        try {
            Log::info('sendMessageAction başladı');
            
            if ($message) {
                $this->message = $message;
                Log::info('Mesaj parametresi: ' . $message);
            }
            
            $this->sendMessage();
            
            Log::info('sendMessageAction tamamlandı');
        } catch (Exception $e) {
            Log::error('sendMessageAction hatası: ' . $e->getMessage());
        }
    }
    
    public function retryLastMessage()
    {
        try {
            Log::info('retryLastMessage başladı');
            
            if (!$this->conversationId) {
                Log::warning('Konuşma ID olmadığı için yeniden deneme yapılamıyor');
                return;
            }
            
            $lastUserMessage = Message::where('conversation_id', $this->conversationId)
                ->where('role', 'user')
                ->orderBy('id', 'desc')
                ->first();
                
            if ($lastUserMessage) {
                Log::info('Son kullanıcı mesajı bulundu, ID: ' . $lastUserMessage->id);
                $this->sendMessageAction($lastUserMessage->content);
            } else {
                Log::warning('Son kullanıcı mesajı bulunamadı');
            }
            
            Log::info('retryLastMessage tamamlandı');
        } catch (Exception $e) {
            Log::error('Son mesajı yeniden denerken hata: ' . $e->getMessage());
        }
    }
    
    public function streamComplete()
    {
        try {
            Log::info('streamComplete başladı');
            
            $this->isStreaming = false;
            $this->loading = false;
            $this->loadMessages();
            
            Log::info('streamComplete tamamlandı');
        } catch (Exception $e) {
            Log::error('streamComplete hatası: ' . $e->getMessage());
        }
    }
    
    public function sendMessage()
    {
        try {
            Log::info('sendMessage başladı');
            
            $userMessage = trim($this->message ?? '');
            
            if (empty($userMessage)) {
                Log::warning('Boş mesaj gönderilmeye çalışıldı');
                return;
            }
            
            $this->message = '';
            $this->loading = true;
            $this->isStreaming = true;
            $this->error = null;
            
            Log::info('Kullanıcı mesajı: ' . $userMessage);
            
            $aiService = app(AIService::class);
            
            // Eğer konuşma yoksa yeni oluştur
            if (!$this->conversationId) {
                $title = substr($userMessage, 0, 30) . '...';
                $conversation = $aiService->conversations()->createConversation($title, $this->promptId);
                $this->conversationId = $conversation->id;
                Log::info('Yeni konuşma oluşturuldu, ID: ' . $conversation->id);
                $this->loadConversations();
            } else {
                $conversation = Conversation::find($this->conversationId);
                
                if (!$conversation || $conversation->user_id != Auth::id()) {
                    throw new Exception('Konuşma bulunamadı veya erişim izniniz yok.');
                }
                
                Log::info('Mevcut konuşma kullanılıyor, ID: ' . $conversation->id);
            }
            
            // Kullanıcı mesajını ekle
            $userMessageModel = new Message([
                'conversation_id' => $this->conversationId,
                'role' => 'user',
                'content' => $userMessage,
                'tokens' => strlen($userMessage) / 4,
            ]);
            $userMessageModel->save();
            Log::info('Kullanıcı mesajı kaydedildi, ID: ' . $userMessageModel->id);
            
            // Boş AI mesajı oluştur
            $aiMessage = new Message([
                'conversation_id' => $this->conversationId,
                'role' => 'assistant',
                'content' => '',
                'tokens' => 0,
            ]);
            $aiMessage->save();
            
            $this->currentMessageId = $aiMessage->id;
            Log::info('Boş AI mesajı oluşturuldu, ID: ' . $aiMessage->id);
            
            // Kullanıcı mesajını göstermek için mesajları yenile
            $this->loadMessages();
            
            // Stream başlangıç sinyali gönder
            $this->dispatch('streamStart', ['messageId' => $aiMessage->id]);
            
            // AI yanıtını al ve her yeni parçayı frontend'e ilet
            $fullContent = '';
            
            try {
                Log::info('AI yanıtı almaya başlanıyor...');
                
                // Bu metodu değiştiriyoruz - doğrudan yanıtı alıyoruz
                $responseContent = $aiService->conversations()->getAIResponse($conversation, $userMessage, false);
                
                // Yanıtı aldıktan sonra, cümle cümle bölüp stream simülasyonu yapıyoruz
                if ($responseContent) {
                    Log::info('AI yanıtı alındı, toplam karakter: ' . strlen($responseContent));
                    
                    // Cümlelere ayırma
                    $sentences = preg_split('/(?<=[.!?])\s+/', $responseContent, -1, PREG_SPLIT_NO_EMPTY);
                    
                    // Her bir cümleyi ekrana yaz
                    foreach ($sentences as $sentence) {
                        // Her cümleyi bir parça olarak gönder
                        $this->dispatch('streamChunk', [
                            'messageId' => $aiMessage->id,
                            'content' => $sentence . ' '
                        ]);
                        
                        $fullContent .= $sentence . ' ';
                        
                        // Database'i güncelle
                        $aiMessage->content = $fullContent;
                        $aiMessage->tokens = strlen($fullContent) / 4;
                        $aiMessage->save();
                        
                        // Simüle edilmiş gecikme (50-150ms)
                        usleep(rand(50000, 150000));
                    }
                    
                    // Son güncellemeden emin ol
                    $aiMessage->content = $fullContent;
                    $aiMessage->tokens = strlen($fullContent) / 4;
                    $aiMessage->save();
                } else {
                    Log::warning('AI yanıtı boş döndü!');
                    $this->error = 'AI yanıtı alınamadı. Lütfen tekrar deneyin.';
                    
                    // Boş yanıt durumunda özel mesaj ekle
                    $aiMessage->content = 'Yanıt alınamadı. Lütfen tekrar deneyin.';
                    $aiMessage->save();
                }
            } catch (Exception $e) {
                Log::error('AI yanıtı alınırken hata: ' . $e->getMessage());
                $this->error = 'AI yanıtı alınırken hata oluştu: ' . $e->getMessage();
                
                $aiMessage->content = 'Yanıt alınırken bir hata oluştu: ' . $e->getMessage();
                $aiMessage->save();
            }
            
            // Stream tamamlandı sinyali gönder
            $this->dispatch('streamEnd', ['messageId' => $aiMessage->id]);
            
            // Yükleme durumunu kapat
            $this->loading = false;
            $this->isStreaming = false;
            
            Log::info('sendMessage tamamlandı');
            
        } catch (Exception $e) {
            $this->loading = false;
            $this->isStreaming = false;
            $this->error = 'Mesaj gönderilirken bir hata oluştu: ' . $e->getMessage();
            Log::error('Mesaj gönderilirken hata: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        try {
            $remainingDaily = 0;
            $remainingMonthly = 0;
            
            try {
                $aiService = app(AIService::class);
                $remainingDaily = $aiService->limits()->getRemainingDailyLimit();
                $remainingMonthly = $aiService->limits()->getRemainingMonthlyLimit();
            } catch (Exception $e) {
                Log::error('Limit bilgileri alınırken hata: ' . $e->getMessage());
            }
            
            return view('ai::admin.livewire.chat-panel', [
                'remainingDaily' => $remainingDaily,
                'remainingMonthly' => $remainingMonthly,
            ]);
        } catch (Exception $e) {
            Log::error('render metodu hatası: ' . $e->getMessage());
            return view('ai::admin.livewire.chat-panel', [
                'remainingDaily' => 0,
                'remainingMonthly' => 0,
            ]);
        }
    }
}