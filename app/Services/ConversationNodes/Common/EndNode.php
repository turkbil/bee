<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * End Node
 *
 * Marks the end of conversation flow
 * Sends closing message and resets conversation
 */
class EndNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $closingMessage = $this->getConfig('closing_message', 'Yardımcı olabildiysem ne mutlu! İyi günler dileriz.');
        $resetConversation = $this->getConfig('reset_conversation', false);

        // If configured, reset conversation to start
        if ($resetConversation) {
            $conversation->reset();
        }

        $this->log('info', 'Conversation ended', [
            'conversation_id' => $conversation->id,
            'reset' => $resetConversation,
        ]);

        return $this->success(
            $closingMessage,
            ['conversation_ended' => true],
            null // No next node - this is the end
        );
    }

    public function validate(): bool
    {
        return true; // No required config
    }

    public static function getType(): string
    {
        return 'end';
    }

    public static function getName(): string
    {
        return 'Bitiş';
    }

    public static function getDescription(): string
    {
        return 'Sohbeti sonlandır';
    }

    public static function getConfigSchema(): array
    {
        return [
            'closing_message' => [
                'type' => 'textarea',
                'label' => 'Kapanış Mesajı',
                'default' => 'Yardımcı olabildiysem ne mutlu! İyi günler dileriz.',
                'help' => 'Sohbet bitişinde gösterilecek mesaj',
            ],
            'reset_conversation' => [
                'type' => 'boolean',
                'label' => 'Sohbeti Sıfırla',
                'default' => false,
                'help' => 'Aktif olursa sohbet başa döner',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return []; // No outputs - this is the end
    }

    public static function getCategory(): string
    {
        return 'core';
    }

    public static function getIcon(): string
    {
        return 'ti ti-flag-check';
    }
}
