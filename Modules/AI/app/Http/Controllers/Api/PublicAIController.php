<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\ResponseTemplateEngine;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AICreditUsage;
use App\Services\AI\Context\ModuleContextOrchestrator;
use Modules\AI\App\Models\AIConversation;
use Modules\AI\App\Models\AIMessage;
use App\Services\MarkdownService;

/**
 * 🌐 PUBLIC AI CONTROLLER V2 - Frontend API Entegrasyonu
 *
 * Bu controller public erişim için AI özelliklerini API olarak sunar:
 * - Guest user access (rate limited)
 * - Authenticated user access (credit system)
 * - Public chat widget support
 * - Rate limiting and security
 *
 * ENDPOINTS:
 * - POST /api/ai/v1/chat - Public chat access
 * - POST /api/ai/v1/feature/{slug} - Public feature access
 * - GET /api/ai/v1/features/public - Public features list
 * - POST /api/ai/v1/chat/user - Authenticated user chat
 * - GET /api/ai/v1/credits/balance - User credit balance
 */
class PublicAIController extends Controller
{
    private AIService $aiService;
    private ModuleContextOrchestrator $contextOrchestrator;

    public function __construct(
        AIService $aiService,
        ModuleContextOrchestrator $contextOrchestrator
    ) {
        $this->aiService = $aiService;
        $this->contextOrchestrator = $contextOrchestrator;
    }

    /**
     * 💬 Public Chat Endpoint - Guest users with rate limiting
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publicChat(Request $request): JsonResponse
    {
        try {
            // Rate limiting check
            $rateLimitKey = 'public-ai-chat:' . $request->ip();

            if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) { // 10 requests per hour
                return response()->json([
                    'success' => false,
                    'error' => 'Rate limit exceeded. Please try again later.',
                    'retry_after' => RateLimiter::remainingAttempts($rateLimitKey, 10)
                ], 429);
            }

            // Validate request
            $validated = $request->validate([
                'message' => 'required|string|min:3|max:500',
                'feature' => 'nullable|string|exists:ai_features,slug',
                'context' => 'nullable|array',
            ]);

            // Rate limit hit
            RateLimiter::hit($rateLimitKey, 3600); // 1 hour decay

            // Get feature or use default chat
            $feature = null;
            if (!empty($validated['feature'])) {
                $feature = AIFeature::where('slug', $validated['feature'])
                    ->where('is_public', true)
                    ->where('is_active', true)
                    ->first();

                if (!$feature) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Feature not found or not public'
                    ], 404);
                }
            }

            // Build AI prompt with V2 engines
            $promptOptions = [
                'context_type' => 'minimal', // Minimal context for public access
                'feature_name' => $feature?->slug ?? 'public-chat',
                'request_type' => 'public_chat',
                'user_type' => 'guest',
                'ip_address' => $request->ip(),
            ];

            // Use ResponseTemplateEngine V2 for anti-monotony
            if ($feature) {
                $templateEngine = new ResponseTemplateEngine();
                $antiMonotonyPrompt = $templateEngine->buildTemplateAwarePrompt($feature, $promptOptions);
            } else {
                $antiMonotonyPrompt = ResponseTemplateEngine::getQuickAntiMonotonyPrompt('public-chat');
            }

            // Build final prompt
            $finalPrompt = $validated['message'];
            if (!empty($antiMonotonyPrompt)) {
                $finalPrompt = $antiMonotonyPrompt . "\n\n" . $finalPrompt;
            }

            // Call AI service with correct signature
            $response = $this->aiService->processRequest(
                prompt: $finalPrompt,
                maxTokens: $promptOptions['max_tokens'] ?? 1000,
                temperature: $promptOptions['temperature'] ?? 0.7,
                model: $promptOptions['model'] ?? null,
                systemPrompt: $promptOptions['system_prompt'] ?? null,
                metadata: [
                    'feature' => $feature?->slug ?? 'public-chat',
                    'context' => $validated['context'] ?? [],
                    'user_type' => 'guest'
                ]
            );

            // Log public usage for analytics
            $this->logPublicUsage($request, $feature, $response);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $response['content'] ?? 'AI response generated',
                    'feature_used' => $feature?->slug ?? 'public-chat',
                    'remaining_requests' => RateLimiter::remaining($rateLimitKey, 10),
                    'credits_used' => 0, // Public users don't use credits
                    'response_id' => $response['id'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.publicChat failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'input' => $request->input('message', 'N/A')
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    /**
     * 🎯 Public Feature Endpoint - Specific AI feature access
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function publicFeature(Request $request, string $slug): JsonResponse
    {
        try {
            // Rate limiting check
            $rateLimitKey = 'public-ai-feature:' . $request->ip() . ':' . $slug;

            if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) { // 5 feature requests per hour
                return response()->json([
                    'success' => false,
                    'error' => 'Feature rate limit exceeded',
                    'retry_after' => RateLimiter::remainingAttempts($rateLimitKey, 5)
                ], 429);
            }

            // Find public feature
            $feature = AIFeature::where('slug', $slug)
                ->where('is_public', true)
                ->where('is_active', true)
                ->first();

            if (!$feature) {
                return response()->json([
                    'success' => false,
                    'error' => 'Feature not found or not available publicly'
                ], 404);
            }

            // Validate request based on feature requirements
            $validated = $request->validate([
                'input' => 'required|string|min:1|max:1000',
                'options' => 'nullable|array',
            ]);

            // Rate limit hit
            RateLimiter::hit($rateLimitKey, 3600);

            // Build feature-specific prompt with V2 engines
            $promptOptions = [
                'context_type' => 'essential', // Essential context for feature access
                'feature_name' => $feature->slug,
                'request_type' => 'public_feature',
                'user_type' => 'guest',
                'ip_address' => $request->ip(),
            ];

            // Use ResponseTemplateEngine V2 for feature-specific formatting
            $templateEngine = new ResponseTemplateEngine();
            $enhancedPrompt = $templateEngine->buildTemplateAwarePrompt($feature, $promptOptions);

            // Process with AI service
            $response = $this->aiService->processFeatureRequest($feature, [
                'input' => $validated['input'],
                'options' => $validated['options'] ?? [],
                'context' => $promptOptions,
                'enhanced_prompt' => $enhancedPrompt,
                'user_id' => null,
            ]);

            // Log usage
            $this->logPublicFeatureUsage($request, $feature, $response);

            return response()->json([
                'success' => true,
                'data' => [
                    'response' => $response['content'],
                    'feature' => [
                        'slug' => $feature->slug,
                        'name' => $feature->getTranslated('name'),
                        'description' => $feature->getTranslated('description'),
                    ],
                    'formatted_response' => $response['formatted_content'] ?? null,
                    'remaining_requests' => RateLimiter::remainingAttempts($rateLimitKey, 5),
                    'execution_time' => $response['execution_time'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.publicFeature failed', [
                'error' => $e->getMessage(),
                'feature_slug' => $slug,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Feature processing failed'
            ], 500);
        }
    }

    /**
     * 📋 Get Public Features List
     *
     * @return JsonResponse
     */
    public function getPublicFeatures(): JsonResponse
    {
        try {
            $cacheKey = 'public_ai_features_list';

            $features = Cache::remember($cacheKey, now()->addHours(6), function () {
                return AIFeature::where('is_public', true)
                    ->where('is_active', true)
                    ->select(['slug', 'name', 'description', 'icon', 'ai_feature_category_id'])
                    ->get()
                    ->map(function ($feature) {
                        return [
                            'slug' => $feature->slug,
                            'name' => $feature->getTranslated('name'),
                            'description' => $feature->getTranslated('description'),
                            'icon' => $feature->icon,
                            'category' => $feature->getCategoryName(),
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'features' => $features,
                    'total' => $features->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.getPublicFeatures failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load public features'
            ], 500);
        }
    }

    /**
     * 👤 Authenticated User Chat
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userChat(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Authentication required'
                ], 401);
            }

            // Check user credits
            $creditService = app(\Modules\AI\App\Services\AICreditService::class);
            $userCredits = $creditService->getUserCredits($user->id);

            if ($userCredits < 1) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient credits',
                    'credits_available' => $userCredits
                ], 402); // Payment required
            }

            // Validate input
            $validated = $request->validate([
                'message' => 'required|string|min:3|max:1000',
                'feature' => 'nullable|string|exists:ai_features,slug',
                'context' => 'nullable|array',
            ]);

            // Get feature if specified
            $feature = null;
            if (!empty($validated['feature'])) {
                $feature = AIFeature::where('slug', $validated['feature'])
                    ->where('is_active', true)
                    ->first();
            }

            // Build enhanced prompt for authenticated users
            $promptOptions = [
                'context_type' => 'normal', // Full context for authenticated users
                'feature_name' => $feature?->slug ?? 'user-chat',
                'request_type' => 'user_chat',
                'user_type' => 'authenticated',
                'user_id' => $user->id,
            ];

            // Enhanced AI processing for authenticated users
            if ($feature) {
                $templateEngine = new ResponseTemplateEngine();
                $enhancedPrompt = $templateEngine->buildTemplateAwarePrompt($feature, $promptOptions);
            } else {
                $enhancedPrompt = ResponseTemplateEngine::getQuickAntiMonotonyPrompt('user-chat');
            }

            // Process request
            $response = $this->aiService->processRequest([
                'input' => $validated['message'],
                'feature' => $feature,
                'context' => $validated['context'] ?? [],
                'options' => $promptOptions,
                'anti_monotony_prompt' => $enhancedPrompt,
                'user_id' => $user->id,
            ]);

            // Deduct credits
            $creditsUsed = $this->calculateCreditsUsed($feature, $response);
            $creditService->deductCredits($user->id, $creditsUsed, [
                'feature_slug' => $feature?->slug,
                'request_type' => 'user_chat',
                'response_length' => strlen($response['content'] ?? ''),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $response['content'],
                    'credits_used' => $creditsUsed,
                    'credits_remaining' => $userCredits - $creditsUsed,
                    'feature_used' => $feature?->slug,
                    'response_id' => $response['id'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.userChat failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Chat processing failed'
            ], 500);
        }
    }

    /**
     * 💰 Get User Credit Balance
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCreditBalance(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Authentication required'
                ], 401);
            }

            $creditService = app(\Modules\AI\App\Services\AICreditService::class);
            $credits = $creditService->getUserCredits($user->id);
            $recentUsage = $creditService->getRecentUsage($user->id, 30); // Last 30 days

            return response()->json([
                'success' => true,
                'data' => [
                    'credits_available' => $credits,
                    'recent_usage' => $recentUsage,
                    'usage_summary' => [
                        'last_30_days' => $recentUsage->sum('credits_used'),
                        'most_used_feature' => $recentUsage->groupBy('feature_slug')
                            ->map->sum('credits_used')
                            ->sortDesc()
                            ->keys()
                            ->first(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.getCreditBalance failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get credit balance'
            ], 500);
        }
    }

    /**
     * 📊 Log public usage for analytics
     */
    private function logPublicUsage(Request $request, ?AIFeature $feature, array $response): void
    {
        try {
            // Log to database for analytics
            AICreditUsage::create([
                'user_id' => null, // Guest user
                'tenant_id' => tenant('id'),
                'feature_slug' => $feature?->slug ?? 'public-chat',
                'credits_used' => 0, // Public access is free
                'prompt_credits' => 0,
                'completion_credits' => 0,
                'request_data' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input_length' => strlen($request->input('message', '')),
                    'response_length' => strlen($response['content'] ?? ''),
                ],
                'response_data' => [
                    'success' => !empty($response['content']),
                    'execution_time' => $response['execution_time'] ?? null,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log public usage', ['error' => $e->getMessage()]);
        }
    }

    /**
     * 📊 Log public feature usage
     */
    private function logPublicFeatureUsage(Request $request, AIFeature $feature, array $response): void
    {
        try {
            AICreditUsage::create([
                'user_id' => null,
                'tenant_id' => tenant('id'),
                'feature_slug' => $feature->slug,
                'credits_used' => 0,
                'prompt_credits' => 0,
                'completion_credits' => 0,
                'request_data' => [
                    'ip' => $request->ip(),
                    'feature_name' => $feature->getTranslated('name'),
                    'input_length' => strlen($request->input('input', '')),
                ],
                'response_data' => [
                    'success' => !empty($response['content']),
                    'response_length' => strlen($response['content'] ?? ''),
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log public feature usage', ['error' => $e->getMessage()]);
        }
    }

    /**
     * 💰 Calculate credits used based on feature and response
     */
    private function calculateCreditsUsed(?AIFeature $feature, array $response): int
    {
        $baseCredits = 1; // Minimum credit cost

        // Feature-specific multipliers
        if ($feature) {
            $baseCredits *= $feature->credit_cost ?? 1;
        }

        // Response length multiplier
        $responseLength = strlen($response['content'] ?? '');
        if ($responseLength > 500) {
            $baseCredits += intval($responseLength / 500); // +1 credit per 500 chars
        }

        return max(1, $baseCredits); // Minimum 1 credit
    }

    /**
     * 🛍️ Shop Assistant Chat - Multi-module AI with no rate limiting
     *
     * Özel Shop asistanı endpoint:
     * - Rate limiting YOK (unlimited)
     * - Credit cost YOK (0 credit)
     * - Multi-module context (Shop + Page + Blog)
     * - IP-based persistent sessions
     * - Settings-driven personality
     * - Anti-manipulation protection
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function shopAssistantChat(Request $request): JsonResponse
    {
        // 🚨 FILE PROOF - Write to file to prove this code runs
        // Debug removed - production ready

        // 🚨 EMERGENCY DEBUG - SONNET FIXING
        \Log::emergency('🚨🚨🚨 SHOP ASSISTANT ENTRY POINT', [
            'timestamp' => now()->toIso8601String(),
            'message_preview' => substr($request->input('message', ''), 0, 50),
            'session_id' => $request->input('session_id'),
        ]);

        // 🔄 NEW WORKFLOW SYSTEM - Route to ConversationFlowEngine
        // 🚨 SONNET FIX: FORCE V2 SYSTEM TEMPORARILY FOR TESTING
        $useNewSystem = true; // FORCED TRUE - was: config('ai.use_workflow_engine', false);

        \Log::emergency('🚨🚨🚨 CONFIG CHECK RESULT', [
            'config_value' => $useNewSystem,
            'config_type' => gettype($useNewSystem),
            'env_value' => env('AI_USE_WORKFLOW_ENGINE', 'not set'),
            'config_cached' => app()->configurationIsCached(),
            'will_use' => $useNewSystem ? 'V2 (NEW)' : 'V1 (OLD)',
            'v2_method_exists' => method_exists($this, 'shopAssistantChatV2'),
        ]);

        if ($useNewSystem) {
            \Log::emergency('🚨 ROUTING TO V2 SYSTEM NOW!');
            return $this->shopAssistantChatV2($request);
        }

        \Log::emergency('🚨 USING V1 SYSTEM (OLD)');

        // 🔧 OLD SYSTEM (Legacy - will be deprecated)
        // FORCE OPCACHE UPDATE: 2025-11-03 05:22
        \Log::info('🚀🚀🚀 shopAssistantChat STARTED (OLD SYSTEM) - ' . date('Y-m-d H:i:s'), [
            'message' => $request->input('message'),
            'session_id' => $request->input('session_id'),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {

            // Validate input (Tenant context check için exists rule'ları kaldırıldı)
            $validated = $request->validate([
                'message' => 'required|string|min:1|max:1000',
                'product_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'page_slug' => 'nullable|string|max:255',
                'session_id' => 'nullable|string|max:64',
            ]);

            // Generate or use existing session_id (IP-based)
            $sessionId = $validated['session_id'] ?? $this->generateSessionId($request);

            // Find or create conversation
            $conversation = AIConversation::firstOrCreate(
                [
                    'session_id' => $sessionId,
                    'tenant_id' => tenant('id'),
                ],
                [
                    'user_id' => auth()->id(),
                    'feature_slug' => 'shop-assistant',
                    'is_active' => true,
                ]
            );

            // METADATA KAYDI: Her zaman güncel metadata'yı kaydet (firstOrCreate'ten sonra)
            if ($conversation->wasRecentlyCreated || empty($conversation->context_data)) {
                $conversation->context_data = [
                    'tenant_id' => $conversation->tenant_id, // Conversation'daki tenant_id'yi kullan
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'locale' => app()->getLocale(),
                    'device_type' => $this->detectDeviceType($request),
                    'browser' => $this->detectBrowser($request),
                    'os' => $this->detectOS($request),
                    'referrer' => $request->header('referer'),
                    'started_at' => now()->toIso8601String(),
                ];
                $conversation->save();
            }

            // 🆕 Smart Product Search Integration

            // 🔍 SEARCH QUERY: Use current message only (no conversation context)
            // Conversation context is handled by AI prompt, not search query
            $searchQuery = $validated['message'];

            try {
                // 🔍 PRODUCT SEARCH (Tenant-specific or generic)
                $tenantId = tenant('id');

                if ($tenantId == 2 || $tenantId == 3) {
                    // 🏢 Tenant 2/3: iXtif (endüstriyel ekipman)
                    // Kategori bilgileri tenant-specific (transpalet, forklift, reach truck, vb.)
                    $productSearchService = app(\Modules\AI\App\Services\Tenant\Tenant2ProductSearchService::class);
                    \Log::info('🏢 Using Tenant2ProductSearchService', ['tenant_id' => $tenantId]);
                } else {
                    // 🌍 Generic: Tüm diğer 10000 tenant
                    $productSearchService = app(\App\Services\AI\ProductSearchService::class);
                    \Log::info('🌍 Using Generic ProductSearchService', ['tenant_id' => $tenantId]);
                }

                \Log::info('🔍 Product search query', [
                    'original_message' => $validated['message'],
                    'search_query' => $searchQuery,
                    'tenant_id' => tenant('id')
                ]);

                $smartSearchResults = $productSearchService->searchProducts($searchQuery);
                $userSentiment = $productSearchService->detectUserSentiment($validated['message']);

                // 🆕 iXTİF ÖZEL: Fiyat sorgusu detection (en ucuz, en pahalı)
                $isPriceQuery = false;

                if (tenant('id') == 2 || tenant('id') == 3) { // iXtif tenants
                    $lowerMessage = mb_strtolower($validated['message']);
                    // Fiyat kelimesi geçiyorsa veya en ucuz/pahalı sorgusu varsa
                    $isPriceQuery = preg_match('/(fiyat|kaç\s*para|ne\s*kadar|maliyet|ücret|tutar|en\s+ucuz|en\s+uygun|en\s+pahal[ıi])/i', $lowerMessage);

                    // Eğer fiyat sorgusu ise ve ürün adı varsa, o ürünü ara
                    $searchForProduct = false;
                    if ($isPriceQuery && !preg_match('/(en\s+ucuz|en\s+uygun|en\s+pahal[ıi])/i', $lowerMessage)) {
                        // Spesifik ürün fiyatı soruluyor (örn: "F4 fiyatı", "CPD18TVL fiyatı")
                        $searchForProduct = true;
                    }
                }

                // Normal search (ürün başlığı/kategori araması)
                $smartSearchResults = $productSearchService->searchProducts($searchQuery);
                $userSentiment = $productSearchService->detectUserSentiment($validated['message']);

                // 🆕 iXTİF ÖZEL: Fiyat sorgusunda direkt DB'den getir (Meilisearch'te fiyat sync sorunu var)
                if ($isPriceQuery) {
                    \Log::info('🔍 iXtif Price Query - Fetching from DB', [
                        'query' => $validated['message'],
                        'tenant_id' => tenant('id'),
                        'searchForProduct' => $searchForProduct ?? false
                    ]);

                    // Yedek Parça kategorisini atla (ID: 44 - Çatal Kılıf)
                    $isCheapest = preg_match('/(en\s+ucuz|en\s+uygun)/i', mb_strtolower($validated['message']));

                    $query = \Modules\Shop\App\Models\ShopProduct::whereNotNull('base_price')
                        ->where('base_price', '>', 0)
                        ->where('category_id', '!=', 44); // Yedek parça HARİÇ

                    // Eğer spesifik ürün fiyatı soruluyorsa, ürün adını ara
                    if ($searchForProduct) {
                        // Mesajdan ürün kodlarını çıkar (F4, CPD18TVL, EFL181 gibi)
                        preg_match_all('/\b([A-Z]{1,3}\d{1,3}[A-Z]*\d*[A-Z]*)\b/i', $validated['message'], $matches);

                        if (!empty($matches[1])) {
                            $query->where(function($q) use ($matches, $validated) {
                                foreach ($matches[1] as $productCode) {
                                    $q->orWhere('title', 'LIKE', '%' . $productCode . '%')
                                      ->orWhere('sku', 'LIKE', '%' . $productCode . '%');
                                }
                                // Ayrıca tam mesajı da ara (örn: "transpalet" kelimesi)
                                $keywords = ['transpalet', 'forklift', 'istif'];
                                foreach ($keywords as $keyword) {
                                    if (stripos($validated['message'], $keyword) !== false) {
                                        $q->orWhere('title', 'LIKE', '%' . $keyword . '%');
                                    }
                                }
                            });
                        } else {
                            // Ürün kodu bulunamadı, genel arama yap
                            $query->where('title', 'LIKE', '%' . str_replace(['fiyat', 'fiyatı', 'kaç', 'para', 'ne kadar'], '', $validated['message']) . '%');
                        }
                    }

                    $query
                        // SIRALAMA ÖNCELİĞİ: Homepage → Stok → Sort Order → Fiyat
                        ->orderByRaw('show_on_homepage DESC, homepage_sort_order ASC')
                        ->orderBy('current_stock', 'desc')
                        ->orderBy('sort_order', 'asc');

                    // Fiyat sıralaması en sonda
                    if ($isCheapest) {
                        $query->orderBy('base_price', 'asc');
                    } else {
                        $query->orderBy('base_price', 'desc');
                    }

                    $products = $query->limit(5)->get();

                    // Format products for AI
                    $formattedProducts = $products->map(function($p) {
                        return [
                            'title' => $p->getTranslated('title', app()->getLocale()),
                            'slug' => $p->getTranslated('slug', app()->getLocale()),
                            'base_price' => $p->base_price,
                            'currency' => $p->currency ?? 'TRY', // USD, TRY, EUR
                            'current_stock' => $p->current_stock ?? 0,
                            'show_on_homepage' => $p->show_on_homepage ?? 0,
                            'homepage_sort_order' => $p->homepage_sort_order ?? 999, // Homepage sıralaması
                            'sort_order' => $p->sort_order ?? 0, // Kategori içi sıralama
                            'category_id' => $p->category_id,
                        ];
                    })->toArray();

                    $smartSearchResults = [
                        'products' => $formattedProducts,
                        'count' => count($formattedProducts),
                        'search_layer' => 'ixtif_price_query',
                        'tenant_id' => tenant('id')
                    ];
                }

                \Log::info('🔍 Smart Search Results', [
                    'products_found' => $smartSearchResults['count'] ?? 0,
                    'search_layer' => $smartSearchResults['search_layer'] ?? 'none',
                    'user_sentiment' => $userSentiment['tone'] ?? 'neutral',
                    'tenant_id' => tenant('id')
                ]);
            } catch (\Exception $e) {
                \Log::error('❌ ProductSearchService failed', [
                    'error' => $e->getMessage(),
                    'tenant_id' => tenant('id'),
                    'message' => $validated['message']
                ]);

                // Re-throw to stop execution - NO FALLBACK
                throw new \Exception('Product search failed: ' . $e->getMessage());
            }

            // Build context options for orchestrator
            $contextOptions = [
                'product_id' => $validated['product_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'page_slug' => $validated['page_slug'] ?? null,
                'session_id' => $sessionId,
                'user_message' => $validated['message'], // ✅ Pass message for smart search
                'smart_search_results' => $smartSearchResults, // ✅ Include search results
                'user_sentiment' => $userSentiment, // ✅ Include sentiment analysis
            ];

            // DEBUG: Smart search results
            \Log::info('🔍🔍🔍 CONTEXT OPTIONS', [
                'has_smart_search' => !empty($smartSearchResults['products']),
                'product_count' => count($smartSearchResults['products'] ?? []),
                'first_product' => $smartSearchResults['products'][0] ?? null,
            ]);

            // Use ModuleContextOrchestrator to build full context
            $aiContext = $this->contextOrchestrator->buildUserContext(
                $validated['message'],
                $contextOptions
            );

            // 🧠 CONVERSATION MEMORY: Get last 10 messages for context (hız optimizasyonu)
            $conversationHistory = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // 🆕 NEW: Use Optimized Prompt Service (400 satır yerine 2000+)
            $optimizedPromptService = new \Modules\AI\App\Services\OptimizedPromptService();
            $enhancedSystemPrompt = $optimizedPromptService->getFullPrompt($aiContext, $conversationHistory);

            // 🔍 DEBUG: Log enhanced prompt (ilk 5000 karakter - ürün listesini görmek için)
            \Log::info('🤖 AI Optimized Prompt Preview', [
                'prompt_preview' => mb_substr($enhancedSystemPrompt, 0, 5000),
                'prompt_length' => strlen($enhancedSystemPrompt),
                'old_prompt_length' => '~15000 characters (2000+ lines)',
                'reduction_percentage' => '~75% smaller',
                'smart_search_products_count' => $smartSearchResults['count'] ?? 0,
                'user_sentiment' => $userSentiment['tone'] ?? 'neutral',
            ]);

            // 🔍 DEBUG: Log AI context URLs to check if they're correct (especially "i" starting products)
            if (!empty($aiContext['context']['modules']['shop']['all_products'])) {
                // İlk 5 ürünü logla, özellikle "i" ile başlayanları
                $productsToLog = array_slice($aiContext['context']['modules']['shop']['all_products'], 0, 5);
                $iStartingProducts = [];

                foreach ($productsToLog as $product) {
                    $title = is_array($product['title']) ? json_encode($product['title']) : $product['title'];
                    if (stripos($title, 'ixtif') !== false || stripos($title, 'İXTİF') !== false) {
                        $iStartingProducts[] = [
                            'title' => $title,
                            'url' => $product['url'] ?? 'N/A',
                            'slug_starts_with_i' => str_starts_with(basename($product['url'] ?? ''), 'i'),
                        ];
                    }
                }

                if (!empty($iStartingProducts)) {
                    \Log::info('🔍 AI Context - Products with "i" check', [
                        'count' => count($iStartingProducts),
                        'products' => $iStartingProducts,
                    ]);
                }
            }

            // Call AI service with enhanced system prompt + conversation history
            // 🔄 AUTOMATIC FALLBACK CHAIN: GPT-4o-mini → Claude-Haiku → DeepSeek
            $aiResponseText = null;
            $usedModel = 'gpt-4o-mini';

            try {
                $aiResponse = $this->aiService->ask($validated['message'], [
                    'temperature' => 0.7,
                    'custom_prompt' => $enhancedSystemPrompt,
                    'conversation_history' => $conversationHistory, // 🧠 Last 20 messages
                ]);

                // ⚠️ CRITICAL FIX: ask() metodu array döndürebilir (error durumunda)
                // String değilse ve success=false ise fallback'e gir
                if (is_array($aiResponse) && isset($aiResponse['success']) && $aiResponse['success'] === false) {
                    throw new \Exception($aiResponse['error'] ?? 'AI API failed');
                }

                // Normal string response
                $aiResponseText = is_string($aiResponse) ? $aiResponse : ($aiResponse['response'] ?? $aiResponse['content'] ?? '');
            } catch (\Exception $aiError) {
                // 🔄 FALLBACK LAYER 1: GPT-5-mini → GPT-4o-mini
                if (str_contains($aiError->getMessage(), '429') || str_contains($aiError->getMessage(), 'Rate limit') || str_contains($aiError->getMessage(), 'rate_limit')) {
                    Log::warning('🔴 GPT-5-mini rate limit hit, falling back to GPT-4o-mini', [
                        'error' => $aiError->getMessage()
                    ]);

                    try {
                        $openAIProvider = \Modules\AI\App\Models\AIProvider::where('name', 'openai')
                            ->where('is_active', true)
                            ->first();

                        if ($openAIProvider) {
                            $fallbackService = new \Modules\AI\App\Services\OpenAIService([
                                'provider_id' => $openAIProvider->id,
                                'api_key' => $openAIProvider->api_key,
                                'base_url' => $openAIProvider->base_url,
                                'model' => 'gpt-4o-mini',
                            ]);

                            $aiResponseText = $fallbackService->ask($validated['message'], [
                                'temperature' => 0.7,
                                'custom_prompt' => $enhancedSystemPrompt,
                                'conversation_history' => $conversationHistory,
                            ]);

                            $usedModel = 'gpt-4o-mini';
                            Log::info('✅ Successfully used GPT-4o-mini fallback');
                        }
                    } catch (\Exception $fallback1Error) {
                        // 🔄 FALLBACK LAYER 2: GPT-4o-mini → Claude-Haiku
                        Log::warning('🟡 GPT-4o-mini failed, falling back to Claude-Haiku', [
                            'error' => $fallback1Error->getMessage()
                        ]);

                        try {
                            $claudeProvider = \Modules\AI\App\Models\AIProvider::where('name', 'anthropic')
                                ->where('is_active', true)
                                ->first();

                            if ($claudeProvider) {
                                $claudeService = new \Modules\AI\App\Services\ClaudeService([
                                    'provider_id' => $claudeProvider->id,
                                    'api_key' => $claudeProvider->api_key,
                                    'base_url' => $claudeProvider->base_url,
                                    'model' => 'claude-3-haiku-20240307',
                                ]);

                                $aiResponseText = $claudeService->ask($validated['message'], [
                                    'temperature' => 0.7,
                                    'custom_prompt' => $enhancedSystemPrompt,
                                    'conversation_history' => $conversationHistory,
                                ]);

                                $usedModel = 'claude-3-haiku';
                                Log::info('✅ Successfully used Claude-Haiku fallback');
                            }
                        } catch (\Exception $fallback2Error) {
                            // 🔄 FALLBACK LAYER 3: Claude-Haiku → DeepSeek
                            Log::warning('🟠 Claude-Haiku failed, falling back to DeepSeek', [
                                'error' => $fallback2Error->getMessage()
                            ]);

                            try {
                                $deepseekProvider = \Modules\AI\App\Models\AIProvider::where('name', 'deepseek')
                                    ->where('is_active', true)
                                    ->first();

                                if ($deepseekProvider) {
                                    $deepseekService = new \Modules\AI\App\Services\OpenAIService([
                                        'provider_id' => $deepseekProvider->id,
                                        'api_key' => $deepseekProvider->api_key,
                                        'base_url' => $deepseekProvider->base_url,
                                        'model' => $deepseekProvider->default_model ?? 'deepseek-chat',
                                    ]);

                                    $aiResponseText = $deepseekService->ask($validated['message'], [
                                        'temperature' => 0.7,
                                        'custom_prompt' => $enhancedSystemPrompt,
                                        'conversation_history' => $conversationHistory,
                                    ]);

                                    $usedModel = 'deepseek-chat';
                                    Log::info('✅ Successfully used DeepSeek fallback');
                                }
                            } catch (\Exception $fallback3Error) {
                                Log::error('❌ All AI providers failed', [
                                    'gpt5mini_error' => $aiError->getMessage(),
                                    'gpt4o_error' => $fallback1Error->getMessage(),
                                    'haiku_error' => $fallback2Error->getMessage(),
                                    'deepseek_error' => $fallback3Error->getMessage(),
                                ]);

                                $aiResponseText = 'Üzgünüm, şu anda AI servisleri geçici olarak kullanılamıyor. Lütfen birkaç dakika sonra tekrar deneyin.';
                                $usedModel = 'none';
                            }
                        }
                    }
                } else {
                    throw $aiError; // Re-throw if not rate limit error
                }
            }

            // 🔍 DEBUG: Log AI response BEFORE post-processing
            \Log::info('🤖 AI Response BEFORE post-processing', [
                'response_preview' => mb_substr($aiResponseText, 0, 500),
                'contains_ixtif' => str_contains($aiResponseText, 'ixtif'),
                'contains_xtif' => str_contains($aiResponseText, 'xtif'),
            ]);

            // 🔧 POST-PROCESSING: Fix broken URLs in AI response (context-aware)
            $aiResponseText = $this->fixBrokenUrls($aiResponseText, $aiContext);

            // 🔍 DEBUG: Log AI response AFTER post-processing
            \Log::info('✅ AI Response AFTER post-processing', [
                'response_preview' => mb_substr($aiResponseText, 0, 500),
                'contains_ixtif' => str_contains($aiResponseText, 'ixtif'),
                'contains_xtif' => str_contains($aiResponseText, 'xtif'),
            ]);

            // Format response for compatibility
            $aiResponse = [
                'content' => $aiResponseText,
                'model' => $usedModel, // Hangi model kullanıldı
                'usage' => [
                    'total_tokens' => 0, // Will be calculated if available
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                ],
            ];

            // Save user message
            AIMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $validated['message'],
                'context_data' => $contextOptions,
            ]);

            // Save AI response
            $assistantMessage = AIMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $aiResponse['content'] ?? '',
                'model' => $aiResponse['model'] ?? 'unknown',
                'tokens_used' => $aiResponse['usage']['total_tokens'] ?? 0,
                'prompt_tokens' => $aiResponse['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $aiResponse['usage']['completion_tokens'] ?? 0,
            ]);

            // Update conversation
            $conversation->update([
                'last_message_at' => now(),
                'message_count' => $conversation->messages()->count(),
            ]);

            // 📞 PHONE NUMBER DETECTION & TELESCOPE LOGGING
            $this->detectPhoneNumberAndLogToTelescope($conversation);

            // 🔧 WhatsApp Link Post-Processing Fix
            // AI bazen WhatsApp linkini ürün linki ile karıştırıyor, düzeltelim
            $finalMessage = $aiResponse['content'] ?? '';
            $finalMessage = $this->fixWhatsAppLinks($finalMessage);

            // 📝 Format List Items - Convert inline list to proper markdown list
            // AI bazen "için: - item1 - item2" şeklinde yazar, "için:\n- item1\n- item2" yapmalıyız
            $finalMessage = $this->formatListItems($finalMessage);

            // 📝 MARKDOWN - Frontend'e RAW olarak gönder (Frontend render edecek)
            // ❌ Backend'de HTML'e çevirme - Double-escape sorunu yaratıyor!
            // ✅ Frontend: window.aiChatRenderMarkdown() ile render edilecek

            \Log::info('🔍 Markdown response ready', [
                'preview' => mb_substr($finalMessage, 0, 200),
                'is_markdown' => (preg_match('/\*\*|\#\#|\-\s/', $finalMessage) ? 'YES' : 'NO')
            ]);

            // 🔍 VALIDATION: Check for AI hallucinations and errors
            $validator = app(\App\Services\AI\AIResponseValidator::class);
            $validationResult = $validator->validateAndFix($finalMessage, [
                'smart_search_results' => $smartSearchResults ?? [],
            ]);

            if ($validationResult['has_errors']) {
                \Log::warning('⚠️ AI Response validation errors detected', [
                    'errors' => $validationResult['errors'],
                    'conversation_id' => $conversation->id,
                ]);
                $finalMessage = $validationResult['fixed'];
            }

            if ($validationResult['has_warnings']) {
                \Log::info('ℹ️ AI Response warnings', [
                    'warnings' => $validationResult['warnings'],
                    'conversation_id' => $conversation->id,
                ]);
            }

            \Log::info('🔍 FINAL MESSAGE (before return)', [
                'preview' => mb_substr($finalMessage, 0, 200),
                'has_html_tags' => (preg_match('/<h[1-6]>|<ul>|<li>|<strong>/', $finalMessage) ? 'YES' : 'NO'),
                'length' => mb_strlen($finalMessage)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $finalMessage,
                    'session_id' => $sessionId,
                    'conversation_id' => $conversation->id,
                    'message_id' => $assistantMessage->id,
                    'assistant_name' => $aiContext['context']['assistant_name'] ?? 'AI Asistan',
                    'context_used' => [
                        'modules' => array_keys($aiContext['context']['modules'] ?? []),
                        'product_id' => $validated['product_id'] ?? null,
                        'category_id' => $validated['category_id'] ?? null,
                    ],
                    'credits_used' => 0, // Shop assistant is free
                    'tokens_used' => $aiResponse['usage']['total_tokens'] ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.shopAssistantChat failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'message' => $request->input('message', 'N/A'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Bir hata oluştu. Lütfen tekrar deneyin.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * 🔐 Generate IP-based session ID
     */
    private function generateSessionId(Request $request): string
    {
        $data = [
            $request->ip(),
            $request->userAgent() ?? 'unknown',
            tenant('id'),
        ];

        return md5(implode('|', $data));
    }

    /**
     * 📱 Detect device type from user agent
     */
    private function detectDeviceType(Request $request): string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        // Mobile patterns
        $mobilePatterns = ['mobile', 'android', 'iphone', 'ipod', 'blackberry', 'windows phone'];
        foreach ($mobilePatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return 'mobile';
            }
        }

        // Tablet patterns
        $tabletPatterns = ['tablet', 'ipad', 'kindle', 'playbook'];
        foreach ($tabletPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return 'tablet';
            }
        }

        return 'desktop';
    }

    /**
     * 🌐 Detect browser from user agent
     */
    private function detectBrowser(Request $request): string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        $browsers = [
            'edge' => 'Edge',
            'edg' => 'Edge',
            'opr' => 'Opera',
            'opera' => 'Opera',
            'chrome' => 'Chrome',
            'safari' => 'Safari',
            'firefox' => 'Firefox',
            'msie' => 'Internet Explorer',
            'trident' => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $name) {
            if (str_contains($userAgent, $key)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * 💻 Detect OS from user agent
     */
    private function detectOS(Request $request): string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        $osList = [
            'windows nt 10' => 'Windows 10',
            'windows nt 11' => 'Windows 11',
            'windows nt 6.3' => 'Windows 8.1',
            'windows nt 6.2' => 'Windows 8',
            'windows nt 6.1' => 'Windows 7',
            'mac os x' => 'macOS',
            'iphone' => 'iOS',
            'ipad' => 'iOS',
            'android' => 'Android',
            'linux' => 'Linux',
            'ubuntu' => 'Ubuntu',
        ];

        foreach ($osList as $key => $name) {
            if (str_contains($userAgent, $key)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * 🎨 Build enhanced system prompt with product context
     *
     * Combines base system prompt with module-specific context (Product, Category, Page)
     *
     * ============================================================================
     * 🌐 MİMARİ NOTLARI - GLOBAL vs TENANT-SPECIFIC PROMPTS
     * ============================================================================
     *
     * Bu dosya (PublicAIController.php) GLOBAL bir sistem dosyasıdır.
     * Bu controller'daki prompt kuralları TÜM TENANTLAR için geçerlidir (1000+ tenant).
     *
     * ⚠️ ÖNEMLI KURALLAR:
     * 1. Bu dosyada SADECE EVRENSEL kurallar olmalı (örn: "Sadece ürünlerden bahset")
     * 2. Tenant-specific prompt kuralları AYRI DOSYALARDA tutulmalı
     * 3. Prompt'ları kısa ve öz tutun (token tasarrufu + okunabilirlik)
     *
     * 📂 TENANT-SPECIFIC PROMPT DOSYALARI:
     * - Modules/AI/app/Services/Tenant/Tenant2PromptService.php (tenant 2, 3 - iXTİF)
     * - Diğer tenantlar için Services/Tenant/Tenant{ID}PromptService.php oluştur
     *
     * 🔄 NASIL ÇALIŞIR:
     * - Global promptlar (bu dosya) önce eklenir
     * - Tenant ID kontrolü yapılır (örn: tenant('id') == 2)
     * - Eğer tenant-specific prompt varsa, o da eklenir (satır 958-961)
     * - Final prompt = Global + Tenant-Specific (kombine)
     *
     * ✅ ÖRNEK:
     * if (tenant('id') == 2) {
     *     $tenant2Service = new Tenant2PromptService();
     *     $prompts[] = $tenant2Service->getPromptAsString();
     * }
     *
     * ============================================================================
     */
    private function buildEnhancedSystemPrompt(array $aiContext, array $conversationHistory = []): string
    {
        // ⚡ CRITICAL ULTRA-STRICT SYSTEM PROMPT (ZORUNLU KURALLAR)
        $systemPrompt = $this->buildSystemPrompt();

        // 📋 USER CONTEXT (Ürünler, kategoriler, sayfa context)
        $userContext = $this->buildUserContext($aiContext);

        // 🔄 CONVERSATION HISTORY CHECK
        $historyWarning = "";
        if (!empty($conversationHistory)) {
            // Eğer conversation history varsa, "Merhaba" tekrarını engelle
            $hasGreeting = false;
            foreach ($conversationHistory as $msg) {
                if ($msg['role'] === 'assistant' && preg_match('/\b(merhaba|selam|iyi günler|hoş geldin)/i', $msg['content'])) {
                    $hasGreeting = true;
                    break;
                }
            }

            if ($hasGreeting) {
                $historyWarning = "\n\n⚠️ KRİTİK UYARI: Bu konuşmanın DEVAMI! İlk mesajda zaten selamlaştın. Şimdi 'Merhaba' deme, direkt konuya gir!\n";
            }
        }

        // Return as single string for 'custom_prompt' option
        return $systemPrompt . $historyWarning . "\n\n" . $userContext;
    }

    /**
     * ⚡ SYSTEM PROMPT - Zorunlu, tartışılmaz kurallar
     * Bu kurallar HER DURUMDA geçerlidir!
     */
    private function buildSystemPrompt(): string
    {
        $prompts = [];

        $prompts[] = "# 🤖 SİSTEM KURALLARI - ZORUNLU!";
        $prompts[] = "";
        $prompts[] = "## 📝 OUTPUT FORMAT - HER CEVAP İÇİN ZORUNLU!";
        $prompts[] = "";
        $prompts[] = "**⚡ KRİTİK: MARKDOWN FORMAT KULLAN! HTML YASAK!**";
        $prompts[] = "";
        $prompts[] = "**ZORUNLU MARKDOWN FORMAT:**";
        $prompts[] = "- Paragraflar: Boş satırla ayır";
        $prompts[] = "- Liste: Her öğe YENİ SATIRDA '- ' ile başla (tire+boşluk)";
        $prompts[] = "  ❌ YANLIŞ: 'öneriler: - item1 - item2 - item3' (aynı satırda)";
        $prompts[] = "  ✅ DOĞRU:";
        $prompts[] = "  - item1";
        $prompts[] = "  - item2";
        $prompts[] = "  - item3";
        $prompts[] = "- Vurgu: **kalın metin**";
        $prompts[] = "";
        $prompts[] = "**⛔ YASAKLAR:**";
        $prompts[] = "1. ❌ HTML tagları YASAK! (<p>, <ul>, <li>, <br> vb.)";
        $prompts[] = "2. ❌ '<>' veya '<><>' karakterleri YASAK!";
        $prompts[] = "3. ❌ Plain text YASAK! → ✅ Markdown formatını kullan";
        $prompts[] = "4. ❌ Aynı konuşmada 2. kere 'Merhaba' deme! → İlk mesajda selamlaştın, sonra direkt konuya gir!";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU ÖRNEK:**";
        $prompts[] = "Merhaba! Size yardımcı olmak isterim.";
        $prompts[] = "";
        $prompts[] = "İşte sorularım:";
        $prompts[] = "- Hangi kapasite arıyorsunuz?";
        $prompts[] = "- Elektrikli mi, manuel mi?";
        $prompts[] = "";
        $prompts[] = "Bu bilgilerle size en uygun ürünü bulabilirim!";
        $prompts[] = "";
        $prompts[] = "**❌ YANLIŞ ÖRNEK:**";
        $prompts[] = "<p>Merhaba</p> ← HTML YASAK!";
        $prompts[] = "Hangi kapasite?<> ← <> YASAK!";
        $prompts[] = "";
        $prompts[] = "## 🔗 LINK FORMAT - ZORUNLU!";
        $prompts[] = "";
        $prompts[] = "**TEK GEÇERLİ FORMAT:**";
        $prompts[] = "**Ürün Adı** [LINK:shop:slug-buraya]";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU:**";
        $prompts[] = "**Litef EPT15** [LINK:shop:litef-ept15]";
        $prompts[] = "";
        $prompts[] = "**❌ YANLIŞ:**";
        $prompts[] = "[Litef EPT15](https://ixtif.com/shop/...) ← Markdown YASAK!";
        $prompts[] = "<a href=\"...\">Litef EPT15</a> ← HTML link YASAK!";
        $prompts[] = "**[Litef EPT15](url)** ← Bu format YASAK!";
        $prompts[] = "";
        $prompts[] = "## 🎯 ROL";
        $prompts[] = "";
        $prompts[] = "Sen profesyonel satış danışmanısın. Sadece şirket ürünleri hakkında konuş.";
        $prompts[] = "";

        return implode("\n", $prompts);
    }

    /**
     * 📋 USER CONTEXT - Ürünler, kategoriler, senaryo kuralları
     */
    private function buildUserContext(array $aiContext): string
    {
        $prompts = [];

        // 🌐 Get dynamic domain (mevcut tenant'ın domain'i)
        $siteUrl = request()->getSchemeAndHttpHost();

        // 🚨 EN ÖNCELİKLİ: GLOBAL RULES (All tenants) - AI'ın İLK okuması gereken kurallar
        $prompts[] = "## 🎯 KAPSAM";
        $prompts[] = "";
        $prompts[] = "**KAPSAM:** Sadece şirket ürünleri/hizmetleri";
        $prompts[] = "**YASAK:** Siyaset, din, genel bilgi, konu dışı konular";
        $prompts[] = "";
        $prompts[] = "## 🔄 DOĞRU KONUŞMA AKIŞI (KRİTİK!)";
        $prompts[] = "";
        $prompts[] = "### 🎯 ÖNCELİK KONTROLÜ (İLK ADIM!)";
        $prompts[] = "**HER CEVAP VERMEDEN ÖNCE KONTROL ET:**";
        $prompts[] = "";
        $prompts[] = "**ADIM 1: Ürün sayfasında mıyım?**";
        $prompts[] = "→ 'Konuşulan Ürün' bölümüne bak!";
        $prompts[] = "→ ✅ Ürün varsa: SENARYO 4 (Direkt ürün hakkında konuş!)";
        $prompts[] = "→ ❌ Ürün yoksa: ADIM 2'ye geç";
        $prompts[] = "";
        $prompts[] = "**ADIM 2: Spesifik ürün adı söyledi mi?**";
        $prompts[] = "→ Örnek: '[ÜRÜN ADI] hakkında', '[MARKA MODEL] nasıl'";
        $prompts[] = "→ ✅ Ürün adı varsa: SENARYO 4 (O ürünü bul, anlat!)";
        $prompts[] = "→ ❌ Genel talep: ADIM 3'e geç";
        $prompts[] = "";
        $prompts[] = "**ADIM 3: Yeterli detay var mı? (2+ bilgi)";
        $prompts[] = "→ Kontrol: Kapasite + Tip + Kullanım + Ortam gibi";
        $prompts[] = "→ ✅ 2+ detay var: SENARYO 3 (Ürün öner!)";
        $prompts[] = "→ ❌ Sadece 'transpalet' gibi: SENARYO 2 (SORU SOR!)";
        $prompts[] = "";
        $prompts[] = "### ✅ SENARYO 1: Genel Selamlaşma (ÜRÜN SAYFASI DEĞİLSE!)";
        $prompts[] = "Kullanıcı: 'Merhaba' / 'Selam' / 'İyi günler'";
        $prompts[] = "";
        $prompts[] = "**🚨 ZORUNLU YANIT (AYNEN KULLAN, EKSTRA BİR ŞEY SÖYLEME!):**";
        $prompts[] = "'Merhaba! Size nasıl yardımcı olabilirim? 😊'";
        $prompts[] = "";
        $prompts[] = "**❌ KESINLIKLE YASAKLAR:**";
        $prompts[] = "- ❌ Ürün kategorisi adı SÖYLEME! (transpalet, istif makinesi, forklift vb.)";
        $prompts[] = "- ❌ 'Transpaletler hakkında bilgi mi istersiniz' gibi SORULAR SORMA!";
        $prompts[] = "- ❌ Ürün önerisi YAPMA!";
        $prompts[] = "- ❌ SADECE yukarıdaki cümleyi söyle ve BEKLE!";
        $prompts[] = "";
        $prompts[] = "### ✅ SENARYO 2: Genel Ürün Talebi (ÖNCE SORU SOR!)";
        $prompts[] = "";
        $prompts[] = "**🚨 KRİTİK - MUTLAKA KONTROL ET:**";
        $prompts[] = "Kullanıcı SADECE kategori/tip söylediyse (transpalet, forklift, reach truck, istif makinesi vb.)";
        $prompts[] = "→ ❌ ASLA direkt ürün önerme!";
        $prompts[] = "→ ✅ MUTLAKA önce detayları sor!";
        $prompts[] = "";
        $prompts[] = "**🔍 KATEGORİ-BASED ARAMA (KRİTİK - YANLIŞ EŞLEŞTİRME YAPMA!):**";
        $prompts[] = "";
        $prompts[] = "**🚨 ÖNEMLİ: Kullanıcının söylediği KELİMEYİ 'Mevcut Kategoriler' listesinden AYNEN BUL!**";
        $prompts[] = "";
        $prompts[] = "**ADIMLAR:**";
        $prompts[] = "1. Kullanıcı kategori adı söyledi mi? (reach truck, transpalet, forklift vb.)";
        $prompts[] = "2. 'Mevcut Kategoriler' listesini TAM OLARAK tara";
        $prompts[] = "3. Kullanıcının KELİMESİ ile eşleşen kategoriyi bul (case-insensitive, Türkçe karakter duyarsız)";
        $prompts[] = "4. ✅ TAM EŞLEŞİRSE: O kategorideki ürünleri göster";
        $prompts[] = "5. ❌ EŞLEŞMİYORSA: 'Bu kategoride ürün bulamadım' de, alternatif kategoriler öner";
        $prompts[] = "";
        $prompts[] = "**⛔ YASAKLAR (ÇOK ÖNEMLİ!):**";
        $prompts[] = "❌ 'reach truck' dedi diye 'istif makinesi' kategorisini GÖSTERME!";
        $prompts[] = "❌ 'forklift' dedi diye 'transpalet' kategorisini GÖSTERME!";
        $prompts[] = "❌ Kategori isimleri birbirine KARIŞTIRMA!";
        $prompts[] = "❌ Eşleşmiyorsa başka kategoriden ürün ÖNERİ YAPMA!";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU Kategori Eşleştirme:**";
        $prompts[] = "- Kullanıcı: 'reach truck' → Sadece 'Reach Truck' kategorisinden göster";
        $prompts[] = "- Kullanıcı: 'istif makinesi' → Sadece 'İstif Makinesi' kategorisinden göster";
        $prompts[] = "- Kullanıcı: 'transpalet' → Sadece 'Transpalet' kategorisinden göster";
        $prompts[] = "- Kullanıcı: 'forklift' → Sadece 'Forklift' kategorisinden göster";
        $prompts[] = "";
        $prompts[] = "**❌ YANLIŞ (ASLA YAPMA!):**";
        $prompts[] = "- Kullanıcı: 'reach truck' → 'İstif Makinesi' önerme! ❌";
        $prompts[] = "- Kullanıcı: 'forklift' → 'Transpalet' önerme! ❌";
        $prompts[] = "";
        $prompts[] = "**🎯 Kategori bulunca ne yap:**";
        $prompts[] = "- Önce kategori linkini ver: **[Kategori Adı]** [LINK:shop:category:slug]";
        $prompts[] = "- Sonra o kategorideki ürünleri listele (category field'ına bak!)";
        $prompts[] = "- Ürünleri gösterirken **MUTLAKA** soruları sor!";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER (HEPSİNDE DETAY YOK!):**";
        $prompts[] = "- 'Transpalet istiyorum' → ❌ Kapasite yok, tip yok";
        $prompts[] = "- 'İstif makinesi arıyorum' → ❌ Kapasite yok, ortam yok";
        $prompts[] = "- 'Reach truck alacaktım' → ❌ Kapasite yok, özellik yok";
        $prompts[] = "- 'Forklift lazım' → ❌ Kapasite yok, kullanım yok";
        $prompts[] = "- 'Soğuk hava için ürün' → ❌ Kapasite yok, tip yok";
        $prompts[] = "";
        $prompts[] = "**🚨 ZORUNLU ADIMLAR (SIRASINI TAKIP ET!):**";
        $prompts[] = "";
        $prompts[] = "**1. ADIM: ÖNCE DETAYLARI SOR! (İhtiyaç analizi)**";
        $prompts[] = "";
        $prompts[] = "**KATEGORİYE ÖZEL SORULAR ÜRETİN (MARKDOWN FORMAT!):**";
        $prompts[] = "";
        $prompts[] = "**TRANSPALET için:**";
        $prompts[] = "```";
        $prompts[] = "Tabii! Size en uygun transpalet'i önerebilmem için birkaç soru sormama izin verin:";
        $prompts[] = "";
        $prompts[] = "- Hangi kapasite aralığında transpalet arıyorsunuz? (1.5 ton, 2 ton, 2.5 ton vb.)";
        $prompts[] = "- Elektrikli mi yoksa manuel mi tercih edersiniz?";
        $prompts[] = "- Kullanım sıklığınız nedir? (Günlük yoğun / Haftalık / Ara sıra)";
        $prompts[] = "- Kullanacağınız ortam? (İç mekan / Dış mekan / Soğuk hava deposu)";
        $prompts[] = "";
        $prompts[] = "Bu bilgilerle size tam ihtiyacınıza uygun ürünü önerebilirim! 😊";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**REACH TRUCK için:**";
        $prompts[] = "```";
        $prompts[] = "Mükemmel! Size en uygun reach truck'u önerebilmem için detayları öğrenmem gerek:";
        $prompts[] = "";
        $prompts[] = "- Hangi kaldırma yüksekliğine ihtiyacınız var? (3m, 5m, 7m, 10m vb.)";
        $prompts[] = "- Hangi kapasite aralığında? (1.5 ton, 2 ton, 2.5 ton vb.)";
        $prompts[] = "- Dar koridor kullanımı mı? (Depo genişliği önemli mi?)";
        $prompts[] = "- Günlük ne kadar süre kullanılacak? (Batarya kapasitesi için önemli)";
        $prompts[] = "";
        $prompts[] = "Bu bilgilerle size en uygun modeli bulabilirim! 😊";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**İSTİF MAKİNESİ için:**";
        $prompts[] = "```";
        $prompts[] = "Harika! Size en uygun istif makinesini önerebilmem için şu bilgilere ihtiyacım var:";
        $prompts[] = "";
        $prompts[] = "- Hangi kaldırma yüksekliğine ihtiyacınız var? (3m, 5m, 7m vb.)";
        $prompts[] = "- Hangi kapasite? (1.5 ton, 2 ton, 2.5 ton vb.)";
        $prompts[] = "- Dar koridor mu, normal depo mu?";
        $prompts[] = "- Günlük kullanım süresi ne kadar?";
        $prompts[] = "";
        $prompts[] = "Bu bilgilerle tam ihtiyacınıza uygun istif makinesini bulabilirim! 😊";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**FORKLIFT için:**";
        $prompts[] = "```";
        $prompts[] = "Anladım! Size en uygun forklift'i önerebilmem için şu bilgilere ihtiyacım var:";
        $prompts[] = "";
        $prompts[] = "- Hangi kapasite? (1.5 ton, 2 ton, 3 ton, 5 ton vb.)";
        $prompts[] = "- Elektrikli (akü/Li-Ion) mi, LPG/Dizel mi tercih edersiniz?";
        $prompts[] = "- Kullanım alanı? (İç mekan depo / Dış alan / İkisi de)";
        $prompts[] = "- Kaldırma yüksekliği ne kadar olmalı?";
        $prompts[] = "- Günlük kaç saat kullanılacak?";
        $prompts[] = "";
        $prompts[] = "Bu bilgilerle size perfect bir öneri sunabilirim! 😊";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**⚠️ ÖNEMLİ:** REACH TRUCK ≠ İSTİF MAKİNESİ!";
        $prompts[] = "- **Reach Truck**: Dar koridor, yüksek kaldırma, ileri uzanan çatallar";
        $prompts[] = "- **İstif Makinesi**: Genel istif, daha basit, daha ekonomik";
        $prompts[] = "- Kullanıcı 'reach truck' derse sadece reach truck ürünleri göster!";
        $prompts[] = "- Kullanıcı 'istif makinesi' derse sadece istif makinesi ürünleri göster!";
        $prompts[] = "";
        $prompts[] = "**DİĞER KATEGORİLER için benzer mantıkla kategori-specific sorular üret!**";
        $prompts[] = "";
        $prompts[] = "**2. ADIM: CEVAP GELDİKTEN SONRA ÜRÜN ÖNER!**";
        $prompts[] = "- Kullanıcı ihtiyaçlarını belirttikten SONRA 'Mevcut Ürünler' listesinden UYGUN ürünleri bul";
        $prompts[] = "- SLUG'ı listeden AYNEN kopyala (örnek üretme!)";
        $prompts[] = "- **Ürün Adı** [LINK:shop:SLUG] formatında link ver";
        $prompts[] = "";
        $prompts[] = "**ÖRNEK ÜRÜN ÖNERİSİ (DETAYLAR ÖĞRENİLDİKTEN SONRA):**";
        $prompts[] = "```";
        $prompts[] = "Harika! 1.5 ton elektrikli transpalet ihtiyacınıza göre şu ürünleri önerebilirim:";
        $prompts[] = "";
        $prompts[] = "⭐ **[GERÇEK ÜRÜN ADI]** [LINK:shop:[LİSTEDEKİ-SLUG]]";
        $prompts[] = "   - [Gerçek teknik özellikler]";
        $prompts[] = "   - [Gerçek kapasite bilgisi]";
        $prompts[] = "";
        $prompts[] = "NOT: Yukarıdaki örnekteki [GERÇEK ÜRÜN ADI] ve [LİSTEDEKİ-SLUG]'ı 'Mevcut Ürünler' listesinden al!";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "❌ **ASLA direkt ürün önerme!** ÖNCE detayları sor!";
        $prompts[] = "❌ **ASLA örnek ürün adı/slug uydurma!** Sadece 'Mevcut Ürünler' listesinden kullan!";
        $prompts[] = "❌ **ASLA 'genel bilgi' verme!** Detayları öğrendikten sonra gerçek ürünleri öner!";
        $prompts[] = "";
        $prompts[] = "### ✅ SENARYO 3: Detaylı Talep (ÜRÜN ÖNERİSİ AŞAMASI)";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER (MUTLAKA 2+ DETAY OLMALI!):**";
        $prompts[] = "- '1.5 ton elektrikli transpalet istiyorum' (✅ Kapasite + Tip!)";
        $prompts[] = "- '2 ton kapasiteli, soğuk hava için istif' (✅ Kapasite + Ortam!)";
        $prompts[] = "- 'Günlük yoğun kullanım için manuel transpalet' (✅ Kullanım + Tip!)";
        $prompts[] = "";
        $prompts[] = "**ŞİMDİ ÜRÜN ÖNERİSİ YAP:**";
        $prompts[] = "1. 'Mevcut Ürünler' listesini oku";
        $prompts[] = "2. İhtiyaca uygun 2-3 ürün seç";
        $prompts[] = "3. SLUG'ı listeden AYNEN kopyala";
        $prompts[] = "4. **Ürün Adı** [LINK:shop:SLUG] formatında link ver";
        $prompts[] = "";
        $prompts[] = "**FORMAT ÖRNEĞİ (GERÇEKÇİ DEĞİL, SADECE FORMAT GÖSTERMEK İÇİN!):**";
        $prompts[] = "```";
        $prompts[] = "Harika! İhtiyacınıza uygun transpaletler:";
        $prompts[] = "";
        $prompts[] = "⭐ **[LİSTEDEN ÜRÜN ADI]** [LINK:shop:[LİSTEDEN-SLUG]]";
        $prompts[] = "   - [LİSTEDEN teknik özellik]";
        $prompts[] = "   - [LİSTEDEN kapasite]";
        $prompts[] = "";
        $prompts[] = "⭐ **[LİSTEDEN DİĞER ÜRÜN]** [LINK:shop:[DİĞER-SLUG]]";
        $prompts[] = "   - [LİSTEDEN özellik]";
        $prompts[] = "";
        $prompts[] = "🔍 Karşılaştırma yapabilir, alternatif önerebilirsin.";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "❌ **ASLA yukarıdaki köşeli parantezli ifadeleri kullanma!**";
        $prompts[] = "✅ **SADECE 'Mevcut Ürünler' listesinden gerçek ürün adı + slug kullan!**";
        $prompts[] = "";

        // 📞 İletişim bilgilerini ekle (dinamik)
        $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();

        if (!empty($contactInfo['phone']) || !empty($contactInfo['whatsapp'])) {
            $prompts[] = "**📞 İLETİŞİM:**";
            $prompts[] = "";
            $prompts[] = "Dilerseniz numaranızı bırakabilirsiniz, uzmanlarımız sizinle iletişime geçecektir.";
            $prompts[] = "";
            $prompts[] = "Hemen bilgi almak isterseniz:";

            if (!empty($contactInfo['phone'])) {
                $prompts[] = "📞 Telefon: ({$contactInfo['phone']})";
            }

            if (!empty($contactInfo['whatsapp'])) {
                $prompts[] = "💬 WhatsApp: ({$contactInfo['whatsapp']})";
            }

            $prompts[] = "";
        }

        $prompts[] = "### ✅ SENARYO 4: Ürün Sayfasında VEYA Spesifik Ürün Sorusu";
        $prompts[] = "**A) Kullanıcı bir ÜRÜN SAYFASINDAYSA ('Konuşulan Ürün' bölümü doluysa):**";
        $prompts[] = "   - Kullanıcı 'merhaba' dese bile → O ürün hakkında direkt konuş!";
        $prompts[] = "   - Kullanıcı 'fiyatı ne kadar' dese → Fiyatı söyle!";
        $prompts[] = "   - Kullanıcı 'özellikleri' dese → Özellikleri listele!";
        $prompts[] = "   - Benzer ürünleri karşılaştır ve alternatif öner";
        $prompts[] = "";
        $prompts[] = "**B) Kullanıcı SPESİFİK ÜRÜN ADI SÖYLEDİYSE:**";
        $prompts[] = "   - '[ÜRÜN ADI] hakkında bilgi' → O ürünü listede bul, linkini ver, anlat!";
        $prompts[] = "   - '[MARKA MODEL] nasıl' → O ürünü listede ara, bul, detay ver!";
        $prompts[] = "   - Alternatif ürünler öner";
        $prompts[] = "   - NOT: [ÜRÜN ADI] ve [MARKA MODEL] placeholder'dır, gerçek ürün adlarını 'Mevcut Ürünler' listesinden kullan!";
        $prompts[] = "";
        $prompts[] = "## ❌ YASAKLAR";
        $prompts[] = "";
        $prompts[] = "- ❌ ANASAYFADA 'merhaba' dediğinde direkt ürün önerme! (Ama ürün sayfasındaysa öner!)";
        $prompts[] = "- ❌ FİYAT UYDURMA! Fiyat yoksa 'Fiyat için iletişime geçin' de";
        $prompts[] = "- ❌ TEKNİK ÖZELLİK UYDURMA! Data'da olmayan bilgi verme";
        $prompts[] = "- ❌ GENEL AÇIKLAMA YAPMA! Mevcut ürünleri listeden bulup link ver!";
        $prompts[] = "";
        $prompts[] = "## ✅ ÖZETİ HATIRLA";
        $prompts[] = "";
        $prompts[] = "**ÜRÜN SAYFASINDA mı?** → 'Konuşulan Ürün' bölümüne bak!";
        $prompts[] = "   - ✅ Ürün varsa: Direkt o ürün hakkında konuş (merhaba dese bile!)";
        $prompts[] = "   - ❌ Ürün yoksa (anasayfa): Genel selamlaşma yap, detay sor!";
        $prompts[] = "";

        // Base system prompt (personality, contact, knowledge base)
        $prompts[] = $aiContext['system_prompt'];

        // 📚 KNOWLEDGE BASE (All tenants - tenant-specific Q&A)
        try {
            $knowledgeBase = \Modules\SettingManagement\App\Models\AIKnowledgeBase::active()
                ->ordered()
                ->get();

            if ($knowledgeBase->isNotEmpty()) {
                $prompts[] = "\n## 📚 BİLGİ BANKASI (SSS)";
                $prompts[] = "Müşteri aşağıdaki konularda soru sorarsa bu cevapları kullan:\n";

                foreach ($knowledgeBase as $kb) {
                    $prompts[] = "**S: {$kb->question}**";
                    $prompts[] = "C: {$kb->answer}\n";
                }

                $prompts[] = "";
            }
        } catch (\Exception $e) {
            \Log::warning('Knowledge Base yüklenemedi', ['error' => $e->getMessage()]);
        }

        // 🎯 TENANT 2/3-SPECIFIC PROMPT (iXTİF)
        // Professional sales approach, category differentiation, phone collection, price/stock policy
        if (in_array(tenant('id'), [2, 3])) {
            $tenant2Service = new \Modules\AI\App\Services\Tenant\Tenant2PromptService();
            $prompts[] = $tenant2Service->getPromptAsString();
        }

        // Add module context if available
        if (!empty($aiContext['context']['modules'])) {
            $prompts[] = "\n## BAĞLAM BİLGİLERİ\n";

            // Shop context (Product or Category)
            if (!empty($aiContext['context']['modules']['shop'])) {
                $shopContext = $aiContext['context']['modules']['shop'];
                $prompts[] = $this->formatShopContext($shopContext);
            }

            // Page context
            if (!empty($aiContext['context']['modules']['page'])) {
                $pageContext = $aiContext['context']['modules']['page'];
                $prompts[] = $this->formatPageContext($pageContext);
            }
        }

        return implode("\n", $prompts);
    }


    /**
     * Format shop context for AI prompt
     */
    private function formatShopContext(array $shopContext): string
    {
        $formatted = [];

        // Current Product context (if viewing a product)
        if (!empty($shopContext['current_product'])) {
            $product = $shopContext['current_product'];

            $formatted[] = "### Konuşulan Ürün:";
            $formatted[] = "**Ürün Adı:** " . ($product['title'] ?? 'N/A');
            $formatted[] = "**Ürün ID:** " . ($product['id'] ?? 'N/A');
            $formatted[] = "**SKU:** " . ($product['sku'] ?? 'N/A');
            $formatted[] = "";
            $formatted[] = "**🚨 LİNK VERMEK İÇİN:** **{$product['title']}** [LINK:shop:{$product['slug']}]";
            $formatted[] = "";

            if (!empty($product['short_description'])) {
                $descStr = is_array($product['short_description']) ? json_encode($product['short_description'], JSON_UNESCAPED_UNICODE) : $product['short_description'];
                $formatted[] = "**Kısa Açıklama:** {$descStr}";
            }

            if (!empty($product['body'])) {
                $descStr = is_array($product['body']) ? json_encode($product['body'], JSON_UNESCAPED_UNICODE) : $product['body'];
                $formatted[] = "**Detaylı Açıklama:** {$descStr}";
            }

            // Price
            if (!empty($product['price']['formatted'])) {
                $formatted[] = "**Fiyat:** {$product['price']['formatted']}";
            } elseif (!empty($product['price']['on_request'])) {
                $formatted[] = "**Fiyat:** Fiyat sorunuz için lütfen iletişime geçin";
            }

            // Technical specs (İLK 5 ÖZELLIK - Token tasarrufu)
            if (!empty($product['technical_specs']) && is_array($product['technical_specs'])) {
                $formatted[] = "\n**Teknik Özellikler:**";
                $limitedSpecs = array_slice($product['technical_specs'], 0, 5, true);
                foreach ($limitedSpecs as $key => $value) {
                    $valueStr = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                    $formatted[] = "- {$key}: {$valueStr}";
                }
            }

            // Highlighted features ONLY (Features KALDIRILDI - çoğunlukla aynı)
            if (!empty($product['highlighted_features']) && is_array($product['highlighted_features'])) {
                $formatted[] = "\n**Öne Çıkan Özellikler:**";
                $limitedFeatures = array_slice($product['highlighted_features'], 0, 5);
                foreach ($limitedFeatures as $feature) {
                    $featureStr = is_array($feature) ? json_encode($feature, JSON_UNESCAPED_UNICODE) : $feature;
                    $formatted[] = "- {$featureStr}";
                }
            }

            // Use cases (İLK 3 - Token tasarrufu)
            if (!empty($product['use_cases']) && is_array($product['use_cases'])) {
                $formatted[] = "\n**Kullanım Alanları:**";
                $limitedUseCases = array_slice($product['use_cases'], 0, 3);
                foreach ($limitedUseCases as $useCase) {
                    $useCaseStr = is_array($useCase) ? json_encode($useCase, JSON_UNESCAPED_UNICODE) : $useCase;
                    $formatted[] = "- {$useCaseStr}";
                }
            }

            // Warranty & Certifications (ÖZET - Token tasarrufu)
            if (!empty($product['warranty_info'])) {
                $warrantyStr = is_array($product['warranty_info']) ? json_encode($product['warranty_info'], JSON_UNESCAPED_UNICODE) : $product['warranty_info'];
                $formatted[] = "\n**Garanti:** " . mb_substr($warrantyStr, 0, 100);
            }

            // FAQ KALDIRILDI - Çok fazla token kullanıyor, gerekliyse soru geldiğinde cevapla

            // Variants
            if (!empty($shopContext['current_product_variants'])) {
                $formatted[] = "\n**Varyantlar:**";
                foreach ($shopContext['current_product_variants'] as $variant) {
                    $formatted[] = "- {$variant['title']} (SKU: {$variant['sku']})";
                    if (!empty($variant['key_differences'])) {
                        $formatted[] = "  Fark: {$variant['key_differences']}";
                    }
                }
            }

            // Category
            if (!empty($shopContext['current_product_category'])) {
                $cat = $shopContext['current_product_category'];
                $formatted[] = "\n**Kategori:** {$cat['name']}";
            }

            $formatted[] = "\n---\n";
        }

        // Current Category context (if viewing a category)
        if (!empty($shopContext['current_category'])) {
            $category = $shopContext['current_category'];

            $formatted[] = "### Kategori:";
            $formatted[] = "**Kategori Adı:** {$category['name']}";

            if (!empty($category['description'])) {
                $formatted[] = "**Açıklama:** {$category['description']}";
            }

            $formatted[] = "**Toplam Ürün Sayısı:** {$category['product_count']}";

            if (!empty($shopContext['current_category_products'])) {
                $formatted[] = "\n**Kategorideki Ürünler:**";
                foreach (array_slice($shopContext['current_category_products'], 0, 10) as $product) {
                    $formatted[] = "- {$product['title']} (SKU: {$product['sku']})";
                }
            }

            $formatted[] = "\n---\n";
        }

        // ALWAYS include general shop context (categories + featured products)
        if (!empty($shopContext['categories']) || !empty($shopContext['featured_products'])) {
            $formatted[] = "### Diğer Mevcut Ürünler ve Kategoriler:";

            if (!empty($shopContext['total_products'])) {
                $formatted[] = "**Toplam Ürün Sayısı:** {$shopContext['total_products']}";
                $formatted[] = "";
            }

            if (!empty($shopContext['categories'])) {
                $formatted[] = "\n**Mevcut Kategoriler (KATEGORİ ARAMALARI İÇİN KULLAN!):**";
                $formatted[] = "**🔍 Kullanıcı kategori adı/tipi söylerse ÖNCE buradan ara!**";
                $formatted[] = "";
                foreach ($shopContext['categories'] as $cat) {
                    $catSlug = $cat['slug'] ?? null;
                    $catName = $cat['name'] ?? 'Unknown';
                    $productCount = $cat['product_count'] ?? 0;

                    $formatted[] = "- **{$catName}** ({$productCount} ürün) - SLUG: `{$catSlug}` [LINK:shop:category:{$catSlug}]";

                    // Include subcategories if available
                    if (!empty($cat['subcategories'])) {
                        foreach ($cat['subcategories'] as $subcat) {
                            $subcatSlug = $subcat['slug'] ?? null;
                            $subcatName = $subcat['name'] ?? 'Unknown';
                            $formatted[] = "  • **{$subcatName}** - SLUG: `{$subcatSlug}` [LINK:shop:category:{$subcatSlug}]";
                        }
                    }
                }
                $formatted[] = "";
                $formatted[] = "**⚡ AKILLI KATEGORİ ARAMA:**";
                $formatted[] = "- Kullanıcı 'reach truck', 'istif makinesi', 'forklift' gibi kelime kullanırsa →";
                $formatted[] = "- Yukarıdaki kategori listesinden eşleşen kategoriyi BUL";
                $formatted[] = "- O kategorideki ürünleri ÖNCE göster (kategori-bazlı filtreleme)";
                $formatted[] = "";
            }

            if (!empty($shopContext['featured_products'])) {
                $formatted[] = "\n**Öne Çıkan Ürünler:**";
                foreach (array_slice($shopContext['featured_products'], 0, 10) as $product) {
                    $sku = $product['sku'] ?? 'N/A';
                    $title = is_array($product['title']) ? json_encode($product['title'], JSON_UNESCAPED_UNICODE) : $product['title'];
                    $formatted[] = "- {$title} (SKU: {$sku})";
                }
            }

            // ALL ACTIVE PRODUCTS (MAKSIMUM 30 ÜRÜN - Token limit koruması)
            if (!empty($shopContext['all_products'])) {
                $formatted[] = "\n**Mevcut Ürünler (MUTLAKA LİNK VER!):**";
                $formatted[] = "**🚨 KRİTİK LINK FORMATI:**";
                $formatted[] = "- Ürün linki: **Ürün Adı** [LINK:shop:SLUG]";
                $formatted[] = "- Kategori linki: **Kategori Adı** [LINK:shop:category:SLUG]";
                $formatted[] = "- SLUG'ı aşağıdaki listeden AYNEN kopyala! (Örnek VERME!)";
                $formatted[] = "- ASLA örnek ürün adı/slug kullanma!";
                $formatted[] = "";

                // LIMIT: Maksimum 10 ürün göster (token tasarrufu + hız optimizasyonu)
                $limitedProducts = array_slice($shopContext['all_products'], 0, 10);

                foreach ($limitedProducts as $product) {
                    $title = is_array($product['title']) ? json_encode($product['title'], JSON_UNESCAPED_UNICODE) : $product['title'];
                    $sku = $product['sku'] ?? 'N/A';
                    $category = $product['category'] ?? 'Kategorisiz';
                    $slug = $product['slug'] ?? null;

                    // Price info
                    $priceInfo = '';
                    if (!empty($product['price']['formatted'])) {
                        $priceInfo = ", Fiyat: {$product['price']['formatted']}";
                    } elseif (!empty($product['price']['on_request'])) {
                        $priceInfo = ", Fiyat: Sorunuz";
                    }

                    // YENİ FORMAT: SLUG-based + Kategori bilgisi
                    // Format: • **Ürün Adı** (Kategori: xxx, SKU: xxx, Fiyat: xxx) [LINK:shop:SLUG]
                    $formatted[] = "• **{$title}** (📁 Kategori: {$category}, SKU: {$sku}{$priceInfo}) [LINK:shop:{$slug}]";
                }

                $formatted[] = "";
            }
        }

        return implode("\n", $formatted);
    }

    /**
     * Format page context for AI prompt
     */
    private function formatPageContext(array $pageContext): string
    {
        $formatted = [];

        // Current Page context (if viewing a specific page)
        if (!empty($pageContext['current_page'])) {
            $page = $pageContext['current_page'];

            $formatted[] = "### Görüntülenen Sayfa:";
            $formatted[] = "**Sayfa Başlığı:** {$page['title']}";

            if (!empty($page['content'])) {
                $formatted[] = "**İçerik:** {$page['content']}";
            }

            $formatted[] = "\n---\n";
        }

        // ALWAYS include important pages (About, Services, Contact)
        if (!empty($pageContext['about'])) {
            $formatted[] = "### Hakkımızda:";
            $formatted[] = "**{$pageContext['about']['title']}**";
            $formatted[] = $pageContext['about']['summary'];
            $formatted[] = "";
        }

        if (!empty($pageContext['services'])) {
            $formatted[] = "### Hizmetlerimiz:";
            $formatted[] = "**{$pageContext['services']['title']}**";
            $formatted[] = $pageContext['services']['summary'];
            $formatted[] = "";
        }

        if (!empty($pageContext['contact'])) {
            $formatted[] = "### İletişim:";
            $formatted[] = "**{$pageContext['contact']['title']}**";
            $formatted[] = $pageContext['contact']['summary'];
            $formatted[] = "";
        }

        // IMPORTANT PAGES ONLY (Token limit koruması)
        if (!empty($pageContext['all_pages'])) {
            $formatted[] = "### Önemli Sayfalar:";

            // LIMIT: Maksimum 5 sayfa (token tasarrufu)
            $limitedPages = array_slice($pageContext['all_pages'], 0, 5);

            foreach ($limitedPages as $page) {
                $title = $page['title'] ?? 'Başlıksız';
                $slug = $page['slug'] ?? '';

                // Summary KALDIRILDI - token tasarrufu
                $formatted[] = "- **{$title}** (/{$slug})";
            }
            $formatted[] = "";
        }

        return implode("\n", $formatted);
    }

    /**
     * 📜 Get conversation history
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConversationHistory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'session_id' => 'nullable|string|max:64',
                'conversation_id' => 'nullable|integer|exists:ai_conversations,id',
            ]);

            // Find conversation by session_id or conversation_id
            $conversation = null;

            if (!empty($validated['conversation_id'])) {
                $conversation = AIConversation::where('id', $validated['conversation_id'])
                    ->where('tenant_id', tenant('id'))
                    ->first();
            } elseif (!empty($validated['session_id'])) {
                $conversation = AIConversation::where('session_id', $validated['session_id'])
                    ->where('tenant_id', tenant('id'))
                    ->first();
            } else {
                // Generate session_id from IP
                $sessionId = $this->generateSessionId($request);
                $conversation = AIConversation::where('session_id', $sessionId)
                    ->where('tenant_id', tenant('id'))
                    ->first();
            }

            if (!$conversation) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'messages' => [],
                        'conversation_id' => null,
                    ],
                ]);
            }

            // Get messages
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    // ✅ NO PARSING NEEDED!
                    // Assistant messages are already saved as HTML in database (since V2 system)
                    // User messages are plain text
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'content' => $message->content,
                        'created_at' => $message->created_at->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation_id' => $conversation->id,
                    'session_id' => $conversation->session_id,
                    'messages' => $messages,
                    'message_count' => $messages->count(),
                    'created_at' => $conversation->created_at->toIso8601String(),
                    'last_message_at' => $conversation->last_message_at?->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.getConversationHistory failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Geçmiş yüklenemedi',
            ], 500);
        }
    }

    /**
     * 🔧 Fix broken URLs in AI response (Post-processing) - CONTEXT-AWARE V2
     *
     * AI sometimes generates wrong URLs by missing characters:
     * - Wrong: http://laravel.test/shopxtif-cpd15tvl... (missing "/" and "i")
     * - Correct: http://laravel.test/shop/ixtif-cpd15tvl...
     *
     * Solution: Match AI's broken URLs with correct URLs from context
     *
     * @param string $content AI response text
     * @param array $aiContext Full AI context with product URLs
     * @return string Fixed content
     */
    private function fixBrokenUrls(string $content, array $aiContext): string
    {
        \Log::info('🔧 fixBrokenUrls() CALLED', [
            'content_length' => strlen($content),
            'has_context' => !empty($aiContext['context']['modules']['shop']['all_products'])
        ]);

        // Step 1: Collect all correct URLs from context
        $correctUrls = [];

        // From all_products
        if (!empty($aiContext['context']['modules']['shop']['all_products'])) {
            foreach ($aiContext['context']['modules']['shop']['all_products'] as $product) {
                if (!empty($product['url'])) {
                    $correctUrls[] = $product['url'];
                }
            }
        }

        // From current_product
        if (!empty($aiContext['context']['modules']['shop']['current_product']['url'])) {
            $correctUrls[] = $aiContext['context']['modules']['shop']['current_product']['url'];
        }

        // From variants
        if (!empty($aiContext['context']['modules']['shop']['current_product_variants'])) {
            foreach ($aiContext['context']['modules']['shop']['current_product_variants'] as $variant) {
                if (!empty($variant['url'])) {
                    $correctUrls[] = $variant['url'];
                }
            }
        }

        // From featured_products
        if (!empty($aiContext['context']['modules']['shop']['featured_products'])) {
            foreach ($aiContext['context']['modules']['shop']['featured_products'] as $product) {
                if (!empty($product['url'])) {
                    $correctUrls[] = $product['url'];
                }
            }
        }

        // Step 2: Extract all markdown links from AI response
        preg_match_all('/\[(.*?)\]\((http[s]?:\/\/[^)]+)\)/i', $content, $matches, PREG_SET_ORDER);

        $replacements = [];
        $fixedCount = 0;

        foreach ($matches as $match) {
            $linkText = $match[1];
            $brokenUrl = $match[2];
            $originalLink = $match[0]; // Full markdown: [text](url)

            // Step 3: Find best matching correct URL
            $bestMatch = null;
            $bestSimilarity = 0;

            foreach ($correctUrls as $correctUrl) {
                // Calculate similarity percentage
                similar_text(strtolower($brokenUrl), strtolower($correctUrl), $similarity);

                if ($similarity > $bestSimilarity && $similarity >= 30) { // 30% threshold - very aggressive
                    $bestSimilarity = $similarity;
                    $bestMatch = $correctUrl;
                }
            }

            // Step 4: If found a good match, prepare replacement
            if ($bestMatch && $bestMatch !== $brokenUrl) {
                $fixedLink = "[{$linkText}]({$bestMatch})";
                $replacements[$originalLink] = $fixedLink;
                $fixedCount++;

                \Log::info('🔧 URL Fixed', [
                    'broken' => $brokenUrl,
                    'fixed' => $bestMatch,
                    'similarity' => round($bestSimilarity, 1) . '%',
                ]);
            }
        }

        // Step 5: Apply all replacements
        foreach ($replacements as $broken => $fixed) {
            $content = str_replace($broken, $fixed, $content);
        }

        \Log::info('🔧 Post-processing complete', [
            'total_links_found' => count($matches),
            'links_fixed' => $fixedCount,
            'correct_urls_available' => count($correctUrls),
        ]);

        return $content;
    }

    /**
     * 📞 Detect Phone Number & Log to Telescope
     *
     * Detects if a phone number was collected in the conversation
     * and logs the conversation summary + admin link to Telescope
     *
     * @param AIConversation $conversation
     * @return void
     */
    private function detectPhoneNumberAndLogToTelescope(AIConversation $conversation): void
    {
        try {
            // Initialize services
            $phoneService = new \Modules\AI\App\Services\PhoneNumberDetectionService();
            $summaryService = new \Modules\AI\App\Services\ConversationSummaryService();

            // Get all messages
            $messages = $conversation->messages;

            // Check if any message contains a phone number (ONLY in user messages, NOT assistant)
            $hasPhoneNumber = false;
            $detectedPhones = [];

            foreach ($messages as $message) {
                // 🚨 CRITICAL: Ignore phone numbers in AI's own responses (role='assistant')
                // AI sometimes shares company phone numbers (0534 515 2626, 0216 755 3 555)
                if ($message->role === 'assistant') {
                    continue; // Skip AI messages
                }

                // Only check USER messages for phone numbers
                if ($phoneService->hasPhoneNumber($message->content)) {
                    $hasPhoneNumber = true;
                    $phones = $phoneService->extractPhoneNumbers($message->content);
                    $detectedPhones = array_merge($detectedPhones, $phones);
                }
            }

            // If phone number detected, log to Telescope
            if ($hasPhoneNumber && !empty($detectedPhones)) {
                $detectedPhones = array_unique($detectedPhones);

                // Generate full summary
                $fullSummary = $summaryService->generateSummary($conversation);

                // Generate admin link
                $adminLink = $summaryService->generateAdminLink($conversation);

                // Generate compact summary for Telescope tags
                $compactSummary = $summaryService->generateCompactSummary($conversation);

                // Log to Telescope using Laravel's Log facade
                // Telescope will automatically capture this log entry
                Log::info('📞 AI CONVERSATION - PHONE NUMBER COLLECTED', [
                    'conversation_id' => $conversation->id,
                    'tenant_id' => $conversation->tenant_id,
                    'session_id' => $conversation->session_id,
                    'message_count' => $conversation->message_count,
                    'phone_numbers' => array_map(
                        fn($p) => $phoneService->formatPhoneNumber($p),
                        $detectedPhones
                    ),
                    'admin_link' => $adminLink,
                    'compact_summary' => $compactSummary,
                    'full_summary' => $fullSummary,
                    'detected_at' => now()->toIso8601String(),
                ]);

                \Log::info('✅ Phone number detected and logged to Telescope', [
                    'conversation_id' => $conversation->id,
                    'phones_count' => count($detectedPhones),
                ]);

                // 📱 TELEGRAM BİLDİRİMİ GÖNDER
                try {
                    $telegramService = new \Modules\AI\App\Services\TelegramNotificationService();
                    $telegramService->sendPhoneNumberAlert($conversation, $detectedPhones);
                } catch (\Exception $telegramError) {
                    // Silent fail - Telegram hatası ana akışı bozmasın
                    \Log::warning('⚠️ Telegram notification failed', [
                        'error' => $telegramError->getMessage(),
                    ]);
                }

                // 📱 WHATSAPP BİLDİRİMİ GÖNDER
                try {
                    $whatsappService = app(\App\Services\WhatsAppNotificationService::class);
                    $whatsappService->sendCustomerLead(
                        [
                            'name' => $detectedPhones[0]['name'] ?? 'Müşteri',
                            'phone' => $detectedPhones[0]['phone'] ?? '',
                        ],
                        $conversation->last_user_message ?? 'AI Chat talebi',
                        [],
                        [
                            'site' => tenant('domain'),
                            'page_url' => request()->headers->get('referer'),
                            'device' => request()->userAgent(),
                        ]
                    );
                } catch (\Exception $whatsappError) {
                    // Silent fail - WhatsApp hatası ana akışı bozmasın
                    \Log::warning('⚠️ WhatsApp notification failed', [
                        'error' => $whatsappError->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silent fail - don't break the main flow
            \Log::error('❌ detectPhoneNumberAndLogToTelescope failed', [
                'conversation_id' => $conversation->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 🎨 Get Product Placeholder Conversation
     *
     * Returns cached or AI-generated placeholder conversation for product chat widget
     *
     * @param string $productId
     * @return JsonResponse
     */
    /**
     * 🎨 Product Placeholder - FALLBACK ONLY
     *
     * Placeholder sistemi kaldırıldı, sadece varsayılan konuşma döner.
     */
    public function getProductPlaceholder(string $productId): JsonResponse
    {
        // Fallback conversation (simple welcome, matching floating widget)
        $conversation = [
            ['role' => 'assistant', 'text' => 'Merhaba! Size nasıl yardımcı olabilirim?'],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $conversation,
                'from_cache' => false,
                'generated_at' => null,
            ],
        ]);
    }

    /**
     * 🔗 Resolve Link - Convert [LINK:module:type:id] to URL
     *
     * Universal link resolver for AI-generated links
     * - Tenant-aware
     * - Multi-language support
     * - Works with all modules (shop, blog, page, portfolio)
     *
     * @param string $module
     * @param string $type
     * @param int $id
     * @return JsonResponse
     */
    public function resolveLink(string $module, string $type, int $id): JsonResponse
    {
        try {
            $resolver = app(\App\Services\AI\ModuleLinkResolverService::class);

            $result = $resolver->resolve($module, $type, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'error' => 'Link could not be resolved',
                    'module' => $module,
                    'type' => $type,
                    'id' => $id,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.resolveLink failed', [
                'module' => $module,
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Link resolution failed',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Conversation
     *
     * ADMIN/TESTING endpoint - Deletes conversation + all messages from database
     * WARNING: No authentication for now - add auth middleware in production!
     *
     * @param int $conversationId
     * @return JsonResponse
     */
    public function deleteConversation(int $conversationId): JsonResponse
    {
        try {
            // Find conversation
            $conversation = AIConversation::where('id', $conversationId)
                ->where('tenant_id', tenant('id')) // Tenant-scoped
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Conversation not found',
                ], 404);
            }

            // Delete all messages first (cascade should handle this, but just in case)
            $messagesDeleted = $conversation->messages()->delete();

            // Delete conversation
            $conversation->delete();

            Log::info('🗑️ Conversation deleted', [
                'conversation_id' => $conversationId,
                'tenant_id' => tenant('id'),
                'messages_deleted' => $messagesDeleted,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation deleted successfully',
                'data' => [
                    'conversation_id' => $conversationId,
                    'messages_deleted' => $messagesDeleted,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.deleteConversation failed', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to delete conversation',
            ], 500);
        }
    }

    /**
     * 🔧 Fix WhatsApp Links - AI bazen ürün linki koyuyor, düzeltelim
     *
     * AI yanıtında WhatsApp linkini ürün linki ile karıştırıyorsa, doğru wa.me linkini oluştur
     *
     * @param string $message AI yanıtı
     * @return string Düzeltilmiş mesaj
     */
    private function fixWhatsAppLinks(string $message): string
    {
        // WhatsApp numarası settings'ten al
        $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();
        $whatsapp = $contactInfo['whatsapp'] ?? '0534 515 2626';

        // Clean WhatsApp number (0534 -> 905345152626)
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        if (substr($cleanWhatsapp, 0, 1) === '0') {
            $cleanWhatsapp = '90' . substr($cleanWhatsapp, 1);
        }
        $correctWhatsAppLink = "https://wa.me/{$cleanWhatsapp}";

        // Pattern: [WHATSAPP_NUMBER](WRONG_LINK)
        // Örnek: [0501 005 67 58](https://ixtif.com/shop/...)
        // Düzelt: [0501 005 67 58](https://wa.me/905010056758)
        $pattern = '/\[([0-9\s]+)\]\(https?:\/\/[^\)]+\/shop\/[^\)]+\)/i';
        $replacement = "[$1]({$correctWhatsAppLink})";

        $fixed = preg_replace($pattern, $replacement, $message);

        // Eğer WhatsApp: kelimesi varsa ve hala yanlış link varsa düzelt
        $pattern2 = '/(WhatsApp:\s*)\[([0-9\s]+)\]\(https?:\/\/(?!wa\.me)[^\)]+\)/i';
        $replacement2 = "$1[$2]({$correctWhatsAppLink})";

        $fixed = preg_replace($pattern2, $replacement2, $fixed);

        return $fixed;
    }

    /**
     * 📝 Format List Items - Convert inline list to proper markdown list
     *
     * AI bazen liste itemlarını tek satırda yazar:
     * "Bilgi için: - Kapasite? - Manuel mi? - Nerede kullanacaksınız?"
     *
     * Bunu şöyle çevir:
     * "Bilgi için:\n- Kapasite?\n- Manuel mi?\n- Nerede kullanacaksınız?"
     *
     * Böylece MarkdownService düzgün <ul><li> oluşturur.
     *
     * @param string $content AI response content
     * @return string Formatted content with proper line breaks for lists
     */
    private function formatListItems(string $content): string
    {
        // Pattern: "text: - item" → "text:\n- item"
        // Closing paren da ekle: "item?) - next" → "item?)\n- next"
        $content = preg_replace('/([:\?\!\.\)])(\s+)-\s+/', "$1\n- ", $content);

        return $content;
    }

    /**
     * 🌊 Shop Assistant Chat - STREAMING VERSION
     *
     * Server-Sent Events (SSE) ile streaming response
     * ChatGPT benzeri typing effect
     *
     * @param Request $request
     * @return Response (SSE stream)
     */
    public function shopAssistantChatStream(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
            'product_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'page_slug' => 'nullable|string|max:255',
        ]);

        // ⚡ HAZIRLIK - Stream öncesi tüm işlemler
        $sessionId = $validated['session_id'] ?? 'guest_' . uniqid();

        $conversation = \Modules\AI\App\Models\AIConversation::firstOrCreate([
            'session_id' => $sessionId,
        ], [
            'tenant_id' => tenant('id'),
            'user_id' => auth()->id(),
            'context_type' => 'shop_assistant',
            'context_id' => $validated['product_id'] ?? null,
        ]);

        // Save user message
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        // Build context
        $contextOptions = [
            'product_id' => $validated['product_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'page_slug' => $validated['page_slug'] ?? null,
        ];

        $aiContext = app(\App\Services\AI\Context\ModuleContextOrchestrator::class)->buildAIContext(
            $validated['message'],
            $contextOptions
        );

        // Conversation history
        $conversationHistory = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
            })
            ->toArray();

        // Build prompt
        $optimizedPromptService = new \Modules\AI\App\Services\OptimizedPromptService();
        $enhancedSystemPrompt = $optimizedPromptService->getFullPrompt($aiContext, $conversationHistory);

        // Prepare messages
        $messages = [
            ['role' => 'system', 'content' => $enhancedSystemPrompt]
        ];

        foreach ($conversationHistory as $historyMsg) {
            $messages[] = $historyMsg;
        }

        // Get OpenAI service
        $provider = \Modules\AI\App\Models\AIProvider::where('name', 'openai')
            ->where('is_active', true)
            ->first();

        $service = new \Modules\AI\App\Services\OpenAIService([
            'provider_id' => $provider->id,
            'api_key' => $provider->api_key,
            'base_url' => $provider->base_url,
            'model' => 'gpt-4o-mini',
        ]);

        // ⚡ STREAMING BAŞLAT
        return response()->stream(function () use ($service, $messages, $conversation, $sessionId) {
            set_time_limit(0);
            ignore_user_abort(false);

            $fullResponse = '';

            // Callback
            $streamCallback = function($chunk) use (&$fullResponse) {
                $fullResponse .= $chunk;
                echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            };

            // Start event
            echo "data: " . json_encode(['event' => 'start']) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            try {
                $service->generateCompletionStream($messages, $streamCallback, [
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ]);

                // Save response
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'model' => 'gpt-4o-mini',
                ]);

                // End event
                echo "data: " . json_encode([
                    'event' => 'end',
                    'session_id' => $sessionId,
                    'conversation_id' => $conversation->id,
                ]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();

            } catch (\Exception $e) {
                echo "data: " . json_encode([
                    'event' => 'error',
                    'error' => $e->getMessage()
                ]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * 🔄 NEW WORKFLOW SYSTEM - Shop Assistant Chat V2
     * Routes to ConversationFlowEngine instead of legacy system
     */
    protected function shopAssistantChatV2(Request $request): JsonResponse
    {
        \Log::info('🚀 shopAssistantChatV2 STARTED (NEW WORKFLOW SYSTEM)', [
            'message' => $request->input('message'),
            'session_id' => $request->input('session_id'),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            // Validate input (same as old system)
            $validated = $request->validate([
                'message' => 'required|string|min:1|max:1000',
                'product_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'page_slug' => 'nullable|string|max:255',
                'session_id' => 'nullable|string|max:64',
            ]);

            // Generate or use existing session_id
            $sessionId = $validated['session_id'] ?? $this->generateSessionId($request);

            // 🔥 USE FLOW EXECUTOR FOR E-COMMERCE CHAT
            $flow = \Modules\AI\App\Models\Flow::getActiveFlow();

            if (!$flow) {
                \Log::error('❌ No active flow found');
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow not configured',
                ], 500);
            }

            \Log::info('🚀 Using FlowExecutor', [
                'flow_id' => $flow->id,
                'flow_name' => $flow->name
            ]);

            $flowExecutor = app(\Modules\AI\App\Services\Workflow\FlowExecutor::class);

            // Load conversation history from database
            $conversationHistory = [];

            // Conversation loading verified

            $conversation = \Modules\AI\App\Models\AIConversation::where('session_id', $sessionId)
                ->where('tenant_id', tenant('id'))
                ->first();

            \Log::emergency('🔍 Conversation lookup', [
                'session_id' => $sessionId,
                'tenant_id' => tenant('id'),
                'conversation_found' => $conversation ? 'YES' : 'NO',
                'conversation_id' => $conversation ? $conversation->id : null
            ]);

            if ($conversation) {
                // Get last 10 messages for context (most recent first, then reverse)
                $messages = $conversation->messages()
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->reverse()
                    ->values(); // Reset array keys

                // Messages loaded successfully

                \Log::emergency('🔍 Messages query result', [
                    'messages_count' => $messages->count(),
                    'conversation_id' => $conversation->id
                ]);

                foreach ($messages as $msg) {
                    $conversationHistory[] = [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                }

                // Conversation history prepared

                \Log::emergency('📜 Loaded conversation history', [
                    'session_id' => $sessionId,
                    'message_count' => count($conversationHistory),
                    'history_preview' => array_slice($conversationHistory, 0, 2)
                ]);
            } else {
                \Log::emergency('⚠️ NO CONVERSATION FOUND - will create new');
            }

            // 🔍 WORKFLOW V2: ProductSearchNode handles product search internally
            // No need to pre-search products here, ProductSearchNode will do it
            \Log::info('🚀 Using Workflow V2 - ProductSearchNode will handle search', [
                'tenant_id' => tenant('id')
            ]);

            // Execute flow - ProductSearchNode will search products using Meilisearch
            $result = $flowExecutor->execute($flow->flow_data, [
                'user_message' => $validated['message'],
                'session_id' => $sessionId,
                'tenant_id' => tenant('id'),
                'conversation_history' => $conversationHistory
            ]);

            $aiResponse = $result['final_response'] ?? '';

            if (empty($aiResponse)) {
                \Log::error('❌ Empty AI response from FlowExecutor');
                return response()->json([
                    'success' => false,
                    'message' => 'AI yanıt üretemedi',
                ], 500);
            }

            // 📝 MARKDOWN - Frontend'e RAW olarak gönder (Frontend render edecek)
            // ❌ Backend'de HTML'e çevirme - Double-escape sorunu yaratıyor!
            // ✅ Frontend: window.aiChatRenderMarkdown() ile render edilecek

            \Log::info('🔍 Markdown response ready (V2)', [
                'is_markdown' => (preg_match('/\*\*|\#\#|\-\s/', $aiResponse) ? 'YES' : 'NO'),
                'preview' => mb_substr($aiResponse, 0, 200),
                'length' => mb_strlen($aiResponse)
            ]);

            // ✅ BOŞ YANIT KONTROLÜ
            if (empty(trim($aiResponse))) {
                \Log::error('❌ Empty AI response', [
                    'original_length' => strlen($result['final_response'] ?? ''),
                    'response_length' => strlen($aiResponse)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'AI yanıt üretemedi',
                ], 500);
            }

            \Log::info('✅ FlowExecutor completed', [
                'response_length' => strlen($aiResponse)
            ]);

            // Save conversation to database
            if (!$conversation) {
                $conversation = \Modules\AI\App\Models\AIConversation::create([
                    'session_id' => $sessionId,
                    'tenant_id' => tenant('id'),
                    'user_id' => null, // Guest user
                    'feature_slug' => 'shop-assistant',
                    'status' => 'active',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // Save user message
            $conversation->messages()->create([
                'role' => 'user',
                'content' => $validated['message'],
            ]);

            // Save AI response
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResponse,
            ]);

            \Log::info('💾 Conversation saved', [
                'conversation_id' => $conversation->id,
                'session_id' => $sessionId
            ]);

            // Map flow result to API response format
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $aiResponse,
                    'session_id' => $sessionId,
                    'conversation_id' => $conversation->id,
                    'metadata' => [
                        'system' => 'workflow_engine_v2',
                        'flow_name' => $flow->name,
                        'nodes_executed' => array_keys($result),
                    ],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('❌ shopAssistantChatV2 EXCEPTION', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
