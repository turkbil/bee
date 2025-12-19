    {{-- Widget Integration --}}
    @widgetstyles
    @widgetscripts

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- marked.js: Markdown parser for AI Chat --}}
    <script src="https://cdn.jsdelivr.net/npm/marked@11.1.1/lib/marked.umd.min.js"></script>

    {{-- AI Chat JS --}}
    <script src="/assets/js/ai-chat.js?v={{ time() }}"></script>

    {{-- Admin Cache & AI Chat Functions --}}
    <script>
    (function() {
        window.clearSystemCache = function(button) {
            if (!button) return;
            button.disabled = true;
            const buttonText = button.querySelector('.button-text');
            if (buttonText) buttonText.textContent = 'Temizleniyor...';

            fetch('/opcache-reset.php', { method: 'GET', credentials: 'same-origin' })
                .then(response => response.json())
                .then(data => {
                    if (buttonText) buttonText.textContent = 'Başarılı!';
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    console.error('[Cache] Clear failed:', error);
                    if (buttonText) buttonText.textContent = 'Hata!';
                    button.disabled = false;
                });
        };

        window.clearAIConversation = function(button) {
            if (typeof window.openAIChat === 'function') {
                window.openAIChat();
            } else {
                console.log('[AI Chat] Opening...');
            }
        };
    })();
    </script>

    {{-- Core System Scripts --}}
    <script defer src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />

    @stack('scripts')
</body>
</html>
