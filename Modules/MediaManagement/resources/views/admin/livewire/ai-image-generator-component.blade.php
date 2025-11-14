{{-- AI Image Generator Component --}}
@include('mediamanagement::admin.helper')

<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
                </div>
                <div>{{ $errorMessage }}</div>
            </div>
        </div>
    @endif

    <div class="row row-cards">
        <!-- Generator Form -->
        <div class="col-lg-8">
            <div class="card" style="position: relative;">
                <div class="card-header">
                    <h3 class="card-title">Yeni Görsel Oluştur</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="generate">
                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="generate" class="overlay">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Yükleniyor...</span>
                                </div>
                                <h3 class="text-muted">Görsel Oluşturuluyor...</h3>
                                <p class="text-muted">Bu işlem 30-60 saniye sürebilir.</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Prompt</label>
                            <textarea
                                wire:model="prompt"
                                class="form-control @error('prompt') is-invalid @enderror"
                                rows="4"
                                placeholder="Oluşturmak istediğiniz görseli tanımlayın... (örn: 'Modern depoda profesyonel forklift')"
                                {{ $isGenerating ? 'disabled' : '' }}
                            ></textarea>
                            @error('prompt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="form-hint">Minimum 10 karakter. Detaylı ve açıklayıcı olun.</small>
                        </div>

                        @if($companyName)
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="includeCompanyName" {{ $isGenerating ? 'disabled' : '' }}>
                                <span class="form-check-label">Firma ismini ekle: <strong>{{ $companyName }}</strong></span>
                            </label>
                            <small class="form-hint text-muted">Prompt başına "{{ $companyName }} - " ekler</small>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Boyut</label>
                                    <select wire:model="size" class="form-select" {{ $isGenerating ? 'disabled' : '' }}>
                                        <option value="1024x1024">1024x1024 (Kare)</option>
                                        <option value="1024x1792">1024x1792 (Dikey)</option>
                                        <option value="1792x1024">1792x1024 (Yatay)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kalite</label>
                                    <select wire:model="quality" class="form-select" {{ $isGenerating ? 'disabled' : '' }}>
                                        <option value="hd">HD (1 kredi)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" {{ $isGenerating || $availableCredits < 1 ? 'disabled' : '' }}>
                                @if ($isGenerating)
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Oluşturuluyor...
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg>
                                    Görsel Oluştur
                                @endif
                            </button>

                            @if ($generatedImageUrl)
                                <button type="button" wire:click="resetForm" class="btn btn-ghost-secondary">
                                    Sıfırla
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Generated Image Preview -->
            @if ($generatedImageUrl)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Oluşturulan Görsel</h3>
                        <div class="card-actions">
                            <a href="{{ $generatedImageUrl }}" class="btn btn-primary" download>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path><path d="M7 11l5 5l5 -5"></path><path d="M12 4l0 12"></path></svg>
                                İndir
                            </a>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $generatedImageUrl }}" alt="Oluşturulan Görsel" class="img-fluid rounded" style="max-height: 600px;">
                    </div>
                </div>
            @endif
        </div>

        <!-- History Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Oluşturulanlar</h3>
                </div>
                <div class="card-body p-0">
                    @if(count($history) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($history as $item)
                                @php
                                    $media = $item->getFirstMedia('library');
                                @endphp
                                @if($media)
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img src="{{ $media->getUrl('thumb') ?? $media->getUrl() }}" alt="Thumbnail" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="col text-truncate">
                                                <div class="text-reset d-block text-truncate">{{ $item->generation_prompt }}</div>
                                                <div class="text-muted text-truncate mt-1">
                                                    <small>{{ $item->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 8h.01"></path><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path></svg>
                            </div>
                            <p class="empty-title">Henüz görsel yok</p>
                            <p class="empty-subtitle text-muted">
                                Görsel oluşturmaya başlayın
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Bilgi</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Model</div>
                            <div class="datagrid-content">DALL-E 3</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Kredi Maliyeti</div>
                            <div class="datagrid-content">
                                <span class="badge bg-blue">1 kredi / HD görsel</span>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Oluşturma Süresi</div>
                            <div class="datagrid-content">30-60 saniye</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--tblr-body-bg);
        opacity: 0.98;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        border-radius: 0.5rem;
    }

    .overlay > div {
        margin: auto;
    }
    </style>
</div>
