<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionMethod;

/**
 * Relationship Mapper V2
 * 
 * Model ilişkilerini akıllı şekilde keşfeder ve haritalandırır
 * Eloquent relationships'ları analiz ederek AI için context oluşturur
 * 
 * Features:
 * - Eloquent relationship detection
 * - Model dependency mapping  
 * - Inheritance hierarchy analysis
 * - Trait usage detection
 * - AI-optimized relationship descriptions
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class RelationshipMapper
{
    /**
     * Eloquent relationship methods
     */
    private const RELATIONSHIP_METHODS = [
        'hasOne',
        'hasMany', 
        'belongsTo',
        'belongsToMany',
        'hasManyThrough',
        'hasOneThrough',
        'morphOne',
        'morphMany',
        'morphTo',
        'morphToMany',
        'morphedByMany'
    ];

    /**
     * Common relationship patterns in method names
     */
    private const RELATIONSHIP_PATTERNS = [
        'user' => 'User relationship',
        'users' => 'Multiple users relationship',
        'category' => 'Category relationship',
        'categories' => 'Multiple categories relationship',
        'parent' => 'Hierarchical parent relationship',
        'children' => 'Hierarchical children relationship',
        'tags' => 'Tag relationship',
        'comments' => 'Comments relationship',
        'posts' => 'Posts relationship',
        'pages' => 'Pages relationship',
        'settings' => 'Settings relationship'
    ];

    public function __construct()
    {
    }

    /**
     * Ana relationship mapping işlemi
     */
    public function mapRelationships(array $activeModules): array
    {
        try {
            Log::info('[Relationship Mapper V2] Starting relationship analysis');

            $allRelationships = [];
            $modelGraph = [];
            
            foreach ($activeModules as $moduleName => $moduleInfo) {
                Log::info("[Relationship Mapper] Analyzing module: {$moduleName}");
                
                $moduleRelationships = $this->analyzeModuleRelationships($moduleInfo);
                
                if (!empty($moduleRelationships)) {
                    $allRelationships[$moduleName] = $moduleRelationships;
                    
                    // Model graph'ı oluştur
                    $this->buildModelGraph($moduleRelationships, $modelGraph);
                }
            }

            // Cross-module relationships'ları bul
            $crossModuleRels = $this->findCrossModuleRelationships($allRelationships);

            // Relationship istatistiklerini hesapla
            $stats = $this->calculateRelationshipStats($allRelationships, $crossModuleRels);

            Log::info('[Relationship Mapper V2] Analysis completed', [
                'total_models' => $stats['total_models'],
                'total_relationships' => $stats['total_relationships'],
                'cross_module_relationships' => count($crossModuleRels)
            ]);

            return [
                'module_relationships' => $allRelationships,
                'cross_module_relationships' => $crossModuleRels,
                'model_graph' => $modelGraph,
                'statistics' => $stats,
                'ai_context' => $this->generateRelationshipAIContext($allRelationships, $crossModuleRels, $modelGraph)
            ];

        } catch (Exception $e) {
            Log::error('[Relationship Mapper V2] Relationship analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Bir modülün relationship'larını analiz et
     */
    private function analyzeModuleRelationships(array $moduleInfo): array
    {
        $relationships = [];

        foreach ($moduleInfo['models'] ?? [] as $modelInfo) {
            try {
                $modelRelationships = $this->analyzeModelRelationships($modelInfo);
                
                if (!empty($modelRelationships)) {
                    $relationships[$modelInfo['name']] = $modelRelationships;
                }

            } catch (Exception $e) {
                Log::error('[Relationship Mapper] Model analysis failed', [
                    'model' => $modelInfo['name'],
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $relationships;
    }

    /**
     * Tek bir modelin relationship'larını analiz et
     */
    private function analyzeModelRelationships(array $modelInfo): array
    {
        try {
            $relationships = [];
            
            // Model dosyasını oku ve analiz et
            if (!File::exists($modelInfo['file'])) {
                return [];
            }

            $fileContent = File::get($modelInfo['file']);
            
            // Namespace ve class'ı kontrol et
            if (!class_exists($modelInfo['namespace'])) {
                return [];
            }

            // Reflection ile model'i analiz et
            $reflection = new ReflectionClass($modelInfo['namespace']);
            
            // Public metodları analiz et
            $publicMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            foreach ($publicMethods as $method) {
                $methodName = $method->getName();
                
                // Inherited method'ları atla
                if ($method->getDeclaringClass()->getName() !== $modelInfo['namespace']) {
                    continue;
                }

                // Magic method'ları atla
                if (strpos($methodName, '__') === 0) {
                    continue;
                }

                // Model method'u analiz et
                $relationshipInfo = $this->analyzeMethodForRelationship($method, $fileContent);
                
                if ($relationshipInfo) {
                    $relationships[$methodName] = $relationshipInfo;
                }
            }

            // File content'ten ek relationship'ları bul
            $additionalRels = $this->findRelationshipsInContent($fileContent, $modelInfo['name']);
            $relationships = array_merge($relationships, $additionalRels);

            return $relationships;

        } catch (Exception $e) {
            Log::error('[Relationship Mapper] Model relationship analysis failed', [
                'model' => $modelInfo['name'],
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Method'u relationship için analiz et
     */
    private function analyzeMethodForRelationship(ReflectionMethod $method, string $fileContent): ?array
    {
        try {
            $methodName = $method->getName();
            
            // Method'un return type'ını kontrol et
            $returnType = $method->getReturnType();
            $returnTypeName = $returnType ? $returnType->getName() : null;
            
            // Eloquent relationship return type'larını kontrol et
            if ($returnTypeName && $this->isEloquentRelationshipType($returnTypeName)) {
                return [
                    'method_name' => $methodName,
                    'return_type' => $returnTypeName,
                    'relationship_type' => $this->getRelationshipTypeFromReturnType($returnTypeName),
                    'related_model' => $this->extractRelatedModelFromMethod($method, $fileContent),
                    'detection_method' => 'return_type',
                    'ai_description' => $this->generateRelationshipDescription($methodName, $returnTypeName)
                ];
            }

            // File content'ten relationship'ı tespit et
            $contentAnalysis = $this->analyzeMethodContentForRelationship($methodName, $fileContent);
            
            if ($contentAnalysis) {
                return $contentAnalysis;
            }

            // Pattern-based detection
            $patternAnalysis = $this->analyzeMethodNamePattern($methodName);
            
            if ($patternAnalysis) {
                return $patternAnalysis;
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Return type'ın Eloquent relationship olup olmadığını kontrol et
     */
    private function isEloquentRelationshipType(string $typeName): bool
    {
        $eloquentRelTypes = [
            'Illuminate\Database\Eloquent\Relations\HasOne',
            'Illuminate\Database\Eloquent\Relations\HasMany',
            'Illuminate\Database\Eloquent\Relations\BelongsTo',
            'Illuminate\Database\Eloquent\Relations\BelongsToMany',
            'Illuminate\Database\Eloquent\Relations\HasManyThrough',
            'Illuminate\Database\Eloquent\Relations\HasOneThrough',
            'Illuminate\Database\Eloquent\Relations\MorphOne',
            'Illuminate\Database\Eloquent\Relations\MorphMany',
            'Illuminate\Database\Eloquent\Relations\MorphTo',
            'Illuminate\Database\Eloquent\Relations\MorphToMany',
            'Illuminate\Database\Eloquent\Relations\MorphedByMany'
        ];

        return in_array($typeName, $eloquentRelTypes) || 
               strpos($typeName, 'Relations\\') !== false;
    }

    /**
     * Return type'tan relationship type'ını çıkar
     */
    private function getRelationshipTypeFromReturnType(string $returnType): string
    {
        $parts = explode('\\', $returnType);
        return end($parts);
    }

    /**
     * Method'tan related model'i çıkar
     */
    private function extractRelatedModelFromMethod(ReflectionMethod $method, string $fileContent): ?string
    {
        $methodName = $method->getName();
        
        // Method content'ini bul
        $pattern = '/function\s+' . preg_quote($methodName) . '\s*\([^)]*\)\s*(?::\s*[^{]*)?{([^}]*)}/s';
        
        if (preg_match($pattern, $fileContent, $matches)) {
            $methodContent = $matches[1];
            
            // İlk parametre olarak model class'ını bul
            if (preg_match('/return\s+\$this->\w+\(\s*([^,\s)]+)/', $methodContent, $modelMatches)) {
                $modelReference = trim($modelMatches[1], '"\'');
                
                // Class reference ise namespace'i temizle
                if (strpos($modelReference, '::class') !== false) {
                    return str_replace('::class', '', $modelReference);
                }
                
                return $modelReference;
            }
        }

        return null;
    }

    /**
     * Method content'ini relationship için analiz et
     */
    private function analyzeMethodContentForRelationship(string $methodName, string $fileContent): ?array
    {
        // Method content'ini bul
        $pattern = '/function\s+' . preg_quote($methodName) . '\s*\([^)]*\)\s*(?::\s*[^{]*)?{([^}]*)}/s';
        
        if (preg_match($pattern, $fileContent, $matches)) {
            $methodContent = $matches[1];
            
            // Eloquent relationship method'larını ara
            foreach (self::RELATIONSHIP_METHODS as $relMethod) {
                if (strpos($methodContent, '$this->' . $relMethod . '(') !== false) {
                    // Related model'i bul
                    $relatedModel = $this->extractModelFromRelationshipCall($methodContent, $relMethod);
                    
                    return [
                        'method_name' => $methodName,
                        'relationship_type' => $relMethod,
                        'related_model' => $relatedModel,
                        'detection_method' => 'content_analysis',
                        'ai_description' => $this->generateRelationshipDescription($methodName, $relMethod, $relatedModel)
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Relationship call'undan model'i çıkar
     */
    private function extractModelFromRelationshipCall(string $content, string $relationshipMethod): ?string
    {
        $pattern = '/\$this->' . preg_quote($relationshipMethod) . '\s*\(\s*([^,\s)]+)/';
        
        if (preg_match($pattern, $content, $matches)) {
            $modelReference = trim($matches[1], '"\'');
            
            // Class reference
            if (strpos($modelReference, '::class') !== false) {
                return str_replace('::class', '', $modelReference);
            }
            
            return $modelReference;
        }

        return null;
    }

    /**
     * Method isminden pattern analizi
     */
    private function analyzeMethodNamePattern(string $methodName): ?array
    {
        $lowerMethod = strtolower($methodName);
        
        foreach (self::RELATIONSHIP_PATTERNS as $pattern => $description) {
            if (strpos($lowerMethod, $pattern) !== false) {
                // Plural/singular'a göre relationship type'ını tahmin et
                $relType = $this->guessRelationshipTypeFromName($methodName);
                
                return [
                    'method_name' => $methodName,
                    'relationship_type' => $relType,
                    'related_model' => $this->guessModelFromMethodName($methodName),
                    'detection_method' => 'pattern_analysis',
                    'ai_description' => $description
                ];
            }
        }

        return null;
    }

    /**
     * Method isminden relationship type'ını tahmin et
     */
    private function guessRelationshipTypeFromName(string $methodName): string
    {
        $lowerMethod = strtolower($methodName);
        
        // Plural isimlerde genelde hasMany
        if (str_ends_with($lowerMethod, 's') || 
            str_ends_with($lowerMethod, 'ies') || 
            str_ends_with($lowerMethod, 'es')) {
            return 'hasMany';
        }
        
        // Parent-children patterns
        if (strpos($lowerMethod, 'parent') !== false) {
            return 'belongsTo';
        }
        
        if (strpos($lowerMethod, 'child') !== false) {
            return 'hasMany';
        }

        // Default to belongsTo for singular
        return 'belongsTo';
    }

    /**
     * Method isminden model ismini tahmin et
     */
    private function guessModelFromMethodName(string $methodName): string
    {
        // Capitalize ve singular yap
        $modelName = ucfirst($methodName);
        
        // Plural'ları singular yap
        if (str_ends_with($modelName, 'ies')) {
            $modelName = substr($modelName, 0, -3) . 'y';
        } elseif (str_ends_with($modelName, 's')) {
            $modelName = substr($modelName, 0, -1);
        }

        return $modelName;
    }

    /**
     * File content'ten ek relationship'ları bul
     */
    private function findRelationshipsInContent(string $content, string $modelName): array
    {
        $relationships = [];
        
        // Tüm $this->relationshipMethod() çağrılarını bul
        foreach (self::RELATIONSHIP_METHODS as $relMethod) {
            $pattern = '/\$this->' . preg_quote($relMethod) . '\s*\(\s*([^,\)]+)/';
            
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $relatedModel = trim($match[1], '"\'');
                    
                    if (strpos($relatedModel, '::class') !== false) {
                        $relatedModel = str_replace('::class', '', $relatedModel);
                    }
                    
                    // Key olarak relationship method + model kullan
                    $key = strtolower($relMethod . '_' . basename($relatedModel));
                    
                    $relationships[$key] = [
                        'method_name' => $key,
                        'relationship_type' => $relMethod,
                        'related_model' => $relatedModel,
                        'detection_method' => 'content_scan',
                        'ai_description' => "Auto-detected {$relMethod} relationship with {$relatedModel}"
                    ];
                }
            }
        }

        return $relationships;
    }

    /**
     * Cross-module relationship'ları bul
     */
    private function findCrossModuleRelationships(array $allRelationships): array
    {
        $crossModule = [];

        foreach ($allRelationships as $sourceModule => $moduleRels) {
            foreach ($moduleRels as $sourceModel => $modelRels) {
                foreach ($modelRels as $relName => $relInfo) {
                    $relatedModel = $relInfo['related_model'] ?? '';
                    
                    if (!$relatedModel) continue;
                    
                    // Bu related model başka bir modülde var mı?
                    $targetModule = $this->findModelModule($relatedModel, $allRelationships);
                    
                    if ($targetModule && $targetModule !== $sourceModule) {
                        $crossModule[] = [
                            'source_module' => $sourceModule,
                            'source_model' => $sourceModel,
                            'target_module' => $targetModule,
                            'target_model' => $relatedModel,
                            'relationship' => $relInfo,
                            'ai_description' => "Cross-module relationship: {$sourceModule}.{$sourceModel} -> {$targetModule}.{$relatedModel}"
                        ];
                    }
                }
            }
        }

        return $crossModule;
    }

    /**
     * Model'in hangi modülde olduğunu bul
     */
    private function findModelModule(string $modelName, array $allRelationships): ?string
    {
        foreach ($allRelationships as $moduleName => $moduleRels) {
            if (isset($moduleRels[$modelName])) {
                return $moduleName;
            }
        }

        return null;
    }

    /**
     * Model graph'ı oluştur
     */
    private function buildModelGraph(array $relationships, array &$modelGraph): void
    {
        foreach ($relationships as $modelName => $modelRels) {
            if (!isset($modelGraph[$modelName])) {
                $modelGraph[$modelName] = [
                    'connections' => [],
                    'incoming' => 0,
                    'outgoing' => 0
                ];
            }

            foreach ($modelRels as $relName => $relInfo) {
                $relatedModel = $relInfo['related_model'] ?? '';
                
                if ($relatedModel) {
                    $modelGraph[$modelName]['connections'][] = [
                        'target' => $relatedModel,
                        'type' => $relInfo['relationship_type'],
                        'method' => $relName
                    ];
                    
                    $modelGraph[$modelName]['outgoing']++;
                    
                    // Target model'in incoming count'unu artır
                    if (!isset($modelGraph[$relatedModel])) {
                        $modelGraph[$relatedModel] = [
                            'connections' => [],
                            'incoming' => 0,
                            'outgoing' => 0
                        ];
                    }
                    
                    $modelGraph[$relatedModel]['incoming']++;
                }
            }
        }
    }

    /**
     * Relationship istatistiklerini hesapla
     */
    private function calculateRelationshipStats(array $allRelationships, array $crossModuleRels): array
    {
        $totalModels = 0;
        $totalRelationships = 0;
        $relationshipTypes = [];

        foreach ($allRelationships as $moduleRels) {
            $totalModels += count($moduleRels);
            
            foreach ($moduleRels as $modelRels) {
                $totalRelationships += count($modelRels);
                
                foreach ($modelRels as $relInfo) {
                    $type = $relInfo['relationship_type'] ?? 'unknown';
                    $relationshipTypes[$type] = ($relationshipTypes[$type] ?? 0) + 1;
                }
            }
        }

        return [
            'total_models' => $totalModels,
            'total_relationships' => $totalRelationships,
            'cross_module_relationships' => count($crossModuleRels),
            'relationship_types' => $relationshipTypes,
            'most_common_relationship' => !empty($relationshipTypes) ? array_search(max($relationshipTypes), $relationshipTypes) : null
        ];
    }

    /**
     * Relationship AI context'i oluştur
     */
    private function generateRelationshipAIContext(array $allRelationships, array $crossModuleRels, array $modelGraph): string
    {
        $context = "# Model Relationships Overview\n\n";
        
        // Modül başına ilişki özeti
        foreach ($allRelationships as $moduleName => $moduleRels) {
            $context .= "## {$moduleName} Module\n";
            
            foreach ($moduleRels as $modelName => $modelRels) {
                $context .= "- **{$modelName}**: ";
                
                $relDescriptions = [];
                foreach ($modelRels as $relInfo) {
                    $relDescriptions[] = $relInfo['ai_description'] ?? $relInfo['relationship_type'];
                }
                
                $context .= implode(', ', $relDescriptions) . "\n";
            }
            
            $context .= "\n";
        }

        // Cross-module relationships
        if (!empty($crossModuleRels)) {
            $context .= "## Cross-Module Relationships\n";
            foreach ($crossModuleRels as $crossRel) {
                $context .= "- " . $crossRel['ai_description'] . "\n";
            }
            $context .= "\n";
        }

        // Model connectivity
        $context .= "## Model Connectivity\n";
        foreach ($modelGraph as $modelName => $graphInfo) {
            $context .= "- **{$modelName}**: {$graphInfo['outgoing']} outgoing, {$graphInfo['incoming']} incoming connections\n";
        }

        return $context;
    }

    /**
     * Relationship açıklaması oluştur
     */
    private function generateRelationshipDescription(string $methodName, string $type, ?string $relatedModel = null): string
    {
        $description = "{$methodName} ({$type})";
        
        if ($relatedModel) {
            $description .= " -> {$relatedModel}";
        }

        return $description;
    }
}