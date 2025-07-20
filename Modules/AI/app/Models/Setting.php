<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // AI tabloları her zaman central database'de
        $this->setConnection('mysql');
    }

    protected $fillable = [
        'api_key',
        'model',
        'max_tokens',
        'temperature',
        'enabled',
        'max_question_length',
        'max_daily_questions',
        'max_monthly_questions',
        'question_token_limit',
        'free_question_tokens_daily',
        'charge_question_tokens',
        'default_language',
        'response_format',
        'cache_duration',
        'concurrent_requests',
        'content_filtering',
        'rate_limiting',
        'detailed_logging',
        'performance_monitoring',
        'providers',
        'active_provider',
    ];

    protected $casts = [
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'enabled' => 'boolean',
        'max_question_length' => 'integer',
        'max_daily_questions' => 'integer',
        'max_monthly_questions' => 'integer',
        'question_token_limit' => 'integer',
        'free_question_tokens_daily' => 'integer',
        'charge_question_tokens' => 'boolean',
        'cache_duration' => 'integer',
        'concurrent_requests' => 'integer',
        'content_filtering' => 'boolean',
        'rate_limiting' => 'boolean',
        'detailed_logging' => 'boolean',
        'performance_monitoring' => 'boolean',
        'providers' => 'array',
    ];

    /**
     * API anahtarını şifreli olarak kaydet
     *
     * @param string $value
     * @return void
     */
    public function setApiKeyAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['api_key'] = Crypt::encryptString($value);
        }
    }

    /**
     * API anahtarını deşifre ederek getir
     *
     * @param string $value
     * @return string|null
     */
    public function getApiKeyAttribute($value)
    {
        if (!empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Aktif provider'ı getir
     */
    public function getActiveProvider()
    {
        $providers = $this->providers ?? [];
        $activeProviderName = $this->active_provider ?? 'deepseek';
        
        return $providers[$activeProviderName] ?? null;
    }

    /**
     * Tüm aktif provider'ları getir
     */
    public function getActiveProviders()
    {
        $providers = $this->providers ?? [];
        
        return array_filter($providers, function($provider) {
            return $provider['is_active'] ?? false;
        });
    }

    /**
     * Provider'ı güncelle
     */
    public function updateProvider($providerName, $data)
    {
        $providers = $this->providers ?? [];
        
        if (isset($providers[$providerName])) {
            $providers[$providerName] = array_merge($providers[$providerName], $data);
            $this->providers = $providers;
            $this->save();
        }
    }

    /**
     * Provider performansını güncelle
     */
    public function updateProviderPerformance($providerName, $responseTime)
    {
        $providers = $this->providers ?? [];
        
        if (isset($providers[$providerName])) {
            $currentTime = $providers[$providerName]['average_response_time'] ?? 0;
            
            // Weighted average calculation
            if ($currentTime > 0) {
                $providers[$providerName]['average_response_time'] = ($currentTime * 0.8) + ($responseTime * 0.2);
            } else {
                $providers[$providerName]['average_response_time'] = $responseTime;
            }
            
            $this->providers = $providers;
            $this->save();
        }
    }

    /**
     * Provider'ı varsayılan yap
     */
    public function setDefaultProvider($providerName)
    {
        $providers = $this->providers ?? [];
        
        if (isset($providers[$providerName]) && $providers[$providerName]['is_active']) {
            $this->active_provider = $providerName;
            $this->save();
            return true;
        }
        
        return false;
    }
}