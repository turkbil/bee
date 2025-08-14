<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Schema Learner V2
 * 
 * Veritabanı şemasını akıllı şekilde analiz eder
 * Tablolar, kolonlar, indexler ve constraints'leri öğrenir
 * AI prompts için optimize edilmiş schema bilgisi üretir
 * 
 * Features:
 * - Complete table structure analysis
 * - Column types and constraints detection  
 * - Index and foreign key mapping
 * - Data type intelligence
 * - Performance optimized queries
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class SchemaLearner
{
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
        'job_batches',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring'
    ];

    /**
     * Important column patterns
     */
    private const IMPORTANT_PATTERNS = [
        'id' => 'Primary Key',
        'uuid' => 'UUID Identifier',
        'slug' => 'URL Slug',
        'name' => 'Name Field',
        'title' => 'Title Field', 
        'description' => 'Description Field',
        'content' => 'Content Field',
        'email' => 'Email Field',
        'password' => 'Password Field',
        'status' => 'Status Field',
        'active' => 'Active Flag',
        'published' => 'Published Flag',
        'created_at' => 'Creation Timestamp',
        'updated_at' => 'Update Timestamp',
        'deleted_at' => 'Soft Delete Timestamp'
    ];

    public function __construct()
    {
    }

    /**
     * Ana schema analiz işlemi
     */
    public function analyzeSchema(): array
    {
        try {
            Log::info('[Schema Learner V2] Starting schema analysis');

            $tables = $this->getAllTables();
            $schemaInfo = [];

            foreach ($tables as $tableName) {
                // System tablolarını atla
                if (in_array($tableName, self::EXCLUDED_TABLES)) {
                    continue;
                }

                $tableInfo = $this->analyzeTable($tableName);
                if ($tableInfo) {
                    $schemaInfo[$tableName] = $tableInfo;
                }
            }

            Log::info('[Schema Learner V2] Schema analysis completed', [
                'total_tables' => count($tables),
                'analyzed_tables' => count($schemaInfo)
            ]);

            return $schemaInfo;

        } catch (Exception $e) {
            Log::error('[Schema Learner V2] Schema analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Tüm tabloları al
     */
    private function getAllTables(): array
    {
        try {
            $tables = Schema::getAllTables();
            
            // Laravel'in table structure'ına göre tablo isimlerini çıkar
            return array_map(function ($table) {
                // PostgreSQL format
                if (isset($table->tablename)) {
                    return $table->tablename;
                }
                // MySQL format  
                if (isset($table->{'Tables_in_' . DB::getDatabaseName()})) {
                    return $table->{'Tables_in_' . DB::getDatabaseName()};
                }
                // SQLite format
                if (isset($table->name)) {
                    return $table->name;
                }
                // Fallback - object to array conversion
                $tableArray = (array) $table;
                return reset($tableArray);
            }, $tables);

        } catch (Exception $e) {
            Log::error('[Schema Learner] Failed to get all tables', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Tek bir tabloyu analiz et
     */
    private function analyzeTable(string $tableName): ?array
    {
        try {
            if (!Schema::hasTable($tableName)) {
                return null;
            }

            $tableInfo = [
                'name' => $tableName,
                'columns' => [],
                'indexes' => [],
                'foreign_keys' => [],
                'row_count' => 0,
                'table_size' => 0,
                'importance_score' => 0,
                'ai_context' => []
            ];

            // Kolon bilgilerini analiz et
            $columns = $this->getTableColumns($tableName);
            foreach ($columns as $column) {
                $columnInfo = $this->analyzeColumn($tableName, $column);
                if ($columnInfo) {
                    $tableInfo['columns'][$column] = $columnInfo;
                }
            }

            // Index'leri analiz et
            $tableInfo['indexes'] = $this->getTableIndexes($tableName);

            // Foreign key'leri analiz et
            $tableInfo['foreign_keys'] = $this->getTableForeignKeys($tableName);

            // Tablo istatistiklerini al
            $stats = $this->getTableStats($tableName);
            $tableInfo['row_count'] = $stats['row_count'];
            $tableInfo['table_size'] = $stats['table_size'];

            // Önem skoru hesapla
            $tableInfo['importance_score'] = $this->calculateTableImportance($tableInfo);

            // AI context bilgisi oluştur
            $tableInfo['ai_context'] = $this->generateTableAIContext($tableInfo);

            return $tableInfo;

        } catch (Exception $e) {
            Log::error('[Schema Learner] Table analysis failed', [
                'table' => $tableName,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Tablo kolonlarını al
     */
    private function getTableColumns(string $tableName): array
    {
        try {
            return Schema::getColumnListing($tableName);
        } catch (Exception $e) {
            Log::error('[Schema Learner] Failed to get columns', [
                'table' => $tableName,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Kolon bilgisini analiz et
     */
    private function analyzeColumn(string $tableName, string $columnName): ?array
    {
        try {
            // DB-specific column information
            $columnInfo = $this->getColumnDetails($tableName, $columnName);
            
            if (!$columnInfo) {
                return null;
            }

            // Pattern recognition
            $pattern = $this->recognizeColumnPattern($columnName, $columnInfo);
            
            // AI importance
            $importance = $this->calculateColumnImportance($columnName, $columnInfo);

            return [
                'name' => $columnName,
                'type' => $columnInfo['type'] ?? 'unknown',
                'nullable' => $columnInfo['nullable'] ?? true,
                'default' => $columnInfo['default'] ?? null,
                'key' => $columnInfo['key'] ?? null,
                'extra' => $columnInfo['extra'] ?? null,
                'length' => $columnInfo['length'] ?? null,
                'pattern' => $pattern,
                'importance' => $importance,
                'ai_description' => $this->generateColumnAIDescription($columnName, $columnInfo, $pattern)
            ];

        } catch (Exception $e) {
            Log::error('[Schema Learner] Column analysis failed', [
                'table' => $tableName,
                'column' => $columnName,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Kolon detaylarını al (DB-specific)
     */
    private function getColumnDetails(string $tableName, string $columnName): ?array
    {
        try {
            $query = "
                SELECT 
                    COLUMN_NAME as name,
                    DATA_TYPE as type,
                    IS_NULLABLE as nullable,
                    COLUMN_DEFAULT as `default`,
                    COLUMN_KEY as `key`,
                    EXTRA as extra,
                    CHARACTER_MAXIMUM_LENGTH as length
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = ? 
                AND COLUMN_NAME = ?
                AND TABLE_SCHEMA = ?
            ";

            $result = DB::select($query, [$tableName, $columnName, DB::getDatabaseName()]);
            
            if (empty($result)) {
                return null;
            }

            $column = $result[0];
            
            return [
                'type' => $column->type,
                'nullable' => $column->nullable === 'YES',
                'default' => $column->default,
                'key' => $column->key,
                'extra' => $column->extra,
                'length' => $column->length
            ];

        } catch (Exception $e) {
            // Fallback: basic type detection
            return [
                'type' => 'unknown',
                'nullable' => true,
                'default' => null,
                'key' => null,
                'extra' => null,
                'length' => null
            ];
        }
    }

    /**
     * Kolon pattern'ini tanı
     */
    private function recognizeColumnPattern(string $columnName, array $columnInfo): ?string
    {
        $lowerColumn = strtolower($columnName);
        
        // Direct matches
        if (isset(self::IMPORTANT_PATTERNS[$lowerColumn])) {
            return self::IMPORTANT_PATTERNS[$lowerColumn];
        }

        // Pattern matching
        foreach (self::IMPORTANT_PATTERNS as $pattern => $description) {
            if (strpos($lowerColumn, $pattern) !== false) {
                return $description;
            }
        }

        // Type-based patterns
        $type = strtolower($columnInfo['type'] ?? '');
        
        if (in_array($type, ['text', 'longtext', 'mediumtext'])) {
            return 'Content Field';
        }
        
        if (in_array($type, ['json'])) {
            return 'JSON Data';
        }

        if (in_array($type, ['timestamp', 'datetime'])) {
            return 'Timestamp Field';
        }

        if (in_array($type, ['boolean', 'tinyint(1)'])) {
            return 'Boolean Flag';
        }

        return null;
    }

    /**
     * Kolon önemini hesapla
     */
    private function calculateColumnImportance(string $columnName, array $columnInfo): int
    {
        $importance = 0;
        $lowerColumn = strtolower($columnName);

        // Primary key
        if (($columnInfo['key'] ?? '') === 'PRI') {
            $importance += 100;
        }

        // Important patterns
        if (isset(self::IMPORTANT_PATTERNS[$lowerColumn])) {
            $importance += 50;
        }

        // Foreign keys
        if (($columnInfo['key'] ?? '') === 'MUL') {
            $importance += 30;
        }

        // Required fields
        if (!($columnInfo['nullable'] ?? true)) {
            $importance += 20;
        }

        // Content fields
        $type = strtolower($columnInfo['type'] ?? '');
        if (in_array($type, ['text', 'longtext', 'json'])) {
            $importance += 25;
        }

        return $importance;
    }

    /**
     * Tablo index'lerini al
     */
    private function getTableIndexes(string $tableName): array
    {
        try {
            $query = "SHOW INDEXES FROM `{$tableName}`";
            $indexes = DB::select($query);
            
            $indexInfo = [];
            foreach ($indexes as $index) {
                $indexName = $index->Key_name;
                
                if (!isset($indexInfo[$indexName])) {
                    $indexInfo[$indexName] = [
                        'name' => $indexName,
                        'unique' => !$index->Non_unique,
                        'type' => $index->Index_type,
                        'columns' => []
                    ];
                }
                
                $indexInfo[$indexName]['columns'][] = $index->Column_name;
            }
            
            return array_values($indexInfo);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Foreign key'leri al
     */
    private function getTableForeignKeys(string $tableName): array
    {
        try {
            $query = "
                SELECT 
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as referenced_table,
                    REFERENCED_COLUMN_NAME as referenced_column,
                    CONSTRAINT_NAME as constraint_name
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
                AND TABLE_SCHEMA = ?
            ";

            $foreignKeys = DB::select($query, [$tableName, DB::getDatabaseName()]);
            
            $fkInfo = [];
            foreach ($foreignKeys as $fk) {
                $fkInfo[] = [
                    'column' => $fk->column_name,
                    'referenced_table' => $fk->referenced_table,
                    'referenced_column' => $fk->referenced_column,
                    'constraint' => $fk->constraint_name
                ];
            }
            
            return $fkInfo;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Tablo istatistiklerini al
     */
    private function getTableStats(string $tableName): array
    {
        try {
            // Row count
            $rowCount = DB::table($tableName)->count();
            
            // Table size (approximate)
            $tableSizeQuery = "
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ? 
                AND table_name = ?
            ";
            
            $sizeResult = DB::select($tableSizeQuery, [DB::getDatabaseName(), $tableName]);
            $tableSize = $sizeResult[0]->size_mb ?? 0;

            return [
                'row_count' => $rowCount,
                'table_size' => (float) $tableSize
            ];

        } catch (Exception $e) {
            return [
                'row_count' => 0,
                'table_size' => 0
            ];
        }
    }

    /**
     * Tablo önemini hesapla
     */
    private function calculateTableImportance(array $tableInfo): int
    {
        $importance = 0;
        
        // Row count factor
        $rowCount = $tableInfo['row_count'] ?? 0;
        if ($rowCount > 1000) $importance += 30;
        elseif ($rowCount > 100) $importance += 20;
        elseif ($rowCount > 0) $importance += 10;

        // Column count factor
        $columnCount = count($tableInfo['columns'] ?? []);
        $importance += min($columnCount * 5, 50);

        // Foreign key factor
        $fkCount = count($tableInfo['foreign_keys'] ?? []);
        $importance += $fkCount * 10;

        // Important columns factor
        foreach ($tableInfo['columns'] ?? [] as $column) {
            $importance += ($column['importance'] ?? 0) / 10;
        }

        return (int) $importance;
    }

    /**
     * Kolon AI açıklaması oluştur
     */
    private function generateColumnAIDescription(string $columnName, array $columnInfo, ?string $pattern): string
    {
        $description = $columnName;
        
        if ($pattern) {
            $description .= " ({$pattern})";
        }
        
        $type = $columnInfo['type'] ?? 'unknown';
        $description .= " - Type: {$type}";
        
        if (!($columnInfo['nullable'] ?? true)) {
            $description .= " (Required)";
        }
        
        if ($columnInfo['key'] === 'PRI') {
            $description .= " (Primary Key)";
        }
        
        if ($columnInfo['key'] === 'MUL') {
            $description .= " (Foreign Key)";
        }

        return $description;
    }

    /**
     * Tablo AI context'i oluştur
     */
    private function generateTableAIContext(array $tableInfo): array
    {
        $context = [
            'table_purpose' => $this->guessTablePurpose($tableInfo),
            'key_columns' => [],
            'relationships' => [],
            'data_types' => []
        ];

        // Key columns
        foreach ($tableInfo['columns'] ?? [] as $column) {
            if (($column['importance'] ?? 0) > 50) {
                $context['key_columns'][] = $column['ai_description'];
            }
        }

        // Relationships
        foreach ($tableInfo['foreign_keys'] ?? [] as $fk) {
            $context['relationships'][] = "References {$fk['referenced_table']}.{$fk['referenced_column']} via {$fk['column']}";
        }

        return $context;
    }

    /**
     * Tablo amacını tahmin et
     */
    private function guessTablePurpose(array $tableInfo): string
    {
        $tableName = strtolower($tableInfo['name']);
        
        // Common patterns
        if (strpos($tableName, 'user') !== false) return 'User Management';
        if (strpos($tableName, 'page') !== false) return 'Content Management';
        if (strpos($tableName, 'post') !== false) return 'Content Publishing';
        if (strpos($tableName, 'product') !== false) return 'E-commerce';
        if (strpos($tableName, 'order') !== false) return 'Order Management';
        if (strpos($tableName, 'setting') !== false) return 'Configuration';
        if (strpos($tableName, 'log') !== false) return 'Logging/Audit';
        if (strpos($tableName, 'cache') !== false) return 'Caching';
        if (strpos($tableName, 'session') !== false) return 'Session Management';
        if (strpos($tableName, 'permission') !== false) return 'Authorization';
        if (strpos($tableName, 'role') !== false) return 'Authorization';

        // Column-based detection
        $columns = array_keys($tableInfo['columns'] ?? []);
        
        if (in_array('email', $columns) && in_array('password', $columns)) {
            return 'User Authentication';
        }
        
        if (in_array('title', $columns) && in_array('content', $columns)) {
            return 'Content Management';
        }

        return 'Data Storage';
    }
}