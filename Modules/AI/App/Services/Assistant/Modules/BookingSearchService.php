<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use Illuminate\Support\Facades\Log;

/**
 * Booking Search Service
 *
 * Randevu/Rezervasyon için AI arama servisi.
 * İleride Booking modülü eklendiğinde doldurulacak.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class BookingSearchService implements ModuleSearchInterface
{
    /**
     * @inheritDoc
     */
    public function search(string $query, array $filters = [], int $limit = 50): array
    {
        // TODO: Booking modülü eklendiğinde implement edilecek
        return [
            'success' => true,
            'items' => [],
            'total' => 0,
            'module_type' => 'booking',
            'message' => 'Randevu sistemi henüz aktif değil',
        ];
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        return "## 📅 Randevu Bilgileri\n\nRandevu sistemi aktif olduğunda müsait zamanlar burada gösterilecek.\n";
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'Randevu Al',
                'message' => 'Randevu almak istiyorum',
                'icon' => 'fas fa-calendar-plus',
                'color' => 'blue',
            ],
            [
                'label' => 'Müsait Saatler',
                'message' => 'Müsait saatleriniz nelerdir?',
                'icon' => 'fas fa-clock',
                'color' => 'green',
            ],
            [
                'label' => 'İptal/Değişiklik',
                'message' => 'Randevumu iptal etmek veya değiştirmek istiyorum',
                'icon' => 'fas fa-edit',
                'color' => 'orange',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        return "
## RANDEVU ASSISTANT KURALLARI

1. **Randevu İşlemleri:**
   - Müsait zamanları göster
   - Randevu onayı al
   - Hatırlatma yap

2. **Bilgi Toplama:**
   - Ad soyad
   - Telefon
   - Tercih edilen tarih/saat
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'booking';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'Randevu Asistanı';
    }
}
