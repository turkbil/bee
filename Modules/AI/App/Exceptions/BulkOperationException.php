<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

/**
 * Bulk Operation Exception
 * Specific exception for bulk operation errors in the Universal Input System
 * 
 * @package Modules\AI\Exceptions
 * @version 3.0.0
 */
class BulkOperationException extends UniversalInputSystemException
{
    protected string $severity = 'error';
    protected int $statusCode = 422;

    /**
     * Create exception for bulk operation creation failure
     */
    public static function creationFailed(string $reason, int $itemCount = 0): self
    {
        return new self(
            message: "Bulk operation creation failed: {$reason}",
            userMessage: __('ai::errors.bulk_operation_creation_failed'),
            context: [
                'failure_reason' => $reason,
                'item_count' => $itemCount,
                'error_type' => 'bulk_creation_failed'
            ],
            code: 5003
        );
    }

    /**
     * Create exception for operation not found
     */
    public static function operationNotFound(string $operationId): self
    {
        return new self(
            message: "Bulk operation not found: {$operationId}",
            userMessage: __('ai::errors.bulk_operation_not_found'),
            context: [
                'operation_id' => $operationId,
                'error_type' => 'operation_not_found'
            ],
            code: 4010
        )->withStatusCode(404);
    }

    /**
     * Create exception for operation processing failure
     */
    public static function processingFailed(string $operationId, string $reason, int $processedCount = 0): self
    {
        return new self(
            message: "Bulk operation processing failed for {$operationId}: {$reason}",
            userMessage: __('ai::errors.bulk_processing_failed'),
            context: [
                'operation_id' => $operationId,
                'failure_reason' => $reason,
                'processed_count' => $processedCount,
                'error_type' => 'processing_failed'
            ],
            code: 5004
        );
    }

    /**
     * Create exception for operation timeout
     */
    public static function operationTimeout(string $operationId, int $timeoutSeconds): self
    {
        return new self(
            message: "Bulk operation timed out after {$timeoutSeconds} seconds: {$operationId}",
            userMessage: __('ai::errors.bulk_operation_timeout'),
            context: [
                'operation_id' => $operationId,
                'timeout_seconds' => $timeoutSeconds,
                'error_type' => 'operation_timeout'
            ],
            code: 5005
        );
    }

    /**
     * Create exception for invalid item data
     */
    public static function invalidItemData(int $itemIndex, array $errors = []): self
    {
        return new self(
            message: "Invalid item data at index {$itemIndex}",
            userMessage: __('ai::errors.bulk_invalid_item_data', ['index' => $itemIndex]),
            context: [
                'item_index' => $itemIndex,
                'validation_errors' => $errors,
                'error_type' => 'invalid_item_data'
            ],
            code: 4011
        );
    }

    /**
     * Create exception for maximum items exceeded
     */
    public static function maxItemsExceeded(int $itemCount, int $maxItems): self
    {
        return new self(
            message: "Maximum items exceeded: {$itemCount} items (max: {$maxItems})",
            userMessage: __('ai::errors.bulk_max_items_exceeded', [
                'count' => $itemCount,
                'max' => $maxItems
            ]),
            context: [
                'item_count' => $itemCount,
                'max_items' => $maxItems,
                'error_type' => 'max_items_exceeded'
            ],
            code: 4012
        );
    }

    /**
     * Create exception for queue processing error
     */
    public static function queueProcessingError(string $operationId, string $queueError): self
    {
        return new self(
            message: "Queue processing error for operation {$operationId}: {$queueError}",
            userMessage: __('ai::errors.bulk_queue_error'),
            context: [
                'operation_id' => $operationId,
                'queue_error' => $queueError,
                'error_type' => 'queue_processing_error'
            ],
            code: 5006
        );
    }

    /**
     * Create exception for operation cancellation failure
     */
    public static function cancellationFailed(string $operationId, string $reason): self
    {
        return new self(
            message: "Failed to cancel operation {$operationId}: {$reason}",
            userMessage: __('ai::errors.bulk_cancellation_failed'),
            context: [
                'operation_id' => $operationId,
                'failure_reason' => $reason,
                'error_type' => 'cancellation_failed'
            ],
            code: 5007
        );
    }

    /**
     * Create exception for retry limit exceeded
     */
    public static function retryLimitExceeded(string $operationId, int $maxRetries): self
    {
        return new self(
            message: "Retry limit exceeded for operation {$operationId}: {$maxRetries} attempts",
            userMessage: __('ai::errors.bulk_retry_limit_exceeded'),
            context: [
                'operation_id' => $operationId,
                'max_retries' => $maxRetries,
                'error_type' => 'retry_limit_exceeded'
            ],
            code: 5008
        );
    }

    /**
     * Create exception for memory limit exceeded during processing
     */
    public static function memoryLimitExceeded(string $operationId, int $memoryUsage, int $memoryLimit): self
    {
        return new self(
            message: "Memory limit exceeded for operation {$operationId}: {$memoryUsage}MB (limit: {$memoryLimit}MB)",
            userMessage: __('ai::errors.bulk_memory_limit_exceeded'),
            context: [
                'operation_id' => $operationId,
                'memory_usage' => $memoryUsage,
                'memory_limit' => $memoryLimit,
                'error_type' => 'memory_limit_exceeded'
            ],
            code: 5009
        );
    }

    /**
     * Create exception for concurrent operation limit
     */
    public static function concurrentOperationLimit(int $activeOperations, int $maxConcurrent): self
    {
        return new self(
            message: "Concurrent operation limit exceeded: {$activeOperations} active (max: {$maxConcurrent})",
            userMessage: __('ai::errors.bulk_concurrent_limit'),
            context: [
                'active_operations' => $activeOperations,
                'max_concurrent' => $maxConcurrent,
                'error_type' => 'concurrent_operation_limit'
            ],
            code: 4013
        );
    }

    /**
     * Create exception for invalid operation state
     */
    public static function invalidOperationState(string $operationId, string $currentState, string $requiredState): self
    {
        return new self(
            message: "Invalid operation state for {$operationId}: current='{$currentState}', required='{$requiredState}'",
            userMessage: __('ai::errors.bulk_invalid_state', [
                'current' => $currentState,
                'required' => $requiredState
            ]),
            context: [
                'operation_id' => $operationId,
                'current_state' => $currentState,
                'required_state' => $requiredState,
                'error_type' => 'invalid_operation_state'
            ],
            code: 4014
        );
    }
}