<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

/**
 * Category Detection Node (GLOBAL)
 *
 * IMPORTANT: This is a GLOBAL node - no tenant-specific logic!
 * Tenant-specific category detection should be handled by:
 * - Tenant{X}ProductSearchService::detectCategoryId()
 *
 * This node is optional and can be skipped if ProductSearchNode
 * uses tenant-specific service.
 */
class CategoryDetectionNode extends BaseNode
{
    public function execute(array $context): array
    {
        $userMessage = $context['user_message'] ?? '';

        // Generic category detection (very basic, tenants should override)
        $category = $this->detectGenericCategory($userMessage);

        Log::info('🏷️ CategoryDetectionNode (Generic)', [
            'category' => $category,
            'note' => 'Tenant-specific detection in ProductSearchNode'
        ]);

        // Return only new keys (FlowExecutor will merge with context)
        return [
            'detected_category' => $category
        ];
    }

    /**
     * Generic category detection (fallback only)
     * Returns null by default - tenant services should handle category detection
     */
    protected function detectGenericCategory(string $message): ?int
    {
        // No generic detection - tenant services handle this
        // This node exists for backward compatibility
        return null;
    }
}
