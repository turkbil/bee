<div>
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">HLS Olmayan Şarkılar</div>
                    </div>
                    <div class="h1 mb-0">{{ $this->totalSongsWithoutHls }}</div>
                    <div class="text-muted mt-1">
                        <small>Dönüştürme bekliyor</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">HLS Olan Şarkılar</div>
                    </div>
                    <div class="h1 mb-0 text-success">{{ $this->totalSongsWithHls }}</div>
                    <div class="text-muted mt-1">
                        <small>Dönüştürülmüş</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Seçili Şarkılar</div>
                    </div>
                    <div class="h1 mb-0 text-primary">{{ count($selectedSongs) }}</div>
                    <div class="text-muted mt-1">
                        <small>Dönüştürülecek</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Şarkı ara..."
                            wire:model.live.debounce.300ms="searchTerm"
                        >
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="startConversion"
                        wire:loading.attr="disabled"
                        :disabled="count($selectedSongs) === 0"
                    >
                        <span wire:loading.remove wire:target="startConversion">
                            <i class="fas fa-cog me-1"></i>
                            HLS'e Dönüştür ({{ count($selectedSongs) }})
                        </span>
                        <span wire:loading wire:target="startConversion">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Dönüştürülüyor...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Songs Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">HLS Olmayan Şarkılar</h3>
            <div class="card-actions">
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="selectAll"
                        wire:model.live="selectAll"
                    >
                    <label class="form-check-label" for="selectAll">
                        Tümünü Seç
                    </label>
                </div>
            </div>
        </div>

        @if($this->songsWithoutHls->isEmpty())
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-check-circle fa-3x text-success"></i>
                </div>
                <h3 class="text-muted">Tüm şarkılar HLS'e dönüştürülmüş! 🎉</h3>
                <p class="text-muted">HLS olmayan şarkı bulunmuyor.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th class="w-1">
                                <!-- Checkbox column -->
                            </th>
                            <th>Şarkı</th>
                            <th>Albüm</th>
                            <th>Tür</th>
                            <th>Süre</th>
                            <th>MP3 Dosyası</th>
                            <th class="w-1">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->songsWithoutHls as $song)
                            <tr
                                wire:key="song-{{ $song->song_id }}"
                                class="{{ in_array($song->song_id, $selectedSongs) ? 'bg-primary-lt' : '' }}"
                            >
                                <td>
                                    <label class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            wire:click="toggleSong({{ $song->song_id }})"
                                            {{ in_array($song->song_id, $selectedSongs) ? 'checked' : '' }}
                                        >
                                    </label>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $song->getTranslated('title', 'tr') }}</div>
                                            <div class="text-muted small">ID: {{ $song->song_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($song->album)
                                        <span class="badge bg-blue-lt">
                                            {{ $song->album->getTranslated('title', 'tr') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($song->genre)
                                        <span class="badge bg-green-lt">
                                            {{ $song->genre->getTranslated('title', 'tr') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ gmdate('i:s', $song->duration) }}</span>
                                </td>
                                <td>
                                    <code class="small">{{ $song->file_path }}</code>
                                </td>
                                <td>
                                    @if(isset($conversionResults[$song->song_id]))
                                        @if($conversionResults[$song->song_id]['status'] === 'success')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Kuyruğa eklendi
                                            </span>
                                        @else
                                            <span class="badge bg-danger"
                                                data-bs-toggle="tooltip"
                                                title="{{ $conversionResults[$song->song_id]['message'] }}"
                                            >
                                                <i class="fas fa-times me-1"></i>
                                                Hata
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            HLS Yok
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <div class="text-muted">
                    Toplam {{ $this->songsWithoutHls->count() }} şarkı gösteriliyor.
                    @if(count($selectedSongs) > 0)
                        <span class="text-primary fw-bold">{{ count($selectedSongs) }} şarkı seçildi.</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mt-3">
        <div class="d-flex">
            <div>
                <i class="fas fa-info-circle me-2"></i>
            </div>
            <div>
                <h4 class="alert-title">Nasıl Çalışır?</h4>
                <div class="text-muted">
                    1. Dönüştürmek istediğiniz şarkıları seçin (checkbox ile)<br>
                    2. "HLS'e Dönüştür" butonuna tıklayın<br>
                    3. Şarkılar otomatik olarak HLS formatına dönüştürülecek (arka planda, queue ile)<br>
                    4. Dönüşüm tamamlandığında şarkılar bu listeden kaybolacak
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('livewire:navigated', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
