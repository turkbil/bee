{{-- PreTitle --}}
@section('pretitle')
{{ __('muzibu::admin.muzibu') }}
@endsection

@push('pretitle')
{{ __('muzibu::admin.muzibu') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('muzibu::admin.muzibu_management') }}
@endpush

{{-- Modül Menüsü - Modern & Responsive --}}
@push('module-menu')
<div class="module-menu-wrapper">
    {{-- Desktop Menü --}}
    <div class="d-none d-lg-flex align-items-center gap-2">
        {{-- İçerik Yönetimi --}}
        <div class="dropdown">
            <button type="button" class="btn btn-ghost-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-music me-1"></i>
                İçerik
            </button>
            <div class="dropdown-menu dropdown-menu-arrow">
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.song.index') }}">
                    <i class="fas fa-music me-2 text-primary"></i>Şarkılar
                </a>
                @endhasmoduleaccess
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.album.index') }}">
                    <i class="fas fa-compact-disc me-2 text-info"></i>Albümler
                </a>
                @endhasmoduleaccess
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.genre.index') }}">
                    <i class="fas fa-guitar me-2 text-warning"></i>Türler
                </a>
                @endhasmoduleaccess
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.artist.index') }}">
                    <i class="fas fa-user-music me-2 text-purple"></i>Sanatçılar
                </a>
                @endhasmoduleaccess
            </div>
        </div>

        {{-- Yayın --}}
        <div class="dropdown">
            <button type="button" class="btn btn-ghost-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-broadcast-tower me-1"></i>
                Yayın
            </button>
            <div class="dropdown-menu dropdown-menu-arrow">
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.index') }}">
                    <i class="fas fa-list-music me-2 text-success"></i>Çalma Listeleri
                </a>
                @endhasmoduleaccess
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.radio.index') }}">
                    <i class="fas fa-broadcast-tower me-2 text-danger"></i>Radyolar
                </a>
                @endhasmoduleaccess
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.sector.index') }}">
                    <i class="fas fa-layer-group me-2 text-azure"></i>Sektörler
                </a>
                @endhasmoduleaccess
            </div>
        </div>

        {{-- Kurumsal --}}
        @hasmoduleaccess('muzibu', 'view')
        <a href="{{ route('admin.muzibu.corporate.index') }}" class="btn btn-ghost-primary">
            <i class="fas fa-building me-1"></i>
            Kurumsal
        </a>
        @endhasmoduleaccess

        {{-- Toplu HLS Dönüştür --}}
        @hasmoduleaccess('muzibu', 'create')
        <a href="{{ route('admin.muzibu.song.bulk-convert') }}" class="btn btn-ghost-primary">
            <i class="fas fa-cog me-1"></i>
            HLS Dönüştür
        </a>
        @endhasmoduleaccess

        {{-- Hızlı Ekle --}}
        @hasmoduleaccess('muzibu', 'create')
        <div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-plus me-1"></i>
                Ekle
            </button>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('admin.muzibu.song.manage') }}">
                    <i class="fas fa-music me-2"></i>Yeni Şarkı
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.album.manage') }}">
                    <i class="fas fa-compact-disc me-2"></i>Yeni Albüm
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.genre.manage') }}">
                    <i class="fas fa-guitar me-2"></i>Yeni Tür
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.artist.manage') }}">
                    <i class="fas fa-user-music me-2"></i>Yeni Sanatçı
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.manage') }}">
                    <i class="fas fa-list-music me-2"></i>Yeni Playlist
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.radio.manage') }}">
                    <i class="fas fa-broadcast-tower me-2"></i>Yeni Radyo
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.sector.manage') }}">
                    <i class="fas fa-layer-group me-2"></i>Yeni Sektör
                </a>
            </div>
        </div>
        @endhasmoduleaccess
    </div>

    {{-- Mobile Menü --}}
    <div class="d-lg-none">
        <div class="dropdown w-100">
            <button type="button" class="btn btn-outline-primary w-100 dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-bars me-2"></i>
                Muzibu Menü
            </button>
            <div class="dropdown-menu dropdown-menu-arrow w-100">
                {{-- İçerik --}}
                <h6 class="dropdown-header">
                    <i class="fas fa-music me-1"></i> İçerik
                </h6>
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.song.index') }}">
                    <i class="fas fa-music me-2 text-primary"></i>Şarkılar
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.album.index') }}">
                    <i class="fas fa-compact-disc me-2 text-info"></i>Albümler
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.genre.index') }}">
                    <i class="fas fa-guitar me-2 text-warning"></i>Türler
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.artist.index') }}">
                    <i class="fas fa-user-music me-2 text-purple"></i>Sanatçılar
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.muzibu.song.bulk-convert') }}">
                    <i class="fas fa-cog me-2 text-orange"></i>Toplu HLS Dönüştür
                </a>
                @endhasmoduleaccess

                <div class="dropdown-divider"></div>

                {{-- Yayın --}}
                <h6 class="dropdown-header">
                    <i class="fas fa-broadcast-tower me-1"></i> Yayın
                </h6>
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.index') }}">
                    <i class="fas fa-list-music me-2 text-success"></i>Çalma Listeleri
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.radio.index') }}">
                    <i class="fas fa-broadcast-tower me-2 text-danger"></i>Radyolar
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.sector.index') }}">
                    <i class="fas fa-layer-group me-2 text-azure"></i>Sektörler
                </a>
                @endhasmoduleaccess

                <div class="dropdown-divider"></div>

                {{-- Kurumsal --}}
                <h6 class="dropdown-header">
                    <i class="fas fa-building me-1"></i> Kurumsal
                </h6>
                @hasmoduleaccess('muzibu', 'view')
                <a class="dropdown-item" href="{{ route('admin.muzibu.corporate.index') }}">
                    <i class="fas fa-building me-2 text-blue"></i>Kurumsal Hesaplar
                </a>
                @endhasmoduleaccess

                @hasmoduleaccess('muzibu', 'create')
                <div class="dropdown-divider"></div>

                {{-- Hızlı Ekle --}}
                <h6 class="dropdown-header">
                    <i class="fas fa-plus me-1"></i> Hızlı Ekle
                </h6>
                <a class="dropdown-item" href="{{ route('admin.muzibu.song.manage') }}">
                    <i class="fas fa-music me-2"></i>Yeni Şarkı
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.album.manage') }}">
                    <i class="fas fa-compact-disc me-2"></i>Yeni Albüm
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.genre.manage') }}">
                    <i class="fas fa-guitar me-2"></i>Yeni Tür
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.artist.manage') }}">
                    <i class="fas fa-user-music me-2"></i>Yeni Sanatçı
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.manage') }}">
                    <i class="fas fa-list-music me-2"></i>Yeni Playlist
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.radio.manage') }}">
                    <i class="fas fa-broadcast-tower me-2"></i>Yeni Radyo
                </a>
                <a class="dropdown-item" href="{{ route('admin.muzibu.sector.manage') }}">
                    <i class="fas fa-layer-group me-2"></i>Yeni Sektör
                </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>
</div>

<style>
.module-menu-wrapper {
    display: flex;
    align-items: center;
}
.module-menu-wrapper .btn-ghost-primary {
    color: var(--tblr-primary);
    background: transparent;
    border: none;
}
.module-menu-wrapper .btn-ghost-primary:hover {
    background: rgba(var(--tblr-primary-rgb), 0.1);
}
.module-menu-wrapper .dropdown-menu {
    min-width: 180px;
}
.module-menu-wrapper .dropdown-header {
    font-weight: 600;
    color: var(--tblr-muted);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
@media (max-width: 991.98px) {
    .module-menu-wrapper .dropdown-menu {
        max-height: 70vh;
        overflow-y: auto;
    }
}
</style>
@endpush
