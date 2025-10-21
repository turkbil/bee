<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;
use Illuminate\Database\Eloquent\Model;

/**
 * Extended Domain Model
 *
 * Vendor Domain model'ini extend eder ve ek metodlar ekler
 */
class Domain extends BaseDomain
{
    /**
     * Tenant'ın primary domain'ini al
     *
     * @param int|string $tenantId
     * @return Domain|null
     */
    public static function getPrimaryDomain($tenantId): ?Domain
    {
        // Önce is_primary=1 olanı bul
        $primary = static::where('tenant_id', $tenantId)
            ->where('is_primary', 1)
            ->first();

        if ($primary) {
            return $primary;
        }

        // Primary yoksa ilk domaini döndür (fallback)
        return static::where('tenant_id', $tenantId)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Tenant'ın tüm domainlerini al
     *
     * @param int|string $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTenantDomains($tenantId)
    {
        return static::where('tenant_id', $tenantId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Primary domain olarak işaretle
     *
     * @return bool
     */
    public function setAsPrimary(): bool
    {
        // Önce aynı tenant'ın tüm domainlerini is_primary=0 yap
        static::where('tenant_id', $this->tenant_id)
            ->update(['is_primary' => 0]);

        // Bu domaini primary yap
        return $this->update(['is_primary' => 1]);
    }

    /**
     * Primary domain mi kontrol et
     *
     * @return bool
     */
    public function isPrimary(): bool
    {
        return (bool) $this->is_primary;
    }
}
