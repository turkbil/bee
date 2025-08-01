/**
 * Global AI Widget System
 * Auto-loads when AI module is active
 * Works with any module context (page, portfolio, blog, etc.)
 */

// AI Widget Global Configuration
window.aiWidgetConfig = window.aiWidgetConfig || {};

// Initialize AI Widget System
document.addEventListener('DOMContentLoaded', function() {
    // console.log('🤖 Global AI Widget System başlatılıyor...');
    initializeAIWidgets();
});

/**
 * Initialize all AI Widgets on page
 */
function initializeAIWidgets() {
    const widgets = document.querySelectorAll('.ai-widget-container');
    
    widgets.forEach(widget => {
        const widgetId = widget.id;
        const context = widget.dataset.context || 'page';
        const entityId = widget.dataset.entityId || null;
        const entityType = widget.dataset.entityType || 'page';
        
        // Create widget configuration
        createWidgetConfig(widgetId, context, entityId, entityType);
        
        console.log('✅ AI Widget initialized:', widgetId, context);
    });
}

/**
 * Create widget configuration dynamically
 */
function createWidgetConfig(widgetId, context, entityId, entityType) {
    window.aiWidgetConfig[widgetId] = {
        widgetId: widgetId,
        context: context,
        entityId: entityId ? parseInt(entityId) : null,
        entityType: entityType,
        currentData: getCurrentPageData(),
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        apiEndpoints: {
            executeFeature: '/admin/ai/execute-widget-feature',
            sendMessage: '/admin/ai/send-message'
        }
    };
}

/**
 * Get current page data for AI context
 */
function getCurrentPageData() {
    const data = {};
    
    // Multi-language form data
    document.querySelectorAll('[wire\\:model^="multiLangInputs"]').forEach(input => {
        if (input.value) {
            const name = input.getAttribute('wire:model');
            const match = name.match(/multiLangInputs\.([^.]+)\.(.+)/);
            if (match) {
                const lang = match[1];
                const field = match[2];
                if (!data[lang]) data[lang] = {};
                data[lang][field] = input.value;
            }
        }
    });
    
    // Single language data
    document.querySelectorAll('[wire\\:model^="inputs"]').forEach(input => {
        if (input.value) {
            const name = input.getAttribute('wire:model');
            const field = name.replace('inputs.', '');
            data[field] = input.value;
        }
    });
    
    return data;
}

/**
 * Toggle AI Widget visibility
 */
window.toggleAIWidget = function(widgetId) {
    const content = document.getElementById('content-' + widgetId);
    const toggleBtn = document.querySelector(`#${widgetId} [onclick="toggleAIWidget('${widgetId}')"]`);
    
    if (!content || !toggleBtn) return;
    
    const icon = toggleBtn.querySelector('i');
    const text = toggleBtn.querySelector('span');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        if (icon) icon.className = 'fas fa-chevron-up';
        if (text) text.textContent = 'Küçült';
    } else {
        content.style.display = 'none';
        if (icon) icon.className = 'fas fa-chevron-down';
        if (text) text.textContent = 'Genişlet';
    }
};

/**
 * Execute AI Widget Feature
 */
window.executeAIWidgetFeature = function(widgetId, featureSlug, featureName) {
    // console.log('🚀 AI Widget Feature:', widgetId, featureSlug, featureName);
    
    const resultsContainer = document.getElementById('results-' + widgetId);
    const clearBtn = document.getElementById('clear-' + widgetId);
    const config = window.aiWidgetConfig[widgetId];
    
    if (!config) {
        console.error('❌ AI Widget config not found:', widgetId);
        console.log('Available configs:', Object.keys(window.aiWidgetConfig));
        return;
    }
    
    // Show loading state
    resultsContainer.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <h6 class="text-primary">${featureName}</h6>
            <small class="text-muted">AI analizi çalışıyor...</small>
        </div>
    `;
    
    // Show clear button
    if (clearBtn) clearBtn.style.display = 'block';
    
    // Execute feature
    executeAIFeatureAjax(widgetId, featureSlug, featureName, config, resultsContainer);
};

/**
 * Clear AI Widget Results
 */
window.clearAIWidgetResults = function(widgetId) {
    const resultsContainer = document.getElementById('results-' + widgetId);
    const clearBtn = document.getElementById('clear-' + widgetId);
    
    if (resultsContainer) {
        resultsContainer.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-robot fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">AI özelliklerinden birini seçin</p>
                <small>Sonuçlar burada görünecek</small>
            </div>
        `;
    }
    
    if (clearBtn) clearBtn.style.display = 'none';
};

/**
 * Execute AI Feature via AJAX
 */
function executeAIFeatureAjax(widgetId, featureSlug, featureName, config, resultsContainer) {
    const requestData = {
        feature_slug: featureSlug,
        context: config.context,
        entity_id: config.entityId,
        entity_type: config.entityType,
        current_data: getCurrentPageData() // Get fresh data
    };
    
    // console.log('🚀 AI Widget AJAX Request:', requestData);
    
    fetch(config.apiEndpoints.executeFeature, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': config.csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ AI Widget Response:', data);
        
        if (data.success) {
            // Success response
            resultsContainer.innerHTML = `
                <div class="card border-success">
                    <div class="card-header bg-success-subtle">
                        <h6 class="mb-0 text-success">
                            <i class="fas fa-check me-2"></i>${featureName} - Sonuçlar
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="ai-result-content" style="line-height: 1.6;">
                            ${data.formatted_response || data.response}
                        </div>
                        ${data.tokens_used ? `
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-coins me-1"></i>
                                    ${data.tokens_used} token kullanıldı
                                </small>
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                ${data.suggestions && Object.keys(data.suggestions).length > 0 ? `
                    <div class="card mt-3 border-warning">
                        <div class="card-header bg-warning-subtle">
                            <h6 class="mb-0 text-warning">
                                <i class="fas fa-lightbulb me-2"></i>Öneriler
                            </h6>
                        </div>
                        <div class="card-body">
                            ${Object.entries(data.suggestions).map(([key, value]) => `
                                <div class="d-flex align-items-center mb-2">
                                    <strong class="me-2">${key}:</strong>
                                    <span class="text-muted">${value}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
        } else {
            // Error response
            resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>AI Hatası:</strong> ${data.error || 'Bilinmeyen hata oluştu'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('❌ AI Widget AJAX Error:', error);
        resultsContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Bağlantı Hatası:</strong> ${error.message}
            </div>
        `;
    });
}