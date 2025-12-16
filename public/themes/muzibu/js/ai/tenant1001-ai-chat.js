/**
 * Tenant 1001 AI Chat Store
 * Alpine.js Store for Muzibu AI Music Assistant
 */

document.addEventListener('alpine:init', () => {
    Alpine.store('tenant1001AI', {
        // State
        isOpen: false,
        isMinimized: false,
        isLoading: false,
        messages: [],
        quickActions: [],
        sessionId: null,
        conversationId: null,
        currentMessage: '',

        // Init
        init() {
            this.loadSession();
            // ⏱️ DELAYED: Fetch quick actions after 900ms (avoid rate limiting on page load)
            setTimeout(() => {
                this.fetchQuickActions();
            }, 900);
        },

        // Toggle chat window
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.isMinimized = false;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.focusInput();
                });
            }
        },

        // Minimize/maximize
        toggleMinimize() {
            this.isMinimized = !this.isMinimized;
        },

        // Close
        close() {
            this.isOpen = false;
            this.isMinimized = false;
        },

        // Send message
        async sendMessage(message = null) {
            const msg = message || this.currentMessage.trim();
            if (!msg || this.isLoading) return;

            // Add user message
            this.messages.push({
                role: 'user',
                content: msg,
                timestamp: new Date()
            });

            this.currentMessage = '';
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const response = await fetch('/api/ai/v1/assistant/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        message: msg,
                        session_id: this.sessionId,
                        conversation_id: this.conversationId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Add AI response
                    this.messages.push({
                        role: 'assistant',
                        content: data.data.message,
                        timestamp: new Date()
                    });

                    // Update session info
                    this.sessionId = data.data.session_id;
                    this.conversationId = data.data.conversation_id;

                    // Update quick actions if provided
                    if (data.data.quick_actions) {
                        this.quickActions = data.data.quick_actions.slice(0, 4); // Limit to 4
                    }

                    this.saveSession();
                } else {
                    throw new Error(data.message || 'Bir hata oluştu');
                }
            } catch (error) {
                console.error('AI Chat Error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.',
                    timestamp: new Date(),
                    isError: true
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },

        // Quick action click
        handleQuickAction(action) {
            if (action.message) {
                this.sendMessage(action.message);
            }
        },

        // Fetch initial quick actions
        async fetchQuickActions() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch('/api/ai/v1/assistant/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        message: '',
                        get_quick_actions: true
                    })
                });


                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Quick Actions HTTP Error:', response.status, errorText);
                    return;
                }

                const data = await response.json();

                if (data.success && data.data.quick_actions) {
                    this.quickActions = data.data.quick_actions.slice(0, 4);
                }
            } catch (error) {
                console.error('❌ Quick Actions Error:', error);
            }
        },

        // Save session to localStorage
        saveSession() {
            try {
                localStorage.setItem('tenant1001_ai_session', JSON.stringify({
                    sessionId: this.sessionId,
                    conversationId: this.conversationId,
                    messages: this.messages.slice(-10) // Keep last 10 messages
                }));
            } catch (e) {
                console.error('Session save error:', e);
            }
        },

        // Load session from localStorage
        loadSession() {
            try {
                const saved = localStorage.getItem('tenant1001_ai_session');
                if (saved) {
                    const data = JSON.parse(saved);
                    this.sessionId = data.sessionId;
                    this.conversationId = data.conversationId;
                    this.messages = data.messages || [];
                }
            } catch (e) {
                console.error('Session load error:', e);
            }
        },

        // Clear conversation
        clearConversation() {
            if (confirm('Konuşma geçmişini temizlemek istediğinize emin misiniz?')) {
                this.messages = [];
                this.sessionId = null;
                this.conversationId = null;
                localStorage.removeItem('tenant1001_ai_session');
            }
        },

        // Scroll to bottom
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.querySelector('#ai-chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        // Focus input
        focusInput() {
            const input = document.querySelector('#ai-chat-input');
            if (input) {
                input.focus();
            }
        },

        // Parse markdown to HTML (simple version)
        parseMarkdown(text) {
            if (!text) return '';

            let html = text;

            // 🎵 MARKDOWN LİNK PARSE (Şarkı çalma için button'a çevir)
            // 1. "Çal" linklerini HTML button'a çevir (tekil şarkı önerileri için)
            html = html.replace(/\[Çal\]\(([^)]+)\)/g,
                '<button onclick="window.location.href=\'$1\'" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md ml-2"><i class="fas fa-play text-xs"></i> Çal</button>');

            // 2. Diğer markdown linkler: [text](url) → <a> tag
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-blue-400 hover:text-blue-300 underline">$1</a>');

            // Headers (parse first, before bold/italic to avoid ### conflict with *)
            // h3 first, then h2, then h1 to avoid ### matching ##
            html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
            html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
            html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');

            // Bold: **text** or __text__
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

            // Italic: *text* or _text_
            html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
            html = html.replace(/_(.+?)_/g, '<em>$1</em>');

            // Line breaks
            html = html.replace(/\n\n/g, '</p><p>');
            html = html.replace(/\n/g, '<br>');

            // Lists: - item or * item
            html = html.replace(/^[\-\*]\s+(.+)$/gm, '<li>$1</li>');
            html = html.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');

            // Wrap in paragraph if not already wrapped
            if (!html.startsWith('<')) {
                html = '<p>' + html + '</p>';
            }

            return html;
        },

        /**
         * Parse ACTION button from AI response
         * Formats:
         * - [ACTION:CREATE_PLAYLIST:song_ids=325,326:title=Rock Müzikler]
         * - [ACTION:ADD_TO_FAVORITES:type=song:id=325]
         */
        parseActionButton(content) {
            // Try CREATE_PLAYLIST first
            const playlistRegex = /\[ACTION:CREATE_PLAYLIST:song_ids=([0-9,]+):title=([^\]]+)\]/;
            const playlistMatch = content.match(playlistRegex);

            if (playlistMatch) {
                return {
                    type: 'CREATE_PLAYLIST',
                    songIds: playlistMatch[1].split(',').map(id => parseInt(id)),
                    title: playlistMatch[2],
                    rawAction: playlistMatch[0]
                };
            }

            // Try ADD_TO_FAVORITES
            const favoriteRegex = /\[ACTION:ADD_TO_FAVORITES:type=(song|playlist|album):id=(\d+)\]/;
            const favoriteMatch = content.match(favoriteRegex);

            if (favoriteMatch) {
                return {
                    type: 'ADD_TO_FAVORITES',
                    favoriteType: favoriteMatch[1],
                    itemId: parseInt(favoriteMatch[2]),
                    rawAction: favoriteMatch[0]
                };
            }

            return null;
        },

        /**
         * Render ACTION button HTML
         */
        renderActionButton(actionData) {
            if (!actionData) return '';

            // CREATE_PLAYLIST button
            if (actionData.type === 'CREATE_PLAYLIST') {
                const songIdsStr = actionData.songIds.join(',');
                return `
                    <div class="mt-4 flex gap-2">
                        <button
                            onclick="Alpine.store('tenant1001AI').handlePlaylistSave('${songIdsStr}', '${actionData.title.replace(/'/g, "\\'")}')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                        >
                            <i class="fas fa-save"></i>
                            Playlist Olarak Kaydet
                        </button>
                    </div>
                `;
            }

            // ADD_TO_FAVORITES button
            if (actionData.type === 'ADD_TO_FAVORITES') {
                const typeLabels = {
                    song: 'Şarkıyı',
                    playlist: 'Playlist\'i',
                    album: 'Albümü'
                };
                const label = typeLabels[actionData.favoriteType] || 'Bu içeriği';

                return `
                    <div class="mt-4 flex gap-2">
                        <button
                            onclick="Alpine.store('tenant1001AI').handleAddToFavorites('${actionData.favoriteType}', ${actionData.itemId})"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium"
                        >
                            <i class="fas fa-heart"></i>
                            ${label} Favorilere Ekle
                        </button>
                    </div>
                `;
            }

            return '';
        },

        /**
         * Check if user is authenticated
         */
        checkUserAuth() {
            // Check if user data exists in window or meta tag
            const userMeta = document.querySelector('meta[name="user-id"]');
            const userAuth = window.authUser || userMeta?.content;
            return !!userAuth;
        },

        /**
         * Handle playlist save button click
         */
        async handlePlaylistSave(songIdsStr, title) {
            // Parse song IDs
            const songIds = songIdsStr.split(',').map(id => parseInt(id));

            // 1. Check auth
            if (!this.checkUserAuth()) {
                this.showNotification('Lütfen önce giriş yapın', 'warning');
                // Redirect to login after 1s
                setTimeout(() => {
                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1000);
                return;
            }

            // 2. Show loading notification
            this.showNotification('Playlist kaydediliyor...', 'info');

            try {
                // 3. Send API request
                const response = await fetch('/api/muzibu/ai/playlist/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        title: title,
                        song_ids: songIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Success!
                    const message = data.can_play
                        ? 'Playlist kaydedildi ve dinlemeye hazır! 🎵'
                        : 'Playlist kaydedildi! Premium ile dinleyebilirsiniz. ✨';

                    this.showNotification(message, 'success');

                    // If can play, optionally redirect to playlist
                    if (data.can_play && data.playlist_url) {
                        setTimeout(() => {
                            if (confirm('Playlist\'i şimdi dinlemek ister misiniz?')) {
                                window.location.href = data.playlist_url;
                            }
                        }, 1500);
                    }
                } else {
                    // Handle specific error codes
                    if (data.error_code === 'PREMIUM_REQUIRED') {
                        this.showNotification(data.message + ' Premium üyeliğe geçin!', 'warning');
                        if (data.upgrade_url) {
                            setTimeout(() => {
                                window.location.href = data.upgrade_url;
                            }, 2000);
                        }
                    } else {
                        this.showNotification(data.message || 'Bir hata oluştu', 'error');
                    }
                }
            } catch (error) {
                console.error('Playlist save error:', error);
                this.showNotification('Bağlantı hatası. Lütfen tekrar deneyin.', 'error');
            }
        },

        /**
         * Handle add to favorites button click
         */
        async handleAddToFavorites(type, itemId) {
            // 1. Check auth
            if (!this.checkUserAuth()) {
                this.showNotification('Lütfen önce giriş yapın', 'warning');
                // Redirect to login after 1s
                setTimeout(() => {
                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1000);
                return;
            }

            // 2. Show loading notification
            this.showNotification('Favorilere ekleniyor...', 'info');

            try {
                // 3. Send API request
                const response = await fetch('/api/muzibu/ai/favorite/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        type: type,
                        id: itemId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Success!
                    this.showNotification(data.message, 'success');
                } else {
                    // Handle specific error codes
                    if (data.error_code === 'AUTH_REQUIRED') {
                        this.showNotification('Lütfen giriş yapın', 'warning');
                        setTimeout(() => {
                            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                        }, 1000);
                    } else if (data.error_code === 'ALREADY_IN_FAVORITES') {
                        this.showNotification(data.message, 'warning');
                    } else {
                        this.showNotification(data.message || 'Bir hata oluştu', 'error');
                    }
                }
            } catch (error) {
                console.error('Add to favorites error:', error);
                this.showNotification('Bağlantı hatası. Lütfen tekrar deneyin.', 'error');
            }
        },

        /**
         * Show toast notification
         */
        showNotification(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-0 ${
                type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' :
                type === 'warning' ? 'bg-yellow-600' :
                'bg-blue-600'
            }`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' :
                        'fa-info-circle'
                    }"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Auto remove after 4s
            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        },

        /**
         * Process AI response content (parse markdown + ACTION buttons)
         */
        processAIContent(content) {
            // 1. Parse ACTION button first
            const actionData = this.parseActionButton(content);

            // 2. Remove ACTION string from content
            let processedContent = content;
            if (actionData) {
                processedContent = content.replace(actionData.rawAction, '').trim();
            }

            // 3. Parse markdown
            let html = this.parseMarkdown(processedContent);

            // 4. Append ACTION button if exists
            if (actionData) {
                html += this.renderActionButton(actionData);
            }

            return html;
        }
    });
});
