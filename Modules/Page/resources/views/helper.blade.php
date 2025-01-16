{{-- PreTitle --}}
@push('pretitle')
    Sayfalar
@endpush

{{-- Başlık --}}
@push('title')
    Sayfa Listesi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
    <ul class="nav">
        <li class="nav-item">
            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
                Yeni Sayfa
            </a>
        </li>
    </ul>
@endpush