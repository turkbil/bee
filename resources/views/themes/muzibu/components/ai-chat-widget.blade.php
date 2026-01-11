{{-- Muzibu AI Music Assistant Widget --}}
{{-- 🎨 Design inspired by iXtif.com AI Chat Widget --}}
{{-- 🎯 Rotating messages, bubble animations, pulse effects --}}
<div
    x-data="$store.tenant1001AI"
    class="fixed bottom-28 md:bottom-32 right-4 md:right-6 lg:right-8 z-[35]"
    x-cloak
>
    {{-- Floating Button (when closed) --}}
    <div
        x-show="!isOpen"
        x-transition:enter="transition-opacity ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        style="transition-delay: 0.5s;"
    >
        <button
            @click="toggle()"
            x-data="{
                // Desktop messages (3-4 words max)
                desktopMessages: [
                    'Sorunuz mu var? 🎵',
                    'Şarkı arıyorsunuz? 🎤',
                    'Radyo dinle 📻',
                    'Sektör önerisi? 🎶',
                    'Playlist yardımı? 🎧',
                    'Albüm keşfet 🌟',
                    'Hadi konuşalım! 💬'
                ],
                // Mobile messages (2-3 words max)
                mobileMessages: [
                    'Sorunuz var? 🎵',
                    'Şarkı mı? 🎤',
                    'Radyo? 📻',
                    'Sektör? 🎶',
                    'Playlist? 🎧',
                    'Konuşalım! 💬'
                ],
                currentIndex: 0,
                currentMessage: 'Merhaba! 🎵',
                bubbleVisible: true,

                init() {
                    // Set initial message based on screen size
                    this.updateMessage();

                    // Animation cycle optimized for mobile:
                    // - Bubble shows: 1.5s
                    // - Fade out: 300ms
                    // - Robot shows: 1.5s (no bubble)
                    // - Fade in next bubble: 300ms
                    // Total: 3.6s per cycle
                    setInterval(() => {
                        // Show bubble for 1.5s
                        this.bubbleVisible = true;

                        // Hide bubble after 1.5s
                        setTimeout(() => {
                            this.bubbleVisible = false;

                            // Change message while hidden (after fade-out)
                            setTimeout(() => {
                                this.currentIndex = (this.currentIndex + 1) % this.getMessages().length;
                                this.updateMessage();
                            }, 300);
                        }, 1500);
                    }, 3300); // 1.5s visible + 300ms fade + 1.5s robot visible
                },

                getMessages() {
                    return window.innerWidth < 1024 ? this.mobileMessages : this.desktopMessages;
                },

                updateMessage() {
                    this.currentMessage = this.getMessages()[this.currentIndex];
                }
            }"
            class="relative group"
            aria-label="Muzibu Müzik Asistanı"
        >
            {{-- DESKTOP BUBBLE: Classic top bubble with arrow --}}
            <div
                :class="{ 'opacity-0 pointer-events-none': isOpen || !bubbleVisible }"
                class="absolute z-[101] transition-opacity duration-300 max-lg:hidden
                       top-[-70px] right-[-10px]"
                style="filter: drop-shadow(0 6px 25px rgba(0,0,0,0.3));"
            >
                {{-- Bubble Message --}}
                <div
                    x-text="currentMessage"
                    class="bg-white px-5 py-3 rounded-full text-sm font-bold whitespace-nowrap"
                    style="color: #ff7f50; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"
                >
                </div>

                {{-- Bubble Arrow (points down) --}}
                <div
                    class="absolute w-0 h-0 bottom-[-10px] right-[39px]"
                    style="
                        border-left: 11px solid transparent;
                        border-right: 11px solid transparent;
                        border-top: 11px solid white;
                    "
                ></div>
            </div>

            {{-- Main Button with V1 Classic Pulse --}}
            <div class="relative w-20 h-20 rounded-full shadow-2xl transition-all duration-300 flex items-center justify-center group-hover:scale-110 v1-classic-button"
                 style="background: linear-gradient(135deg, #ff7f50 0%, #ec4899 100%); box-shadow: 0 5px 20px rgba(255, 127, 80, 0.4); z-index: 1;">

                {{-- MOBILE CIRCLE: Overlays robot at EXACT same position, z-index on top --}}
                <div
                    :class="{ 'opacity-0 pointer-events-none': isOpen || !bubbleVisible }"
                    class="lg:hidden absolute inset-0 z-[2] transition-opacity duration-300
                           w-20 h-20 rounded-full
                           flex items-center justify-center text-center
                           bg-white shadow-2xl"
                    style="padding: 12px; line-height: 1.2;"
                >
                    <div
                        x-text="currentMessage"
                        class="text-xs font-bold text-center w-full"
                        style="color: #ff7f50; text-shadow: 0 1px 2px rgba(0,0,0,0.1); overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;"
                    >
                    </div>
                </div>

                {{-- Robot SVG Icon - z-index BELOW mobile bubble --}}
                <svg class="relative z-[1] w-12 h-12 transition-transform group-hover:scale-110 group-hover:rotate-6" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="white">
                    <path d="m181 301c-8.284 0-15 6.716-15 15v30c0 8.284 6.716 15 15 15s15-6.716 15-15v-30c0-8.284-6.716-15-15-15z"></path>
                    <path d="m331 361c8.284 0 15-6.716 15-15v-30c0-8.284-6.716-15-15-15s-15 6.716-15 15v30c0 8.284 6.716 15 15 15z"></path>
                    <path d="m272 106h164c8.284 0 15-6.716 15-15s-6.716-15-15-15h-164c-8.284 0-15 6.716-15 15s6.716 15 15 15z"></path>
                    <path d="m512 176v-111c0-35.841-29.159-65-65-65h-186c-35.841 0-65 29.159-65 65v116h-20c-54.827 0-100.809 38.57-112.255 90h-2.745v-92.58c17.459-6.192 30-22.865 30-42.42 0-24.813-20.187-45-45-45s-45 20.187-45 45c0 19.555 12.541 36.228 30 42.42v94.821c-17.977 5.901-31 22.833-31 42.759v60c0 24.813 20.187 45 45 45h18.527c11.069 51.929 57.291 91 112.473 91h160c55.182 0 101.404-39.071 112.473-91h18.527c24.813 0 45-20.187 45-45v-60c0-24.813-20.187-45-45-45h-18.751c-2.331-10.48-6.115-20.577-11.247-30h9.998c35.841 0 65-29.159 65-65zm-286-111c0-19.299 15.701-35 35-35h186c19.299 0 35 15.701 35 35v111c0 19.299-15.701 35-35 35h-176c-2.329 0-4.625.542-6.708 1.583l-38.292 19.146zm-180 56c8.271 0 15 6.729 15 15s-6.729 15-15 15-15-6.729-15-15 6.729-15 15-15zm-16 255v-60c0-8.271 6.729-15 15-15h16v90h-16c-8.271 0-15-6.729-15-15zm452-60v60c0 8.271-6.729 15-15 15h-16v-90h16c8.271 0 15 6.729 15 15zm-61-20v101c0 46.869-38.131 85-85 85h-160c-46.869 0-85-38.131-85-85v-101c0-46.869 38.131-85 85-85h20v45c0 11.132 11.742 18.4 21.708 13.416l56.833-28.416h126.241c13.038 15.344 20.218 34.804 20.218 55z"></path>
                    <path d="m272 166h164c8.284 0 15-6.716 15-15s-6.716-15-15-15h-164c-8.284 0-15 6.716-15 15s6.716 15 15 15z"></path>
                    <path d="m211 406c0 8.284 6.716 15 15 15h60c8.284 0 15-6.716 15-15s-6.716-15-15-15h-60c-8.284 0-15 6.716-15 15z"></path>
                </svg>
            </div>

            {{-- Active indicator with bounce --}}
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-bounce-subtle shadow-lg shadow-green-400/50"></span>
        </button>
    </div>

    {{-- Chat Window - Opens above button like iXtif --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        :style="{
            height: isMinimized ? '56px' : '480px',
            maxHeight: 'calc(100vh - 200px)'
        }"
        class="absolute bottom-24 right-0 w-96 max-w-[calc(100vw-2rem)] bg-muzibu-gray rounded-2xl shadow-2xl border border-muzibu-gray-light overflow-hidden flex flex-col"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-muzibu-coral/20 to-pink-500/20 border-b border-muzibu-gray-light px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-muzibu-coral to-pink-500 rounded-full flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">Muzibu Müzik Asistanı</h3>
                    <p class="text-muzibu-text-gray text-xs">Size yardımcı olmak için buradayım</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="toggleMinimize()"
                    class="text-muzibu-text-gray hover:text-white transition-colors"
                    aria-label="Minimize"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isMinimized ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/>
                    </svg>
                </button>
                <button
                    @click="close()"
                    class="text-muzibu-text-gray hover:text-white transition-colors"
                    aria-label="Kapat"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content (hidden when minimized) --}}
        <div x-show="!isMinimized" class="flex flex-col flex-1 min-h-0">
            {{-- Messages --}}
            <div id="ai-chat-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-4 scroll-smooth">
                {{-- Welcome message (when no messages) --}}
                <div x-show="messages.length === 0" class="text-center py-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-muzibu-coral to-pink-500 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">Merhaba! 👋</h4>
                    <p class="text-muzibu-text-gray text-sm mb-4">Size nasıl yardımcı olabilirim?</p>

                    {{-- Quick Actions --}}
                    <div x-show="quickActions.length > 0" class="grid grid-cols-2 gap-2 max-w-xs mx-auto">
                        <template x-for="action in quickActions" :key="action.label">
                            <button
                                @click="handleQuickAction(action)"
                                class="px-3 py-2 bg-muzibu-gray-light hover:bg-muzibu-coral/20 rounded-lg text-xs text-muzibu-text-gray hover:text-white transition-colors border border-transparent hover:border-muzibu-coral/30"
                            >
                                <i :class="action.icon" class="mr-1"></i>
                                <span x-text="action.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Message list --}}
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div
                            :class="msg.role === 'user'
                                ? 'bg-gradient-to-r from-muzibu-coral to-pink-500 text-white'
                                : 'bg-muzibu-gray-light text-muzibu-text-gray'"
                            class="max-w-[80%] rounded-2xl px-4 py-2 text-sm ai-message-content"
                        >
                            {{-- User messages: plain text --}}
                            <p x-show="msg.role === 'user'" x-text="msg.content" class="whitespace-pre-wrap m-0"></p>

                            {{-- AI messages: parsed markdown HTML + ACTION buttons --}}
                            <div x-show="msg.role === 'assistant'" x-html="processAIContent(msg.content)" class="m-0"></div>
                        </div>
                    </div>
                </template>

                {{-- Loading indicator --}}
                <div x-show="isLoading" class="flex justify-start">
                    <div class="bg-muzibu-gray-light rounded-2xl px-4 py-2">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-muzibu-text-gray rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-muzibu-text-gray rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-muzibu-text-gray rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="border-t border-muzibu-gray-light p-4">
                <form @submit.prevent="sendMessage()" class="flex gap-2">
                    <input
                        id="ai-chat-input"
                        type="text"
                        x-model="currentMessage"
                        placeholder="Mesajınızı yazın..."
                        class="flex-1 bg-muzibu-gray-light border border-transparent focus:border-muzibu-coral/50 rounded-full px-4 py-2 text-sm text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral/30"
                        :disabled="isLoading"
                    >
                    <button
                        type="submit"
                        :disabled="!currentMessage.trim() || isLoading"
                        class="w-10 h-10 bg-gradient-to-r from-muzibu-coral to-pink-500 rounded-full flex items-center justify-center text-white disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 transition-transform"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Footer Credit --}}
            <div class="border-t border-muzibu-gray-light px-4 py-2 bg-muzibu-gray-light/30">
                <p class="text-center text-xs text-muzibu-text-gray">
                    Bu AI
                    <a href="https://www.turkbilisim.com.tr" target="_blank" class="text-muzibu-coral hover:text-pink-400 transition-colors">
                        Türk Bilişim
                    </a>
                    tarafından Muzibu için özel olarak hazırlanmıştır.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Styles for animation --}}
<style>
    [x-cloak] { display: none !important; }

    #ai-chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    #ai-chat-messages::-webkit-scrollbar-track {
        background: #121212;
    }

    #ai-chat-messages::-webkit-scrollbar-thumb {
        background: #282828;
        border-radius: 4px;
    }

    #ai-chat-messages::-webkit-scrollbar-thumb:hover {
        background: #ff7f50;
    }

    /* 🎨 V1 Classic Pulse Animation (from iXtif) */
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

    /* Subtle bounce animation */
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }

    .animate-bounce-subtle {
        animation: bounce-subtle 1.5s ease-in-out infinite;
    }

    /* AI Message Content Styling */
    .ai-message-content p {
        margin: 0 0 0.5rem 0;
    }

    .ai-message-content p:last-child {
        margin-bottom: 0;
    }

    .ai-message-content strong {
        font-weight: 700;
        color: inherit;
    }

    .ai-message-content em {
        font-style: italic;
    }

    .ai-message-content h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 1rem 0 0.75rem 0;
        color: inherit;
        line-height: 1.2;
    }

    .ai-message-content h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0.875rem 0 0.625rem 0;
        color: inherit;
        line-height: 1.3;
    }

    .ai-message-content h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0.75rem 0 0.5rem 0;
        color: inherit;
        line-height: 1.4;
    }

    .ai-message-content h1:first-child,
    .ai-message-content h2:first-child,
    .ai-message-content h3:first-child {
        margin-top: 0;
    }

    .ai-message-content ul {
        margin: 0.5rem 0;
        padding-left: 1.5rem;
        list-style-type: disc;
    }

    .ai-message-content li {
        margin: 0.25rem 0;
    }

    .ai-message-content br {
        content: "";
        display: block;
        margin: 0.25rem 0;
    }
</style>
