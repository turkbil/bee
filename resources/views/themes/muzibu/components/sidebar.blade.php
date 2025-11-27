<aside class="fixed left-0 z-40 h-full w-64 bg-black {{ auth()->check() ? 'top-0' : 'top-14' }}">
    <div class="flex flex-col h-full">
        <div class="p-6">
            {!! app(\App\Services\LogoService::class)->renderHeaderLogo(['class' => 'h-10 w-auto']) !!}
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                <li><a href="/" :class="currentPath === '/' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-home w-5 mr-3"></i>{{ tenant_trans('front.home') }}</a></li>
                <li><a href="/search" :class="currentPath === '/search' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-search w-5 mr-3"></i>{{ tenant_trans('front.search') }}</a></li>
                <li><a href="/playlists" :class="currentPath === '/playlists' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-list w-5 mr-3"></i>{{ tenant_trans('front.playlists') }}</a></li>
                <li><a href="/albums" :class="currentPath === '/albums' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-record-vinyl w-5 mr-3"></i>{{ tenant_trans('front.albums') }}</a></li>
                <li><a href="/genres" :class="currentPath === '/genres' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-guitar w-5 mr-3"></i>{{ tenant_trans('front.genres') }}</a></li>
                <li><a href="/sectors" :class="currentPath === '/sectors' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-building w-5 mr-3"></i>{{ tenant_trans('front.sectors') }}</a></li>
            </ul>

            <div class="mt-6 pt-6 border-t border-white/10">
                <button class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-all w-full">
                    <i class="fas fa-plus-square w-5 mr-3"></i>{{ tenant_trans('front.add_to_playlist') }}
                </button>
                <button class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-all w-full">
                    <i class="fas fa-heart w-5 mr-3"></i>{{ tenant_trans('front.favorites') }}
                </button>
            </div>

            @guest
            <div class="mt-6 px-3 space-y-3">
                <button @click="showAuthModal = 'register'" class="w-full px-4 py-3 rounded-full bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold transition-all shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>{{ tenant_trans('front.free_trial') }}
                </button>
                <button @click="showAuthModal = 'login'" class="w-full px-4 py-3 rounded-full bg-transparent border border-gray-600 text-white font-semibold hover:border-white hover:scale-105 transition-all">
                    {{ tenant_trans('player.login') }}
                </button>

                {{-- Cache Clear Button (Guest) --}}
                <div class="flex gap-2 mt-3 pt-3 border-t border-white/10">
                    <button onclick="clearSystemCache(this)"
                            title="Cache Temizle"
                            class="flex-1 px-2 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-trash-can text-xs"></i>
                        <span class="ml-1">Cache Temizle</span>
                    </button>
                </div>
            </div>
            @endguest

            @auth
            <div class="mt-6 px-3">
                <div class="p-4 bg-spotify-gray rounded-lg">
                    <!-- Normal State -->
                    <template x-if="!isLoggingOut">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="relative">
                                    <div class="w-10 h-10 bg-gradient-to-br from-spotify-green to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    @if(auth()->user()->isPremium())
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg border-2 border-black">
                                            <i class="fas fa-crown text-yellow-900 text-xs"></i>
                                        </div>
                                    @elseif(auth()->user()->isTrialActive())
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center shadow-lg border-2 border-black">
                                            <i class="fas fa-gift text-purple-900 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-white text-sm truncate">
                                        {{ auth()->user()->name }}
                                        @if(auth()->user()->isPremium())
                                            <i class="fas fa-crown text-yellow-400 text-xs ml-1"></i>
                                        @elseif(auth()->user()->isTrialActive())
                                            <i class="fas fa-gift text-purple-400 text-xs ml-1"></i>
                                        @endif
                                    </div>
                                    @if(auth()->user()->isPremium())
                                        <div class="text-xs text-spotify-green-light">
                                            {{ tenant_trans('front.premium') }}
                                        </div>
                                    @elseif(auth()->user()->isTrialActive())
                                        <div class="text-xs text-yellow-400">
                                            Deneme
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">
                                            Üye
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button @click="logout()" class="w-full px-3 py-2 bg-white/10 text-white rounded-full text-xs hover:bg-white/20 transition-all font-semibold">
                                {{ tenant_trans('front.logout') }}
                            </button>

                            {{-- Play Limits Widget --}}
                            @include('themes.muzibu.components.play-limits-widget')

                            {{-- Cache Clear Button (All Users) --}}
                            <div class="flex gap-2 mt-3 pt-3 border-t border-white/10">
                                <button onclick="clearSystemCache(this)"
                                        title="Cache Temizle"
                                        class="flex-1 px-2 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1">
                                    <i class="fas fa-trash-can text-xs"></i>
                                    <span class="hidden sm:inline ml-1">Cache</span>
                                </button>

                                @if(auth()->user()->hasRole('root'))
                                    <button onclick="clearAIConversation(this)"
                                            title="AI Chat Temizle"
                                            class="flex-1 px-2 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1">
                                        <i class="fas fa-comments text-xs"></i>
                                        <span class="hidden sm:inline ml-1">AI</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </template>

                    <!-- Logging Out State -->
                    <template x-if="isLoggingOut">
                        <div class="flex items-center justify-center py-4">
                            <i class="fas fa-spinner fa-spin text-spotify-green text-xl mr-2"></i>
                            <span class="text-gray-400 text-sm">{{ tenant_trans('front.logout') }}...</span>
                        </div>
                    </template>
                </div>
            </div>
            @endauth
        </nav>
    </div>
</aside>
