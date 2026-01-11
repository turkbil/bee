<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\V3\UniversalInputManager;
use Modules\AI\App\Services\V3\ContextAwareEngine;

/**
 * Universal Input System Controller V3
 * 
 * Enterprise-level form structure management with:
 * - Dynamic form generation based on context
 * - Smart defaults and user preferences
 * - Real-time validation and suggestions
 * - Multi-language support
 * - Context-aware field adaptation
 */
class UniversalInputController extends Controller
{
    public function __construct(
        private readonly UniversalInputManager $universalInputManager,
        private readonly ContextAwareEngine $contextEngine
    ) {}

    /**
     * Universal Input System Ana Sayfa
     */
    public function index()
    {
        return view('ai::admin.universal.index');
    }

    /**
     * Input Management Sayfası
     */
    public function inputManagement()
    {
        return view('ai::admin.universal.input-management');
    }

    /**
     * Get dynamic form structure for feature
     */
    public function getFormStructure(Request $request, int $featureId): JsonResponse
    {
        try {
            $context = $this->extractContext($request);
            
            $formStructure = $this->universalInputManager->getFormStructure(
                $featureId, 
                $context
            );
            
            return response()->json([
                'success' => true,
                'data' => $formStructure,
                'context' => $context,
                'cache_ttl' => 3600,
                'generated_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Form structure generation failed', [
                'feature_id' => $featureId,
                'context' => $request->get('context', []),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Form structure could not be generated',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Submit universal form and process with AI
     */
    public function submitForm(Request $request, int $featureId): JsonResponse
    {
        try {
            // Validate basic request structure
            $validator = Validator::make($request->all(), [
                'inputs' => 'required|array',
                'context' => 'sometimes|array',
                'options' => 'sometimes|array',
                'save_preferences' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $inputs = $request->get('inputs', []);
            $context = $this->extractContext($request);
            $options = $request->get('options', []);
            
            // Validate inputs against feature requirements
            $validation = $this->universalInputManager->validateInputs($inputs, $featureId);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors'],
                    'suggestions' => $validation['suggestions'] ?? [],
                    'message' => 'Input validation failed'
                ], 422);
            }

            // Build dynamic inputs with context awareness
            $dynamicInputs = $this->universalInputManager->buildDynamicInputs(
                $featureId, 
                $context['module_type'] ?? ''
            );

            // Apply context rules to enhance inputs
            $enhancedInputs = $this->universalInputManager->applyContextRules(
                $inputs, 
                $context
            );

            // Map inputs to prompts for AI processing
            $promptMapping = $this->universalInputManager->mapInputsToPrompts(
                $enhancedInputs, 
                $featureId
            );

            // Save user preferences if requested
            if ($request->get('save_preferences', false) && auth()->check()) {
                $this->universalInputManager->saveUserPreferences(
                    auth()->id(),
                    $featureId,
                    $enhancedInputs
                );
            }

            // Return successful submission data
            return response()->json([
                'success' => true,
                'data' => [
                    'processed_inputs' => $enhancedInputs,
                    'prompt_mapping' => $promptMapping,
                    'dynamic_fields' => $dynamicInputs,
                    'context_applied' => $context
                ],
                'metadata' => [
                    'feature_id' => $featureId,
                    'processing_time' => microtime(true),
                    'preferences_saved' => $request->get('save_preferences', false)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Form submission failed', [
                'feature_id' => $featureId,
                'inputs' => $request->get('inputs', []),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Form submission failed',
                'message' => config('app.debug') ? $e->getMessage() : 'Processing error occurred'
            ], 500);
        }
    }

    /**
     * Get smart defaults for user and feature
     */
    public function getSmartDefaults(Request $request, int $featureId): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No user logged in, returning empty defaults'
                ]);
            }

            $defaults = $this->universalInputManager->getSmartDefaults($userId, $featureId);
            $context = $this->extractContext($request);

            // Enhance defaults with context awareness
            $contextualDefaults = $this->contextEngine->applyRules($context);
            
            // Merge smart defaults with contextual suggestions
            $enhancedDefaults = array_merge($defaults, $contextualDefaults['suggestions'] ?? []);

            return response()->json([
                'success' => true,
                'data' => [
                    'defaults' => $enhancedDefaults,
                    'user_history' => $defaults['history'] ?? [],
                    'popular_choices' => $defaults['popular'] ?? [],
                    'context_suggestions' => $contextualDefaults['suggestions'] ?? []
                ],
                'metadata' => [
                    'user_id' => $userId,
                    'feature_id' => $featureId,
                    'context_score' => $contextualDefaults['confidence'] ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Smart defaults generation failed', [
                'feature_id' => $featureId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Could not load smart defaults',
                'message' => 'Using fallback defaults'
            ], 500);
        }
    }

    /**
     * Save user preferences
     */
    public function savePreferences(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_id' => 'required|integer|exists:ai_features,id',
                'preferences' => 'required|array',
                'preferences.inputs' => 'sometimes|array',
                'preferences.options' => 'sometimes|array',
                'preferences.templates' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
            }

            $featureId = $request->get('feature_id');
            $preferences = $request->get('preferences');

            $this->universalInputManager->saveUserPreferences(
                $userId,
                $featureId,
                $preferences
            );

            return response()->json([
                'success' => true,
                'message' => 'Preferences saved successfully',
                'data' => [
                    'saved_at' => now()->toISOString(),
                    'preference_count' => count($preferences)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Preference saving failed', [
                'user_id' => auth()->id(),
                'preferences' => $request->get('preferences', []),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Could not save preferences'
            ], 500);
        }
    }

    /**
     * Validate inputs without processing
     */
    public function validateInputs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_id' => 'required|integer|exists:ai_features,id',
                'inputs' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $featureId = $request->get('feature_id');
            $inputs = $request->get('inputs');

            $validation = $this->universalInputManager->validateInputs($inputs, $featureId);

            return response()->json([
                'success' => true,
                'validation' => $validation,
                'data' => [
                    'valid' => $validation['valid'],
                    'errors' => $validation['errors'] ?? [],
                    'warnings' => $validation['warnings'] ?? [],
                    'suggestions' => $validation['suggestions'] ?? [],
                    'completeness_score' => $validation['score'] ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Input validation failed', [
                'feature_id' => $request->get('feature_id'),
                'inputs' => $request->get('inputs', []),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validation could not be performed',
                'message' => config('app.debug') ? $e->getMessage() : 'Validation error'
            ], 500);
        }
    }

    /**
     * Get field suggestions based on context
     */
    public function getFieldSuggestions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_id' => 'required|integer|exists:ai_features,id',
                'field_name' => 'required|string',
                'partial_input' => 'sometimes|string',
                'context' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $featureId = $request->get('feature_id');
            $fieldName = $request->get('field_name');
            $partialInput = $request->get('partial_input', '');
            $context = $this->extractContext($request);

            // Get context-aware suggestions
            $contextSuggestions = $this->contextEngine->getRecommendations([
                'feature_id' => $featureId,
                'field_name' => $fieldName,
                'partial_input' => $partialInput,
                ...$context
            ]);

            // Get smart defaults for this field
            $smartDefaults = [];
            if (auth()->check()) {
                $userDefaults = $this->universalInputManager->getSmartDefaults(
                    auth()->id(), 
                    $featureId
                );
                $smartDefaults = $userDefaults[$fieldName] ?? [];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $contextSuggestions['suggestions'] ?? [],
                    'smart_defaults' => $smartDefaults,
                    'popular_choices' => $contextSuggestions['popular'] ?? [],
                    'context_hints' => $contextSuggestions['hints'] ?? []
                ],
                'metadata' => [
                    'field_name' => $fieldName,
                    'suggestion_count' => count($contextSuggestions['suggestions'] ?? []),
                    'confidence' => $contextSuggestions['confidence'] ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Field suggestions failed', [
                'feature_id' => $request->get('feature_id'),
                'field_name' => $request->get('field_name'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Could not generate suggestions',
                'data' => ['suggestions' => [], 'smart_defaults' => []]
            ], 500);
        }
    }

    /**
     * Extract context from request
     */
    private function extractContext(Request $request): array
    {
        $context = $request->get('context', []);
        
        // Add system context
        $context['timestamp'] = now()->timestamp;
        $context['user_id'] = auth()->id();
        $context['ip_address'] = $request->ip();
        $context['user_agent'] = $request->userAgent();
        
        // Add detected context from ContextAwareEngine
        try {
            $detectedContext = $this->contextEngine->detectContext($request->all());
            $context = array_merge($context, $detectedContext);
        } catch (\Exception $e) {
            Log::warning('Context detection failed', ['error' => $e->getMessage()]);
        }
        
        return $context;
    }
}