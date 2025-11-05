<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Context Builder Node
 *
 * Builds AI context from conversation data, tenant directives, and custom data
 * Prepares comprehensive context for AI response generation
 */
class ContextBuilderNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $includeDirectives = $this->getConfig('include_tenant_directives', true);
        $includeHistory = $this->getConfig('include_conversation_history', true);
        $includeContext = $this->getConfig('include_conversation_context', true);

        $contextData = [];

        // 1. Tenant Directives
        if ($includeDirectives) {
            $directives = \App\Models\AITenantDirective::getAllForTenant($conversation->tenant_id);
            $contextData['tenant_directives'] = $directives;
        }

        // 2. Conversation History
        if ($includeHistory) {
            $historyLimit = $this->getConfig('history_limit', 10);
            $history = $this->getConversationHistory($conversation, $historyLimit);
            $contextData['conversation_history'] = $history;
        }

        // 3. Conversation Context Data
        if ($includeContext && !empty($conversation->context_data)) {
            $contextData['conversation_context'] = $conversation->context_data;
        }

        // 4. Brand/Tenant Info
        $contextData['tenant_info'] = [
            'tenant_id' => $conversation->tenant_id,
            'locale' => app()->getLocale(),
        ];

        // Get next node
        $nextNode = $this->getConfig('next_node');

        $this->log('info', 'Context builder node executed', [
            'conversation_id' => $conversation->id,
            'context_keys' => array_keys($contextData),
            'history_count' => count($contextData['conversation_history'] ?? []),
        ]);

        return $this->success(
            null, // Context builder doesn't generate prompt, just prepares data
            $contextData,
            $nextNode
        );
    }

    protected function getConversationHistory(AIConversation $conversation, int $limit): array
    {
        try {
            return $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->toIso8601String(),
                ])
                ->toArray();
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load conversation history', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function validate(): bool
    {
        return true; // Context builder has no required config
    }

    public static function getType(): string
    {
        return 'context_builder';
    }

    public static function getName(): string
    {
        return 'Context Hazırla';
    }

    public static function getDescription(): string
    {
        return 'AI için kapsamlı context bilgisi hazırlar';
    }

    public static function getConfigSchema(): array
    {
        return [
            'include_tenant_directives' => [
                'type' => 'boolean',
                'label' => 'Tenant Direktiflerini Ekle',
                'default' => true,
                'help' => 'Tenant\'a özel talimatları context\'e ekle',
            ],
            'include_conversation_history' => [
                'type' => 'boolean',
                'label' => 'Konuşma Geçmişini Ekle',
                'default' => true,
                'help' => 'Önceki mesajları context\'e ekle',
            ],
            'history_limit' => [
                'type' => 'number',
                'label' => 'Geçmiş Limiti',
                'min' => 1,
                'max' => 50,
                'default' => 10,
                'help' => 'Kaç mesaj yüklensin',
                'depends_on' => 'include_conversation_history',
            ],
            'include_conversation_context' => [
                'type' => 'boolean',
                'label' => 'Conversation Context Ekle',
                'default' => true,
                'help' => 'Conversation state\'teki context_data\'yı ekle',
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
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Context Hazır'],
        ];
    }

    public static function getCategory(): string
    {
        return 'data';
    }

    public static function getIcon(): string
    {
        return 'ti ti-database';
    }
}
