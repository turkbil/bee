<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AIProvider extends Model
{
    use HasFactory;

    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'display_name',
        'service_class',
        'default_model',
        'available_models',
        'default_settings',
        'api_key',
        'base_url',
        'is_active',
        'is_default',
        'priority',
        'average_response_time',
        'description',
    ];

    protected $casts = [
        'available_models' => 'array',
        'default_settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'average_response_time' => 'decimal:2',
    ];

    /**
     * Varsayılan provider'ı getir
     */
    public static function getDefault()
    {
        return self::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Aktif provider'ları getir
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('average_response_time', 'asc')
            ->get();
    }

    /**
     * Provider'ın servis sınıfını örnekle
     */
    public function getServiceInstance()
    {
        $serviceClass = "Modules\\AI\\App\\Services\\{$this->service_class}";
        
        if (!class_exists($serviceClass)) {
            throw new \Exception("Service class not found: {$serviceClass}");
        }

        $service = new $serviceClass();

        // API key ve base URL'yi ayarla
        if ($this->api_key && method_exists($service, 'setApiKey')) {
            $service->setApiKey($this->api_key);
        }

        if ($this->base_url && method_exists($service, 'setBaseUrl')) {
            $service->setBaseUrl($this->base_url);
        }

        // Model'i ayarla
        if ($this->default_model && method_exists($service, 'setModel')) {
            $service->setModel($this->default_model);
        }

        return $service;
    }

    /**
     * Performans güncellemesi
     */
    public function updatePerformance($responseTime)
    {
        // Ortalama yanıt süresini güncelle (weighted average)
        if ($this->average_response_time) {
            $this->average_response_time = ($this->average_response_time * 0.8) + ($responseTime * 0.2);
        } else {
            $this->average_response_time = $responseTime;
        }

        $this->save();
    }

    /**
     * API anahtarını şifreli olarak kaydet - geçici olarak kapatıldı
     */
    // public function setApiKeyAttribute($value)
    // {
    //     if ($value) {
    //         $this->attributes['api_key'] = encrypt($value);
    //     }
    // }

    /**
     * API anahtarını şifreli olarak al - geçici olarak kapatıldı
     */
    // public function getApiKeyAttribute($value)
    // {
    //     if ($value) {
    //         try {
    //             return decrypt($value);
    //         } catch (\Exception $e) {
    //             return $value; // Eğer decrypt edilemezse raw değeri döndür
    //         }
    //     }
    //     return $value;
    // }

    /**
     * Provider'ın kullanılabilir olup olmadığını kontrol et
     */
    public function isAvailable()
    {
        return $this->is_active && $this->api_key && $this->service_class;
    }
}