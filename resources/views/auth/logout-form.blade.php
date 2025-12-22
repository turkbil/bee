<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çıkış Yapılıyor...</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Otomatik POST submit with error handling
        const form = document.getElementById('logout-form');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json().then(data => ({ status: response.status, data })))
        .then(({ status, data }) => {
            // Başarılı logout veya session zaten expire (redirect field varsa kullan)
            if (data && data.redirect) {
                window.location.href = data.redirect;
            } else if (status === 200 || data.success) {
                // Default: Login sayfasına
                window.location.href = '/login';
            } else {
                // Hata durumu -> Anasayfaya
                window.location.href = '/';
            }
        })
        .catch(error => {
            // JSON parse hatası veya network hatası -> Anasayfaya
            console.error('Logout error:', error);
            window.location.href = '/';
        });
    </script>

    <div style="text-align: center; padding: 50px; font-family: system-ui; color: #666;">
        <div style="font-size: 48px; margin-bottom: 20px;">👋</div>
        <p style="font-size: 16px;">Çıkış yapılıyor...</p>
    </div>
</body>
</html>
