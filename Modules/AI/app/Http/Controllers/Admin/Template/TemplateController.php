<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Template;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\V3\TemplateGenerator;
use Modules\AI\App\Services\V3\ContextAwareEngine;
use Modules\AI\App\Services\V3\SmartAnalyzer;

/**
 * Template Controller V3
 * 
 * Enterprise-level template management with:
 * - Dynamic template generation and inheritance
 * - Multi-language template variants
 * - Real-time template optimization
 * - Context-aware template selection
 * - Template analytics and performance metrics
 */
class TemplateController extends Controller
{
    public function __construct(
        private readonly TemplateGenerator $templateGenerator,
        private readonly ContextAwareEngine $contextEngine,
        private readonly SmartAnalyzer $analyzer
    ) {}

    /**
     * Get available templates
     */
    public function getTemplates(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_id' => 'sometimes|integer|exists:ai_features,id',
                'category' => 'sometimes|string|max:100',
                'language' => 'sometimes|string|size:2',
                'tags' => 'sometimes|array',
                'limit' => 'sometimes|integer|min:1|max:100',
                'search' => 'sometimes|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $filters = [
                'feature_id' => $request->get('feature_id'),
                'category' => $request->get('category'),
                'language' => $request->get('language', 'tr'),
                'tags' => $request->get('tags', []),
                'search' => $request->get('search'),
                'user_id' => auth()->id()
            ];

            $templates = $this->templateGenerator->getAvailableTemplates($filters);
            $categories = $this->templateGenerator->getTemplateCategories();
            $popularTags = $this->templateGenerator->getPopularTags();

            return response()->json([
                'success' => true,
                'data' => [
                    'templates' => $templates,
                    'categories' => $categories,
                    'popular_tags' => $popularTags,
                    'total_count' => count($templates)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Templates retrieval failed', [
                'filters' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve templates'
            ], 500);
        }
    }

    /**
     * Generate content from template
     */
    public function generateFromTemplate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'template_id' => 'required|string',
                'variables' => 'required|array',
                'feature_id' => 'sometimes|integer|exists:ai_features,id',
                'language' => 'sometimes|string|size:2',
                'options' => 'sometimes|array',
                'options.tone' => 'sometimes|string|in:professional,casual,friendly,formal,creative',
                'options.length' => 'sometimes|string|in:short,medium,long,detailed',
                'options.format' => 'sometimes|string|in:html,markdown,text,json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $templateId = $request->get('template_id');
            $variables = $request->get('variables');
            $options = $request->get('options', []);

            // Get user context for personalized generation
            $context = $this->contextEngine->detectContext([
                'user_id' => auth()->id(),
                'feature_id' => $request->get('feature_id'),
                'language' => $request->get('language', 'tr'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Generate content using template
            $result = $this->templateGenerator->generateFromTemplate(
                $templateId,
                $variables,
                array_merge($options, ['context' => $context])
            );

            // Track usage for analytics
            $this->templateGenerator->trackTemplateUsage(
                $templateId,
                auth()->id(),
                $context
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'generated_content' => $result['content'],
                    'template_id' => $templateId,
                    'variables_used' => $variables,
                    'generation_time' => $result['generation_time'] ?? null,
                    'template_version' => $result['version'] ?? '1.0',
                    'applied_optimizations' => $result['optimizations'] ?? []
                ],
                'message' => 'Content generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Template generation failed', [
                'template_id' => $request->get('template_id'),
                'variables' => $request->get('variables', []),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template generation failed',
                'message' => config('app.debug') ? $e->getMessage() : 'Generation error occurred'
            ], 500);
        }
    }

    /**
     * Create new custom template
     */
    public function createTemplate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'content' => 'required|string',
                'variables' => 'sometimes|array',
                'category' => 'sometimes|string|max:100',
                'tags' => 'sometimes|array',
                'tags.*' => 'string|max:50',
                'language' => 'sometimes|string|size:2',
                'is_public' => 'sometimes|boolean',
                'parent_template_id' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $templateData = [
                'name' => $request->get('name'),
                'description' => $request->get('description', ''),
                'content' => $request->get('content'),
                'variables' => $request->get('variables', []),
                'category' => $request->get('category', 'custom'),
                'tags' => $request->get('tags', []),
                'language' => $request->get('language', 'tr'),
                'is_public' => $request->get('is_public', false),
                'parent_template_id' => $request->get('parent_template_id'),
                'created_by' => auth()->id(),
                'created_at' => now()
            ];

            // Validate template content and variables
            $validation = $this->templateGenerator->validateTemplate($templateData);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors'],
                    'message' => 'Template validation failed'
                ], 422);
            }

            $template = $this->templateGenerator->createTemplate($templateData);

            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Template created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Template creation failed', [
                'template_data' => $request->all(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template creation failed'
            ], 500);
        }
    }

    /**
     * Update existing template
     */
    public function updateTemplate(Request $request, string $templateId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'content' => 'sometimes|string',
                'variables' => 'sometimes|array',
                'category' => 'sometimes|string|max:100',
                'tags' => 'sometimes|array',
                'tags.*' => 'string|max:50',
                'is_public' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if template exists and user has permission
            $template = $this->templateGenerator->getTemplate($templateId);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'error' => 'Template not found'
                ], 404);
            }

            if ($template['created_by'] !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
            }

            $updateData = array_filter($request->only([
                'name', 'description', 'content', 'variables', 
                'category', 'tags', 'is_public'
            ]));

            $updatedTemplate = $this->templateGenerator->updateTemplate($templateId, $updateData);

            return response()->json([
                'success' => true,
                'data' => $updatedTemplate,
                'message' => 'Template updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Template update failed', [
                'template_id' => $templateId,
                'update_data' => $request->all(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template update failed'
            ], 500);
        }
    }

    /**
     * Delete template
     */
    public function deleteTemplate(string $templateId): JsonResponse
    {
        try {
            // Check if template exists and user has permission
            $template = $this->templateGenerator->getTemplate($templateId);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'error' => 'Template not found'
                ], 404);
            }

            if ($template['created_by'] !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
            }

            $deleted = $this->templateGenerator->deleteTemplate($templateId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Template could not be deleted'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Template deletion failed', [
                'template_id' => $templateId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template deletion failed'
            ], 500);
        }
    }

    /**
     * Clone template
     */
    public function cloneTemplate(Request $request, string $templateId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'modifications' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $originalTemplate = $this->templateGenerator->getTemplate($templateId);
            
            if (!$originalTemplate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Original template not found'
                ], 404);
            }

            $cloneData = [
                'name' => $request->get('name'),
                'description' => $request->get('description', $originalTemplate['description'] ?? ''),
                'content' => $originalTemplate['content'],
                'variables' => $originalTemplate['variables'] ?? [],
                'category' => $originalTemplate['category'] ?? 'custom',
                'tags' => array_merge($originalTemplate['tags'] ?? [], ['cloned']),
                'language' => $originalTemplate['language'] ?? 'tr',
                'is_public' => false,
                'parent_template_id' => $templateId,
                'created_by' => auth()->id(),
                'created_at' => now()
            ];

            // Apply modifications if provided
            if ($request->has('modifications')) {
                $modifications = $request->get('modifications');
                
                if (isset($modifications['content'])) {
                    $cloneData['content'] = $modifications['content'];
                }
                
                if (isset($modifications['variables'])) {
                    $cloneData['variables'] = array_merge($cloneData['variables'], $modifications['variables']);
                }
            }

            $clonedTemplate = $this->templateGenerator->createTemplate($cloneData);

            return response()->json([
                'success' => true,
                'data' => $clonedTemplate,
                'message' => 'Template cloned successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Template cloning failed', [
                'template_id' => $templateId,
                'clone_data' => $request->all(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template cloning failed'
            ], 500);
        }
    }

    /**
     * Get template analytics
     */
    public function getTemplateAnalytics(string $templateId): JsonResponse
    {
        try {
            $template = $this->templateGenerator->getTemplate($templateId);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'error' => 'Template not found'
                ], 404);
            }

            // Get comprehensive analytics
            $analytics = $this->analyzer->getTemplateAnalytics($templateId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'template_id' => $templateId,
                    'template_name' => $template['name'],
                    'analytics' => $analytics
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Template analytics retrieval failed', [
                'template_id' => $templateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve template analytics'
            ], 500);
        }
    }

    /**
     * Preview template with sample data
     */
    public function previewTemplate(Request $request, string $templateId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sample_variables' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $template = $this->templateGenerator->getTemplate($templateId);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'error' => 'Template not found'
                ], 404);
            }

            // Use provided sample variables or generate defaults
            $sampleVariables = $request->get('sample_variables') ?? 
                $this->templateGenerator->generateSampleVariables($template);

            // Generate preview
            $preview = $this->templateGenerator->generatePreview($templateId, $sampleVariables);

            return response()->json([
                'success' => true,
                'data' => [
                    'template_id' => $templateId,
                    'preview_content' => $preview['content'],
                    'sample_variables' => $sampleVariables,
                    'template_structure' => $preview['structure'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Template preview failed', [
                'template_id' => $templateId,
                'sample_variables' => $request->get('sample_variables', []),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template preview failed'
            ], 500);
        }
    }
}