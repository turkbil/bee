<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens',
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
}