{{-- Tailwind Ekran Boyutu Göstergesi - Sadece Root Kullanıcılar İçin --}}
@auth
    @if(auth()->user()->hasRole('root'))
        <div id="tailwind-screen-indicator"></div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Indicator oluştur
            const indicator = document.getElementById('tailwind-screen-indicator');

            function updateScreenSize() {
                const w = window.innerWidth;
                let breakpoint, color, range;

                // Tailwind breakpoints
                if (w < 640) {
                    breakpoint = 'XS';
                    color = '#ef4444'; // red-500
                    range = '<640px';
                } else if (w < 768) {
                    breakpoint = 'SM';
                    color = '#f97316'; // orange-500
                    range = '640-767px';
                } else if (w < 1024) {
                    breakpoint = 'MD';
                    color = '#eab308'; // yellow-500
                    range = '768-1023px';
                } else if (w < 1280) {
                    breakpoint = 'LG';
                    color = '#22c55e'; // green-500
                    range = '1024-1279px';
                } else if (w < 1536) {
                    breakpoint = 'XL';
                    color = '#3b82f6'; // blue-500
                    range = '1280-1535px';
                } else {
                    breakpoint = '2XL';
                    color = '#a855f7'; // purple-500
                    range = '≥1536px';
                }

                // Style ve içerik güncelle
                indicator.style.cssText = `
                    position: fixed;
                    bottom: 16px;
                    left: 16px;
                    z-index: 9999;
                    background-color: ${color};
                    color: white;
                    padding: 8px 12px;
                    border-radius: 8px;
                    font-weight: bold;
                    font-size: 14px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    border: 2px solid white;
                    pointer-events: none;
                    user-select: none;
                `;

                indicator.innerHTML = `
                    <span style="font-size: 10px; opacity: 0.75; margin-right: 4px;">${range}</span>
                    <span style="font-size: 16px;">${breakpoint}</span>
                `;
            }

            // İlk çalıştır
            updateScreenSize();

            // Resize'da güncelle
            window.addEventListener('resize', updateScreenSize);
        });
        </script>
    @endif
@endauth
