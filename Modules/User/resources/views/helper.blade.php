{{-- PreTitle --}}
@push('pretitle')
Kullanıcılar
@endpush

{{-- Başlık --}}
@push('title')
Kullanıcı Listesi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
    <ul class="nav">
        <li class="nav-item">
            <a href="{{ route('admin.user.manage') }}" class="btn btn-primary">
                Yeni Kullanıcı
            </a>
        </li>
    </ul>
@endpush