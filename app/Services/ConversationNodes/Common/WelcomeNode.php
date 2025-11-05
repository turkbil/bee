<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Welcome Node
 *
 * Sends a welcome message to start the conversation
 * Can show quick reply suggestions
 */
class WelcomeNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Get welcome message from config
        $welcomeMessage = $this->getConfig('welcome_message', 'Merhaba! Size nasıl yardımcı olabilirim?');

        // Get suggestions if enabled
        $showSuggestions = $this->getConfig('show_suggestions', false);
        $suggestions = $this->getConfig('suggestions', []);

        $data = [
            'node_type' => 'welcome',
            'welcome_message' => $welcomeMessage,
        ];

        if ($showSuggestions && !empty($suggestions)) {
            $data['suggestions'] = $suggestions;
        }

        // Get next node
        $nextNode = $this->getConfig('next_node');

        $this->log('info', 'Welcome node executed', [
            'conversation_id' => $conversation->id,
            'has_suggestions' => !empty($suggestions),
        ]);

        return $this->success(
            null, // Welcome node doesn't need AI, just returns message
            $data,
            $nextNode
        );
    }

    public function validate(): bool
    {
        return !empty($this->getConfig('welcome_message'));
    }

    public static function getType(): string
    {
        return 'welcome';
    }

    public static function getName(): string
    {
        return 'Karşılama';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcıyı karşılama mesajı gösterir';
    }

    public static function getConfigSchema(): array
    {
        return [
            'welcome_message' => [
                'type' => 'textarea',
                'label' => 'Karşılama Mesajı',
                'placeholder' => 'Merhaba! Size nasıl yardımcı olabilirim?',
                'required' => true,
                'help' => 'Kullanıcıya gösterilecek ilk mesaj',
            ],
            'show_suggestions' => [
                'type' => 'boolean',
                'label' => 'Önerileri Göster',
                'default' => false,
                'help' => 'Hızlı yanıt önerileri göster',
            ],
            'suggestions' => [
                'type' => 'array',
                'label' => 'Öneriler',
                'placeholder' => 'Ürün ara, Fiyat bilgisi, İletişim',
                'help' => 'Her satıra bir öneri (virgülle ayırın)',
                'depends_on' => 'show_suggestions',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
                'required' => false,
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Başlat'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Karşılandı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'flow';
    }

    public static function getIcon(): string
    {
        return 'ti ti-hand-stop';
    }
}
