{{-- Universal Notification Toast System - Query Parameter Based --}}
@php
    // Session fallback (tenant-safe)
    $quoteStatus = request()->get('quote_status');
    $hasNotification = $quoteStatus || session('success') || session('error');

    if ($quoteStatus === 'success') {
        $notificationType = 'success';
        $notificationMessage = 'Talebiniz başarıyla gönderildi! En kısa sürede size dönüş yapacağız.';
    } elseif ($quoteStatus === 'error') {
        $notificationType = 'error';
        $notificationMessage = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin veya 0216 755 3 555 numarasını arayın.';
    } elseif (session('success')) {
        $notificationType = 'success';
        $notificationMessage = session('success');
    } elseif (session('error')) {
        $notificationType = 'error';
        $notificationMessage = session('error');
    }
@endphp

@if($hasNotification)
    @php
        $notificationTitle = $notificationType === 'success' ? 'Talebiniz Alındı!' : 'Hata Oluştu';
    @endphp

    <div id="universal-notification"
         class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4 transition-all duration-300"
         style="opacity: 0; z-index: 999999; transform: translate(-50%, -50%) scale(0.9);">

        @if($notificationType === 'success')
            {{-- Success Notification --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-2 border-green-500 dark:border-green-600 overflow-hidden">
                {{-- Green Header Bar --}}
                <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500"></div>

                <div class="p-6 flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-xl"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                            {{ $notificationTitle }}
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $notificationMessage }}
                        </p>
                    </div>

                    {{-- Close Button --}}
                    <button onclick="closeNotification()"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"></i>
                    </button>
                </div>
            </div>
        @else
            {{-- Error Notification --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-2 border-red-500 dark:border-red-600 overflow-hidden">
                {{-- Red Header Bar --}}
                <div class="h-2 bg-gradient-to-r from-red-500 to-rose-500"></div>

                <div class="p-6 flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                            {{ $notificationTitle }}
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $notificationMessage }}
                        </p>
                    </div>

                    {{-- Close Button --}}
                    <button onclick="closeNotification()"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Vanilla JavaScript - Alpine.js'e bağımlı değil
        (function() {
            const notification = document.getElementById('universal-notification');
            if (!notification) return;

            // URL'den query parameter'ı temizle (history API ile)
            if (window.history && window.history.replaceState) {
                const url = new URL(window.location.href);
                if (url.searchParams.has('quote_status')) {
                    url.searchParams.delete('quote_status');
                    window.history.replaceState({}, document.title, url.toString());
                }
            }

            // Animasyonla göster (center fade-in + scale)
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translate(-50%, -50%) scale(1)';
            }, 100);

            // 10 saniye sonra otomatik kapat
            const autoCloseTimer = setTimeout(() => {
                closeNotification();
            }, 10000);

            // Kapatma fonksiyonu
            window.closeNotification = function() {
                clearTimeout(autoCloseTimer);
                notification.style.opacity = '0';
                notification.style.transform = 'translate(-50%, -50%) scale(0.9)';

                // Animasyon bittikten sonra DOM'dan kaldır
                setTimeout(() => {
                    notification.remove();
                }, 300);
            };
        })();
    </script>
@endif

{{-- Toast Notification Component (Alpine.js) - Modern bildirimler için --}}
@include('components.notifications.toast')
