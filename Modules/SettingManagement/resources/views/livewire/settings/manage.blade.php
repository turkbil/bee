@include('settingmanagement::helper')

<form wire:submit="save">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">Temel Bilgiler</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">Ayar Detayları</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade active show" id="tabs-1">
                    <div class="form-floating mb-3">
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
                        <label class="form-label required">Grup</label>
                        @error('inputs.group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" wire:model.live="inputs.label"
                            class="form-control @error('inputs.label') is-invalid @enderror"
                            placeholder="Başlık">
                        <label class="form-label required">Başlık</label>
                        @error('inputs.label')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="text" wire:model="inputs.key"
                                class="form-control @error('inputs.key') is-invalid @enderror"
                                placeholder="sistem_icin_benzersiz_anahtar">
                        </div>
                        <small class="form-hint">Sadece harf, rakam ve alt çizgi (_) kullanabilirsiniz. Grup adı otomatik önek olarak eklenecektir.</small>
                        @error('inputs.key')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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

                <div class="tab-pane fade" id="tabs-2">
                    <div class="mb-3">
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
                    </div> @endif

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
                    
                    <div class="mb-3">
                        <label class="form-label required">Sıralama</label>
                        <input type="number" wire:model="inputs.sort_order"
                            class="form-control @error('inputs.sort_order') is-invalid @enderror">
                        @error('inputs.sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ url()->previous() }}" class="btn">
                <i class="fas fa-arrow-left me-2"></i>İptal
            </a>
            <div class="btn-list">
                <button type="submit" class="btn btn-success" wire:click="$set('redirect', false)">
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