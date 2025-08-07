<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Component Test --}}
    <x-seo-meta />
    
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SEO Component Test Sayfası</h1>
        
        <p>Bu sayfa SEO component'inin çalışıp çalışmadığını test etmek için oluşturuldu.</p>
        
        <div class="info">
            <h3>Test Bilgileri:</h3>
            <ul>
                <li>Component: <code>&lt;x-seo-meta /&gt;</code></li>
                <li>Konum: Head tag'i içinde</li>
                <li>Beklenen: SEO meta tag'lerinin oluşturulması</li>
            </ul>
        </div>
        
        <div class="info" style="background: #f3e5f5; margin-top: 20px;">
            <h3>Sayfa Kaynağını Kontrol Et:</h3>
            <p>Tarayıcıda sağ tık → "Sayfa Kaynağını Görüntüle" yaparak head tag'i içinde SEO meta tag'lerinin oluşup oluşmadığını kontrol edebilirsiniz.</p>
        </div>
    </div>
</body>
</html>