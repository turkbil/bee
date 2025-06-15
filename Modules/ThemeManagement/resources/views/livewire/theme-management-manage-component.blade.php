@include('thememanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tema Bilgileri</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Temel Bilgiler -->
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.name"
                                class="form-control @error('inputs.name') is-invalid @enderror"
                                placeholder="Tema kodu">
                            <label>Tema Kodu</label>
                            @error('inputs.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Örnek: default, dark, blue, modern (Aynı zamanda klasör adı olarak da kullanılacak)</small>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="Tema başlığı">
                            <label>Tema Başlığı</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Örnek: Varsayılan Tema, Koyu Tema, Mavi Tema</small>
                        </div>
                        
                        <!-- Gizli alan olarak folder_name'i name ile aynı yap -->
                        <input type="hidden" wire:model="inputs.folder_name" value="{{ $inputs['name'] }}">
                        
                        <!-- Aktif/Varsayılan Durum -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth">
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
                            
                            <div class="col-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth">
                                    <input type="checkbox" id="is_default" name="is_default" wire:model="inputs.is_default"
                                        value="1" {{ $inputs['is_default'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>Varsayılan Tema</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Varsayılan Değil</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tema Görseli -->
                        <div class="mb-4">
                            <h4 class="form-label">Tema Önizleme Görseli</h4>
                            @include('thememanagement::livewire.partials.image-upload', [
                                'imageKey' => 'thumbnail',
                                'label' => 'Önizleme görselini sürükleyip bırakın veya tıklayın'
                            ])
                        </div>
                        
                        <!-- Tema Açıklaması -->
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.description" class="form-control" data-bs-toggle="autosize" rows="5" 
                                placeholder="Tema hakkında detaylı açıklama"></textarea>
                            <label>Tema Açıklaması</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.thememanagement" :model-id="$themeId" />

        </div>
    </form>
</div>