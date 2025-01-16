@include('user::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">Temel Bilgiler</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text"
                                   wire:model.defer="inputs.name"
                                   class="form-control @error('inputs.name') is-invalid @enderror"
                                   placeholder="Kullanıcı adı">
                            <label>İsim</label>
                            @error('inputs.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email"
                                   wire:model.defer="inputs.email"
                                   class="form-control @error('inputs.email') is-invalid @enderror"
                                   placeholder="E-posta adresi">
                            <label>E-posta</label>
                            @error('inputs.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password"
                                   wire:model.defer="inputs.password"
                                   class="form-control @error('inputs.password') is-invalid @enderror"
                                   placeholder="Şifre">
                            <label>Şifre</label>
                            @error('inputs.password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-thick p-smooth ms-1">
                                <input type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       wire:model="inputs.is_active"
                                       value="1"
                                       @if($inputs['is_active']) checked @endif />
                                <div class="state ms-2">
                                    <label>Aktif / Online</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.user.index') }}" class="btn">İptal</a>

                <div class="d-flex gap-2">
                    @if($userId)
                        <!-- Kaydet ve Devam Et -->
                        <button type="button" class="btn"
                                wire:click="save(false, false)"
                                wire:loading.attr="disabled"
                                wire:target="save(false, false)">
                            <span class="d-flex align-items-center">
                                <span class="ms-2" wire:loading.remove wire:target="save(false, false)"><i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et</span>
                                <span class="ms-2" wire:loading wire:target="save(false, false)"><i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et</span>
                            </span>
                        </button>
                    @else
                        <!-- Kaydet ve Yeni Ekle -->
                        <button type="button" class="btn"
                                wire:click="save(false, true)"
                                wire:loading.attr="disabled"
                                wire:target="save(false, true)">
                            <span class="d-flex align-items-center">
                                <span class="ms-2" wire:loading.remove wire:target="save(false, true)"><i class="fa-thin fa-plus me-2"></i> Kaydet ve Yeni Ekle</span>
                                <span class="ms-2" wire:loading wire:target="save(false, true)"><i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Yeni Ekle</span>
                            </span>
                        </button>
                    @endif

                    <!-- Kaydet -->
                    <button type="button" class="btn btn-primary ms-4"
                            wire:click="save(true, false)"
                            wire:loading.attr="disabled"
                            wire:target="save(true, false)">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(true, false)"> <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet</span>
                            <span class="ms-2" wire:loading wire:target="save(true, false)"><i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>