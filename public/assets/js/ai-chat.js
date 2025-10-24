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
 * - [LINK:shop:slug] → tıklanabilir ürün linki
 * - Paragraflar, renkler, hover efektleri ekler
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    let html = content;

    // STEP 1: Link Processing (process FIRST)

    // 1A: Product links - **Ürün Adı** [LINK:shop:litef-ept15]
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/${slug}`;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="ai-product-link"><strong>${linkText.trim()}</strong></a>`;
    });

    // 1B: Category links - **Kategori** [LINK:shop:category:slug]
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:category:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/category/${slug}`;
        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="ai-category-link"><strong>${linkText.trim()}</strong></a>`;
    });

    // 1C: Standard Markdown links - [text](url) - WhatsApp, email, tel links
    html = html.replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+|mailto:[^\)]+|tel:[^\)]+)\)/gi, function(match, linkText, url) {
        // Determine link type for styling
        let linkClass = 'ai-standard-link';
        if (url.includes('wa.me') || url.includes('whatsapp')) {
            linkClass = 'ai-whatsapp-link';
        } else if (url.startsWith('mailto:')) {
            linkClass = 'ai-email-link';
        } else if (url.startsWith('tel:')) {
            linkClass = 'ai-phone-link';
        } else if (url.includes('t.me') || url.includes('telegram')) {
            linkClass = 'ai-telegram-link';
        }

        return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="${linkClass}">${linkText.trim()}</a>`;
    });

    // STEP 2: List Processing (MOVED BEFORE BOLD - fixes inline list items!)

    // 2A: Önce satır içi liste öğelerini ayır (AI bazen `- item1 - item2` gibi yazabiliyor)
    // Pattern: Satır başında OLMAYAN tüm " - **" pattern'lerini yeni satıra al
    html = html.replace(/(?<!^)\s+-\s+\*\*/gm, '\n- **');

    // 2B: Numbered lists: 1. 2. 3.
    html = html.replace(/((?:^|\n)\d+[.)](?!\d)\s+.+(?:\n\d+[.)](?!\d)\s+.+)*)/gm, function(match) {
        let items = match.split('\n').filter(line => /^\d+[.)](?!\d)\s+/.test(line.trim()));
        let listItems = items.map(line => {
            let text = line.replace(/^\d+[.)](?!\d)\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ol>\n${listItems}\n</ol>`;
    });

    // 2C: Unordered lists: - item (şimdi düzgün alt alta olmalı)
    html = html.replace(/((?:^|\n)-\s+.+(?:\n-\s+.+)*)/gm, function(match) {
        let items = match.split('\n').filter(line => line.trim().startsWith('- '));
        let listItems = items.map(line => {
            let text = line.replace(/^-\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ul>\n${listItems}\n</ul>`;
    });

    // STEP 3: Bold Processing (AFTER list processing!)
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

/**
 * =============================================================================
 * 4. AI CHAT ADMIN FUNCTIONS
 * =============================================================================
 */

/**
 * Clear AI Conversation
 * Admin fonksiyonu - Aktif konuşma geçmişini siler (DB + Alpine Store)
 *
 * @param {HTMLElement} button - Tıklanan buton elementi
 */
window.clearAIConversation = function(button) {
    if (!window.Alpine || !window.Alpine.store('aiChat')) {
        console.error('❌ AI Chat sistemi yüklü değil!');
        return;
    }

    const chat = window.Alpine.store('aiChat');

    if (!chat.conversationId) {
        console.log('ℹ️ Aktif bir konuşma bulunamadı.');
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

            // Success feedback (no alert!)
            console.log('✅ AI konuşma geçmişi silindi!');

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

console.log('✅ AI Chat System JavaScript loaded');
console.log('📦 Includes: Chat Store, Placeholder System, Markdown Converter, Admin Functions');
