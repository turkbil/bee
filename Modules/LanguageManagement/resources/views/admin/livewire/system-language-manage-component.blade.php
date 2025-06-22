<div>
@include('languagemanagement::admin.helper')
@include('admin.partials.error_message')

<form wire:submit.prevent="save">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $isEditing ? 'Sistem Dili Düzenle' : 'Yeni Sistem Dili' }}</h3>
            @if($isEditing && in_array(strtolower($code), ['tr', 'en']))
            <div class="card-actions">
                <span class="badge bg-warning-lt">
                    <i class="fas fa-shield-alt me-1"></i>
                    Korumalı Dil
                </span>
            </div>
            @endif
        </div>
        
        <div class="card-body">
            <div class="row g-5">
                <!-- Sol Kolon - Ana Bilgiler -->
                <div class="col-xl-8">
                    <!-- Dil Kodu -->
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="code" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" placeholder="tr, en, de, fr..." maxlength="10"
                               {{ $isEditing && in_array(strtolower($code), ['tr', 'en']) ? 'readonly' : '' }}>
                        <label for="code">Dil Kodu <span class="text-danger">*</span></label>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Küçük harflerle, maksimum 10 karakter (örn: tr, en, de)</div>
                    </div>

                    <!-- İngilizce Adı -->
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" placeholder="Turkish, English, German...">
                        <label for="name">İngilizce Adı <span class="text-danger">*</span></label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Yerel Adı -->
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="native_name" 
                               class="form-control @error('native_name') is-invalid @enderror" 
                               id="native_name" placeholder="Türkçe, English, Deutsch...">
                        <label for="native_name">Yerel Adı <span class="text-danger">*</span></label>
                        @error('native_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Dilin kendi adıyla yazılması</div>
                    </div>

                    <!-- Metin Yönü -->
                    <div class="form-floating mb-3">
                        <select wire:model="direction" 
                                class="form-select @error('direction') is-invalid @enderror" 
                                id="direction"
                                data-choices
                                data-choices-search="false">
                            <option value="ltr">Soldan Sağa (LTR)</option>
                            <option value="rtl">Sağdan Sola (RTL)</option>
                        </select>
                        <label for="direction">Metin Yönü <span class="text-danger">*</span></label>
                        @error('direction')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bayrak İkonu -->
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="flag_icon" 
                               class="form-control @error('flag_icon') is-invalid @enderror" 
                               id="flag_icon" placeholder="🇹🇷, 🇺🇸, 🇩🇪..." maxlength="10">
                        <label for="flag_icon">Bayrak İkonu</label>
                        @error('flag_icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Emoji bayrak kodu (opsiyonel)</div>
                    </div>
                </div>

                <!-- Sağ Kolon - Yan Panel -->
                <div class="col-xl-4">
                    <!-- Durum Ayarları -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">Durum Ayarları</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth">
                                    <input type="checkbox" wire:model="is_active" id="is_active"
                                           {{ $isEditing && in_array(strtolower($code), ['tr', 'en']) ? 'disabled' : '' }}>
                                    <div class="state p-success p-on">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off">
                                        <label>Pasif</label>
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    @if($isEditing && in_array(strtolower($code), ['tr', 'en']))
                                        <span class="text-warning">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Temel diller deaktive edilemez
                                        </span>
                                    @else
                                        Pasif diller admin panelinde görünmez
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Önizleme -->
                    @if($native_name || $flag_icon)
                        <div class="card border-0 bg-azure-lt">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fas fa-eye me-2"></i>
                                    Önizleme
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    @if($flag_icon)
                                        <span class="avatar me-3" style="font-size: 1.5rem;">{{ $flag_icon }}</span>
                                    @endif
                                    <div class="text-{{ $direction === 'rtl' ? 'end' : 'start' }}">
                                        <div class="fw-bold">{{ $native_name ?: 'Yerel Adı' }}</div>
                                        @if($name)
                                            <small class="text-muted">{{ $name }}</small>
                                        @endif
                                        @if($code)
                                            <div class="mt-1">
                                                <span class="badge bg-primary-lt">{{ strtoupper($code) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <x-form-footer route="admin.languagemanagement.system" :model-id="$languageId" />
    </div>
</form>
</div>