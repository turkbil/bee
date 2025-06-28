<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 min-h-screen p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-blue-800 mb-6">🧪 Cache Test Page</h1>
        
        <div class="space-y-4">
            <div class="bg-blue-100 p-4 rounded">
                <h2 class="font-bold text-blue-800">Page Generated At:</h2>
                <p class="text-2xl font-mono text-blue-600">{{ $timestamp }}</p>
            </div>
            
            <div class="bg-green-100 p-4 rounded">
                <h2 class="font-bold text-green-800">Auth Status:</h2>
                <p class="text-xl font-semibold text-green-600">{{ $auth_status }}</p>
                @if($auth_status === 'AUTHENTICATED')
                    <p class="text-green-600">User: {{ $user_name }}</p>
                @endif
            </div>
            
            <div class="bg-purple-100 p-4 rounded">
                <h2 class="font-bold text-purple-800">Locale:</h2>
                <p class="text-xl font-mono text-purple-600">{{ $locale }}</p>
            </div>
            
            <div class="bg-yellow-100 p-4 rounded">
                <h2 class="font-bold text-yellow-800">Random Number:</h2>
                <p class="text-2xl font-mono text-yellow-600">{{ $random_number }}</p>
                <p class="text-sm text-yellow-600">Bu sayı cache'lenirse aynı kalır</p>
            </div>
        </div>
        
        <div class="mt-8 space-x-4">
            <a href="{{ route('cache.debug') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Debug Sayfası
            </a>
            <a href="{{ route('cache.test.page') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Bu Sayfayı Yenile
            </a>
            <a href="{{ url('/') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Anasayfa
            </a>
        </div>
        
        <div class="mt-6 text-sm text-gray-600">
            <strong>Cache Test:</strong> Bu sayfa cache'lenmişse timestamp ve random number değişmeyecek.
        </div>
    </div>
</body>
</html>