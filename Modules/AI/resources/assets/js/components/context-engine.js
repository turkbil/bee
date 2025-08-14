/**
 * Context Engine JavaScript Module - Advanced Context Processing
 * UNIVERSAL INPUT SYSTEM V3 - Enterprise Context Intelligence
 * 
 * Features:
 * - Real-time context analysis and scoring
 * - Multi-dimensional context detection (user, module, content, temporal)
 * - Context optimization recommendations
 * - Performance monitoring and analytics
 * - Accessibility compliance
 * - Error handling and recovery
 * 
 * Dependencies: Alpine.js 3.x, Modern Browser APIs
 * Compatible: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * 
 * @version 3.0.0
 * @author UNIVERSAL INPUT SYSTEM V3
 */

class ContextEngine {
    constructor(config = {}) {
        this.config = {
            apiEndpoint: '/admin/ai/universal/context/analyze',
            cacheTtl: 300000, // 5 minutes
            debounceDelay: 500,
            maxRetries: 3,
            enableAnalytics: true,
            enableAccessibility: true,
            performanceThreshold: 100, // ms
            contextTypes: ['user', 'module', 'content', 'temporal', 'system'],
            ...config
        };

        this.cache = new Map();
        this.analytics = {
            totalAnalyses: 0,
            averageTime: 0,
            errorCount: 0,
            cacheHits: 0,
            contextScores: []
        };
        
        this.debounceTimers = new Map();
        this.abortControllers = new Map();
        this.isInitialized = false;
        
        this.init();
    }

    /**
     * Initialize Context Engine
     */
    async init() {
        try {
            this.setupPerformanceMonitoring();
            this.setupAccessibility();
            this.setupEventListeners();
            
            if (this.config.enableAnalytics) {
                this.startAnalyticsCollection();
            }
            
            this.isInitialized = true;
            this.log('Context Engine initialized successfully');
            
        } catch (error) {
            this.handleError('Initialization failed', error);
        }
    }

    /**
     * Analyze context for given input data
     */
    async analyzeContext(inputData, options = {}) {
        if (!this.isInitialized) {
            await this.init();
        }

        const startTime = performance.now();
        const analysisId = this.generateAnalysisId();
        
        try {
            // Check cache first
            const cacheKey = this.generateCacheKey(inputData, options);
            const cached = this.getFromCache(cacheKey);
            
            if (cached) {
                this.analytics.cacheHits++;
                this.log('Context analysis served from cache', { analysisId, cacheKey });
                return cached;
            }

            // Prepare analysis request
            const analysisRequest = await this.prepareAnalysisRequest(inputData, options);
            
            // Perform context analysis
            const contextResult = await this.performContextAnalysis(analysisRequest, analysisId);
            
            // Process and enhance results
            const enhancedResult = await this.enhanceContextResult(contextResult, inputData);
            
            // Cache result
            this.setCache(cacheKey, enhancedResult);
            
            // Update analytics
            this.updateAnalytics(startTime, enhancedResult);
            
            this.log('Context analysis completed', {
                analysisId,
                score: enhancedResult.overallScore,
                duration: performance.now() - startTime
            });
            
            return enhancedResult;
            
        } catch (error) {
            this.analytics.errorCount++;
            this.handleError('Context analysis failed', error, { analysisId, inputData });
            throw error;
        }
    }

    /**
     * Prepare analysis request with context enrichment
     */
    async prepareAnalysisRequest(inputData, options) {
        const timestamp = Date.now();
        const userContext = await this.collectUserContext();
        const moduleContext = await this.collectModuleContext();
        const systemContext = await this.collectSystemContext();
        
        return {
            id: this.generateAnalysisId(),
            timestamp,
            inputData: this.sanitizeInputData(inputData),
            contexts: {
                user: userContext,
                module: moduleContext,
                system: systemContext,
                temporal: this.getTemporalContext(),
                content: this.analyzeContentContext(inputData)
            },
            options: {
                types: options.contextTypes || this.config.contextTypes,
                depth: options.analysisDepth || 'standard',
                includeRecommendations: options.includeRecommendations !== false,
                performanceMode: options.performanceMode || 'balanced',
                ...options
            },
            metadata: {
                userAgent: navigator.userAgent,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: {
                    width: screen.width,
                    height: screen.height,
                    colorDepth: screen.colorDepth
                }
            }
        };
    }

    /**
     * Perform actual context analysis via API
     */
    async performContextAnalysis(request, analysisId) {
        const controller = new AbortController();
        this.abortControllers.set(analysisId, controller);
        
        try {
            const response = await fetch(this.config.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Analysis-ID': analysisId
                },
                body: JSON.stringify(request),
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(`Analysis API responded with ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Analysis failed');
            }

            return result.data;
            
        } finally {
            this.abortControllers.delete(analysisId);
        }
    }

    /**
     * Enhance context result with client-side intelligence
     */
    async enhanceContextResult(contextResult, inputData) {
        const enhancement = {
            clientSideScore: this.calculateClientSideScore(contextResult, inputData),
            performanceMetrics: this.getPerformanceMetrics(),
            accessibility: this.getAccessibilityMetrics(),
            recommendations: await this.generateSmartRecommendations(contextResult),
            optimization: this.suggestOptimizations(contextResult),
            userGuidance: this.generateUserGuidance(contextResult)
        };

        return {
            ...contextResult,
            enhancement,
            overallScore: this.calculateOverallScore(contextResult, enhancement),
            quality: this.assessQuality(contextResult, enhancement),
            timestamp: Date.now()
        };
    }

    /**
     * Calculate client-side context score
     */
    calculateClientSideScore(contextResult, inputData) {
        let score = 0;
        let maxScore = 0;

        // Input completeness (0-25 points)
        const completeness = this.assessInputCompleteness(inputData);
        score += completeness * 25;
        maxScore += 25;

        // Context richness (0-25 points)
        const richness = this.assessContextRichness(contextResult);
        score += richness * 25;
        maxScore += 25;

        // Relevance (0-25 points)
        const relevance = this.assessContextRelevance(contextResult, inputData);
        score += relevance * 25;
        maxScore += 25;

        // User experience (0-25 points)
        const ux = this.assessUserExperience(contextResult);
        score += ux * 25;
        maxScore += 25;

        return {
            score: Math.round(score),
            maxScore,
            percentage: Math.round((score / maxScore) * 100),
            breakdown: {
                completeness: Math.round(completeness * 100),
                richness: Math.round(richness * 100),
                relevance: Math.round(relevance * 100),
                userExperience: Math.round(ux * 100)
            }
        };
    }

    /**
     * Generate smart recommendations
     */
    async generateSmartRecommendations(contextResult) {
        const recommendations = [];

        // Content recommendations
        if (contextResult.content?.score < 0.7) {
            recommendations.push({
                type: 'content',
                priority: 'high',
                title: '0çerik Kalitesini Art1r1n',
                message: '0çeriiniz daha detayl1 ve aç1klay1c1 olabilir.',
                action: 'expand_content',
                impact: 'high'
            });
        }

        // Context recommendations
        if (contextResult.contexts?.missing?.length > 0) {
            recommendations.push({
                type: 'context',
                priority: 'medium',
                title: 'Eksik Balam Bilgileri',
                message: `^u balam bilgileri eksik: ${contextResult.contexts.missing.join(', ')}`,
                action: 'add_context',
                impact: 'medium'
            });
        }

        // Performance recommendations
        const perfIssues = await this.detectPerformanceIssues();
        if (perfIssues.length > 0) {
            recommendations.push({
                type: 'performance',
                priority: 'low',
                title: 'Performans 0yile_tirmeleri',
                message: 'Baz1 performans iyile_tirmeleri mevcut.',
                action: 'optimize_performance',
                impact: 'low',
                details: perfIssues
            });
        }

        return recommendations.sort((a, b) => {
            const priorityOrder = { high: 3, medium: 2, low: 1 };
            return priorityOrder[b.priority] - priorityOrder[a.priority];
        });
    }

    /**
     * Collect user context information
     */
    async collectUserContext() {
        return {
            preferences: this.getUserPreferences(),
            history: this.getUserHistory(),
            capabilities: this.getUserCapabilities(),
            currentSession: this.getCurrentSessionInfo()
        };
    }

    /**
     * Collect module context information
     */
    async collectModuleContext() {
        return {
            currentModule: this.getCurrentModule(),
            availableFeatures: this.getAvailableFeatures(),
            configuration: this.getModuleConfiguration(),
            integrations: this.getModuleIntegrations()
        };
    }

    /**
     * Collect system context information
     */
    async collectSystemContext() {
        return {
            performance: await this.getSystemPerformance(),
            resources: this.getResourceUsage(),
            capabilities: this.getSystemCapabilities(),
            status: this.getSystemStatus()
        };
    }

    /**
     * Get temporal context
     */
    getTemporalContext() {
        const now = new Date();
        return {
            timestamp: now.getTime(),
            hour: now.getHours(),
            dayOfWeek: now.getDay(),
            month: now.getMonth(),
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            workingHours: this.isWorkingHours(),
            season: this.getCurrentSeason()
        };
    }

    /**
     * Analyze content context
     */
    analyzeContentContext(inputData) {
        const analysis = {
            length: 0,
            complexity: 0,
            language: 'tr',
            topics: [],
            sentiment: 'neutral',
            readability: 0
        };

        if (typeof inputData === 'string') {
            analysis.length = inputData.length;
            analysis.complexity = this.calculateTextComplexity(inputData);
            analysis.language = this.detectLanguage(inputData);
            analysis.topics = this.extractTopics(inputData);
            analysis.sentiment = this.analyzeSentiment(inputData);
            analysis.readability = this.calculateReadability(inputData);
        } else if (typeof inputData === 'object') {
            // Analyze object structure
            analysis.structure = this.analyzeObjectStructure(inputData);
            analysis.depth = this.calculateObjectDepth(inputData);
            analysis.keys = Object.keys(inputData);
        }

        return analysis;
    }

    /**
     * Setup performance monitoring
     */
    setupPerformanceMonitoring() {
        if (!window.PerformanceObserver) return;

        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.name.includes('context-analysis')) {
                    this.analytics.performanceEntries.push({
                        name: entry.name,
                        duration: entry.duration,
                        startTime: entry.startTime,
                        timestamp: Date.now()
                    });
                }
            }
        });

        observer.observe({ entryTypes: ['measure'] });
    }

    /**
     * Setup accessibility features
     */
    setupAccessibility() {
        if (!this.config.enableAccessibility) return;

        // ARIA live region for context updates
        if (!document.getElementById('context-live-region')) {
            const liveRegion = document.createElement('div');
            liveRegion.id = 'context-live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.style.cssText = 'position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;';
            document.body.appendChild(liveRegion);
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });

        // Handle visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseAnalytics();
            } else {
                this.resumeAnalytics();
            }
        });
    }

    /**
     * Cache management
     */
    generateCacheKey(inputData, options) {
        const keyData = {
            input: typeof inputData === 'string' ? inputData.slice(0, 100) : JSON.stringify(inputData).slice(0, 100),
            options: JSON.stringify(options),
            timestamp: Math.floor(Date.now() / this.config.cacheTtl)
        };
        
        return btoa(JSON.stringify(keyData));
    }

    getFromCache(key) {
        const cached = this.cache.get(key);
        if (!cached) return null;
        
        if (Date.now() - cached.timestamp > this.config.cacheTtl) {
            this.cache.delete(key);
            return null;
        }
        
        return cached.data;
    }

    setCache(key, data) {
        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
        
        // Clean old entries
        if (this.cache.size > 100) {
            const oldestKey = this.cache.keys().next().value;
            this.cache.delete(oldestKey);
        }
    }

    /**
     * Analytics and monitoring
     */
    updateAnalytics(startTime, result) {
        const duration = performance.now() - startTime;
        
        this.analytics.totalAnalyses++;
        this.analytics.averageTime = (this.analytics.averageTime + duration) / 2;
        this.analytics.contextScores.push(result.overallScore);
        
        if (this.analytics.contextScores.length > 100) {
            this.analytics.contextScores.shift();
        }
    }

    startAnalyticsCollection() {
        setInterval(() => {
            this.sendAnalytics();
        }, 60000); // Send every minute
    }

    async sendAnalytics() {
        if (this.analytics.totalAnalyses === 0) return;

        try {
            const analyticsData = {
                ...this.analytics,
                timestamp: Date.now(),
                sessionId: this.getSessionId()
            };

            await fetch('/admin/ai/universal/analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(analyticsData)
            });
            
        } catch (error) {
            this.log('Analytics sending failed', error);
        }
    }

    /**
     * Utility methods
     */
    generateAnalysisId() {
        return 'ctx_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    sanitizeInputData(data) {
        if (typeof data === 'string') {
            return data.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        }
        return data;
    }

    calculateTextComplexity(text) {
        const words = text.split(/\s+/).length;
        const sentences = text.split(/[.!?]+/).length;
        const avgWordsPerSentence = words / sentences;
        return Math.min(avgWordsPerSentence / 20, 1); // Normalize to 0-1
    }

    detectLanguage(text) {
        // Simple Turkish detection
        const turkishChars = /[ç1ö_üÇI0Ö^Ü]/g;
        const turkishWords = /\b(ve|bir|bu|_u|için|ile|olan|olan|deil|ama|fakat)\b/gi;
        
        const turkishCharCount = (text.match(turkishChars) || []).length;
        const turkishWordCount = (text.match(turkishWords) || []).length;
        
        return (turkishCharCount > 0 || turkishWordCount > 0) ? 'tr' : 'en';
    }

    extractTopics(text) {
        const words = text.toLowerCase().match(/\b\w{4,}\b/g) || [];
        const frequency = {};
        
        words.forEach(word => {
            frequency[word] = (frequency[word] || 0) + 1;
        });
        
        return Object.entries(frequency)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5)
            .map(([word]) => word);
    }

    analyzeSentiment(text) {
        const positive = /\b(iyi|güzel|harika|mükemmel|ba_ar1l1|mutlu|sevindirici)\b/gi;
        const negative = /\b(kötü|berbat|korkunç|ba_ar1s1z|üzücü|sinir|problem)\b/gi;
        
        const positiveCount = (text.match(positive) || []).length;
        const negativeCount = (text.match(negative) || []).length;
        
        if (positiveCount > negativeCount) return 'positive';
        if (negativeCount > positiveCount) return 'negative';
        return 'neutral';
    }

    calculateReadability(text) {
        const words = text.split(/\s+/).length;
        const sentences = text.split(/[.!?]+/).length;
        const syllables = this.countSyllables(text);
        
        // Simplified readability score
        const score = 206.835 - 1.015 * (words / sentences) - 84.6 * (syllables / words);
        return Math.max(0, Math.min(100, score)) / 100; // Normalize to 0-1
    }

    countSyllables(text) {
        return text.toLowerCase().split(/[aeiouüö1âîôû]/).length - 1;
    }

    isWorkingHours() {
        const hour = new Date().getHours();
        return hour >= 9 && hour <= 17;
    }

    getCurrentSeason() {
        const month = new Date().getMonth();
        if (month >= 2 && month <= 4) return 'spring';
        if (month >= 5 && month <= 7) return 'summer';
        if (month >= 8 && month <= 10) return 'autumn';
        return 'winter';
    }

    getSessionId() {
        if (!this.sessionId) {
            this.sessionId = 'ctx_session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        return this.sessionId;
    }

    /**
     * Error handling and logging
     */
    handleError(message, error, context = {}) {
        const errorInfo = {
            message,
            error: error.message || error,
            stack: error.stack,
            context,
            timestamp: Date.now(),
            userAgent: navigator.userAgent
        };

        console.error('[ContextEngine]', errorInfo);

        // Send error to server
        if (this.config.enableAnalytics) {
            fetch('/admin/ai/universal/errors', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(errorInfo)
            }).catch(() => {}); // Silent fail
        }
    }

    log(message, data = {}) {
        if (process.env.NODE_ENV === 'development') {
            console.log('[ContextEngine]', message, data);
        }
    }

    /**
     * Cleanup resources
     */
    cleanup() {
        // Abort pending requests
        for (const controller of this.abortControllers.values()) {
            controller.abort();
        }
        this.abortControllers.clear();

        // Clear timers
        for (const timer of this.debounceTimers.values()) {
            clearTimeout(timer);
        }
        this.debounceTimers.clear();

        // Clear cache
        this.cache.clear();

        // Send final analytics
        if (this.config.enableAnalytics && this.analytics.totalAnalyses > 0) {
            navigator.sendBeacon('/admin/ai/universal/analytics', 
                JSON.stringify(this.analytics));
        }
    }

    /**
     * Public API methods
     */
    async getContextScore(inputData, options = {}) {
        try {
            const result = await this.analyzeContext(inputData, options);
            return result.overallScore;
        } catch (error) {
            this.handleError('Failed to get context score', error);
            return 0;
        }
    }

    async getRecommendations(inputData, options = {}) {
        try {
            const result = await this.analyzeContext(inputData, options);
            return result.enhancement.recommendations;
        } catch (error) {
            this.handleError('Failed to get recommendations', error);
            return [];
        }
    }

    getAnalytics() {
        return { ...this.analytics };
    }

    clearCache() {
        this.cache.clear();
    }

    reset() {
        this.cleanup();
        this.analytics = {
            totalAnalyses: 0,
            averageTime: 0,
            errorCount: 0,
            cacheHits: 0,
            contextScores: []
        };
    }
}

// Alpine.js integration
document.addEventListener('alpine:init', () => {
    Alpine.data('contextEngine', (config = {}) => ({
        engine: null,
        currentAnalysis: null,
        isAnalyzing: false,
        recommendations: [],
        contextScore: 0,

        async init() {
            this.engine = new ContextEngine(config);
            await this.engine.init();
        },

        async analyzeContext(inputData, options = {}) {
            this.isAnalyzing = true;
            
            try {
                this.currentAnalysis = await this.engine.analyzeContext(inputData, options);
                this.contextScore = this.currentAnalysis.overallScore;
                this.recommendations = this.currentAnalysis.enhancement.recommendations;
                
                this.$dispatch('context-analyzed', {
                    analysis: this.currentAnalysis,
                    score: this.contextScore,
                    recommendations: this.recommendations
                });
                
            } catch (error) {
                this.$dispatch('context-error', { error: error.message });
            } finally {
                this.isAnalyzing = false;
            }
        },

        getScoreClass() {
            if (this.contextScore >= 80) return 'text-success';
            if (this.contextScore >= 60) return 'text-warning';
            return 'text-danger';
        },

        getScoreText() {
            if (this.contextScore >= 80) return 'Mükemmel';
            if (this.contextScore >= 60) return '0yi';
            if (this.contextScore >= 40) return 'Orta';
            return 'Geli_tirilmeli';
        }
    }));
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ContextEngine;
} else if (typeof define === 'function' && define.amd) {
    define([], () => ContextEngine);
} else {
    window.ContextEngine = ContextEngine;
}