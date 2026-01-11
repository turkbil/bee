<?php

namespace Modules\Muzibu\App\Traits;

/**
 * HasCachedCounts Trait
 *
 * Provides lazy-loaded cached count fields for Muzibu models.
 * When a cached field is NULL, it calculates the value and saves it.
 * Subsequent requests read directly from DB (no query).
 *
 * Usage in model:
 *   use HasCachedCounts;
 *
 *   protected function getCachedCountsConfig(): array {
 *       return [
 *           'songs_count' => fn() => $this->songs()->count(),
 *           'total_duration' => fn() => $this->songs()->sum('duration'),
 *       ];
 *   }
 */
trait HasCachedCounts
{
    /**
     * Boot the trait
     */
    public static function bootHasCachedCounts(): void
    {
        // Model retrieved olduğunda lazy calculation yapılabilir
        // Ama performans için accessor'da yapalım
    }

    /**
     * Override in model to define cached count fields and their calculators
     *
     * @return array<string, callable>
     */
    protected function getCachedCountsConfig(): array
    {
        return [];
    }

    /**
     * Get cached count value with lazy calculation
     *
     * @param string $field Field name (e.g., 'songs_count')
     * @return int
     */
    public function getCachedCount(string $field): int
    {
        $config = $this->getCachedCountsConfig();

        if (!isset($config[$field])) {
            return 0;
        }

        // Check if column exists (migration might not have run yet)
        // Use array_key_exists to handle both NULL and non-existent keys
        if (!array_key_exists($field, $this->attributes)) {
            // Column doesn't exist yet, calculate without saving
            return (int) call_user_func($config[$field]);
        }

        // If NULL, calculate and save
        if ($this->attributes[$field] === null) {
            $value = (int) call_user_func($config[$field]);
            $this->updateCachedCountQuietly($field, $value);
            return $value;
        }

        return (int) $this->attributes[$field];
    }

    /**
     * Update cached count without triggering events
     *
     * @param string $field
     * @param int $value
     * @return void
     */
    protected function updateCachedCountQuietly(string $field, int $value): void
    {
        // Use query builder to avoid model events
        static::where($this->getKeyName(), $this->getKey())
            ->update([$field => $value]);

        // Update local attribute
        $this->attributes[$field] = $value;
    }

    /**
     * Recalculate all cached counts for this model
     *
     * @return array<string, int> Calculated values
     */
    public function recalculateCachedCounts(): array
    {
        $config = $this->getCachedCountsConfig();
        $values = [];

        foreach ($config as $field => $calculator) {
            $values[$field] = (int) call_user_func($calculator);
        }

        if (!empty($values)) {
            static::where($this->getKeyName(), $this->getKey())
                ->update($values);

            foreach ($values as $field => $value) {
                $this->attributes[$field] = $value;
            }
        }

        return $values;
    }

    /**
     * Increment a cached count field
     *
     * @param string $field
     * @param int $amount
     * @return void
     */
    public function incrementCachedCount(string $field, int $amount = 1): void
    {
        // If column doesn't exist, skip (migration not run yet)
        if (!array_key_exists($field, $this->attributes)) {
            return;
        }

        // If NULL, recalculate instead of increment
        if ($this->attributes[$field] === null) {
            $this->recalculateCachedCounts();
            return;
        }

        static::where($this->getKeyName(), $this->getKey())
            ->increment($field, $amount);

        $this->attributes[$field] = ($this->attributes[$field] ?? 0) + $amount;
    }

    /**
     * Decrement a cached count field
     *
     * @param string $field
     * @param int $amount
     * @return void
     */
    public function decrementCachedCount(string $field, int $amount = 1): void
    {
        // If column doesn't exist, skip (migration not run yet)
        if (!array_key_exists($field, $this->attributes)) {
            return;
        }

        // If NULL, recalculate instead of decrement
        if ($this->attributes[$field] === null) {
            $this->recalculateCachedCounts();
            return;
        }

        $newValue = max(0, ($this->attributes[$field] ?? 0) - $amount);

        static::where($this->getKeyName(), $this->getKey())
            ->update([$field => $newValue]);

        $this->attributes[$field] = $newValue;
    }

    /**
     * Check if cached counts need recalculation (any is NULL)
     *
     * @return bool
     */
    public function needsCachedCountRecalculation(): bool
    {
        $config = $this->getCachedCountsConfig();

        foreach (array_keys($config) as $field) {
            if (!array_key_exists($field, $this->attributes) || $this->attributes[$field] === null) {
                return true;
            }
        }

        return false;
    }
}
