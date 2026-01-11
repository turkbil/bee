<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

use Exception;
use Throwable;

/**
 * AI Credit System Exception
 * 
 * Specialized exception class for handling credit system errors.
 * Provides detailed error contexts and factory methods for common credit scenarios.
 * 
 * @package Modules\AI\app\Exceptions
 * @author AI V2 System
 * @version 2.0.0
 */
class AICreditException extends Exception
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
     * Insufficient credits for operation
     */
    public static function insufficientCredits(
        float $requiredCredits,
        float $availableCredits,
        ?array $context = null
    ): self {
        return new self(
            message: "Insufficient credits: required {$requiredCredits}, available {$availableCredits}",
            code: 2001,
            context: array_merge([
                'required_credits' => $requiredCredits,
                'available_credits' => $availableCredits,
                'deficit' => $requiredCredits - $availableCredits,
            ], $context ?? []),
            errorCategory: 'insufficient_credits',
            userMessage: "You don't have enough credits for this operation. Required: {$requiredCredits}, Available: {$availableCredits}"
        );
    }

    /**
     * Credit purchase failed
     */
    public static function purchaseFailed(
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: $technicalMessage,
            code: 2002,
            context: $context,
            errorCategory: 'purchase_failed',
            userMessage: 'Credit purchase failed. Please try again or contact support if the problem persists.'
        );
    }

    /**
     * Credit usage failed
     */
    public static function usageFailed(
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: $technicalMessage,
            code: 2003,
            context: $context,
            errorCategory: 'usage_failed',
            userMessage: 'Credit usage could not be processed. Please try again.'
        );
    }

    /**
     * Invalid credit package
     */
    public static function invalidPackage(
        int $packageId,
        string $reason = '',
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid credit package {$packageId}: {$reason}",
            code: 2004,
            context: array_merge(['package_id' => $packageId, 'reason' => $reason], $context ?? []),
            errorCategory: 'invalid_package',
            userMessage: 'The selected credit package is not available or has expired.'
        );
    }

    /**
     * Payment processing failed
     */
    public static function paymentFailed(
        string $paymentReference,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Payment failed for reference {$paymentReference}: {$technicalMessage}",
            code: 2005,
            context: array_merge(['payment_reference' => $paymentReference], $context ?? []),
            errorCategory: 'payment_failed',
            userMessage: 'Payment processing failed. Please check your payment method and try again.'
        );
    }

    /**
     * Credits expired
     */
    public static function creditsExpired(
        float $expiredCredits,
        string $expiryDate,
        ?array $context = null
    ): self {
        return new self(
            message: "Credits expired: {$expiredCredits} credits expired on {$expiryDate}",
            code: 2006,
            context: array_merge([
                'expired_credits' => $expiredCredits,
                'expiry_date' => $expiryDate,
            ], $context ?? []),
            errorCategory: 'credits_expired',
            userMessage: "Some of your credits have expired. Expired credits: {$expiredCredits}"
        );
    }

    /**
     * Invalid usage category
     */
    public static function invalidCategory(
        string $category,
        array $validCategories = [],
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid usage category: {$category}. Valid categories: " . implode(', ', $validCategories),
            code: 2007,
            context: array_merge([
                'invalid_category' => $category,
                'valid_categories' => $validCategories,
            ], $context ?? []),
            errorCategory: 'invalid_category',
            userMessage: 'Invalid operation category specified.'
        );
    }

    /**
     * User not found or access denied
     */
    public static function accessDenied(
        int $userId,
        string $operation,
        ?array $context = null
    ): self {
        return new self(
            message: "Access denied for user {$userId} to operation {$operation}",
            code: 2008,
            context: array_merge([
                'user_id' => $userId,
                'operation' => $operation,
            ], $context ?? []),
            errorCategory: 'access_denied',
            userMessage: 'You do not have permission to perform this credit operation.'
        );
    }

    /**
     * Credit calculation failed
     */
    public static function calculationFailed(
        string $calculationType,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Credit calculation failed for {$calculationType}: {$technicalMessage}",
            code: 2009,
            context: array_merge(['calculation_type' => $calculationType], $context ?? []),
            errorCategory: 'calculation_failed',
            userMessage: 'Credit calculation encountered an error. Please try again.'
        );
    }

    /**
     * Database operation failed
     */
    public static function databaseError(
        string $operation,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Database operation failed for {$operation}: {$technicalMessage}",
            code: 2010,
            context: array_merge(['operation' => $operation], $context ?? []),
            errorCategory: 'database_error',
            userMessage: 'A system error occurred while processing your request. Please try again.'
        );
    }

    /**
     * Credit limit exceeded
     */
    public static function limitExceeded(
        string $limitType,
        float $currentValue,
        float $limitValue,
        ?array $context = null
    ): self {
        return new self(
            message: "{$limitType} limit exceeded: current {$currentValue}, limit {$limitValue}",
            code: 2011,
            context: array_merge([
                'limit_type' => $limitType,
                'current_value' => $currentValue,
                'limit_value' => $limitValue,
            ], $context ?? []),
            errorCategory: 'limit_exceeded',
            userMessage: "Operation limit exceeded. Please wait before trying again or upgrade your plan."
        );
    }

    /**
     * Invalid transaction state
     */
    public static function invalidTransactionState(
        string $transactionId,
        string $currentState,
        string $expectedState,
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid transaction state for {$transactionId}: current {$currentState}, expected {$expectedState}",
            code: 2012,
            context: array_merge([
                'transaction_id' => $transactionId,
                'current_state' => $currentState,
                'expected_state' => $expectedState,
            ], $context ?? []),
            errorCategory: 'invalid_state',
            userMessage: 'Transaction is in an invalid state and cannot be processed.'
        );
    }

    /**
     * Refund processing failed
     */
    public static function refundFailed(
        string $purchaseId,
        float $refundAmount,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Refund failed for purchase {$purchaseId}, amount {$refundAmount}: {$technicalMessage}",
            code: 2013,
            context: array_merge([
                'purchase_id' => $purchaseId,
                'refund_amount' => $refundAmount,
            ], $context ?? []),
            errorCategory: 'refund_failed',
            userMessage: 'Refund processing failed. Please contact support for assistance.'
        );
    }

    /**
     * Credit transfer failed
     */
    public static function transferFailed(
        int $fromUserId,
        int $toUserId,
        float $amount,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Credit transfer failed from user {$fromUserId} to user {$toUserId}, amount {$amount}: {$technicalMessage}",
            code: 2014,
            context: array_merge([
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount,
            ], $context ?? []),
            errorCategory: 'transfer_failed',
            userMessage: 'Credit transfer could not be completed. Please check the details and try again.'
        );
    }

    /**
     * Analytics calculation failed
     */
    public static function analyticsFailed(
        string $analyticsType,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Analytics calculation failed for {$analyticsType}: {$technicalMessage}",
            code: 2015,
            context: array_merge(['analytics_type' => $analyticsType], $context ?? []),
            errorCategory: 'analytics_failed',
            userMessage: 'Usage analytics are temporarily unavailable. Please try again later.'
        );
    }

    /**
     * Invalid credit amount
     */
    public static function invalidAmount(
        float $amount,
        float $minAmount,
        float $maxAmount,
        ?array $context = null
    ): self {
        return new self(
            message: "Invalid credit amount {$amount}, must be between {$minAmount} and {$maxAmount}",
            code: 2016,
            context: array_merge([
                'amount' => $amount,
                'min_amount' => $minAmount,
                'max_amount' => $maxAmount,
            ], $context ?? []),
            errorCategory: 'invalid_amount',
            userMessage: "Invalid amount. Please enter a value between {$minAmount} and {$maxAmount} credits."
        );
    }

    /**
     * Provider integration failed
     */
    public static function providerIntegrationFailed(
        string $provider,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Provider integration failed for {$provider}: {$technicalMessage}",
            code: 2017,
            context: array_merge(['provider' => $provider], $context ?? []),
            errorCategory: 'provider_failed',
            userMessage: 'External service integration failed. Credit calculation may be affected.'
        );
    }

    /**
     * Cache operation failed
     */
    public static function cacheFailure(
        string $operation,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Cache operation failed for {$operation}: {$technicalMessage}",
            code: 2018,
            context: array_merge(['operation' => $operation], $context ?? []),
            errorCategory: 'cache_failed',
            userMessage: 'System performance may be affected, but credit operations will continue.'
        );
    }

    /**
     * Forecasting failed
     */
    public static function forecastingFailed(
        string $forecastType,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Credit forecasting failed for {$forecastType}: {$technicalMessage}",
            code: 2019,
            context: array_merge(['forecast_type' => $forecastType], $context ?? []),
            errorCategory: 'forecasting_failed',
            userMessage: 'Credit usage forecasting is temporarily unavailable.'
        );
    }

    /**
     * Configuration error
     */
    public static function configurationError(
        string $setting,
        string $technicalMessage,
        ?array $context = null
    ): self {
        return new self(
            message: "Configuration error for {$setting}: {$technicalMessage}",
            code: 2020,
            context: array_merge(['setting' => $setting], $context ?? []),
            errorCategory: 'configuration_error',
            userMessage: 'System configuration issue detected. Please contact administrator.'
        );
    }
}