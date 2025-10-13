{{--
    Alpine.js AI Chat Store Component v2.0.1

    Global state management for AI chatbot widgets.
    Bu component sayfaya bir kez dahil edilmeli ve tüm widget'lar bu store'u kullanmalı.

    Kullanım:
    <x-ai.chat-store />

    Widget'lardan erişim:
    <div x-data="{ chat: $store.aiChat }">
        <button @click="chat.toggleFloating()">Toggle Chat</button>
    </div>

    Changelog v2.0.1: Fixed $nextTick to setTimeout for Alpine store compatibility
--}}

<script>
// AI Chat Store v2.0.1 - Alpine.js Global State
document.addEventListener('alpine:init', () => {
    Alpine.store('aiChat', {
        // State
        sessionId: null,
        conversationId: null,
        messages: [],
        isLoading: false,
        isTyping: false,
        error: null,

        // Widget states
        floatingVisible: false,
        floatingOpen: false,
        inlineStates: {}, // { widgetId: { open: bool, visible: bool } }

        // Config
        apiEndpoint: '{{ route('api.ai.api.v1.shop-assistant.chat') }}',
        historyEndpoint: '{{ route('api.ai.api.v1.shop-assistant.history') }}',
        assistantName: 'iXtif Yapay Zeka Sistemi',

        // Context data (product_id, category_id, page_slug)
        context: {
            product_id: null,
            category_id: null,
            page_slug: null,
        },

        // Initialize
        init() {
            // Load session ID from localStorage
            this.sessionId = localStorage.getItem('ai_chat_session_id');

            // Load floating widget state from localStorage
            const savedFloatingState = localStorage.getItem('ai_chat_floating_open');
            if (savedFloatingState !== null) {
                this.floatingOpen = savedFloatingState === 'true';
            }

            // Load conversation history if session exists
            if (this.sessionId) {
                this.loadHistory();
            }

            // Listen for context changes (from widgets)
            window.addEventListener('ai-chat-context-change', (e) => {
                this.updateContext(e.detail);
            });

            // Scroll to bottom on init (after DOM is ready)
            setTimeout(() => {
                this.scrollToBottom();
            }, 500);

            console.log('🤖 AI Chat Store initialized', {
                floatingOpen: this.floatingOpen,
                sessionId: this.sessionId
            });
        },

        // Update context (product_id, category_id, page_slug)
        updateContext(newContext) {
            this.context = { ...this.context, ...newContext };
            console.log('🔄 Context updated:', this.context);
        },

        // Toggle floating widget
        toggleFloating() {
            this.floatingOpen = !this.floatingOpen;

            // Save state to localStorage
            localStorage.setItem('ai_chat_floating_open', this.floatingOpen.toString());

            if (this.floatingOpen && this.messages.length === 0) {
                // İlk açılışta hoş geldin mesajı
                this.addSystemMessage(`👋 **${this.assistantName}** iXtif.com için özel olarak geliştirilmiştir.\n\nÜrünlerimiz, hizmetlerimiz ve şirketimiz hakkında tüm sorularınızı yanıtlamak için buradayım. Size nasıl yardımcı olabilirim?`);
            }

            // Scroll to bottom when opening
            if (this.floatingOpen) {
                setTimeout(() => {
                    this.scrollToBottom();
                }, 200);
            }
        },

        // Open floating widget
        openFloating() {
            this.floatingOpen = true;

            // Save state to localStorage
            localStorage.setItem('ai_chat_floating_open', 'true');

            if (this.messages.length === 0) {
                this.addSystemMessage(`👋 **${this.assistantName}** iXtif.com için özel olarak geliştirilmiştir.\n\nÜrünlerimiz, hizmetlerimiz ve şirketimiz hakkında tüm sorularınızı yanıtlamak için buradayım. Size nasıl yardımcı olabilirim?`);
            }

            // Scroll to bottom when opening
            setTimeout(() => {
                this.scrollToBottom();
            }, 200);
        },

        // Close floating widget
        closeFloating() {
            this.floatingOpen = false;

            // Save state to localStorage
            localStorage.setItem('ai_chat_floating_open', 'false');
        },

        // Register inline widget
        registerInline(widgetId) {
            if (!this.inlineStates[widgetId]) {
                this.inlineStates[widgetId] = {
                    open: false,
                    visible: true,
                };
            }
        },

        // Toggle inline widget
        toggleInline(widgetId) {
            if (this.inlineStates[widgetId]) {
                this.inlineStates[widgetId].open = !this.inlineStates[widgetId].open;
            }
        },

        // Send message to AI
        async sendMessage(messageText, contextOverride = {}) {
            if (!messageText || !messageText.trim()) {
                return;
            }

            // Add user message to UI immediately
            this.addMessage({
                role: 'user',
                content: messageText.trim(),
                created_at: new Date().toISOString(),
            });

            // Set loading state
            this.isLoading = true;
            this.isTyping = true;
            this.error = null;

            try {
                // Merge context
                const finalContext = { ...this.context, ...contextOverride };

                // API request
                const response = await fetch(this.apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        message: messageText.trim(),
                        session_id: this.sessionId,
                        product_id: finalContext.product_id,
                        category_id: finalContext.category_id,
                        page_slug: finalContext.page_slug,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Bir hata oluştu');
                }

                // Update session ID
                if (data.data.session_id) {
                    this.sessionId = data.data.session_id;
                    localStorage.setItem('ai_chat_session_id', this.sessionId);
                }

                // Update conversation ID
                if (data.data.conversation_id) {
                    this.conversationId = data.data.conversation_id;
                }

                // Update assistant name
                if (data.data.assistant_name) {
                    this.assistantName = data.data.assistant_name;
                }

                // Add AI response
                this.addMessage({
                    role: 'assistant',
                    content: data.data.message,
                    created_at: new Date().toISOString(),
                });

                console.log('✅ Message sent successfully', {
                    tokens: data.data.tokens_used,
                    modules: data.data.context_used?.modules,
                });

            } catch (error) {
                console.error('❌ Failed to send message:', error);
                this.error = error.message || 'Mesaj gönderilemedi';

                // Add error message to chat
                this.addMessage({
                    role: 'system',
                    content: `⚠️ Hata: ${this.error}`,
                    created_at: new Date().toISOString(),
                    isError: true,
                });

            } finally {
                this.isLoading = false;
                this.isTyping = false;
            }
        },

        // Load conversation history
        async loadHistory() {
            if (!this.sessionId) {
                return;
            }

            try {
                const url = new URL(this.historyEndpoint);
                url.searchParams.append('session_id', this.sessionId);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.messages = data.data.messages || [];
                    this.conversationId = data.data.conversation_id;

                    console.log('📜 History loaded:', this.messages.length, 'messages');

                    // Scroll to bottom after history is loaded
                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 300);
                }

            } catch (error) {
                console.error('❌ Failed to load history:', error);
            }
        },

        // Add message to state
        addMessage(message) {
            this.messages.push(message);

            // Auto-scroll to bottom (next tick)
            setTimeout(() => {
                this.scrollToBottom();
            }, 100);
        },

        // Add system message
        addSystemMessage(content) {
            this.addMessage({
                role: 'system',
                content: content,
                created_at: new Date().toISOString(),
            });
        },

        // Clear conversation
        clearConversation() {
            if (confirm('Konuşma geçmişini silmek istediğinizden emin misiniz?')) {
                this.messages = [];
                this.conversationId = null;
                this.sessionId = null;
                localStorage.removeItem('ai_chat_session_id');

                this.addSystemMessage(`Konuşma geçmişi silindi. Yeni bir konuşmaya başlayabilirsiniz.`);
            }
        },

        // Scroll to bottom of chat
        scrollToBottom() {
            const chatContainers = document.querySelectorAll('[data-ai-chat-messages]');
            chatContainers.forEach(container => {
                container.scrollTop = container.scrollHeight;
            });
        },

        // Get messages count
        get messageCount() {
            return this.messages.length;
        },

        // Get last message
        get lastMessage() {
            return this.messages[this.messages.length - 1] || null;
        },

        // Has conversation
        get hasConversation() {
            return this.messages.length > 0;
        },
    });
});
</script>
