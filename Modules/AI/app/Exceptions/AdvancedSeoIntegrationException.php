<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

use Exception;
use Throwable;

/**
 * Advanced SEO Integration Exception
 * 
 * Specialized exception class for handling SEO integration system errors.
 * Provides detailed error contexts and factory methods for common error scenarios.
 * 
 * @package Modules\AI\app\Exceptions
 * @author AI V2 System
 * @version 2.0.0
 */
class AdvancedSeoIntegrationException extends Exception
{
    /**
     * Additional context for debugging
     */
    private ?array $context;

    /**
     * Error category for classification
     */
    private string $errorCategory;

    /**
     * User-friendly message for display
     */
    private string $userMessage;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?array $context = null,
        string $errorCategory = 'general',
        string $userMessage = ''
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->context = $context;
        $this->errorCategory = $errorCategory;
        $this->userMessage = $userMessage ?: $message;
    }

    /**
     * Get error context for debugging
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * Get error category
     */
    public function getErrorCategory(): string
    {
        return $this->errorCategory;
    }

    /**
     * Get user-friendly message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Convert exception to array for API responses
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->getUserMessage(),
            'technical_message' => $this->getMessage(),
            'category' => $this->getErrorCategory(),
            'code' => $this->getCode(),
            'context' => $this->getContext(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * SEO analysis failed due to content processing error
     */
    public static function analysisFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: $technicalMessage,
            code: 1001,
            context: $context,
            errorCategory: 'analysis',
            userMessage: 'SEO analysis could not be completed. Please try again or contact support if the problem persists.'
        );
    }

    /**
     * Content data is invalid or missing required fields
     */
    public static function invalidContentData(
        string $missingField, 
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid content data: missing or invalid field '{$missingField}'",
            code: 1002,
            context: $context,
            errorCategory: 'validation',
            userMessage: "Content data is incomplete. Please ensure all required fields are provided."
        );
    }

    /**
     * Unsupported content type provided
     */
    public static function unsupportedContentType(
        string $contentType, 
        array $supportedTypes = []
    ): self {
        return new self(
            message: "Unsupported content type: '{$contentType}'. Supported types: " . implode(', ', $supportedTypes),
            code: 1003,
            context: ['content_type' => $contentType, 'supported_types' => $supportedTypes],
            errorCategory: 'validation',
            userMessage: "This content type is not supported for SEO analysis."
        );
    }

    /**
     * SEO factor analysis failed for specific factor
     */
    public static function factorAnalysisFailure(
        string $factor, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO factor analysis failed for '{$factor}': {$technicalMessage}",
            code: 1004,
            context: array_merge(['factor' => $factor], $context ?? []),
            errorCategory: 'analysis',
            userMessage: "Some SEO analysis features are temporarily unavailable. Results may be incomplete."
        );
    }

    /**
     * Keyword analysis failed due to invalid or missing keywords
     */
    public static function keywordAnalysisFailure(
        string $reason, 
        ?array $context = null
    ): self {
        return new self(
            message: "Keyword analysis failed: {$reason}",
            code: 1005,
            context: $context,
            errorCategory: 'keyword_analysis',
            userMessage: 'Keyword analysis could not be completed. Please check your keyword settings.'
        );
    }

    /**
     * Content parsing failed (HTML, text extraction, etc.)
     */
    public static function contentParsingFailure(
        string $contentType, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Content parsing failed for {$contentType}: {$technicalMessage}",
            code: 1006,
            context: array_merge(['content_type' => $contentType], $context ?? []),
            errorCategory: 'parsing',
            userMessage: 'Content could not be processed for SEO analysis. Please check the content format.'
        );
    }

    /**
     * SEO dashboard data retrieval failed
     */
    public static function dashboardFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: $technicalMessage,
            code: 1007,
            context: $context,
            errorCategory: 'dashboard',
            userMessage: 'SEO dashboard data is temporarily unavailable. Please refresh the page.'
        );
    }

    /**
     * Competitive analysis failed due to API or data issues
     */
    public static function competitiveAnalysisFailure(
        string $reason, 
        ?array $context = null
    ): self {
        return new self(
            message: "Competitive analysis failed: {$reason}",
            code: 1008,
            context: $context,
            errorCategory: 'competitive_analysis',
            userMessage: 'Competitive analysis is temporarily unavailable. Basic SEO analysis will still be provided.'
        );
    }

    /**
     * Schema markup analysis failed
     */
    public static function schemaAnalysisFailure(
        string $contentType, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Schema markup analysis failed for {$contentType}: {$technicalMessage}",
            code: 1009,
            context: array_merge(['content_type' => $contentType], $context ?? []),
            errorCategory: 'schema_analysis',
            userMessage: 'Schema markup analysis encountered an issue. Other SEO factors will still be analyzed.'
        );
    }

    /**
     * Image optimization analysis failed
     */
    public static function imageAnalysisFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Image optimization analysis failed: {$technicalMessage}",
            code: 1010,
            context: $context,
            errorCategory: 'image_analysis',
            userMessage: 'Image optimization analysis is temporarily unavailable.'
        );
    }

    /**
     * URL structure analysis failed
     */
    public static function urlAnalysisFailure(
        string $slug, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "URL analysis failed for slug '{$slug}': {$technicalMessage}",
            code: 1011,
            context: array_merge(['slug' => $slug], $context ?? []),
            errorCategory: 'url_analysis',
            userMessage: 'URL structure analysis encountered an issue.'
        );
    }

    /**
     * Content quality analysis failed
     */
    public static function contentQualityAnalysisFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Content quality analysis failed: {$technicalMessage}",
            code: 1012,
            context: $context,
            errorCategory: 'content_quality',
            userMessage: 'Content quality analysis is temporarily unavailable.'
        );
    }

    /**
     * Internal linking analysis failed
     */
    public static function linkAnalysisFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Internal linking analysis failed: {$technicalMessage}",
            code: 1013,
            context: $context,
            errorCategory: 'link_analysis',
            userMessage: 'Link structure analysis encountered an issue.'
        );
    }

    /**
     * Mobile optimization analysis failed
     */
    public static function mobileAnalysisFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Mobile optimization analysis failed: {$technicalMessage}",
            code: 1014,
            context: $context,
            errorCategory: 'mobile_analysis',
            userMessage: 'Mobile optimization analysis is temporarily unavailable.'
        );
    }

    /**
     * SEO scoring calculation failed
     */
    public static function scoringFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO scoring calculation failed: {$technicalMessage}",
            code: 1015,
            context: $context,
            errorCategory: 'scoring',
            userMessage: 'SEO score calculation encountered an issue. Individual factor analysis is still available.'
        );
    }

    /**
     * SEO suggestions generation failed
     */
    public static function suggestionGenerationFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO suggestions generation failed: {$technicalMessage}",
            code: 1016,
            context: $context,
            errorCategory: 'suggestions',
            userMessage: 'SEO recommendations are temporarily unavailable. Analysis results are still provided.'
        );
    }

    /**
     * Optimization roadmap creation failed
     */
    public static function roadmapCreationFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Optimization roadmap creation failed: {$technicalMessage}",
            code: 1017,
            context: $context,
            errorCategory: 'roadmap',
            userMessage: 'Optimization roadmap is temporarily unavailable. Individual recommendations are still provided.'
        );
    }

    /**
     * Cache system failure during SEO analysis
     */
    public static function cacheFailure(
        string $operation, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Cache operation '{$operation}' failed: {$technicalMessage}",
            code: 1018,
            context: array_merge(['operation' => $operation], $context ?? []),
            errorCategory: 'cache',
            userMessage: 'System performance may be affected, but analysis will continue.'
        );
    }

    /**
     * Database query failure during SEO analysis
     */
    public static function databaseFailure(
        string $query, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Database query failed: {$technicalMessage}",
            code: 1019,
            context: array_merge(['query_type' => $query], $context ?? []),
            errorCategory: 'database',
            userMessage: 'Data retrieval temporarily unavailable. Please try again.'
        );
    }

    /**
     * Configuration error (missing settings, invalid configuration)
     */
    public static function configurationError(
        string $setting, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "Configuration error for '{$setting}': {$technicalMessage}",
            code: 1020,
            context: array_merge(['setting' => $setting], $context ?? []),
            errorCategory: 'configuration',
            userMessage: 'System configuration issue detected. Please contact administrator.'
        );
    }

    /**
     * Rate limiting exceeded for SEO analysis
     */
    public static function rateLimitExceeded(
        int $limit, 
        int $windowMinutes, 
        ?array $context = null
    ): self {
        return new self(
            message: "Rate limit exceeded: {$limit} requests per {$windowMinutes} minutes",
            code: 1021,
            context: array_merge([
                'limit' => $limit, 
                'window_minutes' => $windowMinutes
            ], $context ?? []),
            errorCategory: 'rate_limit',
            userMessage: "Too many SEO analysis requests. Please wait {$windowMinutes} minutes before trying again."
        );
    }

    /**
     * External service failure (APIs for competitive analysis, etc.)
     */
    public static function externalServiceFailure(
        string $serviceName, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "External service '{$serviceName}' failed: {$technicalMessage}",
            code: 1022,
            context: array_merge(['service' => $serviceName], $context ?? []),
            errorCategory: 'external_service',
            userMessage: 'Some advanced features are temporarily unavailable due to external service issues.'
        );
    }

    /**
     * Permission denied for SEO analysis
     */
    public static function permissionDenied(
        string $requiredPermission, 
        ?array $context = null
    ): self {
        return new self(
            message: "Permission denied: requires '{$requiredPermission}' permission",
            code: 1023,
            context: array_merge(['required_permission' => $requiredPermission], $context ?? []),
            errorCategory: 'permission',
            userMessage: 'You do not have permission to perform this SEO analysis.'
        );
    }

    /**
     * Timeout during SEO analysis processing
     */
    public static function analysisTimeout(
        int $timeoutSeconds, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO analysis timed out after {$timeoutSeconds} seconds",
            code: 1024,
            context: array_merge(['timeout_seconds' => $timeoutSeconds], $context ?? []),
            errorCategory: 'timeout',
            userMessage: 'Analysis is taking longer than expected. Please try again or contact support.'
        );
    }

    /**
     * Memory limit exceeded during analysis
     */
    public static function memoryLimitExceeded(
        string $memoryUsed, 
        string $memoryLimit, 
        ?array $context = null
    ): self {
        return new self(
            message: "Memory limit exceeded: used {$memoryUsed}, limit {$memoryLimit}",
            code: 1025,
            context: array_merge([
                'memory_used' => $memoryUsed,
                'memory_limit' => $memoryLimit
            ], $context ?? []),
            errorCategory: 'memory',
            userMessage: 'Content is too large for analysis. Please try with smaller content or contact support.'
        );
    }

    /**
     * Invalid analysis options provided
     */
    public static function invalidAnalysisOptions(
        array $invalidOptions, 
        array $validOptions = [], 
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid analysis options: " . implode(', ', $invalidOptions),
            code: 1026,
            context: array_merge([
                'invalid_options' => $invalidOptions,
                'valid_options' => $validOptions
            ], $context ?? []),
            errorCategory: 'validation',
            userMessage: 'Invalid analysis settings provided. Please check your configuration.'
        );
    }

    /**
     * Automated recommendation generation failed
     */
    public static function recommendationFailure(
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: $technicalMessage,
            code: 1027,
            context: $context,
            errorCategory: 'recommendations',
            userMessage: 'Automated recommendations are temporarily unavailable. Manual analysis results are still provided.'
        );
    }

    /**
     * Content ID not found or invalid
     */
    public static function contentNotFound(
        int $contentId, 
        ?array $context = null
    ): self {
        return new self(
            message: "Content with ID {$contentId} not found or inaccessible",
            code: 1028,
            context: array_merge(['content_id' => $contentId], $context ?? []),
            errorCategory: 'content_access',
            userMessage: 'The requested content could not be found or you do not have access to it.'
        );
    }

    /**
     * SEO template processing failed
     */
    public static function templateProcessingFailure(
        string $templateName, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO template '{$templateName}' processing failed: {$technicalMessage}",
            code: 1029,
            context: array_merge(['template' => $templateName], $context ?? []),
            errorCategory: 'template',
            userMessage: 'SEO template processing encountered an issue. Default analysis will be used.'
        );
    }

    /**
     * Report generation failed
     */
    public static function reportGenerationFailure(
        string $reportType, 
        string $technicalMessage, 
        ?array $context = null
    ): self {
        return new self(
            message: "SEO report generation failed for type '{$reportType}': {$technicalMessage}",
            code: 1030,
            context: array_merge(['report_type' => $reportType], $context ?? []),
            errorCategory: 'reporting',
            userMessage: 'Report generation is temporarily unavailable. Analysis data is still accessible.'
        );
    }
}