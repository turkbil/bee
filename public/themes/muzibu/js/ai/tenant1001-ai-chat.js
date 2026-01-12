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

        /**
         * Format duration: seconds → "Xdk YYsn"
         * @param {number} seconds - Duration in seconds
         * @returns {string} Formatted duration (e.g., "2dk 46sn")
         */
        formatDuration(seconds) {
            if (!seconds || seconds <= 0) return '0sn';

            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;

            if (minutes === 0) {
                return `${secs}sn`;
            }

            return secs > 0 ? `${minutes}dk ${secs}sn` : `${minutes}dk`;
        },

        /**
         * Escape HTML special characters to prevent XSS and HTML injection
         * @param {string} text - Text to escape
         * @returns {string} Escaped text safe for HTML content
         */
        escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        /**
         * Escape string for use in JavaScript code (onclick, etc.)
         * @param {string} text - Text to escape
         * @returns {string} Escaped text safe for JavaScript strings
         */
        escapeJs(text) {
            if (!text) return '';
            return String(text)
                .replace(/\\/g, '\\\\')  // Backslash first!
                .replace(/'/g, "\\'")     // Single quote
                .replace(/"/g, '\\"')     // Double quote
                .replace(/\n/g, '\\n')    // Newline
                .replace(/\r/g, '\\r')    // Carriage return
                .replace(/\t/g, '\\t');   // Tab
        },

        // Parse markdown to HTML (improved version with song table support)
        parseMarkdown(text) {
            if (!text) return '';

            let html = text;

            // 🎵 SPECIAL: Detect and parse song list FIRST (before any markdown)
            // Pattern: "1. **Song Title** - Artist (duration) [Çal](url)"
            const songListRegex = /(\d+)\.\s+\*\*(.+?)\*\*\s+-\s+(.+?)\s+\((\d+)\s*saniye\)\s+\[(?:▶️\s*)?Çal\]\((https?:\/\/[^\/]+\/play\/song\/(\d+))\)/gm;
            let songMatches = [];
            let match;

            while ((match = songListRegex.exec(html)) !== null) {
                songMatches.push({
                    fullMatch: match[0],
                    number: match[1],
                    title: match[2],
                    artist: match[3],
                    seconds: parseInt(match[4]),
                    url: match[5],
                    songId: match[6]
                });
            }

            // If song list detected, convert to table format
            if (songMatches.length > 0) {
                let tableHTML = '<div class="bg-slate-800/30 rounded-xl overflow-hidden my-4"><div class="divide-y divide-slate-700">';

                songMatches.forEach((song) => {
                    const duration = this.formatDuration(song.seconds);
                    // Escape for HTML content (text nodes)
                    const safeTitle = this.escapeHtml(song.title);
                    const safeArtist = this.escapeHtml(song.artist);
                    // Escape for JavaScript code (onclick attribute)
                    const jsUrl = this.escapeJs(song.url);
                    const safeSongId = parseInt(song.songId); // Number, safe to use directly

                    // Build HTML as single line to prevent \n → <br> conversion inside tags
                    // Compact design: minimal padding, icon-only button
                    tableHTML += '<div class="flex items-center gap-2 py-2 px-2 hover:bg-slate-700/30 transition-colors group">' +
                        '<div class="text-slate-500 font-mono text-xs w-6 flex-shrink-0 text-right">' + song.number + '</div>' +
                        '<div class="flex-1 min-w-0">' +
                            '<div class="font-semibold text-white text-sm truncate">' + safeTitle + '</div>' +
                            '<div class="text-xs text-slate-400 truncate">' + safeArtist + ' • ' + duration + '</div>' +
                        '</div>' +
                        '<button type="button" onclick="if(window.playContent){window.playContent(\'song\',' + safeSongId + ')}else{window.location.href=\'' + jsUrl + '\'}" class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors opacity-0 group-hover:opacity-100" title="Çal">' +
                            '<i class="fas fa-play text-xs"></i>' +
                        '</button>' +
                    '</div>';
                });

                tableHTML += '</div></div>';

                // Remove song list from original text, keep rest
                songMatches.forEach((song) => {
                    html = html.replace(song.fullMatch, '');
                });

                // Insert table after first heading
                html = html.replace(/(###?\s+.+?\n)/, (match) => {
                    return match + tableHTML;
                });
            }

            // 1. "Çal" linklerini player-entegre button'a çevir (remaining ones)
            // Compact: icon-only button
            html = html.replace(/\[(?:▶️\s*)?Çal\]\((https?:\/\/[^\/]+\/play\/song\/(\d+))\)/gi, (match, url, songId) => {
                const jsUrl = this.escapeJs(url); // JavaScript escape for onclick
                const safeSongId = parseInt(songId);
                return `<button type="button" onclick="if(window.playContent){window.playContent('song',${safeSongId})}else{window.location.href='${jsUrl}'}" class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors ml-2" title="Çal"><i class="fas fa-play text-xs"></i></button>`;
            });

            // 1b. Hash-based play links: [▶️](#play-song-{ID}) → play button
            html = html.replace(/\[▶️\]\(#play-song-(\d+)\)/gi, (match, songId) => {
                const safeSongId = parseInt(songId);
                return `<button type="button" onclick="if(window.playContent){window.playContent('song',${safeSongId})}" class="inline-flex items-center justify-center w-7 h-7 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" title="Çal"><i class="fas fa-play text-xs"></i></button>`;
            });

            // 1c. Hash-based favorite links: [❤️](#fav-song-{ID}) → favorites button
            html = html.replace(/\[❤️\]\(#fav-song-(\d+)\)/gi, (match, songId) => {
                const safeSongId = parseInt(songId);
                return `<button type="button" onclick="Alpine.store('tenant1001AI').handleAddToFavorites('song',${safeSongId})" class="inline-flex items-center justify-center w-7 h-7 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" title="Favorilere Ekle"><i class="fas fa-heart text-xs"></i></button>`;
            });

            // 1d. Hash-based add-to-playlist links: [➕](#add-song-{ID}) → add button
            html = html.replace(/\[➕\]\(#add-song-(\d+)\)/gi, (match, songId) => {
                const safeSongId = parseInt(songId);
                return `<button type="button" onclick="Alpine.store('tenant1001AI').showAddToPlaylistModal(${safeSongId})" class="inline-flex items-center justify-center w-7 h-7 bg-green-600 hover:bg-green-700 text-white rounded transition-colors" title="Playlist'e Ekle"><i class="fas fa-plus text-xs"></i></button>`;
            });

            // 1e. Markdown table support (basic)
            // Convert markdown tables to HTML tables with styling
            const tableRegex = /^\|(.+)\|$/gm;
            const separatorRegex = /^\|[\s\-:|\s]+\|$/gm;

            if (html.match(tableRegex)) {
                // Replace separator rows first
                html = html.replace(separatorRegex, '<!-- table-separator -->');

                // Convert table rows
                let isFirstRow = true;
                html = html.replace(tableRegex, (match, content) => {
                    const cells = content.split('|').map(cell => cell.trim());

                    if (isFirstRow) {
                        isFirstRow = false;
                        // Header row
                        const headerCells = cells.map(cell => `<th class="px-2 py-1 text-left text-xs font-semibold text-slate-400 border-b border-slate-700">${cell}</th>`).join('');
                        return `<thead><tr>${headerCells}</tr></thead><tbody>`;
                    } else {
                        // Body rows
                        const bodyCells = cells.map(cell => `<td class="px-2 py-1 text-sm text-slate-300">${cell}</td>`).join('');
                        return `<tr class="hover:bg-slate-700/30">${bodyCells}</tr>`;
                    }
                });

                // Remove separator placeholders
                html = html.replace(/<!-- table-separator -->/g, '');

                // Wrap in table
                if (html.includes('<thead>')) {
                    html = html.replace(/<thead>/, '<div class="bg-slate-800/30 rounded-lg overflow-hidden my-3"><table class="w-full text-sm"><thead>');
                    // Find last </tr> before any non-table content and close tbody/table
                    html = html.replace(/(<\/tr>)(?![\s\S]*<tr)/, '$1</tbody></table></div>');
                }
            }

            // 2. Süre formatını düzelt: (166 saniye) → (2dk 46sn)
            html = html.replace(/\((\d+)\s*saniye\)/gi, (match, seconds) => {
                return `(${this.formatDuration(parseInt(seconds))})`;
            });

            // 3. Headers
            html = html.replace(/^### (.+)$/gm, '<h3 class="text-lg font-bold text-white mb-2">$1</h3>');
            html = html.replace(/^## (.+)$/gm, '<h2 class="text-xl font-bold text-white mb-3">$1</h2>');
            html = html.replace(/^# (.+)$/gm, '<h1 class="text-2xl font-bold text-white mb-4">$1</h1>');

            // 4. Bold: **text** or __text__
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

            // 5. Italic: *text* or _text_
            html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
            html = html.replace(/_(.+?)_/g, '<em>$1</em>');

            // 6. Markdown images: ![alt](url) → <img> tag (BEFORE links!)
            html = html.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" class="w-16 h-16 rounded-lg object-cover my-2" loading="lazy">');

            // 7. Regular lists: - item or * item
            html = html.replace(/^[\-\*]\s+(.+)$/gm, '<li>$1</li>');
            html = html.replace(/(<li>.*?<\/li>\s*)+/gs, (match) => {
                return '<ul class="list-disc ml-6 my-2 space-y-1">' + match + '</ul>';
            });

            // 8. Other markdown links: [text](url) → <a> tag
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-blue-400 hover:text-blue-300 underline">$1</a>');

            // 9. Line breaks
            html = html.replace(/\n\n/g, '</p><p>');
            html = html.replace(/\n/g, '<br>');

            // 10. Wrap in paragraph if not already wrapped
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
                // 🎯 AUTO TITLE: Markdown'dan başlığı çek (### veya ##)
                let autoTitle = playlistMatch[2]; // Fallback: ACTION'daki title

                // ### ile başlayan başlık ara (örn: "### Türkçe Pop Playlist")
                const h3Match = content.match(/^###\s+(.+)$/m);
                if (h3Match && h3Match[1]) {
                    autoTitle = h3Match[1].trim();
                } else {
                    // ## ile başlayan başlık ara (alternatif)
                    const h2Match = content.match(/^##\s+(.+)$/m);
                    if (h2Match && h2Match[1]) {
                        autoTitle = h2Match[1].trim();
                    }
                }

                return {
                    type: 'CREATE_PLAYLIST',
                    songIds: playlistMatch[1].split(',').map(id => parseInt(id)),
                    title: autoTitle,
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
         * Show add to playlist modal/dropdown
         */
        async showAddToPlaylistModal(songId) {
            // 1. Check auth
            if (!this.checkUserAuth()) {
                this.showNotification('Lütfen önce giriş yapın', 'warning');
                setTimeout(() => {
                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                }, 1000);
                return;
            }

            // 2. If global addToPlaylist function exists, use it
            if (window.addToPlaylist) {
                window.addToPlaylist('song', songId);
                return;
            }

            // 3. Fallback: Show notification and redirect to song page
            this.showNotification('Şarkı sayfasından playlist\'e ekleyebilirsiniz', 'info');
            setTimeout(() => {
                window.location.href = `/play/song/${songId}`;
            }, 1500);
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
