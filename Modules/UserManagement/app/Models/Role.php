<?php

namespace Modules\UserManagement\App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Support\Facades\Log;

class Role extends SpatieRole
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'guard_name',
        'is_protected'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_protected' => 'boolean',
    ];

    /**
     * Temel rollerin listesi
     */
    public const BASE_ROLES = [
        'super-admin',
        'admin',
        'user'
    ];

    /**
     * Rolün temel (değiştirilemez) bir rol olup olmadığını kontrol eder
     */
    public function isBaseRole(): bool
    {
        return in_array($this->name, self::BASE_ROLES);
    }

    /**
     * Rolün düzenlenebilir olup olmadığını kontrol eder
     */
    public function isEditable(): bool
    {
        return !$this->is_protected && !$this->isBaseRole();
    }

    /**
     * Rolün silinebilir olup olmadığını kontrol eder
     */
    public function isDeletable(): bool
    {
        try {
            // Temel rol veya korumalı rol ise silinemez
            if ($this->isBaseRole() || $this->is_protected) {
                Log::info('Rol silinemez: ' . $this->name . ' (Temel rol veya korumalı)');
                return false;
            }

            // Kullanıcısı olan rol silinemez
            if ($this->users()->count() > 0) {
                Log::info('Rol silinemez: ' . $this->name . ' (Kullanıcısı var)');
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('isDeletable kontrolünde hata: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Temel rolleri oluşturmak için kullanılan helper metod
     */
    public static function createBaseRoles(): void
    {
        foreach (self::BASE_ROLES as $roleName) {
            self::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                'is_protected' => true
            ]);
        }
    }
}