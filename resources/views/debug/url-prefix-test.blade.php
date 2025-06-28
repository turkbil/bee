<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Prefix Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">🌐 URL Prefix Test Sayfası</h1>
        
        <!-- Mevcut Durum -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-bold mb-4">📊 Mevcut Sistem Durumu</h2>
            
            @php
                $tenant = tenant();
                $currentMode = $tenant->data['url_prefix']['mode'] ?? 'none';
                $defaultLanguage = $tenant->data['url_prefix']['default_language'] ?? 'tr';
                $currentLocale = app()->getLocale();
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded">
                    <strong>URL Prefix Modu:</strong><br>
                    <span class="text-lg">{{ $currentMode }}</span>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <strong>Varsayılan Dil:</strong><br>
                    <span class="text-lg">{{ $defaultLanguage }}</span>
                </div>
                <div class="bg-yellow-50 p-4 rounded">
                    <strong>Şu Anki Dil:</strong><br>
                    <span class="text-lg">{{ $currentLocale }}</span>
                </div>
            </div>
        </div>
        
        <!-- URL Prefix Ayarları -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-bold mb-4">⚙️ URL Prefix Ayarları</h2>
            
            <form action="/debug/url-prefix-save" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-2">URL Prefix Modu:</label>
                        <select name="mode" class="w-full p-3 border rounded-lg">
                            <option value="none" {{ $currentMode === 'none' ? 'selected' : '' }}>Hiçbirinde prefix yok</option>
                            <option value="default_only" {{ $currentMode === 'default_only' ? 'selected' : '' }}>Varsayılan hariç prefix</option>
                            <option value="all" {{ $currentMode === 'all' ? 'selected' : '' }}>Tümünde prefix</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block font-semibold mb-2">Varsayılan Dil:</label>
                        <select name="default_language" class="w-full p-3 border rounded-lg">
                            <option value="tr" {{ $defaultLanguage === 'tr' ? 'selected' : '' }}>🇹🇷 Türkçe</option>
                            <option value="en" {{ $defaultLanguage === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                            <option value="ar" {{ $defaultLanguage === 'ar' ? 'selected' : '' }}>🇸🇦 العربية</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="mt-4 bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600">
                    💾 Ayarları Kaydet
                </button>
            </form>
        </div>
        
        <!-- URL Örnekleri -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-bold mb-4">🔗 URL Örnekleri</h2>
            
            <div class="space-y-4">
                @foreach(['tr' => '🇹🇷 Türkçe', 'en' => '🇺🇸 English', 'ar' => '🇸🇦 العربية'] as $lang => $langName)
                    @php
                        $needsPrefix = \Modules\LanguageManagement\app\Services\UrlPrefixService::needsPrefix($lang);
                        $prefix = \Modules\LanguageManagement\app\Services\UrlPrefixService::generateUrlPrefix($lang);
                    @endphp
                    
                    <div class="border p-4 rounded-lg">
                        <strong>{{ $langName }}:</strong><br>
                        <code class="text-blue-600">{{ $prefix }}/page/hakkimizda</code>
                        <span class="text-sm text-gray-500">(Prefix gerekli: {{ $needsPrefix ? 'Evet' : 'Hayır' }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Test Linkleri -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">🚀 Test Linkleri</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/page/hakkimizda" class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600">
                    📄 /page/hakkimizda
                </a>
                <a href="/en/page/about-us" class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600">
                    📄 /en/page/about-us
                </a>
            </div>
        </div>
        
        <!-- Language Switcher Test -->
        <div class="mt-8 text-center">
            <h2 class="text-xl font-bold mb-4">🔄 Dil Değiştirici Test</h2>
            @livewire('languagemanagement::language-switcher', ['style' => 'buttons'])
        </div>
    </div>
</body>
</html>