<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Banka Hesapları</h2>
                    <div class="text-muted mt-1">Havale/EFT ödemeleri için banka hesaplarınızı yönetin</div>
                </div>
                <div class="col-auto ms-auto">
                    <button wire:click="openModal" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>
                        Yeni Hesap Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($bankAccounts->isEmpty())
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fa fa-building-columns fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Henüz banka hesabı eklenmemiş</p>
                    <p class="empty-subtitle text-muted">
                        Havale/EFT ödemelerini aktif edebilmek için en az bir banka hesabı eklemeniz gerekiyor.
                    </p>
                    <div class="empty-action">
                        <button wire:click="openModal" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>
                            İlk Hesabı Ekle
                        </button>
                    </div>
                </div>
            @else
                <div class="row row-cards">
                    @foreach($bankAccounts as $account)
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-status-top {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}"></div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fa fa-building-columns fa-2x text-muted"></i>
                                        </div>
                                        <div>
                                            <h3 class="card-title mb-0">{{ $account->bank_name }}</h3>
                                            @if($account->branch_name)
                                                <div class="text-muted small">{{ $account->branch_name }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Hesap Sahibi:</strong><br>
                                        <span>{{ $account->account_holder_name }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <strong>IBAN:</strong><br>
                                        <code class="fs-5">{{ $account->formatted_iban }}</code>
                                        <button class="btn btn-sm btn-ghost-secondary ms-1" 
                                                onclick="navigator.clipboard.writeText('{{ $account->iban }}'); alert('IBAN kopyalandı!')">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>

                                    @if($account->account_number)
                                        <div class="mb-2">
                                            <strong>Hesap No:</strong> {{ $account->account_number }}
                                        </div>
                                    @endif

                                    @if($account->swift_code)
                                        <div class="mb-2">
                                            <strong>SWIFT:</strong> {{ $account->swift_code }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <span class="badge bg-blue">{{ $account->currency }} {{ $account->currency_symbol }}</span>
                                        @if($account->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </div>

                                    @if($account->description)
                                        <div class="text-muted small mb-3">
                                            {{ Str::limit($account->description, 100) }}
                                        </div>
                                    @endif

                                    <div class="btn-group w-100">
                                        <button wire:click="edit({{ $account->bank_account_id }})" class="btn btn-sm btn-white">
                                            <i class="fa fa-edit"></i> Düzenle
                                        </button>
                                        <button wire:click="toggleActive({{ $account->bank_account_id }})" class="btn btn-sm btn-white">
                                            <i class="fa fa-{{ $account->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            {{ $account->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                        </button>
                                        <button wire:click="delete({{ $account->bank_account_id }})" 
                                                onclick="return confirm('Bu hesabı silmek istediğinize emin misiniz?')"
                                                class="btn btn-sm btn-white text-danger">
                                            <i class="fa fa-trash"></i> Sil
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Banka Hesabı Düzenle' : 'Yeni Banka Hesabı Ekle' }}</h5>
                        <button wire:click="$set('showModal', false)" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label required">Banka Adı</label>
                                    <input wire:model="bank_name" type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                           placeholder="Örn: Ziraat Bankası">
                                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label required">Para Birimi</label>
                                    <select wire:model="currency" class="form-select @error('currency') is-invalid @enderror">
                                        <option value="TRY">TRY (₺)</option>
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="RUB">RUB (₽)</option>
                                    </select>
                                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Şube Adı</label>
                                    <input wire:model="branch_name" type="text" class="form-control" placeholder="Örn: Kadıköy Şubesi">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Şube Kodu</label>
                                    <input wire:model="branch_code" type="text" class="form-control" placeholder="Örn: 1234">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Hesap Sahibi</label>
                                <input wire:model="account_holder_name" type="text" class="form-control @error('account_holder_name') is-invalid @enderror" 
                                       placeholder="Örn: ABC Ticaret Ltd. Şti.">
                                @error('account_holder_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">IBAN</label>
                                <input wire:model="iban" type="text" class="form-control @error('iban') is-invalid @enderror" 
                                       placeholder="TR00 0000 0000 0000 0000 0000 00" maxlength="34">
                                @error('iban') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-hint">Boşluksuz girebilirsiniz, sistem otomatik düzenler</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hesap Numarası</label>
                                    <input wire:model="account_number" type="text" class="form-control" placeholder="Opsiyonel">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SWIFT/BIC Kodu</label>
                                    <input wire:model="swift_code" type="text" class="form-control" placeholder="Opsiyonel (Uluslararası transferler için)">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Müşteriye Gösterilecek Açıklama</label>
                                <textarea wire:model="description" class="form-control" rows="3" 
                                          placeholder="Örn: Lütfen açıklama kısmına sipariş numaranızı yazın"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sıralama Önceliği</label>
                                    <input wire:model="sort_order" type="number" class="form-control" min="0">
                                    <small class="form-hint">Küçük sayı önce gösterilir</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Durum</label>
                                    <label class="form-check form-switch">
                                        <input wire:model="is_active" class="form-check-input" type="checkbox">
                                        <span class="form-check-label">Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('showModal', false)" type="button" class="btn btn-secondary">İptal</button>
                        <button wire:click="save" type="button" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>
                            {{ $editMode ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
