<div class="certificate-manage-wrapper">
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Sol Kolon: Ana Bilgiler -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sertifika Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        <!-- Kullanici Secimi -->
                        <div class="mb-3">
                            <label class="form-label required">Kullanici</label>
                            <div class="position-relative">
                                <input type="text" wire:model.live.debounce.300ms="userSearch"
                                    wire:focus="$set('showUserDropdown', true)"
                                    class="form-control @error('user_id') is-invalid @enderror"
                                    placeholder="Kullanici adi veya e-posta ile ara...">

                                @if($showUserDropdown && count($users) > 0)
                                <div class="dropdown-menu show w-100" style="position: absolute; top: 100%; z-index: 1000;">
                                    @foreach($users as $u)
                                    <button type="button" wire:click="selectUser({{ $u->id }})" class="dropdown-item">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-primary-lt">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $u->name }}</div>
                                                <div class="small text-muted">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Uye Adi (Sirket/Kisi) -->
                        <div class="mb-3">
                            <label class="form-label required">Uye Adi (Sertifikada Gorunecek)</label>
                            <input type="text" wire:model="member_name"
                                class="form-control @error('member_name') is-invalid @enderror"
                                placeholder="Sirket adi veya kisi adi">
                            @error('member_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Otomatik olarak buyuk/kucuk harf duzeltmesi yapilir</div>
                        </div>

                        <!-- Vergi Bilgileri -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vergi Dairesi</label>
                                <input type="text" wire:model="tax_office"
                                    class="form-control @error('tax_office') is-invalid @enderror"
                                    placeholder="Ornek: Besiktas">
                                @error('tax_office')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vergi Numarasi</label>
                                <input type="text" wire:model="tax_number"
                                    class="form-control @error('tax_number') is-invalid @enderror"
                                    placeholder="10 veya 11 haneli">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Adres -->
                        <div class="mb-3">
                            <label class="form-label required">Adres</label>
                            <textarea wire:model="address" rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Tam adres bilgisi"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sag Kolon: Durum ve Kaydet -->
            <div class="col-lg-4">
                <!-- Abonelik Bilgileri (Otomatik) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Abonelik Bilgileri</h3>
                        <span class="badge bg-info-lt ms-2">Otomatik</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Uyelik Baslangici</label>
                            <div class="form-control-plaintext">
                                @if($membership_start)
                                    <span class="badge bg-success-lt">{{ $membership_start }}</span>
                                @else
                                    <span class="text-muted">Kullanici secilmedi</span>
                                @endif
                            </div>
                            <div class="form-text">Ilk ucretli abonelik tarihi</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Abonelik Bitis</label>
                            <div class="form-control-plaintext">
                                @if($subscription_expires_at)
                                    <span class="badge bg-primary-lt">{{ $subscription_expires_at }}</span>
                                @else
                                    <span class="text-muted">Aktif abonelik yok</span>
                                @endif
                            </div>
                            <div class="form-text">Dinamik - abonelik yenilenince guncellenir</div>
                        </div>
                    </div>
                </div>

                <!-- Durum Karti -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Durum</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model="is_valid" class="form-check-input">
                                <span class="form-check-label">Sertifika Gecerli</span>
                            </label>
                            <div class="form-text">Kapatilirsa sertifika iptal edilir</div>
                        </div>
                    </div>
                </div>

                <!-- Sertifika Bilgileri (Sadece Edit) -->
                @if($certificateId && $this->certificate)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sertifika Detaylari</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Sertifika Kodu:</strong>
                            <span class="badge bg-warning-lt">{{ $this->certificate->certificate_code }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>QR Goruntulenme:</strong>
                            <span class="badge bg-secondary">{{ $this->certificate->view_count ?? 0 }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Olusturulma:</strong>
                            <span class="text-muted">{{ $this->certificate->issued_at?->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ $this->certificate->getVerificationUrl() }}"
                                target="_blank" class="btn btn-outline-primary w-100">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Dogrulama Sayfasi
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Kaydet Butonlari -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i>
                                {{ $certificateId ? 'Guncelle' : 'Olustur' }}
                            </button>
                            <a href="{{ route('admin.muzibu.certificate.index') }}" class="btn btn-outline-secondary">
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
