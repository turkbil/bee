<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $table = 'ai_messages';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens',
        'prompt_tokens',
        'completion_tokens',
        'model_used',
        'processing_time_ms',
        'metadata',
        'message_type',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Mesajın ait olduğu konuşma
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Konuşmadaki bir önceki mesajı getir
     *
     * @return self|null
     */
    public function getPreviousMessage()
    {
        return self::where('conversation_id', $this->conversation_id)
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Konuşmadaki bir sonraki mesajı getir
     *
     * @return self|null
     */
    public function getNextMessage()
    {
        return self::where('conversation_id', $this->conversation_id)
            ->where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Scope: Mesaj tipine göre filtrele
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope: Sadece test mesajları
     */
    public function scopeTestMessages($query)
    {
        return $query->where('message_type', 'test');
    }

    /**
     * Scope: Kullanılan modele göre filtrele
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model_used', $model);
    }

    /**
     * Formatlanmış süreyi getir
     */
    public function getFormattedProcessingTimeAttribute()
    {
        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . 'ms';
        }
        return round($this->processing_time_ms / 1000, 2) . 's';
    }

    /**
     * Token verimliliği (karakter/token oranı)
     */
    public function getTokenEfficiencyAttribute()
    {
        if ($this->tokens == 0) return 0;
        return round(strlen($this->content) / $this->tokens, 2);
    }
}