{{--
    Chat Widget Header Component
    
    Reusable header component for chat widget
    Supports different themes, sizes, and configurations
    
    Props:
    - $title: Widget title
    - $subtitle: Optional subtitle
    - $theme: Theme configuration
    - $size: Size configuration
    - $showMinimize: Whether to show minimize button
    - $showClose: Whether to show close button
    - $showStatus: Whether to show online status
    - $avatar: Optional avatar URL
--}}

@props([
    'title' => 'AI Asistan',
    'subtitle' => null,
    'theme' => 'modern',
    'size' => 'standard',
    'showMinimize' => true,
    'showClose' => true,
    'showStatus' => true,
    'avatar' => null,
    'config' => []
])

@php
$themeClasses = [
    'modern' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
    'minimal' => 'bg-white text-gray-800 border-b border-gray-100',
    'colorful' => 'bg-gradient-to-r from-purple-600 to-pink-600 text-white',
    'dark' => 'bg-gray-800 text-gray-100 border-b border-gray-700',
    'glassmorphism' => 'bg-white/20 backdrop-blur-md text-gray-800 border-b border-white/30',
    'neumorphism' => 'bg-gray-100 text-gray-800 shadow-neuro-sm'
];

$sizeClasses = [
    'compact' => 'p-2 text-xs h-10',
    'standard' => 'p-3 text-sm h-12',
    'large' => 'p-4 text-base h-14',
    'fullscreen' => 'p-6 text-lg h-16'
];

$headerClass = ($themeClasses[$theme] ?? $themeClasses['modern']) . ' ' . 
               ($sizeClasses[$size] ?? $sizeClasses['standard']);
@endphp

<div class="widget-header flex items-center justify-between {{ $headerClass }}" 
     role="banner"
     aria-label="{{ $title }} Widget Başlığı">
    
    {{-- Left Side: Avatar, Title, Status --}}
    <div class="header-left flex items-center space-x-3 min-w-0 flex-1">
        
        {{-- Avatar --}}
        @if($avatar || $showStatus)
        <div class="avatar-container relative flex-shrink-0">
            @if($avatar)
                <img src="{{ $avatar }}" 
                     alt="{{ $title }} Avatar" 
                     class="w-8 h-8 rounded-full object-cover border-2 border-white/20">
            @else
                <div class="w-8 h-8 rounded-full {{ $theme === 'dark' ? 'bg-gray-600' : 'bg-white/20' }} flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $theme === 'dark' ? 'text-gray-300' : 'text-white' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            @endif
            
            {{-- Online Status Indicator --}}
            @if($showStatus)
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full animate-pulse" 
                     aria-label="Çevrimiçi"
                     title="AI Asistan Çevrimiçi"></div>
            @endif
        </div>
        @endif
        
        {{-- Title and Subtitle --}}
        <div class="title-container min-w-0 flex-1">
            <h3 class="font-semibold truncate mb-0" id="widget-title">
                {{ $title }}
            </h3>
            @if($subtitle)
                <p class="text-xs opacity-75 truncate mt-0.5" id="widget-subtitle">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>
    
    {{-- Right Side: Action Buttons --}}
    <div class="header-right flex items-center space-x-1 flex-shrink-0">
        
        {{-- Minimize Button --}}
        @if($showMinimize)
        <button type="button"
                class="minimize-btn p-1.5 rounded-lg opacity-70 hover:opacity-100 hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/20"
                aria-label="Widget'ı Küçült"
                title="Widget'ı Küçült">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
        </button>
        @endif
        
        {{-- Close Button --}}
        @if($showClose)
        <button type="button"
                class="close-btn p-1.5 rounded-lg opacity-70 hover:opacity-100 hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/20"
                aria-label="Widget'ı Kapat"
                title="Widget'ı Kapat">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        @endif
        
        {{-- Settings Button (Optional) --}}
        @if($config['show_settings'] ?? false)
        <button type="button"
                class="settings-btn p-1.5 rounded-lg opacity-70 hover:opacity-100 hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/20"
                aria-label="Widget Ayarları"
                title="Widget Ayarları">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
        @endif
    </div>
</div>

{{-- Theme-specific styles --}}
<style>
/* Header animations */
.widget-header {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button hover effects by theme */
.widget-header button {
    position: relative;
    overflow: hidden;
}

.widget-header button:active {
    transform: scale(0.95);
}

/* Avatar hover effect */
.avatar-container img:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

/* Online status pulse animation */
@keyframes pulse-status {
    0%, 100% { 
        opacity: 1;
        transform: scale(1);
    }
    50% { 
        opacity: 0.5;
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse-status 2s infinite;
}

/* Title truncation with tooltip */
.title-container h3:hover,
.title-container p:hover {
    position: relative;
}

.title-container h3:hover::after,
.title-container p:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 0;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    opacity: 0;
    animation: fadeInTooltip 0.2s ease-in-out forwards;
}

@keyframes fadeInTooltip {
    to {
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .header-left .title-container h3 {
        font-size: 0.875rem;
    }
    
    .header-left .title-container p {
        font-size: 0.75rem;
    }
    
    .header-right button {
        padding: 0.5rem;
    }
    
    .avatar-container img,
    .avatar-container > div {
        width: 1.75rem;
        height: 1.75rem;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .widget-header {
        border: 1px solid currentColor;
    }
    
    .widget-header button {
        border: 1px solid currentColor;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .widget-header,
    .widget-header button,
    .avatar-container img,
    .animate-pulse {
        animation: none;
        transition: none;
    }
}

/* RTL Support */
.rtl .header-left {
    flex-direction: row-reverse;
    text-align: right;
}

.rtl .header-right {
    flex-direction: row-reverse;
}

.rtl .header-left .space-x-3 > * + * {
    margin-left: 0;
    margin-right: 0.75rem;
}

.rtl .header-right .space-x-1 > * + * {
    margin-left: 0;
    margin-right: 0.25rem;
}
</style>