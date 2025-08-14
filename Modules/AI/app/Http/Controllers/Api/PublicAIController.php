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

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
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
}