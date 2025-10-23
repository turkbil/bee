{{--
    AI Chat Inline Widget

    Sayfa içine gömülü (embedded) chatbot widget'ı.
    Tailwind CSS + Alpine.js ile oluşturulmuştur.

    Kullanım:
    <x-ai.inline-widget />

    <x-ai.inline-widget
        title="Ürün Hakkında Soru Sor"
        :product-id="$product->product_id"
        :initially-open="true"
    />

    Props:
    @props [
        'title' => 'string',
        'productId' => 'int',
        'categoryId' => 'int',
        'pageSlug' => 'string',
        'initiallyOpen' => 'bool',
        'height' => 'string',
        'theme' => 'blue|green|purple|gray',
    ]
--}}

@props([
    'title' => 'Yapay Zeka Asistanı ile Konuşun',
    'productId' => null,
    'categoryId' => null,
    'pageSlug' => null,
    'initiallyOpen' => false,
    'alwaysOpen' => false,
    'height' => '500px',
    'theme' => 'blue',
    'widgetId' => null,
])

@php
// Unique widget ID
$widgetId = $widgetId ?? 'inline-' . uniqid();

$themeClasses = [
    'blue' => [
        'header' => 'bg-gradient-to-r from-blue-600 to-blue-700',
        'button' => 'bg-blue-600 hover:bg-blue-700',
    ],
    'green' => [
        'header' => 'bg-gradient-to-r from-green-600 to-green-700',
        'button' => 'bg-green-600 hover:bg-green-700',
    ],
    'purple' => [
        'header' => 'bg-gradient-to-r from-purple-600 to-purple-700',
        'button' => 'bg-purple-600 hover:bg-purple-700',
    ],
    'gray' => [
        'header' => 'bg-gradient-to-r from-gray-700 to-gray-800',
        'button' => 'bg-gray-700 hover:bg-gray-800',
    ],
];

$selectedTheme = $themeClasses[$theme] ?? $themeClasses['blue'];
@endphp

{{-- AI Chat External CSS/JS (once per page) --}}
@once
    <link rel="stylesheet" href="{{ asset('assets/css/ai-chat.css') }}?v={{ now()->timestamp }}" media="all">
    {{-- DEFER KALDIRILDI: Livewire Alpine ile senkronize çalışsın --}}
    <script src="{{ asset('assets/js/ai-chat.js') }}?v={{ now()->timestamp }}"></script>
@endonce

<div
    x-data="{
        chat: $store.aiChat,
        widgetId: '{{ $widgetId }}',
        message: '',
        isOpen: {{ ($initiallyOpen || $alwaysOpen) ? 'true' : 'false' }},
        alwaysOpen: {{ $alwaysOpen ? 'true' : 'false' }},

        init() {
            // Register this widget
            this.chat.registerInline(this.widgetId);

            // Set context if provided
            @if($productId || $categoryId || $pageSlug)
            this.chat.updateContext({
                @if($productId) product_id: {{ $productId }}, @endif
                @if($categoryId) category_id: {{ $categoryId }}, @endif
                @if($pageSlug) page_slug: '{{ $pageSlug }}', @endif
            });
            @endif

            // Removed welcome message - using placeholder animation instead

            // Scroll to bottom after init (if open)
            if (this.isOpen) {
                setTimeout(() => {
                    this.chat.scrollToBottom();
                }, 400);
            }
        },

        toggle() {
            // If alwaysOpen, don't toggle
            if (this.alwaysOpen) return;

            this.isOpen = !this.isOpen;

            // Scroll to bottom when opening
            if (this.isOpen) {
                setTimeout(() => {
                    this.chat.scrollToBottom();
                }, 200);
            }
        },

        submitMessage() {
            if (this.message.trim()) {
                const context = {};
                @if($productId) context.product_id = {{ $productId }}; @endif
                @if($categoryId) context.category_id = {{ $categoryId }}; @endif
                @if($pageSlug) context.page_slug = '{{ $pageSlug }}'; @endif

                this.chat.sendMessage(this.message, context);
                this.message = '';
            }
        }
    }"
    class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700"
>
    {{-- Header --}}
    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white px-6 py-4">
        <div class="flex items-center justify-between" :class="{ 'cursor-pointer': !alwaysOpen }" @click="toggle()">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">{{ $title }}</h3>
                    <p class="text-xs text-white/80" x-text="chat.assistantName"></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Message count badge --}}
                <span
                    x-show="chat.messageCount > 0"
                    x-text="chat.messageCount"
                    class="bg-white/20 text-white text-xs font-bold rounded-full px-2 py-1"
                    x-cloak
                ></span>

                {{-- Toggle icon (hidden if alwaysOpen) --}}
                <svg x-show="!alwaysOpen" class="w-6 h-6 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Chat Container --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        style="height: {{ $height }};"
        class="flex flex-col"
        x-cloak
    >
        {{-- Messages Container --}}
        <div
            data-ai-chat-messages
            class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50 dark:bg-gray-900"
        >
            {{-- Placeholder Animation (V4 - Slide Up - Dynamic Product-specific) --}}
            <div x-show="!chat.hasConversation"
                 x-data="placeholderV4({{ $productId ? "'" . $productId . "'" : 'null' }})"
                 x-init="await init(); await start()"
                 class="space-y-3">
            </div>

            {{-- Messages --}}
            <template x-for="(msg, index) in chat.messages" :key="index">
                <div
                    :class="{
                        'flex justify-end': msg.role === 'user',
                        'flex justify-start': msg.role !== 'user'
                    }"
                >
                    <div
                        :class="{
                            'bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700': msg.role === 'user',
                            'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700': msg.role === 'assistant',
                            'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700': msg.role === 'system',
                            'bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700': msg.isError
                        }"
                        class="max-w-[80%] rounded-2xl px-4 py-3 shadow-sm"
                    >
                        <div
                            :class="{
                                'text-white [&_*]:!text-white': msg.role === 'user',
                                'text-gray-800 dark:text-white dark:[&_*]:!text-white': msg.role === 'assistant',
                                'text-yellow-800 dark:text-white dark:[&_*]:!text-white': msg.role === 'system',
                                'text-red-800 dark:text-white dark:[&_*]:!text-white': msg.isError
                            }"
                            :data-role="msg.role"
                            class="text-sm prose prose-sm max-w-none prose-strong:font-bold prose-ul:list-disc prose-ul:ml-4 prose-li:my-1 dark:prose-invert ai-message-content"
                            x-html="window.aiChatRenderMarkdown(msg.content)"
                        ></div>
                        <p
                            :class="{
                                'text-white opacity-80': msg.role === 'user',
                                'text-gray-500 dark:text-gray-400': msg.role === 'assistant',
                                'text-yellow-700 dark:text-yellow-300': msg.role === 'system',
                                'text-red-700 dark:text-red-300': msg.isError
                            }"
                            class="text-xs mt-1"
                            x-text="new Date(msg.created_at).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })"
                        ></p>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="chat.isTyping" class="flex justify-start">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 shadow-sm">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            {{-- Error message --}}
            <div x-show="chat.error" class="mb-3 p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm rounded-lg" x-cloak>
                <span x-text="chat.error"></span>
            </div>

            {{-- Input form --}}
            <form @submit.prevent="submitMessage()" class="flex gap-2">
                <input
                    type="text"
                    x-model="message"
                    placeholder="Mesajınızı yazın..."
                    :disabled="chat.isLoading"
                    class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed transition-all"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    :disabled="!message.trim() || chat.isLoading"
                    class="{{ $selectedTheme['button'] }} text-white px-6 py-2.5 rounded-full disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors shadow-sm hover:shadow-md"
                >
                    <svg class="w-5 h-5" :class="{ 'animate-spin': chat.isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!chat.isLoading" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        <path x-show="chat.isLoading" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </form>

            {{-- Footer Info - Always visible --}}
            <div class="mt-2">
                <p style="font-size: 11px !important; line-height: 1.4 !important; opacity: 0.9 !important;"
                   class="text-gray-800 dark:text-white">
                    Bu yapay zeka destekli sohbet asistanı, iXtif yazılım mühendisleri tarafından iXtif için özel olarak hazırlanmıştır.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Inline CSS kaldırıldı - Tüm stiller /public/assets/css/ai-chat.css dosyasında --}}

{{-- DEBUG CONSOLE KODU (Console'a yapıştır) --}}
{{--
// DARK MODE TEXT DEBUG
console.log('=== DARK MODE DEBUG ===');
console.log('HTML has dark class:', document.documentElement.classList.contains('dark'));
console.log('All message elements:', document.querySelectorAll('.ai-message-content'));

document.querySelectorAll('.ai-message-content').forEach((el, i) => {
    console.log(`Message ${i + 1}:`);
    console.log('  Element:', el);
    console.log('  Computed color:', window.getComputedStyle(el).color);
    console.log('  Classes:', el.className);

    el.querySelectorAll('*').forEach((child, j) => {
        console.log(`  Child ${j + 1}:`, child.tagName, window.getComputedStyle(child).color);
    });
});

// FORCE FIX (test için)
document.querySelectorAll('.ai-message-content, .ai-message-content *').forEach(el => {
    el.style.setProperty('color', 'white', 'important');
});
console.log('✅ Forced white color to all elements');
--}}
