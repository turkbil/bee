<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Services\TranslationModuleRegistry;

/**
 * Universal Translation Trait
 * 
 * Her Livewire component'ine eklenebilir
 * Otomatik entity detection ve translation support
 */
trait HasUniversalTranslation
{
    /**
     * Queue'ya çeviri job'ı gönder
     */
    public function queueTranslation($entityId, $sourceLanguage, $targetLanguages)
    {
        try {
            $entityType = $this->detectEntityType();
            $sessionId = Str::uuid();
            
            // Registry'den entity config'ini kontrol et
            $entityConfig = TranslationModuleRegistry::getEntityConfig($entityType);
            
            if (!$entityConfig) {
                $this->dispatch('translationError', [
                    'message' => "Entity type '{$entityType}' is not supported for translation"
                ]);
                return;
            }
            
            // Universal Translation Job dispatch et
            \Modules\AI\App\Jobs\UniversalTranslationJob::dispatch(
                $entityType,
                $entityId,
                $sourceLanguage,
                $targetLanguages,
                $sessionId,
                $entityConfig
            );
            
            $this->dispatch('translationQueued', ['sessionId' => $sessionId]);
            
            \Log::info('🚀 Universal translation queued', [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'session_id' => $sessionId,
                'component' => class_basename($this)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Universal translation queue failed', [
                'error' => $e->getMessage(),
                'component' => class_basename($this),
                'entity_id' => $entityId
            ]);
            
            $this->dispatch('translationError', [
                'message' => 'Translation could not be queued: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Modal'dan çeviri başlat
     * JavaScript'ten gelen array formatını destekler
     */
    public function translateFromModal(array $data): array
    {
        try {
            $entityId = $data['entityId'] ?? null;
            $sourceLanguage = $data['sourceLanguage'] ?? 'tr';
            $targetLanguages = $data['targetLanguages'] ?? [];

            if (!$entityId) {
                throw new \Exception('Entity ID bulunamadı');
            }

            \Log::info('🌍 Universal Translation Modal çağrısı', [
                'entity_type' => $this->detectEntityType(),
                'entity_id' => $entityId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages
            ]);

            // Queue translation
            $this->queueTranslation($entityId, $sourceLanguage, $targetLanguages);

            return [
                'success' => true,
                'message' => 'Çeviri kuyruğa başarıyla eklendi'
            ];
        } catch (\Exception $e) {
            \Log::error('❌ Universal Translation Modal hatası', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Component class isminden entity type'ı algıla
     */
    private function detectEntityType(): string
    {
        $className = class_basename($this);
        
        // Component suffix'lerini kaldır
        $entityName = str_replace(['Component', 'Manage', 'Admin', 'Livewire'], '', $className);
        
        // Snake case'e çevir
        return strtolower(Str::snake($entityName));
    }
    
    /**
     * Entity'nin çeviri destekli olup olmadığını kontrol et
     */
    public function isTranslationSupported(): bool
    {
        try {
            $entityType = $this->detectEntityType();
            $entityConfig = TranslationModuleRegistry::getEntityConfig($entityType);
            
            return $entityConfig !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Entity'nin çevrilebilir alanlarını getir
     */
    public function getTranslatableFields(): array
    {
        try {
            $entityType = $this->detectEntityType();
            $entityConfig = TranslationModuleRegistry::getEntityConfig($entityType);
            
            return $entityConfig['fields'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Entity'nin SEO destekli olup olmadığını kontrol et
     */
    public function isSeoSupported(): bool
    {
        try {
            $entityType = $this->detectEntityType();
            $entityConfig = TranslationModuleRegistry::getEntityConfig($entityType);
            
            return $entityConfig['seo_enabled'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}