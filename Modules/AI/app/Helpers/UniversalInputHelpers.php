<?php

declare(strict_types=1);

/**
 * Universal Input System V3 Professional - Helper Functions & Utilities
 * Comprehensive helper functions for AI-powered form processing and management
 * 
 * @package Modules\AI\Helpers
 * @version 3.0.0
 * @author AI Universal Input System
 */

use Modules\AI\app\Services\V3\Universal\UniversalInputManagerV3;
use Modules\AI\app\Services\V3\Universal\BulkOperationProcessorV3;
use Modules\AI\app\Services\V3\Universal\ContextAwareEngine;
use Modules\AI\app\Services\V3\Universal\AnalyticsEngine;
use Modules\AI\app\Models\AIFeatureInput;
use Modules\AI\app\Models\AIInputGroup;
use Modules\AI\app\Models\AIInputOption;
use Modules\AI\app\Models\AIDynamicDataSource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

if (!function_exists('uis_get_form_structure')) {
    /**
     * Get complete form structure for a specific AI feature
     * 
     * @param int $featureId AI Feature ID
     * @param array $context Additional context data
     * @param bool $includeDefaults Include smart default values
     * @return array Complete form structure with inputs, groups, and validation
     */
    function uis_get_form_structure(int $featureId, array $context = [], bool $includeDefaults = true): array
    {
        try {
            $cacheKey = "uis_form_structure_{$featureId}_" . md5(serialize($context)) . "_{$includeDefaults}";
            
            return Cache::tags(['uis-forms', "feature-{$featureId}"])->remember(
                $cacheKey,
                config('ai.cache.form_structure_ttl', 3600),
                function () use ($featureId, $context, $includeDefaults) {
                    $manager = app(UniversalInputManagerV3::class);
                    return $manager->getFormStructure($featureId, $context, $includeDefaults);
                }
            );
        } catch (\Exception $e) {
            Log::error('UIS Helper - Form structure error', [
                'feature_id' => $featureId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to load form structure',
                'inputs' => [],
                'groups' => []
            ];
        }
    }
}

if (!function_exists('uis_process_form_submission')) {
    /**
     * Process form submission with AI integration
     * 
     * @param int $featureId AI Feature ID
     * @param array $data Form submission data
     * @param array $options Processing options
     * @return array Processing result with AI response
     */
    function uis_process_form_submission(int $featureId, array $data, array $options = []): array
    {
        try {
            $manager = app(UniversalInputManagerV3::class);
            $result = $manager->processFormSubmission($featureId, $data, $options);
            
            // Log successful submission for analytics
            if ($result['success']) {
                uis_log_analytics('form_submission', [
                    'feature_id' => $featureId,
                    'data_size' => strlen(json_encode($data)),
                    'processing_time' => $result['meta']['processing_time'] ?? 0,
                    'response_size' => strlen($result['ai_response'] ?? ''),
                    'status' => 'success'
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('UIS Helper - Form submission error', [
                'feature_id' => $featureId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            
            uis_log_analytics('form_submission', [
                'feature_id' => $featureId,
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Form submission failed',
                'message' => 'An error occurred while processing your request'
            ];
        }
    }
}

if (!function_exists('uis_validate_input_data')) {
    /**
     * Validate input data against feature requirements
     * 
     * @param int $featureId AI Feature ID
     * @param array $data Input data to validate
     * @param bool $strict Enable strict validation mode
     * @return array Validation result with errors and warnings
     */
    function uis_validate_input_data(int $featureId, array $data, bool $strict = false): array
    {
        try {
            $manager = app(UniversalInputManagerV3::class);
            return $manager->validateInputData($featureId, $data, $strict);
        } catch (\Exception $e) {
            Log::error('UIS Helper - Input validation error', [
                'feature_id' => $featureId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'errors' => ['general' => 'Validation failed due to system error'],
                'warnings' => []
            ];
        }
    }
}

if (!function_exists('uis_get_smart_defaults')) {
    /**
     * Get smart default values for form inputs
     * 
     * @param int $featureId AI Feature ID
     * @param array $context User and system context
     * @param string $locale User locale for localized defaults
     * @return array Smart default values for form fields
     */
    function uis_get_smart_defaults(int $featureId, array $context = [], string $locale = 'tr'): array
    {
        try {
            $cacheKey = "uis_smart_defaults_{$featureId}_" . md5(serialize($context)) . "_{$locale}";
            
            return Cache::tags(['uis-defaults', "feature-{$featureId}"])->remember(
                $cacheKey,
                config('ai.cache.defaults_ttl', 1800),
                function () use ($featureId, $context, $locale) {
                    $contextEngine = app(ContextAwareEngine::class);
                    return $contextEngine->generateSmartDefaults($featureId, $context, $locale);
                }
            );
        } catch (\Exception $e) {
            Log::error('UIS Helper - Smart defaults error', [
                'feature_id' => $featureId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
}

if (!function_exists('uis_get_field_suggestions')) {
    /**
     * Get AI-powered field suggestions based on context
     * 
     * @param int $featureId AI Feature ID
     * @param string $fieldId Input field identifier
     * @param string $currentValue Current field value
     * @param array $context Additional context data
     * @return array Array of suggested values with confidence scores
     */
    function uis_get_field_suggestions(int $featureId, string $fieldId, string $currentValue = '', array $context = []): array
    {
        try {
            $contextEngine = app(ContextAwareEngine::class);
            return $contextEngine->generateFieldSuggestions($featureId, $fieldId, $currentValue, $context);
        } catch (\Exception $e) {
            Log::error('UIS Helper - Field suggestions error', [
                'feature_id' => $featureId,
                'field_id' => $fieldId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'suggestions' => [],
                'confidence' => 0,
                'source' => 'error'
            ];
        }
    }
}

if (!function_exists('uis_create_bulk_operation')) {
    /**
     * Create and queue a bulk operation for processing multiple items
     * 
     * @param int $featureId AI Feature ID
     * @param array $items Array of items to process
     * @param array $options Bulk processing options
     * @return array Operation result with UUID and status
     */
    function uis_create_bulk_operation(int $featureId, array $items, array $options = []): array
    {
        try {
            $processor = app(BulkOperationProcessorV3::class);
            $operationId = $processor->createBulkOperation($featureId, $items, $options);
            
            uis_log_analytics('bulk_operation_created', [
                'operation_id' => $operationId,
                'feature_id' => $featureId,
                'items_count' => count($items),
                'options' => $options
            ]);
            
            return [
                'success' => true,
                'operation_id' => $operationId,
                'status' => 'queued',
                'items_count' => count($items),
                'estimated_completion' => now()->addMinutes(count($items) * 0.5)->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('UIS Helper - Bulk operation creation error', [
                'feature_id' => $featureId,
                'items_count' => count($items),
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to create bulk operation',
                'message' => $e->getMessage()
            ];
        }
    }
}

if (!function_exists('uis_get_operation_status')) {
    /**
     * Get status of a bulk operation
     * 
     * @param string $operationId UUID of the operation
     * @param bool $includeDetails Include detailed progress information
     * @return array Operation status with progress details
     */
    function uis_get_operation_status(string $operationId, bool $includeDetails = false): array
    {
        try {
            $processor = app(BulkOperationProcessorV3::class);
            return $processor->getOperationStatus($operationId, $includeDetails);
        } catch (\Exception $e) {
            Log::error('UIS Helper - Operation status error', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'status' => 'error',
                'error' => 'Failed to retrieve operation status'
            ];
        }
    }
}

if (!function_exists('uis_get_input_groups')) {
    /**
     * Get organized input groups for a feature
     * 
     * @param int $featureId AI Feature ID
     * @param bool $activeOnly Only return active groups
     * @return Collection Collection of input groups with their inputs
     */
    function uis_get_input_groups(int $featureId, bool $activeOnly = true): Collection
    {
        $query = AIInputGroup::with(['inputs' => function ($query) use ($activeOnly) {
            $query->where('is_active', true);
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            $query->orderBy('sort_order');
        }])->where('feature_id', $featureId);
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('sort_order')->get();
    }
}

if (!function_exists('uis_get_input_options')) {
    /**
     * Get options for a specific input field
     * 
     * @param int $inputId Input field ID
     * @param array $context Context for dynamic options
     * @return Collection Collection of input options
     */
    function uis_get_input_options(int $inputId, array $context = []): Collection
    {
        $input = AIFeatureInput::find($inputId);
        if (!$input) {
            return collect();
        }
        
        // Static options
        $staticOptions = AIInputOption::where('input_id', $inputId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Dynamic options from data source
        $dynamicOptions = collect();
        if ($input->dynamic_data_source_id) {
            $dataSource = AIDynamicDataSource::find($input->dynamic_data_source_id);
            if ($dataSource && $dataSource->is_active) {
                $dynamicOptions = uis_fetch_dynamic_options($dataSource, $context);
            }
        }
        
        return $staticOptions->merge($dynamicOptions);
    }
}

if (!function_exists('uis_fetch_dynamic_options')) {
    /**
     * Fetch options from dynamic data source
     * 
     * @param AIDynamicDataSource $dataSource Data source configuration
     * @param array $context Context for dynamic fetching
     * @return Collection Collection of dynamic options
     */
    function uis_fetch_dynamic_options(AIDynamicDataSource $dataSource, array $context = []): Collection
    {
        try {
            $cacheKey = "uis_dynamic_options_{$dataSource->id}_" . md5(serialize($context));
            
            return Cache::tags(['uis-dynamic-options'])->remember(
                $cacheKey,
                $dataSource->cache_ttl,
                function () use ($dataSource, $context) {
                    switch ($dataSource->source_type) {
                        case 'database':
                            return uis_fetch_database_options($dataSource, $context);
                        case 'api':
                            return uis_fetch_api_options($dataSource, $context);
                        case 'static':
                            return collect(json_decode($dataSource->source_config['options'] ?? '[]', true));
                        default:
                            return collect();
                    }
                }
            );
        } catch (\Exception $e) {
            Log::error('UIS Helper - Dynamic options fetch error', [
                'data_source_id' => $dataSource->id,
                'source_type' => $dataSource->source_type,
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
}

if (!function_exists('uis_fetch_database_options')) {
    /**
     * Fetch options from database source
     * 
     * @param AIDynamicDataSource $dataSource Data source configuration
     * @param array $context Context for query building
     * @return Collection Collection of database options
     */
    function uis_fetch_database_options(AIDynamicDataSource $dataSource, array $context = []): Collection
    {
        $config = $dataSource->source_config;
        $table = $config['table'] ?? null;
        $valueField = $config['value_field'] ?? 'id';
        $labelField = $config['label_field'] ?? 'name';
        $whereConditions = $config['where_conditions'] ?? [];
        
        if (!$table) {
            return collect();
        }
        
        $query = \DB::table($table)->select($valueField . ' as value', $labelField . ' as label');
        
        // Apply where conditions
        foreach ($whereConditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;
            
            if ($field && $value !== null) {
                // Replace context placeholders
                $value = str_replace(array_keys($context), array_values($context), $value);
                $query->where($field, $operator, $value);
            }
        }
        
        // Apply ordering
        if (isset($config['order_by'])) {
            $orderBy = $config['order_by'];
            $orderDirection = $config['order_direction'] ?? 'asc';
            $query->orderBy($orderBy, $orderDirection);
        }
        
        // Apply limit
        if (isset($config['limit'])) {
            $query->limit($config['limit']);
        }
        
        return collect($query->get());
    }
}

if (!function_exists('uis_fetch_api_options')) {
    /**
     * Fetch options from API source
     * 
     * @param AIDynamicDataSource $dataSource Data source configuration
     * @param array $context Context for API request
     * @return Collection Collection of API options
     */
    function uis_fetch_api_options(AIDynamicDataSource $dataSource, array $context = []): Collection
    {
        $config = $dataSource->source_config;
        $url = $config['url'] ?? null;
        $method = $config['method'] ?? 'GET';
        $headers = $config['headers'] ?? [];
        $params = $config['params'] ?? [];
        
        if (!$url) {
            return collect();
        }
        
        try {
            // Replace context placeholders in URL and params
            $url = str_replace(array_keys($context), array_values($context), $url);
            foreach ($params as $key => $value) {
                $params[$key] = str_replace(array_keys($context), array_values($context), $value);
            }
            
            $client = new \GuzzleHttp\Client(['timeout' => $config['timeout'] ?? 30]);
            $response = $client->request($method, $url, [
                'headers' => $headers,
                'query' => $method === 'GET' ? $params : [],
                'json' => $method !== 'GET' ? $params : null
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            // Extract options from response based on path configuration
            $optionsPath = $config['options_path'] ?? 'data';
            $options = data_get($data, $optionsPath, []);
            
            // Transform options if transformer is configured
            if (isset($config['transformer']) && is_callable($config['transformer'])) {
                $options = array_map($config['transformer'], $options);
            }
            
            return collect($options);
        } catch (\Exception $e) {
            Log::error('UIS Helper - API options fetch error', [
                'url' => $url,
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
}

if (!function_exists('uis_log_analytics')) {
    /**
     * Log analytics event for Universal Input System
     * 
     * @param string $event Event name
     * @param array $data Event data
     * @param string $level Log level
     * @return void
     */
    function uis_log_analytics(string $event, array $data = [], string $level = 'info'): void
    {
        try {
            $analyticsEngine = app(AnalyticsEngine::class);
            $analyticsEngine->logEvent($event, array_merge($data, [
                'timestamp' => now()->toISOString(),
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]));
        } catch (\Exception $e) {
            Log::error('UIS Helper - Analytics logging error', [
                'event' => $event,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }
}

if (!function_exists('uis_get_feature_analytics')) {
    /**
     * Get analytics data for a specific AI feature
     * 
     * @param int $featureId AI Feature ID
     * @param string $timeRange Time range for analytics (1h, 24h, 7d, 30d)
     * @param array $metrics Specific metrics to retrieve
     * @return array Analytics data with metrics and trends
     */
    function uis_get_feature_analytics(int $featureId, string $timeRange = '24h', array $metrics = []): array
    {
        try {
            $analyticsEngine = app(AnalyticsEngine::class);
            return $analyticsEngine->getFeatureAnalytics($featureId, $timeRange, $metrics);
        } catch (\Exception $e) {
            Log::error('UIS Helper - Feature analytics error', [
                'feature_id' => $featureId,
                'time_range' => $timeRange,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to retrieve analytics data',
                'metrics' => []
            ];
        }
    }
}

if (!function_exists('uis_clear_cache')) {
    /**
     * Clear Universal Input System caches
     * 
     * @param string|array|null $tags Cache tags to clear (null for all UIS caches)
     * @param int|null $featureId Specific feature ID to clear cache for
     * @return bool Success status
     */
    function uis_clear_cache($tags = null, ?int $featureId = null): bool
    {
        try {
            if ($featureId) {
                Cache::tags(["feature-{$featureId}"])->flush();
            }
            
            if ($tags === null) {
                // Clear all UIS caches
                $uisTags = [
                    'uis-forms',
                    'uis-defaults',
                    'uis-dynamic-options',
                    'uis-analytics',
                    'uis-context'
                ];
                foreach ($uisTags as $tag) {
                    Cache::tags([$tag])->flush();
                }
            } elseif (is_array($tags)) {
                foreach ($tags as $tag) {
                    Cache::tags([$tag])->flush();
                }
            } else {
                Cache::tags([$tags])->flush();
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('UIS Helper - Cache clear error', [
                'tags' => $tags,
                'feature_id' => $featureId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}

if (!function_exists('uis_generate_form_html')) {
    /**
     * Generate HTML for Universal Input System form
     * 
     * @param int $featureId AI Feature ID
     * @param array $options Form rendering options
     * @param array $values Pre-filled form values
     * @return string Generated HTML string
     */
    function uis_generate_form_html(int $featureId, array $options = [], array $values = []): string
    {
        try {
            $structure = uis_get_form_structure($featureId, $options['context'] ?? []);
            if (!$structure['success']) {
                return '<div class="uis-alert uis-alert-danger">Failed to load form structure</div>';
            }
            
            $html = '<div class="universal-input-system-v3" data-feature-id="' . $featureId . '">';
            
            // Form header
            if (!empty($options['title'])) {
                $html .= '<div class="uis-form-header">';
                $html .= '<h3 class="uis-form-title">' . e($options['title']) . '</h3>';
                if (!empty($options['subtitle'])) {
                    $html .= '<p class="uis-form-subtitle">' . e($options['subtitle']) . '</p>';
                }
                $html .= '</div>';
            }
            
            // Render groups
            foreach ($structure['groups'] as $group) {
                $html .= uis_render_input_group($group, $values, $options);
            }
            
            // Form actions
            $html .= '<div class="uis-form-actions">';
            $html .= '<button type="submit" class="uis-btn uis-btn-primary">';
            $html .= $options['submit_text'] ?? __('ai::admin.submit');
            $html .= '</button>';
            $html .= '</div>';
            
            $html .= '</div>';
            
            return $html;
        } catch (\Exception $e) {
            Log::error('UIS Helper - Form HTML generation error', [
                'feature_id' => $featureId,
                'options' => $options,
                'error' => $e->getMessage()
            ]);
            
            return '<div class="uis-alert uis-alert-danger">Form generation failed</div>';
        }
    }
}

if (!function_exists('uis_render_input_group')) {
    /**
     * Render HTML for a single input group
     * 
     * @param array $group Input group data
     * @param array $values Form values
     * @param array $options Rendering options
     * @return string Generated HTML for the group
     */
    function uis_render_input_group(array $group, array $values = [], array $options = []): string
    {
        $html = '<div class="uis-input-group" data-group-id="' . ($group['id'] ?? '') . '">';
        
        // Group header
        if (!empty($group['title'])) {
            $html .= '<div class="uis-input-group-header">';
            $html .= '<h4 class="uis-input-group-title">' . e($group['title']) . '</h4>';
            if (!empty($group['badge'])) {
                $html .= '<span class="uis-input-group-badge">' . e($group['badge']) . '</span>';
            }
            $html .= '</div>';
        }
        
        // Group description
        if (!empty($group['description'])) {
            $html .= '<p class="uis-help-text">' . e($group['description']) . '</p>';
        }
        
        // Render inputs
        foreach ($group['inputs'] ?? [] as $input) {
            $html .= uis_render_form_input($input, $values, $options);
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('uis_render_form_input')) {
    /**
     * Render HTML for a single form input
     * 
     * @param array $input Input configuration
     * @param array $values Form values
     * @param array $options Rendering options
     * @return string Generated HTML for the input
     */
    function uis_render_form_input(array $input, array $values = [], array $options = []): string
    {
        $inputId = $input['key'] ?? '';
        $inputType = $input['type'] ?? 'text';
        $currentValue = $values[$inputId] ?? $input['default_value'] ?? '';
        $isRequired = $input['required'] ?? false;
        
        $html = '<div class="uis-input-field" data-input-id="' . $inputId . '">';
        
        // Label
        if (!empty($input['label'])) {
            $labelClasses = 'uis-label';
            if ($isRequired) {
                $labelClasses .= ' uis-label-required';
            } elseif ($input['optional'] ?? false) {
                $labelClasses .= ' uis-label-optional';
            }
            
            $html .= '<label for="' . $inputId . '" class="' . $labelClasses . '">';
            $html .= e($input['label']);
            $html .= '</label>';
        }
        
        // Input field
        switch ($inputType) {
            case 'textarea':
                $html .= uis_render_textarea($input, $currentValue);
                break;
            case 'select':
                $html .= uis_render_select($input, $currentValue);
                break;
            case 'checkbox':
                $html .= uis_render_checkbox($input, $currentValue);
                break;
            case 'radio':
                $html .= uis_render_radio($input, $currentValue);
                break;
            case 'file':
                $html .= uis_render_file_input($input);
                break;
            default:
                $html .= uis_render_text_input($input, $currentValue);
                break;
        }
        
        // Help text
        if (!empty($input['help_text'])) {
            $html .= '<div class="uis-help-text">';
            $html .= '<i class="uis-help-text-icon fas fa-info-circle"></i>';
            $html .= e($input['help_text']);
            $html .= '</div>';
        }
        
        // Validation feedback containers
        $html .= '<div class="uis-valid-feedback"></div>';
        $html .= '<div class="uis-invalid-feedback"></div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('uis_render_text_input')) {
    /**
     * Render text-based input field
     * 
     * @param array $input Input configuration
     * @param string $value Current value
     * @return string Generated HTML
     */
    function uis_render_text_input(array $input, string $value = ''): string
    {
        $inputId = $input['key'] ?? '';
        $inputType = $input['type'] ?? 'text';
        $placeholder = $input['placeholder'] ?? '';
        $isRequired = $input['required'] ?? false;
        $validationRules = $input['validation_rules'] ?? [];
        
        $attributes = [
            'type' => $inputType,
            'id' => $inputId,
            'name' => $inputId,
            'class' => 'uis-form-control',
            'value' => e($value),
            'placeholder' => e($placeholder)
        ];
        
        if ($isRequired) {
            $attributes['required'] = 'required';
        }
        
        // Add validation attributes
        foreach ($validationRules as $rule => $ruleValue) {
            switch ($rule) {
                case 'min_length':
                    $attributes['minlength'] = $ruleValue;
                    break;
                case 'max_length':
                    $attributes['maxlength'] = $ruleValue;
                    break;
                case 'pattern':
                    $attributes['pattern'] = $ruleValue;
                    break;
            }
        }
        
        $attributeString = '';
        foreach ($attributes as $key => $attrValue) {
            $attributeString .= ' ' . $key . '="' . $attrValue . '"';
        }
        
        return '<input' . $attributeString . '>';
    }
}

if (!function_exists('uis_render_textarea')) {
    /**
     * Render textarea input field
     * 
     * @param array $input Input configuration
     * @param string $value Current value
     * @return string Generated HTML
     */
    function uis_render_textarea(array $input, string $value = ''): string
    {
        $inputId = $input['key'] ?? '';
        $placeholder = $input['placeholder'] ?? '';
        $isRequired = $input['required'] ?? false;
        $rows = $input['rows'] ?? 4;
        
        $attributes = [
            'id' => $inputId,
            'name' => $inputId,
            'class' => 'uis-form-control uis-textarea',
            'rows' => $rows,
            'placeholder' => e($placeholder)
        ];
        
        if ($isRequired) {
            $attributes['required'] = 'required';
        }
        
        $attributeString = '';
        foreach ($attributes as $key => $attrValue) {
            $attributeString .= ' ' . $key . '="' . $attrValue . '"';
        }
        
        return '<textarea' . $attributeString . '>' . e($value) . '</textarea>';
    }
}

if (!function_exists('uis_render_select')) {
    /**
     * Render select dropdown input field
     * 
     * @param array $input Input configuration
     * @param string $value Current value
     * @return string Generated HTML
     */
    function uis_render_select(array $input, string $value = ''): string
    {
        $inputId = $input['key'] ?? '';
        $isRequired = $input['required'] ?? false;
        $isMultiple = $input['multiple'] ?? false;
        $options = $input['options'] ?? [];
        
        $attributes = [
            'id' => $inputId,
            'name' => $inputId . ($isMultiple ? '[]' : ''),
            'class' => 'uis-form-control uis-select'
        ];
        
        if ($isRequired) {
            $attributes['required'] = 'required';
        }
        
        if ($isMultiple) {
            $attributes['multiple'] = 'multiple';
        }
        
        $attributeString = '';
        foreach ($attributes as $key => $attrValue) {
            $attributeString .= ' ' . $key . '="' . $attrValue . '"';
        }
        
        $html = '<select' . $attributeString . '>';
        
        // Add placeholder option
        if (!$isRequired && !$isMultiple) {
            $html .= '<option value="">' . e($input['placeholder'] ?? __('ai::admin.select_option')) . '</option>';
        }
        
        // Add options
        foreach ($options as $option) {
            $optionValue = $option['value'] ?? $option['id'] ?? '';
            $optionLabel = $option['label'] ?? $option['name'] ?? $optionValue;
            $isSelected = ($optionValue == $value) || (is_array($value) && in_array($optionValue, $value));
            
            $html .= '<option value="' . e($optionValue) . '"' . ($isSelected ? ' selected' : '') . '>';
            $html .= e($optionLabel);
            $html .= '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
}

if (!function_exists('uis_render_checkbox')) {
    /**
     * Render checkbox input field
     * 
     * @param array $input Input configuration
     * @param mixed $value Current value
     * @return string Generated HTML
     */
    function uis_render_checkbox(array $input, $value = null): string
    {
        $inputId = $input['key'] ?? '';
        $label = $input['label'] ?? '';
        $checkValue = $input['value'] ?? '1';
        $isChecked = ($value == $checkValue) || ($value === true);
        
        $html = '<div class="uis-form-check">';
        $html .= '<input type="checkbox" id="' . $inputId . '" name="' . $inputId . '" class="uis-form-check-input" value="' . e($checkValue) . '"' . ($isChecked ? ' checked' : '') . '>';
        $html .= '<label for="' . $inputId . '" class="uis-form-check-label">' . e($label) . '</label>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('uis_render_radio')) {
    /**
     * Render radio button group
     * 
     * @param array $input Input configuration
     * @param string $value Current value
     * @return string Generated HTML
     */
    function uis_render_radio(array $input, string $value = ''): string
    {
        $inputId = $input['key'] ?? '';
        $options = $input['options'] ?? [];
        
        $html = '<div class="uis-radio-group">';
        
        foreach ($options as $index => $option) {
            $optionValue = $option['value'] ?? $option['id'] ?? '';
            $optionLabel = $option['label'] ?? $option['name'] ?? $optionValue;
            $optionId = $inputId . '_' . $index;
            $isChecked = ($optionValue == $value);
            
            $html .= '<div class="uis-form-check">';
            $html .= '<input type="radio" id="' . $optionId . '" name="' . $inputId . '" class="uis-form-check-input" value="' . e($optionValue) . '"' . ($isChecked ? ' checked' : '') . '>';
            $html .= '<label for="' . $optionId . '" class="uis-form-check-label">' . e($optionLabel) . '</label>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('uis_render_file_input')) {
    /**
     * Render file upload input field
     * 
     * @param array $input Input configuration
     * @return string Generated HTML
     */
    function uis_render_file_input(array $input): string
    {
        $inputId = $input['key'] ?? '';
        $isRequired = $input['required'] ?? false;
        $acceptedTypes = $input['accepted_types'] ?? [];
        $maxSize = $input['max_size'] ?? null;
        $isMultiple = $input['multiple'] ?? false;
        
        $attributes = [
            'type' => 'file',
            'id' => $inputId,
            'name' => $inputId . ($isMultiple ? '[]' : ''),
            'class' => 'uis-form-control'
        ];
        
        if ($isRequired) {
            $attributes['required'] = 'required';
        }
        
        if ($isMultiple) {
            $attributes['multiple'] = 'multiple';
        }
        
        if (!empty($acceptedTypes)) {
            $attributes['accept'] = implode(',', $acceptedTypes);
        }
        
        $attributeString = '';
        foreach ($attributes as $key => $attrValue) {
            $attributeString .= ' ' . $key . '="' . $attrValue . '"';
        }
        
        $html = '<input' . $attributeString . '>';
        
        // Add file constraints info
        if (!empty($acceptedTypes) || $maxSize) {
            $html .= '<div class="uis-help-text">';
            if (!empty($acceptedTypes)) {
                $html .= __('ai::admin.accepted_file_types') . ': ' . implode(', ', $acceptedTypes);
            }
            if ($maxSize) {
                $html .= ($acceptedTypes ? ' | ' : '') . __('ai::admin.max_file_size') . ': ' . uis_format_file_size($maxSize);
            }
            $html .= '</div>';
        }
        
        return $html;
    }
}

if (!function_exists('uis_format_file_size')) {
    /**
     * Format file size in human readable format
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    function uis_format_file_size(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}

if (!function_exists('uis_sanitize_input')) {
    /**
     * Sanitize user input for Universal Input System
     * 
     * @param mixed $input Input value to sanitize
     * @param string $type Input type for specific sanitization
     * @return mixed Sanitized input value
     */
    function uis_sanitize_input($input, string $type = 'text')
    {
        if (is_array($input)) {
            return array_map(function ($item) use ($type) {
                return uis_sanitize_input($item, $type);
            }, $input);
        }
        
        if (!is_string($input)) {
            return $input;
        }
        
        switch ($type) {
            case 'html':
                return strip_tags($input, '<p><br><strong><em><ul><ol><li>');
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'number':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'text':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
}

if (!function_exists('uis_is_feature_enabled')) {
    /**
     * Check if Universal Input System is enabled for a feature
     * 
     * @param int $featureId AI Feature ID
     * @return bool Whether UIS is enabled for the feature
     */
    function uis_is_feature_enabled(int $featureId): bool
    {
        try {
            $inputs = AIFeatureInput::where('feature_id', $featureId)
                ->where('is_active', true)
                ->count();
            
            return $inputs > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('uis_get_system_info')) {
    /**
     * Get Universal Input System information and status
     * 
     * @return array System information
     */
    function uis_get_system_info(): array
    {
        try {
            return [
                'version' => '3.0.0',
                'status' => 'active',
                'features_count' => AIFeatureInput::distinct('feature_id')->count(),
                'total_inputs' => AIFeatureInput::where('is_active', true)->count(),
                'total_groups' => AIInputGroup::where('is_active', true)->count(),
                'cache_status' => Cache::getStore() instanceof \Illuminate\Cache\RedisStore ? 'redis' : 'file',
                'last_update' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'version' => '3.0.0',
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}