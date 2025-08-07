<?php

declare(strict_types=1);

namespace Modules\AI\app\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\AI\app\Exceptions\DatabaseLearningException;

/**
 * Database Learning Service V2
 * 
 * Akıllı sistem - Aktif modülleri keşfederek veritabanını öğrenir
 * AI'ın ihtiyacı olan context bilgisini otomatik oluşturur
 * 
 * Features:
 * - Active module detection
 * - Database schema learning
 * - Model relationship mapping  
 * - Auto-context building for AI prompts
 * - Performance optimized caching
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class DatabaseLearningService
{
    /**
     * Cache TTL for learned data (24 hours)
     */
    private const CACHE_TTL = 60 * 60 * 24;
    
    /**
     * Cache key prefixes
     */
    private const CACHE_PREFIX_MODULES = 'ai_learned_modules';
    private const CACHE_PREFIX_SCHEMA = 'ai_learned_schema';
    private const CACHE_PREFIX_RELATIONSHIPS = 'ai_learned_relationships';
    private const CACHE_PREFIX_CONTEXT = 'ai_learned_context';

    /**
     * Supported model file patterns
     */
    private const MODEL_PATTERNS = [
        '*/app/Models/*.php',
        '*/Models/*.php',
        'app/Models/*.php'
    ];

    /**
     * Excluded system tables
     */
    private const EXCLUDED_TABLES = [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'cache',
        'jobs',
        'job_batches'
    ];

    public function __construct(
        private SchemaLearner $schemaLearner,
        private RelationshipMapper $relationshipMapper,
        private ContextBuilder $contextBuilder
    ) {}

    /**
     * Ana learning process - Tüm sistemi öğren
     */
    public function learnCompleteSystem(): array
    {
        try {
            Log::info('[Database Learning V2] Complete system learning started');

            // 1. Aktif modülleri keşfet
            $activeModules = $this->discoverActiveModules();
            
            // 2. Veritabanı şemasını öğren
            $schemaInfo = $this->learnDatabaseSchema();
            
            // 3. Model ilişkilerini mapple
            $relationships = $this->mapModelRelationships($activeModules);
            
            // 4. AI context'ini oluştur
            $aiContext = $this->buildAIContext($activeModules, $schemaInfo, $relationships);
            
            // 5. Öğrenilen bilgiyi cache'le
            $this->cacheLearningResults([
                'modules' => $activeModules,
                'schema' => $schemaInfo,
                'relationships' => $relationships,
                'ai_context' => $aiContext,
                'learned_at' => now()->toISOString()
            ]);

            Log::info('[Database Learning V2] Learning completed successfully', [
                'modules_count' => count($activeModules),
                'tables_count' => count($schemaInfo),
                'relationships_count' => count($relationships)
            ]);

            return [
                'success' => true,
                'data' => [
                    'active_modules' => $activeModules,
                    'schema_info' => $schemaInfo,
                    'relationships' => $relationships,
                    'ai_context' => $aiContext
                ],
                'stats' => [
                    'modules_discovered' => count($activeModules),
                    'tables_analyzed' => count($schemaInfo),
                    'relationships_mapped' => count($relationships),
                    'context_size' => strlen($aiContext)
                ]
            ];

        } catch (Exception $e) {
            Log::error('[Database Learning V2] Learning failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw DatabaseLearningException::systemLearningFailed($e->getMessage());
        }
    }

    /**
     * Aktif modülleri keşfet
     */
    public function discoverActiveModules(): array
    {
        return Cache::remember(self::CACHE_PREFIX_MODULES, self::CACHE_TTL, function () {
            try {
                $modules = [];
                $modulesPath = base_path('Modules');
                
                if (!File::exists($modulesPath)) {
                    return [];
                }

                $moduleDirectories = File::directories($modulesPath);
                
                foreach ($moduleDirectories as $modulePath) {
                    $moduleName = basename($modulePath);
                    
                    // Module.json kontrol et
                    $moduleJsonPath = $modulePath . '/module.json';
                    if (!File::exists($moduleJsonPath)) {
                        continue;
                    }

                    // Module bilgilerini oku
                    $moduleJson = json_decode(File::get($moduleJsonPath), true);
                    if (!$moduleJson || !($moduleJson['active'] ?? false)) {
                        continue;
                    }

                    // Module yapısını analiz et
                    $moduleInfo = $this->analyzeModuleStructure($modulePath, $moduleName);
                    
                    if ($moduleInfo) {
                        $modules[$moduleName] = $moduleInfo;
                    }
                }

                Log::info('[Database Learning] Active modules discovered', [
                    'count' => count($modules),
                    'modules' => array_keys($modules)
                ]);

                return $modules;

            } catch (Exception $e) {
                Log::error('[Database Learning] Module discovery failed', [
                    'error' => $e->getMessage()
                ]);
                
                return [];
            }
        });
    }

    /**
     * Module yapısını analiz et
     */
    private function analyzeModuleStructure(string $modulePath, string $moduleName): ?array
    {
        try {
            $structure = [
                'name' => $moduleName,
                'path' => $modulePath,
                'models' => [],
                'controllers' => [],
                'migrations' => [],
                'routes' => [],
                'views' => []
            ];

            // Models klasörünü analiz et
            $modelsPath = $modulePath . '/app/Models';
            if (File::exists($modelsPath)) {
                $modelFiles = File::files($modelsPath);
                foreach ($modelFiles as $modelFile) {
                    if ($modelFile->getExtension() === 'php') {
                        $modelName = $modelFile->getFilenameWithoutExtension();
                        $structure['models'][] = [
                            'name' => $modelName,
                            'file' => $modelFile->getPathname(),
                            'namespace' => "Modules\\{$moduleName}\\app\\Models\\{$modelName}"
                        ];
                    }
                }
            }

            // Controllers analiz et
            $controllersPath = $modulePath . '/app/Http/Controllers';
            if (File::exists($controllersPath)) {
                $this->analyzeControllers($controllersPath, $structure, $moduleName);
            }

            // Migrations analiz et
            $migrationsPath = $modulePath . '/database/migrations';
            if (File::exists($migrationsPath)) {
                $migrationFiles = File::files($migrationsPath);
                foreach ($migrationFiles as $migrationFile) {
                    if ($migrationFile->getExtension() === 'php') {
                        $structure['migrations'][] = [
                            'name' => $migrationFile->getFilename(),
                            'file' => $migrationFile->getPathname()
                        ];
                    }
                }
            }

            // Routes analiz et  
            $routesPath = $modulePath . '/routes';
            if (File::exists($routesPath)) {
                $routeFiles = File::files($routesPath);
                foreach ($routeFiles as $routeFile) {
                    if ($routeFile->getExtension() === 'php') {
                        $structure['routes'][] = [
                            'name' => $routeFile->getFilenameWithoutExtension(),
                            'file' => $routeFile->getPathname()
                        ];
                    }
                }
            }

            // Eğer hiçbir önemli yapı yoksa null döndür
            if (empty($structure['models']) && empty($structure['controllers'])) {
                return null;
            }

            return $structure;

        } catch (Exception $e) {
            Log::error('[Database Learning] Module structure analysis failed', [
                'module' => $moduleName,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Controllers analiz et (recursive)
     */
    private function analyzeControllers(string $controllersPath, array &$structure, string $moduleName): void
    {
        $controllerFiles = File::allFiles($controllersPath);
        
        foreach ($controllerFiles as $controllerFile) {
            if ($controllerFile->getExtension() === 'php') {
                $relativePath = str_replace($controllersPath . '/', '', $controllerFile->getPathname());
                $controllerName = str_replace(['/', '.php'], ['\\', ''], $relativePath);
                
                $structure['controllers'][] = [
                    'name' => $controllerName,
                    'file' => $controllerFile->getPathname(),
                    'namespace' => "Modules\\{$moduleName}\\app\\Http\\Controllers\\{$controllerName}"
                ];
            }
        }
    }

    /**
     * Veritabanı şemasını öğren
     */
    public function learnDatabaseSchema(): array
    {
        return Cache::remember(self::CACHE_PREFIX_SCHEMA, self::CACHE_TTL, function () {
            return $this->schemaLearner->analyzeSchema();
        });
    }

    /**
     * Model ilişkilerini mapple
     */
    public function mapModelRelationships(array $activeModules): array
    {
        return Cache::remember(self::CACHE_PREFIX_RELATIONSHIPS, self::CACHE_TTL, function () use ($activeModules) {
            return $this->relationshipMapper->mapRelationships($activeModules);
        });
    }

    /**
     * AI context oluştur
     */
    public function buildAIContext(array $activeModules, array $schemaInfo, array $relationships): string
    {
        return Cache::remember(self::CACHE_PREFIX_CONTEXT, self::CACHE_TTL, function () use ($activeModules, $schemaInfo, $relationships) {
            return $this->contextBuilder->buildContext($activeModules, $schemaInfo, $relationships);
        });
    }

    /**
     * Öğrenilen bilgiyi cache'le
     */
    private function cacheLearningResults(array $learningData): void
    {
        Cache::put('ai_learning_complete_results', $learningData, self::CACHE_TTL);
        
        Cache::put(self::CACHE_PREFIX_MODULES, $learningData['modules'], self::CACHE_TTL);
        Cache::put(self::CACHE_PREFIX_SCHEMA, $learningData['schema'], self::CACHE_TTL);
        Cache::put(self::CACHE_PREFIX_RELATIONSHIPS, $learningData['relationships'], self::CACHE_TTL);
        Cache::put(self::CACHE_PREFIX_CONTEXT, $learningData['ai_context'], self::CACHE_TTL);
    }

    /**
     * Cached learning results al
     */
    public function getCachedLearningResults(): ?array
    {
        return Cache::get('ai_learning_complete_results');
    }

    /**
     * Learning cache'ini temizle
     */
    public function clearLearningCache(): bool
    {
        try {
            Cache::forget('ai_learning_complete_results');
            Cache::forget(self::CACHE_PREFIX_MODULES);
            Cache::forget(self::CACHE_PREFIX_SCHEMA);
            Cache::forget(self::CACHE_PREFIX_RELATIONSHIPS);
            Cache::forget(self::CACHE_PREFIX_CONTEXT);
            
            Log::info('[Database Learning] Cache cleared successfully');
            
            return true;
        } catch (Exception $e) {
            Log::error('[Database Learning] Cache clear failed', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * AI prompt için optimized context al
     */
    public function getAIOptimizedContext(string $featureType = 'general'): string
    {
        $cachedResults = $this->getCachedLearningResults();
        
        if (!$cachedResults) {
            // Cache yoksa hızlı learning yap
            $results = $this->learnCompleteSystem();
            $cachedResults = $results['data'];
        }

        return $this->contextBuilder->getOptimizedContext($cachedResults, $featureType);
    }

    /**
     * System stats
     */
    public function getSystemStats(): array
    {
        $cachedResults = $this->getCachedLearningResults();
        
        if (!$cachedResults) {
            return [
                'cached' => false,
                'modules' => 0,
                'tables' => 0,
                'relationships' => 0,
                'last_learned' => null
            ];
        }

        return [
            'cached' => true,
            'modules' => count($cachedResults['modules'] ?? []),
            'tables' => count($cachedResults['schema'] ?? []),
            'relationships' => count($cachedResults['relationships'] ?? []),
            'context_size' => strlen($cachedResults['ai_context'] ?? ''),
            'last_learned' => $cachedResults['learned_at'] ?? null
        ];
    }
}