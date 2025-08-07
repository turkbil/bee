<?php

declare(strict_types=1);

namespace Modules\AI\app\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Context Builder V2
 * 
 * AI için optimize edilmiş context metinleri oluşturur
 * Öğrenilen sistem bilgisini AI prompt'larına uygun formatta sunar
 * Feature-aware context optimization yapar
 * 
 * Features:
 * - AI-optimized context generation
 * - Feature-specific context adaptation
 * - Smart content summarization
 * - Performance-focused text building
 * - Multi-language context support
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class ContextBuilder
{
    /**
     * Context templates for different feature types
     */
    private const CONTEXT_TEMPLATES = [
        'general' => [
            'include_modules' => true,
            'include_schema' => true,
            'include_relationships' => true,
            'detail_level' => 'medium',
            'max_length' => 5000
        ],
        'seo' => [
            'include_modules' => true,
            'include_schema' => false,
            'include_relationships' => false,
            'detail_level' => 'low',
            'max_length' => 2000,
            'focus_areas' => ['content_management', 'page_structure']
        ],
        'blog' => [
            'include_modules' => true,
            'include_schema' => true,
            'include_relationships' => true,
            'detail_level' => 'high',
            'max_length' => 8000,
            'focus_areas' => ['content_management', 'user_management']
        ],
        'translation' => [
            'include_modules' => false,
            'include_schema' => false,
            'include_relationships' => false,
            'detail_level' => 'minimal',
            'max_length' => 500
        ],
        'analysis' => [
            'include_modules' => true,
            'include_schema' => true,
            'include_relationships' => true,
            'detail_level' => 'high',
            'max_length' => 10000,
            'focus_areas' => ['data_structure', 'system_architecture']
        ],
        'code' => [
            'include_modules' => true,
            'include_schema' => true,
            'include_relationships' => true,
            'detail_level' => 'maximum',
            'max_length' => 15000,
            'focus_areas' => ['system_architecture', 'model_relationships', 'data_structure']
        ]
    ];

    /**
     * Important system keywords for context prioritization
     */
    private const SYSTEM_KEYWORDS = [
        'user' => 10,
        'page' => 9,
        'content' => 8,
        'seo' => 7,
        'setting' => 6,
        'module' => 6,
        'portfolio' => 5,
        'announcement' => 5,
        'language' => 4,
        'tenant' => 3
    ];

    public function __construct()
    {
    }

    /**
     * Ana context oluşturma işlemi
     */
    public function buildContext(array $activeModules, array $schemaInfo, array $relationships): string
    {
        try {
            Log::info('[Context Builder V2] Building AI context');

            $contextSections = [];

            // System Overview
            $contextSections[] = $this->buildSystemOverview($activeModules);

            // Module Information
            if (!empty($activeModules)) {
                $contextSections[] = $this->buildModuleContext($activeModules);
            }

            // Database Schema
            if (!empty($schemaInfo)) {
                $contextSections[] = $this->buildSchemaContext($schemaInfo);
            }

            // Model Relationships
            if (!empty($relationships)) {
                $contextSections[] = $this->buildRelationshipContext($relationships);
            }

            // System Capabilities
            $contextSections[] = $this->buildCapabilitiesContext($activeModules, $schemaInfo);

            $fullContext = implode("\n\n", array_filter($contextSections));

            Log::info('[Context Builder V2] Context built successfully', [
                'total_length' => strlen($fullContext),
                'sections' => count($contextSections)
            ]);

            return $fullContext;

        } catch (Exception $e) {
            Log::error('[Context Builder V2] Context building failed', [
                'error' => $e->getMessage()
            ]);

            return $this->buildFallbackContext();
        }
    }

    /**
     * Feature-specific optimize edilmiş context
     */
    public function getOptimizedContext(array $learningData, string $featureType = 'general'): string
    {
        try {
            $template = self::CONTEXT_TEMPLATES[$featureType] ?? self::CONTEXT_TEMPLATES['general'];

            Log::info('[Context Builder V2] Building optimized context', [
                'feature_type' => $featureType,
                'template' => $template
            ]);

            $contextSections = [];

            // System overview (always include)
            $contextSections[] = $this->buildSystemOverviewOptimized($learningData, $template);

            // Conditional sections based on template
            if ($template['include_modules'] ?? false) {
                $contextSections[] = $this->buildModuleContextOptimized($learningData, $template);
            }

            if ($template['include_schema'] ?? false) {
                $contextSections[] = $this->buildSchemaContextOptimized($learningData, $template);
            }

            if ($template['include_relationships'] ?? false) {
                $contextSections[] = $this->buildRelationshipContextOptimized($learningData, $template);
            }

            // Focus areas için özel content
            if (!empty($template['focus_areas'])) {
                $contextSections[] = $this->buildFocusAreaContext($learningData, $template['focus_areas']);
            }

            $fullContext = implode("\n\n", array_filter($contextSections));

            // Max length kontrolü
            if (isset($template['max_length']) && strlen($fullContext) > $template['max_length']) {
                $fullContext = $this->truncateContext($fullContext, $template['max_length']);
            }

            Log::info('[Context Builder V2] Optimized context built', [
                'feature_type' => $featureType,
                'final_length' => strlen($fullContext)
            ]);

            return $fullContext;

        } catch (Exception $e) {
            Log::error('[Context Builder V2] Optimized context building failed', [
                'feature_type' => $featureType,
                'error' => $e->getMessage()
            ]);

            return $this->buildFallbackContext();
        }
    }

    /**
     * System genel özeti
     */
    private function buildSystemOverview(array $activeModules): string
    {
        $moduleCount = count($activeModules);
        $moduleNames = array_keys($activeModules);

        $context = "# Laravel CMS System Overview\n\n";
        $context .= "**System Type**: Laravel 11 + Multi-tenant Modular CMS\n";
        $context .= "**Active Modules**: {$moduleCount} modules\n";
        $context .= "**Available Modules**: " . implode(', ', $moduleNames) . "\n";
        $context .= "**Architecture**: Domain-based tenancy with isolated databases\n";
        $context .= "**Frontend**: Tailwind CSS + Alpine.js\n";
        $context .= "**Admin Panel**: Tabler.io + Bootstrap + Livewire\n";

        return $context;
    }

    /**
     * Optimize edilmiş system overview
     */
    private function buildSystemOverviewOptimized(array $learningData, array $template): string
    {
        $detailLevel = $template['detail_level'] ?? 'medium';
        $modules = $learningData['modules'] ?? [];
        
        $context = "# System Context\n\n";

        if ($detailLevel === 'minimal') {
            $context .= "Laravel CMS with " . count($modules) . " active modules\n";
        } elseif ($detailLevel === 'low') {
            $context .= "**System**: Laravel 11 Multi-tenant CMS\n";
            $context .= "**Modules**: " . implode(', ', array_keys($modules)) . "\n";
        } else {
            $context .= "**System Type**: Laravel 11 + Multi-tenant Modular CMS\n";
            $context .= "**Active Modules**: " . count($modules) . " modules\n";
            $context .= "**Available Modules**: " . implode(', ', array_keys($modules)) . "\n";
            
            if ($detailLevel === 'high' || $detailLevel === 'maximum') {
                $context .= "**Architecture**: Domain-based tenancy with isolated databases\n";
                $context .= "**Frontend**: Tailwind CSS + Alpine.js\n";
                $context .= "**Admin Panel**: Tabler.io + Bootstrap + Livewire\n";
            }
        }

        return $context;
    }

    /**
     * Module context'i oluştur
     */
    private function buildModuleContext(array $activeModules): string
    {
        $context = "# Active Modules\n\n";

        foreach ($activeModules as $moduleName => $moduleInfo) {
            $context .= "## {$moduleName} Module\n";
            $context .= "- **Models**: " . count($moduleInfo['models'] ?? []) . "\n";
            $context .= "- **Controllers**: " . count($moduleInfo['controllers'] ?? []) . "\n";
            $context .= "- **Migrations**: " . count($moduleInfo['migrations'] ?? []) . "\n";

            if (!empty($moduleInfo['models'])) {
                $modelNames = array_column($moduleInfo['models'], 'name');
                $context .= "- **Key Models**: " . implode(', ', $modelNames) . "\n";
            }

            $context .= "\n";
        }

        return $context;
    }

    /**
     * Optimize edilmiş module context
     */
    private function buildModuleContextOptimized(array $learningData, array $template): string
    {
        $modules = $learningData['modules'] ?? [];
        $detailLevel = $template['detail_level'] ?? 'medium';

        if (empty($modules)) {
            return '';
        }

        $context = "# Modules\n\n";

        foreach ($modules as $moduleName => $moduleInfo) {
            $importance = $this->calculateModuleImportance($moduleName, $moduleInfo);

            if ($detailLevel === 'minimal' && $importance < 5) {
                continue;
            }

            $context .= "**{$moduleName}**: ";

            if ($detailLevel === 'minimal') {
                $context .= count($moduleInfo['models'] ?? []) . " models\n";
            } elseif ($detailLevel === 'low') {
                $context .= count($moduleInfo['models'] ?? []) . " models, " . 
                           count($moduleInfo['controllers'] ?? []) . " controllers\n";
            } else {
                $context .= "\n";
                $context .= "- Models: " . count($moduleInfo['models'] ?? []) . "\n";
                $context .= "- Controllers: " . count($moduleInfo['controllers'] ?? []) . "\n";
                
                if (!empty($moduleInfo['models'])) {
                    $modelNames = array_column($moduleInfo['models'], 'name');
                    $context .= "- Key Models: " . implode(', ', $modelNames) . "\n";
                }
            }

            $context .= "\n";
        }

        return $context;
    }

    /**
     * Schema context'i oluştur
     */
    private function buildSchemaContext(array $schemaInfo): string
    {
        if (empty($schemaInfo)) {
            return '';
        }

        $context = "# Database Schema\n\n";
        $importantTables = $this->getImportantTables($schemaInfo, 10);

        foreach ($importantTables as $tableName => $tableInfo) {
            $context .= "## {$tableName} Table\n";
            $context .= "- **Purpose**: " . ($tableInfo['ai_context']['table_purpose'] ?? 'Data Storage') . "\n";
            $context .= "- **Columns**: " . count($tableInfo['columns'] ?? []) . "\n";
            $context .= "- **Rows**: " . ($tableInfo['row_count'] ?? 0) . "\n";

            if (!empty($tableInfo['ai_context']['key_columns'])) {
                $context .= "- **Key Fields**: " . implode(', ', $tableInfo['ai_context']['key_columns']) . "\n";
            }

            $context .= "\n";
        }

        return $context;
    }

    /**
     * Optimize edilmiş schema context
     */
    private function buildSchemaContextOptimized(array $learningData, array $template): string
    {
        $schemaInfo = $learningData['schema'] ?? [];
        $detailLevel = $template['detail_level'] ?? 'medium';

        if (empty($schemaInfo)) {
            return '';
        }

        $context = "# Database\n\n";
        
        if ($detailLevel === 'minimal') {
            $context .= count($schemaInfo) . " tables available\n";
            return $context;
        }

        $tableLimit = $detailLevel === 'low' ? 5 : 10;
        $importantTables = $this->getImportantTables($schemaInfo, $tableLimit);

        foreach ($importantTables as $tableName => $tableInfo) {
            if ($detailLevel === 'low') {
                $context .= "**{$tableName}**: " . ($tableInfo['ai_context']['table_purpose'] ?? 'Data Storage') . "\n";
            } else {
                $context .= "**{$tableName} Table**\n";
                $context .= "- Purpose: " . ($tableInfo['ai_context']['table_purpose'] ?? 'Data Storage') . "\n";
                $context .= "- Columns: " . count($tableInfo['columns'] ?? []) . "\n";
                
                if ($detailLevel === 'high' || $detailLevel === 'maximum') {
                    if (!empty($tableInfo['ai_context']['key_columns'])) {
                        $context .= "- Key Fields: " . implode(', ', array_slice($tableInfo['ai_context']['key_columns'], 0, 3)) . "\n";
                    }
                }
            }
            
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Relationship context'i oluştur
     */
    private function buildRelationshipContext(array $relationships): string
    {
        if (empty($relationships['ai_context'])) {
            return '';
        }

        return "# Model Relationships\n\n" . $relationships['ai_context'];
    }

    /**
     * Optimize edilmiş relationship context
     */
    private function buildRelationshipContextOptimized(array $learningData, array $template): string
    {
        $relationships = $learningData['relationships'] ?? [];
        $detailLevel = $template['detail_level'] ?? 'medium';

        if (empty($relationships['ai_context'])) {
            return '';
        }

        $context = "# Relationships\n\n";

        if ($detailLevel === 'minimal') {
            $stats = $relationships['statistics'] ?? [];
            $context .= "System has " . ($stats['total_relationships'] ?? 0) . " model relationships\n";
        } elseif ($detailLevel === 'low') {
            $context .= $this->summarizeRelationships($relationships);
        } else {
            $context .= $relationships['ai_context'];
        }

        return $context;
    }

    /**
     * System capabilities context'i
     */
    private function buildCapabilitiesContext(array $activeModules, array $schemaInfo): string
    {
        $capabilities = [];

        // Module-based capabilities
        foreach ($activeModules as $moduleName => $moduleInfo) {
            switch (strtolower($moduleName)) {
                case 'page':
                    $capabilities[] = 'Content Management System';
                    break;
                case 'portfolio':
                    $capabilities[] = 'Portfolio Management';
                    break;
                case 'usermanagement':
                    $capabilities[] = 'User Authentication & Authorization';
                    break;
                case 'announcement':
                    $capabilities[] = 'News & Announcements';
                    break;
                case 'ai':
                    $capabilities[] = 'AI Integration & Automation';
                    break;
                case 'seo':
                case 'seomanagement':
                    $capabilities[] = 'SEO Optimization';
                    break;
            }
        }

        if (empty($capabilities)) {
            return '';
        }

        $context = "# System Capabilities\n\n";
        foreach ($capabilities as $capability) {
            $context .= "- {$capability}\n";
        }

        return $context;
    }

    /**
     * Focus area context'i oluştur
     */
    private function buildFocusAreaContext(array $learningData, array $focusAreas): string
    {
        $context = "# Focus Areas\n\n";

        foreach ($focusAreas as $focusArea) {
            switch ($focusArea) {
                case 'content_management':
                    $context .= $this->buildContentManagementContext($learningData);
                    break;
                case 'user_management':
                    $context .= $this->buildUserManagementContext($learningData);
                    break;
                case 'data_structure':
                    $context .= $this->buildDataStructureContext($learningData);
                    break;
                case 'system_architecture':
                    $context .= $this->buildSystemArchitectureContext($learningData);
                    break;
                case 'model_relationships':
                    $context .= $this->buildModelRelationshipsContext($learningData);
                    break;
                case 'page_structure':
                    $context .= $this->buildPageStructureContext($learningData);
                    break;
            }
        }

        return $context;
    }

    /**
     * Content management focus context
     */
    private function buildContentManagementContext(array $learningData): string
    {
        $modules = $learningData['modules'] ?? [];
        $contentModules = array_intersect_key($modules, array_flip(['Page', 'Portfolio', 'Announcement']));

        if (empty($contentModules)) {
            return '';
        }

        $context = "## Content Management\n";
        foreach ($contentModules as $moduleName => $moduleInfo) {
            $context .= "- **{$moduleName}**: Content creation and management\n";
        }

        return $context . "\n";
    }

    /**
     * User management focus context
     */
    private function buildUserManagementContext(array $learningData): string
    {
        $modules = $learningData['modules'] ?? [];
        
        if (!isset($modules['UserManagement'])) {
            return '';
        }

        return "## User Management\n- User authentication, roles, and permissions\n- Multi-tenant user isolation\n\n";
    }

    /**
     * Data structure focus context
     */
    private function buildDataStructureContext(array $learningData): string
    {
        $schema = $learningData['schema'] ?? [];
        $importantTables = $this->getImportantTables($schema, 5);

        $context = "## Data Structure\n";
        foreach ($importantTables as $tableName => $tableInfo) {
            $context .= "- **{$tableName}**: " . ($tableInfo['ai_context']['table_purpose'] ?? 'Data Storage') . "\n";
        }

        return $context . "\n";
    }

    /**
     * System architecture focus context
     */
    private function buildSystemArchitectureContext(array $learningData): string
    {
        $modules = $learningData['modules'] ?? [];
        
        $context = "## System Architecture\n";
        $context .= "- **Modular Design**: " . count($modules) . " independent modules\n";
        $context .= "- **Multi-tenancy**: Domain-based tenant isolation\n";
        $context .= "- **MVC Pattern**: Laravel framework structure\n";

        return $context . "\n";
    }

    /**
     * Model relationships focus context
     */
    private function buildModelRelationshipsContext(array $learningData): string
    {
        $relationships = $learningData['relationships'] ?? [];
        
        if (empty($relationships['statistics'])) {
            return '';
        }

        $stats = $relationships['statistics'];
        
        $context = "## Model Relationships\n";
        $context .= "- **Total Models**: " . ($stats['total_models'] ?? 0) . "\n";
        $context .= "- **Total Relationships**: " . ($stats['total_relationships'] ?? 0) . "\n";
        $context .= "- **Cross-Module Links**: " . ($stats['cross_module_relationships'] ?? 0) . "\n";

        return $context . "\n";
    }

    /**
     * Page structure focus context
     */
    private function buildPageStructureContext(array $learningData): string
    {
        $modules = $learningData['modules'] ?? [];
        
        if (!isset($modules['Page'])) {
            return '';
        }

        $context = "## Page Structure\n";
        $context .= "- **Dynamic Routing**: Slug-based URL structure\n";
        $context .= "- **SEO Integration**: Meta tags and optimization\n";
        $context .= "- **Multi-language Support**: Translatable content\n";

        return $context . "\n";
    }

    /**
     * Önemli tabloları getir
     */
    private function getImportantTables(array $schemaInfo, int $limit): array
    {
        // Önem skoruna göre sırala
        uasort($schemaInfo, function ($a, $b) {
            return ($b['importance_score'] ?? 0) <=> ($a['importance_score'] ?? 0);
        });

        return array_slice($schemaInfo, 0, $limit, true);
    }

    /**
     * Module önemini hesapla
     */
    private function calculateModuleImportance(string $moduleName, array $moduleInfo): int
    {
        $importance = 0;
        $lowerName = strtolower($moduleName);

        // System keyword'lere göre önem
        foreach (self::SYSTEM_KEYWORDS as $keyword => $score) {
            if (strpos($lowerName, $keyword) !== false) {
                $importance += $score;
            }
        }

        // Model sayısına göre önem
        $importance += count($moduleInfo['models'] ?? []) * 2;

        return $importance;
    }

    /**
     * Relationship'ları özetle
     */
    private function summarizeRelationships(array $relationships): string
    {
        $stats = $relationships['statistics'] ?? [];
        
        $summary = "**Summary**: ";
        $summary .= ($stats['total_models'] ?? 0) . " models with ";
        $summary .= ($stats['total_relationships'] ?? 0) . " relationships";
        
        if (!empty($stats['most_common_relationship'])) {
            $summary .= " (mostly " . $stats['most_common_relationship'] . ")";
        }
        
        return $summary . "\n";
    }

    /**
     * Context'i keserek boyut sınırla
     */
    private function truncateContext(string $context, int $maxLength): string
    {
        if (strlen($context) <= $maxLength) {
            return $context;
        }

        // Paragraf sınırlarında kes
        $truncated = substr($context, 0, $maxLength);
        $lastNewline = strrpos($truncated, "\n");
        
        if ($lastNewline !== false) {
            $truncated = substr($truncated, 0, $lastNewline);
        }

        return $truncated . "\n\n[Context truncated for length...]";
    }

    /**
     * Fallback context
     */
    private function buildFallbackContext(): string
    {
        return "# Laravel CMS System\n\nLaravel 11 based multi-tenant CMS with modular architecture.\nSystem supports content management, user authentication, and AI integration.\n";
    }
}