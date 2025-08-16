<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Traits\HasTranslations;

readonly class LanguageCleanupService
{
    /**
     * Belirtilen dilleri tüm modüllerden temizle
     */
    public function cleanupLanguagesFromAllModules(array $removedLanguages): array
    {
        Log::info('🧹 Language cleanup başlatıldı', [
            'removed_languages' => $removedLanguages,
            'tenant' => tenant() ? tenant()->id : 'central'
        ]);

        $processedTables = [];
        $totalUpdatedRows = 0;

        // 1. Modül modellerini otomatik tespit et
        $translatableModels = $this->discoverTranslatableModels();

        foreach ($translatableModels as $modelInfo) {
            try {
                $result = $this->cleanupLanguageFromModel(
                    $modelInfo['model'],
                    $modelInfo['table'],
                    $modelInfo['translatable_fields'],
                    $removedLanguages
                );

                $processedTables[] = [
                    'table' => $modelInfo['table'],
                    'model' => $modelInfo['model'],
                    'updated_rows' => $result['updated_rows'],
                    'processed_fields' => $result['processed_fields']
                ];

                $totalUpdatedRows += $result['updated_rows'];

                Log::info("✅ {$modelInfo['table']} tablosu temizlendi", [
                    'updated_rows' => $result['updated_rows'],
                    'fields' => $result['processed_fields']
                ]);

            } catch (\Exception $e) {
                Log::error("❌ {$modelInfo['table']} temizlenirken hata", [
                    'error' => $e->getMessage(),
                    'model' => $modelInfo['model']
                ]);

                $processedTables[] = [
                    'table' => $modelInfo['table'],
                    'model' => $modelInfo['model'],
                    'error' => $e->getMessage(),
                    'updated_rows' => 0
                ];
            }
        }

        Log::info('🎉 Language cleanup tamamlandı', [
            'total_processed_tables' => count($processedTables),
            'total_updated_rows' => $totalUpdatedRows,
            'removed_languages' => $removedLanguages
        ]);

        return [
            'success' => true,
            'processed_tables' => $processedTables,
            'total_updated_rows' => $totalUpdatedRows,
            'removed_languages' => $removedLanguages
        ];
    }

    /**
     * HasTranslations trait kullanan tüm modelleri otomatik tespit et
     */
    public function discoverTranslatableModels(): Collection
    {
        $models = collect();

        // Modül dizinlerini tara
        $moduleBasePath = base_path('Modules');
        
        if (!File::exists($moduleBasePath)) {
            return $models;
        }

        $moduleDirectories = File::directories($moduleBasePath);

        foreach ($moduleDirectories as $moduleDir) {
            $modelPath = $moduleDir . '/app/Models';
            
            if (!File::exists($modelPath)) {
                continue;
            }

            $modelFiles = File::glob($modelPath . '/*.php');

            foreach ($modelFiles as $modelFile) {
                try {
                    $modelInfo = $this->analyzeModelFile($modelFile);
                    if ($modelInfo) {
                        $models->push($modelInfo);
                    }
                } catch (\Exception $e) {
                    Log::warning("Model dosyası analiz edilemedi: {$modelFile}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // App/Models dizinini de kontrol et
        $appModelPath = app_path('Models');
        if (File::exists($appModelPath)) {
            $appModelFiles = File::glob($appModelPath . '/*.php');
            
            foreach ($appModelFiles as $modelFile) {
                try {
                    $modelInfo = $this->analyzeModelFile($modelFile);
                    if ($modelInfo) {
                        $models->push($modelInfo);
                    }
                } catch (\Exception $e) {
                    Log::warning("App model dosyası analiz edilemedi: {$modelFile}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $models;
    }

    /**
     * Model dosyasını analiz et ve HasTranslations trait kontrolü yap
     */
    private function analyzeModelFile(string $filePath): ?array
    {
        $content = File::get($filePath);

        // HasTranslations trait kullanıyor mu?
        if (!str_contains($content, 'HasTranslations')) {
            return null;
        }

        // Class name ve namespace'i çıkar
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        preg_match('/class\s+(\w+)/', $content, $classMatches);

        if (!$namespaceMatches || !$classMatches) {
            return null;
        }

        $namespace = $namespaceMatches[1];
        $className = $classMatches[1];
        $fullClassName = $namespace . '\\' . $className;

        try {
            // Model'i instantiate et
            if (!class_exists($fullClassName)) {
                return null;
            }

            $modelInstance = new $fullClassName;

            // HasTranslations trait'ini kullanıyor mu kontrol et
            $traits = class_uses_recursive($modelInstance);
            if (!in_array(HasTranslations::class, $traits)) {
                return null;
            }

            // Translatable fields'ları al
            $translatableFields = $modelInstance->getTranslatableFields();
            if (empty($translatableFields)) {
                return null;
            }

            // Tablo adını al
            $tableName = $modelInstance->getTable();

            return [
                'model' => $fullClassName,
                'table' => $tableName,
                'translatable_fields' => $translatableFields,
                'file_path' => $filePath
            ];

        } catch (\Exception $e) {
            Log::warning("Model instantiate edilemedi: {$fullClassName}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Belirli bir model için dil temizleme işlemi
     */
    private function cleanupLanguageFromModel(
        string $modelClass,
        string $tableName,
        array $translatableFields,
        array $removedLanguages
    ): array {
        $updatedRows = 0;
        $processedFields = [];

        foreach ($translatableFields as $field) {
            $fieldUpdatedRows = $this->cleanupLanguageFromField(
                $tableName,
                $field,
                $removedLanguages
            );

            $updatedRows += $fieldUpdatedRows;
            $processedFields[] = [
                'field' => $field,
                'updated_rows' => $fieldUpdatedRows
            ];
        }

        return [
            'updated_rows' => $updatedRows,
            'processed_fields' => $processedFields
        ];
    }

    /**
     * Belirli bir field için JSON temizleme
     */
    private function cleanupLanguageFromField(
        string $tableName,
        string $fieldName,
        array $removedLanguages
    ): int {
        try {
            // Tablo var mı kontrol et
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("Tablo bulunamadı: {$tableName}");
                return 0;
            }

            // Field var mı kontrol et
            if (!DB::getSchemaBuilder()->hasColumn($tableName, $fieldName)) {
                Log::warning("Field bulunamadı: {$tableName}.{$fieldName}");
                return 0;
            }

            $updatedRows = 0;

            // Model cache kullanarak primary key al
            $primaryKey = $this->getTablePrimaryKey($tableName);

            // JSON field'larını çek ve temizle
            $rows = DB::table($tableName)
                ->whereNotNull($fieldName)
                ->where($fieldName, '!=', '')
                ->select($primaryKey, $fieldName)
                ->get();

            foreach ($rows as $row) {
                $jsonData = $row->{$fieldName};
                
                // JSON decode et
                if (is_string($jsonData)) {
                    $decodedData = json_decode($jsonData, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        continue; // JSON değilse atla
                    }
                } elseif (is_array($jsonData)) {
                    $decodedData = $jsonData;
                } else {
                    continue; // JSON değilse atla
                }

                // Silinen dilleri çıkar
                $cleanedData = $decodedData;
                $hasChanges = false;

                foreach ($removedLanguages as $language) {
                    if (isset($cleanedData[$language])) {
                        unset($cleanedData[$language]);
                        $hasChanges = true;
                    }
                }

                // Değişiklik varsa güncelle
                if ($hasChanges) {
                    DB::table($tableName)
                        ->where($primaryKey, $row->{$primaryKey})
                        ->update([
                            $fieldName => json_encode($cleanedData, JSON_UNESCAPED_UNICODE)
                        ]);

                    $updatedRows++;
                }
            }

            return $updatedRows;

        } catch (\Exception $e) {
            Log::error("Field temizlenirken hata: {$tableName}.{$fieldName}", [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Orphaned JSON language keys'leri tespit et
     */
    public function detectOrphanedLanguageKeys(): array
    {
        // Aktif dilleri al
        $activeLanguages = $this->getActiveLanguagesForTenant();
        
        $orphanedData = [];
        $translatableModels = $this->discoverTranslatableModels();

        foreach ($translatableModels as $modelInfo) {
            foreach ($modelInfo['translatable_fields'] as $field) {
                $orphanedKeys = $this->findOrphanedKeysInField(
                    $modelInfo['table'],
                    $field,
                    $activeLanguages
                );

                if (!empty($orphanedKeys)) {
                    $orphanedData[] = [
                        'table' => $modelInfo['table'],
                        'field' => $field,
                        'orphaned_languages' => $orphanedKeys,
                        'count' => count($orphanedKeys)
                    ];
                }
            }
        }

        return $orphanedData;
    }

    /**
     * Belirli field'da orphaned language key'leri bul
     */
    private function findOrphanedKeysInField(string $tableName, string $fieldName, array $activeLanguages): array
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable($tableName) || 
                !DB::getSchemaBuilder()->hasColumn($tableName, $fieldName)) {
                return [];
            }

            $orphanedLanguages = [];
            
            $rows = DB::table($tableName)
                ->whereNotNull($fieldName)
                ->where($fieldName, '!=', '')
                ->select($fieldName)
                ->get();

            foreach ($rows as $row) {
                $jsonData = $row->{$fieldName};
                
                if (is_string($jsonData)) {
                    $decodedData = json_decode($jsonData, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        continue;
                    }
                } elseif (is_array($jsonData)) {
                    $decodedData = $jsonData;
                } else {
                    continue;
                }

                foreach (array_keys($decodedData) as $language) {
                    if (!in_array($language, $activeLanguages) && !in_array($language, $orphanedLanguages)) {
                        $orphanedLanguages[] = $language;
                    }
                }
            }

            return $orphanedLanguages;

        } catch (\Exception $e) {
            Log::error("Orphaned keys tespit edilirken hata: {$tableName}.{$fieldName}", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Tenant için aktif dilleri al
     */
    private function getActiveLanguagesForTenant(): array
    {
        try {
            // Site languages tablosundan aktif dilleri al
            $activeLanguages = DB::table('site_languages')
                ->where('is_active', true)
                ->pluck('code')
                ->toArray();

            return $activeLanguages ?: ['tr']; // Fallback olarak tr

        } catch (\Exception $e) {
            Log::error('Aktif diller alınırken hata', [
                'error' => $e->getMessage()
            ]);
            return ['tr']; // Fallback
        }
    }

    /**
     * Tablo için primary key'i al
     */
    private function getTablePrimaryKey(string $tableName): string
    {
        // Model bilgilerini al
        $modelInfo = $this->discoverTranslatableModels()
            ->where('table', $tableName)
            ->first();

        $primaryKey = 'id'; // varsayılan

        if ($modelInfo && isset($modelInfo['model'])) {
            $modelClass = $modelInfo['model'];
            
            if (class_exists($modelClass)) {
                try {
                    $modelInstance = new $modelClass;
                    $primaryKey = $modelInstance->getKeyName();
                } catch (\Exception $e) {
                    Log::warning("Model primary key alınamadı: {$modelClass}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $primaryKey;
    }
}