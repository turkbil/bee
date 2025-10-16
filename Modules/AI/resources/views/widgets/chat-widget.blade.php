{{--
🌐 AI Chat Widget V2 - Frontend Integration Component

Bu widget public web sitelerinde AI chat özelliği sağlar:
- Alpine.js ile reactive functionality
- Tailwind CSS ile modern styling
- Rate limiting aware
- Guest/User mode support
- ResponseTemplateEngine V2 integration
--}}

<div
    x-data="aiChatWidget({
        apiBaseUrl: '{{ config('app.url') }}/api/ai/v1',
        tenantId: '{{ tenant('id') ?? 'default' }}',
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
        csrfToken: '{{ csrf_token() }}',
        features: @js($publicFeatures ?? []),
        maxMessageLength: {{ $maxMessageLength ?? 500 }}
    })"
    x-show="showWidget"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-6 scale-95"
    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 transform translate-y-6 scale-95"
    class="fixed bottom-4 right-4 z-50 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700"
    style="max-height: 600px;">
    
    {{-- Widget Header --}}
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-t-2xl">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-sm">AI Asistan</h3>
                <p class="text-xs text-white/80" x-text="statusText">Hazır</p>
            </div>
        </div>
        <button
            @click="toggleWidget()"
            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Feature Selection (if multiple features available) --}}
    <div x-show="features.length > 1" class="p-3 border-b border-gray-200 dark:border-gray-700">
        <select
            x-model="selectedFeature"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Genel Chat</option>
            <template x-for="feature in features" :key="feature.slug">
                <option :value="feature.slug" x-text="feature.name"></option>
            </template>
        </select>
    </div>

    {{-- Messages Container --}}
    <div 
        x-ref="messagesContainer"
        class="h-80 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900">
        
        {{-- Welcome Message --}}
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                AI
            </div>
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl rounded-tl-sm p-3 shadow-sm">
                <p class="text-sm text-gray-900 dark:text-gray-100">
                    Merhaba! Size nasıl yardımcı olabilirim? 
                    <span x-show="!isAuthenticated" class="text-xs text-gray-500 block mt-1">
                        Misafir kullanıcı olarak saatte 10 mesaj gönderebilirsiniz.
                    </span>
                </p>
            </div>
        </div>

        {{-- Dynamic Messages --}}
        <template x-for="message in messages" :key="message.id">
            <div class="flex items-start space-x-3" :class="message.sender === 'user' ? 'flex-row-reverse space-x-reverse' : ''">
                <div 
                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                    :class="message.sender === 'user' ? 'bg-gray-500' : 'bg-gradient-to-r from-blue-500 to-purple-600'">
                    <span x-text="message.sender === 'user' ? 'Sen' : 'AI'"></span>
                </div>
                <div 
                    class="flex-1 rounded-2xl p-3 shadow-sm max-w-xs"
                    :class="message.sender === 'user' ? 'bg-blue-500 text-white rounded-tr-sm' : 'bg-white dark:bg-gray-800 rounded-tl-sm'">
                    <div 
                        class="text-sm whitespace-pre-wrap"
                        :class="message.sender === 'user' ? 'text-white' : 'text-gray-900 dark:text-gray-100'"
                        x-html="formatMessage(message.content)">
                    </div>
                    <div 
                        class="text-xs mt-1 opacity-70"
                        :class="message.sender === 'user' ? 'text-blue-100' : 'text-gray-500'"
                        x-text="formatTime(message.timestamp)">
                    </div>
                </div>
            </div>
        </template>

        {{-- Loading Message --}}
        <div x-show="isLoading" class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                AI
            </div>
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl rounded-tl-sm p-3 shadow-sm">
                <div class="flex items-center space-x-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    <span class="text-xs text-gray-500">AI düşünüyor...</span>
                </div>
            </div>
        </div>

        {{-- Error Message --}}
        <div x-show="errorMessage" class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm">
                ⚠️
            </div>
            <div class="flex-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl rounded-tl-sm p-3">
                <p class="text-sm text-red-800 dark:text-red-200" x-text="errorMessage"></p>
                <button 
                    @click="clearError()"
                    class="text-xs text-red-600 dark:text-red-400 mt-1 hover:underline">
                    Kapat
                </button>
            </div>
        </div>
    </div>

    {{-- Input Area --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        
        {{-- Rate Limit Warning --}}
        <div x-show="remainingRequests !== null && remainingRequests <= 2" 
             class="mb-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-xs text-yellow-800 dark:text-yellow-200">
                <span x-show="!isAuthenticated">
                    Kalan mesaj hakkınız: <span x-text="remainingRequests"></span>
                </span>
                <span x-show="isAuthenticated && creditsRemaining !== null">
                    Kalan krediniz: <span x-text="creditsRemaining"></span>
                </span>
            </p>
        </div>

        {{-- Message Input --}}
        <div class="flex items-end space-x-2">
            <div class="flex-1">
                <textarea
                    x-ref="messageInput"
                    x-model="currentMessage"
                    @keydown.enter.prevent="sendMessage()"
                    @keydown.shift.enter.prevent="currentMessage += '\n'"
                    @input="updateCharCount()"
                    :disabled="isLoading || rateLimited"
                    :placeholder="getInputPlaceholder()"
                    rows="1"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
                    style="min-height: 40px; max-height: 120px;"></textarea>
                
                {{-- Character Count --}}
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-gray-500">
                        <span x-text="currentMessage.length"></span>/<span x-text="maxMessageLength"></span>
                    </p>
                    <p class="text-xs text-gray-500" x-show="selectedFeature">
                        Feature: <span x-text="getFeatureName(selectedFeature)"></span>
                    </p>
                </div>
            </div>
            
            <button
                @click="sendMessage()"
                :disabled="!canSendMessage()"
                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Widget Toggle Button (when minimized) --}}
<button
    x-show="!showWidget"
    @click="toggleWidget()"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-6 scale-95"
    x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 transform translate-y-6 scale-95"
    class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full shadow-2xl hover:shadow-3xl hover:scale-105 transition-all flex items-center justify-center">
    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
    </svg>
    <div x-show="unreadCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">
        <span x-text="unreadCount"></span>
    </div>
</button>

<script>
/**
 * 🌐 AI Chat Widget V2 - Alpine.js Component
 * 
 * Bu component ResponseTemplateEngine V2 ile entegre çalışır
 * ve monoton yanıtları engeller.
 */
function aiChatWidget(config) {
    return {
        // Configuration
        apiBaseUrl: config.apiBaseUrl || '/api/ai/v1',
        tenantId: config.tenantId || 'default',
        isAuthenticated: config.isAuthenticated || false,
        csrfToken: config.csrfToken || '',
        features: config.features || [],
        maxMessageLength: config.maxMessageLength || 500,
        
        // State
        showWidget: false,
        isLoading: false,
        rateLimited: false,
        currentMessage: '',
        selectedFeature: '',
        messages: [],
        errorMessage: '',
        remainingRequests: null,
        creditsRemaining: null,
        unreadCount: 0,
        sessionId: null, // ✅ For conversation continuity
        
        // Computed
        get statusText() {
            if (this.isLoading) return 'Yanıtlıyor...';
            if (this.rateLimited) return 'Limit aşıldı';
            if (this.remainingRequests !== null && this.remainingRequests <= 0) return 'Limit doldu';
            return 'Hazır';
        },
        
        // Lifecycle
        init() {
            this.loadStoredMessages();
            if (this.isAuthenticated) {
                this.loadCreditBalance();
            }
            
            // Auto-resize textarea
            this.$nextTick(() => {
                if (this.$refs.messageInput) {
                    this.$refs.messageInput.style.height = 'auto';
                    this.$refs.messageInput.addEventListener('input', this.autoResizeTextarea.bind(this));
                }
            });
        },
        
        // Widget Management
        toggleWidget() {
            this.showWidget = !this.showWidget;
            
            if (this.showWidget) {
                this.unreadCount = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.focus();
                    }
                });
            }
        },
        
        // Message Management
        async sendMessage() {
            if (!this.canSendMessage()) return;
            
            const message = this.currentMessage.trim();
            const feature = this.selectedFeature;
            
            // Add user message
            this.addMessage('user', message);
            this.currentMessage = '';
            this.clearError();
            
            // Show loading
            this.isLoading = true;
            this.scrollToBottom();
            
            try {
                // ✅ FIX: Use shop-assistant/chat for Smart Product Search integration
                const endpoint = '/shop-assistant/chat';
                const payload = {
                    message: message,
                    session_id: this.sessionId || `session-${Date.now()}`,
                    feature: feature || null,
                    context: {
                        widget_version: '2.0',
                        timestamp: Date.now()
                    }
                };

                // Store session ID for conversation continuity
                if (!this.sessionId) {
                    this.sessionId = payload.session_id;
                }
                
                const response = await fetch(`${this.apiBaseUrl}${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Add AI response (with V2 formatting)
                    this.addMessage('ai', data.data.message || data.data.response);
                    
                    // Update counters
                    if (data.data.remaining_requests !== undefined) {
                        this.remainingRequests = data.data.remaining_requests;
                    }
                    if (data.data.credits_remaining !== undefined) {
                        this.creditsRemaining = data.data.credits_remaining;
                    }
                    
                    // Check rate limiting
                    if (this.remainingRequests !== null && this.remainingRequests <= 0) {
                        this.rateLimited = true;
                    }
                    
                } else {
                    this.handleError(data.error || 'Bir hata oluştu');
                    
                    // Handle specific errors
                    if (response.status === 429) {
                        this.rateLimited = true;
                        this.remainingRequests = 0;
                    }
                }
                
            } catch (error) {
                console.error('AI Chat Widget Error:', error);
                this.handleError('Bağlantı hatası oluştu');
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },
        
        addMessage(sender, content) {
            const message = {
                id: Date.now() + Math.random(),
                sender: sender,
                content: content,
                timestamp: Date.now()
            };
            
            this.messages.push(message);
            this.saveStoredMessages();
            
            // Update unread count if widget is closed
            if (!this.showWidget && sender === 'ai') {
                this.unreadCount++;
            }
        },
        
        // Utility Methods
        canSendMessage() {
            return !this.isLoading && 
                   !this.rateLimited && 
                   this.currentMessage.trim().length > 0 && 
                   this.currentMessage.length <= this.maxMessageLength;
        },
        
        getInputPlaceholder() {
            if (this.rateLimited) return 'Rate limit aşıldı, lütfen bekleyiniz...';
            if (this.isLoading) return 'AI yanıtlıyor...';
            return 'Mesajınızı yazın... (Enter: gönder, Shift+Enter: yeni satır)';
        },
        
        getFeatureName(slug) {
            const feature = this.features.find(f => f.slug === slug);
            return feature ? feature.name : slug;
        },
        
        formatMessage(content) {
            // V2 ResponseTemplateEngine formatlarını destekle
            if (!content) return '';
            
            // Basic markdown-like formatting
            return content
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
                .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic
                .replace(/```([\s\S]*?)```/g, '<pre class="bg-gray-100 dark:bg-gray-800 p-2 rounded text-sm overflow-x-auto"><code>$1</code></pre>') // Code blocks
                .replace(/`(.*?)`/g, '<code class="bg-gray-100 dark:bg-gray-800 px-1 rounded text-sm">$1</code>') // Inline code
                .replace(/\n/g, '<br>'); // Line breaks
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('tr-TR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        updateCharCount() {
            // Auto-resize textarea
            this.autoResizeTextarea();
        },
        
        autoResizeTextarea() {
            if (this.$refs.messageInput) {
                this.$refs.messageInput.style.height = 'auto';
                this.$refs.messageInput.style.height = Math.min(this.$refs.messageInput.scrollHeight, 120) + 'px';
            }
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        },
        
        // Error Handling
        handleError(message) {
            this.errorMessage = message;
            this.addMessage('ai', `Üzgünüm, bir hata oluştu: ${message}`);
        },
        
        clearError() {
            this.errorMessage = '';
        },
        
        // Storage Management
        loadStoredMessages() {
            try {
                const stored = localStorage.getItem(`ai_chat_messages_${this.tenantId}`);
                if (stored) {
                    const messages = JSON.parse(stored);
                    // Keep only recent messages (last 24 hours)
                    const dayAgo = Date.now() - (24 * 60 * 60 * 1000);
                    this.messages = messages.filter(m => m.timestamp > dayAgo);
                }
            } catch (error) {
                console.warn('Failed to load stored messages:', error);
            }
        },
        
        saveStoredMessages() {
            try {
                // Keep only last 50 messages
                const messagesToStore = this.messages.slice(-50);
                localStorage.setItem(`ai_chat_messages_${this.tenantId}`, JSON.stringify(messagesToStore));
            } catch (error) {
                console.warn('Failed to save messages:', error);
            }
        },
        
        // Credit Management
        async loadCreditBalance() {
            if (!this.isAuthenticated) return;
            
            try {
                const response = await fetch(`${this.apiBaseUrl}/credits/balance`, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.creditsRemaining = data.data.credits_available;
                }
            } catch (error) {
                console.warn('Failed to load credit balance:', error);
            }
        }
    }
}
</script>