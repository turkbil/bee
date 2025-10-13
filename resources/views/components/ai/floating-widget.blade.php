{{--
    AI Chat Floating Widget

    Sağ alt köşede sabit duran, açılır-kapanır chatbot widget'ı.
    Tailwind CSS + Alpine.js ile oluşturulmuştur.

    Kullanım:
    <x-ai.floating-widget />

    Props:
    @props [
        'buttonText' => 'string',
        'buttonIcon' => 'svg|html',
        'position' => 'bottom-right|bottom-left',
        'theme' => 'blue|green|purple|gray',
    ]
--}}

@props([
    'buttonText' => 'Canlı Destek',
    'position' => 'bottom-right',
    'theme' => 'blue',
])

@php
$themeClasses = [
    'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
    'green' => 'bg-green-600 hover:bg-green-700 text-white',
    'purple' => 'bg-purple-600 hover:bg-purple-700 text-white',
    'gray' => 'bg-gray-800 hover:bg-gray-900 text-white',
];

$positionClasses = [
    'bottom-right' => 'bottom-6 right-6',
    'bottom-left' => 'bottom-6 left-6',
];

$selectedTheme = $themeClasses[$theme] ?? $themeClasses['blue'];
$selectedPosition = $positionClasses[$position] ?? $positionClasses['bottom-right'];
@endphp

<div x-data="{
    chat: $store.aiChat,
    message: '',
    autoOpenTimer: null,

    init() {
        // Auto-open after 10 seconds if user hasn't interacted
        this.autoOpenTimer = setTimeout(() => {
            if (!this.chat.floatingOpen && !this.chat.hasConversation) {
                console.log('🤖 Auto-opening AI chat...');
                this.chat.openFloating();
            }
        }, 10000);
    },

    destroy() {
        // Clear timer on component destroy
        if (this.autoOpenTimer) {
            clearTimeout(this.autoOpenTimer);
        }
    },

    submitMessage() {
        if (this.message.trim()) {
            this.chat.sendMessage(this.message);
            this.message = '';
        }
    }
}"
class="fixed {{ $selectedPosition }} z-50">

    {{-- Chat Button with Awesome Animations --}}
    <button
        @click="chat.toggleFloating(); clearTimeout(autoOpenTimer);"
        x-show="!chat.floatingOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        class="relative group"
        aria-label="Sohbeti Aç"
    >
        {{-- Main Button Container with Ripple --}}
        <div class="relative flex items-center justify-center">
            {{-- Ripple Animation Circles (İÇ İÇE GEÇEN) --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-blue-400/30 dark:bg-blue-500/30 animate-ping"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center" style="animation-delay: 0.5s;">
                <div class="w-24 h-24 rounded-full bg-purple-400/20 dark:bg-purple-500/20 animate-ping"></div>
            </div>

            {{-- Glow Effect --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 opacity-75 group-hover:opacity-100 blur animate-pulse"></div>
            </div>

            {{-- Main Button (İÇTE, MERKEZİ) --}}
            <div class="relative flex items-center gap-3 px-5 py-3 rounded-full shadow-xl {{ $selectedTheme }} hover:shadow-2xl transition-all duration-300 animate-bounce-slow z-10">
                {{-- Wizard/Robot Icon --}}
                <div class="relative">
                    {{-- Sihirbaz/Robot SVG (Emoji tarzı) --}}
                    <svg class="w-8 h-8 animate-wiggle" viewBox="0 0 64 64" fill="currentColor">
                        {{-- Wizard Hat --}}
                        <path d="M32 4 L28 20 L36 20 Z" fill="#FCD34D"/>
                        <circle cx="32" cy="18" r="2" fill="#F59E0B"/>

                        {{-- Face/Head --}}
                        <circle cx="32" cy="32" r="14" fill="#60A5FA"/>

                        {{-- Eyes --}}
                        <circle cx="27" cy="30" r="2.5" fill="#1E3A8A"/>
                        <circle cx="37" cy="30" r="2.5" fill="#1E3A8A"/>

                        {{-- Happy Smile --}}
                        <path d="M 25 36 Q 32 40 39 36" stroke="#1E3A8A" stroke-width="2" fill="none" stroke-linecap="round"/>

                        {{-- Stars around --}}
                        <text x="14" y="20" font-size="8" fill="#FCD34D">✨</text>
                        <text x="44" y="20" font-size="8" fill="#FCD34D">✨</text>
                    </svg>

                    {{-- Online Pulse Dot --}}
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-ping"></span>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></span>
                </div>

                <span class="font-bold text-sm hidden sm:inline animate-pulse-slow">Yapay Zeka</span>
            </div>
        </div>

        {{-- Text Below --}}
        <div class="mt-2 text-center">
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 px-3 py-1 rounded-full shadow-md">
                Soru Sor 💬
            </span>
        </div>

        {{-- Unread badge --}}
        <span x-show="chat.messageCount > 0 && !chat.floatingOpen"
              x-text="chat.messageCount"
              class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center animate-bounce shadow-lg"
              x-cloak></span>
    </button>

    {{-- Chat Window --}}
    <div
        x-show="chat.floatingOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-96 h-[600px] flex flex-col overflow-hidden border border-gray-200 dark:border-gray-700"
        style="max-height: calc(100vh - 120px);"
        x-cloak
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg" x-text="chat.assistantName"></h3>
                    <p class="text-xs text-blue-100">Online</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Clear button --}}
                <button
                    @click="chat.clearConversation()"
                    class="p-2 hover:bg-white/10 rounded-lg transition"
                    title="Konuşmayı Temizle"
                    x-show="chat.hasConversation"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>

                {{-- Close button --}}
                <button
                    @click="chat.closeFloating()"
                    class="p-2 hover:bg-white/10 rounded-lg transition"
                    title="Kapat"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages Container --}}
        <div
            data-ai-chat-messages
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
        >
            {{-- Empty state --}}
            <div x-show="!chat.hasConversation" class="text-center text-gray-500 dark:text-gray-400 mt-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                </svg>
                <p class="text-sm">Henüz mesaj yok.</p>
                <p class="text-xs mt-1">Bir mesaj yazarak başlayın!</p>
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
                            class="text-sm prose prose-sm max-w-none dark:prose-invert ai-floating-message-content"
                            x-html="window.aiChatRenderMarkdown(msg.content)"
                            x-init="
                                // FORCE white color in dark mode for ALL child elements
                                if (document.documentElement.classList.contains('dark')) {
                                    $el.querySelectorAll('*').forEach(child => {
                                        child.style.setProperty('color', 'white', 'important');
                                    });
                                }
                            "
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
        <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
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
                    class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed transition-all"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    :disabled="!message.trim() || chat.isLoading"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-full disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed transition-colors shadow-sm"
                >
                    <svg class="w-5 h-5" :class="{ 'animate-spin': chat.isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!chat.isLoading" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        <path x-show="chat.isLoading" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </form>

            {{-- Powered by --}}
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center mt-2">
                AI destekli müşteri asistanı
            </p>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

/* Custom Animations for AI Robot Button */
@keyframes wiggle {
    0%, 100% { transform: rotate(-5deg); }
    50% { transform: rotate(5deg); }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(-15px);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.animate-wiggle {
    animation: wiggle 2s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

/* DARK MODE TEXT FIX - Floating Widget Messages */
.dark .ai-floating-message-content,
.dark .ai-floating-message-content *,
.dark .ai-floating-message-content p,
.dark .ai-floating-message-content span,
.dark .ai-floating-message-content strong,
.dark .ai-floating-message-content em,
.dark .ai-floating-message-content li,
.dark .ai-floating-message-content ul,
.dark .ai-floating-message-content ol,
.dark .ai-floating-message-content h1,
.dark .ai-floating-message-content h2,
.dark .ai-floating-message-content h3 {
    color: white !important;
}

/* Links özel renk (turuncu) */
.dark .ai-floating-message-content a {
    color: #fb923c !important; /* orange-400 */
}
</style>
