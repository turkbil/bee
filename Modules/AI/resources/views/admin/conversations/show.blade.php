@include('ai::admin.helper')

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $conversation->title }}</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i> Tüm Konuşmalar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chat-messages" style="max-height: 600px; overflow-y: auto; padding: 1rem;">
                            @foreach($messages as $message)
                                <div class="message {{ $message->role == 'assistant' ? 'message-assistant' : 'message-user' }} mb-3">
                                    <div class="message-avatar">
                                        @if($message->role == 'assistant')
                                            <span class="avatar bg-primary-lt">AI</span>
                                        @else
                                            <span class="avatar bg-secondary-lt">
                                                {{ substr(auth()->user()->name, 0, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="message-content">
                                        <div class="message-bubble">
                                            {!! nl2br(e($message->content)) !!}
                                        </div>
                                        <div class="message-footer text-muted">
                                            {{ $message->created_at->format('d.m.Y H:i') }}
                                            <span class="ms-2">{{ $message->tokens }} token</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('admin.ai.index') }}?conversation={{ $conversation->id }}" class="btn btn-primary">
                                <i class="fas fa-reply me-2"></i> Bu Konuşmaya Devam Et
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chat-messages {
        display: flex;
        flex-direction: column;
    }
    
    .message {
        display: flex;
        margin-bottom: 15px;
    }
    
    .message-user {
        justify-content: flex-end;
    }
    
    .message-assistant {
        justify-content: flex-start;
    }
    
    .message-avatar {
        margin-right: 10px;
    }
    
    .message-user .message-avatar {
        order: 1;
        margin-left: 10px;
        margin-right: 0;
    }
    
    .message-content {
        max-width: 70%;
    }
    
    .message-bubble {
        padding: 10px 15px;
        border-radius: 10px;
        background-color: #f1f5f9;
        white-space: pre-wrap;
    }
    
    .message-user .message-bubble {
        background-color: #206bc4;
        color: white;
    }
    
    .message-footer {
        font-size: 0.75rem;
        margin-top: 5px;
    }
</style>
@endpush