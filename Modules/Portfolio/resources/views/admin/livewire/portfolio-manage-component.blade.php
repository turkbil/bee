@include('portfolio::admin.helper')
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
                                placeholder="Portfolyo başlığı">
                            <label>Başlık</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.portfolio_category_id"
                                class="form-select tomselect @error('inputs.portfolio_category_id') is-invalid @enderror">
                                <option value="">Kategori Seçin</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->portfolio_category_id }}" {{ $category->
                                    portfolio_category_id == $inputs['portfolio_category_id'] ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                                @endforeach
                            </select>
                            <label>Kategori</label>
                            @error('inputs.portfolio_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @include('portfolio::admin.partials.image-upload', [
                        'imageKey' => 'image',
                        'label' => 'Görseli sürükleyip bırakın veya tıklayın'
                        ])

                        <div class="mb-3" wire:ignore>
                            <textarea id="editor" wire:model.defer="inputs.body">{{ $inputs['body'] }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Aktif Değil </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.slug" class="form-control" placeholder="Slug">
                            <label>Slug</label>
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

            <x-form-footer route="admin.portfolio" :model-id="$portfolioId" />

        </div>
    </form>
</div>