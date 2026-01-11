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

        const isPremium = player.currentUser?.is_premium;
        if (!isPremium) {
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

        // 🚫 DUPLICATE CONTROL - Remove already played songs from queue
        const uniqueSongIds = new Set();
        const uniqueSongs = shuffledSongs.filter(song => {
            const songId = song.song_id || song.id;
            if (uniqueSongIds.has(songId)) {
                return false; // Skip duplicate
            }
            uniqueSongIds.add(songId);
            return true;
        });

        // Load unique shuffled songs into queue and play
        player.queue = uniqueSongs;
        player.queueIndex = 0;

        // 🎸 Set play context to 'genre'
        const muzibuStore = Alpine.store('muzibu');
        if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
            const genreTitle = data.genre?.title?.tr || data.genre?.title?.en || data.genre?.title || 'Genre';
            muzibuStore.setPlayContext({
                type: 'genre',
                id: genreId,
                name: genreTitle,
                offset: 0,
                source: 'genre_click'
            });
        }

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

        const isPremium = player.currentUser?.is_premium;
        if (!isPremium) {
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

        // 🚫 DUPLICATE CONTROL - Remove already played songs from queue
        // Get unique songs (filter by song_id)
        const uniqueSongIds = new Set();
        const uniqueSongs = shuffledSongs.filter(song => {
            const songId = song.song_id || song.id;
            if (uniqueSongIds.has(songId)) {
                return false; // Skip duplicate
            }
            uniqueSongIds.add(songId);
            return true;
        });

        // 🎯 MINIMUM 15 SONGS - Genre'den doldur
        const MIN_QUEUE_SIZE = 15;
        let finalQueue = [...uniqueSongs];

        if (finalQueue.length < MIN_QUEUE_SIZE) {
            // Son şarkının genre_id'sini al
            const lastSong = finalQueue[finalQueue.length - 1];
            const genreId = lastSong?.genre_id;

            if (genreId) {
                console.log(`[PlayPlaylist] Queue has ${finalQueue.length} songs, fetching from genre ${genreId} to reach ${MIN_QUEUE_SIZE}`);

                try {
                    // Genre API'den şarkı çek
                    const genreResponse = await fetch(`/api/muzibu/genres/${genreId}/songs`);
                    if (genreResponse.ok) {
                        const genreData = await genreResponse.json();
                        const genreSongs = genreData.songs || [];

                        // Shuffle genre songs
                        const shuffledGenreSongs = shuffleArray(genreSongs);

                        // Genre şarkılarından sadece queue'da olmayanları ekle
                        for (const song of shuffledGenreSongs) {
                            if (finalQueue.length >= MIN_QUEUE_SIZE) break;

                            const songId = song.song_id || song.id;
                            if (!uniqueSongIds.has(songId)) {
                                uniqueSongIds.add(songId);
                                finalQueue.push(song);
                                console.log(`[PlayPlaylist] Added song ${songId} from genre, queue size: ${finalQueue.length}`);
                            }
                        }
                    }
                } catch (genreError) {
                    console.warn('[PlayPlaylist] Genre fill failed:', genreError);
                }
            }
        }


        // Load unique shuffled songs into queue and play
        player.queue = finalQueue;
        player.queueIndex = 0;

        // 🎵 Set play context to 'playlist'
        const muzibuStore = Alpine.store('muzibu');
        if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
            const playlistTitle = data.playlist?.title?.tr || data.playlist?.title?.en || data.playlist?.title || 'Playlist';
            muzibuStore.setPlayContext({
                type: 'playlist',
                id: playlistId,
                name: playlistTitle,
                offset: 0,
                source: 'playlist_click'
            });
        }

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

        const isPremium = player.currentUser?.is_premium;
        if (!isPremium) {
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

        // 🚫 DUPLICATE CONTROL - Remove already played songs from queue
        const uniqueSongIds = new Set();
        const uniqueSongs = shuffledSongs.filter(song => {
            const songId = song.song_id || song.id;
            if (uniqueSongIds.has(songId)) {
                return false; // Skip duplicate
            }
            uniqueSongIds.add(songId);
            return true;
        });

        // 🎯 MINIMUM 15 SONGS - Genre'den doldur
        const MIN_QUEUE_SIZE = 15;
        let finalQueue = [...uniqueSongs];

        if (finalQueue.length < MIN_QUEUE_SIZE) {
            const lastSong = finalQueue[finalQueue.length - 1];
            const genreId = lastSong?.genre_id;

            if (genreId) {
                console.log(`[PlayAlbum] Queue has ${finalQueue.length} songs, fetching from genre ${genreId}`);

                try {
                    const genreResponse = await fetch(`/api/muzibu/genres/${genreId}/songs`);
                    if (genreResponse.ok) {
                        const genreData = await genreResponse.json();
                        const genreSongs = genreData.songs || [];
                        const shuffledGenreSongs = shuffleArray(genreSongs);

                        for (const song of shuffledGenreSongs) {
                            if (finalQueue.length >= MIN_QUEUE_SIZE) break;
                            const songId = song.song_id || song.id;
                            if (!uniqueSongIds.has(songId)) {
                                uniqueSongIds.add(songId);
                                finalQueue.push(song);
                            }
                        }
                    }
                } catch (genreError) {
                    console.warn('[PlayAlbum] Genre fill failed:', genreError);
                }
            }
        }


        // Load unique shuffled songs into queue and play
        player.queue = finalQueue;
        player.queueIndex = 0;

        // 💿 Set play context to 'album'
        const muzibuStore = Alpine.store('muzibu');
        if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
            const albumTitle = data.album?.title?.tr || data.album?.title?.en || data.album?.title || 'Album';
            muzibuStore.setPlayContext({
                type: 'album',
                id: albumId,
                name: albumTitle,
                offset: 0,
                source: 'album_click'
            });
        }

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

        const isPremium = player.currentUser?.is_premium;
        if (!isPremium) {
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

        // 🚫 DUPLICATE CONTROL
        const uniqueSongIds = new Set();
        const uniqueSongs = shuffledSongs.filter(song => {
            const songId = song.song_id || song.id;
            if (uniqueSongIds.has(songId)) return false;
            uniqueSongIds.add(songId);
            return true;
        });

        // 🎯 MINIMUM 15 SONGS - Genre'den doldur
        const MIN_QUEUE_SIZE = 15;
        let finalQueue = [...uniqueSongs];

        if (finalQueue.length < MIN_QUEUE_SIZE) {
            const lastSong = finalQueue[finalQueue.length - 1];
            const genreId = lastSong?.genre_id;

            if (genreId) {
                console.log(`[PlayRadio] Queue has ${finalQueue.length} songs, fetching from genre ${genreId}`);

                try {
                    const genreResponse = await fetch(`/api/muzibu/genres/${genreId}/songs`);
                    if (genreResponse.ok) {
                        const genreData = await genreResponse.json();
                        const genreSongs = genreData.songs || [];
                        const shuffledGenreSongs = shuffleArray(genreSongs);

                        for (const song of shuffledGenreSongs) {
                            if (finalQueue.length >= MIN_QUEUE_SIZE) break;
                            const songId = song.song_id || song.id;
                            if (!uniqueSongIds.has(songId)) {
                                uniqueSongIds.add(songId);
                                finalQueue.push(song);
                            }
                        }
                    }
                } catch (genreError) {
                    console.warn('[PlayRadio] Genre fill failed:', genreError);
                }
            }
        }


        // Load shuffled songs into queue and play
        player.queue = finalQueue;
        player.queueIndex = 0;

        // 📻 Set play context to 'radio' (queue butonunu gizlemek için)
        const muzibuStore = Alpine.store('muzibu');
        if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
            const radioTitle = data.radio?.title?.tr || data.radio?.title?.en || data.radio?.title || 'Radio';
            muzibuStore.setPlayContext({
                type: 'radio',
                id: radioId,
                name: radioTitle,
                offset: 0,
                source: 'radio_click'
            });
        }

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

        const isPremium = player.currentUser?.is_premium;
        if (!isPremium) {
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

        // 🚫 DUPLICATE CONTROL - Remove already played songs from queue
        const uniqueSongIds = new Set();
        const uniqueSongs = shuffledSongs.filter(song => {
            const songId = song.song_id || song.id;
            if (uniqueSongIds.has(songId)) {
                return false; // Skip duplicate
            }
            uniqueSongIds.add(songId);
            return true;
        });

        // 🎯 MINIMUM 15 SONGS - Genre'den doldur
        const MIN_QUEUE_SIZE = 15;
        let finalQueue = [...uniqueSongs];

        if (finalQueue.length < MIN_QUEUE_SIZE) {
            const lastSong = finalQueue[finalQueue.length - 1];
            const genreId = lastSong?.genre_id;

            if (genreId) {
                console.log(`[PlaySector] Queue has ${finalQueue.length} songs, fetching from genre ${genreId}`);

                try {
                    const genreResponse = await fetch(`/api/muzibu/genres/${genreId}/songs`);
                    if (genreResponse.ok) {
                        const genreData = await genreResponse.json();
                        const genreSongs = genreData.songs || [];
                        const shuffledGenreSongs = shuffleArray(genreSongs);

                        for (const song of shuffledGenreSongs) {
                            if (finalQueue.length >= MIN_QUEUE_SIZE) break;
                            const songId = song.song_id || song.id;
                            if (!uniqueSongIds.has(songId)) {
                                uniqueSongIds.add(songId);
                                finalQueue.push(song);
                            }
                        }
                    }
                } catch (genreError) {
                    console.warn('[PlaySector] Genre fill failed:', genreError);
                }
            }
        }


        // Load unique shuffled songs into queue and play
        player.queue = finalQueue;
        player.queueIndex = 0;

        // 🏢 Set play context to 'sector'
        const muzibuStore = Alpine.store('muzibu');
        if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
            const sectorTitle = data.sector?.title?.tr || data.sector?.title?.en || data.sector?.title || 'Sector';
            muzibuStore.setPlayContext({
                type: 'sector',
                id: sectorId,
                name: sectorTitle,
                offset: 0,
                source: 'sector_click'
            });
        }

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
            const muzibuStore = Alpine.store('muzibu');
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

                    // 🎵 Context güncelle: Şarkının albümüne veya genre'sine geç
                    if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
                        if (song.album_id) {
                            muzibuStore.setPlayContext({
                                type: 'album',
                                id: song.album_id,
                                name: song.album_title || 'Albüm',
                                offset: 0,
                                source: 'song_click'
                            });
                        } else if (song.genre_id) {
                            muzibuStore.setPlayContext({
                                type: 'genre',
                                id: song.genre_id,
                                name: song.genre_title || 'Tür',
                                offset: 0,
                                source: 'song_click'
                            });
                        }
                    }

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
            // 🎵 Artist play: Sanatçının şarkılarını shuffle çal
            const player = Alpine.store('player');
            const muzibuStore = Alpine.store('muzibu');
            if (!player) return;

            try {
                // API'den sanatçı ve şarkılarını al
                const response = await fetch(`/api/muzibu/artists/${artistId}/songs?limit=30`, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    player.showToast('Sanatçı şarkıları yüklenemedi', 'error');
                    return;
                }

                const data = await response.json();
                const songs = data.songs || [];

                if (songs.length === 0) {
                    player.showToast('Bu sanatçının şarkısı bulunamadı', 'warning');
                    return;
                }

                // 🎵 Context ayarla
                if (muzibuStore && typeof muzibuStore.setPlayContext === 'function') {
                    muzibuStore.setPlayContext({
                        type: 'artist',
                        id: artistId,
                        name: data.artist?.title || 'Muzibu',
                        offset: 0,
                        source: 'artist_click'
                    });
                }

                // 🚫 DUPLICATE CONTROL - Remove already played songs from queue
                const uniqueSongIds = new Set();
                const uniqueSongs = songs.filter(song => {
                    const songId = song.song_id || song.id;
                    if (uniqueSongIds.has(songId)) {
                        return false; // Skip duplicate
                    }
                    uniqueSongIds.add(songId);
                    return true;
                });

                // Queue'yu yeni şarkılarla değiştir ve çalmaya başla
                player.queue = uniqueSongs;
                player.queueIndex = 0;
                await player.playSongFromQueue(0);

            } catch (error) {
                console.error('Artist play error:', error);
                player.showToast('Sanatçı çalınamadı', 'error');
            }
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
