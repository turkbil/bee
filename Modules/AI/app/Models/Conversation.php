<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
    protected $connection = 'central';

    protected $table = 'ai_conversations';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }

    protected $fillable = [
        'title',
        'type',
        'feature_name',
        'is_demo',
        'user_id',
        'tenant_id',
        'prompt_id',
        'session_id',
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
     * Bu conversation'da kullanılan AI modelini al
     * 
     * @return string
     */
    public function getUsedModel()
    {
        // Önce mesajlardaki model_used alanından kontrol et
        $messageModel = $this->messages()
            ->where('role', 'assistant')
            ->whereNotNull('model_used')
            ->where('model_used', '!=', '')
            ->latest()
            ->value('model_used');
            
        if ($messageModel) {
            return $messageModel;
        }
        
        // Mesajlarda yoksa token usage kayıtlarından al
        try {
            $tokenUsage = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $this->tenant_id)
                ->where('description', 'like', 'AI Chat:%')
                ->whereNotNull('model')
                ->where('model', '!=', '')
                ->where('created_at', '>=', $this->created_at->subMinutes(5))
                ->where('created_at', '<=', $this->created_at->addMinutes(5))
                ->latest()
                ->first();
                
            if ($tokenUsage && $tokenUsage->model) {
                return $tokenUsage->model;
            }
        } catch (\Exception $e) {
            // Token usage bulunamazsa devam et
        }
        
        // Hiçbiri yoksa current provider'ı al
        try {
            if (class_exists('Modules\AI\App\Services\AIService')) {
                $aiService = app('Modules\AI\App\Services\AIService');
                if (method_exists($aiService, 'getCurrentProviderModel')) {
                    return $aiService->getCurrentProviderModel();
                }
            }
        } catch (\Exception $e) {
            // AI Service ulaşılamazsa
        }
        
        return 'unknown';
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
     * Bu konuşmaya ait toplam credit kullanımı
     */
    public function getTotalCreditsUsed()
    {
        return \Modules\AI\App\Models\AICreditUsage::where('conversation_id', $this->id)
            ->sum('credits_used') ?? 0;
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
            'total_credits_used' => $this->getTotalCreditsUsed(),
        ];
    }
}