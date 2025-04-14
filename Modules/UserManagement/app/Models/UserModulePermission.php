<?php

namespace Modules\UserManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserModulePermission extends Model
{
    use LogsActivity;

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
    
    /**
     * Activity log ayarları
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'module_name', 'permission_type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('UserModulePermission');
    }
}