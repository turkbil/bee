<?php

namespace App\Services;

use App\Contracts\TranslatableEntity;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\FastHtmlTranslationService;
use Modules\LanguageManagement\app\Models\TenantLanguage;

/**
 * 🚀 UNIVERSAL TRANSLATION SERVICE
 * 
 * Tüm modüller için ortak çeviri servisi.
 * Page modülü pattern'i temel alınarak tasarlandı.
 * 
 * KULLANIM:
 * $service = app(UniversalTranslationService::class);
 * $result = $service->translateEntity('Page', 1, 'tr', 'en');
 */
class UniversalTranslationService
{
    protected $fastHtmlService;

    public function __construct(FastHtmlTranslationService $fastHtmlService)
    {
        $this->fastHtmlService = $fastHtmlService;
    }

    /**
     * Herhangi bir modülü çevir
     * 
     * @param string $moduleName Modül adı (Page, Portfolio, Blog vb.)
     * @param int $entityId Entity ID'si
     * @param string $sourceLanguage Kaynak dil
     * @param string $targetLanguage Hedef dil
     * @return array Sonuç ['success' => bool, 'data' => array, 'error' => string]
     */
    public function translateEntity(
        string $moduleName, 
        int $entityId, 
        string $sourceLanguage, 
        string $targetLanguage
    ): array {
        try {
            Log::info("🌍 Universal Translation başlatıldı", [
                'module' => $moduleName,
                'entity_id' => $entityId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage
            ]);

            // 🌐 Dynamic language validation
            if (!$this->isValidTenantLanguage($sourceLanguage)) {
                throw new \Exception("Geçersiz kaynak dil: {$sourceLanguage}. Bu dil tenant'ta aktif değil.");
            }

            if (!$this->isValidTenantLanguage($targetLanguage)) {
                throw new \Exception("Geçersiz hedef dil: {$targetLanguage}. Bu dil tenant'ta aktif değil.");
            }

            // 1. Modül class'ını bul
            $modelClass = $this->findModelClass($moduleName);
            if (!$modelClass) {
                throw new \Exception("Model bulunamadı: {$moduleName}");
            }

            // 2. Entity'yi yükle
            $entity = $modelClass::find($entityId);
            if (!$entity) {
                throw new \Exception("Entity bulunamadı: {$moduleName}#{$entityId}");
            }

            // 3. TranslatableEntity interface kontrolü
            if (!$entity instanceof TranslatableEntity) {
                throw new \Exception("{$moduleName} modülü TranslatableEntity interface'ini implement etmiyor");
            }

            // 4. Çevrilebilir alanları al
            $translatableFields = $entity->getTranslatableFields();
            
            // 5. Her alanı çevir
            $translatedData = [];
            foreach ($translatableFields as $fieldName => $fieldType) {
                $sourceValue = $this->getSourceValue($entity, $fieldName, $sourceLanguage);
                
                if (empty($sourceValue)) {
                    continue;
                }

                $translatedValue = $this->translateField($sourceValue, $fieldType, $sourceLanguage, $targetLanguage);
                if ($translatedValue) {
                    $translatedData[$fieldName] = $translatedValue;
                }
            }

            // 6. Slug otomatik oluştur (eğer gerekiyorsa)
            $translatedData = $this->handleAutoSlug($translatedData, $translatableFields);

            // 7. Çeviriyi kaydet
            $this->saveTranslation($entity, $translatedData, $targetLanguage);

            // 8. SEO çevirisi (eğer gerekiyorsa)
            if ($entity->hasSeoSettings()) {
                $this->translateSeoSettings($entity, $sourceLanguage, $targetLanguage);
            }

            // 9. After translation hook
            $entity->afterTranslation($targetLanguage, $translatedData);

            Log::info("✅ Universal Translation tamamlandı", [
                'module' => $moduleName,
                'entity_id' => $entityId,
                'translated_fields' => array_keys($translatedData)
            ]);

            return [
                'success' => true,
                'data' => $translatedData,
                'error' => null
            ];

        } catch (\Exception $e) {
            Log::error("❌ Universal Translation hatası", [
                'module' => $moduleName,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Modül class'ını dinamik olarak bul
     */
    private function findModelClass(string $moduleName): ?string
    {
        $possiblePaths = [
            "Modules\\{$moduleName}\\App\\Models\\{$moduleName}",
            "Modules\\{$moduleName}\\app\\Models\\{$moduleName}",
            "App\\Models\\{$moduleName}"
        ];

        foreach ($possiblePaths as $className) {
            if (class_exists($className)) {
                return $className;
            }
        }

        return null;
    }

    /**
     * Entity'den kaynak dil değerini al
     */
    private function getSourceValue($entity, string $fieldName, string $sourceLanguage): ?string
    {
        // HasTranslations trait'inin getTranslated metodunu kullan
        if (method_exists($entity, 'getTranslated')) {
            return $entity->getTranslated($fieldName, $sourceLanguage);
        }

        // Fallback: Direkt attribute'den al
        $value = $entity->getAttribute($fieldName);
        if (is_array($value)) {
            return $value[$sourceLanguage] ?? null;
        }

        return $value;
    }

    /**
     * Alanı türüne göre çevir
     */
    private function translateField(
        string $sourceValue, 
        string $fieldType, 
        string $sourceLanguage, 
        string $targetLanguage
    ): ?string {
        switch ($fieldType) {
            case 'html':
                return $this->fastHtmlService->translateHtmlContentFast(
                    $sourceValue, 
                    $sourceLanguage, 
                    $targetLanguage, 
                    'Universal entity content translation'
                );

            case 'text':
            default:
                // TODO: Implement text translation
                // Şimdilik FastHtml kullanıyoruz, sonra ayrı text translator ekleyebiliriz
                return $this->fastHtmlService->translateHtmlContentFast(
                    $sourceValue, 
                    $sourceLanguage, 
                    $targetLanguage, 
                    'Universal entity text translation'
                );
        }
    }

    /**
     * Auto slug işle
     */
    private function handleAutoSlug(array $translatedData, array $translatableFields): array
    {
        foreach ($translatableFields as $fieldName => $fieldType) {
            if ($fieldType === 'auto' && $fieldName === 'slug') {
                // Title'dan slug oluştur
                if (isset($translatedData['title'])) {
                    $translatedData['slug'] = \Str::slug($translatedData['title']);
                }
            }
        }

        return $translatedData;
    }

    /**
     * Çeviriyi kaydet
     */
    private function saveTranslation($entity, array $translatedData, string $targetLanguage): void
    {
        $updateData = [];

        foreach ($translatedData as $fieldName => $translatedValue) {
            // Mevcut değeri al
            $currentValue = $entity->getAttribute($fieldName) ?? [];
            
            // JSON decode eğer string ise
            if (is_string($currentValue)) {
                $currentValue = json_decode($currentValue, true) ?? [];
            }

            // Yeni çeviriyi ekle
            $currentValue[$targetLanguage] = $translatedValue;
            $updateData[$fieldName] = $currentValue;
        }

        // Kaydet
        $entity->update($updateData);
    }

    /**
     * SEO ayarlarını çevir
     */
    private function translateSeoSettings($entity, string $sourceLanguage, string $targetLanguage): void
    {
        if (!method_exists($entity, 'seoSetting') || !$entity->seoSetting) {
            return;
        }

        // TODO: SEO çevirisi implement edilecek
        // Page modülündeki SEO translation logic'ini kullan
        Log::info("SEO çevirisi henüz implement edilmedi", [
            'entity' => get_class($entity),
            'entity_id' => $entity->{$entity->getPrimaryKeyName()}
        ]);
    }

    /**
     * 🌐 Dynamic tenant language validation
     * Tenant'ın aktif dillerini kontrol eder
     */
    private function isValidTenantLanguage(string $languageCode): bool
    {
        try {
            return TenantLanguage::where('code', $languageCode)
                ->where('is_active', true)
                ->exists();
        } catch (\Exception $e) {
            Log::warning("Dil validation hatası", [
                'language_code' => $languageCode,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 🌐 Get all active tenant languages
     * Admin panel için aktif dillerin listesini döner
     */
    public function getActiveTenantLanguages(): array
    {
        try {
            return TenantLanguage::active()
                ->visible()
                ->orderBy('sort_order')
                ->get(['code', 'name', 'native_name', 'is_default', 'flag_emoji'])
                ->toArray();
        } catch (\Exception $e) {
            Log::error("Aktif diller alınamadı", ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * 🌐 Get tenant default language
     * Tenant'ın varsayılan dilini döner
     */
    public function getTenantDefaultLanguage(): ?string
    {
        try {
            $defaultLang = TenantLanguage::where('is_default', true)
                ->where('is_active', true)
                ->first();
            
            return $defaultLang ? $defaultLang->code : null;
        } catch (\Exception $e) {
            Log::error("Varsayılan dil alınamadı", ['error' => $e->getMessage()]);
            return null;
        }
    }
}