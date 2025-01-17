@include('portfolio::helper')
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
                    <li class="nav-item">
                        <a href="#tabs-3" class="nav-link" data-bs-toggle="tab">Kod Alanı</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="Sayfa başlığı">
                            <label>Başlık</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group mb-3">
                            <label for="image">Görsel</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                wire:model="image">
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-floating mb-3">
                            <textarea wire:model.defer="inputs.body"
                                class="form-control @error('inputs.body') is-invalid @enderror"
                                placeholder="Sayfa içeriği" style="height: 300px; min-height: 300px; resize: vertical;">
                            </textarea>
                            <label>İçerik</label>
                            @error('inputs.body')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-thick p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" @if($inputs['is_active']) checked @endif />
                                <div class="state ms-2">
                                    <label>Aktif / Online</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.slug"
                                class="form-control @error('inputs.slug') is-invalid @enderror" placeholder="Slug">
                            <label>Slug</label>
                            @error('inputs.slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.metakey"
                                class="form-control @error('inputs.metakey') is-invalid @enderror"
                                placeholder="Meta anahtar kelimeler">
                            <label>Meta Anahtar Kelimeler</label>
                            @error('inputs.metakey')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea wire:model.defer="inputs.metadesc"
                                class="form-control @error('inputs.metadesc') is-invalid @enderror"
                                data-bs-toggle="autosize" placeholder="Meta açıklaması"></textarea>
                            <label>Meta Açıklama</label>
                            @error('inputs.metadesc')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Kod Alanı -->
                    <div class="tab-pane fade" id="tabs-3">
                        <div class="form-floating mb-3">
                            <textarea wire:model.defer="inputs.css"
                                class="form-control @error('inputs.css') is-invalid @enderror" data-bs-toggle="autosize"
                                placeholder="CSS kodları"></textarea>
                            <label>CSS</label>
                            @error('inputs.css')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea wire:model.defer="inputs.js"
                                class="form-control @error('inputs.js') is-invalid @enderror" data-bs-toggle="autosize"
                                placeholder="JS kodları"></textarea>
                            <label>JS</label>
                            @error('inputs.js')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.portfolio.index') }}" class="btn">İptal</a>

                <div class="d-flex gap-2">
                    @if($portfolioId)
                    <!-- Kaydet ve Devam Et -->
                    <button type="button" class="btn" wire:click="save(false, false)" wire:loading.attr="disabled"
                        wire:target="save(false, false)">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, false)"><i
                                    class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et</span>
                            <span class="ms-2" wire:loading wire:target="save(false, false)"><i
                                    class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et</span>
                        </span>
                    </button>
                    @else
                    <!-- Kaydet ve Yeni Ekle -->
                    <button type="button" class="btn" wire:click="save(false, true)" wire:loading.attr="disabled"
                        wire:target="save(false, true)">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, true)"><i
                                    class="fa-thin fa-plus me-2"></i> Kaydet ve Yeni Ekle</span>
                            <span class="ms-2" wire:loading wire:target="save(false, true)"><i
                                    class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Yeni Ekle</span>
                        </span>
                    </button>
                    @endif

                    <!-- Kaydet -->
                    <button type="button" class="btn btn-primary ms-4" wire:click="save(true, false)"
                        wire:loading.attr="disabled" wire:target="save(true, false)">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(true, false)"> <i
                                    class="fa-thin fa-floppy-disk me-2"></i> Kaydet</span>
                            <span class="ms-2" wire:loading wire:target="save(true, false)"><i
                                    class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>