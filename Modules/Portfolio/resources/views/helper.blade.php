{{-- PreTitle --}}
@push('pretitle')
Portfolyolar
@endpush

{{-- Başlık --}}
@push('title')
Portfolyo Listesi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<ul class="nav">
    <li class="nav-item">
        <a href="{{ route('admin.portfolios.manage') }}" class="btn btn-primary">
            Yeni Portfolyo
        </a>
    </li>
</ul>
@endpush