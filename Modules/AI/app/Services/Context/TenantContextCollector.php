<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\AITenantProfile;

/**
 * Tenant Context Collector  
 * Tenant/şirket bilgilerini toplar ve context oluşturur
 */
class TenantContextCollector extends ContextCollector
{
    public function __construct()
    {
        parent::__construct('tenant');
        // Tenant context daha uzun cache'lenir (1 saat)
        $this->cacheTtl = 3600;
    }

    /**
     * Tenant context'ini topla
     */
    protected function collectContext(array $options): array
    {
        $tenantId = tenant('id');
        
        if (!$tenantId) {
            return [
                'type' => 'no_tenant',
                'has_tenant' => false,
                'context_text' => '🏢 NO TENANT: Genel sistem modu.',
                'priority' => 5
            ];
        }

        // AI Tenant Profile'ı al
        $profile = AITenantProfile::where('tenant_id', $tenantId)->first();
        
        if (!$profile || !$profile->is_completed) {
            return [
                'type' => 'incomplete_profile',
                'has_tenant' => true,
                'tenant_id' => $tenantId,
                'context_text' => '🏢 INCOMPLETE PROFILE: Temel şirket modu.',
                'priority' => 4
            ];
        }

        // Tam profil var - detaylı context oluştur
        $context = [
            'type' => 'complete_profile',
            'has_tenant' => true,
            'tenant_id' => $tenantId,
            'profile_id' => $profile->id,
            'is_completed' => $profile->is_completed,
            'priority' => 1 // Highest priority for complete profiles
        ];

        // Temel şirket bilgileri
        if ($profile->company_info) {
            $context['company'] = [
                'name' => $profile->company_info['brand_name'] ?? null,
                'city' => $profile->company_info['city'] ?? null,
                'main_service' => $profile->company_info['main_service'] ?? null,
                'founding_year' => $profile->company_info['founding_year'] ?? null,
                'founder' => $profile->company_info['founder'] ?? null,
                'ceo' => $profile->company_info['ceo'] ?? null,
            ];
        }

        // Sektör bilgileri
        if ($profile->sector_details) {
            $context['sector'] = [
                'selection' => $profile->sector_details['sector_selection'] ?? null,
                'brand_personality' => $profile->sector_details['brand_personality'] ?? [],
                'target_audience' => $profile->sector_details['target_audience'] ?? [],
                'market_position' => $profile->sector_details['market_position'] ?? null,
            ];
        }

        // AI davranış kuralları
        if ($profile->ai_behavior_rules) {
            $context['behavior'] = [
                'writing_tone' => $profile->ai_behavior_rules['writing_tone'] ?? [],
                'communication_style' => $profile->ai_behavior_rules['communication_style'] ?? [],
                'emphasis_points' => $profile->ai_behavior_rules['emphasis_points'] ?? [],
                'avoid_topics' => $profile->ai_behavior_rules['avoid_topics'] ?? [],
            ];
        }

        // Data context (yeni eklenen)
        if ($profile->data) {
            $context['additional_data'] = $profile->data;
        }

        // Mevcut optimized context'i kullan
        $context['optimized_context'] = $profile->getOptimizedAIContext(3);
        
        // AI için context metni oluştur
        $context['context_text'] = $this->buildContextText($context, $profile);

        Log::debug('Tenant context collected', [
            'tenant_id' => $tenantId,
            'profile_id' => $profile->id,
            'company_name' => $context['company']['name'] ?? 'Unknown',
            'context_size' => strlen($context['context_text'])
        ]);

        return $context;
    }

    /**
     * AI için okunabilir context metni oluştur
     */
    private function buildContextText(array $context, AITenantProfile $profile): string
    {
        if ($context['type'] === 'no_tenant') {
            return '🏢 NO TENANT: Genel sistem modu - şirket bilgisi yok.';
        }

        if ($context['type'] === 'incomplete_profile') {
            return '🏢 INCOMPLETE PROFILE: Şirket profili tamamlanmamış - genel yaklaşım kullan.';
        }

        $parts = [];
        
        // Şirket kimliği
        if (isset($context['company']['name'])) {
            $parts[] = "🤖 AI IDENTITY: Sen {$context['company']['name']} şirketinin yapay zeka modelisin.";
        }

        // Ana hizmet
        if (isset($context['company']['main_service'])) {
            $parts[] = "🎯 MAIN SERVICE: {$context['company']['main_service']}";
        }

        // Şirket kurucusu 
        if (isset($context['company']['founder'])) {
            $parts[] = "👨‍💼 COMPANY FOUNDER: {$context['company']['founder']}";
        }

        // Şirket CEO'su
        if (isset($context['company']['ceo'])) {
            $parts[] = "🏢 COMPANY CEO: {$context['company']['ceo']}";
        }

        // Marka kişiliği
        if (isset($context['sector']['brand_personality']) && is_array($context['sector']['brand_personality'])) {
            $personalities = array_keys(array_filter($context['sector']['brand_personality']));
            if (!empty($personalities)) {
                $parts[] = "🎨 BRAND PERSONALITY: " . implode(', ', $personalities);
            }
        }

        // Yazı tonu
        if (isset($context['behavior']['writing_tone']) && is_array($context['behavior']['writing_tone'])) {
            $tones = array_keys(array_filter($context['behavior']['writing_tone']));
            if (!empty($tones)) {
                $parts[] = "✍️ WRITING TONE: " . implode(', ', $tones);
            }
        }

        // İletişim tarzı
        if (isset($context['behavior']['communication_style']) && is_array($context['behavior']['communication_style'])) {
            $styles = array_keys(array_filter($context['behavior']['communication_style']));
            if (!empty($styles)) {
                $parts[] = "💬 COMMUNICATION: " . implode(', ', $styles);
            }
        }

        // Özel talimatlar
        $parts[] = "🎯 CONTEXT GUIDELINE: Bu şirket bağlamında profesyonel ve markayla uyumlu yaklaşım benimse.";
        
        // Kaçınılacak konular
        if (isset($context['behavior']['avoid_topics']) && is_array($context['behavior']['avoid_topics'])) {
            $avoidTopics = array_keys(array_filter($context['behavior']['avoid_topics']));
            if (!empty($avoidTopics)) {
                $parts[] = "⚠️ AVOID TOPICS: " . implode(', ', $avoidTopics);
            }
        }

        // Optimized context ekle (eğer varsa)
        if (isset($context['optimized_context']) && !empty($context['optimized_context'])) {
            $parts[] = "\n--- DETAYLAR ---";
            $parts[] = $context['optimized_context'];
        }

        return implode("\n", $parts);
    }

    /**
     * Context priority hesaplama (override)
     */
    protected function calculatePriority(array $context): int
    {
        switch ($context['type']) {
            case 'complete_profile':
                return 1; // Highest priority
                
            case 'incomplete_profile':
                return 3; // Medium priority
                
            case 'no_tenant':
            default:
                return 5; // Lowest priority
        }
    }

    /**
     * Context validation (override)
     */
    protected function validateContext(array $context): bool
    {
        return isset($context['type']) && 
               in_array($context['type'], ['no_tenant', 'incomplete_profile', 'complete_profile']);
    }

    /**
     * Tenant profile'ın güncellendiği durumlarda cache temizle
     */
    public function handleProfileUpdate(int $tenantId): void
    {
        // Tüm cache'leri temizle
        $this->clearAllCacheForTenant($tenantId);
        
        Log::info('Tenant context cache cleared due to profile update', [
            'tenant_id' => $tenantId
        ]);
    }

    /**
     * Belirli tenant için tüm cache'leri temizle
     */
    private function clearAllCacheForTenant(int $tenantId): void
    {
        // Pattern-based cache clearing
        try {
            $pattern = "context_tenant_{$tenantId}_*";
            $keys = \Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                \Cache::getRedis()->del($keys);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear tenant context cache pattern', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Şirket profilinin tamamlanma durumunu kontrol et
     */
    public function checkProfileCompleteness(int $tenantId): array
    {
        $profile = AITenantProfile::where('tenant_id', $tenantId)->first();
        
        if (!$profile) {
            return [
                'status' => 'no_profile',
                'completeness' => 0,
                'missing_sections' => ['all']
            ];
        }

        $completionData = $profile->getCompletionPercentage();
        
        return [
            'status' => $profile->is_completed ? 'complete' : 'incomplete',
            'completeness' => $completionData['percentage'],
            'completed_fields' => $completionData['completed'],
            'total_fields' => $completionData['total'],
            'sections' => $completionData['sections']
        ];
    }

    /**
     * Context mode'a göre farklı detay seviyeleri
     */
    public function collectWithMode(string $mode = 'normal'): array
    {
        $options = ['mode' => $mode];
        
        // Mode'a göre cache TTL'i ayarla
        switch ($mode) {
            case 'minimal':
                $this->cacheTtl = 7200; // 2 saat (minimal değişir)
                break;
                
            case 'detailed':
                $this->cacheTtl = 1800; // 30 dakika (detaylı daha sık günceller)
                break;
                
            case 'normal':
            default:
                $this->cacheTtl = 3600; // 1 saat
                break;
        }
        
        return $this->collect($options);
    }
}