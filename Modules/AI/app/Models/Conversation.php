<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'title',
        'user_id',
        'prompt_id',
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
}