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
                                <input type="text" id="search-available" class="form-control" style="padding-right: 2.5rem;"
                                       placeholder="{{ __('muzibu::admin.playlist.search_songs_placeholder') }}">
                                <span class="input-icon-addon" style="right: 0; cursor: pointer; z-index: 10; pointer-events: auto;" id="clear-available-search">
                                    <i class="fas fa-times text-muted" style="display: none;"></i>
                                </span>
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
                        <!-- Arama (Playlist) -->
                        <div class="p-3 border-bottom">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="search-playlist" class="form-control" style="padding-right: 2.5rem;"
                                       placeholder="{{ __('muzibu::admin.playlist.search_in_playlist') }}">
                                <span class="input-icon-addon" style="right: 0; cursor: pointer; z-index: 10; pointer-events: auto;" id="clear-playlist-search">
                                    <i class="fas fa-times text-muted" style="display: none;"></i>
                                </span>
                            </div>
                        </div>

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
    let availableSongsOffset = 0;
    let availableSongsLoading = false;
    let availableSongsHasMore = true;
    let currentSearch = '';

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

    // Kullanılabilir şarkıları yükle (Infinite Scroll)
    function loadAvailableSongs(search = '', append = false) {
        if (availableSongsLoading) return;

        // Yeni search ise offset'i sıfırla
        if (search !== currentSearch) {
            availableSongsOffset = 0;
            availableSongsHasMore = true;
            currentSearch = search;
            append = false;
        }

        if (!availableSongsHasMore && append) return;

        availableSongsLoading = true;

        // 🔍 DEBUG: AJAX isteği
        console.log('🎵 SEARCH REQUEST:', {
            search: search,
            offset: availableSongsOffset,
            append: append
        });

        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/available`,
            data: {
                search: search,
                offset: availableSongsOffset
            },
            beforeSend: function() {
                if (!append) {
                    // Minimal loading - sadece küçük spinner
                    $('#available-songs-container').html(`
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                        </div>
                    `);
                } else {
                    // Append loading indicator
                    if (!$('#loading-more').length) {
                        $('#available-songs-container .list-group').append(`
                            <div id="loading-more" class="text-center p-2">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                            </div>
                        `);
                    }
                }
            },
            success: function(data) {
                availableSongsLoading = false;
                $('#loading-more').remove();

                // 🔍 DEBUG: AJAX cevabı
                console.log('✅ SEARCH RESPONSE:', {
                    count: data.length,
                    first_3: data.slice(0, 3).map(s => `${s.title} - ${s.artist}`)
                });

                if (data.length < 50) {
                    availableSongsHasMore = false;
                }

                if (append) {
                    // Append to existing
                    availableSongs = availableSongs.concat(data);
                    renderAvailableSongs(true);
                } else {
                    // Replace
                    availableSongs = data;
                    renderAvailableSongs(false);
                }

                availableSongsOffset += data.length;
            },
            error: function() {
                availableSongsLoading = false;
                $('#loading-more').remove();
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
    function renderAvailableSongs(append = false) {
        // Backend zaten search yapmış, client-side filter gereksiz!
        const filtered = availableSongs;

        $('#available-count').text(availableSongs.length);

        if (filtered.length === 0 && !append) {
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

        let html = '';

        if (append) {
            // Sadece yeni eklenen şarkıları render et
            const existingIds = new Set($('#available-songs-container [data-song-id]').map(function() {
                return parseInt($(this).data('song-id'));
            }).get());

            filtered.forEach(song => {
                if (!existingIds.has(song.id)) {
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
                }
            });

            $('#available-songs-container .list-group').append(html);
        } else {
            // Full render
            html = '<div class="list-group list-group-flush">';
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

    // Şarkı ekleme - ANINDA transfer (UI first, AJAX after)
    $(document).on('click', '.add-song-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const songId = btn.data('song-id');
        const songItem = btn.closest('.list-group-item');

        // Memory güncelle
        const song = availableSongs.find(s => s.id === songId);
        playlistSongs.push(song);
        availableSongs = availableSongs.filter(s => s.id !== songId);

        // ANINDA TRANSFER: Sol → Sağ (UI ÖNCELİKLİ!)
        const newIndex = playlistSongs.length - 1;
        const row = songItem.find('.row');

        // 1. Sortable handle + position badge ekle (row başına)
        row.find('.col').before(`
            <div class="col-auto">
                <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: grab; font-size: 1.2rem;"></i>
            </div>
            <div class="col-auto">
                <span class="badge bg-secondary">${newIndex + 1}</span>
            </div>
        `);

        // 2. Butonu değiştir (+ → ×)
        songItem.find('.add-song-btn')
            .removeClass('btn-success add-song-btn')
            .addClass('btn-outline-danger remove-song-btn')
            .html('<i class="fas fa-times"></i>');

        // 3. Class değiştir (available → playlist)
        songItem.removeClass('list-group-item-action')
            .addClass('sortable-item')
            .attr('data-position', newIndex);

        // 4. Avatar color değiştir (secondary → primary)
        songItem.find('.bg-secondary-lt').removeClass('bg-secondary-lt').addClass('bg-primary-lt');

        // 5. Sağ listeye APPEND et
        const playlistContainer = $('#sortable-playlist');
        if (playlistContainer.length) {
            songItem.detach().appendTo(playlistContainer);
            initSortable();
        } else {
            // Liste boşsa, container oluştur
            $('#playlist-songs-container').html('<div id="sortable-playlist" class="list-group list-group-flush"></div>');
            songItem.detach().appendTo('#sortable-playlist');
            initSortable();
        }

        // Counter'ları güncelle
        $('#available-count').text(availableSongs.length);
        $('#playlist-count').text(playlistSongs.length);
        updateTotalDurationFromMemory();

        // AJAX kayıt (ARKA PLANDA - UI bloklama yok!)
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/add`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { song_id: songId },
            error: function(xhr) {
                // Hata varsa geri al
                showError(xhr.responseJSON?.message || '{{ __("admin.error_occurred") }}');
                // TODO: Rollback UI changes
            }
        });
    });

    // Şarkı çıkarma - ANINDA transfer (UI first, AJAX after)
    $(document).on('click', '.remove-song-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const songId = btn.data('song-id');
        const songItem = btn.closest('.list-group-item');

        // Memory güncelle
        const song = playlistSongs.find(s => s.id === songId);
        availableSongs.push(song);
        playlistSongs = playlistSongs.filter(s => s.id !== songId);

        // ANINDA TRANSFER: Sağ → Sol (UI ÖNCELİKLİ!)
        // 1. Sortable handle + position badge sil
        songItem.find('.sortable-handle').closest('.col-auto').remove();
        songItem.find('.badge').closest('.col-auto').remove();

        // 2. Butonu değiştir (× → +)
        songItem.find('.remove-song-btn')
            .removeClass('btn-outline-danger remove-song-btn')
            .addClass('btn-success add-song-btn')
            .html('<i class="fas fa-plus me-1"></i> {{ __("admin.add") }}');

        // 3. Class değiştir (playlist → available)
        songItem.removeClass('sortable-item')
            .addClass('list-group-item-action')
            .removeAttr('data-position');

        // 4. Avatar color değiştir (primary → secondary)
        songItem.find('.bg-primary-lt').removeClass('bg-primary-lt').addClass('bg-secondary-lt');

        // 5. Sol listeye APPEND et
        const availableContainer = $('#available-songs-container .list-group');
        if (availableContainer.length) {
            songItem.detach().appendTo(availableContainer);
        } else {
            // Liste boşsa yeniden oluştur
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

        // AJAX kayıt (ARKA PLANDA - UI bloklama yok!)
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/remove`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { song_id: songId },
            error: function(xhr) {
                // Hata varsa geri al
                showError(xhr.responseJSON?.message || '{{ __("admin.error_occurred") }}');
                // TODO: Rollback UI changes
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

    // SOL TARAF: Arama (Backend) - debounce
    let searchAvailableTimeout;
    $('#search-available').on('keyup input', function() {
        clearTimeout(searchAvailableTimeout);
        const search = $(this).val();

        // X ikonunu göster/gizle
        if (search.length > 0) {
            $('#clear-available-search i').show();
        } else {
            $('#clear-available-search i').hide();
        }

        searchAvailableTimeout = setTimeout(() => {
            // Boşsa veya 2+ karakterse backend'den yükle
            if (search.length === 0 || search.length >= 2) {
                loadAvailableSongs(search, false);
            }
            // 1 karakterse hiçbir şey yapma (kullanıcı yazmaya devam edecek)
        }, 150); // Hızlı yanıt için 150ms
    });

    // SOL TARAF: Clear button
    $('#clear-available-search').on('click', function() {
        $('#search-available').val('').trigger('input');
        $('#clear-available-search i').hide();
    });

    // SOL TARAF: Infinite Scroll
    $('#available-songs-container').on('scroll', function() {
        const container = $(this);
        const scrollTop = container.scrollTop();
        const scrollHeight = container[0].scrollHeight;
        const clientHeight = container.height();

        // Bottom'a 100px kala load et
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            if (availableSongsHasMore && !availableSongsLoading) {
                loadAvailableSongs(currentSearch, true);
            }
        }
    });

    // SAĞ TARAF: Arama (Client-side) - instant filter
    $('#search-playlist').on('keyup input', function() {
        const search = $(this).val().toLowerCase();

        // X ikonunu göster/gizle
        if (search.length > 0) {
            $('#clear-playlist-search i').show();
        } else {
            $('#clear-playlist-search i').hide();
        }

        if (search === '') {
            // Boşsa hepsini göster
            $('#sortable-playlist .sortable-item').show();
        } else {
            // Filtrele
            $('#sortable-playlist .sortable-item').each(function() {
                const $item = $(this);
                const title = $item.find('strong').text().toLowerCase();
                const artist = $item.find('small').text().toLowerCase();

                if (title.includes(search) || artist.includes(search)) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        }
    });

    // SAĞ TARAF: Clear button
    $('#clear-playlist-search').on('click', function() {
        $('#search-playlist').val('').trigger('input');
        $('#clear-playlist-search i').hide();
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
/* Kompakt Liste - Daha fazla şarkı */
.list-group-item {
    padding: 0.5rem 0.75rem !important;
}

.list-group-item .avatar {
    width: 32px !important;
    height: 32px !important;
}

.list-group-item .avatar i {
    font-size: 0.875rem;
}

.list-group-item strong {
    font-size: 0.875rem;
}

.list-group-item small {
    font-size: 0.75rem;
}

.list-group-item .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.list-group-item .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.4rem;
}

/* Sortable styles */
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e7f3ff;
}

.sortable-handle {
    font-size: 1rem !important;
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
