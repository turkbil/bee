<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

use Exception;

/**
 * Database Learning Exception V2
 * 
 * Database Learning System için özel exception sınıfları
 * 
 * @package Modules\AI\app\Exceptions
 * @author AI V2 System
 * @version 2.0.0
 */
abstract class DatabaseLearningException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?array $context = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * System learning failed exception
 */
class SystemLearningFailedException extends DatabaseLearningException
{
    public static function systemLearningFailed(string $reason): self
    {
        return new self(
            message: "Database learning system failed: {$reason}",
            context: ['reason' => $reason]
        );
    }
}

/**
 * Module discovery failed exception  
 */
class ModuleDiscoveryFailedException extends DatabaseLearningException
{
    public static function moduleDiscoveryFailed(string $reason): self
    {
        return new self(
            message: "Module discovery failed: {$reason}",
            context: ['reason' => $reason]
        );
    }
}

/**
 * Schema analysis failed exception
 */
class SchemaAnalysisFailedException extends DatabaseLearningException
{
    public static function schemaAnalysisFailed(string $tableName, string $reason): self
    {
        return new self(
            message: "Schema analysis failed for table '{$tableName}': {$reason}",
            context: ['table' => $tableName, 'reason' => $reason]
        );
    }
}

/**
 * Context building failed exception
 */
class ContextBuildingFailedException extends DatabaseLearningException
{
    public static function contextBuildingFailed(string $featureType, string $reason): self
    {
        return new self(
            message: "Context building failed for feature '{$featureType}': {$reason}",
            context: ['feature_type' => $featureType, 'reason' => $reason]
        );
    }
}