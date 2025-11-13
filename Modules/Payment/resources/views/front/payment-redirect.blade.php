<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Tamamlanıyor...</title>
</head>
<body>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h2>Ödeme işlemi tamamlanıyor...</h2>
        <p>Lütfen bekleyin, sipariş sayfanıza yönlendiriliyorsunuz.</p>
    </div>

    <script>
        // ✅ PayTR iframe içindeyiz - parent window'u /payment/success'e yönlendir
        if (window.top !== window.self) {
            // iframe içindeyiz
            console.log('✅ PayTR ödeme başarılı - parent window yönlendiriliyor...');
            window.top.location.href = '/payment/success';
        } else {
            // Normal sayfada açılmış (direkt erişim)
            console.log('✅ Direkt erişim - /payment/success\'e yönlendiriliyor...');
            window.location.href = '/payment/success';
        }
    </script>
</body>
</html>
