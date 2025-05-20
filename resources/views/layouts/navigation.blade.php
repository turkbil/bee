<!-- resources/views/layouts/navigation.blade.php -->
<nav x-data="{ open: false }" style="background-color: #2d2d2d; border-bottom: 1px solid #404040;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px;">
        <div style="display: flex; justify-content: space-between; height: 64px;">
            <div style="display: flex; align-items: center;">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" style="display: block; margin-right: 32px;">
                    <x-application-logo style="height: 36px; width: auto; fill: #e0e0e0;" />
                </a>

                <!-- Navigation Links -->
                <div style="display: flex; gap: 24px;">
                    <a href="{{ route('dashboard') }}" 
                       style="color: {{ request()->routeIs('dashboard') ? '#ffffff' : '#a0a0a0' }}; text-decoration: none;">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ url('admin/dashboard') }}" 
                       style="color: {{ request()->is('admin/dashboard') ? '#ffffff' : '#a0a0a0' }}; text-decoration: none;">
                        {{ __('Yönetim Paneli') }}
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       style="color: {{ request()->routeIs('profile.edit') ? '#ffffff' : '#a0a0a0' }}; text-decoration: none;">
                        {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" 
                                style="background: none; border: none; color: #a0a0a0; cursor: pointer; padding: 0;">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- User Info -->
            <div style="display: flex; align-items: center;">
                <div style="color: #e0e0e0;">{{ Auth::user()->name }}</div>
            </div>

            <!-- Mobile menu button -->
            <div style="display: none;">
                <button @click="open = !open" 
                        style="padding: 8px; color: #a0a0a0; background: none; border: none; cursor: pointer;">
                    <svg style="height: 24px; width: 24px;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" style="display: none;">
    </div>
</nav>