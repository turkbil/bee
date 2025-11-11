@include('muzibu::admin.helper')
@extends('admin.layout')

@push('title')
<span id="playlist-title-dynamic">{{ __('admin.loading') }}...</span>
@endpush

@section('content')
<div class="container-xl">
    <!-- Dual-List Container -->
    <div class="page-body">
        <div class="row g-4">
            <!-- SOL KOLON: TÜM ŞARKILAR -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-music me-2 text-muted"></i>
                            {{ __('muzibu::admin.playlist.all_songs') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- Arama -->
                        <div class="p-3 border-bottom">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="search-available" class="form-control"
                                       placeholder="{{ __('muzibu::admin.playlist.search_songs_placeholder') }}">
                            </div>
                        </div>

                        <!-- Şarkı Listesi -->
                        <div id="available-songs-container" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">{{ __('admin.loading') }}...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-muted">
                        <small>
                            <span id="available-count">0</span> {{ __('muzibu::admin.playlist.songs_found') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- SAĞ KOLON: PLAYLIST ŞARKILARI -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-ol me-2 text-primary"></i>
                            {{ __('muzibu::admin.playlist.playlist_songs') }}
                            <span class="badge bg-primary ms-2" id="playlist-count">0</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- Playlist Şarkı Listesi (Sortable) -->
                        <div id="playlist-songs-container" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">{{ __('admin.loading') }}...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-muted">
                        <small>
                            <i class="fas fa-clock me-1"></i>
                            {{ __('muzibu::admin.playlist.total_duration') }}:
                            <strong id="total-duration">00:00</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
$(document).ready(function() {
    const playlistId = {{ $playlistId }};
    let availableSongs = [];
    let playlistSongs = [];
    let sortable = null;

    // İlk yükleme
    loadPlaylistInfo();
    loadAvailableSongs();
    loadPlaylistSongs();

    // Playlist bilgisi
    function loadPlaylistInfo() {
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/info`,
            success: function(data) {
                $('#playlist-title-dynamic').text(data.title);
            },
            error: function() {
                $('#playlist-title-dynamic').text('{{ __("admin.unknown") }}');
            }
        });
    }

    // Kullanılabilir şarkıları yükle
    function loadAvailableSongs(search = '') {
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/available`,
            data: { search: search },
            beforeSend: function() {
                if (search === '') {
                    $('#available-songs-container').html(`
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary"></div>
                            <div class="mt-2 text-muted">{{ __('admin.loading') }}...</div>
                        </div>
                    `);
                }
            },
            success: function(data) {
                availableSongs = data;
                renderAvailableSongs();
            },
            error: function() {
                showError('{{ __("admin.error_loading_data") }}');
            }
        });
    }

    // Playlist şarkılarını yükle
    function loadPlaylistSongs() {
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/selected`,
            beforeSend: function() {
                $('#playlist-songs-container').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2 text-muted">{{ __('admin.loading') }}...</div>
                    </div>
                `);
            },
            success: function(data) {
                playlistSongs = data.songs;
                renderPlaylistSongs();
                updateTotalDuration(data.total_duration);
            },
            error: function() {
                showError('{{ __("admin.error_loading_data") }}');
            }
        });
    }

    // Kullanılabilir şarkıları render et
    function renderAvailableSongs() {
        const search = $('#search-available').val().toLowerCase();
        const filtered = availableSongs.filter(song =>
            song.title.toLowerCase().includes(search) ||
            (song.artist && song.artist.toLowerCase().includes(search))
        );

        $('#available-count').text(filtered.length);

        if (filtered.length === 0) {
            $('#available-songs-container').html(`
                <div class="empty py-5">
                    <div class="empty-icon">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_found') }}</p>
                </div>
            `);
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        filtered.forEach(song => {
            html += `
                <div class="list-group-item list-group-item-action" data-song-id="${song.id}">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-secondary-lt"><i class="fas fa-music"></i></div>
                                <div>
                                    <strong class="d-block">${song.title}</strong>
                                    <small class="text-muted">${song.artist || '{{ __("admin.unknown") }}'} ${song.duration ? '· ' + song.duration : ''}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-success add-song-btn" data-song-id="${song.id}">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('admin.add') }}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $('#available-songs-container').html(html);
    }

    // Playlist şarkılarını render et
    function renderPlaylistSongs() {
        $('#playlist-count').text(playlistSongs.length);

        if (playlistSongs.length === 0) {
            $('#playlist-songs-container').html(`
                <div class="empty py-5">
                    <div class="empty-icon">
                        <i class="fas fa-music-slash fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_in_playlist') }}</p>
                    <p class="empty-subtitle text-muted">{{ __('muzibu::admin.playlist.add_songs_from_left') }}</p>
                </div>
            `);
            return;
        }

        let html = '<div id="sortable-playlist" class="list-group list-group-flush">';
        playlistSongs.forEach((song, index) => {
            html += `
                <div class="list-group-item sortable-item" data-song-id="${song.id}" data-position="${index}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: grab; font-size: 1.2rem;"></i>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-secondary">${index + 1}</span>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-primary-lt"><i class="fas fa-music"></i></div>
                                <div>
                                    <strong class="d-block">${song.title}</strong>
                                    <small class="text-muted">${song.artist || '{{ __("admin.unknown") }}'} ${song.duration ? '· ' + song.duration : ''}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-danger remove-song-btn" data-song-id="${song.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $('#playlist-songs-container').html(html);

        // Sortable.js init
        initSortable();
    }

    // Sortable.js başlat
    function initSortable() {
        if (sortable) {
            sortable.destroy();
        }

        const sortableEl = document.getElementById('sortable-playlist');
        if (!sortableEl) return;

        sortable = Sortable.create(sortableEl, {
            handle: '.sortable-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                // Yeni sıralamayı topla
                const items = sortableEl.querySelectorAll('.sortable-item');
                const newOrder = {};

                items.forEach((item, index) => {
                    const songId = parseInt(item.dataset.songId);
                    newOrder[songId] = index;
                });

                // AJAX ile kaydet
                saveSongOrder(newOrder);
            }
        });
    }

    // HTML helper fonksiyonları
    function createPlaylistSongHtml(song, index) {
        return `
            <div class="list-group-item sortable-item" data-song-id="${song.id}" data-position="${index}">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: grab; font-size: 1.2rem;"></i>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-secondary">${index + 1}</span>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 bg-primary-lt"><i class="fas fa-music"></i></div>
                            <div>
                                <strong class="d-block">${song.title}</strong>
                                <small class="text-muted">${song.artist || '{{ __("admin.unknown") }}'} ${song.duration ? '· ' + song.duration : ''}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-danger remove-song-btn" data-song-id="${song.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function createAvailableSongHtml(song) {
        return `
            <div class="list-group-item list-group-item-action" data-song-id="${song.id}">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 bg-secondary-lt"><i class="fas fa-music"></i></div>
                            <div>
                                <strong class="d-block">${song.title}</strong>
                                <small class="text-muted">${song.artist || '{{ __("admin.unknown") }}'} ${song.duration ? '· ' + song.duration : ''}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-success add-song-btn" data-song-id="${song.id}">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('admin.add') }}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Şarkı ekleme - anında geçiş (render yok!)
    $(document).on('click', '.add-song-btn', function() {
        const btn = $(this);
        const songId = btn.data('song-id');
        const songItem = btn.closest('[data-song-id]');

        // AJAX çağrısı (arka planda)
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/add`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { song_id: songId },
            success: function(response) {
                // Şarkıyı sol listeden bul
                const song = availableSongs.find(s => s.id === songId);
                if (song) {
                    // Memory güncelle
                    playlistSongs.push(song);
                    availableSongs = availableSongs.filter(s => s.id !== songId);

                    // Şarkının HTML'ini oluştur (sağ liste için)
                    const newIndex = playlistSongs.length - 1;
                    const playlistHtml = createPlaylistSongHtml(song, newIndex);

                    // Sol listeden anında kaldır
                    songItem.fadeOut(100, function() {
                        $(this).remove();

                        // Sağ listeye ekle
                        const playlistContainer = $('#sortable-playlist');
                        if (playlistContainer.length) {
                            $(playlistHtml).hide().appendTo(playlistContainer).fadeIn(100);
                            // Sortable'ı yeniden init et
                            initSortable();
                        } else {
                            // Liste boşsa, container'ı yeniden oluştur
                            $('#playlist-songs-container').html('<div id="sortable-playlist" class="list-group list-group-flush">' + playlistHtml + '</div>');
                            initSortable();
                        }

                        // Counter'ları güncelle
                        $('#available-count').text(availableSongs.length);
                        $('#playlist-count').text(playlistSongs.length);
                        updateTotalDurationFromMemory();
                    });
                }
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || '{{ __("admin.error_occurred") }}');
            }
        });
    });

    // Şarkı çıkarma - anında geçiş (render yok!)
    $(document).on('click', '.remove-song-btn', function() {
        const btn = $(this);
        const songId = btn.data('song-id');
        const songItem = btn.closest('[data-song-id]');

        // AJAX çağrısı (arka planda)
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/remove`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { song_id: songId },
            success: function(response) {
                // Şarkıyı sağ listeden bul
                const song = playlistSongs.find(s => s.id === songId);
                if (song) {
                    // Memory güncelle
                    availableSongs.push(song);
                    playlistSongs = playlistSongs.filter(s => s.id !== songId);

                    // Şarkının HTML'ini oluştur (sol liste için)
                    const availableHtml = createAvailableSongHtml(song);

                    // Sağ listeden anında kaldır
                    songItem.fadeOut(100, function() {
                        $(this).remove();

                        // Sol listeye ekle
                        const availableContainer = $('#available-songs-container .list-group');
                        if (availableContainer.length) {
                            // Mevcut listeye append et
                            $(availableHtml).hide().appendTo(availableContainer).fadeIn(100);
                        } else {
                            // Liste boşsa (search sonucu), yeniden oluştur
                            renderAvailableSongs();
                        }

                        // Sağ listedeki position badge'leri düzelt
                        $('#sortable-playlist .sortable-item').each(function(index) {
                            $(this).find('.badge').text(index + 1);
                            $(this).attr('data-position', index);
                        });

                        // Counter'ları güncelle
                        $('#available-count').text(availableSongs.length);
                        $('#playlist-count').text(playlistSongs.length);
                        updateTotalDurationFromMemory();

                        // Playlist boşaldıysa empty state göster
                        if (playlistSongs.length === 0) {
                            $('#playlist-songs-container').html(`
                                <div class="empty py-5">
                                    <div class="empty-icon">
                                        <i class="fas fa-music-slash fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_in_playlist') }}</p>
                                    <p class="empty-subtitle text-muted">{{ __('muzibu::admin.playlist.add_songs_from_left') }}</p>
                                </div>
                            `);
                        }
                    });
                }
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || '{{ __("admin.error_occurred") }}');
            }
        });
    });

    // Sıralama kaydetme
    function saveSongOrder(newOrder) {
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/reorder`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { order: newOrder },
            success: function(response) {
                // Sessiz başarı - notification yok
                // Position badge'leri güncelle
                $('.sortable-item').each(function(index) {
                    $(this).find('.badge').text(index + 1);
                });
            },
            error: function() {
                showError('{{ __("admin.error_occurred") }}');
                loadPlaylistSongs(); // Hata durumunda yeniden yükle
            }
        });
    }

    // Toplam süreyi güncelle (server'dan gelen saniye)
    function updateTotalDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        let duration = '';
        if (hours > 0) {
            duration = `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            duration = `${minutes}:${secs.toString().padStart(2, '0')}`;
        }

        $('#total-duration').text(duration);
    }

    // Toplam süreyi memory'den hesapla (add/remove sonrası)
    function updateTotalDurationFromMemory() {
        let totalSeconds = 0;
        playlistSongs.forEach(song => {
            if (song.duration) {
                // Duration format: "03:18" veya "1:23:45"
                const parts = song.duration.split(':');
                if (parts.length === 2) {
                    totalSeconds += parseInt(parts[0]) * 60 + parseInt(parts[1]);
                } else if (parts.length === 3) {
                    totalSeconds += parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
                }
            }
        });
        updateTotalDuration(totalSeconds);
    }

    // Arama - debounce
    let searchTimeout;
    $('#search-available').on('keyup', function() {
        clearTimeout(searchTimeout);
        const search = $(this).val();

        searchTimeout = setTimeout(() => {
            if (search.length >= 2 || search.length === 0) {
                loadAvailableSongs(search);
            } else {
                renderAvailableSongs();
            }
        }, 300);
    });

    // Notification fonksiyonları
    function showSuccess(message) {
        if (typeof notyf !== 'undefined') {
            notyf.success(message);
        } else {
            alert(message);
        }
    }

    function showError(message) {
        if (typeof notyf !== 'undefined') {
            notyf.error(message);
        } else {
            alert(message);
        }
    }
});
</script>
@endpush

@push('styles')
<style>
/* Sortable styles */
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e7f3ff;
}

.sortable-handle:hover {
    color: #206bc4 !important;
}

.sortable-handle:active {
    cursor: grabbing !important;
}

/* Loading animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.list-group-item {
    animation: fadeIn 0.3s;
}
</style>
@endpush
