@section('pretitle', 'Sayfalar') {{-- Modül Adı --}}
@section('title', 'Sayfa Listesi') {{-- Sayfa Başlığı --}}
{{-- Dinamik Modül Menüsü --}}
@section('module-menu')
<ul class="nav">
    <li class="nav-item">
        <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
            Yeni Sayfa
        </a>
    </li>
</ul>
@endsection
