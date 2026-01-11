<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Modules\AI\App\Models\AIContextRules;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

readonly class ContextAwareEngine
{
    /**
     * Context detection - mevcut durumu analiz et
     */
    public function detectContext(array $request): array
    {
        $context = [
            'module' => $this->getModuleContext($request['module_name'] ?? ''),
            'user' => $this->getUserContext(),
            'time' => $this->getTimeContext(),
            'content' => $this->getContentContext($request['content'] ?? ''),
            'language' => $this->getLanguageContext($request['language'] ?? ''),
            'device' => $this->getDeviceContext(),
            'tenant' => $this->getTenantContext()
        ];

        return array_filter($context, fn($value) => !empty($value));
    }

    /**
     * Modül context bilgileri
     */
    public function getModuleContext(string $moduleName): array
    {
        if (empty($moduleName)) {
            return [];
        }

        return Cache::remember("module_context_{$moduleName}", 600, function () use ($moduleName) {
            $moduleConfig = config("{$moduleName}.ai_context", []);
            
            return [
                'name' => $moduleName,
                'type' => $this->getModuleType($moduleName),
                'features' => $this->getModuleFeatures($moduleName),
                'permissions' => $this->getModulePermissions($moduleName),
                'config' => $moduleConfig
            ];
        });
    }

    /**
     * Kullanıcı context bilgileri
     */
    public function getUserContext(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return ['type' => 'guest'];
        }

        return [
            'id' => $user->id,
            'type' => 'authenticated',
            'roles' => $user->getRoleNames()->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'preferences' => $this->getUserPreferences($user->id),
            'usage_stats' => $this->getUserUsageStats($user->id),
            'language' => $user->preferred_language ?? app()->getLocale()
        ];
    }

    /**
     * Zaman context bilgileri
     */
    public function getTimeContext(): array
    {
        $now = now();
        
        return [
            'hour' => $now->hour,
            'day_of_week' => $now->dayOfWeek,
            'is_weekend' => $now->isWeekend(),
            'is_business_hours' => $this->isBusinessHours($now),
            'timezone' => $now->timezone->getName(),
            'period' => $this->getTimePeriod($now->hour)
        ];
    }

    /**
     * İçerik context analizi
     */
    public function getContentContext(string $content): array
    {
        if (empty($content)) {
            return [];
        }

        return [
            'length' => strlen($content),
            'word_count' => str_word_count($content),
            'language' => $this->detectContentLanguage($content),
            'type' => $this->detectContentType($content),
            'complexity' => $this->analyzeComplexity($content),
            'sentiment' => $this->analyzeSentiment($content),
            'keywords' => $this->extractKeywords($content, 5)
        ];
    }

    /**
     * Dil context bilgileri
     */
    public function getLanguageContext(string $language = ''): array
    {
        $currentLang = $language ?: app()->getLocale();
        
        return [
            'current' => $currentLang,
            'available' => $this->getAvailableLanguages(),
            'rtl' => $this->isRTL($currentLang),
            'fallback' => config('app.fallback_locale')
        ];
    }

    /**
     * Context rules'ları uygula
     */
    public function applyRules(array $context): array
    {
        $rules = AIContextRules::where('is_active', true)
            ->orderBy('priority')
            ->get();

        $modifiedContext = $context;
        $appliedRules = [];

        foreach ($rules as $rule) {
            $conditions = json_decode($rule->conditions, true) ?? [];
            
            if ($this->matchesConditions($conditions, $context)) {
                $actions = json_decode($rule->actions, true) ?? [];
                $modifiedContext = $this->applyRuleActions($modifiedContext, $actions);
                $appliedRules[] = $rule->rule_key;
            }
        }

        $modifiedContext['applied_rules'] = $appliedRules;
        
        return $modifiedContext;
    }

    /**
     * Context bazlı öneriler getir
     */
    public function getRecommendations(array $context): array
    {
        $recommendations = [];

        // Modül bazlı öneriler
        if (isset($context['module']['name'])) {
            $recommendations['features'] = $this->getModuleFeatureRecommendations($context['module']['name']);
        }

        // Kullanıcı bazlı öneriler
        if (isset($context['user']['id'])) {
            $recommendations['personal'] = $this->getPersonalRecommendations($context['user']['id']);
        }

        // Zaman bazlı öneriler
        if (isset($context['time'])) {
            $recommendations['time_based'] = $this->getTimeBasedRecommendations($context['time']);
        }

        // İçerik bazlı öneriler
        if (isset($context['content'])) {
            $recommendations['content_based'] = $this->getContentBasedRecommendations($context['content']);
        }

        return array_filter($recommendations);
    }

    /**
     * Conditions'ı kontrol et
     */
    public function matchesConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $key => $expectedValue) {
            $actualValue = data_get($context, $key);
            
            if ($actualValue === null) {
                return false;
            }

            if (is_array($expectedValue)) {
                // Array içinde değer arama
                if (!in_array($actualValue, $expectedValue)) {
                    return false;
                }
            } elseif (is_string($expectedValue) && str_contains($expectedValue, '*')) {
                // Wildcard matching
                if (!fnmatch($expectedValue, (string) $actualValue)) {
                    return false;
                }
            } elseif (is_numeric($expectedValue) && is_numeric($actualValue)) {
                // Numeric comparison
                if ((float) $actualValue !== (float) $expectedValue) {
                    return false;
                }
            } else {
                // Exact match
                if ($actualValue !== $expectedValue) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Private helper methods
     */
    private function getModuleType(string $moduleName): string
    {
        return match ($moduleName) {
            'Page', 'Announcement', 'Portfolio' => 'content',
            'UserManagement', 'TenantManagement' => 'management',
            'AI' => 'service',
            'Studio', 'ThemeManagement' => 'design',
            default => 'other'
        };
    }

    private function getModuleFeatures(string $moduleName): array
    {
        return Cache::remember("module_features_{$moduleName}", 3600, function () use ($moduleName) {
            // AI modülünde bu modül için mevcut feature'ları getir
            return \Modules\AI\App\Models\AIFeature::whereJsonContains('supported_modules', $moduleName)
                ->where('is_active', true)
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    private function getModulePermissions(string $moduleName): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        return $user->getAllPermissions()
            ->filter(fn($permission) => str_starts_with($permission->name, strtolower($moduleName)))
            ->pluck('name')
            ->toArray();
    }

    private function getUserPreferences(int $userId): array
    {
        return Cache::remember("user_preferences_{$userId}", 1800, function () use ($userId) {
            return \Modules\AI\App\Models\AIUserPreferences::where('user_id', $userId)
                ->get()
                ->groupBy('preference_key')
                ->map(fn($prefs) => $prefs->first()->preference_value)
                ->toArray();
        });
    }

    private function getUserUsageStats(int $userId): array
    {
        return Cache::remember("user_usage_stats_{$userId}", 900, function () use ($userId) {
            $stats = \Modules\AI\App\Models\AIUsageAnalytics::where('user_id', $userId)
                ->selectRaw('COUNT(*) as total_uses, AVG(response_time_ms) as avg_response_time, COUNT(DISTINCT feature_id) as features_used')
                ->where('created_at', '>=', now()->subDays(30))
                ->first();

            return [
                'total_uses' => $stats->total_uses ?? 0,
                'avg_response_time' => round($stats->avg_response_time ?? 0),
                'features_used' => $stats->features_used ?? 0,
                'last_used' => now()->subDays(1)->toDateString()
            ];
        });
    }

    private function isBusinessHours(\Carbon\Carbon $time): bool
    {
        return $time->hour >= 9 && $time->hour <= 17 && !$time->isWeekend();
    }

    private function getTimePeriod(int $hour): string
    {
        return match (true) {
            $hour >= 6 && $hour < 12 => 'morning',
            $hour >= 12 && $hour < 17 => 'afternoon', 
            $hour >= 17 && $hour < 21 => 'evening',
            default => 'night'
        };
    }

    private function detectContentLanguage(string $content): string
    {
        // Basit dil tespiti - gerçek projede daha gelişmiş bir sistem kullanılabilir
        $turkishChars = preg_match('/[çğıöşüÇĞIİÖŞÜ]/u', $content);
        
        return $turkishChars ? 'tr' : 'en';
    }

    private function detectContentType(string $content): string
    {
        if (str_contains($content, '<') && str_contains($content, '>')) {
            return 'html';
        }
        
        if (preg_match('/^#\s+/', $content) || str_contains($content, '**')) {
            return 'markdown';
        }
        
        if (str_contains($content, '{') && str_contains($content, '}')) {
            return 'json';
        }
        
        return 'text';
    }

    private function analyzeComplexity(string $content): string
    {
        $wordCount = str_word_count($content);
        $sentences = substr_count($content, '.') + substr_count($content, '!') + substr_count($content, '?');
        $avgWordsPerSentence = $sentences > 0 ? $wordCount / $sentences : $wordCount;
        
        return match (true) {
            $avgWordsPerSentence > 20 => 'high',
            $avgWordsPerSentence > 12 => 'medium',
            default => 'low'
        };
    }

    private function analyzeSentiment(string $content): string
    {
        // Basit sentiment analizi - pozitif/negatif kelime sayısı
        $positive = preg_match_all('/\b(güzel|harika|mükemmel|başarılı|iyi|olumlu)\b/ui', $content);
        $negative = preg_match_all('/\b(kötü|berbat|başarısız|olumsuz|problem|hata)\b/ui', $content);
        
        if ($positive > $negative) return 'positive';
        if ($negative > $positive) return 'negative';
        
        return 'neutral';
    }

    private function extractKeywords(string $content, int $limit = 5): array
    {
        // Basit keyword çıkarma - en sık kullanılan kelimeleri bul
        $words = preg_split('/\s+/', strtolower($content));
        $words = array_filter($words, fn($word) => strlen($word) > 3);
        
        $stopWords = ['için', 'olan', 'olan', 'çok', 'daha', 'sonra', 'önce', 'kadar'];
        $words = array_diff($words, $stopWords);
        
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        
        return array_keys(array_slice($wordCounts, 0, $limit));
    }

    private function getAvailableLanguages(): array
    {
        return Cache::remember('available_languages', 3600, function () {
            // Sistem dillerini getir
            return ['tr', 'en']; // Basit örnek
        });
    }

    private function isRTL(string $language): bool
    {
        return in_array($language, ['ar', 'he', 'fa', 'ur']);
    }

    private function getDeviceContext(): array
    {
        $userAgent = request()->header('User-Agent', '');
        
        return [
            'type' => $this->detectDeviceType($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'is_mobile' => $this->isMobile($userAgent)
        ];
    }

    private function getTenantContext(): array
    {
        if (!tenant()) {
            return ['type' => 'central'];
        }

        return [
            'type' => 'tenant',
            'id' => tenant('id'),
            'domain' => tenant('id'),
            'plan' => 'basic' // Tenant plan bilgisi
        ];
    }

    private function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        }
        
        if (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/', $userAgent)) return 'chrome';
        if (preg_match('/Firefox/', $userAgent)) return 'firefox';
        if (preg_match('/Safari/', $userAgent)) return 'safari';
        if (preg_match('/Edge/', $userAgent)) return 'edge';
        
        return 'other';
    }

    private function isMobile(string $userAgent): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone/', $userAgent);
    }

    private function applyRuleActions(array $context, array $actions): array
    {
        foreach ($actions as $action) {
            $type = $action['type'] ?? '';
            
            switch ($type) {
                case 'set_variable':
                    $context[$action['key']] = $action['value'];
                    break;
                case 'append_array':
                    $context[$action['key']] = array_merge(
                        $context[$action['key']] ?? [], 
                        $action['values']
                    );
                    break;
                case 'modify_priority':
                    $context['priority_modifier'] = $action['modifier'];
                    break;
            }
        }
        
        return $context;
    }

    private function getModuleFeatureRecommendations(string $moduleName): array
    {
        // Modül için önerilen feature'ları getir
        return Cache::remember("module_recommendations_{$moduleName}", 1800, function () use ($moduleName) {
            return \Modules\AI\App\Models\AIFeature::whereJsonContains('supported_modules', $moduleName)
                ->where('is_active', true)
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    private function getPersonalRecommendations(int $userId): array
    {
        // Kullanıcının geçmiş kullanımına göre öneriler
        return Cache::remember("personal_recommendations_{$userId}", 1800, function () use ($userId) {
            return \Modules\AI\App\Models\AIUsageAnalytics::where('user_id', $userId)
                ->where('success', true)
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('feature_id')
                ->selectRaw('feature_id, COUNT(*) as usage_count')
                ->orderBy('usage_count', 'desc')
                ->limit(3)
                ->pluck('usage_count', 'feature_id')
                ->toArray();
        });
    }

    private function getTimeBasedRecommendations(array $timeContext): array
    {
        // Zaman bazlı öneriler
        $period = $timeContext['period'] ?? 'day';
        
        return match ($period) {
            'morning' => ['content_creation', 'seo_optimization'],
            'afternoon' => ['analysis', 'translation'],
            'evening' => ['social_media', 'email_marketing'],
            default => ['quick_tasks']
        };
    }

    private function getContentBasedRecommendations(array $contentContext): array
    {
        // İçerik türüne göre öneriler
        $type = $contentContext['type'] ?? 'text';
        $complexity = $contentContext['complexity'] ?? 'medium';
        
        $recommendations = [];
        
        if ($type === 'html') {
            $recommendations[] = 'seo_optimization';
        }
        
        if ($complexity === 'high') {
            $recommendations[] = 'content_simplification';
        }
        
        if ($contentContext['length'] > 1000) {
            $recommendations[] = 'content_summary';
        }
        
        return $recommendations;
    }
}