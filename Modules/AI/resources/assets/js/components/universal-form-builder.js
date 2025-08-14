/**
 * Universal AI Form Builder JavaScript Module - ENTERPRISE V3.0.0
 * 
 * Advanced form handling, validation, and interaction management
 * Works with Alpine.js and the Universal Input System
 * 
 * Features:
 * - Real-time form validation
 * - Context-aware field interactions
 * - Dynamic form field generation
 * - Progress tracking and analytics
 * - Error handling and recovery
 * - Performance monitoring
 * - Accessibility support
 * 
 * @version 3.0.0
 * @author AI Module Team
 */

document.addEventListener('alpine:init', () => {
    /**
     * Main Universal Form Builder Alpine.js Component
     */
    Alpine.data('universalFormBuilder', (config = {}) => ({
        // Configuration
        feature: config.feature || '',
        formConfig: config.config || {},
        contextData: config.context || {},
        
        // Form State
        isSubmitting: false,
        isFormValid: true,
        submitProgress: 0,
        submitStatusText: '',
        estimatedTime: 5,
        fieldValidationStatus: 'Not validated',
        
        // Analytics & Performance
        startTime: null,
        interactionCount: 0,
        validationErrors: [],
        performanceMetrics: {},
        
        // Advanced Features
        autoSaveEnabled: true,
        autoSaveInterval: null,
        lastAutoSave: null,
        formHistory: [],
        undoStack: [],
        redoStack: [],
        
        // Accessibility
        announcements: [],
        focusTracker: null,
        keyboardNavigation: true,
        
        /**
         * Initialize the form builder
         */
        init() {
            this.startTime = Date.now();
            this.calculateEstimatedTime();
            this.setupValidation();
            this.initializeAccessibility();
            this.setupAutoSave();
            this.setupPerformanceTracking();
            this.setupKeyboardHandlers();
            
            // Custom initialization
            this.$dispatch('form-builder-initialized', {
                feature: this.feature,
                config: this.formConfig
            });
            
            console.log('Universal Form Builder initialized:', {
                feature: this.feature,
                fields: this.countFormFields(),
                context: Object.keys(this.contextData).length
            });
        },
        
        /**
         * Calculate estimated completion time based on form complexity
         */
        calculateEstimatedTime() {
            const fieldCount = this.countFormFields();
            const complexity = this.formConfig.complexity || 'medium';
            const hasAdvancedOptions = this.formConfig.advanced_options?.length > 0;
            
            const baseTime = {
                'simple': 2,
                'medium': 5,
                'complex': 10,
                'enterprise': 15
            };
            
            let time = baseTime[complexity] || 5;
            time += Math.ceil(fieldCount / 3);
            
            if (hasAdvancedOptions) {
                time += 3;
            }
            
            // Context bonus/penalty
            const contextQuality = this.calculateContextQuality();
            if (contextQuality > 80) time *= 0.8; // Good context reduces time
            if (contextQuality < 40) time *= 1.3; // Poor context increases time
            
            this.estimatedTime = Math.max(2, Math.ceil(time));
        },
        
        /**
         * Count total form fields
         */
        countFormFields() {
            let count = 0;
            
            if (this.formConfig.groups) {
                this.formConfig.groups.forEach(group => {
                    count += group.fields ? group.fields.length : 0;
                });
            }
            
            if (this.formConfig.fields) {
                count += this.formConfig.fields.length;
            }
            
            if (this.formConfig.advanced_options) {
                count += this.formConfig.advanced_options.length;
            }
            
            return count;
        },
        
        /**
         * Calculate context quality score
         */
        calculateContextQuality() {
            let score = 0;
            const maxScore = 100;
            
            if (Object.keys(this.contextData).length === 0) return 0;
            
            // User context (25 points)
            if (this.contextData.user) {
                score += 25;
                if (this.contextData.user.preferences) score += 5;
            }
            
            // Module context (20 points)
            if (this.contextData.module) score += 20;
            
            // Time context (15 points)
            if (this.contextData.time) score += 15;
            
            // Tenant context (15 points)
            if (this.contextData.tenant) score += 15;
            
            // Content context (20 points)
            if (this.contextData.content) {
                score += 20;
                if (this.contextData.content.metadata) score += 5;
            }
            
            return Math.min(score, maxScore);
        },
        
        /**
         * Setup form validation
         */
        setupValidation() {
            this.$nextTick(() => {
                const form = this.$el.querySelector('form');
                if (form) {
                    // Real-time validation
                    form.addEventListener('input', this.debounce((e) => {
                        this.validateField(e.target);
                        this.updateValidationStatus();
                    }, 300));
                    
                    form.addEventListener('change', (e) => {
                        this.validateField(e.target);
                        this.updateValidationStatus();
                    });
                    
                    // Initial validation
                    this.validateAllFields();
                }
            });
        },
        
        /**
         * Validate individual field
         */
        validateField(field) {
            if (!field || !field.name) return true;
            
            const fieldName = field.name;
            const value = field.value;
            const fieldType = field.type;
            
            // Remove previous errors for this field
            this.validationErrors = this.validationErrors.filter(error => error.field !== fieldName);
            
            // Required field validation
            if (field.hasAttribute('required') && (!value || value.trim() === '')) {
                this.addValidationError(fieldName, 'Bu alan zorunludur');
                return false;
            }
            
            // Type-specific validation
            if (value && value.trim() !== '') {
                switch (fieldType) {
                    case 'email':
                        if (!this.isValidEmail(value)) {
                            this.addValidationError(fieldName, 'Geçerli bir e-posta adresi giriniz');
                            return false;
                        }
                        break;
                        
                    case 'url':
                        if (!this.isValidUrl(value)) {
                            this.addValidationError(fieldName, 'Geçerli bir URL giriniz');
                            return false;
                        }
                        break;
                        
                    case 'number':
                        const min = field.getAttribute('min');
                        const max = field.getAttribute('max');
                        const numValue = parseFloat(value);
                        
                        if (isNaN(numValue)) {
                            this.addValidationError(fieldName, 'Geçerli bir say1 giriniz');
                            return false;
                        }
                        
                        if (min && numValue < parseFloat(min)) {
                            this.addValidationError(fieldName, `Minimum deer: ${min}`);
                            return false;
                        }
                        
                        if (max && numValue > parseFloat(max)) {
                            this.addValidationError(fieldName, `Maksimum deer: ${max}`);
                            return false;
                        }
                        break;
                }
            }
            
            // Custom validation rules
            const customRules = this.getFieldValidationRules(fieldName);
            if (customRules) {
                for (const rule of customRules) {
                    if (!this.validateCustomRule(value, rule)) {
                        this.addValidationError(fieldName, rule.message || 'Geçersiz deer');
                        return false;
                    }
                }
            }
            
            return true;
        },
        
        /**
         * Add validation error
         */
        addValidationError(fieldName, message) {
            this.validationErrors.push({
                field: fieldName,
                message: message,
                timestamp: Date.now()
            });
        },
        
        /**
         * Get custom validation rules for field
         */
        getFieldValidationRules(fieldName) {
            // Implementation would get rules from form config
            return null;
        },
        
        /**
         * Validate custom rule
         */
        validateCustomRule(value, rule) {
            switch (rule.type) {
                case 'regex':
                    return new RegExp(rule.pattern).test(value);
                case 'length':
                    return value.length >= (rule.min || 0) && value.length <= (rule.max || Infinity);
                case 'custom':
                    return rule.validator ? rule.validator(value) : true;
                default:
                    return true;
            }
        },
        
        /**
         * Validate all form fields
         */
        validateAllFields() {
            const form = this.$el.querySelector('form');
            if (!form) return;
            
            const fields = form.querySelectorAll('input, select, textarea');
            let allValid = true;
            
            fields.forEach(field => {
                if (!this.validateField(field)) {
                    allValid = false;
                }
            });
            
            this.isFormValid = allValid;
            this.updateValidationStatus();
            
            return allValid;
        },
        
        /**
         * Update validation status display
         */
        updateValidationStatus() {
            const errorCount = this.validationErrors.length;
            const fieldCount = this.countFormFields();
            
            if (errorCount === 0) {
                this.fieldValidationStatus = `All ${fieldCount} fields valid`;
                this.isFormValid = true;
            } else {
                this.fieldValidationStatus = `${errorCount} validation error${errorCount > 1 ? 's' : ''}`;
                this.isFormValid = false;
            }
        },
        
        /**
         * Handle form submission
         */
        async handleSubmit(event) {
            event.preventDefault();
            
            // Prevent double submission
            if (this.isSubmitting) return;
            
            // Final validation
            if (!this.validateAllFields()) {
                this.showValidationErrors();
                this.announceToScreenReader('Form has validation errors. Please check and try again.');
                return;
            }
            
            this.isSubmitting = true;
            this.submitProgress = 0;
            this.submitStatusText = 'Initializing...';
            
            try {
                // Prepare form data
                const formData = this.prepareFormData(event.target);
                
                // Add context and metadata
                formData.append('context', JSON.stringify(this.contextData));
                formData.append('feature', this.feature);
                formData.append('performance_metrics', JSON.stringify(this.performanceMetrics));
                formData.append('interaction_count', this.interactionCount.toString());
                formData.append('form_completion_time', (Date.now() - this.startTime).toString());
                
                // Submit with progress tracking
                const response = await this.submitFormWithProgress(formData, event.target.action);
                
                // Handle success
                this.handleSubmissionSuccess(response);
                
            } catch (error) {
                console.error('Form submission error:', error);
                this.handleSubmissionError(error);
            } finally {
                // Reset submission state after delay
                setTimeout(() => {
                    this.isSubmitting = false;
                    this.submitProgress = 0;
                    this.submitStatusText = '';
                }, 2000);
            }
        },
        
        /**
         * Prepare form data for submission
         */
        prepareFormData(form) {
            const formData = new FormData(form);
            
            // Add any additional data processing here
            // e.g., file processing, data transformation
            
            return formData;
        },
        
        /**
         * Submit form with progress tracking
         */
        async submitFormWithProgress(formData, actionUrl) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                
                // Upload progress
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percentComplete = (event.loaded / event.total) * 70; // Reserve 30% for processing
                        this.updateProgress(percentComplete, 'Uploading...');
                    }
                };
                
                // State changes
                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4) {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            this.updateProgress(100, 'Complete!');
                            resolve(JSON.parse(xhr.responseText));
                        } else {
                            reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                        }
                    }
                };
                
                // Error handling
                xhr.onerror = () => {
                    reject(new Error('Network error occurred'));
                };
                
                xhr.ontimeout = () => {
                    reject(new Error('Request timeout'));
                };
                
                // Configure and send
                xhr.open('POST', actionUrl);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.timeout = 300000; // 5 minutes
                
                // Progress simulation for processing phase
                const startProcessing = () => {
                    this.updateProgress(70, 'Processing request...');
                    
                    setTimeout(() => {
                        this.updateProgress(85, 'Generating AI response...');
                    }, 1000);
                    
                    setTimeout(() => {
                        this.updateProgress(95, 'Finalizing...');
                    }, 2000);
                };
                
                xhr.onloadstart = startProcessing;
                xhr.send(formData);
            });
        },
        
        /**
         * Update submission progress
         */
        updateProgress(percent, status) {
            this.submitProgress = Math.min(100, Math.max(0, percent));
            this.submitStatusText = status;
            
            // Accessibility announcement for major milestones
            if (percent >= 25 && percent % 25 === 0) {
                this.announceToScreenReader(`Progress: ${percent}% - ${status}`);
            }
        },
        
        /**
         * Handle successful submission
         */
        handleSubmissionSuccess(response) {
            // Clear auto-save data
            this.clearAutoSave();
            
            // Show success message
            this.showNotification(response.message || 'AI generation completed successfully!', 'success');
            
            // Track success metrics
            this.trackSubmissionSuccess(response);
            
            // Handle response actions
            if (response.redirect) {
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            } else if (response.result) {
                this.displayResult(response.result);
            }
            
            // Dispatch success event
            this.$dispatch('form-submission-success', {
                response: response,
                metrics: this.performanceMetrics
            });
        },
        
        /**
         * Handle submission error
         */
        handleSubmissionError(error) {
            const message = error.message || 'An error occurred during processing';
            
            this.showNotification(message, 'error');
            this.announceToScreenReader(`Error: ${message}`);
            
            // Track error metrics
            this.trackSubmissionError(error);
            
            // Dispatch error event
            this.$dispatch('form-submission-error', {
                error: error,
                metrics: this.performanceMetrics
            });
        },
        
        /**
         * Show validation errors
         */
        showValidationErrors() {
            const form = this.$el.querySelector('form');
            if (form) {
                form.classList.add('was-validated');
                
                // Focus first invalid field
                const firstInvalidField = form.querySelector('.is-invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        },
        
        /**
         * Preview functionality
         */
        async handlePreview() {
            if (!this.validateAllFields()) {
                this.showValidationErrors();
                return;
            }
            
            this.announceToScreenReader('Generating preview...');
            
            // Implementation would show preview modal or panel
            console.log('Preview functionality would be implemented here');
            
            this.$dispatch('form-preview-requested', {
                formData: this.getFormData(),
                feature: this.feature
            });
        },
        
        /**
         * Save draft functionality
         */
        async handleSaveDraft() {
            const formData = this.getFormData();
            
            try {
                // Save to localStorage
                const draftKey = `ai_draft_${this.feature}_${Date.now()}`;
                localStorage.setItem(draftKey, JSON.stringify({
                    formData: formData,
                    timestamp: Date.now(),
                    feature: this.feature,
                    contextData: this.contextData
                }));
                
                this.showNotification('Draft saved successfully!', 'info');
                this.announceToScreenReader('Draft saved');
                
                // Track draft save
                this.trackEvent('draft_saved');
                
            } catch (error) {
                console.error('Failed to save draft:', error);
                this.showNotification('Failed to save draft', 'error');
            }
        },
        
        /**
         * Get current form data
         */
        getFormData() {
            const form = this.$el.querySelector('form');
            if (!form) return {};
            
            const formData = new FormData(form);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    // Handle multiple values (e.g., checkboxes)
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }
            
            return data;
        },
        
        /**
         * Auto-save functionality
         */
        setupAutoSave() {
            if (!this.autoSaveEnabled) return;
            
            this.autoSaveInterval = setInterval(() => {
                this.performAutoSave();
            }, 30000); // Auto-save every 30 seconds
        },
        
        /**
         * Perform auto-save
         */
        performAutoSave() {
            if (this.isSubmitting) return;
            
            const formData = this.getFormData();
            
            // Only save if there's meaningful data
            if (Object.keys(formData).length === 0) return;
            
            const autoSaveKey = `ai_autosave_${this.feature}`;
            
            try {
                localStorage.setItem(autoSaveKey, JSON.stringify({
                    formData: formData,
                    timestamp: Date.now(),
                    feature: this.feature
                }));
                
                this.lastAutoSave = Date.now();
                
            } catch (error) {
                console.error('Auto-save failed:', error);
            }
        },
        
        /**
         * Clear auto-save data
         */
        clearAutoSave() {
            const autoSaveKey = `ai_autosave_${this.feature}`;
            localStorage.removeItem(autoSaveKey);
        },
        
        /**
         * Setup performance tracking
         */
        setupPerformanceTracking() {
            this.performanceMetrics = {
                startTime: this.startTime,
                loadTime: Date.now() - this.startTime,
                fieldCount: this.countFormFields(),
                contextQuality: this.calculateContextQuality(),
                estimatedTime: this.estimatedTime,
                userAgent: navigator.userAgent,
                screenSize: `${screen.width}x${screen.height}`,
                interactions: []
            };
        },
        
        /**
         * Track user interaction
         */
        trackInteraction(type, target, details = {}) {
            this.interactionCount++;
            
            const interaction = {
                type: type,
                target: target,
                timestamp: Date.now(),
                details: details
            };
            
            this.performanceMetrics.interactions.push(interaction);
            
            // Limit interaction history
            if (this.performanceMetrics.interactions.length > 100) {
                this.performanceMetrics.interactions.shift();
            }
        },
        
        /**
         * Track form submission success
         */
        trackSubmissionSuccess(response) {
            this.trackEvent('form_submission_success', {
                duration: Date.now() - this.startTime,
                interactionCount: this.interactionCount,
                fieldCount: this.countFormFields(),
                responseTime: response.processing_time || 0
            });
        },
        
        /**
         * Track form submission error
         */
        trackSubmissionError(error) {
            this.trackEvent('form_submission_error', {
                error: error.message,
                duration: Date.now() - this.startTime,
                interactionCount: this.interactionCount
            });
        },
        
        /**
         * Generic event tracking
         */
        trackEvent(eventName, data = {}) {
            const event = {
                name: eventName,
                timestamp: Date.now(),
                feature: this.feature,
                ...data
            };
            
            // Send to analytics service if available
            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track(eventName, event);
            }
            
            console.log('Event tracked:', event);
        },
        
        /**
         * Initialize accessibility features
         */
        initializeAccessibility() {
            // Setup ARIA live regions
            this.setupLiveRegions();
            
            // Setup focus management
            this.setupFocusManagement();
            
            // Setup keyboard navigation
            this.setupKeyboardNavigation();
        },
        
        /**
         * Setup ARIA live regions for announcements
         */
        setupLiveRegions() {
            let liveRegion = document.getElementById('ai-form-announcements');
            
            if (!liveRegion) {
                liveRegion = document.createElement('div');
                liveRegion.id = 'ai-form-announcements';
                liveRegion.setAttribute('aria-live', 'polite');
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.style.cssText = 'position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden;';
                document.body.appendChild(liveRegion);
            }
        },
        
        /**
         * Announce message to screen readers
         */
        announceToScreenReader(message) {
            const liveRegion = document.getElementById('ai-form-announcements');
            if (liveRegion) {
                liveRegion.textContent = message;
                
                // Clear after announcement
                setTimeout(() => {
                    liveRegion.textContent = '';
                }, 1000);
            }
        },
        
        /**
         * Setup focus management
         */
        setupFocusManagement() {
            this.focusTracker = {
                previousFocus: null,
                trapFocus: false
            };
            
            document.addEventListener('focusin', (e) => {
                this.focusTracker.previousFocus = e.target;
            });
        },
        
        /**
         * Setup keyboard navigation
         */
        setupKeyboardNavigation() {
            document.addEventListener('keydown', (e) => {
                this.handleKeyboardShortcuts(e);
            });
        },
        
        /**
         * Setup keyboard event handlers
         */
        setupKeyboardHandlers() {
            this.$el.addEventListener('keydown', (e) => {
                // Ctrl+S for save draft
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    this.handleSaveDraft();
                }
                
                // Ctrl+Enter for submit
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    const form = this.$el.querySelector('form');
                    if (form) {
                        this.handleSubmit({ preventDefault: () => {}, target: form });
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape' && this.isSubmitting) {
                    // Cancel submission if possible
                    this.announceToScreenReader('Submission cancelled');
                }
            });
        },
        
        /**
         * Handle keyboard shortcuts
         */
        handleKeyboardShortcuts(e) {
            // Alt+P for preview
            if (e.altKey && e.key === 'p') {
                e.preventDefault();
                this.handlePreview();
            }
            
            // Alt+V for validate
            if (e.altKey && e.key === 'v') {
                e.preventDefault();
                this.validateAllFields();
                this.announceToScreenReader(this.fieldValidationStatus);
            }
        },
        
        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            if (window.toast && typeof window.toast[type] === 'function') {
                window.toast[type](message);
            } else {
                // Fallback notification
                console.log(`${type.toUpperCase()}: ${message}`);
                alert(message);
            }
        },
        
        /**
         * Email validation
         */
        isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        
        /**
         * URL validation
         */
        isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch {
                return false;
            }
        },
        
        /**
         * Debounce utility
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        /**
         * Display AI generation result
         */
        displayResult(result) {
            // Implementation would depend on the specific feature
            console.log('AI Result:', result);
            
            // Dispatch event for custom result handling
            this.$dispatch('ai-result-received', {
                result: result,
                feature: this.feature
            });
        },
        
        /**
         * Check field conditional visibility
         */
        checkFieldCondition(fieldName, conditional) {
            if (!conditional || !conditional.field) return true;
            
            const dependentField = document.querySelector(`[name="${conditional.field}"]`);
            if (!dependentField) return true;
            
            const value = dependentField.value;
            const expectedValue = conditional.value;
            const operator = conditional.operator || 'equals';
            
            switch (operator) {
                case 'equals':
                    return value === expectedValue;
                case 'not_equals':
                    return value !== expectedValue;
                case 'contains':
                    return value.includes(expectedValue);
                case 'greater_than':
                    return parseFloat(value) > parseFloat(expectedValue);
                case 'less_than':
                    return parseFloat(value) < parseFloat(expectedValue);
                default:
                    return true;
            }
        },
        
        /**
         * Cleanup when component is destroyed
         */
        destroy() {
            // Clear intervals
            if (this.autoSaveInterval) {
                clearInterval(this.autoSaveInterval);
            }
            
            // Clear event listeners
            // (Alpine.js handles most cleanup automatically)
            
            // Final performance tracking
            this.trackEvent('form_component_destroyed', {
                duration: Date.now() - this.startTime,
                interactionCount: this.interactionCount
            });
        }
    }));
});

/**
 * Utility functions for form field conditions
 */
window.UniversalFormUtils = {
    /**
     * Check if field condition is met
     */
    checkFieldCondition(fieldName, conditional) {
        // This would be the same logic as in the Alpine component
        return true;
    }
};