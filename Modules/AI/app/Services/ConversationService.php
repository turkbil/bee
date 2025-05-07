<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Auth;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\LimitService;

class ConversationService
{
    protected $deepSeekService;
    protected $limitService;

    /**
     * Constructor
     *
     * @param DeepSeekService $deepSeekService
     * @param LimitService $limitService
     */
    public function __construct(DeepSeekService $deepSeekService, LimitService $limitService)
    {
        $this->deepSeekService = $deepSeekService;
        $this->limitService = $limitService;
    }

    /**
     * Yeni konuşma oluştur
     *
     * @param string $title
     * @param int|null $promptId
     * @return Conversation
     */
    public function createConversation(string $title, ?int $promptId = null): Conversation
    {
        return Conversation::create([
            'title' => $title,
            'user_id' => Auth::id(),
            'prompt_id' => $promptId,
        ]);
    }

    /**
     * Konuşmaya mesaj ekle
     *
     * @param Conversation $conversation
     * @param string $content
     * @param string $role
     * @param int|null $tokens
     * @return Message
     */
    public function addMessage(Conversation $conversation, string $content, string $role = 'user', ?int $tokens = null): Message
    {
        // Token sayısını tahmin et
        if ($tokens === null) {
            $tokens = (int) (strlen($content) / 4); // Basit bir tahmin
        }

        // Mesajı kaydet
        return Message::create([
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'tokens' => $tokens,
        ]);
    }

    /**
     * AI'dan yanıt al
     *
     * @param Conversation $conversation
     * @param string $userMessage
     * @param bool $stream
     * @return string|null|\Closure
     */
    public function getAIResponse(Conversation $conversation, string $userMessage, bool $stream = false)
    {
        // Limit kontrolü yap
        if (!$this->limitService->checkLimits()) {
            return null;
        }

        // Kullanıcı mesajını ekle
        $this->addMessage($conversation, $userMessage, 'user');

        // Konuşma mesajlarını formatla
        $messages = $this->deepSeekService->formatConversationMessages($conversation);

        // AI'dan yanıt al
        if ($stream) {
            // Stream modunda çalıştığımızda, içeriği anlık olarak işlemek için closure döndürüyoruz
            return $this->deepSeekService->ask($messages, true);
        } else {
            // Normal mod
            $aiResponse = $this->deepSeekService->ask($messages);

            if ($aiResponse) {
                // Token sayısını tahmin et
                $tokens = $this->deepSeekService->estimateTokens([['role' => 'assistant', 'content' => $aiResponse]]);
                
                // AI yanıtını kaydet
                $this->addMessage($conversation, $aiResponse, 'assistant', $tokens);
                
                // Kullanım limitini güncelle
                $this->limitService->incrementUsage($tokens);
            }

            return $aiResponse;
        }
    }

    /**
     * Stream edilebilir AI yanıtı için
     *
     * @param Conversation $conversation
     * @param string $userMessage
     * @param callable $callback İçerik parçalarını almak için callback fonksiyonu
     * @return Message Oluşturulan mesaj
     */
    public function getStreamingAIResponse(Conversation $conversation, string $userMessage, callable $callback): Message
    {
        // Limit kontrolü yap
        if (!$this->limitService->checkLimits()) {
            $callback("Üzgünüm, kullanım limitinize ulaştınız.");
            return $this->addMessage($conversation, "Üzgünüm, kullanım limitinize ulaştınız.", 'assistant');
        }

        // Kullanıcı mesajını ekle
        $this->addMessage($conversation, $userMessage, 'user');

        // Konuşma mesajlarını formatla
        $messages = $this->deepSeekService->formatConversationMessages($conversation);

        // Boş AI mesajı oluştur
        $aiMessage = $this->addMessage($conversation, "", 'assistant', 0);
        
        // İçeriği toplayacak değişken
        $fullContent = '';
        
        // Stream modunda AI yanıtını al
        $streamFunction = $this->deepSeekService->ask($messages, true);
        
        if (is_callable($streamFunction)) {
            $streamFunction(function ($content) use (&$fullContent, $callback, $aiMessage) {
                // İçeriği topla ve güncelle
                $fullContent .= $content;
                
                // Callback ile UI'ı güncelle
                $callback($content);
                
                // Mesajı veritabanında güncelle
                $aiMessage->content = $fullContent;
                $aiMessage->tokens = (int) (strlen($fullContent) / 4); // Basit token tahmini
                $aiMessage->save();
            });
        }
        
        // Eğer callback tamamlandığında içerik boşsa, hata mesajı ekle
        if (empty($fullContent)) {
            $errorMsg = "Yanıt üretilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            $callback($errorMsg);
            
            $aiMessage->content = $errorMsg;
            $aiMessage->save();
        }
        
        // Kullanım limitini güncelle
        $this->limitService->incrementUsage($aiMessage->tokens);
        
        return $aiMessage;
    }

    /**
     * Konuşmayı güncelle
     *
     * @param Conversation $conversation
     * @param array $data
     * @return Conversation
     */
    public function updateConversation(Conversation $conversation, array $data): Conversation
    {
        $conversation->update($data);
        return $conversation;
    }
    
    /**
     * Konuşma listesini getir
     *
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConversations(?int $limit = null)
    {
        $query = Conversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
            
        if ($limit) {
            return $query->take($limit)->get();
        }
        
        return $query->get();
    }
    
    /**
     * Konuşmayı sil
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function deleteConversation(Conversation $conversation): bool
    {
        return $conversation->delete();
    }
}