<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'ai_prompts';
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'content',
        'is_default',
        'is_system',
        'is_common',
        'is_active',
        'prompt_type',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'is_common' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Varsayılan prompt'u getir
     *
     * @return self|null
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Ortak özellikler promptunu getir
     *
     * @return self|null
     */
    public static function getCommon()
    {
        return self::where('is_common', true)->where('is_active', true)->first();
    }

    /**
     * Sistem promptlarını getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSystemPrompts()
    {
        return self::where('is_system', true)->where('is_active', true)->get();
    }

    /**
     * Tip bazında prompt getir (sistem promptları için)
     *
     * @param string $type
     * @return self|null
     */
    public static function getByType($type)
    {
        return self::where('prompt_type', $type)
                   ->where('is_system', true)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Gizli sistem promptu getir
     */
    public static function getHiddenSystem()
    {
        return self::getByType('hidden_system');
    }

    /**
     * Gizli bilgi tabanını getir
     */
    public static function getSecretKnowledge()
    {
        return self::getByType('secret_knowledge');
    }

    /**
     * Şartlı yanıtları getir
     */
    public static function getConditional()
    {
        return self::getByType('conditional');
    }

    /**
     * Konuşma ilişkisi
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Prompt'a bağlı AI feature'lar (many-to-many)
     */
    public function features()
    {
        return $this->belongsToMany(AIFeature::class, 'ai_feature_prompts', 'ai_prompt_id', 'ai_feature_id')
            ->withPivot(['prompt_role', 'priority', 'is_required', 'is_active', 'conditions', 'parameters', 'notes'])
            ->withTimestamps();
    }

    /**
     * Feature prompt pivot kayıtları
     */
    public function featurePrompts()
    {
        return $this->hasMany(AIFeaturePrompt::class, 'ai_prompt_id');
    }

    /**
     * Prompt silinebilir mi kontrol et
     */
    public function canBeDeleted(): bool
    {
        // Sistem promptları sadece düzenlenebilir, silinemez
        if ($this->is_system || $this->is_common || $this->is_default) {
            return false;
        }
        
        // Herhangi bir feature'a bağlıysa silinemez
        return $this->features()->count() === 0;
    }

    /**
     * Prompt türünü Türkçe al
     */
    public function getTypeName(): string
    {
        $types = [
            'standard' => 'Standart',
            'common' => 'Ortak Özellikler',
            'hidden_system' => 'Gizli Sistem',
            'secret_knowledge' => 'Gizli Bilgi',
            'conditional' => 'Şartlı Yanıt'
        ];

        return $types[$this->prompt_type] ?? 'Bilinmeyen';
    }
}