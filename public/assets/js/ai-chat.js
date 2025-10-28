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
 * - [text](url) → Standart markdown link (href'e göre otomatik class eklenir)
 * - Paragraflar, renkler, hover efektleri ekler
 */
window.aiChatRenderMarkdown = function(content) {
    if (!content) return '';

    let html = content;
    let preservedLinks = []; // Link koruma array'i

    // STEP 1: Link Processing - Parse et ve HEMEN koruma altına al

    // HELPER: Link text içindeki markdown pattern'lerini temizle
    function sanitizeLinkText(text) {
        text = text.trim();
        // Newline + liste pattern'lerini temizle (- veya 1. 2. 3.)
        text = text.replace(/\n\s*-\s+/g, ' - ');        // "\n- item" → " - item"
        text = text.replace(/\n\s*\d+[.)]\s+/g, ' ');    // "\n1. item" → " item"
        text = text.replace(/\n/g, ' ');                  // Kalan newline'ları space yap
        return text;
    }

    // 1A: BACKWARD COMPATIBILITY - **Text** [LINK:shop:slug] - optional description
    // Matches: **Text** [LINK:shop:slug] - extra text (dash + description dahil)
    // ⚠️ SADECE aynı satırdaki dash + text'i yakala (newline EXCLUDE)
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]([ \t]+-[ \t]+[^\n]+)?/gi, function(match, linkText, slug, extraText) {
        linkText = sanitizeLinkText(linkText);
        if (extraText) {
            // Dash + extra text varsa link text'e ekle
            linkText += extraText.replace(/[ \t]+-[ \t]+/, ' - '); // Normalize dash spacing
        }
        return `<a href="/shop/${slug}" target="_blank" rel="noopener noreferrer"><strong>${linkText}</strong></a>`;
    });

    // 1A2: BROKEN FORMAT - **Text** [LINK:shopslug] (colon eksik, "shop" prefix strip)
    html = html.replace(/\*\*([^*]+)\*\*\s*\[LINK:shop([\w\-İıĞğÜüŞşÖöÇç]+)\]/gi, function(match, linkText, slug) {
        linkText = sanitizeLinkText(linkText);
        // "shop" prefix varsa çıkar (ör: "shopxtif-f1" → "xtif-f1")
        slug = slug.replace(/^shop/, '');
        return `<a href="/shop/${slug}" target="_blank" rel="noopener noreferrer"><strong>${linkText}</strong></a>`;
    });

    // 1B: BACKWARD COMPATIBILITY - [Text] [LINK:shop:slug] - optional description
    html = html.replace(/\[([^\]]+)\]\s*\[LINK:shop:([\w\-İıĞğÜüŞşÖöÇç]+)\]([ \t]+-[ \t]+[^\n]+)?/gi, function(match, linkText, slug, extraText) {
        linkText = sanitizeLinkText(linkText);
        if (extraText) {
            linkText += extraText.replace(/[ \t]+-[ \t]+/, ' - ');
        }
        return `<a href="/shop/${slug}" target="_blank" rel="noopener noreferrer">${linkText}</a>`;
    });

    // 1C: Standard Markdown - [text](url) (yeni format)
    html = html.replace(/\[([^\]]+)\]\(([^\)]+)\)/gi, function(match, linkText, url) {
        linkText = sanitizeLinkText(linkText);
        return `<a href="${url}" target="_blank" rel="noopener noreferrer">${linkText}</a>`;
    });

    // 1D: KORUMA ALTINA AL - Link içindeki text işlenmesin!
    html = html.replace(/(<a[\s\S]*?<\/a>)/g, function(match, link) {
        let placeholder = `___LINK_PRESERVED_${preservedLinks.length}___`;
        preservedLinks.push(link);
        return placeholder;
    });

    // STEP 2: List Processing (Artık link'ler korunuyor!)

    // 2A: Önce satır içi liste öğelerini ayır (AI bazen `- item1 - item2` gibi yazabiliyor)
    // Pattern 1: Satır başında OLMAYAN tüm " - **" pattern'lerini yeni satıra al
    html = html.replace(/(?<!^)\s+-\s+\*\*/gm, '\n- **');

    // Pattern 2: Satır başında OLMAYAN tüm " - " (bold olmasa da) yeni satıra al
    // Örnek: "- 2 ton - 80V - Verimli" → "- 2 ton\n- 80V\n- Verimli"
    html = html.replace(/(?<!^|\n)(\s+-\s+)(?=[A-Za-z0-9İıĞğÜüŞşÖöÇç])/gm, '\n- ');

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

            // CRITICAL FIX: Liste maddesi içinde cümle bitişi + yeni cümle varsa ayır
            // Örnek: "- Özellik A Fiyat talep üzerine..." → "- Özellik A" + paragraf
            // Pattern: ". " veya "! " veya "? " sonra büyük harf = yeni cümle başlıyor
            let sentenceSplit = text.match(/^(.*?[.!?])\s+([A-ZİÇŞĞÜÖ].*)$/);
            if (sentenceSplit) {
                // İlk cümle liste içinde kalsın, ikincisi liste dışına çıksın
                return `<li>${sentenceSplit[1]}</li>\n</ul>\n\n<p>${sentenceSplit[2]}</p>\n<ul>`;
            }

            return `<li>${text}</li>`;
        }).join('\n');

        // Boş <ul></ul> çiftlerini temizle (split işleminden kalan)
        let result = `<ul>\n${listItems}\n</ul>`;
        result = result.replace(/<\/ul>\s*<ul>/g, '');
        return result;
    });

    // STEP 3: Bold Processing (Link'ler zaten korunuyor, sadece bold parse et)
    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

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

    // STEP 4B: Liste bitişinden sonra paragraf başlıyorsa ayır
    // Örnek: "</ul>Bu forkliftler" → "</ul>\n\n<p>Bu forkliftler"
    html = html.replace(/(<\/ul>|<\/ol>)([A-ZİÇŞĞÜÖ])/g, '$1\n\n$2');

    // STEP 4C: LINK RESTORATION - Placeholder'ları gerçek link'lerle değiştir
    preservedLinks.forEach((link, index) => {
        html = html.replace(`___LINK_PRESERVED_${index}___`, link);
    });

    // STEP 5: Add Tailwind Classes
    html = html.replace(/<ul>/gi, '<ul class="space-y-0.5 my-1 pl-3 list-disc">');
    html = html.replace(/<ol>/gi, '<ol class="space-y-0.5 my-1 pl-3 list-decimal">');
    html = html.replace(/<li>/gi, '<li class="text-gray-800 dark:text-gray-200 leading-snug">');
    html = html.replace(/<p>/gi, '<p class="mb-3 text-gray-800 dark:text-gray-200 leading-relaxed">');
    html = html.replace(/<strong>/gi, '<strong class="font-bold text-gray-900 dark:text-white">');

    // STEP 6: POST-PROCESS - Add CSS classes to all <a> tags based on href
    html = html.replace(/<a\s+([^>]*href=["']([^"']+)["'][^>]*)>/gi, function(match, attrs, href) {
        let linkClass = 'ai-standard-link';

        // URL pattern'ine göre class belirle
        if (href.startsWith('/shop/')) {
            linkClass = 'ai-product-link';
        } else if (href.startsWith('/category/') || href.includes('/shop/category/')) {
            linkClass = 'ai-category-link';
        } else if (href.includes('wa.me') || href.includes('whatsapp')) {
            linkClass = 'ai-whatsapp-link';
        } else if (href.startsWith('mailto:')) {
            linkClass = 'ai-email-link';
        } else if (href.startsWith('tel:')) {
            linkClass = 'ai-phone-link';
        } else if (href.includes('t.me') || href.includes('telegram')) {
            linkClass = 'ai-telegram-link';
        }

        // Mevcut class varsa ekle, yoksa yeni ekle
        if (attrs.includes('class=')) {
            return `<a ${attrs.replace(/class=["']([^"']*)["']/i, `class="$1 ${linkClass}"`)}>`;
        } else {
            return `<a ${attrs} class="${linkClass}">`;
        }
    });

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
