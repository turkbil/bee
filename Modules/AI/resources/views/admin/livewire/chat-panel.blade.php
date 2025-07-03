<div
    data-t-warning="{{ __('ai::admin.status.warning') }}"
    data-t-empty-message="{{ __('ai::admin.js.please_enter_message') }}"
    data-t-typing="{{ __('ai::admin.typing') }}"
    data-t-copied="{{ __('ai::admin.js.copied_title') }}"
    data-t-message-copied="{{ __('ai::admin.success.message_copied') }}"
    data-t-server-error="{{ __('ai::admin.js.server_response_failed') }}"
    data-t-reset-confirm="{{ __('ai::admin.confirm.reset_conversation') }}"
    data-t-greeting="{{ __('ai::admin.info.greeting') }}"
    data-t-successful="{{ __('ai::admin.js.success_title') }}"
    data-t-reset-success="{{ __('ai::admin.success.conversation_reset') }}"
    data-t-error="{{ __('ai::admin.js.error_title') }}"
    data-t-conversation-copied="{{ __('ai::admin.success.conversation_copied') }}"
    data-t-you="{{ __('ai::admin.general.you') }}"
    data-t-ai="{{ __('ai::admin.general.ai') }}"
    data-t-connection-error="{{ __('ai::admin.js.connection_error_occurred') }}"
    data-t-retry="{{ __('ai::admin.js.retry_action') }}"
    data-t-generic-error="{{ __('ai::admin.js.generic_error') }}"
>
    @include('ai::admin.helper')
        @if (session('error'))
        <div class="alert alert-warning mb-3">
            <div class="d-flex">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                </div>
                <div>
                    {{ session('error') }}
                    <button type="button" class="btn btn-sm btn-warning ms-3" wire:click="retryLastMessage">
                        <i class="fas fa-redo-alt me-1"></i> {{ __('ai::admin.retry') }}
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
                            <h3 class="card-title">{{ __('ai::admin.ai_assistant') }}</h3>
                            <div class="d-flex align-items-center">
                                <!-- Prompt Seçimi -->
                                <div class="me-3">
                                    <select id="prompt-selector" class="form-select"
                                        onchange="promptSelected(this.value)">
                                        @foreach($prompts as $prompt)
                                        <option value="{{ $prompt->id }}" {{ $selectedPromptId==$prompt->id ? 'selected' : ''
                                            }}>
                                            {{ $prompt->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-thin fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item reset-conversation" href="javascript:void(0)"><i
                                                    class="fa-thin fa-rotate me-2"></i>{{ __('ai::admin.reset_conversation') }}</a></li>
                                        <li><a class="dropdown-item copy-conversation" href="javascript:void(0)"><i
                                                    class="fa-thin fa-copy me-2"></i>{{ __('ai::admin.copy_conversation') }}</a></li>
                                        <li><a class="dropdown-item new-window" href="{{ route('admin.ai.index') }}"
                                                target="_blank"><i class="fa-thin fa-external-link me-2"></i>{{ __('ai::admin.new_window') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chat-container" id="chat-container">
                            <div class="chat-messages p-3" id="chat-messages">
                                <div class="message ai-message">
                                    <div class="message-content">
                                        <p>{{ __('ai::admin.info.greeting') }}</p>
                                    </div>
                                    <div class="message-actions">
                                        <button class="btn btn-sm btn-ghost-secondary copy-message"
                                            data-bs-toggle="tooltip" title="{{ __('ai::admin.copy_message') }}">
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
                                    placeholder="{{ __('ai::admin.message_placeholder') }}" required></textarea>
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

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast-notification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fa-thin fa-circle-check text-success me-2"></i>
                <strong class="me-auto" id="toast-title">{{ __('ai::admin.status.successful') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ __('ai::admin.close') }}"></button>
            </div>
            <div class="toast-body" id="toast-message">
                {{ __('ai::admin.success.operation_completed') }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function promptSelected(promptId) {
        // Mevcut seçili prompt ile yeni seçilen aynıysa, işlemi pas geç
        const currentPromptId = document.querySelector('#prompt-selector').value;
        if (currentPromptId === promptId) {
            return;
        }
        
        // Livewire metodu çağır
        @this.call('promptSelected', promptId);
    }
    
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
        const promptSelector = document.getElementById('prompt-selector');
        
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
                const warning = chatContainer.dataset.tWarning;
                const emptyMessage = chatContainer.dataset.tEmptyMessage;
                showToast(warning, emptyMessage, 'warning');
                return;
            }
            
            // Kullanıcı mesajını ekle
            addMessage(message, 'user');
            
            // Form alanını temizle
            userMessage.value = '';
            userMessage.style.height = 'auto';
            
            // Yükleniyor göstergesini etkinleştir
            loadingIndicator.style.display = 'block';
            
            // Seçili prompt ID'sini al
            const selectedPromptId = promptSelector ? promptSelector.value : null;
            
            // AI yanıtını stream et
            streamAIResponse(message, conversationId.value, selectedPromptId);
        });
        
        // AI yanıtını stream et
        function streamAIResponse(message, conversationId, promptId) {
            // URL'i oluştur ve prompt ID'sini ekle
            let url = `/ai/stream?message=${encodeURIComponent(message)}&conversation_id=${conversationId}`;
            if (promptId) {
                url += `&prompt_id=${promptId}`;
            }
            
            // EventSource ile bağlantı kur (cache parametresi ile önbelleği devre dışı bırak)
            const eventSource = new EventSource(url + `&_cache=${new Date().getTime()}`);
            
            let aiResponseElement = null;
            let aiResponseContent = null;
            let fullResponse = '';
            
            // Önce AI mesaj elementini oluştur
            aiResponseElement = createMessageElement('', 'ai');
            chatMessages.appendChild(aiResponseElement);
            
            aiResponseContent = aiResponseElement.querySelector('.message-content p');
            
            // Yazıyor animasyonu ekle
            const typingText = chatContainer.dataset.tTyping || 'Yazıyor';
            aiResponseContent.innerHTML = `<span class="typing-animation">${typingText}<span>.</span><span>.</span><span>.</span></span>`;
            
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
                    
                    // HTML güvenliği için
                    aiResponseContent.innerText = fullResponse;
                    
                    // Otomatik kaydırma
                    scrollToBottom();
                }
            };
                        
            // Stream tamamlandığında
            eventSource.addEventListener('complete', function(event) {
                const data = JSON.parse(event.data);
                
                // Markdown kontrolü
                if (data.has_markdown && data.html_content) {
                    // HTML içeriği markdown olarak işaretlenmiş, doğrudan göster
                    aiResponseContent.innerHTML = data.html_content;
                }
                
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
                    
                    // Yeni prompt seçimini konuşmaya kaydetmek gerekirse
                    if (promptId && data.conversation_id) {
                        // CSRF token'ı al
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        // Veri oluştur
                        const requestData = {
                            conversation_id: data.conversation_id,
                            prompt_id: promptId
                        };
                        
                        // Livewire'ın tam sayfa yenilemesini önlemek için doğrudan bir API çağrısı yapalım
                        fetch('/admin/ai/update-conversation-prompt', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(requestData)
                        })
                        .then(response => {
                            if (!response.ok) {
                                const serverError = chatContainer.dataset.tServerError;
                                throw new Error(`${serverError}: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(responseData => {
                            if (responseData.success) {
                                console.log('Konuşma promptu güncellendi:', responseData.message);
                            } else {
                                console.warn('Konuşma promptu güncellenemedi:', responseData.message);
                            }
                        })
                        .catch(error => {
                            console.error('Konuşma promptu güncellenirken hata:', error);
                        });
                    }
                }
            });
            
            // Hata durumunda
            eventSource.addEventListener('error', function(event) {
                const connectionError = chatContainer.dataset.tConnectionError;
                const data = event.data ? JSON.parse(event.data) : { message: connectionError };
                
                // Hata mesajını göster
                const errorText = chatContainer.dataset.tError;
                aiResponseContent.innerHTML = `<span class="text-danger">${errorText}: ${data.message}</span>`;
                
                // Yeniden deneme butonu ekle
                const retryButton = document.createElement('button');
                retryButton.className = 'btn btn-sm btn-outline-danger mt-2';
                const retryText = chatContainer.dataset.tRetry;
                retryButton.innerHTML = retryText;
                retryButton.addEventListener('click', function() {
                    // AI mesaj elementini kaldır
                    chatMessages.removeChild(aiResponseElement);
                    
                    // Yeni istek gönder
                    streamAIResponse(message, conversationId, promptId);
                });
                
                aiResponseElement.querySelector('.message-content').appendChild(retryButton);
                
                // Yükleniyor göstergesini kapat
                loadingIndicator.style.display = 'none';
                
                // EventSource'ı kapat
                eventSource.close();
            });
        }

        // Metni formatla
        function formatMessage(text) {
            // Markdown algıla ve işle
            if (typeof text !== 'string') {
                return '';
            }
            
            // Eğer markdown içeren bir yanıt ise, HTML'i döndür
            if (text.includes('<markdown>') && text.includes('</markdown>')) {
                // Markdown etiketlerini çıkar ve içeriği al
                const markdownContent = text.replace(/<markdown>/g, '').replace(/<\/markdown>/g, '');
                
                // Bu noktada, markdownContent sunucu tarafından HTML'e dönüştürülmüş olmalı
                return markdownContent;
            }
            
            // Markdown işaretleri yoksa, standart metin işleme
            const div = document.createElement('div');
            div.textContent = text;
            let formattedText = div.innerHTML;
            
            // Yeni satırları koru
            formattedText = formattedText.replace(/\n/g, '<br>');
            
            return formattedText;
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
            const confirmText = chatContainer.dataset.tResetConfirm;
            if (confirm(confirmText)) {
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
                        const greetingText = chatContainer.dataset.tGreeting;
                        addMessage(greetingText, 'ai');
                        
                        const successText = chatContainer.dataset.tSuccessful;
                        const resetSuccessText = chatContainer.dataset.tResetSuccess;
                        showToast(successText, resetSuccessText);
                    } else {
                        const errorText = chatContainer.dataset.tError;
                        showToast(errorText, data.message, 'error');
                    }
                })
                .catch(error => {
                    const errorText = chatContainer.dataset.tError;
                    const genericError = chatContainer.dataset.tGenericError;
                    showToast(errorText, genericError, 'error');
                    console.error('Konuşma sıfırlama hatası:', error);
                });
            }
        });
        
        // Tüm konuşmayı kopyala
        document.querySelector('.copy-conversation').addEventListener('click', function() {
            let conversation = '';
            
            document.querySelectorAll('.message').forEach(function(message) {
                const youText = chatContainer.dataset.tYou;
                const aiText = chatContainer.dataset.tAi;
                const role = message.classList.contains('user-message') ? youText : aiText;
                const content = message.querySelector('.message-content p').textContent;
                
                conversation += `${role}: ${content}\n\n`;
            });
            
            copyToClipboard(conversation);
            const copiedText = chatContainer.dataset.tCopied;
            const conversationCopiedText = chatContainer.dataset.tConversationCopied;
            showToast(copiedText, conversationCopiedText);
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

        /* Yanıtlarda kod blokları için syntax highlighting */
        .message-content .hljs {
            display: block;
            overflow-x: auto;
            padding: 0.5em;
            background: #f0f0f0;
            border-radius: 0.25rem;
        }

        .dark .message-content .hljs {
            background: #1e1e1e;
        }
    </style>
    @endpush
</div>