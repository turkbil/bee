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

    {{-- Chat Button - V1 Classic Pulse Design --}}
    <div x-show="!chat.floatingOpen"
         x-transition:enter="transition-opacity ease-out duration-500"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="transition-delay: 0.5s;"
         class="absolute bottom-0 right-0"
         x-cloak>
    <button
        @click="chat.toggleFloating(); clearTimeout(autoOpenTimer);"
        x-data="{
            messages: [
                'Merhaba! Nasıl yardımcı olabilirim? 👋',
                'Ürünler hakkında her şeyi sorabilirsiniz',
                'Size en uygun modeli bulabilirim',
                'Sorularınızı anında yanıtlıyorum ⚡',
                'Akıllı ürün karşılaştırması yapabilirim',
                'Teknik detayları kolayca açıklıyorum',
                'İhtiyacınıza göre öneri sunuyorum',
                '7/24 anlık destek burada 🤖',
                'Tüm özellikleri size anlatayım',
                'Merak ettiklerinizi öğrenin',
                'Ürün seçiminde yardımcı oluyorum',
                'Karmaşık bilgileri basitleştiriyorum',
                'Size özel çözüm öneriyorum',
                'Her sorunuza hızlı cevap veriyorum',
                'Doğru ürünü bulmak çok kolay',
                'Akıllı asistan olarak buradayım',
                'Soru sormaktan çekinmeyin',
                'Anında bilgi alın',
                'İhtiyacınızı anlıyor ve yönlendiriyorum',
                'Hadi konuşalım! 💬'
            ],
            currentIndex: 0,
            currentMessage: 'Merhaba! Nasıl yardımcı olabilirim? 👋',
            bubbleVisible: true,

            init() {
                // Animation cycle: 3s total
                // 0-0.6s (20%): fade in
                // 0.6-2.4s (20-80%): visible
                // 2.4-3s (80-100%): fade out
                // Change message during fade out (at 2.4s = 80%)

                setInterval(() => {
                    // Hide bubble (opacity will fade via animation)
                    this.bubbleVisible = false;

                    // Change message after 300ms (during fade-out)
                    setTimeout(() => {
                        this.currentIndex = (this.currentIndex + 1) % this.messages.length;
                        this.currentMessage = this.messages[this.currentIndex];
                        this.bubbleVisible = true;
                    }, 300);
                }, 3000);
            }
        }"
        class="relative group"
        aria-label="Sohbeti Aç"
    >
        {{-- Bubble Container (Message + Arrow as one unit) --}}
        <div
            :class="{ 'opacity-0 pointer-events-none': chat.floatingOpen || !bubbleVisible }"
            class="absolute z-[101] transition-opacity duration-300"
            style="top: -70px; right: -10px; filter: drop-shadow(0 6px 25px rgba(0,0,0,0.3));"
        >
            {{-- Bubble Message --}}
            <div
                x-text="currentMessage"
                class="bg-white px-5 py-3 rounded-full text-sm font-bold whitespace-nowrap"
                style="color: #667eea; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"
            >
            </div>

            {{-- Bubble Arrow --}}
            <div
                class="absolute w-0 h-0"
                style="bottom: -10px; right: 39px; border-left: 11px solid transparent; border-right: 11px solid transparent; border-top: 11px solid white;"
            ></div>
        </div>

        {{-- Main Button with V1 Classic Pulse --}}
        <div class="relative w-20 h-20 rounded-full shadow-2xl transition-all duration-300 flex items-center justify-center group-hover:scale-110 v1-classic-button"
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);">
                {{-- Chatbot SVG Icon --}}
                <svg class="w-12 h-12 transition-transform group-hover:scale-110 group-hover:rotate-6" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="white">
                    <path d="m181 301c-8.284 0-15 6.716-15 15v30c0 8.284 6.716 15 15 15s15-6.716 15-15v-30c0-8.284-6.716-15-15-15z"></path>
                    <path d="m331 361c8.284 0 15-6.716 15-15v-30c0-8.284-6.716-15-15-15s-15 6.716-15 15v30c0 8.284 6.716 15 15 15z"></path>
                    <path d="m272 106h164c8.284 0 15-6.716 15-15s-6.716-15-15-15h-164c-8.284 0-15 6.716-15 15s6.716 15 15 15z"></path>
                    <path d="m512 176v-111c0-35.841-29.159-65-65-65h-186c-35.841 0-65 29.159-65 65v116h-20c-54.827 0-100.809 38.57-112.255 90h-2.745v-92.58c17.459-6.192 30-22.865 30-42.42 0-24.813-20.187-45-45-45s-45 20.187-45 45c0 19.555 12.541 36.228 30 42.42v94.821c-17.977 5.901-31 22.833-31 42.759v60c0 24.813 20.187 45 45 45h18.527c11.069 51.929 57.291 91 112.473 91h160c55.182 0 101.404-39.071 112.473-91h18.527c24.813 0 45-20.187 45-45v-60c0-24.813-20.187-45-45-45h-18.751c-2.331-10.48-6.115-20.577-11.247-30h9.998c35.841 0 65-29.159 65-65zm-286-111c0-19.299 15.701-35 35-35h186c19.299 0 35 15.701 35 35v111c0 19.299-15.701 35-35 35h-176c-2.329 0-4.625.542-6.708 1.583l-38.292 19.146zm-180 56c8.271 0 15 6.729 15 15s-6.729 15-15 15-15-6.729-15-15 6.729-15 15-15zm-16 255v-60c0-8.271 6.729-15 15-15h16v90h-16c-8.271 0-15-6.729-15-15zm452-60v60c0 8.271-6.729 15-15 15h-16v-90h16c8.271 0 15 6.729 15 15zm-61-20v101c0 46.869-38.131 85-85 85h-160c-46.869 0-85-38.131-85-85v-101c0-46.869 38.131-85 85-85h20v45c0 11.132 11.742 18.4 21.708 13.416l56.833-28.416h126.241c13.038 15.344 20.218 34.804 20.218 55z"></path>
                    <path d="m272 166h164c8.284 0 15-6.716 15-15s-6.716-15-15-15h-164c-8.284 0-15 6.716-15 15s6.716 15 15 15z"></path>
                    <path d="m211 406c0 8.284 6.716 15 15 15h60c8.284 0 15-6.716 15-15s-6.716-15-15-15h-60c-8.284 0-15 6.716-15 15z"></path>
                </svg>

            {{-- Online Dot --}}
            <span class="absolute top-0 right-0 w-4 h-4 bg-green-400 rounded-full animate-ping"></span>
            <span class="absolute top-0 right-0 w-4 h-4 bg-green-500 rounded-full"></span>
        </div>

        {{-- Unread badge --}}
        <span x-show="chat.messageCount > 0 && !chat.floatingOpen"
              x-text="chat.messageCount"
              class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center animate-bounce shadow-lg"
              x-cloak></span>
    </button>
    </div>

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
                            :data-role="msg.role"
                            class="text-sm prose prose-sm max-w-none dark:prose-invert ai-floating-message-content"
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
                Yapay zeka destekli müşteri asistanı
            </p>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

/* Custom Animations for AI Robot Button */

/* V1 Classic Pulse - Exact copy from test page */
.v1-classic-button::before {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 50%;
    background: inherit;
    z-index: -1;
    animation: pulse-v1 2s infinite;
}

@keyframes pulse-v1 {
    0%, 100% {
        transform: scale(1);
        opacity: 0.8;
    }
    50% {
        transform: scale(1.15);
        opacity: 0;
    }
}

/* Bubble Animation */
@keyframes bubble-fade {
    0%, 100% {
        opacity: 0;
        transform: translateY(10px) scale(0.85);
    }
    20%, 80% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

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

.animate-bubble-fade {
    animation: bubble-fade 3s infinite;
}

/* USER MESSAGES - Always white text (both light and dark mode) */
.ai-floating-message-content[data-role="user"],
.ai-floating-message-content[data-role="user"] *,
.ai-floating-message-content[data-role="user"] p,
.ai-floating-message-content[data-role="user"] span,
.ai-floating-message-content[data-role="user"] strong,
.ai-floating-message-content[data-role="user"] em,
.ai-floating-message-content[data-role="user"] li,
.ai-floating-message-content[data-role="user"] ul,
.ai-floating-message-content[data-role="user"] ol,
.ai-floating-message-content[data-role="user"] h1,
.ai-floating-message-content[data-role="user"] h2,
.ai-floating-message-content[data-role="user"] h3 {
    color: white !important;
}

/* DARK MODE TEXT FIX - Assistant/System messages white in dark mode */
.dark .ai-floating-message-content[data-role="assistant"],
.dark .ai-floating-message-content[data-role="assistant"] *,
.dark .ai-floating-message-content[data-role="system"],
.dark .ai-floating-message-content[data-role="system"] * {
    color: white !important;
}

/* Links - Special colors */
.ai-floating-message-content[data-role="user"] a {
    color: rgba(255, 255, 255, 0.9) !important;
    text-decoration: underline;
}

.dark .ai-floating-message-content a {
    color: #fb923c !important; /* orange-400 */
}
</style>
