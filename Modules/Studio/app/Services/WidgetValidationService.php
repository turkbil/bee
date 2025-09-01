<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Exception;

class WidgetValidationService
{
    /**
     * Widget türlerini tanımla
     */
    const WIDGET_TYPES = [
        'static' => 'Static HTML widget',
        'dynamic' => 'Dynamic content widget',
        'file' => 'File-based widget',
        'module' => 'Module integration widget'
    ];

    /**
     * Widget type'ını validate et
     */
    public function validateWidgetType(string $type): bool
    {
        return array_key_exists($type, self::WIDGET_TYPES);
    }

    /**
     * Widget data'sını validate et
     */
    public function validateWidgetData(array $data, string $type): array
    {
        try {
            $rules = $this->getValidationRules($type);
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                Log::warning('Widget data validation failed', [
                    'type' => $type,
                    'errors' => $validator->errors()->toArray(),
                    'data_keys' => array_keys($data)
                ]);

                throw new InvalidArgumentException(
                    'Widget validation failed: ' . implode(', ', $validator->errors()->all())
                );
            }

            return $this->sanitizeWidgetData($data, $type);

        } catch (Exception $e) {
            Log::error('Widget data validation error', [
                'type' => $type,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Widget type'ına göre validation rules'ları al
     */
    private function getValidationRules(string $type): array
    {
        $baseRules = [
            'name' => 'required|string|max:100|regex:/^[a-zA-Z0-9_\- ]+$/',
            'title' => 'sometimes|string|max:200',
            'description' => 'sometimes|string|max:500',
            'category' => 'required|string|in:api,forms,content,layout,conditional,media,social,ecommerce,custom',
            'is_active' => 'sometimes|boolean',
            'settings' => 'sometimes|array',
            'order' => 'sometimes|integer|min:0|max:999'
        ];

        switch ($type) {
            case 'static':
                return array_merge($baseRules, [
                    'content' => 'required|string|max:50000',
                    'css' => 'sometimes|string|max:10000',
                ]);

            case 'dynamic':
                return array_merge($baseRules, [
                    'data_source' => 'required|string|max:200',
                    'template' => 'required|string|max:10000',
                    'refresh_interval' => 'sometimes|integer|min:5|max:3600',
                    'cache_enabled' => 'sometimes|boolean'
                ]);

            case 'file':
                return array_merge($baseRules, [
                    'file_path' => 'required|string|max:200|regex:/^[a-zA-Z0-9\/_-]+$/',
                    'parameters' => 'sometimes|array',
                    'cache_enabled' => 'sometimes|boolean'
                ]);

            case 'module':
                return array_merge($baseRules, [
                    'module_name' => 'required|string|max:50|regex:/^[a-zA-Z0-9_-]+$/',
                    'component' => 'required|string|max:100|regex:/^[a-zA-Z0-9_-]+$/',
                    'method' => 'sometimes|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
                    'parameters' => 'sometimes|array'
                ]);

            default:
                return $baseRules;
        }
    }

    /**
     * Widget data'sını sanitize et
     */
    private function sanitizeWidgetData(array $data, string $type): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'name':
                case 'title':
                case 'description':
                    $sanitized[$key] = strip_tags(trim($value));
                    break;

                case 'content':
                case 'template':
                    // HTML content için HTMLPurifier kullanılacak
                    $sanitized[$key] = $value; // ContentSanitizationService'de işlenecek
                    break;

                case 'css':
                    // CSS için ayrı sanitization
                    $sanitized[$key] = $value; // ContentSanitizationService'de işlenecek
                    break;

                case 'file_path':
                    $sanitized[$key] = $this->sanitizeFilePath($value);
                    break;

                case 'data_source':
                case 'module_name':
                case 'component':
                case 'method':
                    $sanitized[$key] = preg_replace('/[^a-zA-Z0-9_\/-]/', '', $value);
                    break;

                case 'settings':
                case 'parameters':
                    $sanitized[$key] = $this->sanitizeArrayData($value);
                    break;

                case 'is_active':
                case 'cache_enabled':
                    $sanitized[$key] = (bool) $value;
                    break;

                case 'refresh_interval':
                case 'order':
                    $sanitized[$key] = (int) $value;
                    break;

                default:
                    if (is_string($value)) {
                        $sanitized[$key] = strip_tags(trim($value));
                    } else {
                        $sanitized[$key] = $value;
                    }
                    break;
            }
        }

        Log::debug('Widget data sanitized', [
            'type' => $type,
            'original_keys' => array_keys($data),
            'sanitized_keys' => array_keys($sanitized)
        ]);

        return $sanitized;
    }

    /**
     * File path'ini sanitize et
     */
    private function sanitizeFilePath(string $path): string
    {
        // Path traversal karakterlerini kaldır
        $path = str_replace(['../', '..\\', '\\'], '', $path);
        
        // Sadece güvenli karakterleri tut
        $path = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $path);
        
        return trim($path, '/');
    }

    /**
     * Array data'yı sanitize et
     */
    private function sanitizeArrayData(array $data, int $depth = 0): array
    {
        if ($depth > 5) { // Recursive limit
            return [];
        }

        $sanitized = [];

        foreach ($data as $key => $value) {
            // Key sanitization
            $cleanKey = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
            if (empty($cleanKey) || strlen($cleanKey) > 50) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$cleanKey] = $this->sanitizeArrayData($value, $depth + 1);
            } elseif (is_string($value)) {
                // String value sanitization
                $value = strip_tags($value);
                $value = substr($value, 0, 1000); // Length limit
                $sanitized[$cleanKey] = $value;
            } elseif (is_numeric($value)) {
                $sanitized[$cleanKey] = $value;
            } elseif (is_bool($value)) {
                $sanitized[$cleanKey] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Widget schema'sını validate et
     */
    public function validateWidgetSchema(array $schema): bool
    {
        try {
            $requiredFields = ['name', 'type', 'category'];
            
            foreach ($requiredFields as $field) {
                if (!isset($schema[$field]) || empty($schema[$field])) {
                    throw new InvalidArgumentException("Required field missing: {$field}");
                }
            }

            // Type validation
            if (!$this->validateWidgetType($schema['type'])) {
                throw new InvalidArgumentException("Invalid widget type: {$schema['type']}");
            }

            // Category validation
            $allowedCategories = ['api', 'forms', 'content', 'layout', 'conditional', 'media', 'social', 'ecommerce', 'custom'];
            if (!in_array($schema['category'], $allowedCategories)) {
                throw new InvalidArgumentException("Invalid widget category: {$schema['category']}");
            }

            // Type-specific validation
            $this->validateTypeSpecificSchema($schema);

            Log::debug('Widget schema validated successfully', [
                'type' => $schema['type'],
                'category' => $schema['category'],
                'name' => $schema['name']
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Widget schema validation failed', [
                'schema' => $schema,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Type-specific schema validation
     */
    private function validateTypeSpecificSchema(array $schema): void
    {
        switch ($schema['type']) {
            case 'static':
                if (!isset($schema['content']) || empty($schema['content'])) {
                    throw new InvalidArgumentException('Static widget requires content field');
                }
                break;

            case 'dynamic':
                if (!isset($schema['data_source']) || empty($schema['data_source'])) {
                    throw new InvalidArgumentException('Dynamic widget requires data_source field');
                }
                if (!isset($schema['template']) || empty($schema['template'])) {
                    throw new InvalidArgumentException('Dynamic widget requires template field');
                }
                break;

            case 'file':
                if (!isset($schema['file_path']) || empty($schema['file_path'])) {
                    throw new InvalidArgumentException('File widget requires file_path field');
                }
                break;

            case 'module':
                if (!isset($schema['module_name']) || empty($schema['module_name'])) {
                    throw new InvalidArgumentException('Module widget requires module_name field');
                }
                if (!isset($schema['component']) || empty($schema['component'])) {
                    throw new InvalidArgumentException('Module widget requires component field');
                }
                break;
        }
    }

    /**
     * Widget configuration'ını validate et
     */
    public function validateWidgetConfig(array $config): array
    {
        $defaultConfig = [
            'cache_enabled' => false,
            'cache_ttl' => 300,
            'lazy_load' => false,
            'responsive' => true,
            'permissions' => [],
            'dependencies' => [],
            'version' => '1.0.0'
        ];

        $validatedConfig = array_merge($defaultConfig, $config);

        // Cache TTL validation
        if (!is_int($validatedConfig['cache_ttl']) || $validatedConfig['cache_ttl'] < 0 || $validatedConfig['cache_ttl'] > 86400) {
            $validatedConfig['cache_ttl'] = 300;
        }

        // Version validation
        if (!preg_match('/^\d+\.\d+\.\d+$/', $validatedConfig['version'])) {
            $validatedConfig['version'] = '1.0.0';
        }

        // Dependencies validation
        if (!is_array($validatedConfig['dependencies'])) {
            $validatedConfig['dependencies'] = [];
        }

        // Permissions validation
        if (!is_array($validatedConfig['permissions'])) {
            $validatedConfig['permissions'] = [];
        }

        Log::debug('Widget config validated', [
            'original_keys' => array_keys($config),
            'validated_keys' => array_keys($validatedConfig)
        ]);

        return $validatedConfig;
    }

    /**
     * Widget performans metriklerini validate et
     */
    public function validatePerformanceMetrics(array $metrics): array
    {
        $allowedMetrics = [
            'render_time',
            'memory_usage',
            'query_count',
            'cache_hit_ratio',
            'error_count',
            'load_time'
        ];

        $validated = [];

        foreach ($metrics as $key => $value) {
            if (in_array($key, $allowedMetrics) && is_numeric($value)) {
                $validated[$key] = (float) $value;
            }
        }

        return $validated;
    }

    /**
     * Widget izinlerini validate et
     */
    public function validateWidgetPermissions(array $permissions, int $userId): bool
    {
        try {
            $user = auth()->user();
            if (!$user || $user->id !== $userId) {
                return false;
            }

            // Super admin tüm izinlere sahip
            if ($user->hasRole('super-admin')) {
                return true;
            }

            $requiredPermissions = [
                'widget-create',
                'widget-edit',
                'widget-delete',
                'widget-view'
            ];

            foreach ($permissions as $permission) {
                if (!in_array($permission, $requiredPermissions)) {
                    Log::warning('Invalid widget permission', [
                        'permission' => $permission,
                        'user_id' => $userId
                    ]);
                    return false;
                }

                if (!$user->can($permission)) {
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            Log::error('Widget permission validation error', [
                'permissions' => $permissions,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}