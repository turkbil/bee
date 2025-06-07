<?php

namespace Modules\ModuleManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ModuleTenantSetting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'module_name',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_active',
        'is_system'
    ];

    protected $casts = [
        'setting_value' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_name', 'name');
    }

    public function getValueAttribute()
    {
        switch ($this->setting_type) {
            case 'boolean':
                return (bool) ($this->setting_value['value'] ?? false);
            case 'integer':
                return (int) ($this->setting_value['value'] ?? 0);
            case 'array':
                return $this->setting_value;
            default:
                return (string) ($this->setting_value['value'] ?? '');
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ModuleTenantSetting');
    }

    public static function getForModule(string $moduleName): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('module_name', $moduleName)
            ->where('is_active', true)
            ->orderBy('setting_key')
            ->get();
    }

    public static function getSetting(string $moduleName, string $settingKey, $default = null)
    {
        $setting = static::where('module_name', $moduleName)
            ->where('setting_key', $settingKey)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->value : $default;
    }

    public static function setSetting(string $moduleName, string $settingKey, $value, string $type = 'string', string $description = null)
    {
        $settingValue = $type === 'array' && is_array($value) ? $value : ['value' => $value];
        
        $setting = static::updateOrCreate(
            [
                'module_name' => $moduleName,
                'setting_key' => $settingKey
            ],
            [
                'setting_value' => $settingValue,
                'setting_type' => $type,
                'description' => $description,
                'is_active' => true
            ]
        );

        return $setting;
    }
}