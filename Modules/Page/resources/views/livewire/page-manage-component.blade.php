<div>
    @section('pretitle', 'Sayfalar')
    @section('title', $page ? 'Sayfa Düzenle' : 'Yeni Sayfa')

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
                <div class="tab-pane fade show active" id="tabs-1">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" wire:model="title" class="form-control" placeholder="Sayfa Başlığı">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea wire:model="body" class="form-control" rows="10"></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="tabs-2">
                    <div class="mb-3">
                        <label class="form-label">URL Slug</label>
                        <input type="text" wire:model="slug" class="form-control" placeholder="URL Slug">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Meta Anahtar Kelimeler</label>
                                <textarea wire:model="metakey" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Meta Açıklaması</label>
                                <textarea wire:model="metadesc" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tabs-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">CSS</label>
                                <textarea wire:model="css" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">JS</label>
                                <textarea wire:model="js" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.page.index') }}" class="btn btn-secondary">İptal</a>
        </div>
    </div>
</div>
