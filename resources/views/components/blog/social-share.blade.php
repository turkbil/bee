@props(['url', 'title', 'position' => 'sticky'])

@php
    $shareUrl = urlencode($url);
    $shareTitle = urlencode($title);

    $positionClasses = $position === 'sticky'
        ? 'hidden lg:flex fixed left-8 top-1/2 -translate-y-1/2 flex-col gap-3'
        : 'flex flex-row gap-3 justify-center';
@endphp

<div class="{{ $positionClasses }}" {{ $attributes }}>
    {{-- WhatsApp --}}
    <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="group flex items-center justify-center w-12 h-12 rounded-full bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
       title="WhatsApp'ta Paylaş">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

    {{-- Facebook --}}
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="group flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
       title="Facebook'ta Paylaş">
        <i class="fab fa-facebook-f text-xl"></i>
    </a>

    {{-- Twitter/X --}}
    <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="group flex items-center justify-center w-12 h-12 rounded-full bg-gray-900 hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
       title="X/Twitter'da Paylaş">
        <i class="fab fa-x-twitter text-xl"></i>
    </a>

    {{-- LinkedIn --}}
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="group flex items-center justify-center w-12 h-12 rounded-full bg-blue-700 hover:bg-blue-800 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
       title="LinkedIn'de Paylaş">
        <i class="fab fa-linkedin-in text-xl"></i>
    </a>

    {{-- Email --}}
    <a href="mailto:?subject={{ $shareTitle }}&body={{ $shareUrl }}"
       class="group flex items-center justify-center w-12 h-12 rounded-full bg-gray-600 hover:bg-gray-700 dark:bg-gray-800 dark:hover:bg-gray-900 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
       title="E-posta ile Paylaş">
        <i class="fas fa-envelope text-xl"></i>
    </a>

    {{-- Copy Link --}}
    <button type="button"
            x-data="{ copied: false }"
            @click="
                navigator.clipboard.writeText('{{ $url }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
            class="group flex items-center justify-center w-12 h-12 rounded-full bg-gray-500 hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
            title="Linki Kopyala">
        <i class="fas fa-link text-xl" x-show="!copied"></i>
        <i class="fas fa-check text-xl" x-show="copied" x-cloak></i>
    </button>
</div>

{{-- Mobile Bottom Bar --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg p-3 z-50">
    <div class="flex items-center justify-around max-w-md mx-auto">
        {{-- WhatsApp --}}
        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex flex-col items-center gap-1 text-green-600 hover:text-green-700 transition-colors">
            <i class="fab fa-whatsapp text-2xl"></i>
            <span class="text-xs font-medium">WhatsApp</span>
        </a>

        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex flex-col items-center gap-1 text-blue-600 hover:text-blue-700 transition-colors">
            <i class="fab fa-facebook-f text-2xl"></i>
            <span class="text-xs font-medium">Facebook</span>
        </a>

        {{-- Twitter/X --}}
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex flex-col items-center gap-1 text-gray-900 dark:text-gray-300 hover:text-black dark:hover:text-white transition-colors">
            <i class="fab fa-x-twitter text-2xl"></i>
            <span class="text-xs font-medium">Twitter</span>
        </a>

        {{-- Copy Link --}}
        <button type="button"
                x-data="{ copied: false }"
                @click="
                    navigator.clipboard.writeText('{{ $url }}');
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
                class="flex flex-col items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
            <i class="fas fa-link text-2xl" x-show="!copied"></i>
            <i class="fas fa-check text-2xl text-green-600" x-show="copied" x-cloak></i>
            <span class="text-xs font-medium" x-text="copied ? 'Kopyalandı!' : 'Kopyala'"></span>
        </button>
    </div>
</div>
