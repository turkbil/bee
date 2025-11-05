<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * History Loader Node
 *
 * Loads conversation message history from database
 */
class HistoryLoaderNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $limit = $this->getConfig('limit', 10);
        $order = $this->getConfig('order', 'asc');
        $includeSystem = $this->getConfig('include_system_messages', false);

        try {
            $query = $conversation->messages()
                ->orderBy('created_at', $order)
                ->limit($limit);

            if (!$includeSystem) {
                $query->whereIn('role', ['user', 'assistant']);
            }

            $messages = $query->get()->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toIso8601String(),
            ])->toArray();

            $this->log('info', 'History loaded successfully', [
                'conversation_id' => $conversation->id,
                'message_count' => count($messages),
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                ['messages' => $messages],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Failed to load history', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('History load failed: ' . $e->getMessage());
        }
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'history_loader';
    }

    public static function getName(): string
    {
        return 'Geçmiş Yükle';
    }

    public static function getDescription(): string
    {
        return 'Konuşma geçmişini database\'den yükler';
    }

    public static function getConfigSchema(): array
    {
        return [
            'limit' => [
                'type' => 'number',
                'label' => 'Mesaj Limiti',
                'min' => 1,
                'max' => 50,
                'default' => 10,
                'help' => 'Kaç mesaj yüklensin',
            ],
            'order' => [
                'type' => 'select',
                'label' => 'Sıralama',
                'options' => [
                    'asc' => 'Eskiden yeniye',
                    'desc' => 'Yeniden eskiye',
                ],
                'default' => 'asc',
            ],
            'include_system_messages' => [
                'type' => 'boolean',
                'label' => 'Sistem Mesajlarını Dahil Et',
                'default' => false,
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
            ['id' => 'output_1', 'label' => 'Geçmiş Yüklendi'],
        ];
    }

    public static function getCategory(): string
    {
        return 'data';
    }

    public static function getIcon(): string
    {
        return 'ti ti-clock-hour-4';
    }
}
