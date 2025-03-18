<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f8f9fa;
                color: #333;
                line-height: 1.6;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 40px 20px;
            }
            .card {
                background-color: #fff;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            h1 {
                font-size: 24px;
                margin-bottom: 20px;
                color: #ff2d20;
            }
            .info {
                margin-bottom: 20px;
                font-size: 16px;
            }
            .links {
                margin-top: 30px;
            }
            .links a {
                display: inline-block;
                background-color: #ff2d20;
                color: white;
                padding: 10px 15px;
                border-radius: 4px;
                text-decoration: none;
                margin-right: 10px;
                transition: background-color 0.3s;
            }
            .links a:hover {
                background-color: #e0271b;
            }
            .footer {
                margin-top: 40px;
                text-align: center;
                font-size: 14px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <h1>Laravel Multi-Tenancy</h1>
                
                <div class="info">
                    @php
                    $host = request()->getHost();
                    $centralDomains = config('tenancy.central_domains');
                    $isCentralDomain = in_array($host, $centralDomains);
                    @endphp
                    
                    @if(!$isCentralDomain && app(\Stancl\Tenancy\Tenancy::class)->initialized)
                        <p><strong>Ana Domain:</strong> {{ request()->getHost() }}</p>
                        <p><strong>Tenant ID:</strong> {{ tenant()->id }}</p>
                        <p>Bu bir tenant (kiracı) uygulamasıdır. Şu anda tenant veritabanı kullanılmaktadır.</p>
                    @else
                        <p><strong>Ana Domain:</strong> {{ request()->getHost() }}</p>
                        <p>Bu merkez (central) domainidir. Ana veritabanı kullanılmaktadır.</p>
                    @endif
                </div>
                
                <div class="links">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}">Giriş Yap</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}">Kayıt Ol</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
            
            <div class="footer">
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </div>
        </div>
    </body>
</html>