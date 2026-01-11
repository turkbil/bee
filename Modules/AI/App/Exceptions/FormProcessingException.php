<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

/**
 * Form Processing Exception
 * Specific exception for form processing errors in the Universal Input System
 * 
 * @package Modules\AI\Exceptions
 * @version 3.0.0
 */
class FormProcessingException extends UniversalInputSystemException
{
    protected string $severity = 'error';
    protected int $statusCode = 422;

    /**
     * Create exception for invalid form structure
     */
    public static function invalidFormStructure(int $featureId, array $errors = []): self
    {
        return new self(
            message: "Invalid form structure for feature ID: {$featureId}",
            userMessage: __('ai::errors.invalid_form_structure'),
            context: [
                'feature_id' => $featureId,
                'structure_errors' => $errors,
                'error_type' => 'invalid_form_structure'
            ],
            code: 4001
        );
    }

    /**
     * Create exception for validation failures
     */
    public static function validationFailed(array $errors, int $featureId = null): self
    {
        return new self(
            message: "Form validation failed with " . count($errors) . " errors",
            userMessage: __('ai::errors.validation_failed'),
            context: [
                'feature_id' => $featureId,
                'validation_errors' => $errors,
                'error_count' => count($errors),
                'error_type' => 'validation_failed'
            ],
            code: 4002
        );
    }

    /**
     * Create exception for AI processing failures
     */
    public static function aiProcessingFailed(string $reason, int $featureId = null, array $inputData = []): self
    {
        return new self(
            message: "AI processing failed: {$reason}",
            userMessage: __('ai::errors.ai_processing_failed'),
            context: [
                'feature_id' => $featureId,
                'failure_reason' => $reason,
                'input_data_size' => count($inputData),
                'error_type' => 'ai_processing_failed'
            ],
            code: 5001
        );
    }

    /**
     * Create exception for file upload failures
     */
    public static function fileUploadFailed(string $filename, string $reason): self
    {
        return new self(
            message: "File upload failed for '{$filename}': {$reason}",
            userMessage: __('ai::errors.file_upload_failed', ['filename' => $filename]),
            context: [
                'filename' => $filename,
                'failure_reason' => $reason,
                'error_type' => 'file_upload_failed'
            ],
            code: 4003
        );
    }

    /**
     * Create exception for missing required fields
     */
    public static function missingRequiredFields(array $missingFields, int $featureId = null): self
    {
        return new self(
            message: "Missing required fields: " . implode(', ', $missingFields),
            userMessage: __('ai::errors.missing_required_fields'),
            context: [
                'feature_id' => $featureId,
                'missing_fields' => $missingFields,
                'error_type' => 'missing_required_fields'
            ],
            code: 4004
        );
    }

    /**
     * Create exception for input size exceeded
     */
    public static function inputSizeExceeded(string $fieldName, int $actualSize, int $maxSize): self
    {
        return new self(
            message: "Input size exceeded for field '{$fieldName}': {$actualSize} bytes (max: {$maxSize})",
            userMessage: __('ai::errors.input_size_exceeded', [
                'field' => $fieldName,
                'max_size' => self::formatBytes($maxSize)
            ]),
            context: [
                'field_name' => $fieldName,
                'actual_size' => $actualSize,
                'max_size' => $maxSize,
                'error_type' => 'input_size_exceeded'
            ],
            code: 4005
        );
    }

    /**
     * Create exception for unsupported input type
     */
    public static function unsupportedInputType(string $inputType, int $featureId = null): self
    {
        return new self(
            message: "Unsupported input type: {$inputType}",
            userMessage: __('ai::errors.unsupported_input_type', ['type' => $inputType]),
            context: [
                'feature_id' => $featureId,
                'input_type' => $inputType,
                'error_type' => 'unsupported_input_type'
            ],
            code: 4006
        );
    }

    /**
     * Create exception for form submission timeout
     */
    public static function submissionTimeout(int $timeoutSeconds, int $featureId = null): self
    {
        return new self(
            message: "Form submission timed out after {$timeoutSeconds} seconds",
            userMessage: __('ai::errors.submission_timeout'),
            context: [
                'feature_id' => $featureId,
                'timeout_seconds' => $timeoutSeconds,
                'error_type' => 'submission_timeout'
            ],
            code: 5002
        );
    }

    /**
     * Create exception for rate limit exceeded
     */
    public static function rateLimitExceeded(int $maxAttempts, int $decayMinutes): self
    {
        return new self(
            message: "Rate limit exceeded: {$maxAttempts} attempts per {$decayMinutes} minutes",
            userMessage: __('ai::errors.rate_limit_exceeded', [
                'minutes' => $decayMinutes
            ]),
            context: [
                'max_attempts' => $maxAttempts,
                'decay_minutes' => $decayMinutes,
                'error_type' => 'rate_limit_exceeded'
            ],
            code: 4007
        );
    }

    /**
     * Create exception for concurrent submission limit
     */
    public static function concurrentLimitExceeded(int $maxConcurrent): self
    {
        return new self(
            message: "Concurrent submission limit exceeded: {$maxConcurrent}",
            userMessage: __('ai::errors.concurrent_limit_exceeded'),
            context: [
                'max_concurrent' => $maxConcurrent,
                'error_type' => 'concurrent_limit_exceeded'
            ],
            code: 4008
        );
    }

    /**
     * Create exception for malformed input data
     */
    public static function malformedInputData(string $reason, array $inputSample = []): self
    {
        return new self(
            message: "Malformed input data: {$reason}",
            userMessage: __('ai::errors.malformed_input_data'),
            context: [
                'malformation_reason' => $reason,
                'input_sample' => array_slice($inputSample, 0, 5), // First 5 items only
                'error_type' => 'malformed_input_data'
            ],
            code: 4009
        );
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}