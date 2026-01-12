<?php

namespace App\Services\AI\Context;

use App\Helpers\AISettingsHelper;

/**
 * Module Context Orchestrator
 *
 * Tüm modül context'lerini birleştirip AI için hazır hale getirir.
 * Settings'den personality bilgilerini de ekler.
 */
class ModuleContextOrchestrator
{
    protected ShopContextBuilder $shopContext;
    protected PageContextBuilder $pageContext;

    public function __construct(
        ShopContextBuilder $shopContext,
        PageContextBuilder $pageContext
    ) {
        $this->shopContext = $shopContext;
        $this->pageContext = $pageContext;
    }

    /**
     * Full AI context oluştur (tüm modüller)
     */
    public function buildFullContext(array $options = []): array
    {
        $context = [
            'assistant_name' => AISettingsHelper::getAssistantName(),
            'modules' => [],
        ];

        // Shop context - ALWAYS include general context + specific context
        if (AISettingsHelper::isModuleEnabled('shop')) {
            // Start with general shop context (categories, featured products)
            $shopData = $this->shopContext->buildGeneralShopContext();

            // Add specific product context if product_id provided
            if (!empty($options['product_id'])) {
                $productData = $this->shopContext->buildProductContext($options['product_id']);
                if (!empty($productData)) {
                    // Merge product data into shop context
                    $shopData = array_merge($shopData, [
                        'current_product' => $productData['current_product'] ?? null,
                        'current_product_variants' => $productData['variants'] ?? [],
                        'current_product_category' => $productData['category'] ?? null,
                    ]);
                }
            }
            // Add specific category context if category_id provided
            elseif (!empty($options['category_id'])) {
                $categoryData = $this->shopContext->buildCategoryContext($options['category_id']);
                if (!empty($categoryData)) {
                    // Merge category data into shop context
                    $shopData = array_merge($shopData, [
                        'current_category' => $categoryData['current_category'] ?? null,
                        'current_category_products' => $categoryData['products'] ?? [],
                    ]);
                }
            }

            // 🔍 Add smart search results if available
            \Log::info('🔥🔥 ModuleContextOrchestrator OPTIONS', [
                'has_smart_search' => isset($options['smart_search_results']),
                'has_products' => isset($options['smart_search_results']['products']),
                'product_count' => count($options['smart_search_results']['products'] ?? []),
                'options_keys' => array_keys($options),
            ]);

            if (!empty($options['smart_search_results']['products'])) {
                \Log::info('🔍 ModuleContextOrchestrator: Smart search results bulundu', [
                    'product_count' => count($options['smart_search_results']['products']),
                ]);

                $searchProducts = [];
                foreach ($options['smart_search_results']['products'] as $productData) {
                    // Her ürünü formatla (fiyat + USD hesaplama dahil)
                    $formattedProduct = $this->shopContext->formatProductData($productData);
                    if (!empty($formattedProduct)) {
                        $searchProducts[] = $formattedProduct;
                    }
                }

                if (!empty($searchProducts)) {
                    $shopData['smart_search_products'] = $searchProducts;
                    \Log::info('✅ ModuleContextOrchestrator: Smart search products context\'e eklendi', [
                        'count' => count($searchProducts),
                    ]);
                }
            }

            $context['modules']['shop'] = $shopData;
        }

        // Page context - ALWAYS include general context + specific page
        if (AISettingsHelper::isModuleEnabled('page')) {
            // Start with general page context (about, services, contact, all pages)
            $pageData = $this->pageContext->buildGeneralPageContext();

            // Add specific page context if page_slug provided
            if (!empty($options['page_slug'])) {
                $specificPage = $this->pageContext->buildPageContext($options['page_slug']);
                if (!empty($specificPage)) {
                    // Merge specific page data
                    $pageData = array_merge($pageData, [
                        'current_page' => $specificPage['current_page'] ?? null,
                    ]);
                }
            }

            $context['modules']['page'] = $pageData;
        }

        return $context;
    }

    /**
     * AI için tam sistem prompt oluştur
     */
    public function buildSystemPrompt(): string
    {
        $prompts = [];

        // Settings-based personality prompt
        $prompts[] = AISettingsHelper::buildPersonalityPrompt();
        $prompts[] = "";

        // Contact info
        $contactPrompt = AISettingsHelper::buildContactPrompt();
        if (!empty($contactPrompt)) {
            $prompts[] = $contactPrompt;
            $prompts[] = "";
        }

        // Knowledge Base (FAQ/Q&A)
        $knowledgePrompt = AISettingsHelper::buildKnowledgeBasePrompt();
        if (!empty($knowledgePrompt)) {
            $prompts[] = $knowledgePrompt;
            $prompts[] = "";
        }

        return implode("\n", $prompts);
    }

    /**
     * User message için context hazırla
     */
    public function buildUserContext(string $userMessage, array $options = []): array
    {
        $fullContext = $this->buildFullContext($options);

        return [
            'user_message' => $userMessage,
            'context' => $fullContext,
            'system_prompt' => $this->buildSystemPrompt(),
            'session_id' => $options['session_id'] ?? null,
            'smart_search_results' => $options['smart_search_results'] ?? null, // 🆕 Smart Search results
            'user_sentiment' => $options['user_sentiment'] ?? null, // 🆕 User sentiment
            'user_subscription' => $options['user_subscription'] ?? null, // 🔐 Tenant-aware subscription status
        ];
    }
}
