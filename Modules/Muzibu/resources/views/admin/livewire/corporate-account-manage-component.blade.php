<div>
    <form wire:submit.prevent="save">
        @include('admin.partials.error_message')

        <div class="row">
            {{-- Sol Kolon: Ana Bilgiler --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kurumsal Hesap Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        {{-- Hesap Sahibi --}}
                        <div class="mb-4">
                            <label class="form-label required">Hesap Sahibi</label>

                            @if($corporateId && $this->currentAccount?->user)
                                {{-- Edit modunda: Mevcut kullanici goster (readonly) --}}
                                <div class="d-flex align-items-center p-3 bg-light rounded border">
                                    <span class="avatar avatar-md me-3 bg-primary-lt">
                                        {{ strtoupper(substr($this->currentAccount->user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-bold">{{ $this->currentAccount->user->name }}</div>
                                        <div class="text-muted">{{ $this->currentAccount->user->email }}</div>
                                    </div>
                                    <span class="badge bg-green ms-auto">Secili</span>
                                </div>
                                <div class="form-text">Hesap sahibi degistirilemez</div>
                            @else
                                {{-- Yeni kayit: Kullanici ara --}}
                                <div class="position-relative" x-data="{ open: @entangle('showUserDropdown') }">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="userSearch"
                                        class="form-control @error('inputs.user_id') is-invalid @enderror"
                                        placeholder="Kullanici ara (isim veya email)..."
                                        @focus="open = true"
                                        autocomplete="off">

                                    @error('inputs.user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    {{-- User Dropdown --}}
                                    <div x-show="open && $wire.userSearch.length >= 2"
                                         x-cloak
                                         @click.outside="open = false"
                                         class="position-absolute w-100 mt-1 bg-white border rounded shadow-lg"
                                         style="z-index: 1050; max-height: 300px; overflow-y: auto;">
                                        @forelse($users as $user)
                                            <div wire:click="selectUser({{ $user->id }})"
                                                 class="px-3 py-2 cursor-pointer hover-bg-light border-bottom"
                                                 style="cursor: pointer;">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm me-2 bg-primary-lt">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <div class="fw-medium">{{ $user->name }}</div>
                                                        <div class="text-muted small">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-3 py-2 text-muted">
                                                <i class="fas fa-search me-1"></i> Kullanici bulunamadi
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="form-text">Kurumsal hesabin sahibi olacak kullaniciyi secin</div>
                            @endif
                        </div>

                        {{-- Firma Adi --}}
                        <div class="mb-4">
                            <label class="form-label required">Firma Adi</label>
                            <input type="text"
                                   wire:model="inputs.company_name"
                                   class="form-control @error('inputs.company_name') is-invalid @enderror"
                                   placeholder="Ornek: ABC Muzik Ltd. Sti.">
                            @error('inputs.company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Sube Adi (Opsiyonel) --}}
                        <div class="mb-4">
                            <label class="form-label">Sube Adi</label>
                            <input type="text"
                                   wire:model="inputs.branch_name"
                                   class="form-control @error('inputs.branch_name') is-invalid @enderror"
                                   placeholder="Ornek: Merkez Sube, Kadikoy Sube">
                            @error('inputs.branch_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opsiyonel - Bos birakilirsa ana firma olarak kabul edilir</div>
                        </div>

                        {{-- Kurum Kodu --}}
                        <div class="mb-4">
                            <label class="form-label required">Kurum Kodu</label>
                            <div class="input-group">
                                <input type="text"
                                       wire:model="inputs.corporate_code"
                                       class="form-control @error('inputs.corporate_code') is-invalid @enderror"
                                       placeholder="ABC12345"
                                       maxlength="20">
                                <button type="button" wire:click="generateCode" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-1"></i> Yeni Kod
                                </button>
                            </div>
                            @error('inputs.corporate_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Uyelerin katilim icin kullanacagi benzersiz kod</div>
                        </div>
                    </div>
                </div>

                {{-- Spot Ayarlari --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bullhorn me-2"></i>Spot (Anons) Ayarlari
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check form-switch mb-3">
                                    <input type="checkbox" wire:model="inputs.spot_enabled" class="form-check-input">
                                    <span class="form-check-label">Spot Sistemi Aktif</span>
                                </label>
                                <div class="form-text">Kurumsal anonslar caldirilsin mi?</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kac Sarki Arasinda?</label>
                                <input type="number"
                                       wire:model="inputs.spot_songs_between"
                                       class="form-control"
                                       min="1"
                                       max="50"
                                       placeholder="5">
                                <div class="form-text">Her X sarkidan sonra spot calinir</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sag Kolon: Durum ve Kaydet --}}
            <div class="col-lg-4">
                {{-- Durum Karti --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Durum</h3>
                    </div>
                    <div class="card-body">
                        <label class="form-check form-switch mb-3">
                            <input type="checkbox" wire:model="inputs.is_active" class="form-check-input">
                            <span class="form-check-label">Hesap Aktif</span>
                        </label>
                        <div class="form-text">Pasif hesaplar sisteme erisemez</div>
                    </div>
                </div>

                {{-- Istatistikler (Sadece Edit) --}}
                @if($corporateId && $this->currentAccount)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Istatistikler</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-primary-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">{{ $this->currentAccount->children->count() }}</div>
                                        <div class="small text-muted">Sube/Uye</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-purple-lt">
                                    <div class="card-body p-2 text-center">
                                        <div class="h2 mb-0">{{ $this->currentAccount->spots->count() }}</div>
                                        <div class="small text-muted">Spot</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top text-muted small">
                            <div><i class="fas fa-calendar me-1"></i> Olusturulma: {{ $this->currentAccount->created_at?->format('d.m.Y H:i') }}</div>
                            <div><i class="fas fa-edit me-1"></i> Son Guncelleme: {{ $this->currentAccount->updated_at?->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Kaydet Butonlari --}}
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i>
                                {{ $corporateId ? 'Guncelle' : 'Olustur' }}
                            </button>
                            <button type="button" wire:click="save(true)" class="btn btn-outline-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ $corporateId ? 'Guncelle' : 'Olustur' }} ve Listeye Don
                            </button>
                            <a href="{{ route('admin.muzibu.corporate.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Iptal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
