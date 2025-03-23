<?php

namespace Modules\UserManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ModuleManagement\App\Models\Module;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ModulePermission extends Model
{
    use LogsActivity;

    protected $fillable = [
        'module_id',
        'permission_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Modül ile ilişki
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }
    
    /**
     * Tüm olası izin tiplerini döndürür
     */
    public static function getPermissionTypes(): array
    {
        return [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Güncelleme',
            'delete' => 'Silme'
        ];
    }
    
    /**
     * Activity log ayarları
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['module_id', 'permission_type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ModulePermission');
    }
}