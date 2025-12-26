{{-- SPA Content: Member Listening History --}}
<div x-data="memberHistory()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">

        {{-- Header --}}
        <div class="mb-8">
            {{-- Back Button --}}
            <a href="/corporate/dashboard" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-4 transition" data-spa>
                <i class="fas fa-arrow-left"></i>
                <span>Kurumsal Panel</span>
            </a>

            {{-- Member Info Card --}}
            <div class="bg-gradient-to-r from-purple-900/40 via-pink-900/30 to-purple-900/40 border border-purple-500/30 rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        @php
                            $colors = ['from-blue-500 to-cyan-500', 'from-purple-500 to-pink-500', 'from-amber-500 to-orange-500', 'from-green-500 to-emerald-500', 'from-rose-500 to-red-500'];
                            $colorIndex = ($member['account_id'] ?? 0) % count($colors);
                        @endphp
                        <div class="w-16 h-16 bg-gradient-to-br {{ $colors[$colorIndex] }} rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            {{ $member['initials'] ?? 'U' }}
                        </div>
                        @if($member['is_owner'] ?? false)
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-crown text-white text-xs"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h1 class="text-2xl font-bold text-white">{{ $member['name'] ?? 'Uye' }}</h1>
                            @if($member['is_owner'] ?? false)
                                <span class="text-xs text-yellow-400 bg-yellow-500/20 px-2 py-0.5 rounded-full font-medium">
                                    Ana Sube
                                </span>
                            @else
                                <span class="text-xs text-purple-400 bg-purple-500/20 px-2 py-0.5 rounded-full">
                                    {{ $member['branch_name'] ?? '' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-sm">{{ $member['email'] ?? '' }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-purple-300 text-sm">
                                <i class="fas fa-building mr-1"></i>{{ $parentAccount->company_name ?? 'Kurumsal' }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-headphones mr-1"></i>{{ $history->total() }} dinleme
                            </span>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center gap-4 sm:gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $history->total() }}</p>
                            <p class="text-gray-500 text-xs">Toplam</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- History List --}}
        <div class="bg-slate-900/50 rounded-lg overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
                <h2 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-history text-green-400"></i>
                    Dinleme Gecmisi
                </h2>
                @if($history->total() > 0)
                    <span class="text-gray-500 text-sm">{{ $history->total() }} kayit</span>
                @endif
            </div>

            @if($history->count() > 0)
                @foreach($history as $index => $play)
                    <x-muzibu.song-history-row :play="$play" :index="$index" />
                @endforeach

                {{-- Pagination --}}
                @if($history->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $history->links('themes.muzibu.partials.pagination') }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-400">
                    <i class="fas fa-history text-5xl mb-4 opacity-50"></i>
                    <p class="text-lg mb-2">Henuz dinleme gecmisi yok</p>
                    <p class="text-sm">Bu uye henuz sarki dinlemedi.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
function memberHistory() {
    return {
        playSong(songId) {
            if (window.MuzibuPlayer) {
                window.MuzibuPlayer.playById(songId);
            }
        }
    }
}
</script>
