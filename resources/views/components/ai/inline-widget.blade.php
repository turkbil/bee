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

{{-- Markdown renderer function (outside x-data to avoid escaping issues) --}}
<script>
window.aiChatRenderMarkdown = function(text) {
    if (!text) return '';

    let html = text;

    // Convert [text](url) to <a> links - SITE RENKLERİNE UYGUN (turuncu/yeşil) + DARK MODE
    html = html.replace(/\[([^\]]+)\]\(([^\)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold border-b-2 border-orange-400 dark:border-orange-500 hover:border-orange-600 dark:hover:border-orange-400 transition-colors">$1</a>');

    // Convert **bold** to <strong>
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold">$1</strong>');

    // Convert *italic* to <em>
    html = html.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');

    // Convert ### heading to h3
    html = html.replace(/^### (.*?)$/gm, '<h3 class="text-lg font-bold mt-4 mb-2">$1</h3>');

    // Convert ## heading to h2
    html = html.replace(/^## (.*?)$/gm, '<h2 class="text-xl font-bold mt-4 mb-2">$1</h2>');

    // Convert # heading to h1
    html = html.replace(/^# (.*?)$/gm, '<h1 class="text-2xl font-bold mt-4 mb-3">$1</h1>');

    // Split into blocks first (separated by double newline or list patterns)
    const blocks = [];
    const lines = html.split('\n');
    let currentBlock = [];
    let blockType = null;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].trim();

        // Empty line - finish current block
        if (!line) {
            if (currentBlock.length > 0) {
                blocks.push({ type: blockType, lines: currentBlock });
                currentBlock = [];
                blockType = null;
            }
            continue;
        }

        // Determine block type
        if (line.startsWith('- ')) {
            if (blockType !== 'list') {
                // Finish previous block
                if (currentBlock.length > 0) {
                    blocks.push({ type: blockType, lines: currentBlock });
                    currentBlock = [];
                }
                blockType = 'list';
            }
            currentBlock.push(line);
        } else if (line.startsWith('<h')) {
            // Finish previous block
            if (currentBlock.length > 0) {
                blocks.push({ type: blockType, lines: currentBlock });
                currentBlock = [];
            }
            blocks.push({ type: 'heading', lines: [line] });
            blockType = null;
        } else {
            if (blockType !== 'text') {
                // Finish previous block
                if (currentBlock.length > 0) {
                    blocks.push({ type: blockType, lines: currentBlock });
                    currentBlock = [];
                }
                blockType = 'text';
            }
            currentBlock.push(line);
        }
    }

    // Add final block
    if (currentBlock.length > 0) {
        blocks.push({ type: blockType, lines: currentBlock });
    }

    // Render blocks
    const result = blocks.map(block => {
        if (block.type === 'list') {
            const items = block.lines.map(line => '<li>' + line.substring(2) + '</li>').join('\n');
            return '<ul class="list-disc ml-5 my-3 space-y-2">\n' + items + '\n</ul>';
        } else if (block.type === 'heading') {
            return block.lines.join('\n');
        } else if (block.type === 'text') {
            // Each line becomes a paragraph for better spacing
            return block.lines.map(line => '<p class="mb-2">' + line + '</p>').join('\n');
        }
        return '';
    });

    return result.join('\n');
};
</script>

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
    <div class="{{ $selectedTheme['header'] }} dark:from-blue-700 dark:to-blue-800 text-white px-6 py-4">
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
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
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

            {{-- Footer Info - Auto-hide after 10 seconds --}}
            <div
                x-data="{ visible: true }"
                x-init="setTimeout(() => visible = false, 10000)"
                x-show="visible"
                x-transition:leave="transition ease-in duration-1000"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mt-2"
            >
                <p class="text-[8px] text-gray-400 dark:text-gray-500 leading-tight opacity-60">
                    Bu yapay zeka destekli sohbet asistanı, iXtif yazılım mühendisleri tarafından iXtif için özel olarak hazırlanmıştır.
                    Geliştirme süreci devam etmektedir, zaman zaman hatalar görülebilir.
                    Ürünler hakkında daha detaylı bilgi veya destek için
                    <a href="/sayfa/iletisim" target="_blank" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 underline">
                        iletişim sayfamızdan
                    </a>
                    bize ulaşabilirsiniz.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

/* USER MESSAGES - Always white text (both light and dark mode) */
.ai-message-content[data-role="user"],
.ai-message-content[data-role="user"] *,
.ai-message-content[data-role="user"] p,
.ai-message-content[data-role="user"] span,
.ai-message-content[data-role="user"] strong,
.ai-message-content[data-role="user"] em,
.ai-message-content[data-role="user"] li,
.ai-message-content[data-role="user"] ul,
.ai-message-content[data-role="user"] ol,
.ai-message-content[data-role="user"] h1,
.ai-message-content[data-role="user"] h2,
.ai-message-content[data-role="user"] h3 {
    color: white !important;
}

/* DARK MODE TEXT FIX - Assistant/System messages white in dark mode */
.dark .ai-message-content[data-role="assistant"],
.dark .ai-message-content[data-role="assistant"] *,
.dark .ai-message-content[data-role="system"],
.dark .ai-message-content[data-role="system"] * {
    color: white !important;
}

/* Links - Special colors */
.ai-message-content[data-role="user"] a {
    color: rgba(255, 255, 255, 0.9) !important;
    text-decoration: underline;
}

.dark .ai-message-content a {
    color: #fb923c !important; /* orange-400 */
}
</style>

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
