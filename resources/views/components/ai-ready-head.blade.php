{{-- 
===========================================================
🤖 AI-READY HEAD COMPONENT - Enterprise AI Integration
===========================================================
Bu component AI entegrasyonu için gerekli head etiketlerini ekler:
- AI API DNS prefetch
- Security headers for AI
- Performance optimization
- Resource hints for AI assets
===========================================================
--}}

{{-- AI Content Generation Meta Tags --}}
<meta name="ai-content-generation" content="enabled">
<meta name="ai-streaming" content="supported">
<meta name="ai-language-support" content="multi">
<meta name="ai-ready" content="true">

{{-- AI API Security & Performance --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

{{-- AI API DNS Prefetch - Performance Boost --}}
<link rel="dns-prefetch" href="//api.openai.com">
<link rel="dns-prefetch" href="//api.anthropic.com">
<link rel="dns-prefetch" href="//api.deepseek.com">

{{-- AI Assets Preload --}}
@php
$aiModuleActive = false;
try {
    $modules = app('modules')->allEnabled();
    $aiModuleActive = is_array($modules) ? in_array('AI', array_keys($modules)) : $modules->has('AI');
} catch(\Exception $e) {
    // Module service not available
}
@endphp

@if($aiModuleActive)
<link rel="preload" href="/admin-assets/css/ai-widget.css" as="style">
<link rel="preload" href="/admin-assets/js/ai-core.js" as="script">
<link rel="prefetch" href="/admin-assets/js/ai-streaming.js">
@endif

{{-- CSP for AI APIs --}}
<meta http-equiv="Content-Security-Policy" 
      content="default-src 'self';
               connect-src 'self' https://api.openai.com https://api.anthropic.com https://api.deepseek.com wss: ws:;
               script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com;
               worker-src 'self' blob:;
               style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
               font-src 'self' https://fonts.gstatic.com;
               img-src 'self' data: blob: https:;
               media-src 'self' blob: https:;">

{{-- AI Streaming Ready Script --}}
@if($aiModuleActive)
<script>
// AI Resource Manager - Preload AI assets
window.AIResourceManager = {
    initialized: false,
    
    init: function() {
        if (this.initialized) return;
        
        // Preload AI assets
        this.preloadAIAssets();
        
        // Setup streaming support
        this.setupStreaming();
        
        this.initialized = true;
    },
    
    preloadAIAssets: function() {
        // Preload critical AI CSS if not already loaded
        if (!document.querySelector('link[href*="ai-widget.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/admin-assets/css/ai-widget.css?v={{ time() }}';
            document.head.appendChild(link);
        }
        
        // Prefetch AI JavaScript for faster loading
        const scriptPrefetch = document.createElement('link');
        scriptPrefetch.rel = 'prefetch';
        scriptPrefetch.href = '/admin-assets/js/ai-core.js';
        document.head.appendChild(scriptPrefetch);
    },
    
    setupStreaming: function() {
        // Server-sent events support check
        if (typeof EventSource !== 'undefined') {
            window.AIStreamingSupported = true;
        }
        
        // WebSocket support check
        if (typeof WebSocket !== 'undefined') {
            window.AIWebSocketSupported = true;
        }
        
        // Prepare for AI streaming responses
        window.AIStreamBuffer = [];
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.AIResourceManager) {
        window.AIResourceManager.init();
    }
});
</script>
@endif

{{-- AI Error Handling & Fallbacks --}}
<script>
// AI Connection Error Handler
window.AIErrorHandler = {
    handleAPIError: function(error, provider) {
        console.warn(`AI API Error (${provider}):`, error);
        
        // Fallback strategy
        if (provider === 'openai') {
            // Try Anthropic as fallback
            return this.tryFallback('anthropic');
        } else if (provider === 'anthropic') {
            // Try DeepSeek as fallback
            return this.tryFallback('deepseek');
        }
        
        // Show user-friendly error
        this.showUserError();
    },
    
    tryFallback: function(provider) {
        // Implement fallback logic
        console.info(`Trying fallback AI provider: ${provider}`);
    },
    
    showUserError: function() {
        // Show graceful error message to user
        if (typeof window.showToast === 'function') {
            window.showToast('AI sistemi geçici olarak kullanılamıyor. Lütfen daha sonra tekrar deneyin.', 'warning');
        }
    }
};
</script>

{{-- Performance Monitoring for AI --}}
@if(app()->environment('local', 'staging'))
<script>
// AI Performance Monitoring (Development only)
window.AIPerformanceMonitor = {
    startTime: null,
    
    startAIRequest: function() {
        this.startTime = performance.now();
    },
    
    endAIRequest: function(provider) {
        if (this.startTime) {
            const duration = performance.now() - this.startTime;
            console.info(`AI Request Duration (${provider}): ${duration.toFixed(2)}ms`);
        }
    }
};
</script>
@endif
