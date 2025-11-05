<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Message Saver Node
 *
 * Saves user and assistant messages to database
 */
class MessageSaverNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $saveUser = $this->getConfig('save_user_message', true);
        $saveAssistant = $this->getConfig('save_assistant_message', true);
        $saveMetadata = $this->getConfig('save_metadata', false);

        $saved = [];

        try {
            // Save user message
            if ($saveUser && !empty($userMessage)) {
                $userMsgData = [
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $userMessage,
                ];

                if ($saveMetadata) {
                    $userMsgData['metadata'] = [
                        'timestamp' => now()->toIso8601String(),
                        'node_type' => 'message_saver',
                    ];
                }

                \App\Models\AIConversationMessage::create($userMsgData);
                $saved[] = 'user';
            }

            // Get assistant response from context (if AI response was generated)
            $assistantResponse = $conversation->context_data['last_ai_response'] ?? null;

            if ($saveAssistant && !empty($assistantResponse)) {
                $assistantMsgData = [
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $assistantResponse,
                ];

                if ($saveMetadata) {
                    $assistantMsgData['metadata'] = [
                        'timestamp' => now()->toIso8601String(),
                        'node_type' => 'message_saver',
                    ];
                }

                \App\Models\AIConversationMessage::create($assistantMsgData);
                $saved[] = 'assistant';
            }

            $this->log('info', 'Messages saved successfully', [
                'conversation_id' => $conversation->id,
                'saved_types' => $saved,
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                ['saved_messages' => $saved],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Failed to save messages', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Message save failed: ' . $e->getMessage());
        }
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'message_saver';
    }

    public static function getName(): string
    {
        return 'Mesaj Kaydet';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcı ve AI mesajlarını database\'e kaydeder';
    }

    public static function getConfigSchema(): array
    {
        return [
            'save_user_message' => [
                'type' => 'boolean',
                'label' => 'Kullanıcı Mesajını Kaydet',
                'default' => true,
            ],
            'save_assistant_message' => [
                'type' => 'boolean',
                'label' => 'AI Mesajını Kaydet',
                'default' => true,
            ],
            'save_metadata' => [
                'type' => 'boolean',
                'label' => 'Metadata Kaydet',
                'default' => false,
                'help' => 'Timestamp, node type gibi ekstra bilgileri kaydet',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
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
        return [
            ['id' => 'output_1', 'label' => 'Kaydedildi'],
        ];
    }

    public static function getCategory(): string
    {
        return 'data';
    }

    public static function getIcon(): string
    {
        return 'ti ti-device-floppy';
    }
}
