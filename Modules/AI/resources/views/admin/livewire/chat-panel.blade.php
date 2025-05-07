@include('ai::admin.helper')

<div class="page-body">
    <div class="container-xl">
        @if ($error)
        <div class="alert alert-warning mb-3">
            <div class="d-flex">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                </div>
                <div>
                    {{ $error }}
                    <button type="button" class="btn btn-sm btn-warning ms-3" wire:click="retryLastMessage">
                        <i class="fas fa-redo-alt me-1"></i> Yeniden Dene
                    </button>
                </div>
            </div>
        </div>
        @endif
        
        <div class="row g-3">
            <!-- Konuşma Listesi - Sol Sütun -->
            <div class="col-12 col-lg-4 d-flex flex-column">
                <div class="card flex-grow-1 d-flex flex-column">
                    <div class="card-header">
                        <h3 class="card-title">Konuşmalarım</h3>
                        <div class="card-actions">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new-conversation-modal">
                                <i class="fas fa-plus me-2"></i> Yeni Konuşma
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 overflow-auto flex-grow-1">
                        @if (count($conversations) > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($conversations as $conversation)
                            <a href="#" wire:click.prevent="selectConversation({{ $conversation->id }})" 
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $conversationId == $conversation->id ? 'active' : '' }}">
                                <div class="conversation-title">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comment me-2"></i>
                                        <span>{{ \Illuminate\Support\Str::limit($conversation->title, 25) }}</span>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $conversation->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-ghost-danger" wire:click.stop="deleteConversation({{ $conversation->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="empty">
                            <div class="empty-img">
                                <i class="fas fa-comments fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">Henüz konuşma yok</p>
                            <p class="empty-subtitle text-muted">
                                Yeni bir konuşma başlatmak için yukarıdaki butona tıklayın.
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted small">
                                <span data-bs-toggle="tooltip" title="Günlük kalan limit">
                                    <i class="fas fa-bolt text-primary"></i> {{ number_format($remainingDaily) }}
                                </span>
                            </div>
                            <div class="text-muted small">
                                <span data-bs-toggle="tooltip" title="Aylık kalan limit">
                                    <i class="fas fa-calendar-alt text-info"></i> {{ number_format($remainingMonthly) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konuşma İçeriği - Sağ Sütun -->
            <div class="col-12 col-lg-8 d-flex flex-column">
                <div class="card flex-grow-1 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            @if ($conversationId)
                                {{ optional($conversations->firstWhere('id', $conversationId))->title }}
                            @else
                                AI Asistan
                            @endif
                        </h3>
                        @if($conversationId)
                        <div class="card-actions">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" wire:click.prevent="retryLastMessage">
                                        <i class="fas fa-redo-alt me-2"></i> Son mesajı yeniden dene
                                    </a>
                                    <a href="{{ route('admin.ai.conversations.show', $conversationId) }}" class="dropdown-item">
                                        <i class="fas fa-external-link-alt me-2"></i> Yeni sayfada aç
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column" style="height: 500px;">
                        <!-- Mesaj Listesi -->
                        <div class="chat-messages overflow-auto mb-3 flex-grow-1" id="chat-container">
                            @if (count($messages) > 0)
                                @foreach ($messages as $chatMessage)
                                    <div class="message {{ $chatMessage['role'] == 'assistant' ? 'message-assistant' : 'message-user' }} mb-3" id="message-{{ $chatMessage['id'] }}">
                                        <div class="message-avatar">
                                            @if ($chatMessage['role'] == 'assistant')
                                                <span class="avatar bg-primary-lt">AI</span>
                                            @else
                                                <span class="avatar bg-secondary-lt">
                                                    {{ substr(auth()->user()->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="message-content">
                                            <div class="message-bubble" id="bubble-{{ $chatMessage['id'] }}">
                                                {!! nl2br(htmlspecialchars($chatMessage['content'])) !!}
                                            </div>
                                            <div class="message-footer text-muted">
                                                {{ \Carbon\Carbon::parse($chatMessage['created_at'])->format('H:i') }}
                                                <span class="ms-2">{{ $chatMessage['tokens'] }} token</span>
                                                
                                                @if ($chatMessage['role'] == 'assistant')
                                                <div class="float-end">
                                                    <a href="#" class="text-muted copy-message" data-message-id="{{ $chatMessage['id'] }}" title="Metni kopyala">
                                                        <i class="fas fa-copy"></i>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-robot fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">AI Asistanla Sohbet</p>
                                    <p class="empty-subtitle text-muted">
                                        Merhaba! Ben AI asistanım. Size nasıl yardımcı olabilirim?
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Mesaj Gönderme Formu -->
                        <div class="message-form">
                            <form id="chat-form">
                                <div class="input-group">
                                    <textarea
                                        id="message-input"
                                        class="form-control"
                                        placeholder="Mesajınızı yazın..."
                                        rows="2"
                                        {{ $loading ? 'disabled' : '' }}
                                    ></textarea>
                                    <button type="submit" class="btn btn-primary" {{ $loading ? 'disabled' : '' }}>
                                        @if($loading)
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Yanıt alınıyor...
                                        @else
                                            <i class="fas fa-paper-plane me-2"></i> Gönder
                                        @endif
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yeni Konuşma Modal -->
        <div wire:ignore.self class="modal modal-blur fade" id="new-conversation-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni Konuşma</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Konuşma Başlığı</label>
                            <input type="text" class="form-control" wire:model.defer="title" placeholder="Örn: Proje Planlaması">
                            @error('title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prompt Şablonu (İsteğe Bağlı)</label>
                            <select class="form-select" wire:model.defer="promptId">
                                <option value="">Şablon Seçin</option>
                                @foreach ($prompts as $prompt)
                                    <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                            İptal
                        </button>
                        <button type="button" class="btn btn-primary ms-auto" wire:click="createConversation" data-bs-dismiss="modal">
                            <i class="fas fa-plus me-2"></i> Konuşma Başlat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToBottom = () => {
            const container = document.getElementById('chat-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };
        
        scrollToBottom();
        
        const userInitials = '{{ substr(auth()->user()->name, 0, 2) }}';
        
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        
        if (chatForm && messageInput) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (message !== '') {
                    addUserMessage(message);
                    
                    @this.call('sendMessageAction', message);
                    
                    messageInput.value = '';
                }
            });
            
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    
                    const message = this.value.trim();
                    if (message !== '') {
                        addUserMessage(message);
                        
                        @this.call('sendMessageAction', message);
                        
                        this.value = '';
                    }
                }
            });
        }
        
        // Kopyalama işlevi
        document.addEventListener('click', function(e) {
            if (e.target.closest('.copy-message')) {
                e.preventDefault();
                const messageId = e.target.closest('.copy-message').getAttribute('data-message-id');
                const messageBubble = document.getElementById('bubble-' + messageId);
                
                if (messageBubble) {
                    const textToCopy = messageBubble.innerText;
                    
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        const copyIcon = e.target.closest('.copy-message').querySelector('i');
                        copyIcon.classList.remove('fa-copy');
                        copyIcon.classList.add('fa-check');
                        
                        setTimeout(() => {
                            copyIcon.classList.remove('fa-check');
                            copyIcon.classList.add('fa-copy');
                        }, 2000);
                    });
                }
            }
        });
        
        function addUserMessage(content) {
            const container = document.getElementById('chat-container');
            if (!container) return;
            
            const emptyDiv = container.querySelector('.empty');
            if (emptyDiv) {
                emptyDiv.remove();
            }
            
            const tempId = 'temp-' + Date.now();
            
            const tempUserMessage = document.createElement('div');
            tempUserMessage.className = 'message message-user mb-3';
            tempUserMessage.id = tempId;
            tempUserMessage.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar bg-secondary-lt">${userInitials}</span>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${escapeHtml(content).replace(/\n/g, '<br>')}
                    </div>
                    <div class="message-footer text-muted">
                        ${getCurrentTime()}
                        <span class="ms-2">0 token</span>
                    </div>
                </div>
            `;
            
            // Yükleniyor mesajını ekle
            const loadingMessage = document.createElement('div');
            loadingMessage.className = 'message message-assistant mb-3';
            loadingMessage.id = 'loading-message';
            loadingMessage.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar bg-primary-lt">AI</span>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        <div class="ai-response" id="stream-response"></div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="message-footer text-muted">
                        ${getCurrentTime()}
                    </div>
                </div>
            `;
            
            container.appendChild(tempUserMessage);
            container.appendChild(loadingMessage);
            scrollToBottom();
        }
        
        function getCurrentTime() {
            const now = new Date();
            return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Livewire olaylarını dinle
        document.addEventListener('livewire:initialized', () => {
            // DOM güncellemeleri için observer
            const observer = new MutationObserver(scrollToBottom);
            const container = document.getElementById('chat-container');
            if (container) {
                observer.observe(container, { childList: true, subtree: true });
            }
            
            // Stream başladığında (AI yanıt vermeye başladığında)
            Livewire.on('streamStart', ({ messageId }) => {
                console.log('Stream başladı, messageId:', messageId);
                
                // Yükleniyor mesajını kaldır
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.remove();
                }
                
                // Yeni mesaj elementini oluştur
                const container = document.getElementById('chat-container');
                const messageElement = document.createElement('div');
                messageElement.className = 'message message-assistant mb-3';
                messageElement.id = 'message-' + messageId;
                messageElement.innerHTML = `
                    <div class="message-avatar">
                        <span class="avatar bg-primary-lt">AI</span>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble" id="bubble-${messageId}">
                            <span class="ai-cursor"></span>
                        </div>
                        <div class="message-footer text-muted">
                            ${getCurrentTime()}
                            <span class="ms-2">0 token</span>
                            <div class="float-end">
                                <a href="#" class="text-muted copy-message" data-message-id="${messageId}" title="Metni kopyala">
                                    <i class="fas fa-copy"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                
                container.appendChild(messageElement);
                scrollToBottom();
            });
            
            // Her yeni parça geldiğinde
            Livewire.on('streamChunk', ({ messageId, content }) => {
                console.log('Stream parçası alındı, messageId:', messageId, 'içerik:', content);
                
                const messageBubble = document.getElementById('bubble-' + messageId);
                if (messageBubble) {
                    // İmleç olup olmadığını kontrol et
                    const hasAiCursor = messageBubble.querySelector('.ai-cursor');
                    
                    // İmleç varsa, içeriği yanına ekleyip imleç yeniden ekle
                    if (hasAiCursor) {
                        const currentText = messageBubble.innerHTML.replace(/<span class="ai-cursor"><\/span>/, '');
                        messageBubble.innerHTML = currentText + escapeHtml(content).replace(/\n/g, '<br>') + '<span class="ai-cursor"></span>';
                    } else {
                        // İmleç yoksa, içeriği ekle ve sonuna imleç ekle
                        messageBubble.innerHTML = (messageBubble.innerHTML || '') + escapeHtml(content).replace(/\n/g, '<br>') + '<span class="ai-cursor"></span>';
                    }
                    
                    scrollToBottom();
                }
            });
            
            // Stream bittiğinde
            Livewire.on('streamEnd', ({ messageId }) => {
                console.log('Stream tamamlandı, messageId:', messageId);
                
                const messageBubble = document.getElementById('bubble-' + messageId);
                if (messageBubble) {
                    // İmleci kaldır
                    messageBubble.innerHTML = messageBubble.innerHTML.replace(/<span class="ai-cursor"><\/span>/, '');
                }
                
                Livewire.dispatch('streamComplete');
                scrollToBottom();
            });
            
            // Diğer Livewire işlemleri tamamlandığında
            Livewire.hook('message.processed', () => {
                scrollToBottom();
            });
        });
    });
</script>
@endpush

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
        background-color: var(--tblr-bg-surface);
        white-space: pre-wrap;
        word-break: break-word;
    }
    
    .message-user .message-bubble {
        background-color: var(--tblr-primary);
        color: var(--tblr-white);
    }
    
    .message-footer {
        font-size: 0.75rem;
        margin-top: 5px;
        color: var(--tblr-secondary);
    }
    
    .typing-indicator {
        display: flex;
        align-items: center;
    }
    
    .typing-indicator span {
        height: 8px;
        width: 8px;
        background-color: var(--tblr-secondary);
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: typing-bounce 1.4s infinite ease-in-out both;
    }
    
    .typing-indicator span:nth-child(1) {
        animation-delay: 0s;
    }
    
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing-bounce {
        0%, 80%, 100% {
            transform: scale(0);
        }
        40% {
            transform: scale(1);
        }
    }
    
    .ai-cursor {
        display: inline-block;
        width: 3px;
        height: 18px;
        background-color: var(--tblr-primary);
        margin-left: 2px;
        animation: blink 0.8s infinite;
        vertical-align: middle;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    
    html[data-bs-theme="dark"] .message-bubble {
        background-color: var(--tblr-bg-surface-secondary);
    }
    
    html[data-bs-theme="dark"] .message-user .message-bubble {
        background-color: var(--tblr-primary);
        color: var(--tblr-white);
    }
    
    #chat-container pre {
        white-space: pre-wrap;
    }
    
    #chat-container .ai-response {
        min-height: 1.5em;
    }
    
    .copy-message {
        opacity: 0.6;
        transition: opacity 0.2s;
    }
    
    .copy-message:hover {
        opacity: 1;
    }
    
    .message:hover .copy-message {
        opacity: 1;
    }
</style>
@endpush