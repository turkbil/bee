<?php

namespace Modules\UserManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserModulePermission extends Model
{
    // LogsActivity kaldırıldı - gereksiz log oluşturuyordu

    protected $fillable = [
        'user_id',
        'module_name',
        'permission_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Kullanıcı ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tüm olası izin tiplerini döndürür
     */
    public static function getPermissionTypes(): array
    {
        return ModulePermission::getPermissionTypes();
    }
}