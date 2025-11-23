<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Modules\AI\App\Models\AIKnowledgeBase;
use Illuminate\Support\Facades\Log;

/**
 * Info Search Service
 *
 * Bilgi/SSS/Destek için AI arama servisi.
 * Knowledge Base tablosunu kullanır.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class InfoSearchService implements ModuleSearchInterface
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * @inheritDoc
     */
    public function search(string $query, array $filters = [], int $limit = 50): array
    {
        try {
            $tenantId = tenant('id');

            // Knowledge base'de ara
            $results = AIKnowledgeBase::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('question', 'LIKE', "%{$query}%")
                      ->orWhere('answer', 'LIKE', "%{$query}%")
                      ->orWhere('keywords', 'LIKE', "%{$query}%");
                })
                ->orderBy('sort_order')
                ->limit($limit)
                ->get();

            return [
                'success' => true,
                'items' => $results->toArray(),
                'total' => $results->count(),
                'module_type' => 'info',
            ];

        } catch (\Exception $e) {
            Log::error('InfoSearchService: Search failed', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'items' => [],
                'total' => 0,
                'error' => $e->getMessage(),
                'module_type' => 'info',
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        if (empty($results['items'])) {
            return '';
        }

        $context = "## ❓ İlgili Bilgiler\n\n";

        foreach ($results['items'] as $item) {
            $question = $item['question'] ?? '';
            $answer = $item['answer'] ?? '';
            $category = $item['category'] ?? '';

            if ($category) {
                $context .= "**Kategori:** {$category}\n";
            }

            $context .= "**Soru:** {$question}\n";
            $context .= "**Cevap:** {$answer}\n\n";
            $context .= "---\n\n";
        }

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'SSS',
                'message' => 'Sık sorulan sorular nelerdir?',
                'icon' => 'fas fa-question-circle',
                'color' => 'blue',
            ],
            [
                'label' => 'Nasıl Çalışır',
                'message' => 'Sistem nasıl çalışıyor?',
                'icon' => 'fas fa-cogs',
                'color' => 'green',
            ],
            [
                'label' => 'Destek',
                'message' => 'Teknik destek almak istiyorum',
                'icon' => 'fas fa-headset',
                'color' => 'purple',
            ],
            [
                'label' => 'İletişim',
                'message' => 'İletişim bilgilerinizi öğrenebilir miyim?',
                'icon' => 'fas fa-phone',
                'color' => 'gradient',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        $lowerMessage = mb_strtolower($message);

        // Kategori tespiti
        $categoryKeywords = [
            'teknik' => 'technical',
            'ödeme' => 'payment',
            'kargo' => 'shipping',
            'iade' => 'return',
            'üyelik' => 'membership',
            'hesap' => 'account',
        ];

        foreach ($categoryKeywords as $keyword => $category) {
            if (str_contains($lowerMessage, $keyword)) {
                return ['category' => $category];
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        return "
## BİLGİ ASSISTANT KURALLARI

1. **Soru Yanıtlama:**
   - Knowledge base'deki cevapları kullan
   - Net ve anlaşılır yanıt ver
   - Kaynak belirt

2. **Bilgi Yoksa:**
   - 'Bu konuda bilgi bulunamadı' de
   - İletişim bilgilerini öner
   - Destek talebine yönlendir

3. **Ton:**
   - Yardımsever ve sabırlı ol
   - Adım adım açıkla
   - Teknik terimleri basitleştir

4. **Sınırlar:**
   - Tahmin yapma
   - Yanlış bilgi verme
   - Emin değilsen 'bilmiyorum' de
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'info';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'Bilgi Asistanı';
    }
}
