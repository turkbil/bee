
    /**
     * Konuşma mesajlarını AI için formatla
     */
    public function formatConversationMessages(Conversation $conversation): array
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->get();
            
        $formatted = [];
        
        foreach ($messages as $message) {
            $formatted[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }
        
        return $formatted;
    }

