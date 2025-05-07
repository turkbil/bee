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
     * @return string|null
     */
    public function getAIResponse(Conversation $conversation, string $userMessage): ?string
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