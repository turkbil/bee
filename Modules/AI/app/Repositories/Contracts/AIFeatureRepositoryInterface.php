<?php

declare(strict_types=1);

namespace Modules\AI\App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Modules\AI\App\Models\AIFeature;

/**
 * AI Feature Repository Interface
 * 
 * Defines the contract for AI Feature data access operations
 */
interface AIFeatureRepositoryInterface
{
    /**
     * Find feature by ID with optional caching
     */
    public function findById(int $id, bool $useCache = true): ?AIFeature;

    /**
     * Find feature by slug with caching
     */
    public function findBySlug(string $slug, bool $useCache = true): ?AIFeature;

    /**
     * Get all active features
     */
    public function getActiveFeatures(bool $useCache = true): Collection;

    /**
     * Get public features
     */
    public function getPublicFeatures(bool $useCache = true): Collection;

    /**
     * Get features by category
     */
    public function getByCategory(string $category, bool $useCache = true): Collection;

    /**
     * Search features
     */
    public function search(string $query, array $filters = []): Collection;

    /**
     * Create new feature
     */
    public function create(array $data): AIFeature;

    /**
     * Update feature
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete feature
     */
    public function delete(int $id): bool;

    /**
     * Clear feature cache
     */
    public function clearCache(): void;

    /**
     * Get feature usage statistics
     */
    public function getUsageStatistics(int $featureId, ?int $days = 30): array;

    /**
     * Get popular features
     */
    public function getPopularFeatures(int $limit = 10): Collection;

    /**
     * Increment usage counter
     */
    public function incrementUsageCounter(int $featureId): void;
}