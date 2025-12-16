<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Modules\AI\App\Services\TenantServiceFactory;
use Illuminate\Support\Facades\Log;

/**
 * Music Search Service
 *
 * Müzik platformu için AI arama servisi.
 * Muzibu (Tenant 1001) için kullanılacak.
 *
 * TenantServiceFactory pattern ile Tenant1001ProductSearchService'i kullanır.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class MusicSearchService implements ModuleSearchInterface
{
    /**
     * @inheritDoc
     */
    public function search(string $query, array $filters = [], int $limit = 50): array
    {
        // ✅ DEBUG: Tenant context'i baştan kaydet
        $tenantId = tenant() ? tenant()->id : 'NULL';
        $tenantCentral = tenant() && isset(tenant()->central) ? tenant()->central : 'N/A';

        Log::info('🔍 MusicSearchService::search() START', [
            'query' => $query,
            'limit' => $limit,
            'tenant_id' => $tenantId,
            'tenant_central' => $tenantCentral,
        ]);

        // TenantServiceFactory ile tenant-specific service al
        $productSearchService = TenantServiceFactory::getProductSearchService();

        if (!$productSearchService) {
            Log::warning('❌ MusicSearchService: No tenant product search service found');
            return [
                'success' => false,
                'items' => [],
                'total' => 0,
                'module_type' => 'music',
            ];
        }

        Log::info('✅ ProductSearchService found', [
            'class' => get_class($productSearchService),
        ]);

        // Tenant1001ProductSearchService->search() çağır
        $searchResults = $productSearchService->search($query, $limit);

        Log::info('🎵 MusicSearchService::search() RESULTS', [
            'query' => $query,
            'songs_count' => count($searchResults['songs'] ?? []),
            'albums_count' => count($searchResults['albums'] ?? []),
            'playlists_count' => count($searchResults['playlists'] ?? []),
            'total_found' => $searchResults['total_found'] ?? 0,
            'result_keys' => array_keys($searchResults),
        ]);

        // ✅ Format uyumluluğu: searchResults'ı direkt dön (songs, albums, playlists hepsi içinde)
        return [
            'success' => true,
            'items' => $searchResults['songs'] ?? [],
            'total' => $searchResults['total_found'] ?? 0,
            'showing' => $searchResults['showing'] ?? 0,
            'module_type' => 'music',
            'metadata' => [
                'detected_category' => $searchResults['detected_category'] ?? null,
                'detected_mood' => $searchResults['detected_mood'] ?? null,
                'detected_genre' => $searchResults['detected_genre'] ?? null,
            ],
            // ✅ ÇÖZÜM: RAW searchResults'ı da ekle (buildContextForAI için)
            'raw_results' => $searchResults,
        ];
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        // ✅ DEBUG: Her şeyi logla
        \Log::info('🔍 MusicSearchService::buildContextForAI() CALLED', [
            'results_keys' => array_keys($results),
            'items_count' => count($results['items'] ?? []),
            'items_empty' => empty($results['items']),
        ]);

        if (empty($results['items'])) {
            \Log::warning('❌ buildContextForAI: items is empty!');
            return '';
        }

        // TenantServiceFactory ile service al
        $productSearchService = TenantServiceFactory::getProductSearchService();

        if (!$productSearchService) {
            \Log::warning('❌ buildContextForAI: productSearchService NOT FOUND!');
            return '';
        }

        // ✅ ÇÖZÜM: raw_results kullan (songs, albums, playlists içeren tam format)
        $rawResults = $results['raw_results'] ?? [];

        \Log::info('📝 Raw results check', [
            'has_raw_results' => !empty($rawResults),
            'raw_results_keys' => array_keys($rawResults),
            'songs_in_raw' => count($rawResults['songs'] ?? []),
        ]);

        if (empty($rawResults)) {
            \Log::warning('❌ buildContextForAI: raw_results is empty!', ['results_keys' => array_keys($results)]);
            return '';
        }

        // Tenant1001ProductSearchService->buildContextForAI() çağır (doğru format ile)
        $context = $productSearchService->buildContextForAI($rawResults);

        \Log::info('✅ Context built', [
            'context_length' => strlen($context),
            'context_preview' => substr($context, 0, 200),
        ]);

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'Şarkı Ara',
                'message' => 'Şarkı aramak istiyorum',
                'icon' => 'fas fa-search',
                'color' => 'blue',
            ],
            [
                'label' => 'Playlist',
                'message' => 'Playlist önerir misiniz?',
                'icon' => 'fas fa-list-music',
                'color' => 'purple',
            ],
            [
                'label' => 'Sanatçılar',
                'message' => 'Popüler sanatçılar kimler?',
                'icon' => 'fas fa-microphone',
                'color' => 'orange',
            ],
            [
                'label' => 'Yeni Çıkanlar',
                'message' => 'Bu hafta çıkan şarkılar neler?',
                'icon' => 'fas fa-star',
                'color' => 'green',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        $lowerMessage = mb_strtolower($message);

        // Tür tespiti
        $genres = ['pop', 'rock', 'jazz', 'klasik', 'hip-hop', 'elektronik'];
        foreach ($genres as $genre) {
            if (str_contains($lowerMessage, $genre)) {
                return ['genre' => $genre];
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        return "
## MÜZİK ASSISTANT KURALLARI

1. **Şarkı Önerme:**
   - Kullanıcı zevkine göre öner
   - Tür/mood bazlı filtreleme yap
   - Sanatçı bilgisi ver

2. **Playlist:**
   - Tema bazlı playlist oluştur
   - Süre belirt
   - Çeşitlilik sağla

3. **Ton:**
   - Eğlenceli ve samimi ol
   - Müzik terminolojisi kullan
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'music';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'Müzik Asistanı';
    }

    /**
     * Post-process AI response (delegate to tenant service)
     *
     * @param string $aiResponse
     * @param string $userMessage
     * @return string
     */
    public function postProcessResponse(string $aiResponse, string $userMessage): string
    {
        // TenantServiceFactory ile service al
        $productSearchService = TenantServiceFactory::getProductSearchService();

        if (!$productSearchService) {
            Log::warning('❌ postProcessResponse: productSearchService NOT FOUND!');
            return $aiResponse;
        }

        // Tenant service'te postProcessResponse var mı kontrol et
        if (!method_exists($productSearchService, 'postProcessResponse')) {
            Log::info('ℹ️ Tenant service has no postProcessResponse method, skipping');
            return $aiResponse;
        }

        // Delegate to tenant-specific service (Tenant1001ProductSearchService)
        Log::info('🎯 Delegating postProcessResponse to tenant service', [
            'class' => get_class($productSearchService),
        ]);

        return $productSearchService->postProcessResponse($aiResponse, $userMessage);
    }
}
