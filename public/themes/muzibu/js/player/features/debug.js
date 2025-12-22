/**
 * 🧪 DEBUG FEATURE - Queue & Playback Debug System
 *
 * Bu feature test/debug amaclı. Production'da kapatılabilir.
 * showDebugInfo: false yaparak indicator gizlenir.
 *
 * player-core.js bu objeyi spread eder:
 * ...(window.debugFeature || {})
 *
 * DEBUG PANELI:
 * - Sag alt kosede gorunur (showDebugInfo: true iken)
 * - Son 20 event'i gosterir
 * - Queue degisikliklerini, refill'leri, transition'lari loglar
 */

// 🛡️ GUARD: Prevent redeclaration on SPA navigation
if (typeof DEBUG_COLORS !== 'undefined') {
    console.log('⚠️ Debug module already loaded, skipping...');
} else {

// 🎯 Debug Log Storage
window.debugLogs = window.debugLogs || [];
window.debugMaxLogs = window.debugMaxLogs || 30;

// 🎨 Event Type Colors (for console)
const DEBUG_COLORS = {
    refill: '#10b981',      // green
    transition: '#f59e0b',  // amber
    exhausted: '#ef4444',   // red
    fallback: '#8b5cf6',    // purple
    queue: '#3b82f6',       // blue
    play: '#06b6d4',        // cyan
    remove: '#f97316',      // orange
    shuffle: '#ec4899',     // pink
    info: '#6b7280'         // gray
};

// 🎯 Context Type Labels (Turkish)
const CONTEXT_LABELS = {
    genre: 'Tür',
    album: 'Albüm',
    playlist: 'Playlist',
    artist: 'Sanatçı',
    sector: 'Sektör',
    radio: 'Radyo',
    favorites: 'Favoriler',
    recent: 'Son Dinlenenler',
    popular: 'Popüler',
    search: 'Arama'
};

/**
 * 🔧 Debug Log Function
 * @param {string} type - Event type (refill, transition, exhausted, etc.)
 * @param {string} message - Main message
 * @param {object} details - Additional details
 */
window.debugLog = function(type, message, details = {}) {
    const timestamp = new Date().toLocaleTimeString('tr-TR');
    const color = DEBUG_COLORS[type] || DEBUG_COLORS.info;

    const logEntry = {
        id: Date.now(),
        timestamp,
        type,
        message,
        details,
        color
    };

    // Add to log array
    window.debugLogs.unshift(logEntry);

    // Keep only last N logs
    if (window.debugLogs.length > window.debugMaxLogs) {
        window.debugLogs = window.debugLogs.slice(0, window.debugMaxLogs);
    }

    // Console output with styling (only if debug panel is active)
    const playerStore = window.Alpine?.store?.('muzibuPlayer');
    if (playerStore?.showDebugInfo) {
        const emoji = getTypeEmoji(type);
        console.log(
            `%c${emoji} [${timestamp}] ${message}`,
            `color: ${color}; font-weight: bold;`,
            details
        );
    }

    // Update Alpine store if available
    if (window.Alpine?.store('debug')) {
        window.Alpine.store('debug').logs = [...window.debugLogs];
    }

    // Dispatch event for UI updates
    window.dispatchEvent(new CustomEvent('debug-log', { detail: logEntry }));
};

/**
 * Get emoji for event type
 */
function getTypeEmoji(type) {
    const emojis = {
        refill: '🔄',
        transition: '➡️',
        exhausted: '⚠️',
        fallback: '🔀',
        queue: '📋',
        play: '▶️',
        remove: '🗑️',
        shuffle: '🎲',
        info: 'ℹ️'
    };
    return emojis[type] || '📝';
}

/**
 * 🎵 Queue Refill Debug Logger
 */
window.debugQueueRefill = function(source, count, reason, contextDetails = {}) {
    // reason artık tam mesaj içeriyor (player-core.js'den geliyor)
    window.debugLog('refill', reason, contextDetails);
};

/**
 * ➡️ Context Transition Debug Logger
 */
window.debugContextTransition = function(from, to, reason) {
    const fromLabel = CONTEXT_LABELS[from?.type] || from?.type || 'Yok';
    const toLabel = CONTEXT_LABELS[to?.type] || to?.type || 'Yok';

    window.debugLog('transition', `${fromLabel} → ${toLabel}`, {
        onceki: from,
        yeni: to,
        sebep: reason
    });
};

/**
 * ⚠️ Context Exhausted Debug Logger
 */
window.debugContextExhausted = function(contextType, contextId, totalPlayed, reason) {
    const contextLabel = CONTEXT_LABELS[contextType] || contextType;

    window.debugLog('exhausted', `${contextLabel} tükendi!`, {
        tip: contextLabel,
        id: contextId,
        toplam_calindi: totalPlayed,
        sebep: reason
    });
};

/**
 * 🔀 Fallback Debug Logger
 */
window.debugFallback = function(fallbackType, success, details = {}) {
    const status = success ? 'Başarılı' : 'Başarısız';
    const fallbackLabel = CONTEXT_LABELS[fallbackType] || fallbackType;

    window.debugLog('fallback', `Fallback: ${fallbackLabel} (${status})`, {
        tip: fallbackLabel,
        basarili: success,
        ...details
    });
};

/**
 * 📋 Queue State Debug Logger
 */
window.debugQueueState = function(action, queueLength, queueIndex, details = {}) {
    window.debugLog('queue', `Queue: ${action}`, {
        toplam: queueLength,
        index: queueIndex,
        kalan: queueLength - queueIndex,
        ...details
    });
};

/**
 * ▶️ Play Event Debug Logger
 */
window.debugPlayEvent = function(song, source, index, queueLength) {
    const songTitle = song?.song_title?.tr || song?.song_title?.en || song?.song_title || 'Bilinmiyor';
    const artistTitle = song?.artist_title?.tr || song?.artist_title?.en || song?.artist_title || 'Bilinmiyor';

    window.debugLog('play', `▶️ Çalıyor: "${songTitle}" (${artistTitle})`, {
        sira: `${index + 1}/${queueLength}`,
        kaynak: source || 'Doğrudan',
        sarki_id: song?.song_id || song?.id
    });
};

/**
 * 🗑️ Remove Event Debug Logger
 */
window.debugRemoveEvent = function(removedSong, wasLastSong, queueLength) {
    const songTitle = removedSong?.song_title?.tr || removedSong?.song_title?.en || removedSong?.song_title || 'Bilinmiyor';

    window.debugLog('remove', `🗑️ Kuyruktan çıkarıldı: "${songTitle}"`, {
        sarki: songTitle,
        son_sarki_miydi: wasLastSong,
        kalan_queue: queueLength
    });
};

/**
 * 🎲 Shuffle Debug Logger
 */
window.debugShuffle = function(contextType, reason) {
    const contextLabel = CONTEXT_LABELS[contextType] || contextType;

    window.debugLog('shuffle', `Shuffle: ${contextLabel}`, {
        tip: contextLabel,
        sebep: reason
    });
};

/**
 * 📊 Get Debug Summary
 */
window.getDebugSummary = function() {
    const summary = {
        total_events: window.debugLogs.length,
        refills: window.debugLogs.filter(l => l.type === 'refill').length,
        transitions: window.debugLogs.filter(l => l.type === 'transition').length,
        fallbacks: window.debugLogs.filter(l => l.type === 'fallback').length,
        exhausted: window.debugLogs.filter(l => l.type === 'exhausted').length
    };

    console.table(summary);
    return summary;
};

/**
 * 🧹 Clear Debug Logs
 */
window.clearDebugLogs = function() {
    window.debugLogs = [];
    if (window.Alpine?.store('debug')) {
        window.Alpine.store('debug').logs = [];
    }
};

// 🎯 Debug Feature Export (spread by player-core.js)
window.debugFeature = {
    // 🔧 DEBUG MODE - Production'da false yap
    showDebugInfo: true,

    // Debug panel state
    showDebugPanel: false,

    // Toggle debug panel
    toggleDebugPanel() {
        this.showDebugPanel = !this.showDebugPanel;
    }
};

// 🏪 Alpine Debug Store (register when Alpine loads)
document.addEventListener('alpine:init', () => {
    // Skip if already registered
    if (Alpine.store('debug')) return;

    Alpine.store('debug', {
        logs: window.debugLogs,
        showPanel: false,

        togglePanel() {
            this.showPanel = !this.showPanel;
        },

        clearLogs() {
            window.clearDebugLogs();
        },

        getSummary() {
            return window.getDebugSummary();
        }
    });
});

} // END GUARD

