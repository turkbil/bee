<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class CategoryDetectionNode extends BaseNode
{
    public function execute(array $context): array
    {
        // DEBUG: Check incoming context
        file_put_contents('/tmp/category_node_debug.txt',
            date('Y-m-d H:i:s') . " - CategoryDetectionNode\n" .
            "Incoming context keys: " . implode(', ', array_keys($context)) . "\n" .
            "Has conversation_history: " . (isset($context['conversation_history']) ? 'YES' : 'NO') . "\n" .
            "History count: " . (isset($context['conversation_history']) ? count($context['conversation_history']) : 0) . "\n" .
            "--------------------\n\n",
            FILE_APPEND
        );

        $userMessage = $context['user_message'] ?? '';

        // Simple category detection
        $category = $this->detectCategory($userMessage);

        Log::info('🏷️ CategoryDetectionNode', ['category' => $category]);

        // Return only new keys (FlowExecutor will merge with context)
        return [
            'detected_category' => $category
        ];
    }
    
    protected function detectCategory(string $message): ?string
    {
        $message = mb_strtolower($message);
        
        if (str_contains($message, 'transpalet')) return 'transpalet';
        if (str_contains($message, 'forklift')) return 'forklift';
        if (str_contains($message, 'istif')) return 'stacker';
        
        return null;
    }
}
