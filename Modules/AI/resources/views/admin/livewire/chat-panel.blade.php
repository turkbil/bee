@extends('admin.layout')

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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">AI Asistan</h3>
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa-thin fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item reset-conversation" href="javascript:void(0)"><i
                                                class="fa-thin fa-rotate me-2"></i>Konuşmayı Sıfırla</a></li>
                                    <li><a class="dropdown-item copy-conversation" href="javascript:void(0)"><i
                                                class="fa-thin fa-copy me-2"></i>Tüm Konuşmayı Kopyala</a></li>
                                    <li><a class="dropdown-item new-window" href="{{ route('admin.chat') }}"
                                            target="_blank"><i class="fa-thin fa-external-link me-2"></i>Yeni Pencerede
                                            Aç</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chat-container" id="chat-container">
                            <div class="chat-messages p-3" id="chat-messages">
                                <div class="message ai-message">
                                    <div class="message-content">
                                        <p>Merhaba! Size nasıl yardımcı olabilirim?</p>
                                    </div>
                                    <div class="message-actions">
                                        <button class="btn btn-sm btn-ghost-secondary copy-message"
                                            data-bs-toggle="tooltip" title="Mesajı Kopyala">
                                            <i class="fa-thin fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form id="message-form" class="d-flex align-items-start gap-2">
                            <input type="hidden" id="conversation-id" value="{{ md5(time() . rand(1000, 9999)) }}">
                            <div class="w-100 position-relative">
                                <textarea id="user-message" class="form-control" rows="1"
                                    placeholder="Mesajınızı yazın..." required></textarea>
                                <div id="loading-indicator" class="position-absolute"
                                    style="display: none; right: 10px; bottom: 10px;">
                                    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary align-self-end">
                                <i class="fa-thin fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast-notification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fa-thin fa-circle-check text-success me-2"></i>
            <strong class="me-auto" id="toast-title">Başarılı</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Kapat"></button>
        </div>
        <div class="toast-body" id="toast-message">
            İşlem başarıyla tamamlandı.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const userMessage = document.getElementById('user-message');
    const chatMessages = document.getElementById('chat-messages');
    const conversationId = document.getElementById('conversation-id');
    const loadingIndicator = document.getElementById('loading-indicator');
    const chatContainer = document.getElementById('chat-container');
    const toastNotification = document.getElementById('toast-notification');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    
    // Toast öğesini initialize et
    let toast;
    
    if (typeof Toasts !== 'undefined') {
        // Tabler.io toast kullanımı
        toast = {
            show: function() {
                Toasts.add({
                    title: toastTitle.textContent,
                    content: toastMessage.textContent,
                    icon: toastNotification.querySelector('.toast-header i').className,
                    timeout: 3000
                });
            }
        };
    } else {
        // Fallback - varsayılan tarayıcı alert kullanımı
        toast = {
            show: function() {
                alert(toastMessage.textContent);
            }
        };
    }
    
    // Textarea otomatik yükseklik ayarı
    userMessage.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Enter tuşuna basıldığında form gönderimi
    userMessage.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (userMessage.value.trim() !== '') {
                messageForm.dispatchEvent(new Event('submit'));
            }
        }
    });
    
    // Mesaj gönderimi
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = userMessage.value.trim();
        if (!message) {
            showToast('Uyarı', 'Lütfen bir mesaj yazın.', 'warning');
            return;
        }
        
        // Kullanıcı mesajını ekle
        addMessage(message, 'user');
        
        // Form alanını temizle
        userMessage.value = '';
        userMessage.style.height = 'auto';
        
        // Yükleniyor göstergesini etkinleştir
        loadingIndicator.style.display = 'block';
        
        // AI yanıtını stream et
        streamAIResponse(message, conversationId.value);
    });
    
    // AI yanıtını stream et
    function streamAIResponse(message, conversationId) {
        const eventSource = new EventSource(`/ai/stream?message=${encodeURIComponent(message)}&conversation_id=${conversationId}`);
        
        let aiResponseElement = null;
        let aiResponseContent = null;
        let fullResponse = '';
        
        // Önce AI mesaj elementini oluştur
        aiResponseElement = createMessageElement('', 'ai');
        chatMessages.appendChild(aiResponseElement);
        
        aiResponseContent = aiResponseElement.querySelector('.message-content p');
        
        // Yazıyor animasyonu ekle
        aiResponseContent.innerHTML = '<span class="typing-animation">Yazıyor<span>.</span><span>.</span><span>.</span></span>';
        
        // Stream veri alındığında
        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            if (data.content) {
                // Yazıyor animasyonunu kaldır
                if (fullResponse === '') {
                    aiResponseContent.innerHTML = '';
                }
                
                // AI yanıtını ekle
                fullResponse += data.content;
                // innerText kullanarak formatlamayı önle
                aiResponseContent.innerText = fullResponse;
                
                // Otomatik kaydırma
                scrollToBottom();
            }
        };
        
        // Stream tamamlandığında
        eventSource.addEventListener('complete', function(event) {
            const data = JSON.parse(event.data);
            
            // Butonları etkinleştir
            const copyButton = aiResponseElement.querySelector('.copy-message');
            copyButton.addEventListener('click', function() {
                copyToClipboard(fullResponse);
                showToast('Kopyalandı', 'Mesaj panoya kopyalandı.');
            });
            
            // Yükleniyor göstergesini kapat
            loadingIndicator.style.display = 'none';
            
            // EventSource'ı kapat
            eventSource.close();
            
            // Konuşma ID'sini güncelle
            if (data.conversation_id) {
                conversationId.value = data.conversation_id;
            }
        });
        
        // Hata durumunda
        eventSource.addEventListener('error', function(event) {
            const data = event.data ? JSON.parse(event.data) : { message: 'Bağlantı hatası oluştu.' };
            
            // Hata mesajını göster
            aiResponseContent.innerHTML = `<span class="text-danger">Hata: ${data.message}</span>`;
            
            // Yeniden deneme butonu ekle
            const retryButton = document.createElement('button');
            retryButton.className = 'btn btn-sm btn-outline-danger mt-2';
            retryButton.innerHTML = 'Yeniden Dene';
            retryButton.addEventListener('click', function() {
                // AI mesaj elementini kaldır
                chatMessages.removeChild(aiResponseElement);
                
                // Yeni istek gönder
                streamAIResponse(message, conversationId.value);
            });
            
            aiResponseElement.querySelector('.message-content').appendChild(retryButton);
            
            // Yükleniyor göstergesini kapat
            loadingIndicator.style.display = 'none';
            
            // EventSource'ı kapat
            eventSource.close();
        });
    }

    // Kelime kelime metni ekrana yazan fonksiyon
    function writeWordsOneByOne(text, container, cursorElement, isLast = false) {
        // Metin içindeki kelimeleri ve boşlukları diziye çevir
        const words = text.split(/(\s+)/g);
        
        // Mevcut içeriği alalım
        let currentContent = container.innerHTML;
        
        // İmleç varsa kaldır
        if (container.contains(cursorElement)) {
            container.removeChild(cursorElement);
        }
        
        // Kelimeleri sırayla ekle
        words.forEach((word, index) => {
            setTimeout(() => {
                // Önceki içerik (imleç olmadan)
                currentContent = container.innerHTML;
                
                // Kelimeyi ekleyelim
                const formattedWord = word.replace(/\n/g, '<br>');
                container.innerHTML = currentContent + formattedWord;
                
                // İmleç ekle
                container.appendChild(cursorElement);
                
                // Otomatik kaydırma
                scrollToBottom();
            }, index * 50); // Her kelime için 50ms gecikme
        });
    }
    // Mesaj ekle
    function addMessage(content, role) {
        const messageElement = createMessageElement(content, role);
        chatMessages.appendChild(messageElement);
        
        scrollToBottom();
        
        if (role === 'user') {
            return messageElement;
        }
        
        // Sadece AI mesajları için kopyalama butonu ekle
        const copyButton = messageElement.querySelector('.copy-message');
        copyButton.addEventListener('click', function() {
            copyToClipboard(content);
            showToast('Kopyalandı', 'Mesaj panoya kopyalandı.');
        });
        
        return messageElement;
    }
    
    // Mesaj elementi oluştur
    function createMessageElement(content, role) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role}-message`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        const paragraph = document.createElement('p');
        paragraph.innerHTML = role === 'user' ? escapeHtml(content) : (content ? formatMessage(content) : '');
        
        contentDiv.appendChild(paragraph);
        messageDiv.appendChild(contentDiv);
        
        // AI mesajı için kopyalama butonu ekle
        if (role === 'ai') {
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'message-actions';
            
            const copyButton = document.createElement('button');
            copyButton.className = 'btn btn-sm btn-ghost-secondary copy-message';
            copyButton.setAttribute('data-bs-toggle', 'tooltip');
            copyButton.setAttribute('title', 'Mesajı Kopyala');
            copyButton.innerHTML = '<i class="fa-thin fa-copy"></i>';
            
            actionsDiv.appendChild(copyButton);
            messageDiv.appendChild(actionsDiv);
        }
        
        return messageDiv;
    }
    
    // Metni formatla
    function formatMessage(text) {
        // HTML karakterlerini escape et ama markdown işaretlemelerini koru
        const div = document.createElement('div');
        div.textContent = text;
        let formattedText = div.innerHTML;
        
        // Yeni satırları koru
        formattedText = formattedText.replace(/\n/g, '<br>');
        
        return formattedText;
    }
    
    // HTML karakterlerini escape et
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Panoya kopyala
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).catch(err => {
            console.error('Kopyalama hatası:', err);
        });
    }
    
    // En alta kaydır
    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    // Bildirim göster
    function showToast(title, message, type = 'success') {
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        // Toast başlık rengini ayarla
        toastTitle.className = 'me-auto';
        const icon = toastNotification.querySelector('.toast-header i');
        
        switch (type) {
            case 'success':
                icon.className = 'fa-thin fa-circle-check text-success me-2';
                break;
            case 'warning':
                icon.className = 'fa-thin fa-triangle-exclamation text-warning me-2';
                break;
            case 'error':
                icon.className = 'fa-thin fa-circle-exclamation text-danger me-2';
                break;
            default:
                icon.className = 'fa-thin fa-circle-info text-info me-2';
        }
        
        toast.show();
    }
    
    // Konuşmayı sıfırla
    document.querySelector('.reset-conversation').addEventListener('click', function() {
        if (confirm('Konuşma geçmişi sıfırlanacak. Emin misiniz?')) {
            fetch(`/ai/reset?conversation_id=${conversationId.value}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Mesajları temizle
                    chatMessages.innerHTML = '';
                    
                    // Yeni konuşma ID'si oluştur
                    conversationId.value = md5(Date.now() + Math.random().toString());
                    
                    // Hoş geldin mesajını ekle
                    addMessage('Merhaba! Size nasıl yardımcı olabilirim?', 'ai');
                    
                    showToast('Başarılı', 'Konuşma sıfırlandı.');
                } else {
                    showToast('Hata', data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Hata', 'Bir hata oluştu.', 'error');
                console.error('Konuşma sıfırlama hatası:', error);
            });
        }
    });
    
    // Tüm konuşmayı kopyala
    document.querySelector('.copy-conversation').addEventListener('click', function() {
        let conversation = '';
        
        document.querySelectorAll('.message').forEach(function(message) {
            const role = message.classList.contains('user-message') ? 'Siz' : 'AI';
            const content = message.querySelector('.message-content p').textContent;
            
            conversation += `${role}: ${content}\n\n`;
        });
        
        copyToClipboard(conversation);
        showToast('Kopyalandı', 'Tüm konuşma panoya kopyalandı.');
    });
    
    // MD5
    function md5(input) {
        return Array.from(
            new Uint8Array(
                new TextEncoder().encode(input + Date.now().toString() + Math.random().toString())
            )
        )
        .map(b => b.toString(16).padStart(2, "0"))
        .join("")
        .substring(0, 32);
    }
    
    // Sayfa yüklendiğinde otomatik kaydır
    scrollToBottom();
    
    // Tooltips'i etkinleştir - Tabler.io kullanıldığında
    if (typeof Tooltip !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
            new Tooltip(element);
        });
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

    /* Cursor yanıp sönme animasyonu */
    .cursor {
        display: inline-block;
        width: 0.5rem;
        height: 1rem;
        background-color: var(--tblr-body-color, #000);
        animation: blink 1s step-end infinite;
        margin-left: 2px;
        vertical-align: middle;
    }

    .dark .cursor {
        background-color: #fff;
    }

    @keyframes blink {

        from,
        to {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    /* Form stil ayarları */
    textarea.form-control {
        resize: none;
        overflow: hidden;
        min-height: 38px;
        max-height: 200px;
    }

    /* Kod bloğu formatı için */
    code {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
        padding: 2px 4px;
        font-family: monospace;
        font-size: 0.9em;
    }

    .dark code {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Yanıp sönen imleç */
    .typing-indicator {
        display: inline-block;
        width: 2px;
        height: 15px;
        background-color: var(--tblr-body-color, #000);
        margin-left: 2px;
        animation: blink 0.7s infinite;
    }

    .dark .typing-indicator {
        background-color: #fff;
    }

    @media (max-width: 768px) {
        .message {
            max-width: 90%;
        }

        .chat-container {
            height: calc(100vh - 300px);
        }
    }

    .typing-animation {
        display: inline-block;
    }

    .typing-animation span {
        opacity: 0;
        animation: typing-dot 1.4s infinite;
        animation-fill-mode: forwards;
    }

    .typing-animation span:nth-child(1) {
        animation-delay: 0s;
    }

    .typing-animation span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-animation span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing-dot {
        0% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }
</style>
@endpush