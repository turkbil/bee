<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingValue extends Model
{
    protected $table = 'settings_values';

    protected $fillable = [
        'setting_id',
        'value'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Cache temizleme olayları
        static::saved(function ($settingValue) {
            $settingValue->clearSettingCache();
        });
        
        static::deleted(function ($settingValue) {
            $settingValue->clearSettingCache();
        });
    }
    
    /**
     * Setting cache'ini temizle
     */
    protected function clearSettingCache()
    {
        $setting = $this->setting;
        
        if ($setting) {
            // ID bazlı cache
            $cacheKeyId = 'setting_id_' . $setting->id;
            // KEY bazlı cache  
            $cacheKeyName = 'setting_key_' . $setting->key;
            
            // Tenant varsa tenant prefix'li cache'leri de temizle
            if (tenant()) {
                $tenantId = tenant()->id;
                \Cache::forget('tenant_' . $tenantId . '_' . $cacheKeyId);
                \Cache::forget('tenant_' . $tenantId . '_' . $cacheKeyName);
            }
            
            // Genel cache'leri de temizle
            \Cache::forget($cacheKeyId);
            \Cache::forget($cacheKeyName);
        }
    }

    /**
     * Tenant'da yabancı anahtarlar olmadığından ilişkileri manuel ayarlayacağız
     */
    public function setting(): BelongsTo
    {
        // settings_id'yi kullanarak central'daki settings tablosuna bağlan
        return $this->belongsTo(Setting::class, 'setting_id');
    }
}