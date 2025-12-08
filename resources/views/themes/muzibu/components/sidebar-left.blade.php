{{-- LEFT SIDEBAR - Modern & Clean --}}
<aside
    id="leftSidebar"
    class="muzibu-left-sidebar lg:flex lg:flex-col animate-slide-up"
>
    {{-- Library Section --}}
    <div class="mb-3">
        <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">Kitaplığım</h3>
        <nav class="space-y-1">
            <a href="/my-playlists" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-list w-5 text-base"></i>
                <span class="font-medium text-sm">Playlistlerim</span>
            </a>
            <a href="/favorites" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-heart w-5 text-base"></i>
                <span class="font-medium text-sm">Favorilerim</span>
            </a>
        </nav>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

    {{-- Browse Section --}}
    <div class="mb-3">
        <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">Keşfet</h3>
        <nav class="space-y-1">
            <a href="/playlists" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-fire w-5 text-base"></i>
                <span class="font-medium text-sm">Popüler Playlistler</span>
            </a>
            <a href="/albums" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-compact-disc w-5 text-base"></i>
                <span class="font-medium text-sm">Albümler</span>
            </a>
            <a href="/genres" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-music w-5 text-base"></i>
                <span class="font-medium text-sm">Türler</span>
            </a>
            <a href="/sectors" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-compass w-5 text-base"></i>
                <span class="font-medium text-sm">Sektörler</span>
            </a>
            <a href="/radios" wire:navigate class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-radio w-5 text-base"></i>
                <span class="font-medium text-sm">Canlı Radyolar</span>
            </a>
        </nav>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

    {{-- Actions --}}
    <nav class="space-y-1">
        <button @click="$dispatch('open-create-playlist-modal')" class="w-full flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
            <i class="fas fa-plus-circle w-5 text-base group-hover:text-muzibu-coral transition-colors"></i>
            <span class="font-medium text-sm">Playlist Oluştur</span>
        </button>
    </nav>

    {{-- Spacer to push auth card to bottom --}}
    <div class="flex-1"></div>

    {{-- User Profile Card - Bottom - TEK COMPONENT ILE KALAN SURE --}}
    <div
        x-show="isLoggedIn"
        x-cloak
        x-data="{
            timeLeft: '',
            memberType: '',
            memberIcon: '',
            hasDate: false,
            isExpired: false,
            init() {
                this.updateMemberStatus();
                setInterval(() => this.updateMemberStatus(), 60000);
            },
            updateMemberStatus() {
                // 1. Ucretsiz uye (is_premium = false)
                if (!this.currentUser?.is_premium) {
                    this.memberType = 'Ucretsiz Uye';
                    this.memberIcon = '';
                    this.hasDate = false;
                    return;
                }

                // 2. Trial mi Premium mi? (is_trial flag'i kullan)
                const isTrial = this.currentUser?.is_trial === true;
                const endDate = isTrial
                    ? this.currentUser?.trial_ends_at
                    : this.currentUser?.subscription_ends_at;

                if (isTrial) {
                    this.memberType = 'Deneme Uyesi';
                    this.memberIcon = 'fa-gift';
                } else {
                    this.memberType = 'Premium Uye';
                    this.memberIcon = 'fa-crown';
                }

                // Tarih yoksa sadece badge goster
                if (!endDate) {
                    this.hasDate = false;
                    return;
                }

                this.hasDate = true;
                const now = new Date();
                const expiry = new Date(endDate);
                const diff = expiry - now;

                if (diff <= 0) {
                    this.timeLeft = 'Ueyelik sona erdi';
                    this.isExpired = true;
                    return;
                }

                this.isExpired = false;
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                if (days > 0) {
                    this.timeLeft = days + ' gun ' + hours + ' saat kaldi';
                } else if (hours > 0) {
                    this.timeLeft = hours + ' saat ' + minutes + ' dk kaldi';
                } else {
                    this.timeLeft = minutes + ' dakika kaldi';
                }
            },
            get currentUser() {
                return window.muzibuPlayerConfig?.currentUser || null;
            }
        }"
        class="mt-4 bg-gradient-to-br from-[#ff6b6b] via-[#ff5252] to-[#e91e63] rounded-2xl p-4 shadow-xl relative overflow-hidden group"
    >
        {{-- Animated Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(255,255,255,0.3),transparent_50%)]"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10">
            {{-- User Info --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl border-2 border-white/30">
                    <template x-if="currentUser?.is_premium">
                        <i class="fas fa-crown text-yellow-300"></i>
                    </template>
                    <template x-if="!currentUser?.is_premium">
                        <i class="fas fa-user text-white/80"></i>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-bold text-sm truncate" x-text="currentUser?.name || 'Kullanici'"></h3>

                    {{-- Uyelik Tipi ve Kalan Sure (Tek Component) --}}
                    <div class="text-white/90 text-xs">
                        <div class="flex items-center gap-1">
                            <template x-if="memberIcon">
                                <i class="fas text-yellow-300" :class="memberIcon"></i>
                            </template>
                            <span x-text="memberType"></span>
                        </div>
                        {{-- Kalan Sure - Her zaman goster (tarih varsa) --}}
                        <template x-if="hasDate">
                            <p class="text-[10px] mt-0.5" :class="isExpired ? 'text-red-300 animate-pulse' : 'text-white/70'">
                                <i class="fas fa-clock mr-1"></i>
                                <span x-text="timeLeft"></span>
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Premium'a Gec (Ucretsiz uyeler icin) --}}
            <template x-if="!currentUser?.is_premium">
                <a href="/subscription/plans" class="block w-full mb-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-400 hover:to-orange-400 text-white px-4 py-2.5 rounded-lg text-sm font-bold text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-crown mr-2"></i>
                    Premium'a Gec
                </a>
            </template>

            {{-- Uyeligini Uzat (Premium/Trial uyeler icin) --}}
            <template x-if="currentUser?.is_premium">
                <a href="/subscription/plans" class="block w-full mb-3 bg-gradient-to-r from-yellow-500/80 to-orange-500/80 hover:from-yellow-400 hover:to-orange-400 text-white px-4 py-2.5 rounded-lg text-sm font-bold text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Uyeligini Uzat
                </a>
            </template>

            {{-- Logout Button --}}
            <button
                @click="logout()"
                class="w-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 border border-white/20 hover:border-white/40"
            >
                <i class="fas fa-sign-out-alt mr-2"></i>
                Cikis Yap
            </button>
        </div>
    </div>
</aside>
