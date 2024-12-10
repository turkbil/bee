@extends('admin.layout')
@include('page::helper')
@section('content')
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
    <form method="POST" action="{{ isset($page) ? route('admin.page.manage', $page->page_id) : route('admin.page.manage') }}">
        @csrf
        <div class="card-body">
            <div class="tab-content">
                <!-- Temel Bilgiler -->
                <div class="tab-pane fade show active" id="tabs-1">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Sayfa Başlığı" value="{{ $page->title ?? '' }}" required>
                        <label for="title">Başlık</label>
                    </div>
                    <div class="mb-3">
                        <textarea id="tinymce-mytextarea" name="body">{{ $page->body ?? '' }}</textarea>
                    </div>
                    <!-- Aktiflik Durumu (is_active) Toggle Switch -->
                    <div class="form-floating mb-3">
                        <div class="pretty p-icon p-toggle p-plain">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ isset($page) && $page->is_active ? 'checked' : '' }} />
                            <div class="state p-on">
                                <i class="icon fa-regular fa-square-check"></i>
                                <label>Aktif</label>
                            </div>
                            <div class="state p-off">
                                <i class="icon fa-regular fa-square"></i>
                                <label>Aktif Değil</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SEO -->
                <div class="tab-pane fade" id="tabs-2">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="URL Slug" value="{{ $page->slug ?? '' }}">
                        <label for="slug">URL Slug</label>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="metakey" name="metakey" data-bs-toggle="autosize" placeholder="Meta Anahtar Kelimeler">{{ $page->metakey ?? '' }}</textarea>
                                <label for="metakey">Meta Anahtar Kelimeler</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="metadesc" name="metadesc" data-bs-toggle="autosize" placeholder="Meta Açıklaması">{{ $page->metadesc ?? '' }}</textarea>
                                <label for="metadesc">Meta Açıklaması</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kod Alanı -->
                <div class="tab-pane fade" id="tabs-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="css" name="css" data-bs-toggle="autosize" placeholder="CSS">{{ $page->css ?? '' }}</textarea>
                                <label for="css">CSS</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="js" name="js" data-bs-toggle="autosize" placeholder="JS">{{ $page->js ?? '' }}</textarea>
                                <label for="js">JS</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="{{ route('admin.page.index') }}" class="btn btn-secondary">İptal</a>
        </div>
    </form>
</div>
@endsection
@push('js')
<script src="{{ asset('admin/libs/tinymce/tinymce.min.js') }}" defer></script>
<script>
// TinyMCE Editör Konfigürasyonu
document.addEventListener("DOMContentLoaded", function() {
    let options = {
        selector: '#tinymce-mytextarea',
        height: 300,
        menubar: false,
        statusbar: false,
        license_key: 'gpl',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
            'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
    }
    if (localStorage.getItem("tablerTheme") === 'dark') {
        options.skin = 'oxide-dark';
        options.content_css = 'dark';
    }
    tinyMCE.init(options);
});

</script>
@endpush
