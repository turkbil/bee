{{-- Muzibu AI Music Assistant Widget --}}
<div
    x-data="$store.tenant1001AI"
    x-init="init()"
    class="fixed bottom-24 right-4 z-[35]"
    x-cloak
>
    {{-- Floating Button (when closed) --}}
    <button
        x-show="!isOpen"
        @click="toggle()"
        class="ai-chat-float-button w-14 h-14 bg-gradient-to-br from-muzibu-coral via-pink-500 to-purple-500 rounded-full shadow-2xl flex items-center justify-center text-white hover:shadow-muzibu-coral/50 transition-all duration-300 group relative overflow-hidden"
        aria-label="Muzibu Müzik Asistanı"
    >
        {{-- Animated gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/20 to-transparent animate-gradient-shift"></div>

        {{-- Pulse rings --}}
        <span class="absolute inset-0 rounded-full border-2 border-muzibu-coral/30 animate-ping-slow"></span>
        <span class="absolute inset-0 rounded-full border border-pink-400/20 animate-pulse-ring"></span>

        {{-- Icon with rotation animation --}}
        <svg class="w-7 h-7 relative z-10 group-hover:rotate-12 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
        </svg>

        {{-- Active indicator with bounce --}}
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-bounce-subtle shadow-lg shadow-green-400/50"></span>
    </button>

    {{-- Chat Window --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="w-96 max-w-[calc(100vw-2rem)] bg-muzibu-gray rounded-2xl shadow-2xl border border-muzibu-gray-light overflow-hidden flex flex-col"
        :class="isMinimized ? 'h-14' : 'h-[600px] max-h-[calc(100vh-10rem)]'"
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

    /* 🎨 Modern AI Chat Button Animations */
    .ai-chat-float-button:hover {
        transform: scale(1.1) translateY(-2px);
    }

    .ai-chat-float-button:active {
        transform: scale(0.95);
    }

    /* Gradient shift animation */
    @keyframes gradient-shift {
        0%, 100% { transform: translate(-50%, -50%) rotate(0deg); opacity: 0.5; }
        50% { transform: translate(-50%, -50%) rotate(180deg); opacity: 0.8; }
    }

    .animate-gradient-shift {
        animation: gradient-shift 3s ease-in-out infinite;
    }

    /* Slow ping animation */
    @keyframes ping-slow {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.1); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }

    .animate-ping-slow {
        animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    /* Pulse ring animation */
    @keyframes pulse-ring {
        0%, 100% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.05); opacity: 0.3; }
    }

    .animate-pulse-ring {
        animation: pulse-ring 2s ease-in-out infinite;
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
