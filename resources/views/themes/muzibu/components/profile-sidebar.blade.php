{{-- Profile Sidebar Component - Active state: {{ $active ?? 'profile' }} --}}
@php
    $active = $active ?? 'profile';
@endphp

<div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 sticky top-20">
    <h3 class="text-white font-semibold mb-4 px-2">Hesap Ayarları</h3>
    <nav class="space-y-1">
        <a
            href="/profile"
            wire:navigate
            class="w-full flex items-center gap-3 px-3 py-2.5 {{ $active === 'profile' ? 'bg-muzibu-coral/20 text-muzibu-coral font-medium' : 'hover:bg-white/10 text-white' }} rounded-lg transition-colors">
            <i class="fas fa-user-pen w-5"></i>
            <span class="text-sm">Profil Bilgileri</span>
        </a>

        <a
            href="/profile/avatar"
            wire:navigate
            class="w-full flex items-center gap-3 px-3 py-2.5 {{ $active === 'avatar' ? 'bg-purple-500/20 text-purple-400 font-medium' : 'hover:bg-white/10 text-white' }} rounded-lg transition-colors">
            <i class="fas fa-camera w-5"></i>
            <span class="text-sm">Avatar</span>
        </a>

        <a
            href="/profile/password"
            wire:navigate
            class="w-full flex items-center gap-3 px-3 py-2.5 {{ $active === 'password' ? 'bg-yellow-500/20 text-yellow-400 font-medium' : 'hover:bg-white/10 text-white' }} rounded-lg transition-colors">
            <i class="fas fa-key w-5"></i>
            <span class="text-sm">Şifre Değiştir</span>
        </a>

        <div class="h-px bg-white/10 my-2"></div>

        <a
            href="/profile/delete"
            wire:navigate
            class="w-full flex items-center gap-3 px-3 py-2.5 {{ $active === 'delete' ? 'bg-red-500/30 text-red-300 font-medium' : 'hover:bg-red-500/20 text-red-400' }} rounded-lg transition-colors">
            <i class="fas fa-trash-alt w-5"></i>
            <span class="text-sm">Hesabı Sil</span>
        </a>
    </nav>
</div>
