<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Condition Node
 *
 * Conditional branching - if/else logic
 * Routes conversation to different nodes based on conditions
 */
class ConditionNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $conditionType = $this->getConfig('condition_type', 'contains');
        $value = $this->getConfig('value', '');
        $trueNode = $this->getConfig('true_node');
        $falseNode = $this->getConfig('false_node');

        // Evaluate condition
        $result = $this->evaluateCondition($conditionType, $value, $userMessage, $conversation);

        $nextNode = $result ? $trueNode : $falseNode;

        $this->log('info', 'Condition node evaluated', [
            'conversation_id' => $conversation->id,
            'condition_type' => $conditionType,
            'result' => $result,
            'next_node' => $nextNode,
        ]);

        return $this->success(
            null, // No prompt needed
            ['condition_result' => $result],
            $nextNode
        );
    }

    protected function evaluateCondition(string $type, string $value, string $userMessage, AIConversation $conversation): bool
    {
        $messageLower = mb_strtolower($userMessage, 'UTF-8');
        $valueLower = mb_strtolower($value, 'UTF-8');

        return match ($type) {
            'contains' => str_contains($messageLower, $valueLower),
            'equals' => $messageLower === $valueLower,
            'starts_with' => str_starts_with($messageLower, $valueLower),
            'ends_with' => str_ends_with($messageLower, $valueLower),
            'regex' => (bool) preg_match("/{$value}/i", $userMessage),
            'context_has' => isset($conversation->context_data[$value]),
            'context_equals' => ($conversation->context_data[$value] ?? null) === $this->getConfig('context_value'),
            default => false,
        };
    }

    public function validate(): bool
    {
        return !empty($this->getConfig('condition_type'))
            && !empty($this->getConfig('true_node'));
    }

    public static function getType(): string
    {
        return 'condition';
    }

    public static function getName(): string
    {
        return 'Koşul';
    }

    public static function getDescription(): string
    {
        return 'Eğer/o zaman mantığı ile akışı yönlendir';
    }

    public static function getConfigSchema(): array
    {
        return [
            'condition_type' => [
                'type' => 'select',
                'label' => 'Koşul Türü',
                'options' => [
                    'contains' => 'İçerir',
                    'equals' => 'Eşittir',
                    'starts_with' => 'İle başlar',
                    'ends_with' => 'İle biter',
                    'regex' => 'Regex eşleşmesi',
                    'context_has' => 'Context içinde var',
                    'context_equals' => 'Context değeri eşit',
                ],
                'required' => true,
            ],
            'value' => [
                'type' => 'text',
                'label' => 'Değer',
                'required' => true,
                'help' => 'Kontrol edilecek değer veya anahtar kelime',
            ],
            'context_value' => [
                'type' => 'text',
                'label' => 'Context Değeri',
                'help' => 'context_equals için beklenen değer',
            ],
            'true_node' => [
                'type' => 'node_select',
                'label' => 'Doğru İse',
                'required' => true,
            ],
            'false_node' => [
                'type' => 'node_select',
                'label' => 'Yanlış İse',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Giriş'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_true', 'label' => 'Doğru'],
            ['id' => 'output_false', 'label' => 'Yanlış'],
        ];
    }

    public static function getCategory(): string
    {
        return 'logic';
    }

    public static function getIcon(): string
    {
        return 'ti ti-git-branch';
    }
}
