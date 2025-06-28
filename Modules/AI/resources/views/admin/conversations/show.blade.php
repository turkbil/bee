@extends('admin.layout')

@section('content')
@include('ai::admin.helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $conversation->title }}</h3>
            <div class="card-actions">
                <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('ai::admin.all_conversations') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="chat-container" id="chat-container">
            <div class="chat-messages p-3" id="chat-messages">
                @foreach($messages as $message)
                <div class="message {{ $message->role == 'assistant' ? 'ai-message' : 'user-message' }}">
                    <div class="message-content">
                        <p>{!! nl2br(e($message->content)) !!}</p>
                        <small class="text-muted d-block mt-1">
                            {{ $message->created_at->format(__('ai::admin.date_format')) }} · {{ $message->tokens }} {{ __('ai::admin.token') }}
                        </small>
                    </div>
                    @if($message->role == 'assistant')
                    <div class="message-actions">
                        <button class="btn btn-sm btn-ghost-secondary copy-message" data-bs-toggle="tooltip"
                            title="{{ __('ai::admin.copy_message') }}" onclick="copyToClipboard(this)">
                            <i class="fa-thin fa-copy"></i>
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            <a href="{{ route('admin.ai.index') }}?conversation={{ $conversation->id }}" class="btn btn-primary">
                <i class="fas fa-reply me-2"></i> {{ __('ai::admin.continue_conversation') }}
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(button) {
        const content = button.closest('.message').querySelector('.message-content p').innerText;
        navigator.clipboard.writeText(content).then(() => {
            // Kopyalama başarılı olduğunda bildirim göster
            const originalClass = button.querySelector('i').className;
            button.querySelector('i').className = 'fas fa-check text-success';
            
            setTimeout(() => {
                button.querySelector('i').className = originalClass;
            }, 2000);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Sayfayı en alta kaydır
        const chatContainer = document.getElementById('chat-container');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endpush

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 350px);
        min-height: 300px;
        overflow-y: auto;
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        display: flex;
        max-width: 80%;
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .user-message {
        align-self: flex-end;
        background-color: var(--primary-color, #206bc4);
        color: #fff;
        border-radius: 0.5rem 0.5rem 0 0.5rem;
    }

    .ai-message {
        align-self: flex-start;
        background-color: var(--tblr-bg-surface, #f0f0f0);
        color: var(--tblr-body-color, #000);
        border-radius: 0.5rem 0.5rem 0.5rem 0;
    }

    .dark .ai-message {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .message-content {
        flex: 1;
    }

    .message-content p {
        margin-bottom: 0;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .message-actions {
        display: flex;
        align-items: flex-start;
        margin-left: 0.5rem;
        visibility: hidden;
    }

    .ai-message:hover .message-actions {
        visibility: visible;
    }

    /* Markdown stilleri */
    .message-content h1 {
        font-size: 1.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .message-content h2 {
        font-size: 1.3rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .message-content h3 {
        font-size: 1.1rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .message-content pre {
        background-color: rgba(0, 0, 0, 0.05);
        padding: 0.5rem;
        border-radius: 0.25rem;
        overflow-x: auto;
        margin-bottom: 0.5rem;
    }

    .dark .message-content pre {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .message-content code {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
        padding: 2px 4px;
        font-family: monospace;
        font-size: 0.9em;
    }

    .dark .message-content code {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .message-content ul,
    .message-content ol {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
    }

    .message-content blockquote {
        border-left: 3px solid #aaa;
        padding-left: 1rem;
        margin-left: 0;
        color: #666;
    }

    .dark .message-content blockquote {
        color: #ccc;
    }

    .message-content table {
        border-collapse: collapse;
        width: 100%;
        margin: 0.5rem 0;
    }

    .message-content table th,
    .message-content table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .message-content table tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .dark .message-content table tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .message-content table th {
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: left;
        background-color: rgba(0, 0, 0, 0.05);
    }

    .dark .message-content table th {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush
@endsection