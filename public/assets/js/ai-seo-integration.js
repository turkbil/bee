/**
 * AI SEO Integration System
 * Real AI-powered SEO functionality
 */

(function() {
    'use strict';
    
    // CSRF token for API calls
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    function attachButtonListeners() {
        const seoButtons = document.querySelectorAll('.ai-seo-comprehensive-btn, .seo-generator-btn, .seo-suggestions-btn, [data-seo-feature], [data-action]');
        
        seoButtons.forEach((button) => {
            // Remove existing listeners
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Check button type and handle accordingly
                if (this.classList.contains('ai-seo-comprehensive-btn') || 
                    this.getAttribute('data-seo-feature') === 'seo-comprehensive-audit') {
                    handleSeoAnalysis(this);
                    return;
                }
                
                if (this.classList.contains('seo-generator-btn') || 
                    this.getAttribute('data-action') === 'generate-seo') {
                    handleSeoGenerate(this);
                    return;
                }
                
                if (this.classList.contains('seo-suggestions-btn') || 
                    this.getAttribute('data-action') === 'get-suggestions') {
                    handleSeoSuggestions(this);
                    return;
                }
            });
        });
    }
    
    // Real AI API handlers
    async function handleSeoAnalysis(button) {
        console.log('🚀 SEO ANALYSIS START');
        try {
            setButtonLoading(button, 'Analiz Ediliyor...');
            
            const collectedData = collectFormData();
            console.log('🚨 DEBUG CHECKPOINT 1: collectFormData called');
            const formData = {
                feature_slug: 'seo-comprehensive-audit',
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            console.log('🚨 DEBUG CHECKPOINT 2: collected data keys:', Object.keys(collectedData));
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/analyze');
            const response = await fetch('/admin/seo/ai/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success result:', result);
                // FULL RESPONSE'u kaydet - detailed_scores root level'de!
                window.lastSeoResponse = result;
                console.log('💾 SAVED TO WINDOW:', window.lastSeoResponse);
                displayComprehensiveAnalysis(result.data);
            } else {
                console.error('❌ API Error:', result.message);
                showError('Analiz sırasında hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            resetButton(button, '<i class="fas fa-chart-bar me-1"></i>SEO Analizi');
        }
    }
    
    async function handleSeoGenerate(button) {
        console.log('🚀 SEO GENERATE START');
        try {
            setButtonLoading(button, 'Oluşturuluyor...');
            
            const collectedData = collectFormData();
            const formData = {
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/generate');
            const response = await fetch('/admin/seo/ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success - updating fields:', result.data);
                updateFormFields(result.data);
                showSuccess('SEO içeriği başarıyla oluşturuldu!');
            } else {
                console.error('❌ API Error:', result.message);
                showError('Oluşturma sırasında hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            resetButton(button, '<i class="fas fa-magic me-1"></i>SEO Oluştur');
        }
    }
    
    async function handleSeoSuggestions(button) {
        console.log('🚀 SEO SUGGESTIONS START');
        try {
            setButtonLoading(button, 'Öneriler Alınıyor...');
            
            const collectedData = collectFormData();
            const formData = {
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/suggestions');
            const response = await fetch('/admin/seo/ai/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success - displaying suggestions:', result.data);
                displaySuggestions(result.data);
            } else {
                console.error('❌ API Error:', result.message);
                showError('Öneri alınırken hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            resetButton(button, '<i class="fas fa-lightbulb me-1"></i>Öneriler');
        }
    }
    
    // Utility functions
    function collectFormData() {
        console.log('🔍 collectFormData() BAŞLADI');
        const data = {};
        
        // Collect Livewire model data
        const livewireInputs = document.querySelectorAll('[wire\\:model]');
        console.log('🔍 Bulunan Livewire input sayısı:', livewireInputs.length);
        
        livewireInputs.forEach(input => {
            const model = input.getAttribute('wire:model');
            if (model) {
                // Include all fields, even empty ones - important for SEO scoring
                data[model] = input.value || '';
                console.log('📝 Field eklendi:', model, '=', input.value || 'BOŞ');
            }
        });
        
        // Collect QuillHtml/Hugerte content - include empty editors too
        const quillEditors = document.querySelectorAll('.ql-editor');
        console.log('🔍 Bulunan Quill editor sayısı:', quillEditors.length);
        
        quillEditors.forEach(editor => {
            const parentContainer = editor.closest('[wire\\:ignore]') || editor.closest('.quill-container');
            if (parentContainer) {
                const wireModelElement = parentContainer.querySelector('[wire\\:model]');
                if (wireModelElement) {
                    const model = wireModelElement.getAttribute('wire:model');
                    if (model) {
                        // Include all editors, even empty ones - important for SEO scoring
                        data[model] = editor.innerHTML || '';
                        console.log('📝 Editor eklendi:', model, '=', (editor.innerHTML || 'BOŞ').substring(0, 50));
                    }
                }
            }
        });
        
        // DEBUG: Form field mapping - logda hangi fieldlar var görelim
        console.log('🔍 COLLECTED FORM DATA:', data);
        console.log('🔍 Form data keys:', Object.keys(data));
        
        // Backend için mapping - logda görülen field yapısı
        const mappedData = {};
        Object.keys(data).forEach(key => {
            mappedData[key] = data[key];
            
            // SEO field mapping for backend compatibility
            if (key.includes('multiLangInputs.tr.title')) {
                mappedData['title'] = data[key]; // Backend'de title arıyor
                console.log('✅ MAPPING: title =', data[key]);
            }
            if (key.includes('seoDataCache.tr.seo_description')) {
                mappedData['meta_description'] = data[key]; // Backend'de meta_description arıyor
                console.log('✅ MAPPING: meta_description =', data[key]);
            }
        });
        
        // Add current page context
        mappedData.current_url = window.location.href;
        mappedData.language = document.documentElement.lang || 'tr';
        
        console.log('🔍 MAPPED DATA FOR BACKEND:', mappedData);
        return mappedData;
    }
    
    function setButtonLoading(button, text) {
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${text}`;
        button.disabled = true;
    }
    
    function resetButton(button, originalHtml) {
        button.innerHTML = originalHtml;
        button.disabled = false;
    }
    
    // YENİ KOMPREHENSİF ANALİZ EKRANI
    function displayComprehensiveAnalysis(analysis) {
        console.log('🔍 COMPREHENSIVE ANALYSIS DEBUG START');
        console.log('📄 Full analysis object:', analysis);
        console.log('📄 analysis.data:', analysis.data);
        console.log('📄 analysis.detailed_scores:', analysis.detailed_scores);
        console.log('📄 analysis.metrics:', analysis.metrics);
        
        let panel = document.getElementById('seo-analysis-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-analysis-results-panel';
            panel.className = 'card mt-3';
            
            const buttonRow = document.querySelector('.ai-seo-comprehensive-btn').parentElement;
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            }
        }
        
        // Veri yapısını düzelt - analysis.data içinde asıl veriler var
        const actualData = analysis.data || analysis;
        console.log('🔧 Using actualData:', actualData);
        
        const score = actualData.overall_score || analysis.metrics?.overall_score || 0;
        let badgeClass = score >= 80 ? 'bg-success' : score >= 60 ? 'bg-warning' : 'bg-danger';
        
        panel.innerHTML = `
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Kapsamlı SEO Analizi
                </h3>
            </div>
            <div class="card-body">
                <!-- GENEL SKOR -->
                <div class="row mb-4">
                    <div class="col-auto">
                        <div class="avatar avatar-xl ${badgeClass} text-white" style="font-size: 1.5rem; font-weight: bold;">
                            ${score}
                        </div>
                    </div>
                    <div class="col">
                        <h4>Genel SEO Skoru</h4>
                        <p class="text-secondary">${actualData.health_status || analysis.metrics?.health_status}</p>
                    </div>
                </div>
                
                <!-- SKOR DETAYLARI -->
                <div class="row g-3 mb-4">
                    ${['title', 'description', 'content', 'technical', 'social', 'priority'].map(key => {
                        const detailedScores = actualData.detailed_scores || analysis.detailed_scores || {};
                        const scoreData = detailedScores[key] || {};
                        const val = scoreData.score || scoreData.overall_score || 0;
                        const cls = val >= 80 ? 'success' : val >= 60 ? 'warning' : 'danger';
                        return `
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">${key.replace('_score', '').toUpperCase()}</div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-${cls}" style="width: ${val}%"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2 text-${cls}">${val}/100</div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }).join('')}
                </div>
                
                <!-- OLUMLU YANLAR -->
                <div class="mb-4">
                    <h5 class="text-success"><i class="fas fa-check-circle me-2"></i>Güçlü Yanlar</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.strengths && actualData.strengths.length) ? actualData.strengths.map(item => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <i class="fas fa-plus-circle text-success me-2"></i>${typeof item === 'string' ? item : (item.text || item.title || item.description || JSON.stringify(item))}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - strengths eksik</div>'}
                    </div>
                </div>
                
                <!-- İYİLEŞTİRME ÖNERİLERİ -->
                <div class="mb-4">
                    <h5 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>İyileştirme Alanları</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.improvements && actualData.improvements.length) ? actualData.improvements.map(item => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <i class="fas fa-arrow-up text-warning me-2"></i>${typeof item === 'string' ? item : (item.text || item.title || item.description || JSON.stringify(item))}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - improvements eksik</div>'}
                    </div>
                </div>
                
                <!-- EYLEM ÖNERİLERİ -->
                <div>
                    <h5 class="text-primary"><i class="fas fa-tasks me-2"></i>Öncelikli Eylemler</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.action_items && actualData.action_items.length) ? actualData.action_items.map((item, i) => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <span class="badge bg-primary me-2">${i+1}</span>
                                <strong>${typeof item === 'string' ? item : (item.task || item.text || item.title || item.description || 'Eylem tanımı bulunamadı')}</strong>
                                ${typeof item === 'object' && item.urgency ? `<span class="badge bg-danger ms-2">${item.urgency}</span>` : ''}
                                ${typeof item === 'object' && item.area ? `<br><small class="text-muted">Alan: ${item.area}</small>` : ''}
                                ${typeof item === 'object' && item.expected_impact ? `<small class="text-muted"> • Etki: ${item.expected_impact}</small>` : ''}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - action_items eksik</div>'}
                    </div>
                </div>
            </div>
        `;
    }
    
    function updateFormFields(data) {
        console.log('📝 GENERATE Results data:', data);
        
        // Create UNIQUE panel for generate
        let panel = document.getElementById('seo-generate-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-generate-results-panel';
            panel.className = 'card mt-3';
            
            // Find the button row and put panel right after it
            const buttonRow = document.querySelector('.row .col-12:has(.seo-generator-btn)') ||
                             document.querySelector('.row:has(.seo-generator-btn)') ||
                             document.querySelector('.seo-generator-btn').closest('.row') ||
                             document.querySelector('.seo-generator-btn').parentElement;
            
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            } else {
                // Fallback to button parent
                const anyButton = document.querySelector('.seo-generator-btn, .ai-seo-comprehensive-btn, .seo-suggestions-btn');
                if (anyButton) {
                    anyButton.parentElement.insertAdjacentElement('afterend', panel);
                }
            }
        }
        
        // Professional Generate Panel
        let html = `
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-magic me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l2 0l10 -10l-2 -2l-10 10l0 2z"/>
                        <path d="M19 4l2 2"/>
                        <path d="M9 7h4"/>
                        <path d="M9 11h4"/>
                    </svg>
                    SEO İçerik Oluşturma
                </h3>
            </div>
            <div class="card-body">`;
        
        if (data && (data.meta_title || data.meta_description || data.generated_content)) {
            html += `<div class="row g-3">`;
            
            if (data.meta_title) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan Meta Title</div>
                        <div class="input-group">
                            <input type="text" class="form-control" value="${data.meta_title}" readonly>
                            <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText('${data.meta_title}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>`;
            }
            
            if (data.meta_description) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan Meta Description</div>
                        <div class="input-group">
                            <textarea class="form-control" rows="3" readonly>${data.meta_description}</textarea>
                            <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText('${data.meta_description}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>`;
            }
            
            if (data.generated_content) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan İçerik</div>
                        <div class="card">
                            <div class="card-body">
                                ${typeof data.generated_content === 'object' ? JSON.stringify(data.generated_content, null, 2) : data.generated_content}
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${typeof data.generated_content === 'object' ? JSON.stringify(data.generated_content, null, 2) : data.generated_content}')">
                                    <i class="fas fa-copy me-1"></i>Kopyala
                                </button>
                            </div>
                        </div>
                    </div>`;
            }
            
            html += `</div>`;
        } else {
            html += `
                <div class="empty">
                    <div class="empty-img"><img src="./static/illustrations/undraw_printing_invoices_5r4r.svg" height="128" alt="">
                    </div>
                    <p class="empty-title">İçerik oluşturulamadı</p>
                    <p class="empty-subtitle text-secondary">
                        AI içerik oluşturma işlemi tamamlanamadı veya beklenmedik bir hata oluştu.
                    </p>
                </div>`;
        }
        
        html += `</div>`;
        panel.innerHTML = html;
        console.log('✅ Professional Generate Results shown');
    }
    
    function displaySuggestions(data) {
        console.log('🎯 SUGGESTIONS Results data:', data);
        console.log('🔍 SUGGESTIONS DATA STRUCTURE CHECK:');
        console.log('  data.suggestions:', data.suggestions);
        console.log('  data.suggestions type:', typeof data.suggestions);
        if (data.suggestions && typeof data.suggestions === 'object') {
            console.log('  data.suggestions keys:', Object.keys(data.suggestions));
            console.log('  data.suggestions.title_suggestions:', data.suggestions.title_suggestions);
            console.log('  data.suggestions.description_suggestions:', data.suggestions.description_suggestions);
        }
        
        // Create UNIQUE panel for suggestions
        let panel = document.getElementById('seo-suggestions-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-suggestions-results-panel';
            panel.className = 'card mt-3';
            
            // Find the button row and put panel right after it
            const buttonRow = document.querySelector('.row .col-12:has(.seo-suggestions-btn)') ||
                             document.querySelector('.row:has(.seo-suggestions-btn)') ||
                             document.querySelector('.seo-suggestions-btn').closest('.row') ||
                             document.querySelector('.seo-suggestions-btn').parentElement;
            
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            } else {
                // Fallback to button parent
                const anyButton = document.querySelector('.seo-suggestions-btn, .ai-seo-comprehensive-btn, .seo-generator-btn');
                if (anyButton) {
                    anyButton.parentElement.insertAdjacentElement('afterend', panel);
                }
            }
        }
        
        // Professional Suggestions Panel
        let html = `
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lightbulb me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 21h6"/>
                        <path d="M6 21v-1a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v1"/>
                        <path d="M12 17v-11"/>
                        <path d="M12 6a4 4 0 1 0 4 4"/>
                    </svg>
                    SEO Önerileri
                </h3>
            </div>
            <div class="card-body">`;
        
        // SUGGESTIONS YAPISINI PARSE ET - backend'den obje olarak geliyor
        let hasContent = false;
        
        if (data && data.suggestions && typeof data.suggestions === 'object') {
            html += `<div class="row g-3">`;
            
            // Title Suggestions
            if (data.suggestions.title_suggestions && Array.isArray(data.suggestions.title_suggestions)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-heading me-2"></i>Title Önerileri</h5>
                        <div class="list-group">`;
                data.suggestions.title_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-blue me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            // Description Suggestions  
            if (data.suggestions.description_suggestions && Array.isArray(data.suggestions.description_suggestions)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-file-text me-2"></i>Description Önerileri</h5>
                        <div class="list-group">`;
                data.suggestions.description_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-green me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            // Content Improvements
            if (data.suggestions.content_improvements && Array.isArray(data.suggestions.content_improvements)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-tools me-2"></i>İçerik İyileştirmeleri</h5>
                        <div class="list-group">`;
                data.suggestions.content_improvements.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-orange me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            html += `</div>`;
        }
        
        if (!hasContent) {
            html += `
                <div class="empty">
                    <div class="empty-img"><img src="./static/illustrations/undraw_printing_invoices_5r4r.svg" height="128" alt="">
                    </div>
                    <p class="empty-title">Öneri bulunamadı</p>
                    <p class="empty-subtitle text-secondary">
                        AI önerileri alınamadı veya beklenmedik bir hata oluştu.
                    </p>
                </div>`;
        }
        
        html += `</div>`;
        panel.innerHTML = html;
        console.log('✅ Professional Suggestions shown');
    }
    
    function showSuccess(message) {
        console.log('✅ SUCCESS:', message);
    }
    
    function showError(message) {
        console.error('❌ ERROR:', message);
    }

    // Save SEO Content to Database
    window.saveSeoToDatabase = function(type, data) {
        console.log('💾 Saving SEO data to database:', type, data);
        
        // Get current page/model info
        const currentUrl = window.location.pathname;
        const modelMatch = currentUrl.match(/\/admin\/(\w+)\/manage\/(\d+)/);
        
        if (!modelMatch) {
            showError('Model bilgisi alınamadı');
            return;
        }
        
        const modelType = modelMatch[1];
        const modelId = modelMatch[2];
        
        // Prepare save data
        const saveData = {
            model_type: modelType,
            model_id: modelId,
            language: 'tr',
            data: data,
            type: type
        };
        
        // Save to database via AJAX
        fetch('/admin/seo/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(saveData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccess('SEO verileri kaydedildi');
                
                // Update form fields if needed
                if (data.meta_title) {
                    const titleField = document.querySelector('input[name="seoDataCache[tr][seo_title]"]');
                    if (titleField) titleField.value = data.meta_title;
                }
                
                if (data.meta_description) {
                    const descField = document.querySelector('textarea[name="seoDataCache[tr][seo_description]"]');
                    if (descField) descField.value = data.meta_description;
                }
            } else {
                showError('Kayıt başarısız: ' + (result.error || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showError('Kayıt hatası: ' + error.message);
        });
    };
    
    // Selection UI for Suggestions
    window.applySeoSuggestion = function(field, value) {
        console.log('✅ Applying suggestion:', field, value);
        
        if (field === 'title') {
            const titleField = document.querySelector('input[name="seoDataCache[tr][seo_title]"]');
            if (titleField) {
                titleField.value = value;
                showSuccess('Title güncellendi');
            }
        } else if (field === 'description') {
            const descField = document.querySelector('textarea[name="seoDataCache[tr][seo_description]"]');
            if (descField) {
                descField.value = value;
                showSuccess('Description güncellendi');
            }
        }
    };
    
    // Initialize the system
    function init() {
        console.log('🚀 AI SEO Integration system başlatılıyor...');
        
        // Immediate attachment
        attachButtonListeners();
        
        // DOM ready fallback
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attachButtonListeners);
        }
        
        // Delayed fallback for dynamic content
        setTimeout(attachButtonListeners, 500);
        
        // Window load fallback
        window.addEventListener('load', attachButtonListeners);
        
        console.log('✅ AI SEO Integration system hazır!');
    }
    
    // Start the system
    init();
    
})();