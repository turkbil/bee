<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Category Detection Node (İxtif.com Specific)
 *
 * Detects product category from user message
 * Applies category lock (prevent cross-category suggestions)
 * Categories: transpalet, forklift, istif, yedek_parca
 */
class CategoryDetectionNode extends AbstractNode
{
    /**
     * Category keyword mappings
     */
    protected array $categoryKeywords = [
        'transpalet' => ['transpalet', 'transpaleti', 'trans palet', 'palet taşıma', 'el arabası'],
        'forklift' => ['forklift', 'fork lift', 'yükleyici', 'istif makinesi'],
        'istif' => ['istif', 'istifleme', 'reach truck', 'reach', 'akülü istif'],
        'yedek_parca' => ['yedek parça', 'parça', 'aksesuar', 'tekerlek', 'pom tekerlek'],
    ];

    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Detect category from message
        $detectedCategory = $this->detectCategory($userMessage);

        // Apply category lock (directive-based)
        $categoryLocked = $this->getDirective($conversation, 'category_boundary_strict', true);
        $allowCrossCategory = $this->getDirective($conversation, 'allow_cross_category', false);

        // Save to context
        $conversation->mergeContext([
            'detected_category' => $detectedCategory,
            'category_locked' => $categoryLocked,
            'allow_cross_category' => $allowCrossCategory,
        ]);

        // Build AI prompt
        if ($detectedCategory) {
            $categoryName = ucfirst($detectedCategory);
            $prompt = "Müşteri {$categoryName} kategorisinde ürün arıyor. ";

            if ($categoryLocked) {
                $prompt .= "SADECE bu kategoriden ürün öner. Başka kategori önerme. ";
            }

            $prompt .= "Kategori içinde kalmaya dikkat et.";

            $nextNode = $this->getConfig('category_found_node', 'product_recommendation');
        } else {
            $prompt = "Müşterinin hangi kategoride ürün aradığını netleştirmeye çalış. ";
            $prompt .= "Mevcut kategoriler: Transpalet, Forklift, İstif Makinesi, Yedek Parça.";

            $nextNode = $this->getConfig('category_not_found_node');
        }

        $this->log('info', 'Category detected', [
            'conversation_id' => $conversation->id,
            'detected_category' => $detectedCategory,
            'category_locked' => $categoryLocked,
        ]);

        return $this->success(
            $prompt,
            [
                'category' => $detectedCategory,
                'category_locked' => $categoryLocked,
            ],
            $nextNode
        );
    }

    protected function detectCategory(string $message): ?string
    {
        $messageLower = mb_strtolower($message, 'UTF-8');

        foreach ($this->categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($messageLower, mb_strtolower($keyword, 'UTF-8'))) {
                    return $category;
                }
            }
        }

        return null;
    }

    public function validate(): bool
    {
        return true; // No required config
    }

    public static function getType(): string
    {
        return 'category_detection';
    }

    public static function getName(): string
    {
        return 'Kategori Tespit (İxtif)';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcı mesajından ürün kategorisini tespit eder (transpalet, forklift, istif, yedek parça)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'category_found_node' => [
                'type' => 'node_select',
                'label' => 'Kategori Bulunursa',
                'help' => 'Kategori tespit edildiğinde hangi node çalışsın',
            ],
            'category_not_found_node' => [
                'type' => 'node_select',
                'label' => 'Kategori Bulunamazsa',
                'help' => 'Kategori tespit edilemezse hangi node çalışsın',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Kullanıcı Mesajı'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_found', 'label' => 'Kategori Bulundu'],
            ['id' => 'output_not_found', 'label' => 'Kategori Bulunamadı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'ixtif_ecommerce';
    }

    public static function getIcon(): string
    {
        return 'ti ti-category';
    }
}
