{{--
    Alpine.js AI Chat Store Component v2.0.2

    Global state management for AI chatbot widgets.
    Bu component sayfaya bir kez dahil edilmeli ve tüm widget'lar bu store'u kullanmalı.

    Kullanım:
    <x-ai.chat-store />

    Widget'lardan erişim:
    <div x-data="{ chat: $store.aiChat }">
        <button @click="chat.toggleFloating()">Toggle Chat</button>
    </div>

    Changelog v2.0.2:
    - ✅ Inline CSS/JS kaldırıldı (900+ satır temizlendi)
    - ✅ External files kullanılıyor: ai-chat.css, ai-chat.js
    - ✅ Cache-friendly, minify edilebilir
    - ✅ Component temiz ve bakımı kolay
--}}

{{-- AI Chat System External Files --}}
@once
    @php
        $aiChatCssPath = public_path('assets/css/ai-chat.css');
        $aiChatJsPath = public_path('assets/js/ai-chat.js');
        $cssVersion = file_exists($aiChatCssPath) ? filemtime($aiChatCssPath) : time();
        $jsVersion = file_exists($aiChatJsPath) ? filemtime($aiChatJsPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/css/ai-chat.css') }}?v={{ $cssVersion }}" media="all">
    {{-- DEFER KALDIRILDI: Alpine:init event'inde çalıştığı için sorun yok, ama defer timing problemlerine yol açıyordu --}}
    <script src="{{ asset('assets/js/ai-chat.js') }}?v={{ $jsVersion }}"></script>
@endonce
