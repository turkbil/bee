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

    public function __construct(DeepSeekService $deepSeekService, LimitService $limitService)
    {
        $this->deepSeekService = $deepSeekService;
        $this->limitService = $limitService;
    }

    public function createConversation(string $title, ?int $promptId = null): Conversation
    {
        return Conversation::create([
            'title' => $title,
            'user_id' => Auth::id(),
            'prompt_id' => $promptId,
        ]);
    }

    public function addMessage(Conversation $conversation, string $content, string $role = 'user', ?int $tokens = null): Message
    {
        if ($tokens === null) {
            $tokens = (int) (strlen($content) / 4);
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'tokens' => $tokens,
        ]);
    }

    public function getAIResponse(Conversation $conversation, string $userMessage, bool $stream = false)
    {
        if (!$this->limitService->checkLimits()) {
            return "Üzgünüm, kullanım limitinize ulaştınız.";
        }
    
        $this->addMessage($conversation, $userMessage, 'user');
    
        $messages = $this->deepSeekService->formatConversationMessages($conversation);
    
        if ($stream) {
            return $this->deepSeekService->ask($messages, true);
        } else {
            $aiResponse = $this->deepSeekService->ask($messages);
    
            if ($aiResponse) {
                $tokens = $this->deepSeekService->estimateTokens([['role' => 'assistant', 'content' => $aiResponse]]);
                
                $this->addMessage($conversation, $aiResponse, 'assistant', $tokens);
                
                $this->limitService->incrementUsage($tokens);
            }
    
            return $aiResponse;
        }
    }

    public function getStreamingAIResponse(Conversation $conversation, string $userMessage, callable $callback): Message
    {
        if (!$this->limitService->checkLimits()) {
            $callback("Üzgünüm, kullanım limitinize ulaştınız.");
            return $this->addMessage($conversation, "Üzgünüm, kullanım limitinize ulaştınız.", 'assistant');
        }

        $this->addMessage($conversation, $userMessage, 'user');

        $messages = $this->deepSeekService->formatConversationMessages($conversation);

        $aiMessage = $this->addMessage($conversation, "", 'assistant', 0);
        
        $fullContent = '';
        
        $streamFunction = $this->deepSeekService->ask($messages, true);
        
        if (is_callable($streamFunction)) {
            $streamFunction(function ($content) use (&$fullContent, $callback, $aiMessage) {
                $fullContent .= $content;
                $callback($content);
                
                $aiMessage->content = $fullContent;
                $aiMessage->tokens = (int) (strlen($fullContent) / 4);
                $aiMessage->save();
            });
        }
        
        if (empty($fullContent)) {
            $errorMsg = "Yanıt üretilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            $callback($errorMsg);
            
            $aiMessage->content = $errorMsg;
            $aiMessage->save();
        }
        
        $this->limitService->incrementUsage($aiMessage->tokens);
        
        return $aiMessage;
    }

    public function updateConversation(Conversation $conversation, array $data): Conversation
    {
        $conversation->update($data);
        return $conversation;
    }
    
    public function getConversations(?int $limit = null)
    {
        $query = Conversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
            
        if ($limit) {
            return $query->take($limit)->get();
        }
        
        return $query->get();
    }
    
    public function deleteConversation(Conversation $conversation): bool
    {
        return $conversation->delete();
    }
}