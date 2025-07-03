<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';

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
}