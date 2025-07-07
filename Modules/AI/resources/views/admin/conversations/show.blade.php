@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', __('ai::admin.conversation_detail'))
@section('title', $conversation->feature_name ?: $conversation->title)

@section('content')
    <div class="container-xl">
        <!-- Header Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        @if($conversation->type == 'feature_test')
                                            <i class="fas fa-flask text-white"></i>
                                        @else
                                            <i class="fas fa-comments text-white"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h2 class="mb-0">{{ $conversation->feature_name ?: $conversation->title }}</h2>
                                        <div class="text-muted d-flex align-items-center gap-3 mt-1">
                                            @if($conversation->type == 'feature_test')
                                                <span class="badge badge-outline text-blue">
                                                    <i class="fas fa-vial me-1"></i>{{ __('ai::admin.ai_test') }}
                                                </span>
                                                @if($conversation->is_demo)
                                                    <span class="badge badge-blue">Demo</span>
                                                @else
                                                    <span class="badge badge-green">{{ __('ai::admin.real_ai') }}</span>
                                                @endif
                                            @else
                                                <span class="badge badge-outline text-green">
                                                    <i class="fas fa-comment me-1"></i>{{ __('ai::admin.chat') }}
                                                </span>
                                            @endif
                                            <span>{{ $conversation->created_at->format('d.m.Y H:i') }}</span>
                                            @if($conversation->tenant)
                                                <span>{{ $conversation->tenant->title ?: 'Tenant #' . $conversation->tenant->id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('ai::admin.go_back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        @if($conversation->type == 'feature_test')
        <div class="row mb-4">
            <div class="col-sm-3">
                <div class="card border-0 bg-primary-lt text-center">
                    <div class="card-body py-3">
                        <div class="h3 mb-1">{{ $messageStats['total_messages'] }}</div>
                        <div class="text-muted small">{{ __('ai::admin.total_messages') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 bg-success-lt text-center">
                    <div class="card-body py-3">
                        <div class="h3 mb-1">{{ ai_format_token_count($messageStats['total_tokens']) }}</div>
                        <div class="text-muted small">{{ __('ai::admin.token_usage') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 bg-info-lt text-center">
                    <div class="card-body py-3">
                        <div class="h3 mb-1">
                            @if($messageStats['avg_processing_time'])
                                {{ number_format($messageStats['avg_processing_time'] / 1000, 1) }} sn
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-muted small">{{ __('ai::admin.average_time') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 bg-warning-lt text-center">
                    <div class="card-body py-3">
                        <div class="h3 mb-1">
                            @if($conversation->is_demo)
                                <i class="fas fa-wand-magic-sparkles text-blue"></i>
                            @else
                                <i class="fas fa-robot text-green"></i>
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ $conversation->is_demo ? __('ai::admin.demo_mode') : __('ai::admin.real_ai') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Messages -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history me-2"></i>
                            {{ __('ai::admin.conversation_history') }}
                            <span class="badge badge-outline ms-2">{{ $messages->count() }} {{ __('ai::admin.message') }}</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="chat-container" id="chat-container">
                            <div class="chat-messages p-3">
                                @foreach($messages as $message)
                                    <div class="message {{ $message->role == 'assistant' ? 'ai-message' : 'user-message' }}">
                                        <div class="message-content">
                                            @if($message->role == 'assistant' && $message->has_markdown && $message->html_content)
                                                {!! $message->html_content !!}
                                            @elseif($message->role == 'assistant')
                                                {!! nl2br(e($message->content)) !!}
                                            @else
                                                <p>{{ $message->content }}</p>
                                            @endif
                                        </div>
                                        @if($message->role == 'assistant')
                                            <div class="message-actions">
                                                <button class="btn btn-sm btn-ghost-secondary copy-message" 
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ __('ai::admin.copy_message') }}"
                                                        onclick="copyToClipboard('{{ addslashes(strip_tags($message->content)) }}')">
                                                    <i class="fa-thin fa-copy"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($message->meta_text)
                                        <div class="message-meta text-muted small mb-3">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ $message->meta_text }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    @if($conversation->type == 'chat')
                    <div class="card-footer bg-light border-0">
                        <div class="text-center">
                            <a href="{{ route('admin.ai.index') }}?conversation={{ $conversation->id }}" class="btn btn-primary">
                                <i class="fas fa-reply me-2"></i>{{ __('ai::admin.continue_conversation') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($conversation->type == 'feature_test' && !$conversation->is_demo)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info border-0">
                    <div class="d-flex align-items-center">
                        <div class="alert-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h4 class="alert-title">{{ __('ai::admin.token_usage') }}</h4>
                            {{ __('ai::admin.token_consumption_info', ['tokens' => ai_format_token_count($conversation->total_tokens_used)]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .chat-container {
        height: auto;
        min-height: 300px;
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

    .message-meta {
        text-align: center;
        padding: 0 1rem;
    }

    /* Avatar improvements */
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .avatar-sm {
        height: 2rem;
        width: 2rem;
    }

    .avatar-lg {
        height: 3.5rem;
        width: 3.5rem;
        font-size: 1.25rem;
    }

    /* Markdown stilleri - Admin chat panel'den kopyalandı */
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

    @media (max-width: 768px) {
        .message {
            max-width: 90%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // {{ __('ai::admin.success.message_copied') }}
            console.log('{{ __('ai::admin.message_copied_to_clipboard') }}');
        }).catch(err => {
            console.error('{{ __('ai::admin.copy_error') }}:', err);
        });
    }

    // Tooltip'leri etkinleştir
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Tooltip !== 'undefined') {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
                new Tooltip(element);
            });
        }
    });
</script>
@endpush