<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Modules\Shop\App\Models\ShopCurrency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ShopCurrencyService
{
    /**
     * Get paginated currencies with filters
     */
    public function getPaginatedCurrencies(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = ShopCurrency::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', $search)
                  ->orWhere('name', 'like', $search)
                  ->orWhere('symbol', 'like', $search);
            });
        }

        // Active filter
        if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Sorting
        $sortField = $filters['sortField'] ?? 'currency_id';
        $sortDirection = $filters['sortDirection'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Toggle currency active status
     */
    public function toggleCurrencyStatus(int $currencyId): array
    {
        try {
            $currency = ShopCurrency::findOrFail($currencyId);
            $currency->is_active = !$currency->is_active;
            $currency->save();

            return [
                'success' => true,
                'message' => $currency->is_active
                    ? 'Currency activated successfully'
                    : 'Currency deactivated successfully',
                'type' => 'success',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to toggle currency status: ' . $e->getMessage(),
                'type' => 'error',
            ];
        }
    }

    /**
     * Set currency as default
     */
    public function setAsDefault(int $currencyId): array
    {
        try {
            DB::transaction(function () use ($currencyId) {
                // Remove default from all currencies
                ShopCurrency::where('is_default', true)->update(['is_default' => false]);

                // Set new default
                $currency = ShopCurrency::findOrFail($currencyId);
                $currency->is_default = true;
                $currency->save();
            });

            return [
                'success' => true,
                'message' => 'Default currency set successfully',
                'type' => 'success',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to set default currency: ' . $e->getMessage(),
                'type' => 'error',
            ];
        }
    }

    /**
     * Bulk delete currencies
     */
    public function bulkDeleteCurrencies(array $currencyIds): int
    {
        return ShopCurrency::whereIn('currency_id', $currencyIds)
            ->where('is_default', false) // Prevent deleting default currency
            ->delete();
    }

    /**
     * Create or update currency
     */
    public function saveCurrency(array $data, ?int $currencyId = null): array
    {
        try {
            DB::transaction(function () use ($data, $currencyId, &$currency) {
                if ($currencyId) {
                    $currency = ShopCurrency::findOrFail($currencyId);
                    $currency->update($data);
                } else {
                    $currency = ShopCurrency::create($data);
                }

                // If setting as default, remove default from others
                if (!empty($data['is_default'])) {
                    ShopCurrency::where('currency_id', '!=', $currency->currency_id)
                        ->update(['is_default' => false]);
                }
            });

            return [
                'success' => true,
                'message' => $currencyId ? 'Currency updated successfully' : 'Currency created successfully',
                'currency' => $currency ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save currency: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete single currency
     */
    public function deleteCurrency(int $currencyId): array
    {
        try {
            $currency = ShopCurrency::findOrFail($currencyId);

            if ($currency->is_default) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete default currency',
                ];
            }

            $currency->delete();

            return [
                'success' => true,
                'message' => 'Currency deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete currency: ' . $e->getMessage(),
            ];
        }
    }
}
