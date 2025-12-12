/**
 * Muzibu Player - Keyboard Shortcuts Module
 * Handles keyboard shortcuts for player control
 *
 * Shortcuts:
 * - Space: Play/Pause
 * - Arrow Left/Right: Seek backward/forward (5 seconds)
 * - Arrow Up/Down: Volume up/down (10%)
 * - M: Mute/Unmute
 * - L: Toggle Loop
 * - S: Toggle Shuffle
 * - N: Next song
 * - P: Previous song
 * - Q: Toggle Queue
 * - F: Toggle Favorites
 * - ?: Show keyboard shortcuts help
 */

function muzibuKeyboard() {
    return {
        // Keyboard shortcuts state
        showKeyboardHelp: false,
        keyboardEnabled: true,

        /**
         * Initialize keyboard shortcuts
         * Called from init() in player-core.js
         */
        initKeyboard() {
            // Add keyboard event listener
            document.addEventListener('keydown', (e) => {
                // Don't trigger if user is typing in input/textarea
                const activeElement = document.activeElement;
                const isInputField = activeElement.tagName === 'INPUT' ||
                                   activeElement.tagName === 'TEXTAREA' ||
                                   activeElement.isContentEditable;

                if (isInputField || !this.keyboardEnabled) {
                    return;
                }

                // Handle different key combinations
                this.handleKeyPress(e);
            });

            console.log('🎹 Keyboard shortcuts initialized');
        },

        /**
         * Handle key press events
         */
        handleKeyPress(e) {
            const key = e.key.toLowerCase();
            const ctrl = e.ctrlKey || e.metaKey;
            const shift = e.shiftKey;

            // Prevent default for handled keys
            const handledKeys = [' ', 'arrowleft', 'arrowright', 'arrowup', 'arrowdown',
                                'm', 'l', 's', 'n', 'p', 'q', 'f', '?'];

            if (handledKeys.includes(key)) {
                e.preventDefault();
            }

            // Execute shortcut action
            switch(key) {
                // Play/Pause
                case ' ':
                case 'k':
                    this.togglePlayPause();
                    // Show feedback based on state AFTER toggle
                    setTimeout(() => {
                        this.showKeyboardFeedback(this.isPlaying ? '▶️ Çalıyor' : '⏸️ Durduruldu');
                    }, 10);
                    break;

                // Seek backward (5 seconds)
                case 'arrowleft':
                case 'j':
                    this.seekBackward(shift ? 10 : 5);
                    this.showKeyboardFeedback(`⏪ ${shift ? 10 : 5} saniye geri`);
                    break;

                // Seek forward (5 seconds)
                case 'arrowright':
                case 'l':
                    if (key === 'l' && !shift) {
                        // L key = toggle loop
                        this.toggleLoop();
                        this.showKeyboardFeedback(this.isLooping ? '🔁 Tekrar AÇIK' : '➡️ Tekrar KAPALI');
                    } else {
                        this.seekForward(shift ? 10 : 5);
                        this.showKeyboardFeedback(`⏩ ${shift ? 10 : 5} saniye ileri`);
                    }
                    break;

                // Volume up
                case 'arrowup':
                    this.volumeUp();
                    this.showKeyboardFeedback(`🔊 Ses %${Math.round(this.volume * 100)}`);
                    break;

                // Volume down
                case 'arrowdown':
                    this.volumeDown();
                    this.showKeyboardFeedback(`🔉 Ses %${Math.round(this.volume * 100)}`);
                    break;

                // Mute/Unmute
                case 'm':
                    this.toggleMute();
                    this.showKeyboardFeedback(this.isMuted ? '🔇 Sessiz' : '🔊 Ses Açık');
                    break;

                // Toggle Shuffle
                case 's':
                    this.toggleShuffle();
                    this.showKeyboardFeedback(this.isShuffling ? '🔀 Karıştır AÇIK' : '➡️ Karıştır KAPALI');
                    break;

                // Next song
                case 'n':
                    this.playNext();
                    this.showKeyboardFeedback('⏭️ Sonraki Şarkı');
                    break;

                // Previous song
                case 'p':
                    this.playPrevious();
                    this.showKeyboardFeedback('⏮️ Önceki Şarkı');
                    break;

                // Toggle Queue
                case 'q':
                    this.showQueue = !this.showQueue;
                    this.showKeyboardFeedback(this.showQueue ? '📋 Sıra Açıldı' : '📋 Sıra Kapandı');
                    break;


                // Toggle Lyrics
                case 'y':
                    this.showLyrics = !this.showLyrics;
                    this.showKeyboardFeedback(this.showLyrics ? '🎤 Şarkı Sözü Açıldı' : '🎤 Şarkı Sözü Kapandı');
                    break;
                // Toggle Favorites (if song is playing)
                case 'f':
                    if (this.currentSong) {
                        this.toggleFavorite(this.currentSong.id);
                        this.showKeyboardFeedback('❤️ Favori');
                    }
                    break;

                // Show keyboard shortcuts help
                case '?':
                    this.showKeyboardHelp = !this.showKeyboardHelp;
                    break;

                // Number keys (0-9) - Play song from queue
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    const index = parseInt(key);
                    if (this.queue[index]) {
                        this.playSongFromQueue(index);
                        this.showKeyboardFeedback(`🎵 Sıradan Çalıyor #${index + 1}`);
                    }
                    break;
            }
        },

        /**
         * Seek backward
         */
        seekBackward(seconds) {
            if (this.howl) {
                const currentTime = this.howl.seek();
                const newTime = Math.max(0, currentTime - seconds);
                this.howl.seek(newTime);
            }
        },

        /**
         * Seek forward
         */
        seekForward(seconds) {
            if (this.howl) {
                const currentTime = this.howl.seek();
                const duration = this.howl.duration();
                const newTime = Math.min(duration, currentTime + seconds);
                this.howl.seek(newTime);
            }
        },

        /**
         * Volume up (10%)
         */
        volumeUp() {
            this.volume = Math.min(1, this.volume + 0.1);
            if (this.howl) {
                this.howl.volume(this.volume);
            }
            safeStorage.setItem('player_volume', this.volume);
        },

        /**
         * Volume down (10%)
         */
        volumeDown() {
            this.volume = Math.max(0, this.volume - 0.1);
            if (this.howl) {
                this.howl.volume(this.volume);
            }
            safeStorage.setItem('player_volume', this.volume);
        },

        /**
         * Toggle mute
         */
        toggleMute() {
            this.isMuted = !this.isMuted;
            if (this.howl) {
                this.howl.mute(this.isMuted);
            }
            safeStorage.setItem('player_muted', this.isMuted);
        },

        /**
         * Toggle loop
         */
        toggleLoop() {
            this.isLooping = !this.isLooping;
            if (this.howl) {
                this.howl.loop(this.isLooping);
            }
            safeStorage.setItem('player_loop', this.isLooping);
        },

        /**
         * Toggle shuffle
         */
        toggleShuffle() {
            this.isShuffling = !this.isShuffling;
            safeStorage.setItem('player_shuffle', this.isShuffling);
        },

        /**
         * Show visual feedback for keyboard action
         */
        showKeyboardFeedback(message) {
            // Create or get feedback element
            let feedback = document.getElementById('keyboard-feedback');

            if (!feedback) {
                feedback = document.createElement('div');
                feedback.id = 'keyboard-feedback';
                feedback.className = 'keyboard-feedback';
                document.body.appendChild(feedback);
            }

            // Set message and show
            feedback.textContent = message;
            feedback.classList.add('show');

            // Hide after 1 second
            clearTimeout(this.keyboardFeedbackTimeout);
            this.keyboardFeedbackTimeout = setTimeout(() => {
                feedback.classList.remove('show');
            }, 1000);
        },

        /**
         * Get keyboard shortcuts list (for help modal)
         */
        getKeyboardShortcuts() {
            return [
                { key: 'Space / K', action: 'Play/Pause', icon: '⏯️' },
                { key: '← / J', action: 'Seek Backward (5s)', icon: '⏪' },
                { key: '→ / L', action: 'Seek Forward (5s)', icon: '⏩' },
                { key: '↑', action: 'Volume Up', icon: '🔊' },
                { key: '↓', action: 'Volume Down', icon: '🔉' },
                { key: 'M', action: 'Mute/Unmute', icon: '🔇' },
                { key: 'L', action: 'Toggle Loop', icon: '🔁' },
                { key: 'S', action: 'Toggle Shuffle', icon: '🔀' },
                { key: 'N', action: 'Next Song', icon: '⏭️' },
                { key: 'P', action: 'Previous Song', icon: '⏮️' },
                { key: 'Q', action: 'Toggle Queue', icon: '📋' },
                { key: 'Y', action: 'Toggle Lyrics', icon: '🎤' },
                { key: 'F', action: 'Toggle Favorite', icon: '❤️' },
                { key: '0-9', action: 'Play Song #N from Queue', icon: '🎵' },
                { key: '?', action: 'Show This Help', icon: '❓' },
            ];
        }
    };
}
