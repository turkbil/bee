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
        .then(response => {
            // 419 (Page Expired) veya herhangi bir hata -> Anasayfaya yönlendir
            if (response.status === 419 || !response.ok) {
                window.location.href = '/';
            } else {
                // Başarılı logout -> Login sayfasına
                window.location.href = '/login';
            }
        })
        .catch(error => {
            // Network hatası -> Anasayfaya yönlendir
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
