/**
 * AI Chat System JavaScript
 *
 * Bu dosya AI Chat floating widget için gerekli tüm JavaScript fonksiyonlarını içerir.
 * Component: resources/views/components/ai/floating-widget.blade.php
 *
 * İçerik:
 * 1. AI Chat Store (Alpine.js global state)
 * 2. Placeholder Animation System (örnek sohbet gösterimi)
 * 3. Markdown → HTML Converter (AI mesajlarını formatlar)
 *
 * @version 1.0.0
 * @package AI Content System
 */

/**
 * =============================================================================
 * 1. AI CHAT STORE - Alpine.js Global State Management
 * =============================================================================
 *
 * Ne işe yarar:
 * - Tüm chat mesajlarını saklar
 * - API'ye mesaj gönderir ve cevap alır
 * - Konuşma geçmişini yükler
 * - Chat penceresini açar/kapatır
 * - LocalStorage'da session ID tutar
 */
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
        apiEndpoint: window.location.origin + '/api/ai/v1/shop-assistant/chat',
        historyEndpoint: window.location.origin + '/api/ai/v1/shop-assistant/history',
        assistantName: 'iXtif Yapay Zeka Asistanı',

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
 * =============================================================================
 * 2. PLACEHOLDER ANIMATION SYSTEM - Slide Up Animation
 * =============================================================================
 *
 * Ne işe yarar:
 * - Chat penceresi boşken örnek sohbet gösterir
 * - "Bu ürün ne işe yarar?" gibi örnek sorular
 * - Mesajlar yukarıdan aşağıya animasyonlu gelir
 * - Kullanıcıyı chat kullanmaya teşvik eder
 * - Ürüne özel placeholder API'den yükler (cache'lenir)
 */
window.placeholderV4 = function(productId = null) {
    return {
        conversation: [],
        isLoading: true,
        loadError: false,

        init() {
            console.log('🔍 Placeholder init started', { productId });

            // ⚡ PERFORMANCE: Show fallback immediately, load real data in background
            this.conversation = this.getFallbackConversation();
            this.isLoading = false;

            console.log('⚡ Fallback shown immediately');

            // Load real placeholder in background
            if (productId) {
                const deferredLoad = () => {
                    console.log('🔄 Loading real placeholder from API...');
                    this.loadProductPlaceholder(productId);
                };

                if ('requestIdleCallback' in window) {
                    requestIdleCallback(deferredLoad, { timeout: 2000 });
                } else {
                    setTimeout(deferredLoad, 100);
                }
            }

            console.log('✅ Placeholder init completed', {
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
                    console.log('✅ Product placeholder loaded', {
                        from_cache: data.data.from_cache,
                        load_time_ms: loadTime,
                    });

                    const isSame = JSON.stringify(this.conversation) === JSON.stringify(data.data.conversation);

                    if (!isSame) {
                        this.conversation = data.data.conversation;
                    }

                    this.loadError = false;
                }
            } catch (error) {
                console.error('❌ Failed to load product placeholder:', error);
                this.loadError = true;
            }
        },

        getFallbackConversation() {
            const assistantName = 'iXtif Yapay Zeka Asistanı';

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

            console.log('🎬 Placeholder animation started');

            await this.sleep(1000);

            for (let msg of this.conversation) {
                if (msg.role === 'assistant') {
                    await this.showTypingIndicator(container);
                    await this.sleep(1500);
                    await this.hideTypingIndicator(container);
                }

                await this.slideUpMessage(msg.text, msg.role, container);
                await this.sleep(1800);
            }

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

            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
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

            const textSpan = document.createElement('span');
            textSpan.className = role === 'user'
                ? 'text-white opacity-50'
                : 'text-gray-800 dark:text-white opacity-50';
            textSpan.textContent = text;
            bubble.appendChild(textSpan);

            msgDiv.appendChild(bubble);
            container.appendChild(msgDiv);

            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });

            await this.sleep(50);
            bubble.classList.remove('translate-y-4');
            bubble.classList.add('translate-y-0');

            await this.sleep(100);
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
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

            await this.sleep(200);
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });

            await this.sleep(500);
            container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
        },

        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    };
};

/**
 * =============================================================================
 * 3. MARKDOWN → HTML CONVERTER - AI Message Formatter
 * =============================================================================
 *
 * Ne işe yarar:
 * - AI'ın Markdown formatındaki cevaplarını güzel HTML'e çevirir
 * - **kalın yazı** → <strong> tags
 * - - liste → bullet liste
 * - 1. 2. 3. → numaralı liste
 * - [LINK:shop:slug] → tıklanabilir ürün linki
 * - Paragraflar, renkler, hover efektleri ekler
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    let html = content;

    // STEP 1: Link Processing (process FIRST)
    // Format: **Ürün Adı** [LINK:shop:litef-ept15]
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/${slug}`;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="ai-product-link"><strong>${linkText.trim()}</strong></a>`;
    });

    // Category links
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:category:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/category/${slug}`;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="ai-category-link"><strong>${linkText.trim()}</strong></a>`;
    });

    // STEP 2: Bold Processing
    let preservedLinks = [];
    html = html.replace(/(<a[\s\S]*?<\/a>)/g, function(match, link) {
        let linkPlaceholder = `___LINK_PRESERVED_${preservedLinks.length}___`;
        preservedLinks.push(link);
        return linkPlaceholder;
    });

    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

    preservedLinks.forEach((link, index) => {
        html = html.replace(`___LINK_PRESERVED_${index}___`, link);
    });

    // STEP 3: List Processing
    // Numbered lists: 1. 2. 3.
    html = html.replace(/((?:^|\n)\d+[.)](?!\d)\s+.+(?:\n\d+[.)](?!\d)\s+.+)*)/gm, function(match) {
        let items = match.split('\n').filter(line => /^\d+[.)](?!\d)\s+/.test(line.trim()));
        let listItems = items.map(line => {
            let text = line.replace(/^\d+[.)](?!\d)\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ol>\n${listItems}\n</ol>`;
    });

    // Unordered lists: - item
    html = html.replace(/((?:^|\n)-\s+.+(?:\n-\s+.+)*)/gm, function(match) {
        let items = match.split('\n').filter(line => line.trim().startsWith('- '));
        let listItems = items.map(line => {
            let text = line.replace(/^-\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ul>\n${listItems}\n</ul>`;
    });

    // STEP 4: Paragraph Processing
    let preservedBlocks = [];
    html = html.replace(/(<ul>[\s\S]*?<\/ul>|<ol>[\s\S]*?<\/ol>)/g, function(match, block) {
        let placeholder = `___PRESERVED_BLOCK_${preservedBlocks.length}___`;
        preservedBlocks.push(block);
        return placeholder;
    });

    html = html.split('\n\n').map(block => {
        block = block.trim();
        if (!block) return '';
        if (block.includes('___PRESERVED_BLOCK_') || block.includes('___LINK_PRESERVED_') || block.match(/^<[a-z]/i)) {
            return block;
        }
        return `<p>${block.replace(/\n/g, '<br>')}</p>`;
    }).join('\n');

    preservedBlocks.forEach((block, index) => {
        html = html.replace(`___PRESERVED_BLOCK_${index}___`, block);
    });

    // STEP 5: Add Tailwind Classes
    html = html.replace(/<ul>/gi, '<ul class="space-y-0.5 my-1 pl-3 list-disc">');
    html = html.replace(/<ol>/gi, '<ol class="space-y-0.5 my-1 pl-3 list-decimal">');
    html = html.replace(/<li>/gi, '<li class="text-gray-800 dark:text-gray-200 leading-snug">');
    html = html.replace(/<p>/gi, '<p class="mb-3 text-gray-800 dark:text-gray-200 leading-relaxed">');
    html = html.replace(/<strong>/gi, '<strong class="font-bold text-gray-900 dark:text-white">');

    return html;
};

console.log('✅ AI Chat System JavaScript loaded');
console.log('📦 Includes: Chat Store, Placeholder System, Markdown Converter');
