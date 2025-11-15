<?php

namespace App\Services\AI;

/**
 * Tenant Blog Prompt Enhancer
 *
 * Dinamik tenant-aware blog prompt enhancement servisi.
 * Her tenant için özel enhancement dosyası oluşturulabilir.
 * Dosya yoksa generic prompt kullanılır.
 *
 * Yapı:
 * - app/Services/AI/Tenants/Tenant2BlogEnhancement.php (ixtif.com)
 * - app/Services/AI/Tenants/Tenant3BlogEnhancement.php (diğer tenant)
 *
 * Kullanım:
 * $enhancement = app(TenantBlogPromptEnhancer::class)->getEnhancement();
 * if (!empty($enhancement)) {
 *     // Tenant-specific prompt kullan
 * } else {
 *     // Generic prompt kullan
 * }
 */
class TenantBlogPromptEnhancer
{
    /**
     * Tenant-specific enhancement al (dinamik)
     *
     * @param int|null $tenantId Tenant ID (null ise aktif tenant kullanılır)
     * @return array Enhancement data veya boş array
     */
    public function getEnhancement(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? tenant('id');

        if (!$tenantId) {
            return []; // Tenant context yok, generic kullan
        }

        // Tenant-specific class yolu
        $className = "App\\Services\\AI\\Tenants\\Tenant{$tenantId}BlogEnhancement";

        // Class var mı kontrol et
        if (class_exists($className)) {
            try {
                // Varsa tenant-specific enhancement kullan
                return app($className)->getEnhancement();
            } catch (\Exception $e) {
                // Hata olursa log'la ve generic kullan
                \Log::warning("Tenant{$tenantId}BlogEnhancement error", [
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        }

        // Yoksa generic (boş array, base prompt kullanılır)
        return [];
    }

    /**
     * Tenant enhancement var mı kontrol et
     *
     * @param int|null $tenantId Tenant ID
     * @return bool
     */
    public function hasTenantEnhancement(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? tenant('id');

        if (!$tenantId) {
            return false;
        }

        $className = "App\\Services\\AI\\Tenants\\Tenant{$tenantId}BlogEnhancement";
        return class_exists($className);
    }

    /**
     * Enhancement'ı formatted prompt string'e çevir
     *
     * @param array $enhancement Enhancement data
     * @return string Formatted prompt
     */
    public function buildPromptContext(array $enhancement): string
    {
        if (empty($enhancement)) {
            return '';
        }

        $context = "\n\n🎯 TENANT-SPECIFIC CONTEXT:\n\n";

        // Sektör & Firma
        if (!empty($enhancement['sector']) || !empty($enhancement['company_name'])) {
            $context .= "SEKTÖR & FİRMA:\n";
            if (!empty($enhancement['sector'])) {
                $context .= "- Sektör: {$enhancement['sector']}\n";
            }
            if (!empty($enhancement['company_name'])) {
                $context .= "- Firma: {$enhancement['company_name']}\n";
            }
            if (!empty($enhancement['expertise'])) {
                $context .= "- Uzmanlık: {$enhancement['expertise']}\n";
            }
            $context .= "\n";
        }

        // İçerik Kaynakları
        if (!empty($enhancement['content_sources'])) {
            $context .= "İÇERİK KAYNAKLARI:\n";
            foreach ($enhancement['content_sources'] as $source => $enabled) {
                if ($enabled) {
                    $label = match($source) {
                        'shop_categories' => 'Shop kategorileri (ürün grupları)',
                        'shop_products' => 'Shop ürünleri (spesifik modeller)',
                        'blog_categories' => 'Blog kategorileri',
                        default => ucfirst(str_replace('_', ' ', $source))
                    };
                    $context .= "- {$label}\n";
                }
            }
            $context .= "\n";
        }

        // Hizmetler
        if (!empty($enhancement['services'])) {
            $context .= "HİZMETLERİMİZ (Blog'da bahsedilebilir):\n";
            foreach ($enhancement['services'] as $service => $description) {
                $context .= "- {$description}\n";
            }
            $context .= "\n";
        }

        // İçerik Odağı
        if (!empty($enhancement['content_focus'])) {
            $context .= "İÇERİK ODAĞI:\n";
            foreach ($enhancement['content_focus'] as $focus) {
                $context .= "- {$focus}\n";
            }
            $context .= "\n";
        }

        // Hedef Kitle
        if (!empty($enhancement['target_audience'])) {
            $context .= "HEDEF KİTLE:\n";
            $context .= "- Profil: {$enhancement['target_audience']}\n";
            if (!empty($enhancement['target_industries'])) {
                $context .= "- Sektörler: {$enhancement['target_industries']}\n";
            }
            $context .= "\n";
        }

        // Ton & Stil
        if (!empty($enhancement['tone'])) {
            $context .= "TON & STİL:\n";
            $context .= "- Ton: {$enhancement['tone']}\n";
            if (!empty($enhancement['writing_style'])) {
                foreach ($enhancement['writing_style'] as $style) {
                    $context .= "- {$style}\n";
                }
            }
            $context .= "\n";
        }

        // Referans Kaynakları
        if (!empty($enhancement['reference_sources'])) {
            $context .= "REFERANS KAYNAKLARI:\n";
            foreach ($enhancement['reference_sources'] as $source) {
                $context .= "- {$source}\n";
            }
            $context .= "\n";
        }

        // Blog Konu Önerileri
        if (!empty($enhancement['blog_topic_ideas'])) {
            $context .= "BLOG KONU ÖRNEKLERİ:\n";
            foreach ($enhancement['blog_topic_ideas'] as $category => $description) {
                $context .= "- {$description}\n";
            }
            $context .= "\n";
        }

        return $context;
    }
}
