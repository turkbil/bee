{{--
    Chat Widget Message Component
    
    Reusable message component for chat widget
    Supports user and bot messages with different themes
    
    Props:
    - $type: 'user' or 'bot'
    - $content: Message content
    - $timestamp: Message timestamp
    - $avatar: Optional avatar URL
    - $theme: Theme configuration
    - $size: Size configuration
    - $showTimestamp: Whether to show timestamp
    - $showAvatar: Whether to show avatar
--}}

@props([
    'type' => 'bot',
    'content' => '',
    'timestamp' => null,
    'avatar' => null,
    'theme' => 'modern',
    'size' => 'standard',
    'showTimestamp' => true,
    'showAvatar' => true,
    'isTyping' => false,
    'metadata' => []
])

@php
$isUser = $type === 'user';
$isBot = $type === 'bot';
$messageId = uniqid('msg_');

// Theme-specific classes
$themeClasses = [
    'modern' => [
        'user' => 'bg-blue-500 text-white',
        'bot' => 'bg-gray-100 text-gray-900',
        'avatar_bg' => 'bg-blue-100'
    ],
    'minimal' => [
        'user' => 'bg-gray-800 text-white',
        'bot' => 'bg-gray-50 text-gray-900',
        'avatar_bg' => 'bg-gray-200'
    ],
    'colorful' => [
        'user' => 'bg-gradient-to-r from-purple-500 to-pink-500 text-white',
        'bot' => 'bg-gradient-to-r from-blue-50 to-purple-50 text-gray-900',
        'avatar_bg' => 'bg-purple-100'
    ],
    'dark' => [
        'user' => 'bg-blue-600 text-white',
        'bot' => 'bg-gray-700 text-gray-100',
        'avatar_bg' => 'bg-gray-600'
    ],
    'glassmorphism' => [
        'user' => 'bg-white/20 backdrop-blur-sm text-gray-800 border border-white/30',
        'bot' => 'bg-black/10 backdrop-blur-sm text-gray-800 border border-black/20',
        'avatar_bg' => 'bg-white/30'
    ],
    'neumorphism' => [
        'user' => 'bg-gray-100 shadow-neuro text-gray-800',
        'bot' => 'bg-white shadow-neuro-inset text-gray-900',
        'avatar_bg' => 'bg-gray-200'
    ]
];

// Size-specific classes  
$sizeClasses = [
    'compact' => 'text-xs p-2 max-w-[200px]',
    'standard' => 'text-sm p-3 max-w-[280px]',
    'large' => 'text-base p-4 max-w-[320px]',
    'fullscreen' => 'text-lg p-5 max-w-[400px]'
];

$currentTheme = $themeClasses[$theme] ?? $themeClasses['modern'];
$messageClass = $currentTheme[$type] ?? $currentTheme['bot'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['standard'];
@endphp

<div class="message-wrapper mb-4 {{ $isUser ? 'flex justify-end' : 'flex justify-start' }}" 
     data-message-id="{{ $messageId }}"
     data-message-type="{{ $type }}"
     role="log"
     aria-live="polite">
    
    {{-- Bot Avatar (Left side) --}}
    @if($isBot && $showAvatar)
    <div class="message-avatar flex-shrink-0 mr-3">
        @if($avatar)
            <img src="{{ $avatar }}" 
                 alt="AI Asistan" 
                 class="w-8 h-8 rounded-full object-cover">
        @else
            <div class="w-8 h-8 rounded-full {{ $currentTheme['avatar_bg'] }} flex items-center justify-center">
                @if($isTyping)
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                @else
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </div>
        @endif
    </div>
    @endif
    
    {{-- Message Content --}}
    <div class="message-content-wrapper {{ $isUser ? 'items-end' : 'items-start' }} flex flex-col">
        
        {{-- Message Bubble --}}
        <div class="message-bubble {{ $messageClass }} {{ $sizeClass }} rounded-2xl {{ $isUser ? 'rounded-br-md' : 'rounded-bl-md' }} 
                    shadow-sm hover:shadow-md transition-shadow duration-200 
                    @if($isTyping) animate-pulse @endif"
             role="article"
             aria-label="{{ $isUser ? 'Gönderdiğiniz mesaj' : 'AI asistan cevabı' }}">
            
            {{-- Typing Indicator --}}
            @if($isTyping)
                <div class="flex items-center space-x-1">
                    <span class="text-sm opacity-75">Yazıyor</span>
                    <div class="typing-dots">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
            @else
                {{-- Message Text --}}
                <div class="message-text break-words">
                    {!! nl2br(e($content)) !!}
                </div>
                
                {{-- Metadata (for bot messages) --}}
                @if($isBot && !empty($metadata))
                    <div class="message-metadata mt-2 pt-2 border-t border-current/10">
                        @if(isset($metadata['confidence']) && $metadata['confidence'])
                            <div class="confidence-badge inline-flex items-center text-xs opacity-75">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                %{{ round($metadata['confidence'] * 100) }} güven
                            </div>
                        @endif
                        
                        @if(isset($metadata['sources']) && !empty($metadata['sources']))
                            <div class="sources mt-1">
                                <span class="text-xs opacity-75">Kaynak: {{ implode(', ', $metadata['sources']) }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
        
        {{-- Message Actions (for bot messages) --}}
        @if($isBot && !$isTyping)
        <div class="message-actions flex items-center space-x-2 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
            {{-- Copy Button --}}
            <button type="button" 
                    class="copy-btn p-1 rounded text-xs opacity-60 hover:opacity-100 transition-opacity"
                    onclick="copyMessage('{{ $messageId }}')"
                    title="Mesajı Kopyala"
                    aria-label="Mesajı Kopyala">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </button>
            
            {{-- Regenerate Button --}}
            <button type="button"
                    class="regenerate-btn p-1 rounded text-xs opacity-60 hover:opacity-100 transition-opacity"
                    onclick="regenerateMessage('{{ $messageId }}')"
                    title="Tekrar Oluştur"
                    aria-label="Cevabı Tekrar Oluştur">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
            
            {{-- Feedback Buttons --}}
            <div class="feedback-buttons flex space-x-1">
                <button type="button"
                        class="feedback-positive p-1 rounded text-xs opacity-60 hover:opacity-100 hover:text-green-600 transition-all"
                        onclick="provideFeedback('{{ $messageId }}', 'positive')"
                        title="Bu cevap yararlıydı"
                        aria-label="Pozitif Geri Bildirim">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                </button>
                
                <button type="button"
                        class="feedback-negative p-1 rounded text-xs opacity-60 hover:opacity-100 hover:text-red-600 transition-all"
                        onclick="provideFeedback('{{ $messageId }}', 'negative')"
                        title="Bu cevap yararlı değildi"
                        aria-label="Negatif Geri Bildirim">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2M17 4H19a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif
        
        {{-- Timestamp --}}
        @if($showTimestamp && $timestamp && !$isTyping)
        <div class="message-timestamp text-xs opacity-50 mt-1 {{ $isUser ? 'text-right' : 'text-left' }}">
            <time datetime="{{ $timestamp }}" title="{{ $timestamp }}">
                {{ \Carbon\Carbon::parse($timestamp)->diffForHumans() }}
            </time>
        </div>
        @endif
    </div>
    
    {{-- User Avatar (Right side) --}}
    @if($isUser && $showAvatar)
    <div class="message-avatar flex-shrink-0 ml-3">
        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>
    @endif
</div>

<style>
/* Message hover effects */
.message-wrapper {
    group: message;
}

.message-wrapper:hover .message-actions {
    opacity: 1;
}

/* Typing indicator animation */
.typing-indicator {
    display: flex;
    align-items: center;
    space-x: 1px;
}

.typing-dot {
    width: 4px;
    height: 4px;
    background-color: currentColor;
    border-radius: 50%;
    opacity: 0.4;
    animation: typing-pulse 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing-pulse {
    0%, 80%, 100% {
        opacity: 0.4;
        transform: scale(1);
    }
    40% {
        opacity: 1;
        transform: scale(1.2);
    }
}

/* Typing dots in message */
.typing-dots {
    display: flex;
    align-items: center;
    gap: 2px;
}

.typing-dots .dot {
    width: 3px;
    height: 3px;
    background-color: currentColor;
    border-radius: 50%;
    animation: bounce-typing 1.4s infinite ease-in-out;
}

.typing-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dots .dot:nth-child(2) { animation-delay: -0.16s; }
.typing-dots .dot:nth-child(3) { animation-delay: 0; }

@keyframes bounce-typing {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Message bubble animations */
.message-bubble {
    transform-origin: {{ $isUser ? 'bottom-right' : 'bottom-left' }};
    animation: messageSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes messageSlideIn {
    0% {
        opacity: 0;
        transform: translateY(10px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Feedback button states */
.feedback-positive.active {
    color: #10b981;
    opacity: 1;
}

.feedback-negative.active {
    color: #ef4444;
    opacity: 1;
}

/* Copy notification */
.copy-success {
    position: relative;
}

.copy-success::after {
    content: 'Kopyalandı!';
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    white-space: nowrap;
    animation: fadeInOut 2s ease-in-out;
}

@keyframes fadeInOut {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .message-bubble {
        max-width: calc(100vw - 80px);
    }
    
    .message-actions {
        margin-top: 0.25rem;
    }
    
    .message-actions button {
        padding: 0.25rem;
    }
}

/* RTL Support */
.rtl .message-wrapper.flex {
    flex-direction: row-reverse;
}

.rtl .message-wrapper .message-avatar {
    margin-left: 0;
    margin-right: 0.75rem;
}

.rtl .message-bubble {
    border-radius: 1rem;
}

.rtl .message-bubble.rounded-br-md {
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 1rem;
}

.rtl .message-bubble.rounded-bl-md {
    border-bottom-right-radius: 0.375rem;
    border-bottom-left-radius: 1rem;
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .message-bubble {
        border: 1px solid currentColor;
    }
    
    .message-actions button {
        border: 1px solid currentColor;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .message-bubble,
    .typing-indicator,
    .typing-dots .dot,
    .typing-dot {
        animation: none;
    }
    
    .message-bubble {
        transform: none;
    }
}
</style>

<script>
// Message interaction functions
function copyMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"] .message-text`);
    if (messageElement) {
        const textContent = messageElement.textContent || messageElement.innerText;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(textContent).then(() => {
                showCopySuccess(messageId);
            }).catch(err => {
                console.error('Copy failed:', err);
                fallbackCopyText(textContent);
            });
        } else {
            fallbackCopyText(textContent);
        }
    }
}

function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    showCopySuccess();
}

function showCopySuccess(messageId = null) {
    const button = document.querySelector(`[data-message-id="${messageId}"] .copy-btn`);
    if (button) {
        button.classList.add('copy-success');
        setTimeout(() => {
            button.classList.remove('copy-success');
        }, 2000);
    }
}

function regenerateMessage(messageId) {
    // Implementation for message regeneration
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageElement) {
        // Add loading state
        const messageText = messageElement.querySelector('.message-text');
        const originalContent = messageText.innerHTML;
        
        messageText.innerHTML = '<div class="typing-dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';
        
        // Call regeneration API
        fetch('/admin/ai/chat/regenerate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ message_id: messageId })
        }).then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Regeneration failed');
        }).then(data => {
            messageText.innerHTML = data.content || originalContent;
        }).catch(error => {
            console.error('Regeneration error:', error);
            messageText.innerHTML = originalContent;
        });
    }
}

function provideFeedback(messageId, type) {
    const button = document.querySelector(`[data-message-id="${messageId}"] .feedback-${type}`);
    if (button) {
        // Remove active state from other feedback buttons
        const allFeedbackButtons = document.querySelectorAll(`[data-message-id="${messageId}"] .feedback-positive, [data-message-id="${messageId}"] .feedback-negative`);
        allFeedbackButtons.forEach(btn => btn.classList.remove('active'));
        
        // Add active state to clicked button
        button.classList.add('active');
        
        // Send feedback to server
        fetch('/admin/ai/chat/feedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                message_id: messageId,
                feedback_type: type
            })
        }).then(response => {
            if (!response.ok) {
                console.error('Feedback submission failed');
                button.classList.remove('active');
            }
        }).catch(error => {
            console.error('Feedback error:', error);
            button.classList.remove('active');
        });
    }
}
</script>