<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Debug Test - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">🔍 Cache Debug Test</h1>
            <p class="text-blue-100">{{ $data['timestamp'] }} - {{ $data['request_info']['url'] }}</p>
        </div>

        <!-- Auth Status -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                👤 AUTH DURUMU
                @if($data['auth_status']['is_authenticated'])
                    <span class="ml-3 px-3 py-1 bg-green-600 text-white text-sm rounded-full">AUTHENTICATED</span>
                @else
                    <span class="ml-3 px-3 py-1 bg-gray-600 text-white text-sm rounded-full">GUEST</span>
                @endif
            </h2>
            <div class="space-y-2">
                <div class="flex"><span class="w-32 text-gray-400">Authenticated:</span> <span class="text-green-400">{{ $data['auth_status']['is_authenticated'] ? 'YES' : 'NO' }}</span></div>
                @if($data['auth_status']['is_authenticated'])
                    <div class="flex"><span class="w-32 text-gray-400">User ID:</span> <span class="text-blue-400">{{ $data['auth_status']['user_id'] }}</span></div>
                    <div class="flex"><span class="w-32 text-gray-400">Name:</span> <span class="text-yellow-400">{{ $data['auth_status']['user_name'] }}</span></div>
                    <div class="flex"><span class="w-32 text-gray-400">Email:</span> <span class="text-purple-400">{{ $data['auth_status']['user_email'] }}</span></div>
                @endif
            </div>
        </div>

        <!-- Language Info -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4">🌐 DİL BİLGİLERİ</h2>
            <div class="space-y-2">
                <div class="flex"><span class="w-40 text-gray-400">App Locale:</span> <span class="text-green-400 font-mono">{{ $data['locale_info']['app_locale'] }}</span></div>
                <div class="flex"><span class="w-40 text-gray-400">Session Site Locale:</span> <span class="text-blue-400 font-mono">{{ $data['locale_info']['session_site_locale'] ?? 'NULL' }}</span></div>
            </div>
        </div>

        <!-- Cache Info -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4">💾 CACHE BİLGİLERİ</h2>
            
            @if(isset($data['cache_info']['error']))
                <div class="bg-red-900 border border-red-600 text-red-200 p-4 rounded">
                    <strong>REDIS ERROR:</strong> {{ $data['cache_info']['error'] }}
                </div>
            @else
                <!-- Cache Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-900 p-4 rounded text-center">
                        <div class="text-2xl font-bold text-blue-300">{{ $data['cache_info']['total_keys'] }}</div>
                        <div class="text-blue-200 text-sm">Total Keys</div>
                    </div>
                    <div class="bg-green-900 p-4 rounded text-center">
                        <div class="text-2xl font-bold text-green-300">{{ $data['cache_info']['guest_keys_count'] }}</div>
                        <div class="text-green-200 text-sm">Guest Keys</div>
                    </div>
                    <div class="bg-purple-900 p-4 rounded text-center">
                        <div class="text-2xl font-bold text-purple-300">{{ $data['cache_info']['auth_keys_count'] }}</div>
                        <div class="text-purple-200 text-sm">Auth Keys</div>
                    </div>
                    <div class="bg-yellow-900 p-4 rounded text-center">
                        <div class="text-2xl font-bold text-yellow-300">{{ $data['cache_info']['response_cache_keys'] }}</div>
                        <div class="text-yellow-200 text-sm">Response Keys</div>
                    </div>
                </div>

                <!-- Current Page Hash -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Current Page Hash:</h3>
                    <div class="bg-gray-900 p-3 rounded font-mono text-sm text-green-400 break-all">{{ $data['cache_info']['current_hash'] }}</div>
                    <div class="mt-2">
                        <span class="text-gray-400">Bu sayfa cache'de mi?</span>
                        <span class="ml-2 px-2 py-1 rounded text-sm {{ $data['cache_info']['current_page_cached'] === 'YES' ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                            {{ $data['cache_info']['current_page_cached'] }}
                        </span>
                    </div>
                </div>

                <!-- Sample Keys -->
                @if(!empty($data['cache_info']['sample_guest_keys']))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2 text-green-400">Sample Guest Keys ({{ count($data['cache_info']['sample_guest_keys']) }} adet):</h3>
                        <div class="space-y-1">
                            @foreach($data['cache_info']['sample_guest_keys'] as $key)
                                <div class="bg-green-900 bg-opacity-30 p-2 rounded font-mono text-xs text-green-300 break-all">{{ $key }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($data['cache_info']['sample_auth_keys']))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2 text-purple-400">Sample Auth Keys ({{ count($data['cache_info']['sample_auth_keys']) }} adet):</h3>
                        <div class="space-y-1">
                            @foreach($data['cache_info']['sample_auth_keys'] as $key)
                                <div class="bg-purple-900 bg-opacity-30 p-2 rounded font-mono text-xs text-purple-300 break-all">{{ $key }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6 border border-gray-700">
            <h2 class="text-xl font-bold mb-4">🛠️ CACHE İŞLEMLERİ</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <a href="{{ route('cache.debug.clear', ['type' => 'guest']) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-center text-sm transition"
                   onclick="return confirm('Guest cache\'leri temizle?')">
                    🧹 Guest Cache
                </a>
                <a href="{{ route('cache.debug.clear', ['type' => 'auth']) }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-center text-sm transition"
                   onclick="return confirm('Auth cache\'leri temizle?')">
                    🧹 Auth Cache
                </a>
                <a href="{{ route('cache.debug.clear', ['type' => 'response']) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center text-sm transition"
                   onclick="return confirm('Response cache\'leri temizle?')">
                    🧹 Response Cache
                </a>
                <a href="{{ route('cache.debug.clear', ['type' => 'all']) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-center text-sm transition"
                   onclick="return confirm('TÜM cache\'leri temizle?')">
                    🧹 Hepsini Sil
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="{{ url('/') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-center transition">
                    🏠 Anasayfa
                </a>
                <a href="{{ route('cache.debug') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center transition">
                    🔄 Yenile
                </a>
                @guest
                    <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-center transition">
                        🔐 Login
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-center transition">
                            🚪 Logout
                        </button>
                    </form>
                @endguest
            </div>
        </div>

        <!-- Copy Instructions -->
        <div class="bg-yellow-900 border border-yellow-600 rounded-lg p-4 text-yellow-100">
            <h3 class="font-bold mb-2">📋 Kopyalama Talimatı:</h3>
            <p>Bu sayfadaki tüm içeriği <strong>CTRL+A</strong> ile seç, <strong>CTRL+C</strong> ile kopyala ve Claude'a yapıştır.</p>
        </div>
    </div>
</body>
</html>