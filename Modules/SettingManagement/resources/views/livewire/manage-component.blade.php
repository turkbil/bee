@include('settingmanagement::helper')
@include('admin.partials.error_message')

<div class="card">
    <div class="card-body">
        <form wire:submit="save">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs me-2"></i>
                                Ayar Bilgileri
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Grup</label>
                                    <select wire:model.live="inputs.group_id"
                                        class="form-select @error('inputs.group_id') is-invalid @enderror">
                                        <option value="">Grup Seçin</option>
                                        @foreach($groups->sortBy('name') as $group)
                                        @if(is_null($group->parent_id))
                                        <option disabled>{{ $group->name }}</option>
                                        @foreach($groups->sortBy('name') as $subGroup)
                                        @if($subGroup->parent_id === $group->id)
                                        <option value="{{ $subGroup->id }}">--- {{ $subGroup->name }}</option>
                                        @endif
                                        @endforeach
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('inputs.group_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tip</label>
                                    <select wire:model.live="inputs.type"
                                        class="form-select @error('inputs.type') is-invalid @enderror">
                                        <option value="text">Metin</option>
                                        <option value="textarea">Uzun Metin</option>
                                        <option value="number">Sayı</option>
                                        <option value="select">Seçim Kutusu</option>
                                        <option value="checkbox">Onay Kutusu</option>
                                        <option value="file">Dosya</option>
                                        <option value="color">Renk</option>
                                        <option value="date">Tarih</option>
                                        <option value="email">E-posta</option>
                                        <option value="password">Şifre</option>
                                        <option value="tel">Telefon</option>
                                        <option value="url">URL</option>
                                        <option value="time">Saat</option>
                                    </select>
                                    @error('inputs.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Başlık</label>
                                    <input type="text" wire:model.live="inputs.label"
                                        class="form-control @error('inputs.label') is-invalid @enderror"
                                        placeholder="Başlık">
                                    @error('inputs.label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Anahtar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="text" wire:model="inputs.key"
                                            class="form-control @error('inputs.key') is-invalid @enderror"
                                            placeholder="sistem_icin_benzersiz_anahtar">
                                    </div>
                                    <small class="form-hint">Harf, rakam ve alt çizgi (_) kullanabilirsiniz.</small>
                                    @error('inputs.key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if($inputs['type'] === 'select')
                            <div class="mb-3">
                                <label class="form-label required">Seçenekler</label>
                                <textarea wire:model="inputs.options"
                                    class="form-control @error('inputs.options') is-invalid @enderror" rows="4"
                                    placeholder="erkek=Erkek
kadin=Kadın
diger=Diğer

veya sadece:
Erkek
Kadın
Diğer"></textarea>
                                <small class="form-hint">
                                    Her satıra bir seçenek. Örnek: erkek=Erkek veya sadece Erkek
                                    yazabilirsiniz.</small>
                                @error('inputs.options')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> 
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Varsayılan Değer</label>
                                @if($inputs['type'] === 'textarea')
                                <textarea wire:model="inputs.default_value"
                                    class="form-control @error('inputs.default_value') is-invalid @enderror"
                                    rows="4"></textarea>
                                @elseif($inputs['type'] === 'file')
                                <input type="file" wire:model="tempFile"
                                    class="form-control @error('inputs.default_value') is-invalid @enderror">
                                @if($inputs['default_value'])
                                <div class="mt-2">
                                    @if(Str::startsWith($inputs['default_value'], ['jpg', 'jpeg', 'png',
                                    'gif']))
                                    <img src="{{ Storage::url($inputs['default_value']) }}" alt="Current file"
                                        class="img-fluid" style="max-height: 100px">
                                    @else
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-2"></i>
                                        <span>{{ basename($inputs['default_value']) }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                @elseif($inputs['type'] === 'checkbox')
                                <div class="pretty p-default p-curve p-toggle p-smooth">
                                    <input type="checkbox" 
                                        class="form-check-input @error('inputs.default_value') is-invalid @enderror"
                                        wire:model="inputs.default_value">
                                    <div class="state p-success p-on">
                                        <label>Evet</label>
                                    </div>
                                    <div class="state p-danger p-off">
                                        <label>Hayır</label>
                                    </div>
                                </div>
                                @elseif($inputs['type'] === 'select' && !empty($inputs['options']))
                                <select wire:model="inputs.default_value"
                                    class="form-select @error('inputs.default_value') is-invalid @enderror">
                                    <option value="">Seçiniz</option>
                                    @foreach((array)$inputs['options'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input type="{{ $inputs['type'] }}" wire:model="inputs.default_value"
                                    class="form-control @error('inputs.default_value') is-invalid @enderror">
                                @endif
                                @error('inputs.default_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog me-2"></i>
                                Ayarlar
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Aktiflik Durumu -->
                            <div class="mb-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Aktif Değil</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-link text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>İptal
                </a>

                <div class="d-flex gap-2">
                    @if($settingId)
                    <button type="button" class="btn" wire:click="save(false, false)" wire:loading.attr="disabled"
                        wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, false)">
                                <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(false, false)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                            </span>
                        </span>
                    </button>
                    @else
                    <button type="button" class="btn" wire:click="save(false, true)" wire:loading.attr="disabled"
                        wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, true)">
                                <i class="fa-thin fa-plus me-2"></i> Kaydet ve Yeni Ekle
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(false, true)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Yeni Ekle
                            </span>
                        </span>
                    </button>
                    @endif

                    <button type="button" class="btn btn-primary ms-4" wire:click="save(true, false)"
                        wire:loading.attr="disabled" wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(true, false)">
                                <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(true, false)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-label.required:after {
        content: " *";
        color: red;
    }
</style>
@endpush