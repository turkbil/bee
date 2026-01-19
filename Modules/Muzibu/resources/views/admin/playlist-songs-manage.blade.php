@include('muzibu::admin.helper')
@extends('admin.layout')

@push('title')
<span id="playlist-title-dynamic">{{ __('admin.loading') }}...</span>
@endpush

@section('content')
    <!-- Dual-List Container -->
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
                        <div class="card-actions">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="shuffle-playlist-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('muzibu::admin.playlist.shuffle_songs') }}">
                                <i class="fas fa-shuffle me-1"></i>
                                {{ __('muzibu::admin.playlist.shuffle') }}
                            </button>
                        </div>
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

    {{-- Mini Player --}}
    @include('muzibu::admin.partials.mini-player')
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

    // Search cache - aynı aramaları tekrar yapmamak için
    const searchCache = new Map();
    const CACHE_TTL = 30000; // 30 saniye cache (backend ile senkron)

    // Playlist songs infinite scroll değişkenleri
    let playlistSongsOffset = 0;
    let playlistSongsLoading = false;
    let playlistSongsHasMore = true;
    let playlistTotalCount = 0;

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

    // Kullanılabilir şarkıları yükle (Infinite Scroll + Cache)
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

        // Cache key oluştur
        const cacheKey = `${search}_${availableSongsOffset}`;
        const cached = searchCache.get(cacheKey);

        // Cache varsa ve geçerli ise kullan
        if (cached && (Date.now() - cached.time < CACHE_TTL) && !append) {
            availableSongs = cached.data;
            availableSongsHasMore = cached.hasMore;
            renderAvailableSongs(false);
            availableSongsOffset = cached.data.length;
            return;
        }

        availableSongsLoading = true;

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

                const hasMore = data.length >= 50;
                availableSongsHasMore = hasMore;

                // Cache'e kaydet
                searchCache.set(cacheKey, {
                    data: data,
                    hasMore: hasMore,
                    time: Date.now()
                });

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

    // Playlist şarkılarını yükle (Infinite Scroll)
    function loadPlaylistSongs(append = false) {
        if (playlistSongsLoading) return;

        if (!append) {
            playlistSongsOffset = 0;
            playlistSongsHasMore = true;
        }

        if (!playlistSongsHasMore && append) return;

        playlistSongsLoading = true;

        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/selected`,
            data: {
                offset: playlistSongsOffset
            },
            beforeSend: function() {
                if (!append) {
                    $('#playlist-songs-container').html(`
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary"></div>
                            <div class="mt-2 text-muted">{{ __('admin.loading') }}...</div>
                        </div>
                    `);
                } else {
                    // Append loading indicator
                    if (!$('#loading-more-playlist').length) {
                        $('#sortable-playlist').append(`
                            <div id="loading-more-playlist" class="text-center p-2">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                            </div>
                        `);
                    }
                }
            },
            success: function(data) {
                playlistSongsLoading = false;
                $('#loading-more-playlist').remove();

                playlistSongsHasMore = data.has_more || false;
                playlistTotalCount = data.total_count || 0;

                if (append) {
                    // Append to existing
                    playlistSongs = playlistSongs.concat(data.songs);
                    renderPlaylistSongs(true);
                } else {
                    // Replace
                    playlistSongs = data.songs;
                    renderPlaylistSongs(false);
                }

                playlistSongsOffset += data.songs.length;
                updateTotalDuration(data.total_duration);
                $('#playlist-count').text(playlistTotalCount);
            },
            error: function() {
                playlistSongsLoading = false;
                $('#loading-more-playlist').remove();
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
                    const playBtn = (song.hls_url || song.file_url)
                        ? `<button class="btn-mini btn-mini-primary play-song-btn" data-song-id="${song.id}" data-title="${song.title}" data-artist="${song.artist || ''}" data-hls-url="${song.hls_url || ''}" data-file-url="${song.file_url || ''}" title="Dinle"><i class="fas fa-play"></i></button>`
                        : `<button class="btn-mini btn-mini-muted" disabled title="Dosya yok"><i class="fas fa-play"></i></button>`;

                    html += `
                        <div class="list-group-item list-group-item-action" data-song-id="${song.id}">
                            <div class="song-item">
                                <div class="song-play">${playBtn}</div>
                                <div class="song-content">
                                    <div class="song-row1">
                                        <span class="col-6" title="${song.title}">${song.title}</span>
                                        <span class="col-6" title="${song.artist || ''}">${song.artist || '-'}</span>
                                    </div>
                                    <div class="song-row2">
                                        <span class="col-6" title="${song.album || ''}">${song.album || '-'}</span>
                                        <span class="col-6" title="${song.genre || ''}">${song.genre || '-'}</span>
                                    </div>
                                </div>
                                <div class="song-action">
                                    <span class="song-duration">${song.duration || ''}</span>
                                    <button class="btn-mini btn-mini-success add-song-btn" data-song-id="${song.id}" title="Ekle">
                                        <i class="fas fa-plus"></i>
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
                const playBtn = (song.hls_url || song.file_url)
                    ? `<button class="btn-mini btn-mini-primary play-song-btn" data-song-id="${song.id}" data-title="${song.title}" data-artist="${song.artist || ''}" data-hls-url="${song.hls_url || ''}" data-file-url="${song.file_url || ''}" title="Dinle"><i class="fas fa-play"></i></button>`
                    : `<button class="btn-mini btn-mini-muted" disabled title="Dosya yok"><i class="fas fa-play"></i></button>`;

                html += `
                    <div class="list-group-item list-group-item-action" data-song-id="${song.id}">
                        <div class="song-item">
                            <div class="song-play">${playBtn}</div>
                            <div class="song-content">
                                <div class="song-row1">
                                    <span class="col-6" title="${song.title}">${song.title}</span>
                                    <span class="col-6" title="${song.artist || ''}">${song.artist || '-'}</span>
                                </div>
                                <div class="song-row2">
                                    <span class="col-6" title="${song.album || ''}">${song.album || '-'}</span>
                                    <span class="col-6" title="${song.genre || ''}">${song.genre || '-'}</span>
                                </div>
                            </div>
                            <div class="song-action">
                                <span class="song-duration">${song.duration || ''}</span>
                                <button class="btn-mini btn-mini-success add-song-btn" data-song-id="${song.id}" title="Ekle">
                                    <i class="fas fa-plus"></i>
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
    function renderPlaylistSongs(append = false) {
        if (playlistSongs.length === 0 && !append) {
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

        let html = '';

        if (append) {
            // Sadece yeni eklenen şarkıları render et
            const existingIds = new Set($('#sortable-playlist [data-song-id]').map(function() {
                return parseInt($(this).data('song-id'));
            }).get());

            const startIndex = $('#sortable-playlist .sortable-item').length;

            playlistSongs.forEach((song, index) => {
                if (!existingIds.has(song.id)) {
                    const playBtn = (song.hls_url || song.file_url)
                        ? `<button class="btn-mini btn-mini-primary play-song-btn" data-song-id="${song.id}" data-title="${song.title}" data-artist="${song.artist || ''}" data-hls-url="${song.hls_url || ''}" data-file-url="${song.file_url || ''}" title="Dinle"><i class="fas fa-play"></i></button>`
                        : `<button class="btn-mini btn-mini-muted" disabled title="Dosya yok"><i class="fas fa-play"></i></button>`;

                    html += `
                        <div class="list-group-item sortable-item" data-song-id="${song.id}" data-position="${startIndex + index}">
                            <div class="song-item">
                                <i class="fas fa-grip-vertical sortable-handle"></i>
                                <span class="song-index">${startIndex + index + 1}</span>
                                <div class="song-play">${playBtn}</div>
                                <div class="song-content">
                                    <div class="song-row1">
                                        <span class="col-6" title="${song.title}">${song.title}</span>
                                        <span class="col-6" title="${song.artist || ''}">${song.artist || '-'}</span>
                                    </div>
                                    <div class="song-row2">
                                        <span class="col-6" title="${song.album || ''}">${song.album || '-'}</span>
                                        <span class="col-6" title="${song.genre || ''}">${song.genre || '-'}</span>
                                    </div>
                                </div>
                                <div class="song-action">
                                    <span class="song-duration">${song.duration || ''}</span>
                                    <button class="btn-mini btn-mini-danger remove-song-btn" data-song-id="${song.id}" title="Çıkar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            $('#sortable-playlist').append(html);
        } else {
            // Full render
            html = '<div id="sortable-playlist" class="list-group list-group-flush">';
            playlistSongs.forEach((song, index) => {
                const playBtn = (song.hls_url || song.file_url)
                    ? `<button class="btn-mini btn-mini-primary play-song-btn" data-song-id="${song.id}" data-title="${song.title}" data-artist="${song.artist || ''}" data-hls-url="${song.hls_url || ''}" data-file-url="${song.file_url || ''}" title="Dinle"><i class="fas fa-play"></i></button>`
                    : `<button class="btn-mini btn-mini-muted" disabled title="Dosya yok"><i class="fas fa-play"></i></button>`;

                html += `
                    <div class="list-group-item sortable-item" data-song-id="${song.id}" data-position="${index}">
                        <div class="song-item">
                            <i class="fas fa-grip-vertical sortable-handle"></i>
                            <span class="song-index">${index + 1}</span>
                            <div class="song-play">${playBtn}</div>
                            <div class="song-content">
                                <div class="song-row1">
                                    <span class="col-6" title="${song.title}">${song.title}</span>
                                    <span class="col-6" title="${song.artist || ''}">${song.artist || '-'}</span>
                                </div>
                                <div class="song-row2">
                                    <span class="col-6" title="${song.album || ''}">${song.album || '-'}</span>
                                    <span class="col-6" title="${song.genre || ''}">${song.genre || '-'}</span>
                                </div>
                            </div>
                            <div class="song-action">
                                <span class="song-duration">${song.duration || ''}</span>
                                <button class="btn-mini btn-mini-danger remove-song-btn" data-song-id="${song.id}" title="Çıkar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            $('#playlist-songs-container').html(html);
        }

        // Sortable.js init (her zaman - append'de de gerekebilir)
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

    // Şarkı ekleme - Temiz re-render yaklaşımı
    $(document).on('click', '.add-song-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const songId = btn.data('song-id');

        // Memory güncelle
        const song = availableSongs.find(s => s.id === songId);
        if (!song) return;

        playlistSongs.push(song);
        availableSongs = availableSongs.filter(s => s.id !== songId);

        // Temiz re-render (tutarlı tasarım!)
        renderAvailableSongs(false);
        renderPlaylistSongs();
        updateTotalDurationFromMemory();

        // Otomatik yükleme: Eğer available songs azsa, yeni batch yükle
        checkAndLoadMoreAvailableSongs();

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

    // Şarkı çıkarma - Temiz re-render yaklaşımı
    $(document).on('click', '.remove-song-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const songId = btn.data('song-id');

        // Memory güncelle
        const song = playlistSongs.find(s => s.id === songId);
        if (!song) return;

        availableSongs.unshift(song); // Başa ekle (çıkarılan şarkı en üstte görünsün)
        playlistSongs = playlistSongs.filter(s => s.id !== songId);

        // Temiz re-render (tutarlı tasarım!)
        renderAvailableSongs(false);
        renderPlaylistSongs();
        updateTotalDurationFromMemory();

        // AJAX kayıt (ARKA PLANDA - UI bloklama yok!)
        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/remove`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { song_id: songId },
            error: function(xhr) {
                // Hata varsa geri yükle
                showError(xhr.responseJSON?.message || '{{ __("admin.error_occurred") }}');
                loadAvailableSongs();
                loadPlaylistSongs();
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

    // Playlist şarkılarını karıştır (shuffle)
    $('#shuffle-playlist-btn').on('click', function() {
        if (playlistSongs.length === 0) {
            showError('{{ __("muzibu::admin.playlist.no_songs_to_shuffle") }}');
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();

        // Loading state
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __("admin.shuffling") }}...');

        // Fisher-Yates shuffle algorithm
        const shuffled = [...playlistSongs];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }

        // Memory'yi güncelle
        playlistSongs = shuffled;

        // UI'ı güncelle
        renderPlaylistSongs();

        // Yeni sıralamayı backend'e kaydet
        const newOrder = {};
        playlistSongs.forEach((song, index) => {
            newOrder[song.id] = index;
        });

        $.ajax({
            url: `/admin/muzibu/playlist/api/${playlistId}/reorder`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { order: newOrder },
            success: function(response) {
                btn.prop('disabled', false).html(originalHtml);
                showToast('{{ __("admin.success") }}', '{{ __("muzibu::admin.playlist.shuffled_successfully") }}', 'success');
            },
            error: function() {
                btn.prop('disabled', false).html(originalHtml);
                showError('{{ __("admin.error_occurred") }}');
                loadPlaylistSongs(); // Hata durumunda geri yükle
            }
        });
    });

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
    let lastSearchValue = '';

    $('#search-available').on('keyup input', function() {
        clearTimeout(searchAvailableTimeout);
        const search = $(this).val();

        // X ikonunu göster/gizle
        if (search.length > 0) {
            $('#clear-available-search i').show();
        } else {
            $('#clear-available-search i').hide();
        }

        // Aynı değerse tekrar arama yapma
        if (search === lastSearchValue) return;

        searchAvailableTimeout = setTimeout(() => {
            // Boşsa veya 2+ karakterse backend'den yükle
            if (search.length === 0 || search.length >= 2) {
                lastSearchValue = search;
                loadAvailableSongs(search, false);
            }
            // 1 karakterse hiçbir şey yapma (kullanıcı yazmaya devam edecek)
        }, 400); // 400ms debounce - daha az request
    });

    // Sekme değişikliğinde state'i koru (visibility API)
    document.addEventListener('visibilitychange', function() {
        // Sekme görünür olduğunda mevcut sonuçları koru, yeniden yükleme
        // Sadece availableSongs boşsa ve search varsa yeniden yükle
        if (!document.hidden && availableSongs.length === 0 && currentSearch) {
            loadAvailableSongs(currentSearch, false);
        }
    });

    // Otomatik yükleme: Sol taraf azaldıysa yeni batch yükle
    function checkAndLoadMoreAvailableSongs() {
        // Eğer available songs 20'den azsa ve hala yüklenecek varsa
        if (availableSongs.length < 20 && availableSongsHasMore && !availableSongsLoading) {
            loadAvailableSongs(currentSearch, true);
        }
    }

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

    // SAĞ TARAF: Infinite Scroll (Playlist Songs)
    $('#playlist-songs-container').on('scroll', function() {
        const container = $(this);
        const scrollTop = container.scrollTop();
        const scrollHeight = container[0].scrollHeight;
        const clientHeight = container.height();

        // Bottom'a 100px kala load et
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            if (playlistSongsHasMore && !playlistSongsLoading) {
                loadPlaylistSongs(true);
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
            // Filtrele - tüm song-content içindeki metni ara
            $('#sortable-playlist .sortable-item').each(function() {
                const $item = $(this);
                const content = $item.find('.song-content').text().toLowerCase();

                if (content.includes(search)) {
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

    // Play butonu tıklama
    $(document).on('click', '.play-song-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const songId = btn.data('song-id');
        const title = btn.data('title');
        const artist = btn.data('artist');
        const hlsUrl = btn.attr('data-hls-url');
        const fileUrl = btn.attr('data-file-url');

        if (typeof playAdminSong === 'function') {
            playAdminSong({
                id: songId,
                title: title,
                artist: artist,
                hls_url: hlsUrl,
                file_url: fileUrl,
                is_hls: !!hlsUrl
            });
        } else {
            console.error('playAdminSong function not found');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
/* Liste */
.list-group-item {
    padding: 0.5rem 0.75rem !important;
    border-color: rgba(98, 105, 118, 0.1);
}

/* Ana yapı: Play | Content | Action */
.song-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.song-play {
    flex-shrink: 0;
}

.song-content {
    flex: 1;
    min-width: 0;
}

.song-action {
    flex-shrink: 0;
}

/* Satırlar */
.song-row1,
.song-row2 {
    display: flex;
    white-space: nowrap;
    overflow: hidden;
}

/* Satırlar: 6-6 kolonlar */
.song-row1 .col-6,
.song-row2 .col-6 { flex: 6; overflow: hidden; text-overflow: ellipsis; }

/* Genel stil */
.song-row1 span,
.song-row2 span {
    font-size: 0.75rem;
    color: #64748b;
    text-align: left;
}

/* 1. satır başlık vurgu */
.song-row1 .col-6:first-child {
    font-size: 0.8rem;
    font-weight: 500;
    color: inherit;
}

/* Action alanı: Süre + Buton */
.song-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.song-action .song-duration {
    font-size: 0.7rem;
    color: #94a3b8;
    min-width: 2.5rem;
    text-align: right;
}

/* 2. satır */
.song-row2 {
    margin-top: 2px;
}

/* Sıra numarası */
.song-index {
    flex-shrink: 0;
    width: 1.5rem;
    font-size: 0.7rem;
    color: #94a3b8;
    text-align: center;
}

/* Şarkı bilgileri */
.song-title {
    font-size: 0.8rem;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.song-artist {
    font-size: 0.7rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.song-duration {
    font-size: 0.65rem;
    color: #94a3b8;
    text-align: right;
}

.song-index {
    font-size: 0.65rem;
    color: #94a3b8;
    text-align: center;
}

/* Mini Butonlar */
.btn-mini {
    width: 1.25rem;
    height: 1.25rem;
    padding: 0;
    border: none;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    cursor: pointer;
    transition: all 0.15s;
}

.btn-mini-primary {
    background: #3b82f6;
    color: white;
}

.btn-mini-primary:hover {
    background: #2563eb;
}

.btn-mini-success {
    background: #10b981;
    color: white;
}

.btn-mini-success:hover {
    background: #059669;
}

.btn-mini-danger {
    background: transparent;
    color: #ef4444;
    opacity: 0.4;
}

.btn-mini-danger:hover {
    background: rgba(239, 68, 68, 0.1);
    opacity: 1;
}

.btn-mini-muted {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
}

/* Sortable */
.sortable-handle {
    font-size: 0.7rem;
    cursor: grab;
    opacity: 0.3;
    transition: opacity 0.15s;
    text-align: center;
}

.sortable-handle:hover {
    opacity: 1;
    color: #3b82f6;
}

.sortable-ghost {
    opacity: 0.4;
    background: #f1f5f9;
}

.sortable-chosen {
    background: #eff6ff;
}

/* Dark mode support */
[data-bs-theme="dark"] .song-title {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .list-group-item {
    border-color: rgba(255, 255, 255, 0.1);
}
</style>
@endpush
