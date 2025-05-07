@include('ai::admin.helper')

<div class="page-body">
    <div class="container-xl">
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
                    </div>
                    <div class="card-body d-flex flex-column" style="height: 500px;">
                        <!-- Mesaj Listesi -->
                        <div class="chat-messages overflow-auto mb-3 flex-grow-1" id="chat-container">
                            @if (count($messages) > 0)
                                @foreach ($messages as $chatMessage)
                                    <div class="message {{ $chatMessage['role'] == 'assistant' ? 'message-assistant' : 'message-user' }} mb-3" data-message-id="{{ $chatMessage['id'] }}">
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
                                            <div class="message-bubble">
                                                {!! nl2br(e($chatMessage['content'])) !!}
                                            </div>
                                            <div class="message-footer text-muted">
                                                {{ \Carbon\Carbon::parse($chatMessage['created_at'])->format('H:i') }}
                                                <span class="ms-2">{{ $chatMessage['tokens'] }} token</span>
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

                            @if ($loading)
                                <div class="message message-assistant mb-3">
                                    <div class="message-avatar">
                                        <span class="avatar bg-primary-lt">AI</span>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-bubble">
                                            <div class="typing-indicator">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Mesaj Gönderme Formu -->
                        <div class="message-form">
                            <form wire:submit.prevent="sendMessage" id="message-form">
                                <div class="input-group">
                                    <textarea
                                        wire:model="message"
                                        class="form-control"
                                        placeholder="Mesajınızı yazın..."
                                        rows="2"
                                        {{ $loading ? 'disabled' : '' }}
                                        id="message-input"
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
                            <input type="text" class="form-control" wire:model="title" placeholder="Örn: Proje Planlaması">
                            @error('title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prompt Şablonu (İsteğe Bağlı)</label>
                            <select class="form-select" wire:model="promptId">
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
    document.addEventListener('livewire:initialized', function () {
        const scrollToBottom = () => {
            const container = document.getElementById('chat-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };
        
        // Sayfa yüklendiğinde aşağı kaydır
        scrollToBottom();
        
        // Kullanıcı adı kısaltması için değişken
        const userInitials = '{{ substr(auth()->user()->name, 0, 2) }}';
        
        // DOM güncellemelerini izle ve aşağı kaydır
        const observer = new MutationObserver(scrollToBottom);
        const container = document.getElementById('chat-container');
        
        if (container) {
            observer.observe(container, { childList: true, subtree: true });
        }
        
        // Enter tuşu ile mesaj gönderme
        const messageInput = document.getElementById('message-input');
        const messageForm = document.getElementById('message-form');
        
        if (messageInput && messageForm) {
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    
                    const message = this.value.trim();
                    if (message !== '') {
                        // Anında mesajı göster (yanıt beklemeden)
                        addTemporaryUserMessage(message);
                        
                        // Mesaj kutusunu temizle
                        this.value = '';
                        
                        // Livewire modeline boş değer gönder
                        @this.set('message', '');
                        
                        // Formu gönder
                        messageForm.dispatchEvent(new Event('submit'));
                    }
                }
            });
        }
        
        // Geçici kullanıcı mesajı ekle
        function addTemporaryUserMessage(content) {
            const container = document.getElementById('chat-container');
            if (!container) return;
            
            // Boş durumu kontrol et
            const emptyDiv = container.querySelector('.empty');
            if (emptyDiv) {
                emptyDiv.remove();
            }
            
            // Geçici kullanıcı mesajı oluştur
            const tempUserMessage = document.createElement('div');
            tempUserMessage.className = 'message message-user mb-3 temp-message';
            tempUserMessage.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar bg-secondary-lt">${userInitials}</span>
                </div>
                <div class="message-content">
                    <div class="message-bubble">${content.replace(/\n/g, '<br>')}</div>
                    <div class="message-footer text-muted">
                        ${getCurrentTime()}
                        <span class="ms-2">0 token</span>
                    </div>
                </div>
            `;
            
            // Yükleniyor mesajı oluştur
            const tempLoadingMessage = document.createElement('div');
            tempLoadingMessage.className = 'message message-assistant mb-3 temp-loading';
            tempLoadingMessage.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar bg-primary-lt">AI</span>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            `;
            
            // Mesajları ekle
            container.appendChild(tempUserMessage);
            container.appendChild(tempLoadingMessage);
            
            // Aşağı kaydır
            scrollToBottom();
        }
        
        // Geçici mesajları temizle
        function clearTemporaryMessages() {
            document.querySelectorAll('.temp-message, .temp-loading').forEach(el => {
                el.remove();
            });
        }
        
        // Güncel zamanı döndür
        function getCurrentTime() {
            const now = new Date();
            return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        }
        
        // Stream olaylarını dinle
        Livewire.on('streamStarted', function({ messageId }) {
            // Geçici mesajları kaldır
            clearTemporaryMessages();
            
            // Mesaj elementini bul
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                const messageBubble = messageElement.querySelector('.message-bubble');
                if (messageBubble) {
                    // Cursor ekle
                    messageBubble.innerHTML = '<span class="ai-cursor"></span>';
                }
            }
            
            scrollToBottom();
        });
        
        // Stream içeriği gelince
        Livewire.on('receiveContent', function({ messageId, content }) {
            // Boş içeriği atla
            if (!content || content === '') return;
            
            // Mesaj elementini bul
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                const messageBubble = messageElement.querySelector('.message-bubble');
                if (messageBubble) {
                    // İçeriği ekle ve cursor'u koru
                    const currentContent = messageBubble.innerHTML.replace('<span class="ai-cursor"></span>', '');
                    messageBubble.innerHTML = currentContent + escapeHtml(content) + '<span class="ai-cursor"></span>';
                    
                    // Aşağı kaydır
                    scrollToBottom();
                }
            }
        });
        
        // Stream tamamlandı
        Livewire.on('streamComplete', function({ messageId }) {
            // Mesaj elementini bul
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                const messageBubble = messageElement.querySelector('.message-bubble');
                if (messageBubble) {
                    // Cursor'u kaldır
                    messageBubble.innerHTML = messageBubble.innerHTML.replace('<span class="ai-cursor"></span>', '');
                }
            }
            
            scrollToBottom();
        });
        
        // Stream hatası
        Livewire.on('streamError', function({ error }) {
            console.error('Stream hatası:', error);
            clearTemporaryMessages();
            alert('Yanıt alınamadı: ' + error);
        });
        
        // HTML karakterleri escape et
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/\n/g, '<br>');
        }
        
        // Livewire isteklerini dinle
        Livewire.hook('message.processed', (message, component) => {
            scrollToBottom();
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
</style>
@endpush