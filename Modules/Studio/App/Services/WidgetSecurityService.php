<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class WidgetSecurityService
{
    /**
     * Widget file path'ini validate et
     */
    public function validateWidgetPath(string $filePath): string
    {
        try {
            // Boş değer kontrolü
            if (empty($filePath)) {
                throw new InvalidArgumentException('Widget file path cannot be empty');
            }

            // Path traversal kontrolü
            if ($this->hasPathTraversal($filePath)) {
                throw new InvalidArgumentException('Path traversal detected in widget path: ' . $filePath);
            }

            // Sadece alfanumerik, dash, underscore ve slash karakterlerine izin ver
            if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $filePath)) {
                throw new InvalidArgumentException('Invalid characters in widget path: ' . $filePath);
            }

            // Path uzunluk kontrolü
            if (strlen($filePath) > 100) {
                throw new InvalidArgumentException('Widget path too long: ' . $filePath);
            }

            // Allowed path prefix'leri
            $allowedPrefixes = [
                'api/',
                'forms/',
                'content/',
                'layout/',
                'conditional/',
                'media/',
                'social/',
                'ecommerce/',
                'custom/'
            ];

            $hasValidPrefix = false;
            foreach ($allowedPrefixes as $prefix) {
                if (str_starts_with($filePath, $prefix)) {
                    $hasValidPrefix = true;
                    break;
                }
            }

            if (!$hasValidPrefix) {
                throw new InvalidArgumentException('Widget path must start with allowed prefix: ' . $filePath);
            }

            // View path'ini oluştur
            $viewPath = 'widgetmanagement::blocks.' . $filePath;
            
            // View'ın var olduğunu kontrol et
            if (!View::exists($viewPath)) {
                throw new InvalidArgumentException('Widget view does not exist: ' . $viewPath);
            }

            Log::debug('Widget path validated successfully', [
                'file_path' => $filePath,
                'view_path' => $viewPath
            ]);

            return $viewPath;

        } catch (Exception $e) {
            Log::error('Widget path validation failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Path traversal saldırısını tespit et
     */
    private function hasPathTraversal(string $path): bool
    {
        $dangerousPatterns = [
            '../',
            '..\\',
            '..',
            '/etc/',
            '/var/',
            '/usr/',
            '/tmp/',
            'C:',
            'D:',
            '\\',
            '%2e%2e',
            '0x2e',
            'passwd',
            'shadow',
            'hosts',
            'config'
        ];

        $normalizedPath = strtolower($path);
        
        foreach ($dangerousPatterns as $pattern) {
            if (str_contains($normalizedPath, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Widget dosya adını validate et
     */
    public function validateWidgetFileName(string $fileName): bool
    {
        // Sadece alfanumerik, dash ve underscore
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $fileName)) {
            return false;
        }

        // Uzunluk kontrolü
        if (strlen($fileName) > 50) {
            return false;
        }

        // Yasaklı dosya adları
        $forbiddenNames = [
            'index', 'admin', 'config', 'database', 'env', 'htaccess',
            'passwd', 'shadow', 'hosts', 'etc', 'var', 'tmp'
        ];

        return !in_array(strtolower($fileName), $forbiddenNames);
    }

    /**
     * Widget kategori path'ini validate et
     */
    public function validateCategoryPath(string $category): bool
    {
        $allowedCategories = [
            'api', 'forms', 'content', 'layout', 'conditional',
            'media', 'social', 'ecommerce', 'custom'
        ];

        return in_array($category, $allowedCategories);
    }

    /**
     * Widget settings'lerini validate et
     */
    public function validateWidgetSettings(array $settings): array
    {
        $cleanSettings = [];

        foreach ($settings as $key => $value) {
            // Key validation
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
                Log::warning('Invalid setting key removed', ['key' => $key]);
                continue;
            }

            // Value sanitization based on type
            $cleanSettings[$key] = $this->sanitizeSettingValue($value);
        }

        return $cleanSettings;
    }

    /**
     * Setting value'sunu sanitize et
     */
    private function sanitizeSettingValue($value)
    {
        if (is_string($value)) {
            // HTML strip
            $value = strip_tags($value);
            
            // Dangerous patterns remove
            $value = preg_replace('/javascript:/i', '', $value);
            $value = preg_replace('/data:(?!image)/i', '', $value);
            
            // Length limit
            return substr($value, 0, 1000);
        }

        if (is_array($value)) {
            return array_map([$this, 'sanitizeSettingValue'], $value);
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value;
        }

        // Unknown type, convert to string and sanitize
        return strip_tags((string)$value);
    }

    /**
     * Widget permission'larını kontrol et
     */
    public function checkWidgetPermission(int $widgetId, string $action = 'view'): bool
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return false;
            }

            // Super admin her şeyi yapabilir
            if ($user->hasRole('super-admin')) {
                return true;
            }

            // Widget specific permissions
            $permission = "widget-{$action}";
            
            // Global widget permission
            if ($user->can($permission)) {
                return true;
            }

            // Specific widget permission
            $specificPermission = "widget-{$widgetId}-{$action}";
            if ($user->can($specificPermission)) {
                return true;
            }

            // Module-based permission
            if ($user->can('studio', $action)) {
                return true;
            }

            Log::debug('Widget permission denied', [
                'user_id' => $user->id,
                'widget_id' => $widgetId,
                'action' => $action
            ]);

            return false;

        } catch (Exception $e) {
            Log::error('Widget permission check failed', [
                'widget_id' => $widgetId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Widget asset path'ini validate et
     */
    public function validateAssetPath(string $assetPath): bool
    {
        // Asset path kontrolü
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'css', 'js', 'woff', 'woff2', 'ttf'];
        $extension = pathinfo($assetPath, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return false;
        }

        // Path traversal kontrolü
        if ($this->hasPathTraversal($assetPath)) {
            return false;
        }

        return true;
    }

    /**
     * Widget URL'lerini validate et
     */
    public function validateWidgetUrl(string $url): bool
    {
        // URL format kontrolü
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Allowed schemes
        $allowedSchemes = ['http', 'https'];
        $parsedUrl = parse_url($url);
        
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], $allowedSchemes)) {
            return false;
        }

        // Local network kontrolü (isteğe bağlı)
        if (isset($parsedUrl['host'])) {
            $host = $parsedUrl['host'];
            
            // Private IP ranges (güvenlik için bloklanabilir)
            $privateRanges = [
                '127.0.0.1',
                'localhost',
                '10.',
                '172.16.',
                '192.168.'
            ];
            
            foreach ($privateRanges as $range) {
                if (str_starts_with($host, $range)) {
                    Log::warning('Widget attempting to access private network', [
                        'url' => $url,
                        'host' => $host
                    ]);
                    return false;
                }
            }
        }

        return true;
    }
}