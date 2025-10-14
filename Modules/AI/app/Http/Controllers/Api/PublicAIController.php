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

            // Call AI service
            $response = $this->aiService->processRequest([
                'input' => $validated['message'],
                'feature' => $feature,
                'context' => $validated['context'] ?? [],
                'options' => $promptOptions,
                'anti_monotony_prompt' => $antiMonotonyPrompt,
                'user_id' => null, // Guest user
            ]);

            // Log public usage for analytics
            $this->logPublicUsage($request, $feature, $response);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $response['content'] ?? 'AI response generated',
                    'feature_used' => $feature?->slug,
                    'remaining_requests' => RateLimiter::remainingAttempts($rateLimitKey, 10),
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

            // Build context options for orchestrator
            $contextOptions = [
                'product_id' => $validated['product_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'page_slug' => $validated['page_slug'] ?? null,
                'session_id' => $sessionId,
            ];

            // Use ModuleContextOrchestrator to build full context
            $aiContext = $this->contextOrchestrator->buildUserContext(
                $validated['message'],
                $contextOptions
            );

            // Build enhanced system prompt with product context
            $enhancedSystemPrompt = $this->buildEnhancedSystemPrompt($aiContext);

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

            // 🧠 CONVERSATION MEMORY: Get last 20 messages for context (kullanıcı isteği)
            $conversationHistory = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Call AI service with enhanced system prompt + conversation history
            // 🔄 AUTOMATIC FALLBACK CHAIN: GPT-5 → GPT-4o-mini → Claude-Haiku → DeepSeek
            $aiResponseText = null;
            $usedModel = 'gpt-5';

            try {
                $aiResponseText = $this->aiService->ask($validated['message'], [
                    'temperature' => 0.7,
                    'custom_prompt' => $enhancedSystemPrompt,
                    'conversation_history' => $conversationHistory, // 🧠 Last 20 messages
                ]);
            } catch (\Exception $aiError) {
                // 🔄 FALLBACK LAYER 1: GPT-5 → GPT-4o-mini
                if (str_contains($aiError->getMessage(), '429') || str_contains($aiError->getMessage(), 'Rate limit') || str_contains($aiError->getMessage(), 'rate_limit')) {
                    Log::warning('🔴 GPT-5 rate limit hit, falling back to GPT-4o-mini', [
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
                                    'gpt5_error' => $aiError->getMessage(),
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

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $aiResponse['content'] ?? '',
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
     * Get tenant-specific rules from config
     */
    private function getTenantRules(int $tenantId): array
    {
        $tenantRules = config('ai-tenant-rules', []);

        // Find tenant config by ID
        foreach ($tenantRules as $key => $rules) {
            if (isset($rules['tenant_id']) && $rules['tenant_id'] === $tenantId) {
                return $rules;
            }
        }

        // Return default rules if tenant not found
        return $tenantRules['default'] ?? [
            'category_priority' => ['enabled' => false],
            'faq_enabled' => false,
            'token_limits' => ['products_max' => 30],
        ];
    }

    /**
     * 🎨 Build enhanced system prompt with product context
     *
     * Combines base system prompt with module-specific context (Product, Category, Page)
     */
    private function buildEnhancedSystemPrompt(array $aiContext): string
    {
        $prompts = [];

        // 🌐 Get dynamic domain (mevcut tenant'ın domain'i)
        $siteUrl = request()->getSchemeAndHttpHost();

        // Base system prompt (personality, contact, knowledge base)
        $prompts[] = $aiContext['system_prompt'];

        // 🔥 TENANT-SPECIFIC CUSTOM PROMPTS (iXtif gibi özel kurallar)
        $tenantId = tenant('id');

        // FIX: If no tenant context, skip tenant rules (central domain için güvenlik)
        if ($tenantId) {
            $tenantRules = $this->getTenantRules($tenantId);

            if (!empty($tenantRules['custom_prompts'])) {
                foreach ($tenantRules['custom_prompts'] as $promptKey => $promptContent) {
                    $prompts[] = "\n" . trim($promptContent);
                }
            }
        }

        // 🎯 SATIŞ ODAKLI YAKLAŞIM
        $prompts[] = "\n## 🎯 SEN KİMSİN ve NE YAPARSIN";
        $prompts[] = "Sen profesyonel bir shop asistanısın. Görevin müşterilere en uygun ürünleri önermek ve satış yapmak.";
        $prompts[] = "- Şirket ürünleri ve hizmetleri hakkında konuş";
        $prompts[] = "- Konu dışı sorularda kibarca reddet ve ürünlere yönlendir";
        $prompts[] = "";
        $prompts[] = "## 🔑 DİNAMİK ÜRÜN ÖNERİ SİSTEMİ";
        $prompts[] = "Kullanıcı HERHANGI BİR ürün kategorisi/tipi/marka/özellik söylediğinde, aşağıdaki BAĞLAM BİLGİLERİ bölümünde ilgili ürünleri ara ve öner!";
        $prompts[] = "";
        $prompts[] = "**ÇALIŞMA MANTIĞI:**";
        $prompts[] = "1. Kullanıcı mesajını analiz et: Hangi ürün/kategori/özellik arıyor?";
        $prompts[] = "2. BAĞLAM BİLGİLERİ → Mevcut Ürünler bölümünde ARAMA YAP:";
        $prompts[] = "   - Ürün adında kelime geçiyor mu?";
        $prompts[] = "   - Kategori adında kelime geçiyor mu?";
        $prompts[] = "   - Kısa açıklamada kelime geçiyor mu?";
        $prompts[] = "3. Bulunan TÜM ürünleri MUTLAKA Markdown link ile listele: [Ürün Adı](url)";
        $prompts[] = "   ⚠️ ÖNEMLİ: Her ürün linkli olmalı! Link olmayan ürün ASLA ekleme!";
        $prompts[] = "4. Her ürünün yanına özelliklerinden 1-2 tanesini yaz";
        $prompts[] = "5. Kullanıcıya ihtiyaç netleştirme sorusu sor";
        $prompts[] = "";
        $prompts[] = "**GENEL ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: '[HERHANGI BIR ÜRÜN KATEGORISI] arıyorum'";
        $prompts[] = "Sen: 'Harika! [KATEGORI] modellerimiz:";
        $prompts[] = "- [Ürün 1 Adı](url1) - Özellik A, Özellik B";
        $prompts[] = "- [Ürün 2 Adı](url2) - Özellik C, Özellik D";
        $prompts[] = "- [Ürün 3 Adı](url3) - Özellik E, Özellik F";
        $prompts[] = "";
        $prompts[] = "Hangi özellikler sizin için önemli?'";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**DİKKAT:** Yukarıdaki sadece FORMAT örneği! Gerçek ürün adları ve URL'leri BAĞLAM BİLGİLERİ bölümünden al!";
        $prompts[] = "";
        $prompts[] = "## 📋 URL KURALLARI";
        $prompts[] = "- ASLA kendi URL üretme!";
        $prompts[] = "- SADECE BAĞLAM BİLGİLERİ bölümündeki URL'leri kullan";
        $prompts[] = "- Format: [Ürün Adı](context_url)";
        $prompts[] = "";
        $prompts[] = "## 💬 İLETİŞİM YÖNLENDİRME";
        $prompts[] = "Kullanıcı detaylı bilgi istediğinde WhatsApp/Telefon iletişim bilgilerini paylaş (sistem ayarlarında mevcut).";
        $prompts[] = "";

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

            // Ürün linki - Markdown formatında
            if (!empty($product['url'])) {
                $formatted[] = "**Ürün Linki:** [Ürüne Git](" . $product['url'] . ")";
                $formatted[] = "**ÖNEMLİ:** Kullanıcıya ürün linkini Markdown formatında ver: [Ürüne Git](" . $product['url'] . ")";
            }

            $formatted[] = "**SKU:** " . ($product['sku'] ?? 'N/A');

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
                $formatted[] = "**Tüm Ürünlerimizi Görmek İçin:** [Tüm Ürünler](" . url('/shop') . ")";
                $formatted[] = "**ÖNEMLİ:** Kullanıcı 'tüm ürünler', 'ne ürünleriniz var', 'katalog' gibi sorular sorduğunda bu linki paylaş!";
                $formatted[] = "";
            }

            if (!empty($shopContext['categories'])) {
                $formatted[] = "\n**Kategoriler:**";
                foreach ($shopContext['categories'] as $cat) {
                    $formatted[] = "- {$cat['name']} ({$cat['product_count']} ürün)";

                    // Include subcategories if available
                    if (!empty($cat['subcategories'])) {
                        foreach ($cat['subcategories'] as $subcat) {
                            $formatted[] = "  • {$subcat['name']}";
                        }
                    }
                }
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
                $formatted[] = "**🚨 KRİTİK: Aşağıdaki ürünler için SADECE parantez içindeki URL'leri kullan! Kendi URL üretme!**";
                $formatted[] = "";

                // LIMIT: Maksimum 30 ürün göster (token tasarrufu + tüm transpaletleri kapsa)
                $limitedProducts = array_slice($shopContext['all_products'], 0, 30);

                foreach ($limitedProducts as $product) {
                    $title = is_array($product['title']) ? json_encode($product['title'], JSON_UNESCAPED_UNICODE) : $product['title'];
                    $sku = $product['sku'] ?? 'N/A';
                    $category = $product['category'] ?? 'Kategorisiz';
                    $url = $product['url'] ?? '#';

                    // Price info
                    $priceInfo = '';
                    if (!empty($product['price']['formatted'])) {
                        $priceInfo = " - {$product['price']['formatted']}";
                    } elseif (!empty($product['price']['on_request'])) {
                        $priceInfo = " - (Fiyat sorunuz)";
                    }

                    // FIX: URL'yi daha net göster - AI için açık format
                    $formatted[] = "- **{$title}** → URL: `{$url}` | SKU: {$sku} | {$category}{$priceInfo}";
                    $formatted[] = "  → Markdown format: [{$title}]({$url})";
                }

                $formatted[] = "";
                $formatted[] = "**NOT:** Daha fazla ürün için [Tüm Ürünler](" . url('/shop') . ") sayfasını öner.";
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

                if ($similarity > $bestSimilarity && $similarity >= 70) { // 70% threshold
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
     * 🎨 Get Product Placeholder Conversation
     *
     * Returns cached or AI-generated placeholder conversation for product chat widget
     *
     * @param string $productId
     * @return JsonResponse
     */
    public function getProductPlaceholder(string $productId): JsonResponse
    {
        try {
            // Get placeholder service
            $placeholderService = app(\App\Services\AI\ProductPlaceholderService::class);

            // Get or generate placeholder
            $result = $placeholderService->getPlaceholder($productId);

            return response()->json([
                'success' => $result['success'],
                'data' => [
                    'conversation' => $result['conversation'],
                    'from_cache' => $result['from_cache'] ?? false,
                    'generated_at' => $result['generated_at'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PublicAIController.getProductPlaceholder failed', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Placeholder yüklenemedi',
            ], 500);
        }
    }
}
