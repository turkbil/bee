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
                                    <div class="message {{ $chatMessage->role == 'assistant' ? 'message-assistant' : 'message-user' }} mb-3">
                                        <div class="message-avatar">
                                            @if ($chatMessage->role == 'assistant')
                                                <span class="avatar bg-primary-lt">AI</span>
                                            @else
                                                <span class="avatar bg-secondary-lt">
                                                    {{ substr(auth()->user()->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="message-content">
                                            <div class="message-bubble">
                                                {!! nl2br(e($chatMessage->content)) !!}
                                            </div>
                                            <div class="message-footer text-muted">
                                                {{ $chatMessage->created_at->format('H:i') }}
                                                <span class="ms-2">{{ $chatMessage->tokens }} token</span>
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
    
    html[data-bs-theme="dark"] .message-bubble {
        background-color: var(--tblr-bg-surface-secondary);
    }
    
    html[data-bs-theme="dark"] .message-user .message-bubble {
        background-color: var(--tblr-primary);
        color: var(--tblr-white);
    }
</style>
@endpush

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
        
        // Livewire mesaj güncellemelerinden sonra aşağı kaydır
        Livewire.on('messagesUpdated', () => {
            setTimeout(scrollToBottom, 100);
        });
        
        // DOM güncellemelerini izle
        const observer = new MutationObserver(scrollToBottom);
        const container = document.getElementById('chat-container');
        
        if (container) {
            observer.observe(container, { childList: true, subtree: true });
        }
        
        // Kullanıcı adının ilk iki harfini JS değişkeni olarak tanımla
        const auth_user_name_first_2_chars = '{{ substr(auth()->user()->name, 0, 2) }}';
        
        // Geçici mesaj ve loader için değişkenler
        let messageSubmitted = false;
        let tempMessageElement = null;
        let tempBotLoadingElement = null;
        
        // Enter ve Shift+Enter olaylarını yönet
        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    if (e.shiftKey) {
                        // Shift+Enter: Alt satıra geç (varsayılan davranış)
                        return true;
                    } else {
                        // Enter: Mesajı gönder
                        e.preventDefault();
                        
                        // Boş mesaj kontrolü
                        if (messageInput.value.trim() !== '' && !messageSubmitted) {
                            // Çift gönderimi önle
                            messageSubmitted = true;
                            
                            // Mesaj gönderme formunu manuel olarak tetikle
                            document.getElementById('message-form').dispatchEvent(
                                new Event('submit', { cancelable: true, bubbles: true })
                            );
                            
                            // Kullanıcı mesajını ekle ve bot yanıtı için yükleniyor göster
                            const content = messageInput.value;
                            addUserMessageAndBotLoading(content);
                            
                            // Input alanını temizle (UI'da hemen temizlenir)
                            messageInput.value = '';
                            
                            // Livewire modelini güncellemek için dispatch (gerçek veri güncellemesi)
                            Livewire.dispatch('input', { target: { name: 'message', value: '' }});
                        }
                    }
                }
            });
        }
        
        // Kullanıcı mesajını ve bot yükleniyor göstergesini ekle
        function addUserMessageAndBotLoading(content) {
            const container = document.getElementById('chat-container');
            if (!container) return;
            
            // Boş durumu kaldır
            const emptyDiv = container.querySelector('.empty');
            if (emptyDiv) {
                emptyDiv.remove();
            }
            
            // Önceki geçici öğeleri temizle
            clearTemporaryElements();
            
            // 1. Geçici kullanıcı mesajı oluştur
            tempMessageElement = document.createElement('div');
            tempMessageElement.className = 'message message-user mb-3 temp-message';
            tempMessageElement.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar bg-secondary-lt">
                        ${auth_user_name_first_2_chars}
                    </span>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${content.replace(/\n/g, '<br>')}
                    </div>
                    <div class="message-footer text-muted">
                        ${getCurrentTime()}
                    </div>
                </div>
            `;
            
            // 2. Geçici bot yükleniyor mesajı oluştur
            tempBotLoadingElement = document.createElement('div');
            tempBotLoadingElement.className = 'message message-assistant mb-3 temp-bot-loading';
            tempBotLoadingElement.innerHTML = `
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
            container.appendChild(tempMessageElement);
            container.appendChild(tempBotLoadingElement);
            
            // Aşağı kaydır
            scrollToBottom();
        }
        
        // Geçici öğeleri temizle
        function clearTemporaryElements() {
            const tempMessages = document.querySelectorAll('.temp-message');
            const tempBotLoading = document.querySelectorAll('.temp-bot-loading');
            
            tempMessages.forEach(msg => msg.remove());
            tempBotLoading.forEach(bot => bot.remove());
            
            tempMessageElement = null;
            tempBotLoadingElement = null;
        }
        
        // Geçerli zamanı biçimlendir
        function getCurrentTime() {
            const now = new Date();
            return now.getHours().toString().padStart(2, '0') + ':' + 
                   now.getMinutes().toString().padStart(2, '0');
        }
        
        // Mesaj gönderimi başlamadan önce
        Livewire.hook('message.sent', (message, component) => {
            if (message.updateQueue && message.updateQueue.some(update => update.payload.method === 'sendMessage')) {
                // Form gönderildi, geçici mesajları ekle (eğer henüz eklenmemişse)
                if (!tempMessageElement && !tempBotLoadingElement && messageInput.value.trim() !== '') {
                    addUserMessageAndBotLoading(messageInput.value);
                    messageInput.value = '';
                }
            }
        });
        
        // Livewire yanıtı işlendikten sonra
        Livewire.hook('message.processed', (message, component) => {
            // Mesaj işlendi, geçici öğeleri temizle
            clearTemporaryElements();
            
            // Mesaj gönderimini tekrar etkinleştir
            messageSubmitted = false;
        });
        
        // Livewire bağlantısı kesilirse veya hata oluşursa
        Livewire.hook('message.failed', (message, component) => {
            // Hata durumunda da geçici öğeleri temizle ve gönderimi etkinleştir
            clearTemporaryElements();
            messageSubmitted = false;
            
            // Hata mesajı göster
            alert('Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
        });
        
        // Submit butonu ile mesaj gönderme
        document.getElementById('message-form').addEventListener('submit', function(e) {
            if (!messageSubmitted && messageInput.value.trim() !== '') {
                messageSubmitted = true;
                addUserMessageAndBotLoading(messageInput.value);
            }
        });
    });
</script>
@endpush