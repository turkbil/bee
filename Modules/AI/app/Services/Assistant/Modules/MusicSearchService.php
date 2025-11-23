<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Illuminate\Support\Facades\Log;

/**
 * Music Search Service
 *
 * Müzik platformu için AI arama servisi.
 * Muzibu (Tenant 1001) için kullanılacak.
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
        // TODO: Muzibu modülü ile entegre edilecek
        return [
            'success' => true,
            'items' => [],
            'total' => 0,
            'module_type' => 'music',
        ];
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        if (empty($results['items'])) {
            return '';
        }

        $context = "## 🎵 Müzik Sonuçları\n\n";
        // TODO: Müzik formatı eklenecek
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
}
