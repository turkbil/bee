<?php
$title = "Mobile App - Mobil Uygulama";
$description = "Native iOS ve Android uygulamaları ile her yerden erişim";
$keywords = "mobil uygulama, mobile app, iOS, Android, native app, hybrid app";
$canonical = "https://cms.nurullah.com.tr/mobile-app-details";
$image = "assets/images/mobile-app-hero.jpg";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero relative overflow-hidden bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600/20 to-purple-600/20"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KPGcgZmlsbD0iIzAwMCIgZmlsbC1vcGFjaXR5PSIwLjAzIj4KPHBhdGggZD0iTTEwIDEwaDEwdjEwSDEwek0zMCAxMGgxMHYxMEgzMHpNMTAgMzBoMTB2MTBIMTB6TTMwIDMwaDEwdjEwSDMweiIvPgo8L2c+CjwvZz4KPC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 container mx-auto px-4 py-20">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-6">
                    <i class="fas fa-mobile-alt text-2xl text-white"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Mobile App
                    <span class="block text-xl md:text-2xl font-normal text-violet-100 mt-2">
                        Mobil Uygulama
                    </span>
                </h1>
                <p class="text-xl text-violet-100 max-w-2xl mx-auto mb-8">
                    Native iOS ve Android uygulamaları ile web sitenizi her yerden yönetin. 
                    Offline support, push notifications ve native performance.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#features" class="bg-white text-violet-600 px-8 py-3 rounded-lg font-medium hover:bg-violet-50 transition-colors">
                        Özellikler
                    </a>
                    <a href="#download" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white hover:text-violet-600 transition-colors">
                        İndir
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- App Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Mobil Uygulama Özellikleri
                        <span class="block text-lg text-violet-600">Mobile App Features</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Native mobile development ile cross-platform compatibility (çapraz platform uyumluluğu). 
                        React Native ve Flutter teknolojileri ile high performance (yüksek performans).
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="w-12 h-12 bg-violet-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-wifi text-violet-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Offline Erişim
                            <span class="block text-sm text-gray-500">Offline Access</span>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Internet bağlantısı olmadığında bile uygulama kullanımı. 
                            Local storage (yerel depolama) ve background sync (arka plan senkronizasyonu).
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li>• Offline content editing</li>
                            <li>• Local data storage</li>
                            <li>• Auto-sync when online</li>
                            <li>• Cached media files</li>
                        </ul>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-bell text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Push Notifications
                            <span class="block text-sm text-gray-500">Anında Bildirimler</span>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Real-time notifications ile önemli güncellemeler. 
                            Customizable notification types (özelleştirilebilir bildirim türleri).
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li>• Real-time alerts</li>
                            <li>• Custom notification settings</li>
                            <li>• Rich media notifications</li>
                            <li>• Scheduled notifications</li>
                        </ul>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-fingerprint text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Biometric Authentication
                            <span class="block text-sm text-gray-500">Biyometrik Kimlik Doğrulama</span>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Touch ID, Face ID ve fingerprint authentication ile güvenli erişim. 
                            Multi-factor authentication (çok faktörlü kimlik doğrulama) desteği.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li>• Touch ID / Face ID</li>
                            <li>• Fingerprint login</li>
                            <li>• PIN code backup</li>
                            <li>• Secure keychain</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-8">
                        <h3 class="text-2xl font-bold text-white mb-4">
                            Native Performance
                            <span class="block text-lg text-violet-100">Native Performans</span>
                        </h3>
                        <p class="text-violet-100">
                            Platform-specific optimizations ile maximum performance. 
                            Hardware acceleration ve native UI components kullanımı.
                        </p>
                    </div>
                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">iOS Özellikleri</h4>
                                <ul class="space-y-3 text-gray-600">
                                    <li class="flex items-start">
                                        <i class="fas fa-apple text-gray-400 mt-1 mr-3"></i>
                                        <span><strong>Swift UI:</strong> Modern iOS kullanıcı arayüzü</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-cubes text-blue-600 mt-1 mr-3"></i>
                                        <span><strong>Core Data:</strong> Efficient data management</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-share-alt text-green-600 mt-1 mr-3"></i>
                                        <span><strong>Share Extensions:</strong> iOS native sharing</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Android Özellikleri</h4>
                                <ul class="space-y-3 text-gray-600">
                                    <li class="flex items-start">
                                        <i class="fab fa-android text-green-600 mt-1 mr-3"></i>
                                        <span><strong>Material Design:</strong> Modern Android UI</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-database text-blue-600 mt-1 mr-3"></i>
                                        <span><strong>Room Database:</strong> Local data persistence</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-cogs text-purple-600 mt-1 mr-3"></i>
                                        <span><strong>Background Services:</strong> Continuous operation</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin App Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Admin Mobil Uygulaması
                        <span class="block text-lg text-violet-600">Admin Mobile App</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Web sitenizi mobil cihazlardan yönetin. Full-featured admin panel 
                        ile content management, user management ve analytics erişimi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-violet-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-tachometer-alt text-violet-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Dashboard
                                    <span class="block text-sm text-gray-500">Yönetim Paneli</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            Mobile-optimized dashboard ile real-time metrics, 
                            quick actions ve notification center erişimi.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                                <span class="text-gray-700">Real-time Analytics</span>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-bell text-green-600 mr-3"></i>
                                <span class="text-gray-700">Notification Center</span>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-bolt text-purple-600 mr-3"></i>
                                <span class="text-gray-700">Quick Actions</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-xl">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-edit text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Content Editor
                                    <span class="block text-sm text-gray-500">İçerik Editörü</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            Mobile content editor ile anywhere content creation. 
                            Voice-to-text, image editing ve draft management.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-microphone text-blue-600 mr-3"></i>
                                <span class="text-gray-700">Voice-to-Text</span>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-camera text-green-600 mr-3"></i>
                                <span class="text-gray-700">Camera Integration</span>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-save text-purple-600 mr-3"></i>
                                <span class="text-gray-700">Auto-Save Drafts</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-8 text-white">
                    <div class="max-w-4xl mx-auto">
                        <h3 class="text-2xl font-bold mb-4">
                            Mobil Yönetim Özellikleri
                            <span class="block text-lg text-gray-300">Mobile Management Features</span>
                        </h3>
                        <p class="text-gray-300 mb-8">
                            Tam özellikli mobil admin paneli ile web sitenizi her yerden kontrol edin. 
                            Touch-optimized interface ile native mobile experience.
                        </p>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="bg-white/10 p-6 rounded-lg">
                                <h4 class="font-semibold mb-3">İçerik Yönetimi</h4>
                                <div class="space-y-2 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-violet-400 mr-2"></i>
                                        <span>Makale oluşturma ve düzenleme</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-violet-400 mr-2"></i>
                                        <span>Medya yükleme ve yönetimi</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-violet-400 mr-2"></i>
                                        <span>Kategori ve etiket yönetimi</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-violet-400 mr-2"></i>
                                        <span>Yayınlama ve planlama</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/10 p-6 rounded-lg">
                                <h4 class="font-semibold mb-3">Kullanıcı Yönetimi</h4>
                                <div class="space-y-2 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-blue-400 mr-2"></i>
                                        <span>Kullanıcı kayıt ve düzenleme</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-blue-400 mr-2"></i>
                                        <span>Rol ve izin yönetimi</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-blue-400 mr-2"></i>
                                        <span>Aktivite izleme</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-blue-400 mr-2"></i>
                                        <span>Mesajlaşma sistemi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/10 p-6 rounded-lg">
                                <h4 class="font-semibold mb-3">Analytics ve Raporlar</h4>
                                <div class="space-y-2 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-green-400 mr-2"></i>
                                        <span>Ziyaretçi istatistikleri</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-green-400 mr-2"></i>
                                        <span>İçerik performans analizi</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-green-400 mr-2"></i>
                                        <span>Gelir raporları</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-green-400 mr-2"></i>
                                        <span>Özelleştirilmiş raporlar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section id="download" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Mobil Uygulamayı İndirin
                    <span class="block text-lg text-violet-600">Download Mobile App</span>
                </h2>
                <p class="text-xl text-gray-600 mb-12">
                    iOS ve Android cihazlarınızda native mobile experience. 
                    App Store ve Google Play Store'da mevcuttur.
                </p>
                
                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="w-16 h-16 bg-black rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fab fa-apple text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">iOS App</h3>
                        <p class="text-gray-600 mb-6">
                            iPhone ve iPad için optimize edilmiş native iOS uygulaması. 
                            iOS 13.0 ve üzeri sürümler desteklenir.
                        </p>
                        <a href="#" class="inline-flex items-center bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors">
                            <i class="fab fa-apple mr-2"></i>
                            App Store'dan İndir
                        </a>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fab fa-google-play text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Android App</h3>
                        <p class="text-gray-600 mb-6">
                            Android cihazlar için optimize edilmiş native Android uygulaması. 
                            Android 8.0 ve üzeri sürümler desteklenir.
                        </p>
                        <a href="#" class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fab fa-google-play mr-2"></i>
                            Google Play'den İndir
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">
                        Sistem Gereksinimleri
                        <span class="block text-lg text-violet-600">System Requirements</span>
                    </h3>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">iOS Gereksinimleri</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li>• iOS 13.0 veya üzeri</li>
                                <li>• iPhone 6s veya üzeri</li>
                                <li>• iPad Air 2 veya üzeri</li>
                                <li>• 100 MB boş depolama alanı</li>
                                <li>• Internet bağlantısı (sync için)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Android Gereksinimleri</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li>• Android 8.0 (API level 26) veya üzeri</li>
                                <li>• 2 GB RAM minimum</li>
                                <li>• 100 MB boş depolama alanı</li>
                                <li>• Internet bağlantısı (sync için)</li>
                                <li>• Google Play Services</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-violet-600 to-purple-600">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Mobil Çağda Bir Adım Önde Olun
                </h2>
                <p class="text-xl text-violet-100 mb-8 max-w-2xl mx-auto">
                    Native mobile apps ile kullanıcılarınıza superior mobile experience sunun. 
                    Offline access, push notifications ve native performance.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="bg-white text-violet-600 px-8 py-3 rounded-lg font-medium hover:bg-violet-50 transition-colors">
                        Demo Talep Et
                    </a>
                    <a href="#pricing" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white hover:text-violet-600 transition-colors">
                        Fiyatları Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>