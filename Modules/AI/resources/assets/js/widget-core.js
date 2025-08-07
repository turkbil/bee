/**
 * AI Chat Widget Core - Production Version
 * Modular, performant, accessible chat widget system
 * Version: 2.0.0
 */

class AIWidgetCore {
    constructor(container, config = {}) {
        this.container = container;
        this.config = {
            widget_id: 'default',
            theme: 'modern',
            size: 'standard',
            placement: 'bottom-right',
            ai_enabled: true,
            auto_open: false,
            auto_open_delay: 3000,
            minimize_on_blur: true,
            animations: { enabled: true },
            performance: { monitor: false },
            api_endpoint: '/api/ai/chat',
            max_message_length: 1000,
            typing_indicator: true,
            sound_notifications: false,
            reconnect_attempts: 3,
            ...config
        };
        
        this.state = {
            isOpen: false,
            isLoading: false,
            isTyping: false,
            isConnected: false,
            messages: [],
            conversationId: null,
            reconnectCount: 0
        };
        
        this.elements = {};
        this.listeners = new Map();
        this.messageQueue = [];
        this.reconnectTimer = null;
        
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.setupAccessibility();
        this.setupWebSocket();
        this.restoreState();
        
        if (this.config.auto_open) {
            setTimeout(() => this.open(), this.config.auto_open_delay);
        }
        
        if (this.config.performance.monitor) {
            this.setupPerformanceMonitoring();
        }
        
        this.announce('AI Asistan hazır');
    }
    
    cacheElements() {
        this.elements = {
            widget: this.container.querySelector('.ai-chat-widget'),
            trigger: this.container.querySelector('.widget-trigger'),
            overlay: this.container.querySelector('.widget-overlay'),
            header: this.container.querySelector('.widget-header'),
            body: this.container.querySelector('.widget-body'),
            footer: this.container.querySelector('.widget-footer'),
            messagesContainer: this.container.querySelector('.messages-container'),
            messageInput: this.container.querySelector('.message-input'),
            sendBtn: this.container.querySelector('.send-button'),
            minimizeBtn: this.container.querySelector('.minimize-btn'),
            closeBtn: this.container.querySelector('.close-btn'),
            loadingEl: this.container.querySelector('.widget-loading'),
            errorEl: this.container.querySelector('.widget-error'),
            typingIndicator: this.container.querySelector('.typing-indicator'),
            connectionStatus: this.container.querySelector('.connection-status')
        };
    }
    
    bindEvents() {
        // Trigger button
        this.addListener(this.elements.trigger, 'click', () => this.toggle());
        
        // Close/Minimize buttons
        this.addListener(this.elements.minimizeBtn, 'click', () => this.minimize());
        this.addListener(this.elements.closeBtn, 'click', () => this.close());
        
        // Overlay click
        this.addListener(this.elements.overlay, 'click', (e) => {
            if (e.target === this.elements.overlay) this.close();
        });
        
        // Message input
        this.addListener(this.elements.messageInput, 'keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        this.addListener(this.elements.messageInput, 'input', () => {
            this.handleTyping();
        });
        
        // Send button
        this.addListener(this.elements.sendBtn, 'click', () => this.sendMessage());
        
        // Global events
        this.addListener(document, 'keydown', (e) => {
            if (e.key === 'Escape' && this.state.isOpen) {
                this.close();
            }
        });
        
        // Auto minimize on blur
        if (this.config.minimize_on_blur) {
            this.addListener(document, 'click', (e) => {
                if (!this.container.contains(e.target) && this.state.isOpen) {
                    this.minimize();
                }
            });
        }
        
        // Window events
        this.addListener(window, 'beforeunload', () => this.saveState());
        this.addListener(window, 'resize', () => this.handleResize());
        
        // Network status
        this.addListener(window, 'online', () => this.handleOnline());
        this.addListener(window, 'offline', () => this.handleOffline());
    }
    
    addListener(element, event, handler) {
        if (!element) return;
        
        element.addEventListener(event, handler);
        
        if (!this.listeners.has(element)) {
            this.listeners.set(element, []);
        }
        this.listeners.get(element).push({ event, handler });
    }
    
    removeListeners() {
        this.listeners.forEach((listeners, element) => {
            listeners.forEach(({ event, handler }) => {
                element.removeEventListener(event, handler);
            });
        });
        this.listeners.clear();
    }
    
    toggle() {
        this.state.isOpen ? this.close() : this.open();
    }
    
    open() {
        if (this.state.isOpen) return;
        
        this.state.isOpen = true;
        this.elements.widget.style.display = 'flex';
        this.elements.widget.setAttribute('data-state', 'open');
        
        if (this.elements.overlay) {
            this.elements.overlay.style.display = 'block';
            requestAnimationFrame(() => {
                this.elements.overlay.classList.add('active');
            });
        }
        
        // Animations
        if (this.config.animations.enabled) {
            this.elements.widget.classList.add('opening');
            setTimeout(() => {
                this.elements.widget.classList.remove('opening');
                this.elements.widget.classList.add('open');
            }, 300);
        }
        
        // Accessibility
        this.elements.widget.setAttribute('aria-hidden', 'false');
        this.elements.trigger?.setAttribute('aria-expanded', 'true');
        
        // Focus management
        this.elements.messageInput?.focus();
        
        // Load messages if needed
        if (this.state.messages.length === 0) {
            this.loadMessages();
        }
        
        this.announce('Chat widget açıldı');
        this.trackEvent('widget_opened');
        this.saveState();
    }
    
    close() {
        if (!this.state.isOpen) return;
        
        this.state.isOpen = false;
        this.elements.widget.setAttribute('data-state', 'closed');
        
        // Animations
        if (this.config.animations.enabled) {
            this.elements.widget.classList.add('closing');
            this.elements.overlay?.classList.remove('active');
            
            setTimeout(() => {
                this.elements.widget.style.display = 'none';
                this.elements.overlay && (this.elements.overlay.style.display = 'none');
                this.elements.widget.classList.remove('closing', 'open');
            }, 300);
        } else {
            this.elements.widget.style.display = 'none';
            this.elements.overlay && (this.elements.overlay.style.display = 'none');
        }
        
        // Accessibility
        this.elements.widget.setAttribute('aria-hidden', 'true');
        this.elements.trigger?.setAttribute('aria-expanded', 'false');
        this.elements.trigger?.focus();
        
        this.announce('Chat widget kapatıldı');
        this.trackEvent('widget_closed');
        this.saveState();
    }
    
    minimize() {
        this.elements.widget.classList.add('minimized');
        this.trackEvent('widget_minimized');
    }
    
    async sendMessage(message = null) {
        message = message || this.elements.messageInput?.value?.trim();
        
        if (!message || this.state.isLoading) return;
        
        // Validate message length
        if (message.length > this.config.max_message_length) {
            this.showError(`Mesaj çok uzun (max ${this.config.max_message_length} karakter)`);
            return;
        }
        
        // Clear input
        if (this.elements.messageInput) {
            this.elements.messageInput.value = '';
            this.elements.messageInput.focus();
        }
        
        // Add user message
        this.addMessage('user', message);
        
        // Show loading
        this.setLoading(true);
        this.showTypingIndicator();
        
        try {
            const response = await this.callAPI(message);
            
            if (response.success) {
                this.addMessage('assistant', response.content);
                
                if (response.conversationId) {
                    this.state.conversationId = response.conversationId;
                }
                
                if (response.suggestions) {
                    this.showSuggestions(response.suggestions);
                }
            } else {
                throw new Error(response.error || 'API yanıt vermedi');
            }
            
        } catch (error) {
            console.error('Message send error:', error);
            this.showError('Mesaj gönderilemedi. Lütfen tekrar deneyin.');
            this.trackEvent('message_error', { error: error.message });
            
            // Add to retry queue
            this.messageQueue.push(message);
            
        } finally {
            this.setLoading(false);
            this.hideTypingIndicator();
        }
    }
    
    async callAPI(message) {
        const response = await fetch(this.config.api_endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
                'X-Widget-ID': this.config.widget_id
            },
            body: JSON.stringify({
                message,
                conversation_id: this.state.conversationId,
                context: this.gatherContext()
            })
        });
        
        if (!response.ok) {
            const error = await response.text();
            throw new Error(`HTTP ${response.status}: ${error}`);
        }
        
        return await response.json();
    }
    
    addMessage(type, content, metadata = {}) {
        const message = {
            id: this.generateId(),
            type,
            content,
            timestamp: new Date().toISOString(),
            metadata
        };
        
        this.state.messages.push(message);
        this.renderMessage(message);
        this.saveMessages();
        
        // Sound notification
        if (this.config.sound_notifications && type === 'assistant') {
            this.playNotificationSound();
        }
        
        this.trackEvent('message_added', { type });
    }
    
    renderMessage(message) {
        if (!this.elements.messagesContainer) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `message message-${message.type}`;
        messageEl.dataset.messageId = message.id;
        
        const avatar = message.type === 'assistant' 
            ? '<div class="avatar ai-avatar">AI</div>'
            : '<div class="avatar user-avatar">You</div>';
        
        messageEl.innerHTML = `
            ${avatar}
            <div class="message-content">
                <div class="message-text">${this.escapeHtml(message.content)}</div>
                <div class="message-time">${this.formatTime(message.timestamp)}</div>
            </div>
        `;
        
        this.elements.messagesContainer.appendChild(messageEl);
        this.scrollToBottom();
        
        // Animate message entrance
        if (this.config.animations.enabled) {
            messageEl.classList.add('fade-in');
        }
        
        this.announce(`${message.type === 'user' ? 'Siz' : 'Asistan'}: ${message.content}`);
    }
    
    showTypingIndicator() {
        if (!this.config.typing_indicator || !this.elements.typingIndicator) return;
        
        this.state.isTyping = true;
        this.elements.typingIndicator.style.display = 'flex';
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        if (!this.elements.typingIndicator) return;
        
        this.state.isTyping = false;
        this.elements.typingIndicator.style.display = 'none';
    }
    
    showSuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) return;
        
        const suggestionsEl = document.createElement('div');
        suggestionsEl.className = 'suggestions';
        
        suggestions.forEach(suggestion => {
            const btn = document.createElement('button');
            btn.className = 'suggestion-btn';
            btn.textContent = suggestion;
            btn.onclick = () => {
                this.sendMessage(suggestion);
                suggestionsEl.remove();
            };
            suggestionsEl.appendChild(btn);
        });
        
        this.elements.messagesContainer?.appendChild(suggestionsEl);
        this.scrollToBottom();
    }
    
    async loadMessages() {
        if (!this.state.conversationId) return;
        
        try {
            const response = await fetch(`${this.config.api_endpoint}/messages/${this.state.conversationId}`, {
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'X-Widget-ID': this.config.widget_id
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.state.messages = data.messages || [];
                this.renderAllMessages();
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }
    
    renderAllMessages() {
        if (!this.elements.messagesContainer) return;
        
        this.elements.messagesContainer.innerHTML = '';
        this.state.messages.forEach(message => this.renderMessage(message));
    }
    
    setupWebSocket() {
        if (!this.config.ai_enabled || !window.WebSocket) return;
        
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.host}/ws/ai-widget/${this.config.widget_id}`;
        
        try {
            this.ws = new WebSocket(wsUrl);
            
            this.ws.onopen = () => {
                this.state.isConnected = true;
                this.updateConnectionStatus('connected');
                this.processMessageQueue();
            };
            
            this.ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleWebSocketMessage(data);
            };
            
            this.ws.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.state.isConnected = false;
                this.updateConnectionStatus('error');
            };
            
            this.ws.onclose = () => {
                this.state.isConnected = false;
                this.updateConnectionStatus('disconnected');
                this.attemptReconnect();
            };
            
        } catch (error) {
            console.error('WebSocket setup failed:', error);
        }
    }
    
    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'message':
                this.addMessage('assistant', data.content, data.metadata);
                break;
            case 'typing':
                data.isTyping ? this.showTypingIndicator() : this.hideTypingIndicator();
                break;
            case 'status':
                this.updateConnectionStatus(data.status);
                break;
            default:
                console.log('Unknown WebSocket message type:', data.type);
        }
    }
    
    attemptReconnect() {
        if (this.state.reconnectCount >= this.config.reconnect_attempts) {
            console.log('Max reconnection attempts reached');
            return;
        }
        
        this.state.reconnectCount++;
        const delay = Math.min(1000 * Math.pow(2, this.state.reconnectCount), 30000);
        
        this.reconnectTimer = setTimeout(() => {
            console.log(`Reconnection attempt ${this.state.reconnectCount}`);
            this.setupWebSocket();
        }, delay);
    }
    
    processMessageQueue() {
        while (this.messageQueue.length > 0) {
            const message = this.messageQueue.shift();
            this.sendMessage(message);
        }
    }
    
    updateConnectionStatus(status) {
        if (!this.elements.connectionStatus) return;
        
        this.elements.connectionStatus.className = `connection-status status-${status}`;
        this.elements.connectionStatus.textContent = this.getStatusText(status);
    }
    
    getStatusText(status) {
        const texts = {
            connected: 'Bağlı',
            disconnected: 'Bağlantı kesildi',
            connecting: 'Bağlanıyor...',
            error: 'Bağlantı hatası'
        };
        return texts[status] || status;
    }
    
    handleTyping() {
        // Send typing indicator via WebSocket
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify({
                type: 'typing',
                isTyping: true
            }));
            
            // Clear previous timeout
            clearTimeout(this.typingTimeout);
            
            // Stop typing after 2 seconds of inactivity
            this.typingTimeout = setTimeout(() => {
                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.ws.send(JSON.stringify({
                        type: 'typing',
                        isTyping: false
                    }));
                }
            }, 2000);
        }
    }
    
    handleResize() {
        // Adjust widget position/size on window resize
        if (this.state.isOpen && window.innerWidth < 640) {
            this.elements.widget.classList.add('mobile-view');
        } else {
            this.elements.widget.classList.remove('mobile-view');
        }
    }
    
    handleOnline() {
        this.updateConnectionStatus('connecting');
        this.setupWebSocket();
    }
    
    handleOffline() {
        this.updateConnectionStatus('disconnected');
    }
    
    setLoading(loading) {
        this.state.isLoading = loading;
        
        if (this.elements.loadingEl) {
            this.elements.loadingEl.style.display = loading ? 'flex' : 'none';
        }
        
        if (this.elements.sendBtn) {
            this.elements.sendBtn.disabled = loading;
        }
        
        if (this.elements.messageInput) {
            this.elements.messageInput.disabled = loading;
        }
    }
    
    showError(message) {
        if (!this.elements.errorEl) return;
        
        const errorMessage = this.elements.errorEl.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
        
        this.elements.errorEl.style.display = 'flex';
        
        setTimeout(() => {
            this.hideError();
        }, 5000);
    }
    
    hideError() {
        if (this.elements.errorEl) {
            this.elements.errorEl.style.display = 'none';
        }
    }
    
    scrollToBottom() {
        if (!this.elements.messagesContainer) return;
        
        this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
    }
    
    gatherContext() {
        return {
            url: window.location.href,
            title: document.title,
            referrer: document.referrer,
            userAgent: navigator.userAgent,
            language: navigator.language,
            screenSize: `${window.screen.width}x${window.screen.height}`,
            viewport: `${window.innerWidth}x${window.innerHeight}`,
            timestamp: new Date().toISOString()
        };
    }
    
    saveState() {
        const state = {
            isOpen: this.state.isOpen,
            conversationId: this.state.conversationId,
            messages: this.state.messages.slice(-50) // Keep last 50 messages
        };
        
        localStorage.setItem(`ai-widget-state-${this.config.widget_id}`, JSON.stringify(state));
    }
    
    restoreState() {
        try {
            const saved = localStorage.getItem(`ai-widget-state-${this.config.widget_id}`);
            if (saved) {
                const state = JSON.parse(saved);
                this.state.conversationId = state.conversationId;
                this.state.messages = state.messages || [];
                
                if (state.isOpen && !this.config.auto_open) {
                    // Don't auto-open on restore
                }
            }
        } catch (error) {
            console.error('Failed to restore state:', error);
        }
    }
    
    saveMessages() {
        try {
            const messages = this.state.messages.slice(-100); // Keep last 100
            localStorage.setItem(`ai-widget-messages-${this.config.widget_id}`, JSON.stringify(messages));
        } catch (error) {
            console.error('Failed to save messages:', error);
        }
    }
    
    setupAccessibility() {
        // ARIA attributes
        this.elements.widget?.setAttribute('role', 'dialog');
        this.elements.widget?.setAttribute('aria-label', 'AI Asistan Sohbet Penceresi');
        this.elements.widget?.setAttribute('aria-modal', 'true');
        
        this.elements.trigger?.setAttribute('aria-label', 'AI Asistan\'ı Aç');
        this.elements.trigger?.setAttribute('aria-haspopup', 'dialog');
        
        this.elements.messageInput?.setAttribute('aria-label', 'Mesajınızı yazın');
        this.elements.sendBtn?.setAttribute('aria-label', 'Mesajı Gönder');
        
        // Focus trap
        this.setupFocusTrap();
        
        // Keyboard navigation
        this.setupKeyboardNavigation();
    }
    
    setupFocusTrap() {
        if (!this.elements.widget) return;
        
        const focusableElements = this.elements.widget.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];
        
        this.elements.widget.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab') return;
            
            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable?.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable?.focus();
                }
            }
        });
    }
    
    setupKeyboardNavigation() {
        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Shift + A to toggle widget
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                this.toggle();
            }
        });
    }
    
    setupPerformanceMonitoring() {
        if (!window.PerformanceObserver) return;
        
        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.name.includes('ai-widget')) {
                    console.log('Widget Performance:', {
                        name: entry.name,
                        duration: entry.duration,
                        startTime: entry.startTime
                    });
                    
                    // Send to analytics
                    this.trackEvent('performance', {
                        metric: entry.name,
                        value: entry.duration
                    });
                }
            }
        });
        
        observer.observe({ entryTypes: ['measure', 'navigation'] });
    }
    
    trackEvent(eventName, data = {}) {
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                event_category: 'ai_widget',
                widget_id: this.config.widget_id,
                ...data
            });
        }
        
        // Custom analytics endpoint
        if (this.config.analytics_endpoint) {
            fetch(this.config.analytics_endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Widget-ID': this.config.widget_id
                },
                body: JSON.stringify({
                    event: eventName,
                    data,
                    context: this.gatherContext()
                })
            }).catch(error => console.error('Analytics error:', error));
        }
    }
    
    announce(message) {
        const announcer = document.getElementById('widget-announcements') || this.createAnnouncer();
        announcer.textContent = message;
        
        // Clear after announcement
        setTimeout(() => {
            announcer.textContent = '';
        }, 1000);
    }
    
    createAnnouncer() {
        const announcer = document.createElement('div');
        announcer.id = 'widget-announcements';
        announcer.className = 'sr-only';
        announcer.setAttribute('aria-live', 'polite');
        announcer.setAttribute('aria-atomic', 'true');
        document.body.appendChild(announcer);
        return announcer;
    }
    
    playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUazt5a1uIAU7k9z1ll'
            + 'sbBCl+0O/hlkwKFWS46+OlWA0MRJzd8cRyIgU2idny1nkpBSl+0OzfhkwMFmW56+OlWA0MRJve8chyIAU2idny1Xg');
        audio.volume = 0.3;
        audio.play().catch(() => {});
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('tr-TR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    generateId() {
        return `msg-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    }
    
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
    
    destroy() {
        // Clean up
        this.removeListeners();
        
        if (this.ws) {
            this.ws.close();
        }
        
        clearTimeout(this.reconnectTimer);
        clearTimeout(this.typingTimeout);
        
        this.saveState();
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AIWidgetCore;
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    window.AIWidgetInstances = window.AIWidgetInstances || [];
    
    document.querySelectorAll('.ai-widget-container').forEach(container => {
        const config = {
            ...window.AIWidgetConfig,
            ...JSON.parse(container.dataset.config || '{}')
        };
        
        const instance = new AIWidgetCore(container, config);
        window.AIWidgetInstances.push(instance);
    });
});