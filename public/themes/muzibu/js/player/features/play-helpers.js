/**
 * 🎵 PLAY HELPERS - Global functions for playing content from genre/playlist/album cards
 *
 * These functions are called from Blade templates when user clicks play button on cards.
 * They fetch songs from API and load them into the player queue.
 */

/**
 * 🎸 Play songs from a genre
 * @param {number} genreId - Genre ID
 */
async function playGenres(genreId) {
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

        // Show loading
        player.isLoading = true;

        // Fetch songs from genre
        const response = await fetch(`/api/muzibu/genres/${genreId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.genre_no_playable_songs || 'No songs found in this genre', 'warning');
            player.isLoading = false;
            return;
        }

        // Load songs into queue and play
        player.queue = data.songs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playGenres error:', error);
        player.showToast(player.frontLang?.messages?.songs_loading_failed || 'Failed to load songs', 'error');
    } finally {
        player.isLoading = false;
    }
}

/**
 * 🎵 Play songs from a playlist
 * @param {number} playlistId - Playlist ID
 */
async function playPlaylist(playlistId) {
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

        // Show loading
        player.isLoading = true;

        // Fetch playlist with songs
        const response = await fetch(`/api/muzibu/playlists/${playlistId}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.playlist || !data.playlist.songs || data.playlist.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.playlist_no_playable_songs || 'No songs found in this playlist', 'warning');
            player.isLoading = false;
            return;
        }

        // Load songs into queue and play
        player.queue = data.playlist.songs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playPlaylist error:', error);
        player.showToast(player.frontLang?.messages?.playlist_loading_failed || 'Failed to load playlist', 'error');
    } finally {
        player.isLoading = false;
    }
}

/**
 * 💿 Play songs from an album
 * @param {number} albumId - Album ID
 */
async function playAlbum(albumId) {
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

        // Show loading
        player.isLoading = true;

        // Fetch album with songs
        const response = await fetch(`/api/muzibu/albums/${albumId}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.album || !data.album.songs || data.album.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.album_no_playable_songs || 'No songs found in this album', 'warning');
            player.isLoading = false;
            return;
        }

        // Load songs into queue and play
        player.queue = data.album.songs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playAlbum error:', error);
        player.showToast(player.frontLang?.messages?.album_loading_failed || 'Failed to load album', 'error');
    } finally {
        player.isLoading = false;
    }
}

/**
 * 📻 Play songs from a radio
 * @param {number} radioId - Radio ID
 */
async function playRadio(radioId) {
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

        // Show loading
        player.isLoading = true;

        // Fetch radio songs
        const response = await fetch(`/api/muzibu/radios/${radioId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.radio_no_playable_songs || 'No songs found in this radio', 'warning');
            player.isLoading = false;
            return;
        }

        // Load songs into queue and play
        player.queue = data.songs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playRadio error:', error);
        player.showToast(player.frontLang?.messages?.radio_loading_failed || 'Failed to load radio', 'error');
    } finally {
        player.isLoading = false;
    }
}

/**
 * 🏢 Play songs from a sector
 * @param {number} sectorId - Sector ID
 */
async function playSector(sectorId) {
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

        // Show loading
        player.isLoading = true;

        // Fetch sector songs
        const response = await fetch(`/api/muzibu/sectors/${sectorId}/songs`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data.songs || data.songs.length === 0) {
            player.showToast(player.frontLang?.messages?.sector_no_playable_songs || 'No songs found in this sector', 'warning');
            player.isLoading = false;
            return;
        }

        // Load songs into queue and play
        player.queue = data.songs;
        player.queueIndex = 0;

        // Play first song
        await player.playSongFromQueue(0);

    } catch (error) {
        console.error('playSector error:', error);
        player.showToast(player.frontLang?.messages?.sector_loading_failed || 'Failed to load sector', 'error');
    } finally {
        player.isLoading = false;
    }
}
