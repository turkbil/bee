// 🚀 AI İşlem Detay Tracking Sistemi
let aiProcessTracker = {
    currentProcess: null,
    usedTokens: 0,
    
    start: function(processName, estimatedTokens = 0) {
        console.log('🤖 AI Process Started:', processName);
        this.currentProcess = processName;
        this.usedTokens = estimatedTokens;
        
        const tracker = document.getElementById('aiProcessTracker');
        const step = document.getElementById('aiProcessStep');
        const credits = document.getElementById('aiProcessCredits');
        
        if (tracker && step && credits) {
            tracker.style.display = 'block';
            step.textContent = `🤖 ${processName}`;
            credits.innerHTML = `💎 Tahmini token: <span class="fw-bold">${estimatedTokens}</span>`;
        }
    },
    
    update: function(newStep, actualTokens = null) {
        console.log('🔄 AI Process Update:', newStep);
        const step = document.getElementById('aiProcessStep');
        const credits = document.getElementById('aiProcessCredits');
        
        if (step) step.textContent = `🔄 ${newStep}`;
        if (actualTokens && credits) {
            this.usedTokens = actualTokens;
            credits.innerHTML = `💎 Kullanılan token: <span class="fw-bold text-success">${actualTokens}</span>`;
        }
    },
    
    complete: function(result, totalTokens = null) {
        console.log('✅ AI Process Complete:', result);
        const tracker = document.getElementById('aiProcessTracker');
        const step = document.getElementById('aiProcessStep');
        const credits = document.getElementById('aiProcessCredits');
        
        if (step) step.textContent = `✅ ${result}`;
        if (totalTokens && credits) {
            this.usedTokens = totalTokens;
            credits.innerHTML = `💎 Toplam token: <span class="fw-bold text-primary">${totalTokens}</span>`;
        }
        
        // 3 saniye sonra gizle
        setTimeout(() => {
            if (tracker) tracker.style.display = 'none';
            this.currentProcess = null;
        }, 3000);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🤖 AI Assistant Panel Loading...');
    
    const aiToggleBtn = document.getElementById('aiToggleBtn');
    const aiPanel = document.getElementById('aiPanel');
    const aiCloseBtn = document.getElementById('aiCloseBtn');
    const aiChatMessages = document.getElementById('aiChatMessages');
    
    console.log('🤖 AI Elements:', {
        toggleBtn: !!aiToggleBtn,
        panel: !!aiPanel,
        closeBtn: !!aiCloseBtn,
        chatMessages: !!aiChatMessages
    });
    
    // DEBUG: Test butonunu ara
    const testButtons = document.querySelectorAll('[wire\\:click="testAI"]');
    console.log('🧪 TEST BUTONLARI BULUNDU:', testButtons.length, testButtons);
    
    // DEBUG: Tüm AI action card'larını say
    const actionCards = document.querySelectorAll('.ai-action-card');
    console.log('🎯 TOPLAM AI ACTION CARDS:', actionCards.length, actionCards);
    
    // DEBUG: Livewire component'ini kontrol et
    if (typeof Livewire !== 'undefined') {
        console.log('✅ LIVEWIRE YÜKLÜ!', Livewire);
    } else {
        console.log('❌ LIVEWIRE YOK!');
    }
    
    // Panel toggle
    if (aiToggleBtn && aiPanel) {
        aiToggleBtn.addEventListener('click', function() {
            if (aiPanel.style.display === 'none' || aiPanel.style.display === '') {
                aiPanel.style.display = 'block';
                aiPanel.style.animation = 'aiPanelSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            } else {
                aiPanel.style.animation = 'aiPanelSlideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                setTimeout(() => {
                    aiPanel.style.display = 'none';
                }, 300);
            }
        });
    }
    
    // Close panel
    if (aiCloseBtn && aiPanel) {
        aiCloseBtn.addEventListener('click', function() {
            aiPanel.style.display = 'none';
        });
    }
    
    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
        if (aiPanel && !event.target.closest('.ai-assistant-panel')) {
            aiPanel.style.display = 'none';
        }
    });
    
    // Auto-scroll chat messages
    function scrollChatToBottom() {
        if (aiChatMessages) {
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        }
    }
    
    // Add message to chat
    window.addAiMessage = function(message, isUser = false) {
        if (!aiChatMessages) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `ai-message ${isUser ? 'user' : 'assistant'}`;
        
        const now = new Date().toLocaleTimeString('tr-TR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageEl.innerHTML = `
            <div class="ai-message-avatar">
                <i class="fas fa-${isUser ? 'user' : 'robot'}"></i>
            </div>
            <div class="ai-message-content">
                <div class="ai-message-text">${message}</div>
                <div class="ai-message-time">${now}</div>
            </div>
        `;
        
        aiChatMessages.appendChild(messageEl);
        scrollChatToBottom();
    };
    
    // Show progress
    window.showAiProgress = function(text = 'AI işlemi devam ediyor...') {
        const progressEl = document.getElementById('aiProgress');
        if (progressEl) {
            const textEl = progressEl.querySelector('.ai-progress-text');
            if (textEl) textEl.textContent = text;
            progressEl.style.display = 'block';
        }
    };
    
    // Hide progress
    window.hideAiProgress = function() {
        const progressEl = document.getElementById('aiProgress');
        if (progressEl) progressEl.style.display = 'none';
    };
    
    // Show results
    window.showAiResults = function(content) {
        const resultsEl = document.getElementById('aiResults');
        const contentEl = document.getElementById('aiResultsContent');
        if (contentEl) contentEl.innerHTML = content;
        if (resultsEl) resultsEl.style.display = 'block';
    };
    
    // Format analysis results
    window.formatAnalysisResults = function(analysis) {
        console.log('🎯 FORMATTING ANALYSIS:', analysis);
        
        if (!analysis) return '<div style="color: red;">❌ Analiz sonucu alınamadı.</div>';
        
        let html = '<div class="analysis-results" style="font-family: Inter, sans-serif;">';
        
        // Genel Skor - Büyük ve görünür
        if (analysis.overall_score !== undefined) {
            const scoreColor = analysis.overall_score >= 80 ? '#10b981' : analysis.overall_score >= 60 ? '#f59e0b' : '#ef4444';
            html += `<div style="text-align: center; margin-bottom: 20px; padding: 15px; background: white; border-radius: 10px; border: 2px solid ${scoreColor};">
                <div style="font-size: 32px; font-weight: bold; color: ${scoreColor};">
                    ${analysis.overall_score}/100
                </div>
                <div style="font-size: 14px; color: #6b7280; margin-top: 5px;">
                    🎯 SEO Analiz Skoru
                </div>
            </div>`;
        }
        
        // Detaylı skorlar
        if (analysis.title_score !== undefined || analysis.content_score !== undefined) {
            html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">';
            
            if (analysis.title_score !== undefined) {
                html += `<div style="text-align: center; padding: 10px; background: #f3f4f6; border-radius: 8px;">
                    <div style="font-weight: bold; color: #374151;">📝 Başlık</div>
                    <div style="font-size: 18px; font-weight: bold; color: #667eea;">${analysis.title_score}/100</div>
                </div>`;
            }
            
            if (analysis.content_score !== undefined) {
                html += `<div style="text-align: center; padding: 10px; background: #f3f4f6; border-radius: 8px;">
                    <div style="font-weight: bold; color: #374151;">📄 İçerik</div>
                    <div style="font-size: 18px; font-weight: bold; color: #667eea;">${analysis.content_score}/100</div>
                </div>`;
            }
            
            html += '</div>';
        }
        
        // Öneriler
        if (analysis.suggestions && Array.isArray(analysis.suggestions) && analysis.suggestions.length > 0) {
            html += '<div style="margin-top: 15px;">';
            html += '<div style="font-weight: bold; margin-bottom: 10px; color: #374151;">💡 AI Önerileri:</div>';
            html += '<div style="background: white; border-radius: 8px; padding: 10px;">';
            
            analysis.suggestions.forEach((suggestion, index) => {
                const icon = suggestion.includes('✅') ? '✅' : '💡';
                const color = suggestion.includes('✅') ? '#10b981' : '#6b7280';
                html += `<div style="display: flex; align-items: flex-start; margin-bottom: 8px; padding: 8px; background: #f9fafb; border-radius: 6px;">
                    <span style="margin-right: 8px; font-size: 16px;">${icon}</span>
                    <span style="flex: 1; color: ${color}; font-size: 14px; line-height: 1.4;">${suggestion}</span>
                </div>`;
            });
            
            html += '</div></div>';
        }
        
        html += '</div>';
        console.log('✅ FORMATTED HTML:', html);
        return html;
    };
    
    // Format keyword results
    window.formatKeywordResults = function(keywords) {
        if (!keywords) return '<p>Anahtar kelime sonucu alınamadı.</p>';
        
        let html = '<div class="keyword-results">';
        
        if (keywords.primary_keywords) {
            html += '<div class="keyword-section mb-3">';
            html += '<h6><i class="fas fa-key text-primary me-2"></i>Ana Anahtar Kelimeler:</h6>';
            html += '<div class="keyword-tags">';
            keywords.primary_keywords.forEach(keyword => {
                html += `<span class="badge bg-primary me-1 mb-1">${keyword}</span>`;
            });
            html += '</div></div>';
        }
        
        if (keywords.secondary_keywords) {
            html += '<div class="keyword-section mb-3">';
            html += '<h6><i class="fas fa-tags text-secondary me-2"></i>İkincil Anahtar Kelimeler:</h6>';
            html += '<div class="keyword-tags">';
            keywords.secondary_keywords.forEach(keyword => {
                html += `<span class="badge bg-secondary me-1 mb-1">${keyword}</span>`;
            });
            html += '</div></div>';
        }
        
        html += '</div>';
        return html;
    };
    
    // Livewire event listeners
    document.addEventListener('livewire:init', function () {
        console.log('🔥 LIVEWIRE INIT BAŞLADI!');
        
        // Console log event'i dinle
        Livewire.on('console-log', (event) => {
            console.log('📨 BACKEND MESSAGE:', event.message);
        });
        
        // FORCE DOM UPDATE - GUARANTEED DISPLAY!
        Livewire.on('force-dom-update', (event) => {
            console.log('🔥 FORCE DOM UPDATE:', event);
            const target = document.getElementById(event.target);
            if (target && event.html) {
                target.innerHTML = event.html;
                console.log('✅ DOM UPDATED SUCCESSFULLY!');
                
                // Panel'i visible yap ve scroll yap
                target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                console.log('❌ DOM UPDATE FAILED:', {target: !!target, html: !!event.html});
            }
        });
        
        // AI progress events
        Livewire.on('ai-progress-start', (event) => {
            console.log('⏳ AI PROGRESS START:', event);
            showAiProgress(event.message || 'AI işlemi devam ediyor...');
        });
        
        // AI analysis complete
        Livewire.on('ai-analysis-complete', (event) => {
            hideAiProgress();
            if (event.analysis) {
                const formattedContent = formatAnalysisResults(event.analysis);
                showAiResults(formattedContent);
            }
        });
        
        // AI suggestions ready
        Livewire.on('ai-suggestions-ready', (event) => {
            hideAiProgress();
            if (event.suggestions) {
                const formattedContent = formatAnalysisResults(event.suggestions);
                showAiResults(formattedContent);
            }
        });
        
        // AI keywords ready
        Livewire.on('ai-keywords-ready', (event) => {
            hideAiProgress();
            if (event.keywords) {
                const formattedContent = formatKeywordResults(event.keywords);
                showAiResults(formattedContent);
            }
        });
        
        // AI translations ready
        Livewire.on('ai-translations-ready', (event) => {
            hideAiProgress();
            if (event.translations) {
                let html = '<div class="translation-results">';
                html += '<h6><i class="fas fa-language text-info me-2"></i>Çeviriler:</h6>';
                
                Object.keys(event.translations).forEach(lang => {
                    html += `<div class="translation-item mb-3">
                        <strong>${lang.toUpperCase()}:</strong>
                        <div class="border p-2 mt-1 rounded bg-light">
                            ${event.translations[lang].replace(/\n/g, '<br>')}
                        </div>
                    </div>`;
                });
                
                html += '</div>';
                showAiResults(html);
            }
        });
        
        // AI message events
        Livewire.on('ai-message-sent', (event) => {
            addAiMessage(event.message, true);
        });
        
        Livewire.on('ai-message-received', (event) => {
            addAiMessage(event.message, false);
        });
    });
});