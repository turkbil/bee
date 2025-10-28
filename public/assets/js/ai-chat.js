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

    let html = content;

    // 🚀 MARKDOWN → HTML CONVERSION
    // IMPORTANT: Process in this order to prevent nested structure issues:
    // 1. Links (preserve them)
    // 2. Bold
    // 3. Lists (numbered, then unordered)
    // 4. Paragraphs
    // 5. Element styling

    // ═══════════════════════════════════════════════════════════════════
    // STEP 1: LINK PROCESSING (Process FIRST to prevent list nesting issues)
    // ═══════════════════════════════════════════════════════════════════

    // Process links BEFORE list parsing to prevent "2. **Text** [LINK]" from creating malformed HTML
    // This ensures links are standalone elements, not wrapped inside list items

    // 0A. NEW FORMAT: [LINK:shop:SLUG] → /shop/slug
    // Format: **Ürün Adı** [LINK:shop:litef-ept15] → /shop/litef-ept15
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/${slug}`;

        // FontAwesome icons
        const shopIcon = '<i class="fas fa-shopping-bag"></i>';
        const arrowIcon = '<i class="fas fa-chevron-right opacity-50 group-hover:opacity-100 transition-opacity"></i>';

        return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 px-3 py-2 my-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 rounded-lg transition-all duration-200 text-sm font-medium no-underline text-gray-900 dark:text-gray-100">' + shopIcon + '<span class="no-underline">' + linkText.trim() + '</span>' + arrowIcon + '</a>';
    });

    // 0B. Category SLUG format: [LINK:shop:category:SLUG]
    // IMPORTANT: ONLY processes **bold** text before [LINK:...] to prevent entire sentence being linked
    // Supports uppercase, Turkish chars
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:category:([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        const url = `/shop/category/${slug}`;

        // FontAwesome category icon
        const icon = '<i class="fas fa-list"></i>';

        // FontAwesome arrow icon
        const arrowIcon = '<i class="fas fa-chevron-right opacity-50 group-hover:opacity-100 transition-opacity"></i>';

        return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 px-3 py-2 my-1 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 hover:border-green-500 dark:hover:border-green-500 rounded-lg transition-all duration-200 text-sm font-medium no-underline text-gray-900 dark:text-gray-100">' + icon + '<span class="no-underline">' + linkText.trim() + '</span>' + arrowIcon + '</a>';
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
                icon = '<i class="fas fa-shopping-bag"></i>';
                colorClass = 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50';
            } else if (type === 'category') {
                url = `/shop/category-by-id/${id}`;
                icon = '<i class="fas fa-list"></i>';
                colorClass = 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50';
            } else if (type === 'brand') {
                url = `/shop/brand-by-id/${id}`;
                icon = '<i class="fas fa-tag"></i>';
                colorClass = 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/50';
            }
        }
        // Blog module
        else if (module === 'blog') {
            url = `/blog/post-by-id/${id}`;
            icon = '<i class="fas fa-newspaper"></i>';
            colorClass = 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/50';
        }
        // Page module
        else if (module === 'page') {
            url = `/page-by-id/${id}`;
            icon = '<i class="fas fa-file-alt"></i>';
            colorClass = 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50';
        }
        // Portfolio module
        else if (module === 'portfolio') {
            url = `/portfolio/project-by-id/${id}`;
            icon = '<i class="fas fa-briefcase"></i>';
            colorClass = 'bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/50';
        }
        // Fallback
        else {
            url = '#';
            icon = '';
            colorClass = 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
        }

        // FontAwesome arrow icon
        const arrowIcon = '<i class="fas fa-chevron-right opacity-50 group-hover:opacity-100 transition-opacity"></i>';

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

        return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 px-3 py-2 my-1 bg-white dark:bg-gray-800 border ' + borderColor + ' rounded-lg transition-all duration-200 text-sm font-medium no-underline text-gray-900 dark:text-gray-100">' + icon + '<span class="no-underline">' + linkText.trim() + '</span>' + arrowIcon + '</a>';
    });

    // BACKWARD COMPATIBILITY: Eski [LINK_ID] formatı
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK_ID:(\d+)(?::([a-z0-9-]+))?\]/gi, function(match, productName, productId, productSlug) {
        const productUrl = `/shop/product/${productId}`;

        // FontAwesome icons
        const shopIcon = '<i class="fas fa-shopping-bag"></i>';
        const arrowIcon = '<i class="fas fa-chevron-right opacity-50 group-hover:opacity-100 transition-opacity"></i>';

        return '<a href="' + productUrl + '" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 px-3 py-2 my-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 rounded-lg transition-all duration-200 text-sm font-medium no-underline text-gray-900 dark:text-gray-100">' + shopIcon + '<span class="no-underline">' + productName.trim() + '</span>' + arrowIcon + '</a>';
    });

    // ═══════════════════════════════════════════════════════════════════
    // STEP 2: BOLD PROCESSING
    // ═══════════════════════════════════════════════════════════════════

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

    // ═══════════════════════════════════════════════════════════════════
    // STEP 3: LIST PROCESSING (Numbered first, then Unordered)
    // ═══════════════════════════════════════════════════════════════════

    // 🔢 NUMBERED LIST: "1. item", "2. item", "3. item" → <ol><li>item</li></ol>
    // CRITICAL FIX: This was missing, causing numbers to show as raw text like "2."
    //
    // Pattern matches:
    // - "1. Item text" or "1) Item text"
    // - Consecutive numbered items (multi-line)
    // - Numbers can be 1-999
    //
    // Example input:
    //   1. İXTİF EPT15-15ES ___LINK_PRESERVED_0___
    //   2. İXTİF EPT20-20ETC ___LINK_PRESERVED_1___
    //
    // Example output:
    //   <ol>
    //   <li>İXTİF EPT15-15ES ___LINK_PRESERVED_0___</li>
    //   <li>İXTİF EPT20-20ETC ___LINK_PRESERVED_1___</li>
    //   </ol>
    html = html.replace(/((?:^|\n)\d+[.)]\s+.+(?:\n\d+[.)]\s+.+)*)/gm, function(match) {
        // Split by newlines and filter lines that start with number + period/paren
        let items = match.split('\n').filter(line => /^\d+[.)]\s+/.test(line.trim()));
        let listItems = items.map(line => {
            // Remove "1. " or "1) " prefix
            let text = line.replace(/^\d+[.)]\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ol>\n${listItems}\n</ol>`;
    });

    // 📋 UNORDERED LIST: "- item" → <ul><li>item</li></ul>
    //
    // Example input:
    //   - Yüksek performans
    //   - Uzun ömürlü
    //
    // Example output:
    //   <ul>
    //   <li>Yüksek performans</li>
    //   <li>Uzun ömürlü</li>
    //   </ul>
    html = html.replace(/((?:^|\n)-\s+.+(?:\n-\s+.+)*)/gm, function(match) {
        let items = match.split('\n').filter(line => line.trim().startsWith('- '));
        let listItems = items.map(line => {
            let text = line.replace(/^-\s*/, '').trim();
            return `<li>${text}</li>`;
        }).join('\n');
        return `<ul>\n${listItems}\n</ul>`;
    });

    // ═══════════════════════════════════════════════════════════════════
    // STEP 4: PARAGRAPH PROCESSING
    // ═══════════════════════════════════════════════════════════════════

    // Preserve already-created list blocks before paragraph processing
    let preservedBlocks = [];
    html = html.replace(/(<ul>[\s\S]*?<\/ul>|<ol>[\s\S]*?<\/ol>)/g, function(match, block) {
        let placeholder = `___PRESERVED_BLOCK_${preservedBlocks.length}___`;
        preservedBlocks.push(block);
        return placeholder;
    });

    // Wrap double-newline-separated text blocks as paragraphs
    html = html.split('\n\n').map(block => {
        block = block.trim();
        if (!block) return '';
        // Don't wrap if it contains preserved blocks or HTML tags
        if (block.includes('___PRESERVED_BLOCK_') || block.includes('___LINK_PRESERVED_') || block.match(/^<[a-z]/i)) {
            return block;
        }
        // Wrap as paragraph, convert single newlines to <br>
        return `<p>${block.replace(/\n/g, '<br>')}</p>`;
    }).join('\n');

    // Restore preserved list blocks
    preservedBlocks.forEach((block, index) => {
        html = html.replace(`___PRESERVED_BLOCK_${index}___`, block);
    });

    // ═══════════════════════════════════════════════════════════════════
    // STEP 5: ELEMENT STYLING (Add Tailwind classes)
    // ═══════════════════════════════════════════════════════════════════

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

    // 2. <ul> listelerine Tailwind class ekle (ULTRA COMPACT: minimal spacing)
    // REDUCED: pl-5 → pl-3, my-2 → my-1, space-y-1 → space-y-0.5
    html = html.replace(/<ul>/gi, '<ul class="space-y-0.5 my-1 pl-3 list-disc">');

    // 3. <ol> listelerine Tailwind class ekle (ULTRA COMPACT)
    // REDUCED: pl-5 → pl-3, my-2 → my-1, space-y-1 → space-y-0.5
    html = html.replace(/<ol>/gi, '<ol class="space-y-0.5 my-1 pl-3 list-decimal">');

    // 4. <li> elementlerine class ekle (COMPACT: minimal margin)
    html = html.replace(/<li>/gi, '<li class="text-gray-800 dark:text-gray-200 leading-snug">');

    // 5. <p> elementlerine class ekle (COMPACT: reduced spacing)
    // REDUCED: mb-5 → mb-3
    html = html.replace(/<p>/gi, '<p class="mb-3 text-gray-800 dark:text-gray-200 leading-relaxed">');

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
