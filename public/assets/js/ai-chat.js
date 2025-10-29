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

// Register store - handle both cases: before and after Alpine init
function registerAiChatStore() {
    if (typeof Alpine === 'undefined') {
        console.warn('⏳ Alpine not loaded yet, waiting...');
        setTimeout(registerAiChatStore, 50);
        return;
    }

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
        },

        // Update context (product_id, category_id, page_slug)
        updateContext(newContext) {
            this.context = { ...this.context, ...newContext };
        },

        // Toggle floating widget
        toggleFloating() {
            this.floatingOpen = !this.floatingOpen;

            // Save state to localStorage
            localStorage.setItem('ai_chat_floating_open', this.floatingOpen.toString());

            // Mark all messages as read when opening
            if (this.floatingOpen && this.messages.length > 0) {
                localStorage.setItem('ai_chat_last_read_index', (this.messages.length - 1).toString());
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

            // Clear "user closed" flag (user is actively using the chat now)
            localStorage.removeItem('user_closed_ai_chat');

            // Mark all messages as read
            if (this.messages.length > 0) {
                localStorage.setItem('ai_chat_last_read_index', (this.messages.length - 1).toString());
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

            // Mark that user has manually closed the widget (prevents auto-open on other pages)
            localStorage.setItem('user_closed_ai_chat', 'true');
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

        // Send message to AI (STREAMING VERSION - ChatGPT style)
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

            // Merge context
            const finalContext = { ...this.context, ...contextOverride };

            // ⚡ OPTIMIZED NON-STREAMING REQUEST
            const endpoint = window.location.origin + '/api/ai/v1/shop-assistant/chat';

            try {
                // POST request
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
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

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Mesaj gönderilemedi');
                }

                // Add AI response to chat
                this.addMessage({
                    role: 'assistant',
                    content: data.data.message,
                    created_at: new Date().toISOString(),
                });

                // Update session info
                if (data.data.session_id) {
                    this.sessionId = data.data.session_id;
                    localStorage.setItem('ai_chat_session_id', this.sessionId);
                }
                if (data.data.conversation_id) {
                    this.conversationId = data.data.conversation_id;
                }

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
            this.messages = [];
            this.conversationId = null;
            this.sessionId = null;
            localStorage.removeItem('ai_chat_session_id');

            // Placeholder will automatically show when messages are empty
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

        // Get unread AI messages count (only assistant messages when chat is closed)
        get unreadCount() {
            // If chat is open, user has read everything
            if (this.floatingOpen) {
                return 0;
            }

            // Count assistant messages received while chat was closed
            const lastReadIndex = parseInt(localStorage.getItem('ai_chat_last_read_index') || '-1');
            const unreadMessages = this.messages.filter((msg, index) => {
                return msg.role === 'assistant' && index > lastReadIndex;
            });

            return unreadMessages.length;
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
}

// Call the register function
// If Alpine is already initialized, register immediately
// Otherwise, wait for alpine:init event
if (typeof Alpine !== 'undefined' && Alpine.version) {
    // Alpine already loaded, register immediately
    registerAiChatStore();
} else {
    // Wait for Alpine to initialize
    document.addEventListener('alpine:init', registerAiChatStore);
}

/**
 * =============================================================================
 * 2. MARKDOWN → HTML CONVERTER - AI Message Formatter
 * =============================================================================
 *
 * Ne işe yarar:
 * - AI'ın Markdown formatındaki cevaplarını güzel HTML'e çevirir
 * - **kalın yazı** → <strong> tags
 * - - liste → bullet liste
 * - 1. 2. 3. → numaralı liste
 * - [text](url) → Standart markdown link (href'e göre otomatik class eklenir)
 * - Paragraflar, renkler, hover efektleri ekler
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    // 🚀 BACKEND HTML RENDERING
    // Backend (PHP) artık markdown'ı HTML'e çeviriyor (league/commonmark)
    // Frontend sadece HTML'i render ediyor - güvenli ve tutarlı!
    //
    // Eski sistem: JavaScript'te 290 satır custom markdown parser (hataya açık, güvensiz)
    // Yeni sistem: PHP'de league/commonmark library (battle-tested, XSS korumalı, 15+ yıllık)
    //
    // Backend işlemleri:
    // - Custom link formatları: [LINK:shop:slug], [LINK:shop:category:slug]
    // - Markdown → HTML parsing (CommonMark + GFM extension)
    // - XSS koruması (html_input: strip)
    // - Tailwind class injection
    // - Link target & rel attributes
    //
    // Frontend sorumluluğu:
    // - Sadece backend'den gelen HTML'i render et
    // - Güvenli, hızlı, minimal kod

    // Direkt content'i döndür (backend'den HTML geliyor)
    return content;
};
window.clearAIConversation = function(button) {
    if (!window.Alpine || !window.Alpine.store('aiChat')) {
        console.error('❌ AI Chat sistemi yüklü değil!');
        return;
    }

    const chat = window.Alpine.store('aiChat');

    if (!chat.conversationId) {
        return;
    }

    // Show loading
    const originalText = button.querySelector('.button-text').textContent;
    const spinner = button.querySelector('.loading-spinner');
    button.querySelector('.button-text').textContent = '✓';
    spinner.classList.remove('hidden');
    button.disabled = true;

    // Delete from database
    fetch('/api/ai/v1/conversation/' + chat.conversationId, { method: 'DELETE' })
        .then(response => {
            if (!response.ok) throw new Error('API hatası');

            // Clear from Alpine store
            chat.clearConversation();

            // Visual feedback
            button.querySelector('.button-text').textContent = '✓ Temizlendi';
            setTimeout(() => {
                button.querySelector('.button-text').textContent = originalText;
            }, 2000);
        })
        .catch(err => {
            console.error('AI conversation clear error:', err);
            button.querySelector('.button-text').textContent = '✗ Hata';
            setTimeout(() => {
                button.querySelector('.button-text').textContent = originalText;
            }, 2000);
        })
        .finally(() => {
            // Reset button
            button.querySelector('.button-text').textContent = originalText;
            spinner.classList.add('hidden');
            button.disabled = false;
        });
};
