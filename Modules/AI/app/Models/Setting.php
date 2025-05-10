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
    ];

    protected $casts = [
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'enabled' => 'boolean',
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