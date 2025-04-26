<div class="card p-3">
    <h4 class="mb-3">Seçenekler</h4>
    
    <div class="d-flex mb-3">
        <div class="btn-group w-100" role="group">
            <button type="button" 
                class="btn {{ $optionFormat === 'key-value' ? 'btn-primary' : 'btn-outline-primary' }}" 
                wire:click="$set('optionFormat', 'key-value')">
                <i class="fas fa-key me-1"></i> Anahtar-Değer Çiftleri
            </button>
            <button type="button" 
                class="btn {{ $optionFormat === 'text' ? 'btn-primary' : 'btn-outline-primary' }}" 
                wire:click="$set('optionFormat', 'text')">
                <i class="fas fa-font me-1"></i> Metin Olarak Gir
            </button>
        </div>
    </div>
    
    <div x-data="{}">
        <div x-show="$wire.optionFormat === 'key-value'">
            <div class="row mb-3">
                <div class="col-6">
                    <div class="fw-bold text-muted">Gözüken Seçenek</div>
                </div>
                <div class="col-6">
                    <div class="fw-bold text-muted">Anahtar (slug)</div>
                </div>
            </div>
            
            @if(isset($newField['options_array']) && is_array($newField['options_array']) && count($newField['options_array']) > 0)
            @foreach($newField['options_array'] as $id => $option)
            <div class="row g-2 mb-3">
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-font"></i>
                        </span>
                        <input type="text" class="form-control"
                            wire:model.live="newField.options_array.{{ $id }}.value"
                            wire:change="slugifyOptionKey('{{ $id }}', $event.target.value)"
                            placeholder="Gözüken Değer">
                    </div>
                </div>
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="text" class="form-control" 
                            wire:model.live="newField.options_array.{{ $id }}.key" 
                            placeholder="Anahtar"
                            title="Değiştirmek isterseniz manuel düzenleyebilirsiniz">
                    </div>
                </div>
                <div class="col-auto">
                    <a href="javascript:void(0)" class="btn btn-outline-danger btn-icon" 
                        wire:click="removeFieldOption('{{ $id }}')" 
                        title="Sil">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            @endforeach
            @else
                <div class="text-muted text-center py-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Henüz seçenek eklenmemiş
                </div>
            @endif
            
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addFieldOption">
                    <i class="fas fa-plus me-1"></i> Seçenek Ekle
                </button>
            </div>
        </div>
        
        <div x-show="$wire.optionFormat === 'text'" class="mt-3">
            <label class="form-label mb-2">Her satıra bir seçenek yazın:</label>
            <textarea wire:model.live.debounce.500ms="newField.options"
                class="form-control @error('newField.options') is-invalid @enderror" rows="6"
                placeholder="Her satıra bir seçenek yazın:
erkek=Erkek
kadin=Kadın
diger=Diğer

veya sadece:
Erkek
Kadın
Diğer"></textarea>
            <small class="form-hint">
                Her satıra bir seçenek. Örnek: "erkek=Erkek" veya sadece "Erkek" yazabilirsiniz. Seçenek anahtarı otomatik olarak slug'a çevrilecektir.</small>
        </div>
    </div>
</div>