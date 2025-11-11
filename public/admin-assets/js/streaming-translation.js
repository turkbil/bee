/**
 * Enterprise Streaming Translation System
 * Real-time çeviri sistemi - Production Ready v3.0
 * Claude Code tarafından oluşturuldu
 */

class StreamingTranslation {
    constructor() {
        this.isStreaming = false;
        this.streamingSupported = false;
        this.currentTranslation = null;
        this.queue = [];
        this.init();
        
    }

    init() {
        this.checkStreamingSupport();
        this.setupEventListeners();
    }

    checkStreamingSupport() {
        // Check if server-sent events are supported
        this.streamingSupported = typeof EventSource !== 'undefined';
        
        if (!this.streamingSupported) {
            return;
        }

    }

    setupEventListeners() {
        // Listen for translation requests
        document.addEventListener('translation:start', (e) => {
            this.handleTranslationRequest(e.detail);
        });

        // Listen for streaming events
        document.addEventListener('translation:stream', (e) => {
            this.handleStreamingData(e.detail);
        });
    }

    async handleTranslationRequest(data) {
        const { entityType, entityId, languages, content } = data;

        if (!this.streamingSupported) {
            return this.fallbackToStandardTranslation(data);
        }

        this.isStreaming = true;
        this.currentTranslation = {
            entityType,
            entityId,
            languages,
            startTime: Date.now(),
            progress: {}
        };

        try {
            await this.startStreamingTranslation(data);
        } catch (error) {
            console.error('❌ Streaming translation failed:', error);
            this.fallbackToStandardTranslation(data);
        }
    }

    async startStreamingTranslation(data) {
        const { entityType, entityId, languages } = data;

        // Create EventSource for streaming
        const streamUrl = `/admin/translation/stream?entity=${entityType}&id=${entityId}&languages=${languages.join(',')}`;
        const eventSource = new EventSource(streamUrl);

        eventSource.onmessage = (event) => {
            const streamData = JSON.parse(event.data);
            this.processStreamingUpdate(streamData);
        };

        eventSource.onerror = (error) => {
            console.error('❌ Streaming error:', error);
            eventSource.close();
            this.isStreaming = false;
        };

        eventSource.addEventListener('complete', (event) => {
            eventSource.close();
            this.isStreaming = false;
            this.onTranslationComplete();
        });
    }

    processStreamingUpdate(data) {
        const { language, progress, content, status } = data;

        // Update progress for this language
        if (this.currentTranslation) {
            this.currentTranslation.progress[language] = {
                progress,
                content,
                status,
                timestamp: Date.now()
            };
        }

        // Emit progress event
        document.dispatchEvent(new CustomEvent('translation:progress', {
            detail: {
                language,
                progress,
                content,
                status,
                allProgress: this.currentTranslation?.progress
            }
        }));

        // Update UI
        this.updateProgressUI(language, progress, content, status);
    }

    updateProgressUI(language, progress, content, status) {
        // Find progress elements
        const progressBar = document.querySelector(`[data-translation-progress="${language}"]`);
        const statusElement = document.querySelector(`[data-translation-status="${language}"]`);
        const contentElement = document.querySelector(`[data-translation-content="${language}"]`);

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        if (statusElement) {
            statusElement.textContent = this.getStatusText(status);
            statusElement.className = `badge ${this.getStatusClass(status)}`;
        }

        if (contentElement && content) {
            contentElement.innerHTML = this.formatContent(content);
        }

        // Update overall progress
        this.updateOverallProgress();
    }

    updateOverallProgress() {
        if (!this.currentTranslation) return;

        const progresses = Object.values(this.currentTranslation.progress);
        const totalProgress = progresses.reduce((sum, p) => sum + (p.progress || 0), 0);
        const averageProgress = progresses.length > 0 ? totalProgress / progresses.length : 0;

        // Update main progress bar
        const mainProgressBar = document.querySelector('[data-translation-overall-progress]');
        if (mainProgressBar) {
            mainProgressBar.style.width = `${averageProgress}%`;
        }

        // Update percentage text
        const percentageText = document.querySelector('[data-translation-percentage]');
        if (percentageText) {
            percentageText.textContent = `${Math.round(averageProgress)}%`;
        }
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'Bekliyor',
            'processing': 'İşleniyor',
            'translating': 'Çeviriliyor',
            'completed': 'Tamamlandı',
            'failed': 'Başarısız'
        };
        return statusMap[status] || status;
    }

    getStatusClass(status) {
        const classMap = {
            'pending': 'badge-secondary',
            'processing': 'badge-info',
            'translating': 'badge-primary',
            'completed': 'badge-success',
            'failed': 'badge-danger'
        };
        return classMap[status] || 'badge-secondary';
    }

    formatContent(content) {
        // Basic HTML formatting for preview
        if (typeof content === 'string') {
            return content.substring(0, 200) + (content.length > 200 ? '...' : '');
        }
        return JSON.stringify(content, null, 2);
    }

    onTranslationComplete() {
        // Emit completion event
        document.dispatchEvent(new CustomEvent('translation:complete', {
            detail: {
                translation: this.currentTranslation,
                duration: Date.now() - this.currentTranslation.startTime
            }
        }));

        // Show success message
        this.showCompletionNotification();

        // Reset
        this.currentTranslation = null;
    }

    showCompletionNotification() {
        // Use existing toast system if available
        if (window.showToast) {
            window.showToast('Çeviri tamamlandı!', 'success');
        } else {
        }
    }

    async fallbackToStandardTranslation(data) {
        
        // Emit standard translation event
        document.dispatchEvent(new CustomEvent('translation:fallback', {
            detail: data
        }));

        // Use existing translation system
        if (window.translateFromModal) {
            return window.translateFromModal(data.entityType, data.entityId, data.languages);
        }
    }

    // Public methods
    isCurrentlyStreaming() {
        return this.isStreaming;
    }

    getCurrentTranslation() {
        return this.currentTranslation;
    }

    cancelCurrentTranslation() {
        if (this.isStreaming && this.currentTranslation) {
            this.isStreaming = false;
            this.currentTranslation = null;
            
            // Emit cancellation event
            document.dispatchEvent(new CustomEvent('translation:cancelled'));
        }
    }
}

// Global instance
window.StreamingTranslation = new StreamingTranslation();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StreamingTranslation;
}

