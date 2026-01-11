<?php

namespace Modules\AI\App\Services\Assistant\Modules;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use App\Models\AITenantDirective;
use Illuminate\Support\Facades\Log;

/**
 * Generic Search Service
 *
 * Fallback servis - hiçbir modül tanımlı değilse kullanılır.
 * Sadece knowledge base ve genel bilgi verir.
 *
 * @package Modules\AI\App\Services\Assistant\Modules
 */
class GenericSearchService implements ModuleSearchInterface
{
    /**
     * @inheritDoc
     */
    public function search(string $query, array $filters = [], int $limit = 50): array
    {
        // Generic servis arama yapmaz, sadece bilgi verir
        return [
            'success' => true,
            'items' => [],
            'total' => 0,
            'module_type' => 'generic',
            'message' => 'Bu tenant için özel arama servisi tanımlı değil.',
        ];
    }

    /**
     * @inheritDoc
     */
    public function buildContextForAI(array $results): string
    {
        // Generic context - firma bilgileri
        $context = "## ℹ️ Genel Bilgiler\n\n";

        try {
            if (function_exists('settings')) {
                $siteName = settings('site_title') ?? 'Firmamız';
                $phone = settings('contact_phone_1');
                $email = settings('contact_email_1');
                $address = settings('contact_address_line_1');

                $context .= "**Firma:** {$siteName}\n";

                if ($phone) {
                    $context .= "**Telefon:** {$phone}\n";
                }
                if ($email) {
                    $context .= "**E-posta:** {$email}\n";
                }
                if ($address) {
                    $context .= "**Adres:** {$address}\n";
                }
            }
        } catch (\Exception $e) {
            Log::warning('GenericSearchService: Could not load settings');
        }

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'Hakkımızda',
                'message' => 'Firmanız hakkında bilgi alabilir miyim?',
                'icon' => 'fas fa-building',
                'color' => 'blue',
            ],
            [
                'label' => 'İletişim',
                'message' => 'İletişim bilgilerinizi öğrenebilir miyim?',
                'icon' => 'fas fa-phone',
                'color' => 'green',
            ],
            [
                'label' => 'Hizmetler',
                'message' => 'Hangi hizmetleri sunuyorsunuz?',
                'icon' => 'fas fa-concierge-bell',
                'color' => 'purple',
            ],
            [
                'label' => 'SSS',
                'message' => 'Sık sorulan sorular nelerdir?',
                'icon' => 'fas fa-question-circle',
                'color' => 'orange',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function detectFilters(string $message): ?array
    {
        // Generic servis filtre tespit etmez
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPromptRules(): string
    {
        return "
## GENEL ASSISTANT KURALLARI

1. **Bilgi Verme:**
   - Sadece firma hakkında genel bilgi ver
   - İletişim bilgilerini paylaş
   - Soruları yanıtla

2. **Sınırlar:**
   - Ürün/hizmet detayı verme (tanımlı modül yok)
   - Fiyat bilgisi verme
   - Sipariş/randevu alma

3. **Yönlendirme:**
   - Detaylı bilgi için iletişim öner
   - Web sitesine yönlendir
";
    }

    /**
     * @inheritDoc
     */
    public function getModuleType(): string
    {
        return 'generic';
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return 'Genel Asistan';
    }
}
