<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    
    {{-- Schema.org Test için --}}
    {!! \App\Services\SEOService::getPageSchema($page) !!}
    
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .schema-test {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #ccc;
            margin-top: 40px;
            padding-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>{{ $page->title }}</h1>
    
    <div class="schema-test">
        <h2>Schema.org Test Sayfası</h2>
        <p>{{ $page->excerpt }}</p>
        <p><strong>Güncelleme:</strong> {{ $page->updated_at->format('d.m.Y H:i') }}</p>
    </div>
    
    <div class="footer">
        <h3>Schema.org Nasıl Kontrol Edilir:</h3>
        <ol>
            <li><strong>Kaynak Kodu:</strong> Sayfada sağ tık → "Kaynağı Görüntüle" → <code>application/ld+json</code> ara</li>
            <li><strong>Google Test:</strong> <a href="https://search.google.com/test/rich-results?url={{ urlencode(url()->current()) }}" target="_blank">Rich Results Test</a></li>
            <li><strong>Schema.org Validator:</strong> <a href="https://validator.schema.org/" target="_blank">Validator</a></li>
        </ol>
        
        <p><strong>Test URL:</strong> {{ url()->current() }}</p>
    </div>
</body>
</html>