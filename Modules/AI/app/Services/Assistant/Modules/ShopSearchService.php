<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Modules\AI\App\Services\Tenant\Tenant2ProductSearchService;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use App\Models\AITenantDirective;
use Illuminate\Support\Facades\Log;

/**
 * Shop Search Service
 *
 * E-ticaret/ürün satışı için AI arama servisi.
 * Mevcut ProductSearchService'leri sarmalar.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class ShopSearchService implements ModuleSearchInterface
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
        $tenantId = tenant('id');

        try {
            // Tenant-specific servis kullan
            if (in_array($tenantId, [2, 3])) {
                $searchService = app(Tenant2ProductSearchService::class);
                $results = $searchService->searchProducts($query);
            } else {
                // Generic product search
                $results = $this->genericProductSearch($query, $filters, $limit);
            }

            return [
                'success' => true,
                'items' => $results['products'] ?? [],
                'total' => $results['products_found'] ?? count($results['products'] ?? []),
                'search_layer' => $results['search_layer'] ?? 'database',
                'module_type' => 'shop',
            ];

        } catch (\Exception $e) {
            Log::error('ShopSearchService: Search failed', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'items' => [],
                'total' => 0,
                'error' => $e->getMessage(),
                'module_type' => 'shop',
            ];
        }
    }

    /**
     * Generic product search (non-tenant-specific)
     */
    protected function genericProductSearch(string $query, array $filters, int $limit): array
    {
        $searchQuery = ShopProduct::where('is_active', true);

        // Meilisearch varsa kullan
        if (method_exists(ShopProduct::class, 'search')) {
            $products = ShopProduct::search($query)
                ->take($limit)
                ->get();
        } else {
            // Fallback: Database search
            $products = $searchQuery
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('short_description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get();
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $products = $products->where('category_id', $filters['category_id']);
        }

        return [
            'products' => $products->toArray(),
            'products_found' => $products->count(),
            'search_layer' => 'database',
        ];
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        if (empty($results['items'])) {
            return '';
        }

        $context = "## 📦 Mevcut Ürünler\n\n";

        foreach ($results['items'] as $product) {
            $title = $this->translate($product['title'] ?? '');
            $slug = $this->translate($product['slug'] ?? '');
            $description = $this->translate($product['short_description'] ?? '');
            $price = $product['base_price'] ?? 0;
            $currency = $product['currency'] ?? 'TRY';

            $context .= "### {$title}\n";

            if ($description) {
                $context .= "- {$description}\n";
            }

            if ($price > 0) {
                $context .= "- **Fiyat:** " . number_format($price, 2, ',', '.') . " {$currency}\n";
            } else {
                $context .= "- **Fiyat:** Teklif alınız\n";
            }

            $context .= "- [Ürünü İncele](/shop/{$slug})\n\n";
        }

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        $tenantId = tenant('id');

        // Önce directive'den dene
        try {
            $directive = AITenantDirective::where('tenant_id', $tenantId)
                ->where('directive_key', 'chatbot_quick_actions')
                ->where('is_active', true)
                ->first();

            if ($directive && $directive->directive_value) {
                return json_decode($directive->directive_value, true) ?? [];
            }
        } catch (\Exception $e) {
            Log::warning('ShopSearchService: Could not load quick actions directive');
        }

        // Kategorilerden otomatik oluştur
        try {
            $categories = ShopCategory::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->limit(6)
                ->get();

            $actions = [];
            $colors = ['blue', 'orange', 'green', 'purple', 'red', 'yellow'];

            foreach ($categories as $index => $category) {
                $title = $category->getTranslated('title', $this->locale);
                $actions[] = [
                    'label' => $title,
                    'message' => "{$title} hakkında bilgi almak istiyorum",
                    'icon' => 'fas fa-box',
                    'color' => $colors[$index % 6],
                ];
            }

            return $actions;

        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        $tenantId = tenant('id');

        // Tenant-specific detection
        if (in_array($tenantId, [2, 3])) {
            $searchService = app(Tenant2ProductSearchService::class);
            $category = $searchService->search($message, 1)['detected_category'] ?? null;

            if ($category) {
                return ['category_id' => $category];
            }
        }

        // Generic category detection
        $lowerMessage = mb_strtolower($message);

        try {
            $categories = ShopCategory::where('is_active', true)->get();

            foreach ($categories as $category) {
                $title = mb_strtolower($category->getTranslated('title', $this->locale));
                if (str_contains($lowerMessage, $title)) {
                    return ['category_id' => $category->category_id];
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        $tenantId = tenant('id');

        // Tenant 2/3 için özel kurallar
        if (in_array($tenantId, [2, 3])) {
            $searchService = app(Tenant2ProductSearchService::class);
            $customPrompts = $searchService->getCustomPrompts();

            return implode("\n\n", $customPrompts);
        }

        // Generic shop rules
        return "
## SHOP ASSISTANT KURALLARI

1. **Ürün Gösterimi:**
   - Sadece context'teki ürünleri göster
   - Her ürün için link ekle
   - Fiyatı varsa göster

2. **Fiyat Politikası:**
   - Fiyatsız ürünler için 'Teklif alınız' de
   - Stok bilgisi verme

3. **Satış Tonu:**
   - Profesyonel ve yardımsever ol
   - Ürün özelliklerini vurgula
   - Karşılaştırma yap
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'shop';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'E-Ticaret Asistanı';
    }

    /**
     * JSON multi-language çeviri
     */
    protected function translate($data): string
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            return $data[$this->locale] ?? $data['tr'] ?? $data['en'] ?? reset($data) ?? '';
        }

        return '';
    }
}
