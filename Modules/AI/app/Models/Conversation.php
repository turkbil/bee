<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Conversation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'title',
        'type',
        'feature_name',
        'is_demo',
        'user_id',
        'tenant_id',
        'prompt_id',
        'total_tokens_used',
        'metadata',
        'status',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Konuşmaya ait mesajlar
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Konuşmaya ait kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Konuşmaya ait prompt
     */
    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    /**
     * Konuşmaya ait tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Son mesajı getir
     *
     * @return \Modules\AI\App\Models\Message|null
     */
    public function getLastMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Toplam token sayısını getir
     *
     * @return int
     */
    public function getTotalTokens()
    {
        return $this->messages()->sum('tokens');
    }

    /**
     * Scope: Sadece aktif konuşmalar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Type'a göre filtrele
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Feature testleri
     */
    public function scopeFeatureTests($query)
    {
        return $query->where('type', 'feature_test');
    }

    /**
     * Scope: Demo testler
     */
    public function scopeDemoTests($query)
    {
        return $query->where('is_demo', true);
    }

    /**
     * Scope: Gerçek AI testleri
     */
    public function scopeRealTests($query)
    {
        return $query->where('is_demo', false);
    }

    /**
     * İstatistikler için özet bilgi
     */
    public function getSummaryAttribute()
    {
        $lastMessage = $this->getLastMessage();
        return [
            'last_message_time' => $this->updated_at,
            'message_count' => $this->messages()->count(),
            'total_tokens' => $this->total_tokens_used,
            'last_content' => $lastMessage ? \Str::limit($lastMessage->content, 100) : null,
            'feature_name' => $this->feature_name,
            'is_demo' => $this->is_demo,
        ];
    }
}