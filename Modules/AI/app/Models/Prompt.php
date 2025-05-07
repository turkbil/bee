<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'ai_prompts';

    protected $fillable = [
        'tenant_id',
        'name',
        'content',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Varsayılan prompt'u getir
     *
     * @param int $tenantId
     * @return self|null
     */
    public static function getDefault(int $tenantId)
    {
        return self::where('tenant_id', $tenantId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Tenant ilişkisi
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Konuşma ilişkisi
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}