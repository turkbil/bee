/**
 * Studio Editor - Error Handler
 * Merkezi hata yakalama ve yönetim sistemi
 */

window.StudioErrorHandler = (function() {
    
    // Error types
    const ErrorTypes = {
        NETWORK: 'network',
        VALIDATION: 'validation',
        RUNTIME: 'runtime',
        WIDGET: 'widget',
        EDITOR: 'editor',
        SECURITY: 'security',
        MEMORY: 'memory',
        PERFORMANCE: 'performance'
    };
    
    // Error levels
    const ErrorLevels = {
        INFO: 'info',
        WARNING: 'warning',
        ERROR: 'error',
        CRITICAL: 'critical'
    };
    
    // Error storage
    const errorLog = [];
    const errorCounts = new Map();
    
    /**
     * Error class for structured error handling
     */
    class StudioError extends Error {
        constructor(message, type = ErrorTypes.RUNTIME, level = ErrorLevels.ERROR, context = {}) {
            super(message);
            this.name = 'StudioError';
            this.type = type;
            this.level = level;
            this.context = context;
            this.timestamp = new Date().toISOString();
            this.stack = (new Error()).stack;
        }
    }
    
    /**
     * Error logging
     * @param {Error|StudioError} error - Error object
     * @param {string} context - Error context
     * @param {Object} additionalData - Additional data
     */
    function logError(error, context = '', additionalData = {}) {
        const errorEntry = {
            message: error.message,
            type: error.type || ErrorTypes.RUNTIME,
            level: error.level || ErrorLevels.ERROR,
            context: context,
            timestamp: new Date().toISOString(),
            stack: error.stack,
            url: window.location.href,
            userAgent: navigator.userAgent,
            additionalData: additionalData
        };
        
        // Error count tracking
        const errorKey = `${error.type}_${error.message}`;
        errorCounts.set(errorKey, (errorCounts.get(errorKey) || 0) + 1);
        
        // Add to error log
        errorLog.push(errorEntry);
        
        // Keep only last 100 errors
        if (errorLog.length > 100) {
            errorLog.shift();
        }
        
        // Console logging
        const consoleMethod = getConsoleMethod(errorEntry.level);
        consoleMethod(`[Studio Error] ${errorEntry.type.toUpperCase()}: ${errorEntry.message}`, errorEntry);
        
        // Send to backend if available
        sendErrorToBackend(errorEntry);
        
        // Show user notification for critical errors
        if (errorEntry.level === ErrorLevels.CRITICAL) {
            showCriticalErrorNotification(errorEntry);
        }
        
        return errorEntry;
    }
    
    /**
     * Get console method based on error level
     * @param {string} level - Error level
     * @returns {Function} Console method
     */
    function getConsoleMethod(level) {
        switch (level) {
            case ErrorLevels.INFO:
                return console.info;
            case ErrorLevels.WARNING:
                return console.warn;
            case ErrorLevels.ERROR:
                return console.error;
            case ErrorLevels.CRITICAL:
                return console.error;
            default:
                return console.log;
        }
    }
    
    /**
     * Send error to backend
     * @param {Object} errorEntry - Error entry
     */
    function sendErrorToBackend(errorEntry) {
        // Rate limiting - max 10 errors per minute
        const now = Date.now();
        const recentErrors = errorLog.filter(e => 
            Date.now() - new Date(e.timestamp).getTime() < 60000
        );
        
        if (recentErrors.length > 10) {
            return;
        }
        
        if (window.fetch) {
            fetch('/admin/studio/error/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(errorEntry)
            }).catch(err => {
                console.error('Error logging to backend failed:', err);
            });
        }
    }
    
    /**
     * Show critical error notification
     * @param {Object} errorEntry - Error entry
     */
    function showCriticalErrorNotification(errorEntry) {
        if (window.StudioNotification) {
            window.StudioNotification.error(
                'Kritik Hata',
                `Sistem hatası: ${errorEntry.message.substring(0, 100)}...`,
                10000 // 10 seconds
            );
        }
    }
    
    /**
     * Widget error handler
     * @param {string} widgetId - Widget ID
     * @param {Error} error - Error object
     * @param {string} operation - Operation type
     */
    function handleWidgetError(widgetId, error, operation = 'load') {
        const studioError = new StudioError(
            `Widget ${widgetId} ${operation} failed: ${error.message}`,
            ErrorTypes.WIDGET,
            ErrorLevels.ERROR,
            { widgetId, operation }
        );
        
        logError(studioError, 'Widget Operation', { widgetId, operation });
        
        // Show widget-specific error
        showWidgetError(widgetId, studioError);
        
        // Cleanup failed widget
        if (window.StudioMemoryManager) {
            window.StudioMemoryManager.cleanupWidget(widgetId);
        }
    }
    
    /**
     * Show widget error in UI
     * @param {string} widgetId - Widget ID
     * @param {StudioError} error - Error object
     */
    function showWidgetError(widgetId, error) {
        const container = document.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
        if (container) {
            const errorElement = document.createElement('div');
            errorElement.className = 'widget-error';
            errorElement.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Widget Hatası:</strong> ${error.message}
                    <button class="btn btn-sm btn-outline-danger retry-widget" onclick="window.studioRetryWidget('${widgetId}')">
                        <i class="fa fa-refresh"></i> Tekrar Dene
                    </button>
                </div>
            `;
            
            container.innerHTML = '';
            container.appendChild(errorElement);
        }
    }
    
    /**
     * Network error handler
     * @param {string} url - Request URL
     * @param {Error} error - Error object
     * @param {string} method - HTTP method
     */
    function handleNetworkError(url, error, method = 'GET') {
        const studioError = new StudioError(
            `Network request failed: ${error.message}`,
            ErrorTypes.NETWORK,
            ErrorLevels.ERROR,
            { url, method }
        );
        
        logError(studioError, 'Network Request', { url, method });
        
        // Show network error notification
        if (window.StudioNotification) {
            window.StudioNotification.error(
                'Ağ Hatası',
                'Sunucu bağlantısı başarısız. Lütfen internet bağlantınızı kontrol edin.',
                5000
            );
        }
    }
    
    /**
     * Validation error handler
     * @param {string} field - Field name
     * @param {string} message - Error message
     * @param {*} value - Invalid value
     */
    function handleValidationError(field, message, value) {
        const studioError = new StudioError(
            `Validation failed for ${field}: ${message}`,
            ErrorTypes.VALIDATION,
            ErrorLevels.WARNING,
            { field, value }
        );
        
        logError(studioError, 'Validation', { field, value });
        
        // Show validation error in UI
        showValidationError(field, message);
    }
    
    /**
     * Show validation error in UI
     * @param {string} field - Field name
     * @param {string} message - Error message
     */
    function showValidationError(field, message) {
        const fieldElement = document.querySelector(`[name="${field}"], #${field}`);
        if (fieldElement) {
            fieldElement.classList.add('is-invalid');
            
            // Remove existing error message
            const existingError = fieldElement.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            // Add new error message
            const errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            errorElement.textContent = message;
            fieldElement.parentNode.appendChild(errorElement);
            
            // Remove error after 5 seconds
            setTimeout(() => {
                fieldElement.classList.remove('is-invalid');
                errorElement.remove();
            }, 5000);
        }
    }
    
    /**
     * Global error handler setup
     */
    function setupGlobalErrorHandler() {
        // Catch unhandled JavaScript errors
        window.addEventListener('error', (event) => {
            const error = new StudioError(
                event.message,
                ErrorTypes.RUNTIME,
                ErrorLevels.ERROR,
                {
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno
                }
            );
            
            logError(error, 'Global Error Handler');
        });
        
        // Catch unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            const error = new StudioError(
                event.reason?.message || 'Unhandled Promise Rejection',
                ErrorTypes.RUNTIME,
                ErrorLevels.ERROR,
                { reason: event.reason }
            );
            
            logError(error, 'Unhandled Promise Rejection');
        });
        
        // Catch fetch errors
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);
                if (!response.ok) {
                    handleNetworkError(args[0], new Error(`HTTP ${response.status}`), args[1]?.method || 'GET');
                }
                return response;
            } catch (error) {
                handleNetworkError(args[0], error, args[1]?.method || 'GET');
                throw error;
            }
        };
    }
    
    /**
     * Try-catch wrapper for functions
     * @param {Function} fn - Function to wrap
     * @param {string} context - Error context
     * @returns {Function} Wrapped function
     */
    function wrapWithErrorHandler(fn, context = '') {
        return function(...args) {
            try {
                return fn.apply(this, args);
            } catch (error) {
                const studioError = new StudioError(
                    error.message,
                    ErrorTypes.RUNTIME,
                    ErrorLevels.ERROR,
                    { context, args }
                );
                
                logError(studioError, context);
                throw studioError;
            }
        };
    }
    
    /**
     * Async function wrapper
     * @param {Function} fn - Async function to wrap
     * @param {string} context - Error context
     * @returns {Function} Wrapped async function
     */
    function wrapAsyncWithErrorHandler(fn, context = '') {
        return async function(...args) {
            try {
                return await fn.apply(this, args);
            } catch (error) {
                const studioError = new StudioError(
                    error.message,
                    ErrorTypes.RUNTIME,
                    ErrorLevels.ERROR,
                    { context, args }
                );
                
                logError(studioError, context);
                throw studioError;
            }
        };
    }
    
    /**
     * Widget retry functionality
     * @param {string} widgetId - Widget ID
     */
    function retryWidget(widgetId) {
        if (window.studioLoadWidget) {
            // Remove error display
            const container = document.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
            if (container) {
                container.innerHTML = '<div class="widget-loading"><i class="fa fa-spinner fa-spin"></i> Yeniden yükleniyor...</div>';
            }
            
            // Retry widget loading
            setTimeout(() => {
                window.studioLoadWidget(widgetId);
            }, 1000);
        }
    }
    
    /**
     * Get error statistics
     * @returns {Object} Error statistics
     */
    function getErrorStats() {
        const stats = {
            total: errorLog.length,
            byType: {},
            byLevel: {},
            recent: errorLog.slice(-10)
        };
        
        errorLog.forEach(error => {
            stats.byType[error.type] = (stats.byType[error.type] || 0) + 1;
            stats.byLevel[error.level] = (stats.byLevel[error.level] || 0) + 1;
        });
        
        return stats;
    }
    
    /**
     * Clear error log
     */
    function clearErrorLog() {
        errorLog.length = 0;
        errorCounts.clear();
    }
    
    // Setup global error handler
    setupGlobalErrorHandler();
    
    // Expose retry function globally
    window.studioRetryWidget = retryWidget;
    
    // Public API
    return {
        // Error types and levels
        ErrorTypes: ErrorTypes,
        ErrorLevels: ErrorLevels,
        
        // Error class
        StudioError: StudioError,
        
        // Error logging
        logError: logError,
        
        // Specific error handlers
        handleWidgetError: handleWidgetError,
        handleNetworkError: handleNetworkError,
        handleValidationError: handleValidationError,
        
        // Function wrappers
        wrap: wrapWithErrorHandler,
        wrapAsync: wrapAsyncWithErrorHandler,
        
        // Retry functionality
        retryWidget: retryWidget,
        
        // Error statistics
        getErrorStats: getErrorStats,
        getErrorLog: () => [...errorLog],
        clearErrorLog: clearErrorLog
    };
})();