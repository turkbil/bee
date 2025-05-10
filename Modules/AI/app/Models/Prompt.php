<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'ai_prompts';

    protected $fillable = [
        'name',
        'content',
        'is_default',
        'is_system',
        'is_common',
        'is_active',
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
     * Konuşma ilişkisi
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}