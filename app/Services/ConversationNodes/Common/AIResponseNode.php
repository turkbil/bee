<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * AI Response Node
 *
 * Sends a system prompt to AI and returns AI's response
 * Most basic and commonly used node type
 */
class AIResponseNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Get system prompt from config
        $systemPrompt = $this->getConfig('system_prompt');

        if (empty($systemPrompt)) {
            return $this->failure('System prompt is required');
        }

        // Replace variables in prompt
        $systemPrompt = $this->replaceVariables($systemPrompt, $conversation);

        // Get next node from config
        $nextNode = $this->getConfig('next_node');

        $this->log('info', 'AI Response node executed', [
            'conversation_id' => $conversation->id,
            'prompt_length' => strlen($systemPrompt),
        ]);

        return $this->success(
            $systemPrompt,
            ['node_type' => 'ai_response'],
            $nextNode
        );
    }

    public function validate(): bool
    {
        return !empty($this->getConfig('system_prompt'));
    }

    public static function getType(): string
    {
        return 'ai_response';
    }

    public static function getName(): string
    {
        return 'AI Yanıt';
    }

    public static function getDescription(): string
    {
        return 'AI\'a özel bir talimat gönder ve yanıt al';
    }

    public static function getConfigSchema(): array
    {
        return [
            'system_prompt' => [
                'type' => 'textarea',
                'label' => 'System Prompt',
                'placeholder' => 'Müşteriyi sıcak karşıla ve yardımcı ol...',
                'required' => true,
                'help' => 'AI\'a verilecek sistem talimatı. Değişkenler: {{user_name}}, {{tenant.directive.X}}',
            ],
            'temperature' => [
                'type' => 'number',
                'label' => 'Temperature',
                'min' => 0,
                'max' => 2,
                'step' => 0.1,
                'default' => 0.7,
                'help' => 'Yaratıcılık seviyesi (0=tutarlı, 2=yaratıcı)',
            ],
            'max_tokens' => [
                'type' => 'number',
                'label' => 'Maksimum Token',
                'min' => 50,
                'max' => 2000,
                'default' => 500,
                'help' => 'Yanıt uzunluğu limiti',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
                'required' => false,
                'help' => 'Bu node\'dan sonra hangi node çalışsın',
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
            ['id' => 'output_1', 'label' => 'Yanıt Verildi'],
        ];
    }

    public static function getCategory(): string
    {
        return 'core';
    }

    public static function getIcon(): string
    {
        return 'ti ti-message-chatbot';
    }

    /**
     * Replace variables in prompt
     */
    protected function replaceVariables(string $prompt, AIConversation $conversation): string
    {
        // Replace conversation context variables
        $contextData = $conversation->context_data ?? [];
        foreach ($contextData as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $prompt = str_replace("{{context.{$key}}}", $value, $prompt);
            }
        }

        // Replace tenant directive variables
        preg_match_all('/\{\{tenant\.directive\.([a-z_]+)\}\}/', $prompt, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $directiveKey) {
                $directiveValue = $this->getDirective($conversation, $directiveKey, '');
                $prompt = str_replace("{{tenant.directive.{$directiveKey}}}", $directiveValue, $prompt);
            }
        }

        return $prompt;
    }
}
