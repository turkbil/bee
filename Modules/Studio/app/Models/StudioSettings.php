<?php

namespace Modules\Studio\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudioSettings extends Model
{
    use HasFactory;

    protected $table = 'studio_settings';
    
    protected $fillable = [
        'module',
        'module_id',
        'theme',
        'header_template',
        'footer_template',
        'settings'
    ];
    
    protected $casts = [
        'settings' => 'array',
    ];
    
    /**
     * Ayar değerini al
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }
    
    /**
     * Ayar değerini ayarla
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setSetting(string $key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        
        return $this;
    }
    
    /**
     * İlgili modülün ayarlarını al veya oluştur
     *
     * @param string $module
     * @param int $moduleId
     * @return static
     */
    public static function findOrCreateFor(string $module, int $moduleId)
    {
        return static::firstOrCreate(
            ['module' => $module, 'module_id' => $moduleId],
            ['settings' => []]
        );
    }
}