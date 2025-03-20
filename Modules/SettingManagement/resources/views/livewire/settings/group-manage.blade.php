@include('settingmanagement::helper')
<form wire:submit="save">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Sol Kolon - Ana Bilgiler -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-folder me-2"></i>
                                Grup Bilgileri
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Grup Adı -->
                                <div class="col-md-12">
                                    <label class="form-label required">Grup Adı</label>
                                    <input type="text" wire:model="inputs.name"
                                        class="form-control @error('inputs.name') is-invalid @enderror"
                                        placeholder="Grup adını girin">
                                    @error('inputs.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Üst Grup -->
                                <div class="col-md-12">
                                    <label class="form-label">Üst Grup</label>
                                    <select wire:model="inputs.parent_id"
                                        class="form-select @error('inputs.parent_id') is-invalid @enderror">
                                        <option value="">Ana Grup Olarak Ekle</option>
                                        @foreach($parentGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('inputs.parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Açıklama -->
                                <div class="col-md-12">
                                    <label class="form-label">Açıklama</label>
                                    <textarea wire:model="inputs.description"
                                        class="form-control @error('inputs.description') is-invalid @enderror" rows="3"
                                        placeholder="Grup hakkında kısa açıklama"></textarea>
                                    @error('inputs.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon - Ayarlar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-settings me-2"></i>
                                Ayarlar
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Aktiflik Durumu -->
                            <div class="form-group">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:model="inputs.is_active"
                                        value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Aktif Değil</label>
                                    </div>
                                </div>
                            </div>

                            <!-- İkon -->
                            <div class="form-group mt-3">
                                <label class="form-label">İkon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="{{ $inputs['icon'] ?: 'fas fa-folder' }}"></i>
                                    </span>
                                    <input type="text" wire:model="inputs.icon"
                                        class="form-control @error('inputs.icon') is-invalid @enderror"
                                        placeholder="fas fa-icon">
                                </div>
                                <small class="text-muted">FontAwesome ikon sınıfını girin (örn. fas fa-cog)</small>
                                @error('inputs.icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn">
                <i class="fas fa-arrow-left me-2"></i>Geri Dön
            </a>
            <div class="btn-list">
                <button type="submit" class="btn" wire:click="$set('redirect', false)">
                    <i class="fas fa-save me-2"></i>Kaydet ve Devam Et
                </button>
                <button type="submit" class="btn btn-primary" wire:click="$set('redirect', true)">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</form>

@push('styles')
<style>
    .form-label.required:after {
        content: " *";
        color: red;
    }
</style>
@endpush