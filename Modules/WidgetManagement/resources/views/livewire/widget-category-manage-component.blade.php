@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">Temel Bilgiler</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">SEO</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="Kategori başlığı">
                            <label>Başlık</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" wire:ignore>
                            <textarea id="editor" wire:model.defer="inputs.description">{{ $inputs['description'] }}</textarea>
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

                    <!-- SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.slug"
                                class="form-control @error('inputs.slug') is-invalid @enderror" placeholder="Slug">
                            <label>Slug</label>
                            @error('inputs.slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3" wire:ignore>
                            <select wire:model="inputs.metakey" class="form-select tags"
                                placeholder="Meta anahtar kelimeler" multiple>
                                @if($inputs['metakey'])
                                    @foreach(is_array($inputs['metakey']) ? $inputs['metakey'] : explode(',', $inputs['metakey']) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <label>Meta Anahtar Kelimeler</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.metadesc" class="form-control" data-bs-toggle="autosize"
                                placeholder="Meta açıklaması"></textarea>
                            <label>Meta Açıklama</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="card-footer">
                <div wire:loading class="position-fixed top-0 start-0 w-100" style="z-index: 1050;" wire:target="save">
                    <div class="progress rounded-0" style="height: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-indeterminate bg-primary"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.widgetmanagement.category.index') }}" class="btn btn-link text-decoration-none">İptal</a>

                    <div class="d-flex gap-2">
                        @if($categoryId)
                        <button type="button" class="btn" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save">
                            <span class="d-flex align-items-center">
                                <span class="ms-2" wire:loading.remove wire:target="save(false)">
                                    <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                                </span>
                                <span class="ms-2" wire:loading wire:target="save(false)">
                                    <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                                </span>
                            </span>
                        </button>
                        @endif

                        <button type="button" class="btn btn-primary ms-4" wire:click="save(true)"
                            wire:loading.attr="disabled" wire:target="save">
                            <span class="d-flex align-items-center">
                                <span class="ms-2" wire:loading.remove wire:target="save(true)">
                                    <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                                </span>
                                <span class="ms-2" wire:loading wire:target="save(true)">
                                    <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                                </span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        if (document.getElementById('editor')) {
            initEditor('editor');
        }
        
        function initEditor(editorId) {
            ClassicEditor
                .create(document.getElementById(editorId), {
                    language: 'tr',
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'outdent',
                            'indent',
                            '|',
                            'blockQuote',
                            'insertTable',
                            'mediaEmbed',
                            'undo',
                            'redo'
                        ]
                    }
                })
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        @this.set('inputs.description', editor.getData());
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>
@endpush