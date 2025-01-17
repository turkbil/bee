<ul class="navbar-nav">
    <li class="nav-item active dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="fa-solid fa-user-graduate"></i>
            </span>
            <span class="nav-link-title">
                İçerik
            </span>
        </a>
        <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
                <div class="dropdown-menu-column">
                    <a class="dropdown-item" href="{{ route('admin.page.index') }}">
                        Sayfa
                    </a>
                    <a class="dropdown-item" href="./layout-boxed.html">
                        Boxed
                        <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>
                    </a>
                </div>
                <div class="dropdown-menu-column">
                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">
                        Portfolio
                    </a>
                    <a class="dropdown-item" href="./layout-fluid-vertical.html">
                        Fluid vertical
                    </a>
                </div>
            </div>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="fa-solid fa-user-police-tie"></i>
            </span>
            <span class="nav-link-title">
                Yönetim
            </span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.user.index') }}">
                Kullanıcılar
            </a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="fa-solid fa-user-doctor"></i>
            </span>
            <span class="nav-link-title">
                Sistem
            </span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.tenant.index') }}">
                Tenantlar
            </a>
        </div>
    </li>
</ul>