<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Bulunamadı - 404</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto text-center px-6">
        <!-- 404 Icon -->
        <div class="mb-8">
            <div class="relative">
                <i class="fas fa-globe text-8xl text-red-400 mb-4 opacity-20"></i>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-6xl font-bold text-red-500">404</span>
                </div>
            </div>
        </div>

        <!-- Başlık -->
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Site Bulunamadı</h1>
        
        <!-- Açıklama -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <p class="text-gray-700 mb-4 text-lg">
                <strong class="text-red-600">{{ $domain ?? 'Bu domain' }}</strong> için aktif bir site bulunamadı.
            </p>
            
            <div class="text-left">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    Muhtemel Nedenler:
                </h3>
                <ul class="text-gray-600 space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-400 text-xs mt-2 mr-3"></i>
                        Site geçici olarak kapatılmış olabilir
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-400 text-xs mt-2 mr-3"></i>
                        Domain yanlış yazılmış olabilir
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-400 text-xs mt-2 mr-3"></i>
                        Site henüz kurulmamış olabilir
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-400 text-xs mt-2 mr-3"></i>
                        Hosting süresi dolmuş olabilir
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- İletişim Bilgileri -->
        <div class="bg-blue-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold mb-3 text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Site Sahibi misiniz?
            </h3>
            <p class="text-blue-700 text-sm">
                Bu hatayı görüyorsanız, site yapılandırmanızı kontrol edin veya 
                hosting sağlayıcınız ile iletişime geçin.
            </p>
        </div>

        <!-- Butonlar -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://turkbil.com" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center justify-center">
                <i class="fas fa-home mr-2"></i>
                Ana Siteye Dön
            </a>
            
            <button onclick="window.history.back()" 
                    class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </button>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-gray-500 text-sm">
            <p>
                <i class="fas fa-shield-alt mr-2"></i>
                Bu sayfa Turkbil Bee sistemi tarafından gösterilmektedir.
            </p>
            <p class="mt-2">
                Hata Kodu: <span class="font-mono bg-gray-200 px-2 py-1 rounded">TENANT_NOT_FOUND</span>
            </p>
        </div>
    </div>

    <!-- Animasyon -->
    <script>
        // Sayfa yüklendiğinde fade-in efekti
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(20px)';
            document.body.style.transition = 'all 0.5s ease-in-out';
            
            setTimeout(function() {
                document.body.style.opacity = '1';
                document.body.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
