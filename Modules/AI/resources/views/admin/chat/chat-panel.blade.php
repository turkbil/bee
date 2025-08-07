@include('ai::helper')

<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fa-thin fa-robot me-2"></i>
                        AI Asistan
                    </h3>
                    <div class="d-flex align-items-center">
                        <!-- Prompt Seçimi -->
                        <div class="me-3">
                            <select id="prompt-selector" class="form-select">
                                @foreach($prompts as $prompt)
                                <option value="{{ $prompt->id }}" {{ $selectedPromptId == $prompt->id ? 'selected' : '' }}>
                                    {{ $prompt->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Eylemler -->
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-thin fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="clearChat()">
                                    <i class="fa-thin fa-trash me-2"></i>Sohbeti Temizle
                                </a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="copyAllMessages()">
                                    <i class="fa-thin fa-copy me-2"></i>Tümünü Kopyala
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Chat Messages Container -->
                <div id="chat-container" class="chat-container">
                    <div id="chat-messages" class="chat-messages p-3">
                        <!-- Başlangıç mesajı -->
                        <div class="message ai-message" id="welcome-message">
                            <div class="message-content">
                                <p>Merhaba! Size nasıl yardımcı olabilirim? 🤖</p>
                            </div>
                            <div class="message-actions">
                                <button class="btn btn-sm btn-ghost-secondary" onclick="copyMessage(this)">
                                    <i class="fa-thin fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <!-- Message Form -->
                <form id="message-form" class="d-flex align-items-end gap-2">
                    <div class="flex-grow-1 position-relative">
                        <textarea 
                            id="user-message" 
                            class="form-control" 
                            rows="1" 
                            placeholder="Mesajınızı yazın..." 
                            maxlength="2000"
                            required
                        ></textarea>
                        
                        <!-- Loading Indicator -->
                        <div id="loading-indicator" class="position-absolute d-none" style="right: 10px; bottom: 10px;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="send-button">
                        <i class="fa-thin fa-paper-plane"></i>
                        <span class="d-none d-sm-inline ms-1">Gönder</span>
                    </button>
                </form>

                <!-- Character Counter -->
                <div class="text-muted small mt-2">
                    <span id="char-counter">0</span>/2000 karakter
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 400px);
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
        border-bottom: 1px solid var(--tblr-border-color);
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
    }

    .message {
        display: flex;
        max-width: 80%;
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .user-message {
        align-self: flex-end;
        background: linear-gradient(135deg, var(--tblr-primary), #1a73e8);
        color: white;
        border-radius: 18px 18px 6px 18px;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .ai-message {
        align-self: flex-start;
        background: var(--tblr-bg-surface);
        border: 1px solid var(--tblr-border-color);
        border-radius: 18px 18px 18px 6px;
        padding: 12px 16px;
        position: relative;
    }

    .message-content {
        flex: 1;
    }

    .message-content p {
        margin: 0;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .message-actions {
        margin-left: 8px;
        opacity: 0;
        transition: opacity 0.2s ease;
        align-self: flex-start;
    }

    .ai-message:hover .message-actions {
        opacity: 1;
    }

    .user-message .message-content {
        color: white;
    }

    /* Textarea auto-resize */
    #user-message {
        resize: none;
        overflow: hidden;
        min-height: 42px;
        max-height: 120px;
    }

    /* Typing indicator */
    .typing-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--tblr-muted);
        font-style: italic;
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--tblr-muted);
        animation: typingBounce 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typingBounce {
        0%, 80%, 100% { 
            transform: scale(0);
        } 
        40% { 
            transform: scale(1);
        }
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .message {
            max-width: 95%;
        }
        
        .chat-container {
            height: calc(100vh - 320px);
        }
        
        .chat-messages {
            padding: 1rem;
        }
    }

    /* Dark mode support */
    [data-bs-theme="dark"] .ai-message {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Smooth scrolling */
    .chat-container {
        scroll-behavior: smooth;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🚀 SIMPLE AI CHAT SYSTEM - NO CONFLICTS
    
    const chatContainer = document.getElementById('chat-container');
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const userMessage = document.getElementById('user-message');
    const sendButton = document.getElementById('send-button');
    const loadingIndicator = document.getElementById('loading-indicator');
    const charCounter = document.getElementById('char-counter');
    const promptSelector = document.getElementById('prompt-selector');
    
    // State management
    let isProcessing = false;
    let currentConversationId = generateId();
    
    // Initialize
    setupEventListeners();
    updateCharCounter();
    
    function setupEventListeners() {
        // Form submission
        messageForm.addEventListener('submit', handleSubmit);
        
        // Textarea auto-resize
        userMessage.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            updateCharCounter();
        });
        
        // Enter to send (Shift+Enter for new line)
        userMessage.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (userMessage.value.trim() && !isProcessing) {
                    messageForm.dispatchEvent(new Event('submit'));
                }
            }
        });
        
        // Prompt selector change
        promptSelector.addEventListener('change', function() {
            console.log('Prompt changed to:', this.value);
        });
    }
    
    function handleSubmit(e) {
        e.preventDefault();
        
        // Prevent spam
        if (isProcessing) {
            showToast('Lütfen bekleyin', 'Önceki mesaj hala işleniyor...', 'warning');
            return;
        }
        
        const message = userMessage.value.trim();
        if (!message) {
            userMessage.focus();
            return;
        }
        
        // Start processing
        isProcessing = true;
        showLoading(true);
        
        // Add user message
        addMessage(message, 'user');
        
        // Clear input
        userMessage.value = '';
        userMessage.style.height = 'auto';
        updateCharCounter();
        
        // Scroll to bottom
        scrollToBottom();
        
        // Send to AI
        sendToAI(message);
    }
    
    function sendToAI(message) {
        // Add typing indicator
        const typingElement = addTypingIndicator();
        
        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Prepare request data
        const requestData = {
            message: message,
            conversation_id: currentConversationId,
            prompt_id: promptSelector.value,
            _token: csrfToken
        };
        
        // Create AbortController for timeout control
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            console.warn('🚨 AI Request timeout - aborting');
        }, 45000); // 45 saniye timeout
        
        // Send AJAX request with timeout control
        console.log('🚀 SENDING AI REQUEST:', requestData);
        
        fetch('/admin/ai/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData),
            signal: controller.signal // Timeout control
        })
        .then(response => {
            console.log('📥 RESPONSE RECEIVED:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Remove typing indicator
            if (typingElement) {
                typingElement.remove();
            }
            
            if (data.success) {
                // Add AI response
                addMessage(data.response, 'ai');
                
                // Update conversation ID if provided (use numeric ID from backend)
                if (data.conversation_id) {
                    currentConversationId = data.conversation_id; // This will be numeric ID from backend
                }
                
                showToast('Başarılı', 'Mesaj gönderildi', 'success');
            } else {
                addMessage('Üzgünüm, bir hata oluştu: ' + (data.message || 'Bilinmeyen hata'), 'ai', true);
                showToast('Hata', data.message || 'Mesaj gönderilemedi', 'error');
            }
        })
        .catch(error => {
            console.error('🚨 AI Request Error:', error);
            
            // Clear timeout
            clearTimeout(timeoutId);
            
            // Remove typing indicator
            if (typingElement) {
                typingElement.remove();
            }
            
            // Specific error handling
            let errorMessage = 'Bağlantı hatası oluştu. Lütfen tekrar deneyin.';
            let toastMessage = 'Sunucuya ulaşılamıyor';
            
            if (error.name === 'AbortError') {
                errorMessage = 'İstek zaman aşımına uğradı. Lütfen tekrar deneyin.';
                toastMessage = 'Zaman aşımı (45 saniye)';
                console.warn('🚨 Request aborted due to timeout');
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Ağ bağlantısı sorunu. İnternet bağlantınızı kontrol edin.';
                toastMessage = 'Ağ bağlantısı hatası';
            } else if (error.message.includes('HTTP 413')) {
                errorMessage = 'Mesaj çok uzun. Lütfen kısa mesajlar gönderin.';
                toastMessage = 'Payload çok büyük';
            } else if (error.message.includes('HTTP 5')) {
                errorMessage = 'Sunucu hatası. Lütfen bir kaç saniye sonra tekrar deneyin.';
                toastMessage = 'Sunucu hatası';
            }
            
            addMessage(errorMessage, 'ai', true);
            showToast('Bağlantı Hatası', toastMessage, 'error');
        })
        .finally(() => {
            // Clear timeout in all cases
            clearTimeout(timeoutId);
            
            isProcessing = false;
            showLoading(false);
            userMessage.focus();
            
            console.log('🔄 AI request cycle completed');
        });
    }
    
    function addMessage(content, role, isError = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role}-message`;
        
        // Add error class if needed
        if (isError) {
            messageDiv.classList.add('error-message');
        }
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        const paragraph = document.createElement('p');
        
        // AI yanıtları HTML render et, user mesajları plain text
        if (role === 'ai') {
            // HTML content'i render et
            paragraph.innerHTML = content;
        } else {
            // User mesajları plain text olarak göster (güvenlik için)
            paragraph.textContent = content;
        }
        
        contentDiv.appendChild(paragraph);
        messageDiv.appendChild(contentDiv);
        
        // Add copy button for AI messages
        if (role === 'ai') {
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'message-actions';
            
            const copyButton = document.createElement('button');
            copyButton.className = 'btn btn-sm btn-ghost-secondary';
            copyButton.innerHTML = '<i class="fa-thin fa-copy"></i>';
            copyButton.onclick = () => copyMessage(copyButton);
            
            actionsDiv.appendChild(copyButton);
            messageDiv.appendChild(actionsDiv);
        }
        
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        
        return messageDiv;
    }
    
    function addTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message typing-message';
        typingDiv.innerHTML = `
            <div class="message-content">
                <div class="typing-indicator">
                    <span>AI yazıyor</span>
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(typingDiv);
        scrollToBottom();
        
        return typingDiv;
    }
    
    function showLoading(show) {
        loadingIndicator.classList.toggle('d-none', !show);
        sendButton.disabled = show;
        userMessage.disabled = show;
    }
    
    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function updateCharCounter() {
        const length = userMessage.value.length;
        charCounter.textContent = length;
        charCounter.parentElement.classList.toggle('text-warning', length > 1800);
        charCounter.parentElement.classList.toggle('text-danger', length >= 2000);
    }
    
    function generateId() {
        return 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Global functions for UI actions
    window.copyMessage = function(button) {
        const messageContent = button.closest('.message').querySelector('.message-content p').textContent;
        
        navigator.clipboard.writeText(messageContent).then(() => {
            showToast('Kopyalandı', 'Mesaj panoya kopyalandı', 'success');
        }).catch(() => {
            showToast('Hata', 'Kopyalama başarısız', 'error');
        });
    };
    
    window.copyAllMessages = function() {
        const messages = Array.from(document.querySelectorAll('.message')).map(msg => {
            const isUser = msg.classList.contains('user-message');
            const content = msg.querySelector('.message-content p').textContent;
            return `${isUser ? 'Siz' : 'AI'}: ${content}`;
        }).join('\n\n');
        
        navigator.clipboard.writeText(messages).then(() => {
            showToast('Kopyalandı', 'Tüm sohbet panoya kopyalandı', 'success');
        }).catch(() => {
            showToast('Hata', 'Kopyalama başarısız', 'error');
        });
    };
    
    window.clearChat = function() {
        if (confirm('Tüm sohbet geçmişi silinecek. Emin misiniz?')) {
            // Remove all messages except welcome
            const messages = chatMessages.querySelectorAll('.message:not(#welcome-message)');
            messages.forEach(msg => msg.remove());
            
            // Reset conversation
            currentConversationId = generateId();
            
            showToast('Temizlendi', 'Sohbet geçmişi temizlendi', 'success');
        }
    };
    
    function showToast(title, message, type = 'info') {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <strong class="me-2">${title}</strong>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="small">${message}</div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 4000);
    }
    
    // Focus on message input
    userMessage.focus();
    
    console.log('🤖 AI Chat Panel loaded successfully');
});
</script>
@endpush