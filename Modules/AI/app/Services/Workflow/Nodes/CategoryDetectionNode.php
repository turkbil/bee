<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class CategoryDetectionNode extends BaseNode
{
    public function execute(array $context): array
    {
        $userMessage = $context['user_message'] ?? '';
        
        // Simple category detection
        $category = $this->detectCategory($userMessage);
        $context['detected_category'] = $category;
        
        Log::info('🏷️ CategoryDetectionNode', ['category' => $category]);
        
        return $context;
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
