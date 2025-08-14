<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\V3;

/**
 * ContextAwareEngine - V3 ROADMAP Enterprise Service
 * 
 * Intelligent context detection
 * Multi-dimensional context analysis
 * Smart context rule application
 */
readonly class ContextAwareEngine
{
    public function __construct(
        private \Illuminate\Database\DatabaseManager $database,
        private \Illuminate\Cache\Repository $cache,
        private \Illuminate\Http\Request $request
    ) {}

    /**
     * Request'ten tam context bilgilerini otomatik tespit et
     */
    public function detectContext(array $requestData = []): array
    {
        $context = [];

        // Modül context'i tespit et
        $context['module'] = $this->getModuleContext($requestData['module_name'] ?? null);

        // Kullanıcı context'i tespit et
        $context['user'] = $this->getUserContext((int)($requestData['user_id'] ?? auth()->id()));

        // Zaman context'i tespit et
        $context['time'] = $this->getTimeContext();

        // İçerik context'i tespit et (eğer mevcut içerik varsa)
        if (isset($requestData['content']) || isset($requestData['existing_content'])) {
            $context['content'] = $this->getContentContext($requestData['content'] ?? $requestData['existing_content']);
        }

        // Tenant context'i tespit et
        $context['tenant'] = $this->getTenantContext();

        // Device ve browser context
        $context['device'] = $this->getDeviceContext();

        return $context;
    }

    /**
     * Modül bazlı context bilgilerini al
     */
    public function getModuleContext(?string $moduleName): array
    {
        if (!$moduleName) {
            // URL'den modül tespit etmeye çalış
            $moduleName = $this->detectModuleFromUrl();
        }

        $cacheKey = "module_context_{$moduleName}";
        
        return $this->cache->remember($cacheKey, 1800, function() use ($moduleName) {
            // Modül konfigürasyonu al
            $moduleConfig = $this->database->table('ai_module_integrations')
                ->where('module_name', $moduleName)
                ->where('is_active', true)
                ->first();

            if (!$moduleConfig) {
                return [
                    'name' => $moduleName,
                    'type' => 'unknown',
                    'features' => [],
                    'context_data' => []
                ];
            }

            return [
                'name' => $moduleName,
                'type' => $moduleConfig->integration_type,
                'features' => json_decode($moduleConfig->features_available ?? '[]', true),
                'context_data' => json_decode($moduleConfig->context_data ?? '{}', true),
                'target_action' => $moduleConfig->target_action,
                'permissions' => json_decode($moduleConfig->permissions ?? '{}', true)
            ];
        });
    }

    /**
     * Kullanıcı bazlı context bilgilerini al
     */
    public function getUserContext(int $userId): array
    {
        $cacheKey = "user_context_{$userId}";
        
        return $this->cache->remember($cacheKey, 900, function() use ($userId) {
            // Kullanıcı temel bilgileri
            $user = $this->database->table('users')->where('id', $userId)->first();
            
            if (!$user) {
                return ['type' => 'anonymous', 'preferences' => []];
            }

            // Kullanıcının AI tercihlerini al
            $preferences = $this->database->table('ai_user_preferences')
                ->where('user_id', $userId)
                ->get()
                ->keyBy('preference_key')
                ->map(fn($pref) => json_decode($pref->preference_value, true))
                ->toArray();

            // Kullanıcının favori prompt'ları
            $favoritePrompts = $this->database->table('ai_user_preferences')
                ->where('user_id', $userId)
                ->where('preference_key', 'favorite_prompts')
                ->value('preference_value');

            // Kullanıcının kullanım geçmişi (son 30 gün)
            $usageHistory = $this->database->table('ai_usage_analytics')
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('feature_id, COUNT(*) as usage_count, AVG(tokens_used) as avg_tokens')
                ->groupBy('feature_id')
                ->orderBy('usage_count', 'desc')
                ->limit(10)
                ->get();

            return [
                'id' => $userId,
                'type' => $this->getUserType($user),
                'preferences' => $preferences,
                'favorite_prompts' => json_decode($favoritePrompts ?? '[]', true),
                'usage_patterns' => $usageHistory->toArray(),
                'experience_level' => $this->calculateExperienceLevel($usageHistory),
                'language' => $this->getUserLanguage($user),
                'timezone' => $this->getUserTimezone($user)
            ];
        });
    }

    /**
     * Zaman bazlı context bilgilerini al
     */
    public function getTimeContext(): array
    {
        $now = now();
        $hour = (int)$now->format('H');
        $dayOfWeek = (int)$now->format('w'); // 0=Sunday
        $dayOfMonth = (int)$now->format('d');
        $month = (int)$now->format('n');

        return [
            'current_time' => $now->toISOString(),
            'hour' => $hour,
            'time_of_day' => $this->getTimeOfDay($hour),
            'day_of_week' => $dayOfWeek,
            'day_type' => $this->getDayType($dayOfWeek),
            'day_of_month' => $dayOfMonth,
            'month' => $month,
            'season' => $this->getSeason($month),
            'is_business_hours' => $this->isBusinessHours($hour, $dayOfWeek),
            'is_peak_hours' => $this->isPeakHours($hour),
            'timezone' => config('app.timezone')
        ];
    }

    /**
     * İçerik bazlı context analizi
     */
    public function getContentContext(?string $content): array
    {
        if (empty($content)) {
            return ['type' => 'empty', 'analysis' => []];
        }

        $cacheKey = 'content_context_' . md5($content);
        
        return $this->cache->remember($cacheKey, 3600, function() use ($content) {
            $analysis = [];

            // Metin uzunluğu analizi
            $wordCount = str_word_count($content);
            $charCount = mb_strlen($content);
            
            $analysis['length'] = [
                'words' => $wordCount,
                'characters' => $charCount,
                'category' => $this->categorizeContentLength($wordCount)
            ];

            // Dil tespiti (basit)
            $analysis['language'] = $this->detectLanguage($content);

            // İçerik tipi tespiti
            $analysis['type'] = $this->detectContentType($content);

            // Tone analizi (basit)
            $analysis['tone'] = $this->analyzeTone($content);

            // Komplekslik analizi
            $analysis['complexity'] = $this->analyzeComplexity($content);

            // Keywords çıkarma (basit)
            $analysis['keywords'] = $this->extractKeywords($content);

            return [
                'type' => 'analyzed',
                'analysis' => $analysis,
                'suggestions' => $this->generateContentSuggestions($analysis)
            ];
        });
    }

    /**
     * Tenant bazlı context bilgilerini al
     */
    public function getTenantContext(): array
    {
        $tenantId = $this->getCurrentTenantId();
        
        if (!$tenantId) {
            return ['type' => 'central', 'data' => []];
        }

        $cacheKey = "tenant_context_{$tenantId}";
        
        return $this->cache->remember($cacheKey, 1800, function() use ($tenantId) {
            // Tenant profili
            $profile = $this->database->table('ai_tenant_profiles')
                ->where('tenant_id', $tenantId)
                ->first();

            // Tenant'ın aktif modülleri
            $activeModules = $this->database->table('module_tenants')
                ->join('modules', 'modules.id', '=', 'module_tenants.module_id')
                ->where('module_tenants.tenant_id', $tenantId)
                ->where('module_tenants.is_active', true)
                ->pluck('modules.name')
                ->toArray();

            // Tenant'ın dil ayarları
            $languages = $this->database->table('tenant_languages')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->get();

            return [
                'id' => $tenantId,
                'type' => 'tenant',
                'profile' => $profile ? json_decode($profile->data ?? '{}', true) : [],
                'active_modules' => $activeModules,
                'languages' => $languages->toArray(),
                'primary_language' => $languages->where('is_default', true)->first()?->code ?? 'tr',
                'business_info' => $this->getTenantBusinessInfo($tenantId)
            ];
        });
    }

    /**
     * Device ve browser context'i al
     */
    public function getDeviceContext(): array
    {
        $userAgent = $this->request->header('User-Agent', '');
        
        return [
            'user_agent' => $userAgent,
            'ip_address' => $this->request->ip(),
            'device_type' => $this->detectDeviceType($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'is_mobile' => $this->request->header('Sec-CH-UA-Mobile') === '?1',
            'platform' => $this->detectPlatform($userAgent)
        ];
    }

    /**
     * Context rules'ları uygula
     */
    public function applyRules(array $context): array
    {
        $rules = $this->database->table('ai_context_rules')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $appliedRules = [];
        $modifications = [];

        foreach ($rules as $rule) {
            $conditions = json_decode($rule->conditions ?? '{}', true);
            
            if ($this->evaluateRuleConditions($conditions, $context)) {
                $actions = json_decode($rule->actions ?? '{}', true);
                $modifications = array_merge($modifications, $actions);
                
                $appliedRules[] = [
                    'id' => $rule->id,
                    'name' => $rule->rule_name,
                    'type' => $rule->rule_type,
                    'actions' => $actions
                ];
            }
        }

        return [
            'original_context' => $context,
            'applied_rules' => $appliedRules,
            'modifications' => $modifications,
            'final_context' => $this->applyModifications($context, $modifications)
        ];
    }

    /**
     * Context'e göre öneriler üret
     */
    public function getRecommendations(array $context): array
    {
        $recommendations = [];

        // Modül bazlı öneriler
        if (isset($context['module']['name'])) {
            $recommendations['module'] = $this->getModuleRecommendations($context['module']);
        }

        // Kullanıcı bazlı öneriler
        if (isset($context['user']['usage_patterns'])) {
            $recommendations['user'] = $this->getUserRecommendations($context['user']);
        }

        // Zaman bazlı öneriler
        $recommendations['time'] = $this->getTimeRecommendations($context['time']);

        // İçerik bazlı öneriler
        if (isset($context['content']['analysis'])) {
            $recommendations['content'] = $this->getContentRecommendations($context['content']['analysis']);
        }

        return $recommendations;
    }

    /**
     * Private helper methods
     */
    private function detectModuleFromUrl(): ?string
    {
        $path = $this->request->path();
        
        // Admin panel URL patterns
        if (preg_match('/admin\/([^\/]+)/', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getUserType($user): string
    {
        // User role'üne göre tip belirle
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) return 'admin';
            if ($user->hasRole('editor')) return 'editor';
            if ($user->hasRole('author')) return 'author';
        }
        
        return 'regular';
    }

    private function calculateExperienceLevel($usageHistory): string
    {
        $totalUsage = $usageHistory->sum('usage_count');
        
        if ($totalUsage >= 100) return 'expert';
        if ($totalUsage >= 50) return 'advanced';
        if ($totalUsage >= 10) return 'intermediate';
        
        return 'beginner';
    }

    private function getUserLanguage($user): string
    {
        return $user->language ?? session('site_locale', 'tr');
    }

    private function getUserTimezone($user): string
    {
        return $user->timezone ?? config('app.timezone');
    }

    private function getTimeOfDay(int $hour): string
    {
        if ($hour >= 5 && $hour < 12) return 'morning';
        if ($hour >= 12 && $hour < 17) return 'afternoon';
        if ($hour >= 17 && $hour < 21) return 'evening';
        return 'night';
    }

    private function getDayType(int $dayOfWeek): string
    {
        return in_array($dayOfWeek, [0, 6]) ? 'weekend' : 'weekday';
    }

    private function getSeason(int $month): string
    {
        if (in_array($month, [12, 1, 2])) return 'winter';
        if (in_array($month, [3, 4, 5])) return 'spring';
        if (in_array($month, [6, 7, 8])) return 'summer';
        return 'autumn';
    }

    private function isBusinessHours(int $hour, int $dayOfWeek): bool
    {
        return !in_array($dayOfWeek, [0, 6]) && $hour >= 9 && $hour < 18;
    }

    private function isPeakHours(int $hour): bool
    {
        return in_array($hour, [9, 10, 11, 14, 15, 16]);
    }

    private function getCurrentTenantId(): ?int
    {
        return session('tenant_id') ?? tenancy()->tenant?->id;
    }

    private function getTenantBusinessInfo(int $tenantId): array
    {
        // Business bilgilerini çek
        return [
            'industry' => null,
            'size' => null,
            'location' => null
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
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    private function detectPlatform(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        return 'Unknown';
    }

    private function categorizeContentLength(int $wordCount): string
    {
        if ($wordCount < 100) return 'very_short';
        if ($wordCount < 300) return 'short';
        if ($wordCount < 800) return 'medium';
        if ($wordCount < 1500) return 'long';
        return 'very_long';
    }

    private function detectLanguage(string $content): string
    {
        // Basit Türkçe karakter kontrolü
        if (preg_match('/[ğüşıöçĞÜŞİÖÇ]/', $content)) {
            return 'tr';
        }
        return 'en'; // Default
    }

    private function detectContentType(string $content): string
    {
        if (preg_match('/<[^>]+>/', $content)) return 'html';
        if (preg_match('/^#|\*\*|\[.*\]\(.*\)/', $content)) return 'markdown';
        return 'plain_text';
    }

    private function analyzeTone(string $content): string
    {
        $formalWords = ['sayın', 'sevgiler', 'saygılar', 'teşekkür'];
        $casualWords = ['merhaba', 'selam', 'naber', 'hadi'];
        
        $formalCount = 0;
        $casualCount = 0;
        
        foreach ($formalWords as $word) {
            if (stripos($content, $word) !== false) $formalCount++;
        }
        
        foreach ($casualWords as $word) {
            if (stripos($content, $word) !== false) $casualCount++;
        }
        
        if ($formalCount > $casualCount) return 'formal';
        if ($casualCount > $formalCount) return 'casual';
        
        return 'neutral';
    }

    private function analyzeComplexity(string $content): string
    {
        $avgWordLength = str_word_count($content) > 0 ? 
            mb_strlen(str_replace(' ', '', $content)) / str_word_count($content) : 0;
        
        if ($avgWordLength > 6) return 'complex';
        if ($avgWordLength > 4) return 'medium';
        return 'simple';
    }

    private function extractKeywords(string $content): array
    {
        // Basit keyword extraction
        $words = str_word_count(strtolower($content), 1);
        $stopWords = ['ve', 'bir', 'bu', 'da', 'de', 'ile', 'için', 'olan', 'var'];
        $keywords = array_diff($words, $stopWords);
        $wordCounts = array_count_values($keywords);
        arsort($wordCounts);
        
        return array_slice(array_keys($wordCounts), 0, 10);
    }

    private function generateContentSuggestions(array $analysis): array
    {
        $suggestions = [];
        
        if ($analysis['length']['category'] === 'very_short') {
            $suggestions[] = 'İçeriği genişletmeyi düşünün';
        }
        
        if ($analysis['complexity'] === 'complex') {
            $suggestions[] = 'Daha basit ifadeler kullanabilirsiniz';
        }
        
        return $suggestions;
    }

    private function evaluateRuleConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $key => $expectedValue) {
            $actualValue = data_get($context, $key);
            if ($actualValue !== $expectedValue) {
                return false;
            }
        }
        return true;
    }

    private function applyModifications(array $context, array $modifications): array
    {
        foreach ($modifications as $key => $value) {
            data_set($context, $key, $value);
        }
        return $context;
    }

    private function getModuleRecommendations(array $moduleContext): array
    {
        return [
            'suggested_features' => $moduleContext['features'],
            'best_practices' => []
        ];
    }

    private function getUserRecommendations(array $userContext): array
    {
        return [
            'personalized_features' => [],
            'usage_tips' => []
        ];
    }

    private function getTimeRecommendations(array $timeContext): array
    {
        $recommendations = [];
        
        if ($timeContext['time_of_day'] === 'morning') {
            $recommendations[] = 'Sabah enerjinizi kullanarak yaratıcı içerikler oluşturun';
        }
        
        return $recommendations;
    }

    private function getContentRecommendations(array $contentAnalysis): array
    {
        return $contentAnalysis['suggestions'] ?? [];
    }
}