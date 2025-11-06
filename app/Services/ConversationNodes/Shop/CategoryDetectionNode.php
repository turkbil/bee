<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Category Detection Node
 *
 * Detects category from user message and asks category-specific questions
 * Example: "transpalet" → Asks about capacity, type, etc.
 */
class CategoryDetectionNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        try {
            \Log::info('🔍 CategoryDetectionNode STARTED', [
                'conversation_id' => $conversation->id,
                'config_keys' => array_keys($this->config),
                'next_node_config' => $this->config['next_node'] ?? 'NULL',
            ]);

            // Detect category
            $category = $this->detectCategory($userMessage);

            if (!$category) {
                $this->log('info', 'No category detected', [
                    'conversation_id' => $conversation->id,
                ]);

                $nextNode = $this->getConfig('no_category_next_node') ?? $this->getConfig('next_node');
                return $this->success(null, ['category_detected' => false], $nextNode);
            }

            // Get category-specific questions
            $questions = $this->getCategoryQuestions($category['slug']);

            // Store in context
            $conversation->addToContext('detected_category', $category);
            $conversation->addToContext('category_questions', $questions);

            $nextNode = $this->getConfig('next_node');

            $this->log('info', 'Category detected', [
                'conversation_id' => $conversation->id,
                'category' => $category['name'],
                'questions_count' => count($questions),
                'next_node' => $nextNode,
            ]);

            $result = $this->success(
                null,
                [
                    'category' => $category,
                    'questions' => $questions,
                ],
                $nextNode
            );

            \Log::info('✅ CategoryDetectionNode RETURNING', [
                'success' => $result['success'],
                'next_node' => $result['next_node'],
                'has_data' => !empty($result['data']),
            ]);

            return $result;

        } catch (\Exception $e) {
            $this->log('error', 'Category detection failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Category detection failed: ' . $e->getMessage());
        }
    }

    protected function detectCategory(string $message): ?array
    {
        $message = mb_strtolower($message);

        // Category keywords with synonyms
        $categoryMap = [
            'transpalet' => ['transpalet', 'palet taşıyıcı', 'manuel kaldırıcı', 'palet jack'],
            'forklift' => ['forklift', 'istif makinası', 'yük kaldırıcı', 'forklif'],
            'istif-makinasi' => ['istif makinası', 'istif', 'stacker'],
            'platform' => ['platform', 'yükseltici platform', 'makaslı platform'],
        ];

        foreach ($categoryMap as $slug => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $this->getCategoryBySlug($slug);
                }
            }
        }

        return null;
    }

    protected function getCategoryBySlug(string $slug): ?array
    {
        try {
            // Slug is JSON field, check both languages
            $category = \Modules\Shop\App\Models\ShopCategory::where(function($query) use ($slug) {
                    $query->where('slug->tr', $slug)
                          ->orWhere('slug->en', $slug);
                })
                ->where('is_active', true)
                ->first();

            if (!$category) {
                return null;
            }

            $slugValue = $category->slug[app()->getLocale()] ?? $category->slug['tr'] ?? $category->slug['en'] ?? 'unknown';

            // Get translated title
            $titleValue = is_array($category->title)
                ? ($category->title[app()->getLocale()] ?? $category->title['tr'] ?? $category->title['en'] ?? 'Uncategorized')
                : $category->title;

            return [
                'id' => $category->id,
                'name' => $titleValue,
                'slug' => $slugValue,
                'url' => "/shop/category/{$slugValue}",
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getCategoryQuestions(string $categorySlug): array
    {
        $allQuestions = $this->getConfig('category_questions', []);

        return $allQuestions[$categorySlug] ?? [];
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'category_detection';
    }

    public static function getName(): string
    {
        return 'Kategori Tespit';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcının bahsettiği kategoriyi tespit eder ve özel sorular sorar';
    }

    public static function getConfigSchema(): array
    {
        return [
            'category_questions' => [
                'type' => 'json',
                'label' => 'Kategori Bazlı Sorular',
                'help' => 'Her kategori için özel sorular tanımla',
                'default' => json_encode([
                    'transpalet' => [
                        ['key' => 'capacity', 'question' => 'Hangi kapasite?', 'options' => ['1.5 ton', '2 ton', '2.5 ton', '3 ton']],
                        ['key' => 'type', 'question' => 'Manuel mi elektrikli mi?', 'options' => ['Manuel', 'Elektrikli', 'Yarı elektrikli']],
                    ],
                    'forklift' => [
                        ['key' => 'capacity', 'question' => 'Hangi kapasite?', 'options' => ['2 ton', '3 ton', '5 ton']],
                        ['key' => 'fuel', 'question' => 'Yakıt tipi?', 'options' => ['Dizel', 'Elektrikli', 'LPG']],
                    ],
                ]),
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node (Kategori Bulunca)',
            ],
            'no_category_next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node (Kategori Yok)',
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
            ['id' => 'detected', 'label' => 'Kategori Bulundu'],
            ['id' => 'not_detected', 'label' => 'Kategori Yok'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-category';
    }
}
