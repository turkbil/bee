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
        assistantName: '{{ \App\Helpers\AISettingsHelper::getAssistantName() }}',

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

                // Placeholder will automatically show when messages are empty
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

/**
 * Placeholder V4 - Slide Up Animation (Dynamic Product-specific)
 * Loads AI-generated placeholder conversations from API or uses fallback
 */
window.placeholderV4 = function(productId = null) {
    return {
        conversation: [],
        isLoading: true,
        loadError: false,

        async init() {
            console.log('🔍 Placeholder init started', { productId });

            if (productId) {
                // Start with fallback, then try to load from cache
                this.conversation = this.getFallbackConversation();
                this.isLoading = false;

                console.log('📡 Checking cache for product placeholder...');
                // Try to load - if cached, it will be fast. If not, generate in background
                this.loadProductPlaceholder(productId); // Don't await - run in background
            } else {
                console.log('⚠️ No productId provided, using fallback');
                this.conversation = this.getFallbackConversation();
                this.isLoading = false;
            }

            console.log('✅ Placeholder init completed (immediate)', {
                conversationLength: this.conversation.length
            });
        },

        async loadProductPlaceholder(productId) {
            try {
                const startTime = Date.now();
                const response = await fetch(`/api/ai/v1/product-placeholder/${productId}`);
                const data = await response.json();
                const loadTime = Date.now() - startTime;

                if (data.success && data.data.conversation) {
                    console.log('✅ Product placeholder loaded', {
                        from_cache: data.data.from_cache,
                        generated_at: data.data.generated_at,
                        load_time_ms: loadTime,
                        note: data.data.from_cache ? 'Cached - will use next visit' : 'Generated - cached for next visit'
                    });
                    // Don't replace conversation on first visit - it's already showing fallback
                    // Next visit will load from cache immediately
                } else {
                    throw new Error('Invalid response');
                }
            } catch (error) {
                console.error('❌ Failed to load product placeholder (will retry next time):', error);
                this.loadError = true;
            }
        },

        getFallbackConversation() {
            return [
                { role: 'user', text: "Merhaba, bu ürün ne iş yapıyor?" },
                { role: 'assistant', text: "Merhaba! Bu ürün profesyonel kullanım için tasarlanmış, yüksek performanslı bir ekipmandır. İhtiyaçlarınıza göre farklı modellerde sunulmaktadır." },
                { role: 'user', text: "Hangi kapasitede var?" },
                { role: 'assistant', text: "Farklı kapasite seçeneklerimiz mevcut. Size en uygun modeli belirlemek için kullanım amacınızı ve ihtiyacınızı konuşalım!" },
                { role: 'user', text: "Neden bu modeli tercih etmeliyim?" },
                { role: 'assistant', text: "Bu model dayanıklılığı, yüksek performansı ve kolay kullanımıyla öne çıkıyor. Detaylı teknik özellikleri ve avantajları için benimle konuşmaya başlayın!" }
            ];
        },

        async start() {
            const container = this.$el;
            container.innerHTML = '';

            console.log('🎬 Placeholder animation started', {
                conversationLength: this.conversation.length,
                conversation: this.conversation
            });

            await this.sleep(1000);

            // Loop through all conversation messages
            for (let msg of this.conversation) {
                // Show typing indicator before assistant messages
                if (msg.role === 'assistant') {
                    await this.showTypingIndicator(container);
                    await this.sleep(1500); // Wait while "typing"
                    await this.hideTypingIndicator(container);
                }

                await this.slideUpMessage(msg.text, msg.role, container);
                await this.sleep(1800); // Longer pause between messages
            }

            // Add final "Start chatting" message
            await this.sleep(1000);
            await this.showStartMessage(container);
        },

        async showTypingIndicator(container) {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex justify-start mb-3 typing-indicator-placeholder';
            typingDiv.innerHTML = `
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 shadow-sm opacity-0 translate-y-4 transition-all duration-500">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            `;

            container.appendChild(typingDiv);
            await this.sleep(50);

            const bubble = typingDiv.querySelector('div');
            bubble.classList.remove('opacity-0', 'translate-y-4');
            bubble.classList.add('opacity-100', 'translate-y-0');

            // Smooth scroll to bottom
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        },

        async hideTypingIndicator(container) {
            const typingDiv = container.querySelector('.typing-indicator-placeholder');
            if (typingDiv) {
                typingDiv.remove();
            }
        },

        async slideUpMessage(text, role, container) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-3`;

            const bubble = document.createElement('div');
            bubble.className = `max-w-[85%] rounded-2xl px-4 py-2.5 translate-y-4 transition-all duration-500 ${
                role === 'user'
                    ? 'bg-blue-400 dark:bg-blue-500'
                    : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700'
            }`;

            // Add text with opacity
            const textSpan = document.createElement('span');
            textSpan.className = role === 'user'
                ? 'text-white opacity-50'
                : 'text-gray-800 dark:text-white opacity-50';
            textSpan.textContent = text;
            bubble.appendChild(textSpan);

            msgDiv.appendChild(bubble);
            container.appendChild(msgDiv);

            // Scroll immediately after adding
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });

            await this.sleep(50);
            bubble.classList.remove('translate-y-4');
            bubble.classList.add('translate-y-0');

            // Scroll again after animation starts
            await this.sleep(100);
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        },

        async showStartMessage(container) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'flex justify-center mb-3 mt-6';

            const bubble = document.createElement('div');
            bubble.className = 'px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full shadow-lg opacity-0 scale-90 transition-all duration-500';
            bubble.innerHTML = '💬 <strong>Yazışmak için mesajınızı yazın!</strong>';

            msgDiv.appendChild(bubble);
            container.appendChild(msgDiv);

            await this.sleep(50);
            bubble.classList.remove('opacity-0', 'scale-90');
            bubble.classList.add('opacity-100', 'scale-100');

            // Ensure final message is visible - smooth scroll to bottom
            await this.sleep(200); // Wait for animation
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });

            // Force scroll again after a delay to ensure visibility
            await this.sleep(500);
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        },

        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    };
};

/**
 * Markdown Renderer for AI Chat Messages
 * Converts markdown syntax to HTML for chat display
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    // Sanitize HTML tags first
    let html = content
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    // Headers (h1, h2, h3)
    html = html.replace(/^### (.*$)/gim, '<h3 class="text-lg font-bold mt-3 mb-2">$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2 class="text-xl font-bold mt-4 mb-2">$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1 class="text-2xl font-bold mt-4 mb-3">$1</h1>');

    // Bold **text**
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold">$1</strong>');

    // Italic *text*
    html = html.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');

    // Links [text](url)
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="underline hover:opacity-80 transition-opacity">$1</a>');

    // Unordered lists (- item)
    html = html.replace(/^\s*[-*]\s+(.+)$/gim, '<li class="ml-4">• $1</li>');
    html = html.replace(/(<li class="ml-4">.*<\/li>)/s, '<ul class="space-y-1 my-2">$1</ul>');

    // Line breaks
    html = html.replace(/\n\n/g, '</p><p class="mt-2">');
    html = html.replace(/\n/g, '<br>');

    // Wrap in paragraph
    html = '<p>' + html + '</p>';

    // Clean up multiple <p> tags
    html = html.replace(/<p><\/p>/g, '');

    return html;
};

console.log('✅ AI Chat Markdown Renderer loaded');
</script>
