<div>
    @php
        View::share('pretitle', $spotId ? __('muzibu::admin.spot.edit_spot_pretitle') : __('muzibu::admin.spot.new_spot_pretitle'));
    @endphp

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')

        <div class="row">
            {{-- Sol Kolon: Ana Bilgiler --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Spot Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        {{-- Kurumsal Hesap --}}
                        <div class="mb-4">
                            <div class="form-floating">
                                <select wire:model="inputs.corporate_account_id"
                                    class="form-control @error('inputs.corporate_account_id') is-invalid @enderror"
                                    id="corporate_select">
                                    <option value="">Seçiniz...</option>
                                    @foreach($corporateAccounts as $corp)
                                        <option value="{{ $corp->id }}">{{ $corp->company_name }}</option>
                                    @endforeach
                                </select>
                                <label for="corporate_select">
                                    Kurumsal Hesap <span class="required-star">★</span>
                                </label>
                                @error('inputs.corporate_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Başlık --}}
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="text" wire:model="inputs.title"
                                    class="form-control @error('inputs.title') is-invalid @enderror"
                                    id="title_input"
                                    placeholder="Spot başlığı">
                                <label for="title_input">
                                    Başlık <span class="required-star">★</span>
                                </label>
                                @error('inputs.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Ses Dosyası - Song Manage Tasarımı --}}
                        <div class="mb-4">
                            <label class="form-label required">Ses Dosyası</label>

                            <style>
                                .spot-card-with-hover:hover .spot-delete-btn {
                                    opacity: 1 !important;
                                }
                            </style>

                            <div x-data="{
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        $refs.fileInput.files = files;
                                        $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }">
                                {{-- Hidden File Input --}}
                                <input
                                    type="file"
                                    x-ref="fileInput"
                                    wire:model="audioFile"
                                    class="d-none"
                                    accept="audio/mp3,audio/wav,audio/flac,audio/m4a,audio/ogg,audio/mpeg,audio/aac,audio/x-ms-wma">

                                {{-- Upload Area - Dosya yoksa göster --}}
                                @if(!$existingAudioUrl && !$tempAudioUrl)
                                    <div
                                        @click="$refs.fileInput.click()"
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleDrop($event)"
                                        :class="{ 'border-primary bg-primary-lt': isDragging }"
                                        class="border border-2 border-dashed rounded p-4 text-center cursor-pointer"
                                        style="cursor: pointer; transition: all 0.2s; min-height: 180px;"
                                        wire:loading.class="opacity-50"
                                        wire:target="audioFile">

                                        <div class="mb-3" wire:loading.remove wire:target="audioFile">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>

                                        {{-- Upload Progress --}}
                                        <div wire:loading wire:target="audioFile" class="mb-3">
                                            <div class="spinner-border text-purple" role="status">
                                                <span class="visually-hidden">Yükleniyor...</span>
                                            </div>
                                        </div>

                                        <h4 class="mb-1" wire:loading.remove wire:target="audioFile">Ses Dosyasını Sürükle Bırak</h4>
                                        <p class="text-muted mb-2" wire:loading.remove wire:target="audioFile">veya tıklayarak dosya seç</p>

                                        <div wire:loading wire:target="audioFile">
                                            <p class="text-muted mb-2">Dosya yükleniyor ve analiz ediliyor...</p>
                                        </div>

                                        <small class="text-muted d-block" wire:loading.remove wire:target="audioFile">
                                            Desteklenen formatlar: MP3, WAV, FLAC, M4A, OGG, AAC, WMA
                                            <span class="mx-1">•</span>
                                            Maksimum: 30MB
                                        </small>
                                    </div>
                                @endif

                                {{-- Yeni Yüklenen Dosya Preview (Henüz Kaydedilmedi) --}}
                                @if($tempAudioUrl)
                                    <div class="card position-relative spot-card-with-hover border-success" style="min-height: 140px;">
                                        {{-- Yeni Dosya Badge --}}
                                        <span class="badge bg-success position-absolute" style="top: 8px; left: 8px; z-index: 10;">
                                            <i class="fas fa-check me-1"></i> Yeni Yüklendi
                                        </span>

                                        {{-- X Button (Hover to Show) --}}
                                        <button
                                            wire:click="removeTempAudio"
                                            class="btn btn-icon btn-sm position-absolute spot-delete-btn"
                                            type="button"
                                            style="top: 8px; right: 8px; z-index: 10; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.95); border: 1px solid #dee2e6; opacity: 0; transition: opacity 0.2s;">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>

                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar bg-success-lt me-3">
                                                    <i class="fa fa-bullhorn"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="fw-bold text-truncate" style="max-width: 300px;">
                                                        {{ $tempAudioName }}
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $this->getFormattedDuration() }}
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- Audio Player --}}
                                            <audio controls class="w-100" style="height: 35px;">
                                                <source src="{{ $tempAudioUrl }}" type="audio/mpeg">
                                            </audio>

                                            <div class="alert alert-info mt-2 mb-0 py-2">
                                                <small>
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Kaydet butonuna basana kadar dosya kaydedilmeyecektir.
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Değiştir Butonu --}}
                                    <div class="mt-2">
                                        <button type="button" @click="$refs.fileInput.click()" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-sync me-1"></i>
                                            Farklı Dosya Seç
                                        </button>
                                    </div>
                                @endif

                                {{-- Mevcut Kayıtlı Ses Dosyası Card --}}
                                @if($existingAudioUrl && !$tempAudioUrl)
                                    <div class="card position-relative spot-card-with-hover" style="min-height: 140px;">
                                        {{-- X Button (Hover to Show) --}}
                                        <button
                                            wire:click="removeAudio"
                                            wire:confirm="Ses dosyasını kaldırmak istediğinize emin misiniz?"
                                            class="btn btn-icon btn-sm position-absolute spot-delete-btn"
                                            type="button"
                                            style="top: 8px; right: 8px; z-index: 10; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.95); border: 1px solid #dee2e6; opacity: 0; transition: opacity 0.2s;">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>

                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar bg-purple-lt me-3">
                                                    <i class="fa fa-bullhorn"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="fw-bold text-truncate" style="max-width: 300px;">
                                                        {{ $existingAudioName }}
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $this->getFormattedDuration() }}
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- Audio Player --}}
                                            <audio controls class="w-100" style="height: 35px;">
                                                <source src="{{ $existingAudioUrl }}?v={{ time() }}" type="audio/mpeg">
                                            </audio>
                                        </div>
                                    </div>

                                    {{-- Değiştir Butonu --}}
                                    <div class="mt-2">
                                        <button type="button" @click="$refs.fileInput.click()" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-sync me-1"></i>
                                            Dosyayı Değiştir
                                        </button>
                                    </div>
                                @endif

                                @error('audioFile')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tarih Aralığı --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Başlangıç Tarihi & Saati</label>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <input type="date" wire:model="inputs.starts_at_date"
                                            class="form-control @error('inputs.starts_at_date') is-invalid @enderror">
                                    </div>
                                    <div class="col-5">
                                        <input type="time" wire:model="inputs.starts_at_time"
                                            class="form-control @error('inputs.starts_at_time') is-invalid @enderror">
                                    </div>
                                </div>
                                <div class="form-text">Boş bırakılırsa hemen başlar</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bitiş Tarihi & Saati</label>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <input type="date" wire:model="inputs.ends_at_date"
                                            class="form-control @error('inputs.ends_at_date') is-invalid @enderror">
                                    </div>
                                    <div class="col-5">
                                        <input type="time" wire:model="inputs.ends_at_time"
                                            class="form-control @error('inputs.ends_at_time') is-invalid @enderror">
                                    </div>
                                </div>
                                <div class="form-text">Boş bırakılırsa süresiz devam eder</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sağ Kolon: Durum ve Kaydet --}}
            <div class="col-lg-4">
                {{-- Durum Kartı --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Durum</h3>
                    </div>
                    <div class="card-body">
                        {{-- Aktif --}}
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model="inputs.is_enabled" class="form-check-input">
                                <span class="form-check-label">Aktif</span>
                            </label>
                            <div class="form-text">Spot çalınabilir durumda mı?</div>
                        </div>

                        {{-- Arşiv --}}
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model="inputs.is_archived" class="form-check-input">
                                <span class="form-check-label">Arşivlenmiş</span>
                            </label>
                            <div class="form-text">Arşivlenen spotlar çalınmaz</div>
                        </div>

                        {{-- Süre Bilgisi --}}
                        @if($inputs['duration'] > 0)
                        <div class="mb-0">
                            <div class="d-flex align-items-center text-muted">
                                <i class="fas fa-clock me-2"></i>
                                <span>Süre: <strong>{{ $this->getFormattedDuration() }}</strong></span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- İstatistikler (Sadece Edit) --}}
                @if($spotId && $this->currentSpot)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">İstatistikler</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-primary-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">{{ $this->currentSpot->play_count ?? 0 }}</div>
                                        <div class="small text-muted">Toplam Çalma</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-success-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">{{ $this->currentSpot->today_play_count ?? 0 }}</div>
                                        <div class="small text-muted">Bugün</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-warning-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">{{ $this->currentSpot->skip_count ?? 0 }}</div>
                                        <div class="small text-muted">Atlama</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-danger-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">%{{ number_format($this->currentSpot->skip_rate ?? 0, 1) }}</div>
                                        <div class="small text-muted">Atlama Oranı</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Kaydet Butonları --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i>
                                {{ $spotId ? 'Güncelle' : 'Oluştur' }}
                            </button>
                            <button type="button" wire:click="save(true)" class="btn btn-outline-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ $spotId ? 'Güncelle' : 'Oluştur' }} ve Listeye Dön
                            </button>
                            <a href="{{ route('admin.muzibu.spot.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                İptal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
