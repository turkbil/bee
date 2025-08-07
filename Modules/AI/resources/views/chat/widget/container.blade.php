{{-- 
    Modular Chat Widget V2 - Ana Container
    
    Bu component chat widget'ının ana container'ı olarak çalışır.
    Multi-theme, multi-placement ve responsive tasarım destekler.
    
    Props:
    - $components: Widget bileşenleri (header, body, footer, overlay, trigger)
    - $config: Widget konfigürasyonu
    - $css: Theme-specific CSS
    - $js: Widget JavaScript kodu
    
    Features:
    - 6 tema desteği (modern, minimal, colorful, dark, glassmorphism, neumorphism)
    - 4 boyut seçeneği (compact, standard, large, fullscreen)
    - 9 placement lokasyonu
    - Responsive design ve mobile optimization
    - Accessibility features (WCAG 2.1 AA)
    - Performance optimization ile lazy loading
--}}

<div class="ai-widget-container {{ $config['theme'] }}-theme {{ $config['size'] }}-size" 
     data-widget-id="{{ $config['widget_id'] ?? 'default' }}"
     data-placement="{{ $config['placement'] ?? 'bottom-right' }}"
     data-theme="{{ $config['theme'] ?? 'modern' }}"
     data-size="{{ $config['size'] ?? 'standard' }}"
     data-ai-enabled="{{ $config['ai_enabled'] ? 'true' : 'false' }}"
     role="dialog"
     aria-labelledby="widget-title"
     aria-describedby="widget-description"
     @if($config['preview_mode'] ?? false) data-preview="true" @endif>

    {{-- Widget Trigger Button --}}
    {!! $components['trigger'] !!}

    {{-- Widget Overlay (for modal-style widgets) --}}
    {!! $components['overlay'] !!}

    {{-- Main Widget Panel --}}
    <div class="ai-chat-widget 
                {{ $config['theme'] }}-widget
                {{ $config['size'] }}-widget
                {{ $config['placement'] }}-placement
                @if($config['animations']['enabled'] ?? true) animated @endif
                @if(in_array($config['placement'], ['center', 'fullscreen'])) modal-style @endif
                @if($config['rtl'] ?? false) rtl @endif"
         style="display: none;"
         data-state="closed">

        {{-- Accessibility Announcements --}}
        <div id="widget-announcements" class="sr-only" aria-live="polite" aria-atomic="true"></div>
        
        {{-- Widget Header --}}
        <div class="widget-header" id="widget-title">
            {!! $components['header'] !!}
        </div>

        {{-- Widget Body --}}
        <div class="widget-body" id="widget-description">
            {!! $components['body'] !!}
        </div>

        {{-- Widget Footer --}}
        @if(!empty($components['footer']))
        <div class="widget-footer">
            {!! $components['footer'] !!}
        </div>
        @endif

        {{-- Loading States --}}
        <div class="widget-loading" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
            </div>
            <p class="loading-text">{{ __('ai::admin.widget_loading') ?? 'Yükleniyor...' }}</p>
        </div>

        {{-- Error States --}}
        <div class="widget-error" style="display: none;">
            <div class="error-icon">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="error-message">{{ __('ai::admin.widget_error') ?? 'Bir hata oluştu. Lütfen tekrar deneyin.' }}</p>
            <button class="retry-button btn btn-sm btn-primary">{{ __('ai::admin.retry') ?? 'Tekrar Dene' }}</button>
        </div>
    </div>
</div>

{{-- Widget Styles --}}
<style>
{!! $css !!}

/* Base Widget Styles */
.ai-widget-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --widget-z-index: {{ $config['z_index'] ?? 1000 }};
}

.ai-chat-widget {
    position: relative;
    z-index: var(--widget-z-index);
    border-radius: {{ $config['border_radius'] ?? '12px' }};
    box-shadow: {{ $config['shadow'] ?? '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)' }};
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive Behavior */
@media (max-width: 640px) {
    .ai-chat-widget:not(.inline-placement) {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        top: auto;
        width: 100% !important;
        height: 70vh !important;
        max-height: 500px;
        border-radius: 16px 16px 0 0;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .ai-chat-widget[data-state="open"]:not(.inline-placement) {
        transform: translateY(0);
    }
    
    .widget-trigger {
        bottom: 16px !important;
        right: 16px !important;
        width: 56px !important;
        height: 56px !important;
    }
}

/* Loading States */
.widget-loading {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(4px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.spinner-ring {
    width: 32px;
    height: 32px;
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-top: 3px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text {
    margin-top: 12px;
    font-size: 14px;
    opacity: 0.7;
}

/* Error States */
.widget-error {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 100;
    text-align: center;
    padding: 20px;
}

.error-icon {
    color: #ef4444;
    margin-bottom: 12px;
}

.error-message {
    margin-bottom: 16px;
    font-size: 14px;
    color: #374151;
}

/* RTL Support */
.rtl {
    direction: rtl;
    text-align: right;
}

.rtl .flex {
    flex-direction: row-reverse;
}

/* Accessibility */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Animation Keyframes */
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .ai-chat-widget {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .ai-chat-widget,
    .widget-trigger,
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Print Styles */
@media print {
    .ai-widget-container {
        display: none !important;
    }
}
</style>

{{-- Widget JavaScript --}}
<script>
// Widget Configuration
{!! $js !!}

// Widget Core Functionality
class AIWidgetCore {
    constructor(container) {
        this.container = container;
        this.widget = container.querySelector('.ai-chat-widget');
        this.trigger = container.querySelector('.widget-trigger');
        this.overlay = container.querySelector('.widget-overlay');
        this.minimizeBtn = container.querySelector('.minimize-btn');
        this.messagesContainer = container.querySelector('.messages-container');
        this.messageInput = container.querySelector('input[type="text"]');
        this.sendBtn = container.querySelector('.input-area button');
        this.loadingEl = container.querySelector('.widget-loading');
        this.errorEl = container.querySelector('.widget-error');
        
        this.isOpen = false;
        this.isLoading = false;
        this.config = window.AIWidgetConfig || {};
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.handleAutoOpen();
        this.setupAccessibility();
        
        // Performance monitoring
        if (this.config.performance?.monitor) {
            this.setupPerformanceMonitoring();
        }
    }
    
    bindEvents() {
        // Trigger button
        this.trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });
        
        // Minimize button
        this.minimizeBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            this.close();
        });
        
        // Overlay close
        this.overlay?.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        
        // Send message
        this.sendBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        // Enter key to send
        this.messageInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Auto-minimize on blur
        if (this.config.minimize_on_blur) {
            document.addEventListener('click', (e) => {
                if (!this.container.contains(e.target) && this.isOpen) {
                    this.minimize();
                }
            });
        }
        
        // Retry button
        const retryBtn = this.errorEl?.querySelector('.retry-button');
        retryBtn?.addEventListener('click', () => {
            this.hideError();
            this.sendMessage();
        });
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        if (this.isOpen) return;
        
        this.isOpen = true;
        this.widget.style.display = 'flex';
        this.widget.setAttribute('data-state', 'open');
        this.overlay?.style.setProperty('display', 'block');
        
        // Accessibility
        this.widget.setAttribute('aria-hidden', 'false');
        this.trigger?.setAttribute('aria-expanded', 'true');
        
        // Animation
        setTimeout(() => {
            this.overlay?.style.setProperty('opacity', '1');
            this.overlay?.style.setProperty('pointer-events', 'auto');
        }, 10);
        
        // Focus management
        const firstFocusable = this.widget.querySelector('input, button, [tabindex]:not([tabindex="-1"])');
        firstFocusable?.focus();
        
        // Announce to screen readers
        this.announce('Widget açıldı');
        
        // Analytics
        this.trackEvent('widget_opened');
    }
    
    close() {
        if (!this.isOpen) return;
        
        this.isOpen = false;
        this.widget.setAttribute('data-state', 'closed');
        
        // Accessibility
        this.widget.setAttribute('aria-hidden', 'true');
        this.trigger?.setAttribute('aria-expanded', 'false');
        
        // Animation
        this.overlay?.style.setProperty('opacity', '0');
        this.overlay?.style.setProperty('pointer-events', 'none');
        
        setTimeout(() => {
            this.widget.style.display = 'none';
            this.overlay?.style.setProperty('display', 'none');
        }, 300);
        
        // Focus management
        this.trigger?.focus();
        
        // Announce to screen readers
        this.announce('Widget kapatıldı');
        
        // Analytics
        this.trackEvent('widget_closed');
    }
    
    minimize() {
        // Implementation for minimize functionality
        this.close();
    }
    
    async sendMessage() {
        const message = this.messageInput?.value?.trim();
        if (!message || this.isLoading) return;
        
        this.showLoading();
        
        try {
            // Add user message to UI
            this.addMessage('user', message);
            this.messageInput.value = '';
            
            // Send to AI API (if enabled)
            if (this.config.ai_enabled) {
                const response = await this.callAIAPI(message);
                this.addMessage('bot', response.content);
            } else {
                // Mock response for preview mode
                this.addMessage('bot', 'Bu bir önizleme mesajıdır. AI entegrasyonu aktif değil.');
            }
            
            this.trackEvent('message_sent');
            
        } catch (error) {
            console.error('Message send failed:', error);
            this.showError('Mesaj gönderilemedi. Lütfen tekrar deneyin.');
            this.trackEvent('message_error', { error: error.message });
            
        } finally {
            this.hideLoading();
        }
    }
    
    async callAIAPI(message) {
        const response = await fetch('/admin/ai/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                message: message,
                widget_id: this.config.widget_id,
                context: this.gatherContext()
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    }
    
    gatherContext() {
        return {
            page_url: window.location.href,
            page_title: document.title,
            user_agent: navigator.userAgent,
            timestamp: new Date().toISOString()
        };
    }
    
    addMessage(type, content) {
        if (!this.messagesContainer) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `message message-${type} animate-fade-in-up`;
        messageEl.innerHTML = `
            <div class="message-content">${this.escapeHtml(content)}</div>
            <div class="message-time">${new Date().toLocaleTimeString()}</div>
        `;
        
        this.messagesContainer.appendChild(messageEl);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        
        // Accessibility announcement
        this.announce(`${type === 'user' ? 'Gönderilen' : 'Alınan'} mesaj: ${content}`);
    }
    
    showLoading() {
        this.isLoading = true;
        this.loadingEl?.style.setProperty('display', 'flex');
        this.sendBtn?.setAttribute('disabled', 'true');
    }
    
    hideLoading() {
        this.isLoading = false;
        this.loadingEl?.style.setProperty('display', 'none');
        this.sendBtn?.removeAttribute('disabled');
    }
    
    showError(message) {
        this.errorEl?.style.setProperty('display', 'flex');
        const errorMessage = this.errorEl?.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
    }
    
    hideError() {
        this.errorEl?.style.setProperty('display', 'none');
    }
    
    handleAutoOpen() {
        if (this.config.auto_open) {
            const delay = this.config.auto_open_delay || 3000;
            setTimeout(() => {
                if (!this.isOpen) {
                    this.open();
                }
            }, delay);
        }
    }
    
    setupAccessibility() {
        // ARIA labels and descriptions
        this.widget?.setAttribute('aria-label', 'AI Asistan Sohbet Widget\'ı');
        this.trigger?.setAttribute('aria-label', 'AI Asistan\'ı Aç');
        this.messageInput?.setAttribute('aria-label', 'Mesajınızı yazın');
        this.sendBtn?.setAttribute('aria-label', 'Mesajı Gönder');
        
        // Tab management
        this.widget?.setAttribute('tabindex', '-1');
    }
    
    setupPerformanceMonitoring() {
        // Performance observer for monitoring
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (entry.name.includes('ai-widget')) {
                        console.log('Widget Performance:', entry);
                    }
                });
            });
            observer.observe({ entryTypes: ['measure', 'navigation'] });
        }
    }
    
    trackEvent(eventName, data = {}) {
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                event_category: 'ai_widget',
                widget_id: this.config.widget_id,
                ...data
            });
        }
        
        // Custom analytics
        if (window.widgetAnalytics) {
            window.widgetAnalytics.track(eventName, data);
        }
    }
    
    announce(message) {
        const announcer = document.getElementById('widget-announcements');
        if (announcer) {
            announcer.textContent = message;
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize widget when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.ai-widget-container');
    containers.forEach(container => {
        new AIWidgetCore(container);
    });
});
</script>