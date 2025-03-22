<!DOCTYPE html>
<html>
<head>
    <title>CDN Fonksiyonu Testi</title>
    @php
    // View dosyasında doğrudan fonksiyonu tanımlayalım
    function cdn($path) {
        $path = ltrim($path, '/');
        return rtrim(env('APP_URL', 'http://laravel.test'), '/') . '/' . $path;
    }
    @endphp
</head>
<body>
    <h1>CDN Fonksiyonu Test Sonuçları</h1>
    
    <p>cdn fonksiyonu var mı: {{ function_exists('cdn') ? 'Evet' : 'Hayır' }}</p>
    
    <ul>
        <li>cdn('resim.jpg') = {{ cdn('resim.jpg') }}</li> 123
        <li>cdn('/resim.jpg') = {{ cdn('/resim.jpg') }}</li>
        <li>cdn('css/style.css') = {{ cdn('css/style.css') }}</li>
        <li>cdn('/js/app.js') = {{ cdn('/js/app.js') }}</li>
    </ul>
    
    <p>Çalışan APP_URL değeri: {{ env('APP_URL', 'Belirtilmemiş') }}</p>
    
    <h2>Debug Bilgileri</h2>
    <pre>
    app_path: {{ app_path() }}
    base_path: {{ base_path() }}
    Laravel Versiyonu: {{ app()->version() }}
    </pre>
</body>
</html>