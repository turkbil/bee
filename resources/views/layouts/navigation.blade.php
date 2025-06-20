<!-- Navbar -->
<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('dashboard') }}">
                {{ config('app.name') }}
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <!-- Theme Switcher -->
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Tema seçici">
                    <i class="fas fa-sun theme-icon-light"></i>
                    <i class="fas fa-moon theme-icon-dark"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                    <div class="card-body">
                        <h6 class="card-title">Tema</h6>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="theme-mode" value="light" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <i class="fas fa-sun me-2"></i>
                                    <span>Açık</span>
                                </div>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="theme-mode" value="dark" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <i class="fas fa-moon me-2"></i>
                                    <span>Koyu</span>
                                </div>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="theme-mode" value="auto" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <i class="fas fa-adjust me-2"></i>
                                    <span>Otomatik</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Menu -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Kullanıcı menüsü">
                    <span class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <div class="dropdown-header">
                        Hesap Ayarları
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-edit me-2"></i>
                        Profil Bilgileri
                    </a>
                    <a href="{{ route('profile.edit') }}#password" class="dropdown-item">
                        <i class="fas fa-key me-2"></i>
                        Şifre Değiştir
                    </a>
                    <a href="{{ route('profile.edit') }}#delete-account" class="dropdown-item text-danger">
                        <i class="fas fa-trash-alt me-2"></i>
                        Hesabı Sil
                    </a>
                    @if(auth()->user()->hasAnyRole(['admin', 'root', 'editor']))
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-header">
                        Yönetim
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Admin Paneli
                    </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Çıkış Yap
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fas fa-home"></i>
                            </span>
                            <span class="nav-link-title">
                                Dashboard
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>