<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

use Exception;

/**
 * Provider Multiplier Exception - Specialized exception for provider cost system
 * 
 * Bu exception sınıfı provider multiplier sistemindeki hataları yönetir.
 * Farklı hata türleri için özelleştirilmiş factory metodları içerir.
 * 
 * Exception Types:
 * - Provider not found
 * - Invalid multiplier configuration
 * - Cost calculation errors
 * - Budget constraint violations
 * - Performance metric failures
 * 
 * @author Nurullah Okatan
 * @version 2.0
 */
class ProviderMultiplierException extends Exception
{
    // Exception codes for different error types
    public const PROVIDER_NOT_FOUND = 1001;
    public const INVALID_MULTIPLIER = 1002;
    public const COST_CALCULATION_FAILED = 1003;
    public const BUDGET_CONSTRAINT_VIOLATION = 1004;
    public const PERFORMANCE_METRIC_FAILED = 1005;
    public const FEATURE_TYPE_INVALID = 1006;
    public const TENANT_DATA_MISSING = 1007;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?array $context = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Provider not found exception
     */
    public static function providerNotFound(string $providerName, ?array $context = null): self
    {
        return new self(
            message: "Provider '{$providerName}' not found or inactive",
            code: self::PROVIDER_NOT_FOUND,
            context: array_merge(['provider_name' => $providerName], $context ?? [])
        );
    }

    /**
     * Invalid multiplier configuration exception
     */
    public static function invalidMultiplier(string $providerName, float $multiplier, ?array $context = null): self
    {
        return new self(
            message: "Invalid multiplier configuration for provider '{$providerName}': {$multiplier}",
            code: self::INVALID_MULTIPLIER,
            context: array_merge([
                'provider_name' => $providerName,
                'invalid_multiplier' => $multiplier
            ], $context ?? [])
        );
    }

    /**
     * Cost calculation failed exception
     */
    public static function costCalculationFailed(string $reason, ?array $context = null): self
    {
        return new self(
            message: "Cost calculation failed: {$reason}",
            code: self::COST_CALCULATION_FAILED,
            context: array_merge(['failure_reason' => $reason], $context ?? [])
        );
    }

    /**
     * Budget constraint violation exception
     */
    public static function budgetConstraintViolation(float $requiredBudget, float $availableBudget, ?array $context = null): self
    {
        return new self(
            message: "Budget constraint violated. Required: {$requiredBudget}, Available: {$availableBudget}",
            code: self::BUDGET_CONSTRAINT_VIOLATION,
            context: array_merge([
                'required_budget' => $requiredBudget,
                'available_budget' => $availableBudget,
                'shortage' => $requiredBudget - $availableBudget
            ], $context ?? [])
        );
    }

    /**
     * Performance metric calculation failed exception
     */
    public static function performanceMetricFailed(string $providerName, string $metricType, ?array $context = null): self
    {
        return new self(
            message: "Performance metric calculation failed for provider '{$providerName}', metric: {$metricType}",
            code: self::PERFORMANCE_METRIC_FAILED,
            context: array_merge([
                'provider_name' => $providerName,
                'metric_type' => $metricType
            ], $context ?? [])
        );
    }

    /**
     * Invalid feature type exception
     */
    public static function invalidFeatureType(string $featureType, array $validTypes, ?array $context = null): self
    {
        return new self(
            message: "Invalid feature type '{$featureType}'. Valid types: " . implode(', ', $validTypes),
            code: self::FEATURE_TYPE_INVALID,
            context: array_merge([
                'invalid_feature_type' => $featureType,
                'valid_types' => $validTypes
            ], $context ?? [])
        );
    }

    /**
     * Tenant data missing exception
     */
    public static function tenantDataMissing(int $tenantId, string $dataType, ?array $context = null): self
    {
        return new self(
            message: "Tenant data missing for tenant {$tenantId}, data type: {$dataType}",
            code: self::TENANT_DATA_MISSING,
            context: array_merge([
                'tenant_id' => $tenantId,
                'missing_data_type' => $dataType
            ], $context ?? [])
        );
    }

    /**
     * Generic provider multiplier system exception
     */
    public static function withMessage(string $message, ?array $context = null): self
    {
        return new self(
            message: $message,
            context: $context
        );
    }

    /**
     * Get exception context for logging
     */
    public function getContext(): array
    {
        return $this->context ?? [];
    }

    /**
     * Get user-friendly error message
     */
    public function getUserMessage(): string
    {
        return match ($this->getCode()) {
            self::PROVIDER_NOT_FOUND => 'Seçilen AI sağlayıcısı bulunamadı veya aktif değil.',
            self::INVALID_MULTIPLIER => 'AI sağlayıcısının fiyatlandırma konfigürasyonu hatalı.',
            self::COST_CALCULATION_FAILED => 'Maliyet hesaplaması başarısız oldu.',
            self::BUDGET_CONSTRAINT_VIOLATION => 'Bütçe yetersiz. Lütfen kredi satın alın.',
            self::PERFORMANCE_METRIC_FAILED => 'Performans metrikleri hesaplanamadı.',
            self::FEATURE_TYPE_INVALID => 'Geçersiz özellik türü.',
            self::TENANT_DATA_MISSING => 'Kullanıcı verisi eksik.',
            default => 'AI maliyet sistemi hatası oluştu.'
        };
    }

    /**
     * Get technical details for debugging
     */
    public function getTechnicalDetails(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->getContext(),
            'trace' => $this->getTraceAsString()
        ];
    }

    /**
     * Convert to array for API responses
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'error_code' => $this->getCode(),
            'message' => $this->getUserMessage(),
            'technical_message' => $this->getMessage(),
            'context' => $this->getContext(),
            'timestamp' => now()->toISOString()
        ];
    }
}