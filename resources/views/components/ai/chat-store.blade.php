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

        init() {
            console.log('🔍 Placeholder init started', { productId });

            // ⚡ PERFORMANCE FIX: Immediately show fallback, then load real data in background
            // This prevents page rendering from blocking on API calls

            // Step 1: Show fallback immediately (0ms wait for user)
            this.conversation = this.getFallbackConversation();
            this.isLoading = false;

            console.log('⚡ Fallback shown immediately (non-blocking)');

            // Step 2: Load real placeholder in background (after page is interactive)
            if (productId) {
                // Use requestIdleCallback for better performance
                // Falls back to setTimeout if not supported
                const deferredLoad = () => {
                    console.log('🔄 Background: Loading real placeholder from API...');
                    this.loadProductPlaceholder(productId);
                };

                if ('requestIdleCallback' in window) {
                    // Load during browser idle time (best performance)
                    requestIdleCallback(deferredLoad, { timeout: 2000 });
                } else {
                    // Fallback: Load after 100ms (still non-blocking)
                    setTimeout(deferredLoad, 100);
                }
            } else {
                console.log('⚠️ No productId provided, using fallback only');
            }

            console.log('✅ Placeholder init completed (non-blocking)', {
                conversationLength: this.conversation.length,
            });
        },

        async loadProductPlaceholder(productId) {
            try {
                const startTime = Date.now();
                const response = await fetch(`/api/ai/v1/product-placeholder/${productId}`);
                const data = await response.json();
                const loadTime = Date.now() - startTime;

                if (data.success && data.data.conversation) {
                    console.log('✅ Product placeholder loaded from API', {
                        from_cache: data.data.from_cache,
                        generated_at: data.data.generated_at,
                        load_time_ms: loadTime,
                        conversation_items: data.data.conversation.length
                    });

                    // 🎯 SMOOTH UPDATE: Only update if conversation is different
                    const isSame = JSON.stringify(this.conversation) === JSON.stringify(data.data.conversation);

                    if (!isSame) {
                        console.log('🔄 Updating conversation with real data');
                        this.conversation = data.data.conversation;

                        // Optionally restart animation with new data
                        // You can uncomment this if you want to show animation again
                        // this.start();
                    } else {
                        console.log('✓ Conversation already up-to-date (using fallback)');
                    }

                    this.loadError = false;
                } else {
                    throw new Error('Invalid response');
                }
            } catch (error) {
                console.error('❌ Failed to load product placeholder:', error);
                this.loadError = true;
                // Keep using fallback conversation (already set in init)
            }
        },

        getFallbackConversation() {
            // ✅ FIX: Use tenant-specific assistant name from Alpine store
            // assistantName is already loaded from backend at line 38
            const assistantName = '{{ \App\Helpers\AISettingsHelper::getAssistantName() }}' || 'Asistan';

            return [
                { role: 'user', text: "Bu ürün ne işe yarar?" },
                { role: 'assistant', text: `Merhaba! Ben ${assistantName}, size bu ürün hakkında detaylı bilgi verebilirim. Sorularınızı bekliyorum!` },
                { role: 'user', text: "Hangi özellikleri var?" },
                { role: 'assistant', text: "Ürünün teknik özellikleri, kullanım alanları ve avantajları hakkında size yardımcı olabilirim. Merak ettiklerinizi sorun!" },
                { role: 'user', text: "Nasıl yardımcı olabilirsiniz?" },
                { role: 'assistant', text: "Ürün detayları, karşılaştırmalar ve size en uygun çözümü bulmak için buradan mesaj atabilirsiniz!" }
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
 * Markdown to HTML Converter & Enhancer for AI Chat Messages v4.0
 * AI artık Markdown formatında yanıt veriyor - burada HTML'e çevirip style ekliyoruz
 * Markdown format: paragraflar (boş satır), liste (- item), bold (**text**)
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    let html = content;

    // 🚀 MARKDOWN → HTML CONVERSION (AI artık Markdown kullanıyor!)

    // 1. Liste dönüşümü: "- item" → <ul><li>item</li></ul>
    // IMPROVED: Hem yeni satırlardaki hem aynı satırdaki tireleri algıla

    // 1A. Önce aynı satırdaki yan yana tireleri yeni satırlara böl
    // "Ürünler: - A - B - C" → "Ürünler:\n- A\n- B\n- C"
    html = html.replace(/([.:])\s*-\s+([^-]+?)(?=\s+-\s+|\s*$)/g, function(match, punctuation, text) {
        // İlk tire: paragraftan sonra yeni satıra
        return punctuation + '\n- ' + text.trim();
    });

    // 1B. Kalan yan yana tireleri de ayır: " - item" → "\n- item"
    html = html.replace(/\s+-\s+([^-\n]+?)(?=\s+-|\n|$)/g, '\n- $1');

    // 1C. Ardışık "- " satırlarını tespit et ve <ul> ile wrap et
    html = html.replace(/((?:^|\n)- .+(?:\n- .+)*)/gm, function(match) {
        // Her "- item" satırını <li>item</li>'ye çevir
        let items = match.split('\n').filter(line => line.trim().startsWith('- '));
        let listItems = items.map(line => {
            let text = line.replace(/^-\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ul>\n${listItems}\n</ul>`;
    });

    // 2. Paragraf dönüşümü: Boş satırlarla ayrılmış metinleri <p> ile wrap et
    // Önce mevcut <ul>, <ol>, link taglarını koru (placeholder kullan)
    let preservedBlocks = [];
    html = html.replace(/(<ul>[\s\S]*?<\/ul>|<ol>[\s\S]*?<\/ol>)/g, function(match, block) {
        let placeholder = `___PRESERVED_BLOCK_${preservedBlocks.length}___`;
        preservedBlocks.push(block);
        return placeholder;
    });

    // Şimdi paragrafları <p> ile wrap et
    // Çift newline (\n\n) ile ayrılmış blokları paragraf olarak kabul et
    html = html.split('\n\n').map(block => {
        block = block.trim();
        if (!block) return '';
        // Eğer placeholder veya HTML tag içeriyorsa dokunma
        if (block.includes('___PRESERVED_BLOCK_') || block.match(/^<[a-z]/i)) {
            return block;
        }
        // Tek satırları <p> ile wrap et
        return `<p>${block.replace(/\n/g, '<br>')}</p>`;
    }).join('\n');

    // Preserved block'ları geri koy
    preservedBlocks.forEach((block, index) => {
        html = html.replace(`___PRESERVED_BLOCK_${index}___`, block);
    });

    // 🔗 LINK PROCESSING (custom format → styled HTML)

    // 0A. NEW FORMAT: [LINK:shop:SLUG] → /shop/slug (PREMIUM ŞIK TASARIM!)
    // Format: **Ürün Adı** [LINK:shop:litef-ept15] → /shop/litef-ept15
    // ULTRA ŞIK: Gradient, hover effects, premium icons
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/${slug}`;

        // Minimal shopping icon
        const shopIcon = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>`;

        // Minimal arrow icon
        const arrowIcon = `<svg class="w-4 h-4 flex-shrink-0 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>`;

        return `<a href="${url}" target="_blank" rel="noopener noreferrer"
            class="group inline-flex items-center gap-2 px-3 py-2 my-1
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   hover:border-blue-500 dark:hover:border-blue-500
                   rounded-lg transition-all duration-200
                   text-sm font-medium no-underline
                   text-gray-900 dark:text-gray-100">
            ${shopIcon}
            <span class="no-underline">${linkText.trim()}</span>
            ${arrowIcon}
        </a>`;
    });

    // 0B. Category SLUG format: [LINK:shop:category:SLUG]
    // IMPORTANT: ONLY processes **bold** text before [LINK:...] to prevent entire sentence being linked
    // Supports uppercase, Turkish chars
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:category:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/category/${slug}`;

        // Minimal category icon
        const icon = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>`;

        // Minimal arrow icon
        const arrowIcon = `<svg class="w-4 h-4 flex-shrink-0 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>`;

        return `<a href="${url}" target="_blank" rel="noopener noreferrer"
            class="group inline-flex items-center gap-2 px-3 py-2 my-1
                   bg-white dark:bg-gray-800
                   border border-green-200 dark:border-green-700
                   hover:border-green-500 dark:hover:border-green-500
                   rounded-lg transition-all duration-200
                   text-sm font-medium no-underline
                   text-gray-900 dark:text-gray-100">
            ${icon}
            <span class="no-underline">${linkText.trim()}</span>
            ${arrowIcon}
        </a>`;
    });

    // 0C. BACKWARD COMPATIBILITY: [LINK:shop:TYPE:ID] → /shop/TYPE/ID (OLD ID-BASED FORMAT)
    // Format: **Ürün Adı** [LINK:shop:product:296] → /shop/product/296
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:(\w+):(\w+):(\d+)\]/gi, function(match, linkText, module, type, id) {
        // Universal link format: [LINK:module:type:id]
        let url, icon, colorClass;

        // Shop module
        if (module === 'shop') {
            if (type === 'product') {
                url = `/shop/product/${id}`;
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>`;
                colorClass = 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50';
            } else if (type === 'category') {
                url = `/shop/category-by-id/${id}`;
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>`;
                colorClass = 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50';
            } else if (type === 'brand') {
                url = `/shop/brand-by-id/${id}`;
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>`;
                colorClass = 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/50';
            }
        }
        // Blog module
        else if (module === 'blog') {
            url = `/blog/post-by-id/${id}`;
            icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>`;
            colorClass = 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/50';
        }
        // Page module
        else if (module === 'page') {
            url = `/page-by-id/${id}`;
            icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
            colorClass = 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50';
        }
        // Portfolio module
        else if (module === 'portfolio') {
            url = `/portfolio/project-by-id/${id}`;
            icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
            colorClass = 'bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/50';
        }
        // Fallback
        else {
            url = '#';
            icon = '';
            colorClass = 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
        }

        // Minimal arrow icon
        const arrowIcon = `<svg class="w-4 h-4 flex-shrink-0 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>`;

        // Determine border color based on module type for minimal differentiation
        let borderColor = 'border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500';
        if (module === 'shop' && type === 'category') {
            borderColor = 'border-green-200 dark:border-green-700 hover:border-green-500 dark:hover:border-green-500';
        } else if (module === 'shop' && type === 'brand') {
            borderColor = 'border-purple-200 dark:border-purple-700 hover:border-purple-500 dark:hover:border-purple-500';
        } else if (module === 'blog') {
            borderColor = 'border-orange-200 dark:border-orange-700 hover:border-orange-500 dark:hover:border-orange-500';
        } else if (module === 'page') {
            borderColor = 'border-indigo-200 dark:border-indigo-700 hover:border-indigo-500 dark:hover:border-indigo-500';
        } else if (module === 'portfolio') {
            borderColor = 'border-pink-200 dark:border-pink-700 hover:border-pink-500 dark:hover:border-pink-500';
        }

        return `<a href="${url}" target="_blank" rel="noopener noreferrer"
            class="group inline-flex items-center gap-2 px-3 py-2 my-1
                   bg-white dark:bg-gray-800
                   border ${borderColor}
                   rounded-lg transition-all duration-200
                   text-sm font-medium no-underline
                   text-gray-900 dark:text-gray-100">
            ${icon}
            <span class="no-underline">${linkText.trim()}</span>
            ${arrowIcon}
        </a>`;
    });

    // BACKWARD COMPATIBILITY: Eski [LINK_ID] formatı
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK_ID:(\d+)(?::([a-z0-9-]+))?\]/gi, function(match, productName, productId, productSlug) {
        const productUrl = `/shop/product/${productId}`;

        // Minimal shopping icon
        const shopIcon = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>`;

        // Minimal arrow icon
        const arrowIcon = `<svg class="w-4 h-4 flex-shrink-0 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>`;

        return `<a href="${productUrl}" target="_blank" rel="noopener noreferrer"
            class="group inline-flex items-center gap-2 px-3 py-2 my-1
                   bg-white dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700
                   hover:border-blue-500 dark:hover:border-blue-500
                   rounded-lg transition-all duration-200
                   text-sm font-medium no-underline
                   text-gray-900 dark:text-gray-100">
            ${shopIcon}
            <span class="no-underline">${productName.trim()}</span>
            ${arrowIcon}
        </a>`;
    });

    // 🚨 CRITICAL: Preserve links before bold processing to prevent link splitting
    // This prevents **text** [LINK] from being split into separate elements
    let preservedLinks = [];
    html = html.replace(/(<a[\s\S]*?<\/a>)/g, function(match, link) {
        let linkPlaceholder = `___LINK_PRESERVED_${preservedLinks.length}___`;
        preservedLinks.push(link);
        return linkPlaceholder;
    });

    // Markdown bold syntax'ı HTML'e çevir (**text** → <strong>text</strong>)
    // Now it won't affect links because they're preserved
    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

    // Restore preserved links after bold processing
    preservedLinks.forEach((link, index) => {
        html = html.replace(`___LINK_PRESERVED_${index}___`, link);
    });

    // 1. Link'lere target="_blank", rel ve Tailwind class ekle
    // Standart link: <a href="url">text</a>
    html = html.replace(/<a\s+href="([^"]+)"([^>]*)>([^<]+)<\/a>/gi, function(match, url, attrs, text) {
        // Zaten target varsa dokunma
        if (attrs.includes('target=')) return match;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors font-medium">${text}</a>`;
    });

    // Link içinde <strong> olan durumlar: <a href="url"><strong>text</strong></a>
    html = html.replace(/<a\s+href="([^"]+)"([^>]*)><strong>([^<]+)<\/strong><\/a>/gi, function(match, url, attrs, text) {
        if (attrs.includes('target=')) return match;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors font-bold">${text}</a>`;
    });

    // Link içinde <b> olan durumlar: <a href="url"><b>text</b></a>
    html = html.replace(/<a\s+href="([^"]+)"([^>]*)><b>([^<]+)<\/b><\/a>/gi, function(match, url, attrs, text) {
        if (attrs.includes('target=')) return match;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors font-bold">${text}</a>`;
    });

    // 2. <ul> listelerine Tailwind class ekle (COMPACT: dar alan için optimize)
    html = html.replace(/<ul>/gi, '<ul class="space-y-1 my-2 pl-5 list-disc">');

    // 3. <ol> listelerine Tailwind class ekle (COMPACT)
    html = html.replace(/<ol>/gi, '<ol class="space-y-1 my-2 pl-5 list-decimal">');

    // 4. <li> elementlerine class ekle (COMPACT: minimal margin, NO manual bullet)
    html = html.replace(/<li>/gi, '<li class="text-gray-800 dark:text-gray-200 leading-snug">');

    // 5. <p> elementlerine class ekle (mb-5 = daha fazla boşluk)
    html = html.replace(/<p>/gi, '<p class="mb-5 text-gray-800 dark:text-gray-200 leading-relaxed">');

    // 6. <h3> başlıklarına class ekle
    html = html.replace(/<h3>/gi, '<h3 class="text-lg font-bold mt-4 mb-3 text-gray-900 dark:text-gray-100">');

    // 7. <h4> başlıklarına class ekle
    html = html.replace(/<h4>/gi, '<h4 class="text-base font-semibold mt-3 mb-2 text-gray-900 dark:text-gray-100">');

    // 8. <strong> elementlerine class ekle
    html = html.replace(/<strong>/gi, '<strong class="font-bold text-gray-900 dark:text-white">');

    // 9. <b> elementlerine class ekle
    html = html.replace(/<b>/gi, '<b class="font-bold text-gray-900 dark:text-white">');

    // 10. <em> elementlerine class ekle
    html = html.replace(/<em>/gi, '<em class="italic text-gray-700 dark:text-gray-300">');

    // 11. <i> elementlerine class ekle
    html = html.replace(/<i>/gi, '<i class="italic text-gray-700 dark:text-gray-300">');

    // 12. <br> sonrasında biraz boşluk ekle
    html = html.replace(/<br\s*\/?>/gi, '<br class="my-1">');

    // 13. <div> varsa temel class ekle
    html = html.replace(/<div>/gi, '<div class="my-2">');

    return html;
};

console.log('✅ AI Chat Markdown→HTML Converter & Enhancer v4.0 loaded');
</script>
