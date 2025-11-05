<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AI Conversation Message
 *
 * Stores individual messages in a conversation (user & assistant)
 */
class AIConversationMessage extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Central database
    protected $table = 'ai_conversation_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AIConversation::class, 'conversation_id');
    }
}
