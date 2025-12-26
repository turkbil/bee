/**
 * 🎵 PLAY HELPERS - Global functions for playing content from genre/playlist/album cards
 *
 * These functions are called from Blade templates when user clicks play button on cards.
 * They fetch songs from API and load them into the player queue.
 */

/**
 * 🔀 Shuffle array using Fisher-Yates algorithm
 * @param {Array} array - Array to shuffle
 * @returns {Array} Shuffled array (new copy)
 */
function shuffleArray(array) {
    const shuffled = [...array]; // Create a copy
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}

/**
 * ⏱️ Debounce tracker - Prevents rapid clicking on play buttons
 */
const playDebounce = {
    lastCall: {},
    delay: 1000, // 1 second debounce

    /**
     * Check if we should allow this call
     * @param {string} key - Unique key for this action (e.g., "playAlbum-123")
     * @returns {boolean} True if allowed, false if blocked
     */
    shouldAllow(key) {
        const now = Date.now();
        const lastTime = this.lastCall[key] || 0;

        if (now - lastTime < this.delay) {
            console.log(`⏱️ Debounced: ${key} (too soon)`);
            return false;
        }

        this.lastCall[key] = now;
        return true;
    }
};

/**
 * 🎸 Play songs from a genre
 * @param {number} genreId - Genre ID
 */
async function playGenres(genreId) {
    // 🛡️ DEBOUNCE CHECK
    if (!playDebounce.shouldAllow(`playGenre-${genreId}`)) {
        return;
    }

    const player = Alpine.store('player');

    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        // 🚫 PREMIUM CHECK
        if (!player.isLoggedIn) {
            player.showToast(player.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
            return;
        }

        const isPremiumOrTrial = player.currentUser?.is_premium || player.currentUser?.is_trial;
        if (!isPremiumOrTrial) {
            player.showToast(player.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
            return;
        }

        // Fetch songs from genre
        const response = await fetch(`/api/muzibu/genres/${genreId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.genre_no_playable_songs || 'No songs found in this genre', 'warning');
            return;
        }

        // 🔀 SHUFFLE SONGS - Rastgele sıralama
        const shuffledSongs = shuffleArray(data.songs);

        // Load shuffled songs into queue and play
        player.queue = shuffledSongs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playGenres error:', error);
        player.showToast(player.frontLang?.messages?.songs_loading_failed || 'Failed to load songs', 'error');
    }
}

/**
 * 🎵 Play songs from a playlist
 * @param {number} playlistId - Playlist ID
 */
async function playPlaylist(playlistId) {
    // 🛡️ DEBOUNCE CHECK
    if (!playDebounce.shouldAllow(`playPlaylist-${playlistId}`)) {
        return;
    }

    const player = Alpine.store('player');

    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        // 🚫 PREMIUM CHECK
        if (!player.isLoggedIn) {
            player.showToast(player.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
            return;
        }

        const isPremiumOrTrial = player.currentUser?.is_premium || player.currentUser?.is_trial;
        if (!isPremiumOrTrial) {
            player.showToast(player.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
            return;
        }

        // Fetch playlist with songs
        const response = await fetch(`/api/muzibu/playlists/${playlistId}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.playlist || !data.playlist.songs || data.playlist.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.playlist_no_playable_songs || 'No songs found in this playlist', 'warning');
            return;
        }

        // 🔀 SHUFFLE SONGS - Rastgele sıralama
        const shuffledSongs = shuffleArray(data.playlist.songs);

        // Load shuffled songs into queue and play
        player.queue = shuffledSongs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playPlaylist error:', error);
        player.showToast(player.frontLang?.messages?.playlist_loading_failed || 'Failed to load playlist', 'error');
    }
}

/**
 * 💿 Play songs from an album
 * @param {number} albumId - Album ID
 */
async function playAlbum(albumId) {
    // 🛡️ DEBOUNCE CHECK
    if (!playDebounce.shouldAllow(`playAlbum-${albumId}`)) {
        return;
    }

    const player = Alpine.store('player');

    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        // 🚫 PREMIUM CHECK
        if (!player.isLoggedIn) {
            player.showToast(player.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
            return;
        }

        const isPremiumOrTrial = player.currentUser?.is_premium || player.currentUser?.is_trial;
        if (!isPremiumOrTrial) {
            player.showToast(player.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
            return;
        }

        // Fetch album with songs
        const response = await fetch(`/api/muzibu/albums/${albumId}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.album || !data.album.songs || data.album.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.album_no_playable_songs || 'No songs found in this album', 'warning');
            return;
        }

        // 🔀 SHUFFLE SONGS - Rastgele sıralama
        const shuffledSongs = shuffleArray(data.album.songs);

        // Load shuffled songs into queue and play
        player.queue = shuffledSongs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playAlbum error:', error);
        player.showToast(player.frontLang?.messages?.album_loading_failed || 'Failed to load album', 'error');
    }
}

/**
 * 📻 Play songs from a radio
 * @param {number} radioId - Radio ID
 */
async function playRadio(radioId) {
    // 🛡️ DEBOUNCE CHECK
    if (!playDebounce.shouldAllow(`playRadio-${radioId}`)) {
        return;
    }

    const player = Alpine.store('player');

    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        // 🚫 PREMIUM CHECK
        if (!player.isLoggedIn) {
            player.showToast(player.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
            return;
        }

        const isPremiumOrTrial = player.currentUser?.is_premium || player.currentUser?.is_trial;
        if (!isPremiumOrTrial) {
            player.showToast(player.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
            return;
        }

        // Fetch radio songs
        const response = await fetch(`/api/muzibu/radios/${radioId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.radio_no_playable_songs || 'No songs found in this radio', 'warning');
            return;
        }

        // 🔀 SHUFFLE SONGS
        const shuffledSongs = shuffleArray(data.songs);

        // Load shuffled songs into queue and play
        player.queue = shuffledSongs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playRadio error:', error);
        player.showToast(player.frontLang?.messages?.radio_loading_failed || 'Failed to load radio', 'error');
    }
}

/**
 * 🏢 Play songs from a sector
 * @param {number} sectorId - Sector ID
 */
async function playSector(sectorId) {
    // 🛡️ DEBOUNCE CHECK
    if (!playDebounce.shouldAllow(`playSector-${sectorId}`)) {
        return;
    }

    const player = Alpine.store('player');

    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        // 🚫 PREMIUM CHECK
        if (!player.isLoggedIn) {
            player.showToast(player.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
            return;
        }

        const isPremiumOrTrial = player.currentUser?.is_premium || player.currentUser?.is_trial;
        if (!isPremiumOrTrial) {
            player.showToast(player.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
            return;
        }

        // Fetch sector songs
        const response = await fetch(`/api/muzibu/sectors/${sectorId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.sector_no_playable_songs || 'No songs found in this sector', 'warning');
            return;
        }

        // 🔀 SHUFFLE SONGS - Rastgele sıralama
        const shuffledSongs = shuffleArray(data.songs);

        // Load shuffled songs into queue and play
        player.queue = shuffledSongs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playSector error:', error);
        player.showToast(player.frontLang?.messages?.sector_loading_failed || 'Failed to load sector', 'error');
    }
}

/**
 * 🎯 UNIVERSAL PLAY CONTENT - Context Menu için global fonksiyon
 * @param {string} type - Content type: song, album, playlist, genre, sector, radio, artist
 * @param {number} id - Content ID
 */
window.playContent = async function(type, id, options = {}) {
    console.log(`🎵 playContent called: type=${type}, id=${id}, userInitiated=${options.userInitiated ?? true}`);

    // Default: user initiated (from UI clicks)
    const isUserInitiated = options.userInitiated ?? true;

    // 🛡️ CLICK PROTECTION: Only for user-initiated plays
    if (isUserInitiated && window.MuzibuSpaRouter?.isClickProtected?.()) {
        console.warn('🛡️ playContent BLOCKED: Click protection active after SPA navigation');
        return;
    }

    // 🛡️ BACKGROUND TAB PROTECTION: Only for user-initiated plays
    // System transitions (next song, queue) should NOT be blocked
    if (isUserInitiated && document.hidden) {
        console.warn('🛡️ playContent BLOCKED: Tab is in background (user-initiated play blocked)');
        return;
    }

    const functionMap = {
        'song': async (songId) => {
            const player = Alpine.store('player');
            if (!player) return;

            // Check if song is already in queue
            const existingIndex = player.queue.findIndex(s => s.song_id === songId || s.id === songId);

            if (existingIndex !== -1) {
                // Song already in queue, just play it (context preserved!)
                await player.playSongFromQueue(existingIndex);
            } else {
                // Song not in queue, fetch it and insert at current position
                const response = await fetch(`/api/muzibu/songs/${songId}`, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) {
                    // If 404, show error and return
                    player.showToast(player.frontLang?.messages?.song_not_found || 'Şarkı bulunamadı', 'error');
                    return;
                }
                const song = await response.json();

                // API returns song directly (not wrapped in data.song)
                if (song && song.song_id) {
                    // Insert song after current position
                    const insertIndex = player.queueIndex + 1;
                    player.queue.splice(insertIndex, 0, song);
                    // Play the newly inserted song
                    await player.playSongFromQueue(insertIndex);
                } else {
                    player.showToast(player.frontLang?.messages?.song_not_found || 'Şarkı bulunamadı', 'error');
                }
            }
        },
        'album': playAlbum,
        'playlist': playPlaylist,
        'genre': playGenres,
        'sector': playSector,
        'radio': playRadio,
        'artist': async (artistId) => {
            // Artist play implementation
            console.warn('Artist play not implemented yet');
        }
    };

    const playFunction = functionMap[type];
    if (playFunction) {
        try {
            await playFunction(id);
        } catch (error) {
            console.error(`playContent error (${type}):`, error);
        }
    } else {
        console.error(`Unknown content type: ${type}`);
    }
};

/**
 * ➕ UNIVERSAL ADD TO QUEUE - Context Menu için global fonksiyon
 * @param {string} type - Content type: song, album, playlist, genre, sector, radio
 * @param {number} id - Content ID
 */
window.addContentToQueue = async function(type, id) {
    console.log(`➕ addContentToQueue called: type=${type}, id=${id}`);

    const player = Alpine.store('player');
    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        let songs = [];

        // Fetch songs based on type
        switch(type) {
            case 'song':
                const songResponse = await fetch(`/api/muzibu/songs/${id}`, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                if (songResponse.ok) {
                    const song = await songResponse.json();
                    // API returns song directly (not wrapped)
                    songs = song && song.song_id ? [song] : [];
                }
                break;

            case 'album':
                const albumResponse = await fetch(`/api/muzibu/albums/${id}`);
                if (albumResponse.ok) {
                    const albumData = await albumResponse.json();
                    songs = albumData.album?.songs || [];
                }
                break;

            case 'playlist':
                const playlistResponse = await fetch(`/api/muzibu/playlists/${id}`);
                if (playlistResponse.ok) {
                    const playlistData = await playlistResponse.json();
                    songs = playlistData.playlist?.songs || [];
                }
                break;

            case 'genre':
                const genreResponse = await fetch(`/api/muzibu/genres/${id}/songs`);
                if (genreResponse.ok) {
                    const genreData = await genreResponse.json();
                    songs = genreData.songs || [];
                }
                break;

            case 'sector':
                const sectorResponse = await fetch(`/api/muzibu/sectors/${id}/songs`);
                if (sectorResponse.ok) {
                    const sectorData = await sectorResponse.json();
                    songs = sectorData.songs || [];
                }
                break;

            case 'radio':
                const radioResponse = await fetch(`/api/muzibu/radios/${id}/songs`);
                if (radioResponse.ok) {
                    const radioData = await radioResponse.json();
                    songs = radioData.songs || [];
                }
                break;

            default:
                console.error(`Unknown type for addToQueue: ${type}`);
                return;
        }

        if (songs.length > 0) {
            // 1. Çalan şarkının ID'sini kaydet (queue değişince index kayacak)
            const currentSongId = player.queue[player.queueIndex]?.song_id;

            // 2. Eklenecek şarkıların ID'lerini topla
            const songIdsToAdd = songs.map(s => s.song_id);

            // 3. Queue'dan duplicate şarkıları kaldır
            const originalQueueLength = player.queue.length;
            player.queue = player.queue.filter(queueSong => !songIdsToAdd.includes(queueSong.song_id));
            const removedCount = originalQueueLength - player.queue.length;

            // 4. Çalan şarkının yeni index'ini bul ve güncelle
            if (currentSongId) {
                const newCurrentIndex = player.queue.findIndex(s => s.song_id === currentSongId);
                if (newCurrentIndex !== -1) {
                    player.queueIndex = newCurrentIndex;
                }
            }

            // 5. Şarkıları sıraya ekle (sona)
            player.queue = [...player.queue, ...songs];

            // Toast mesajı
            if (removedCount > 0) {
                const msg = window.trans(window.muzibuLang.queue.added_with_duplicates, { count: songs.length, removed: removedCount });
                player.showToast(msg, 'success');
            } else {
                const msg = window.trans(window.muzibuLang.queue.added_to_queue, { count: songs.length });
                player.showToast(msg, 'success');
            }
        } else {
            player.showToast(window.muzibuLang.queue.song_not_found, 'warning');
        }

    } catch (error) {
        console.error('addToQueue error:', error);
        player.showToast(window.muzibuLang.queue.queue_error, 'error');
    }
};

/**
 * ➕ Content'i sırada bir sonrakine ekle (YouTube Music "Play Next" benzeri)
 * Çalan şarkının hemen ardına ekler
 */
window.addContentToQueueNext = async function(type, id) {
    console.log(`➕ addContentToQueueNext called: type=${type}, id=${id}`);

    const player = Alpine.store('player');
    if (!player) {
        console.error('Player store not found');
        return;
    }

    try {
        let songs = [];

        // Fetch songs based on type (aynı logic)
        switch(type) {
            case 'song':
                const songResponse = await fetch(`/api/muzibu/songs/${id}`, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                if (songResponse.ok) {
                    const song = await songResponse.json();
                    songs = song && song.song_id ? [song] : [];
                }
                break;

            case 'album':
                const albumResponse = await fetch(`/api/muzibu/albums/${id}`);
                if (albumResponse.ok) {
                    const albumData = await albumResponse.json();
                    songs = albumData.album?.songs || [];
                }
                break;

            case 'playlist':
                const playlistResponse = await fetch(`/api/muzibu/playlists/${id}`);
                if (playlistResponse.ok) {
                    const playlistData = await playlistResponse.json();
                    songs = playlistData.playlist?.songs || [];
                }
                break;

            case 'genre':
                const genreResponse = await fetch(`/api/muzibu/genres/${id}/songs`);
                if (genreResponse.ok) {
                    const genreData = await genreResponse.json();
                    songs = genreData.songs || [];
                }
                break;

            case 'sector':
                const sectorResponse = await fetch(`/api/muzibu/sectors/${id}/songs`);
                if (sectorResponse.ok) {
                    const sectorData = await sectorResponse.json();
                    songs = sectorData.songs || [];
                }
                break;

            case 'radio':
                const radioResponse = await fetch(`/api/muzibu/radios/${id}/songs`);
                if (radioResponse.ok) {
                    const radioData = await radioResponse.json();
                    songs = radioData.songs || [];
                }
                break;

            default:
                console.error(`Unknown type for addToQueueNext: ${type}`);
                return;
        }

        if (songs.length > 0) {
            // 1. Çalan şarkının ID'sini kaydet (queue değişince index kayacak)
            const currentSongId = player.queue[player.queueIndex]?.song_id;

            // 2. Eklenecek şarkıların ID'lerini topla
            const songIdsToAdd = songs.map(s => s.song_id);

            // 3. Queue'dan duplicate şarkıları kaldır
            const originalQueueLength = player.queue.length;
            player.queue = player.queue.filter(queueSong => !songIdsToAdd.includes(queueSong.song_id));
            const removedCount = originalQueueLength - player.queue.length;

            // 4. Çalan şarkının yeni index'ini bul
            const newCurrentIndex = player.queue.findIndex(s => s.song_id === currentSongId);
            if (newCurrentIndex !== -1) {
                player.queueIndex = newCurrentIndex;
            }

            // 5. Çalan şarkının hemen ardına ekle
            const insertPosition = (player.queueIndex || 0) + 1;
            player.queue.splice(insertPosition, 0, ...songs);

            // Toast mesajı
            if (removedCount > 0) {
                const msg = window.trans(window.muzibuLang.queue.added_next_with_duplicates, { count: songs.length, removed: removedCount });
                player.showToast(msg, 'success');
            } else {
                const msg = window.trans(window.muzibuLang.queue.added_to_queue_next, { count: songs.length });
                player.showToast(msg, 'success');
            }
        } else {
            player.showToast(window.muzibuLang.queue.song_not_found, 'warning');
        }

    } catch (error) {
        console.error('addToQueueNext error:', error);
        player.showToast(window.muzibuLang.queue.queue_error, 'error');
    }
};

// 🎯 GLOBAL EXPORTS - Context menu action handlers için
window.playAlbum = playAlbum;
window.playPlaylist = playPlaylist;
window.playGenres = playGenres;
window.playSector = playSector;
window.playRadio = playRadio;
