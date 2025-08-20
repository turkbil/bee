<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Schema Registry - Dinamik Model Tanıma Sistemi
 * 
 * Tüm modüllerdeki modelleri otomatik tarar ve schema pattern'lerini belirler
 * Yeni modüller eklendiğinde otomatik algılar
 */
class SchemaRegistryService
{
    private const CACHE_KEY = 'schema_registry_models';
    private const CACHE_TTL = 3600; // 1 saat

    /**
     * Tüm modüllerden modelleri otomatik keşfet
     */
    public function discoverModels(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $models = [];
            
            // Modules dizinindeki tüm modülleri tara
            $modulesPath = base_path('Modules');
            
            if (!File::exists($modulesPath)) {
                return [];
            }
            
            $moduleDirs = File::directories($modulesPath);
            
            foreach ($moduleDirs as $moduleDir) {
                $moduleName = basename($moduleDir);
                $moduleModels = $this->scanModuleModels($moduleName);
                
                if (!empty($moduleModels)) {
                    $models[$moduleName] = $moduleModels;
                }
            }
            
            return $models;
        });
    }

    /**
     * Tek bir modülün modellerini tara
     */
    private function scanModuleModels(string $moduleName): array
    {
        $models = [];
        $modelPaths = [
            base_path("Modules/{$moduleName}/app/Models"),
            base_path("Modules/{$moduleName}/App/Models"), // Büyük A için
        ];

        foreach ($modelPaths as $modelsPath) {
            if (!File::exists($modelsPath)) {
                continue;
            }

            $modelFiles = File::files($modelsPath);
            
            foreach ($modelFiles as $file) {
                if ($file->getExtension() === 'php') {
                    $className = $file->getFilenameWithoutExtension();
                    $fullClassName = "Modules\\{$moduleName}\\App\\Models\\{$className}";
                    
                    // Alternatif namespace (küçük app)
                    if (!class_exists($fullClassName)) {
                        $fullClassName = "Modules\\{$moduleName}\\app\\Models\\{$className}";
                    }
                    
                    if (class_exists($fullClassName)) {
                        $modelInfo = $this->analyzeModel($fullClassName);
                        if ($modelInfo) {
                            $models[$className] = $modelInfo;
                        }
                    }
                }
            }
        }

        return $models;
    }

    /**
     * Model'i analiz et ve schema pattern'ini belirle
     */
    private function analyzeModel(string $className): ?array
    {
        try {
            if (!is_subclass_of($className, Model::class)) {
                return null;
            }

            $reflection = new \ReflectionClass($className);
            $instance = new $className;
            
            // Fillable alanlarını al
            $fillable = $instance->getFillable();
            
            // Schema pattern'ini belirle
            $schemaType = $this->detectSchemaType($className, $fillable);
            
            return [
                'class' => $className,
                'table' => $instance->getTable(),
                'fillable' => $fillable,
                'schema_type' => $schemaType,
                'has_translations' => $this->hasTranslations($instance),
                'has_seo' => $this->hasSeo($instance),
                'confidence' => $this->calculateConfidence($fillable, $schemaType),
                'detected_fields' => $this->detectImportantFields($fillable)
            ];
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Model'den schema tipini akıllıca belirle
     */
    private function detectSchemaType(string $className, array $fillable): string
    {
        $lowerClassName = strtolower(class_basename($className));
        $fieldsString = implode(' ', $fillable);
        $lowerFields = strtolower($fieldsString);
        
        // İçerik tabanlı pattern'ler
        if ($this->hasFields($fillable, ['title', 'content']) || 
            $this->hasFields($fillable, ['title', 'body'])) {
            
            if (Str::contains($lowerClassName, ['news', 'announcement', 'blog'])) {
                return 'NewsArticle';
            }
            if (Str::contains($lowerClassName, ['page', 'content', 'post'])) {
                return 'Article';
            }
            if (Str::contains($lowerClassName, ['event'])) {
                return 'Event';
            }
        }
        
        // E-ticaret pattern'leri
        if (Str::contains($lowerClassName, ['product', 'item']) ||
            $this->hasFields($fillable, ['price', 'sku'])) {
            return 'Product';
        }
        
        // Kişi/Organizasyon pattern'leri
        if (Str::contains($lowerClassName, ['person', 'author', 'user']) ||
            $this->hasFields($fillable, ['name', 'email'])) {
            return 'Person';
        }
        
        if (Str::contains($lowerClassName, ['company', 'organization'])) {
            return 'Organization';
        }
        
        // Yaratıcı işler
        if (Str::contains($lowerClassName, ['portfolio', 'gallery', 'artwork'])) {
            return 'CreativeWork';
        }
        
        // Yer/Mekan
        if (Str::contains($lowerClassName, ['place', 'location', 'venue']) ||
            $this->hasFields($fillable, ['address', 'latitude', 'longitude'])) {
            return 'Place';
        }
        
        // Kategori/Taksonomi
        if (Str::contains($lowerClassName, ['category', 'tag', 'taxonomy'])) {
            return 'Thing';
        }
        
        // Varsayılan
        return 'Thing';
    }

    /**
     * Model'de belirli alanların varlığını kontrol et
     */
    private function hasFields(array $fillable, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (!in_array($field, $fillable)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Model'de çeviri desteği var mı?
     */
    private function hasTranslations($model): bool
    {
        return method_exists($model, 'getTranslated') || 
               in_array('Spatie\Translatable\HasTranslations', class_uses_recursive($model));
    }

    /**
     * Model'de SEO desteği var mı?
     */
    private function hasSeo($model): bool
    {
        return method_exists($model, 'seoSetting') || 
               in_array('App\Traits\HasSeo', class_uses_recursive($model));
    }

    /**
     * Pattern tespitinin güvenilirlik skorunu hesapla
     */
    private function calculateConfidence(array $fillable, string $schemaType): int
    {
        $confidence = 50; // Base confidence
        
        // Alan sayısına göre artır
        $confidence += min(count($fillable) * 5, 30);
        
        // Schema tipine göre ayarla
        if ($schemaType === 'Thing') {
            $confidence -= 20; // Generic tip düşük confidence
        }
        
        // İçerik alanları varsa artır
        if ($this->hasFields($fillable, ['title', 'description']) || 
            $this->hasFields($fillable, ['title', 'content'])) {
            $confidence += 20;
        }
        
        return min($confidence, 95); // Max %95
    }

    /**
     * Önemli alanları tespit et
     */
    private function detectImportantFields(array $fillable): array
    {
        $important = [];
        $patterns = [
            'title' => ['title', 'name', 'headline'],
            'description' => ['description', 'content', 'body', 'summary'],
            'image' => ['image', 'photo', 'picture', 'thumbnail'],
            'url' => ['url', 'link', 'slug'],
            'date' => ['date', 'published_at', 'created_at'],
            'price' => ['price', 'cost', 'amount'],
            'location' => ['address', 'city', 'country', 'latitude', 'longitude']
        ];
        
        foreach ($patterns as $type => $fields) {
            foreach ($fields as $field) {
                if (in_array($field, $fillable)) {
                    $important[$type][] = $field;
                }
            }
        }
        
        return $important;
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Belirli bir model için bilgi al
     */
    public function getModelInfo(string $className): ?array
    {
        $allModels = $this->discoverModels();
        
        foreach ($allModels as $module => $models) {
            foreach ($models as $model => $info) {
                if ($info['class'] === $className) {
                    return $info;
                }
            }
        }
        
        return null;
    }

    /**
     * Yüksek confidence'a sahip modelleri getir
     */
    public function getHighConfidenceModels(int $minConfidence = 70): array
    {
        $highConfidence = [];
        $allModels = $this->discoverModels();
        
        foreach ($allModels as $module => $models) {
            foreach ($models as $model => $info) {
                if ($info['confidence'] >= $minConfidence) {
                    $highConfidence["{$module}::{$model}"] = $info;
                }
            }
        }
        
        return $highConfidence;
    }
}