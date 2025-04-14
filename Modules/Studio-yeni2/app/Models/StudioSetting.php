<?php

namespace Modules\Studio\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudioSetting extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'studio_settings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'module_id',
        'theme',
        'header_template',
        'footer_template',
        'settings'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'json',
    ];
    
    /**
     * Modül için ayarları bul
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @return StudioSetting|null
     */
    public static function findForModule(string $module, int $moduleId): ?self
    {
        return self::where('module', $module)
            ->where('module_id', $moduleId)
            ->first();
    }
    
    /**
     * Modül ayarlarını güncelle veya oluştur
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @param array $data Ayar verileri
     * @return StudioSetting
     */
    public static function updateOrCreateForModule(string $module, int $moduleId, array $data): self
    {
        return self::updateOrCreate(
            ['module' => $module, 'module_id' => $moduleId],
            $data
        );
    }
    
    /**
     * Belirli bir ayarı al
     *
     * @param string $key Anahtar
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }
    
    /**
     * Belirli bir ayarı güncelle
     *
     * @param string $key Anahtar
     * @param mixed $value Değer
     * @return $this
     */
    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        
        return $this;
    }
}