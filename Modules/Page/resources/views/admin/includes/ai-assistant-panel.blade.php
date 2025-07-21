{{-- AI Asistan Panel - Modern Floating Design --}}
{{-- DEBUG: AI PANEL INCLUDE BAŞLADI! --}}
@php
    \Log::info('🎨 AI ASSISTANT PANEL INCLUDE BAŞLADI!', [
        'timestamp' => now()->format('H:i:s'),
        'user' => auth()->user()->name ?? 'Guest',
        'route' => request()->route()->getName() ?? 'Unknown'
    ]);
@endphp

@push('styles')
<style>
        .ai-assistant-panel {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99999;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Toggle Button */
        .ai-toggle-btn {
            position: relative;
            width: 64px;
            height: 64px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .ai-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
        }

        .ai-icon {
            font-size: 24px;
            z-index: 2;
        }

        .ai-pulse {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: aiPulse 2s infinite;
        }

        @keyframes aiPulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Main Panel */
        .ai-panel {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 400px;
            max-height: 600px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: aiPanelSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes aiPanelSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes aiPanelSlideOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
        }

        /* Panel Header */
        .ai-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .ai-panel-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
        }

        .ai-status-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 12px;
        }

        .ai-close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .ai-close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Panel Content */
        .ai-panel-content {
            max-height: 520px;
            overflow-y: auto;
            padding: 24px;
        }

        .ai-section-title {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }

        /* Quick Actions */
        .ai-action-grid {
            display: grid;
            gap: 12px;
            margin-bottom: 32px;
        }

        .ai-action-card {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .ai-action-card:hover {
            border-color: #667eea;
            background: #f8faff;
            transform: translateX(4px);
        }

        .ai-action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
            color: white;
        }

        .ai-action-icon.seo { background: linear-gradient(135deg, #10b981, #059669); }
        .ai-action-icon.content { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .ai-action-icon.keywords { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .ai-action-icon.translate { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .ai-action-icon.analysis { background: linear-gradient(135deg, #10b981, #059669); }
        .ai-action-icon.suggestions { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .ai-action-icon.optimize { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .ai-action-icon.competitor { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .ai-action-icon.quality { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .ai-action-icon.schema { background: linear-gradient(135deg, #6b7280, #4b5563); }

        .ai-action-content {
            flex: 1;
        }

        .ai-action-title {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            margin-bottom: 4px;
        }

        .ai-action-desc {
            font-size: 12px;
            color: #6b7280;
        }

        .ai-action-arrow {
            color: #9ca3af;
            font-size: 12px;
        }

        .ai-token-cost {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Chat Section */
        .ai-chat-messages {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 16px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
        }

        .ai-message {
            display: flex;
            margin-bottom: 16px;
        }

        .ai-message:last-child {
            margin-bottom: 0;
        }

        .ai-message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .ai-message-content {
            flex: 1;
        }

        .ai-message-text {
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.5;
            color: #374151;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .ai-message-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
            margin-left: 16px;
        }

        /* Chat Input */
        .ai-input-container {
            display: flex;
            gap: 8px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .ai-chat-field {
            flex: 1;
            border: none;
            background: none;
            outline: none;
            font-size: 14px;
            color: #374151;
        }

        .ai-chat-field::placeholder {
            color: #9ca3af;
        }

        .ai-send-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-send-btn:hover {
            transform: scale(1.05);
        }

        /* Progress Indicator */
        .ai-progress {
            display: flex;
            justify-content: center;
            padding: 32px 0;
        }

        .ai-progress-content {
            text-align: center;
        }

        .ai-spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto 16px;
            position: relative;
        }

        .ai-spinner-circle {
            width: 100%;
            height: 100%;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: aiSpin 1s linear infinite;
        }

        @keyframes aiSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .ai-progress-text {
            font-size: 14px;
            color: #6b7280;
        }

        /* Results Section */
        .ai-results-content {
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-radius: 12px;
            padding: 20px;
        }

        /* Responsive */
        /* AI Result Cards - Clean Design */
        .ai-result-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #667eea;
            transition: all 0.2s ease;
        }

        .ai-result-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        /* AI Process Tracker */
        .ai-process-tracker {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
            padding: 12px;
            border-left: 3px solid #667eea;
            margin-bottom: 16px;
        }

        /* Remove excessive borders */
        .ai-results-content {
            border: none !important;
        }

        .ai-results-content .bg-white.border {
            border: none !important;
        }

        @media (max-width: 768px) {
            .ai-assistant-panel {
                bottom: 20px;
                right: 20px;
            }
            
            .ai-panel {
                width: calc(100vw - 40px);
                max-width: 360px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .ai-panel {
                background: #1f2937;
                border-color: #374151;
            }
            
            .ai-chat-messages,
            .ai-input-container {
                background: #111827;
                border-color: #374151;
            }
            
            .ai-message-text {
                background: #374151;
                color: #f3f4f6;
            }
            
            .ai-action-card {
                background: #374151;
                border-color: #4b5563;
                color: #f3f4f6;
            }
            
            .ai-action-card:hover {
                background: #4b5563;
                border-color: #667eea;
            }
        }
</style>
@endpush

<div class="ai-assistant-panel" id="aiAssistantPanel">
        {{-- AI Panel Toggle Button --}}
        <button class="ai-toggle-btn" id="aiToggleBtn" title="AI Asistan" 
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.5) !important;">
            <div class="ai-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-pulse"></div>
        </button>

        {{-- AI Assistant Main Panel --}}
        <div class="ai-panel" id="aiPanel" style="display: none;">
            {{-- Panel Header --}}
            <div class="ai-panel-header">
                <div class="ai-panel-title">
                    <i class="fas fa-magic me-2"></i>
                    <span>AI Asistan</span>
                    <span class="ai-status-badge" id="aiStatus">Hazır</span>
                </div>
                <button class="ai-close-btn" id="aiCloseBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Panel Content --}}
            <div class="ai-panel-content">
                {{-- Quick Actions --}}
                <div class="ai-quick-actions">
                    <div class="ai-section-title">
                        <i class="fas fa-bolt me-2"></i>Hızlı İşlemler
                    </div>
                    
                    <div class="ai-action-grid">
                        {{-- 🚀 ANA FEATURES --}}
                        

                        {{-- 🚀 DİNAMİK AI FEATURES --}}
                        @if(isset($aiFeatures) && $aiFeatures->count() > 0)
                            @foreach($aiFeatures as $feature)
                                <div class="ai-action-card {{ $loop->index < 3 ? 'primary' : '' }}" 
                                     wire:click="executeAIFeature('{{ $feature['slug'] }}')"
                                     onclick="aiProcessTracker.start('{{ $feature['name'] }} yapılıyor...', {{ $feature['token_cost'] ?? 100 }})">
                                    <div class="ai-action-icon {{ $this->getFeatureClass($feature['slug']) }}">
                                        <i class="{{ $this->getFeatureIcon($feature['slug']) }}"></i>
                                    </div>
                                    <div class="ai-action-content">
                                        <div class="ai-action-title">{{ $feature['emoji'] }} {{ $feature['name'] }}</div>
                                        <div class="ai-action-desc">{{ Str::limit($feature['description'], 45) }}</div>
                                    </div>
                                    <div class="ai-action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    <div class="ai-token-cost">{{ $feature['token_cost'] ?? 100 }} token</div>
                                </div>
                            @endforeach
                        @else
                            {{-- FALLBACK: Yedek hardcode features (database bağlantısı olmadığında) --}}
                            <div class="ai-action-card primary" wire:click="runQuickAnalysis">
                                <div class="ai-action-icon analysis">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="ai-action-content">
                                    <div class="ai-action-title">🚀 Hızlı Analiz</div>
                                    <div class="ai-action-desc">SEO, içerik ve performans analizi</div>
                                </div>
                                <div class="ai-action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                            
                            <div class="ai-action-card primary" wire:click="generateAISuggestions">
                                <div class="ai-action-icon suggestions">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="ai-action-content">
                                    <div class="ai-action-title">🎯 AI Önerileri</div>
                                    <div class="ai-action-desc">Akıllı içerik ve başlık önerileri</div>
                                </div>
                                <div class="ai-action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- AI Chat Interface --}}
                <div class="ai-chat-section">
                    <div class="ai-section-title">
                        <i class="fas fa-comments me-2"></i>AI Sohbet
                    </div>
                    
                    {{-- Chat Messages --}}
                    <div class="ai-chat-messages" id="aiChatMessages">
                        <div class="ai-message assistant">
                            <div class="ai-message-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="ai-message-content">
                                <div class="ai-message-text">
                                    Merhaba! Sayfanızla ilgili size nasıl yardımcı olabilirim? SEO analizi, içerik optimizasyonu veya çeviri konularında destek verebilirim.
                                </div>
                                <div class="ai-message-time">Şimdi</div>
                            </div>
                        </div>
                    </div>

                    {{-- Chat Input --}}
                    <div class="ai-chat-input">
                        <div class="ai-input-container">
                            <input type="text" 
                                   class="ai-chat-field" 
                                   placeholder="AI asistanına bir soru sorun..."
                                   wire:model.live="aiChatMessage"
                                   wire:keydown.enter.prevent="sendAiMessage">
                            <button class="ai-send-btn" wire:click="sendAiMessage">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Analysis Results --}}
                <div class="ai-results-section" id="aiResults" style="display: block; margin-top: 20px;">
                    <div class="ai-section-title">
                        <i class="fas fa-chart-line me-2"></i>📊 Analiz Sonuçları
                    </div>
                    
                    <div class="ai-results-content" id="aiResultsContent" style="background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%); border-radius: 12px; padding: 20px; margin-top: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                        
                        {{-- AI İşlem Detay Tracking Sistemi --}}
                        <div class="ai-process-tracker" id="aiProcessTracker" style="display: none;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="text-muted">AI işlemi gerçekleştiriliyor...</span>
                            </div>
                            <div class="ai-process-details text-sm text-muted">
                                <div id="aiProcessStep">🤖 Analiz başlatılıyor...</div>
                                <div id="aiProcessCredits" class="mt-1">💎 Kullanılan token: <span class="fw-bold">-</span></div>
                            </div>
                        </div>
                        
                        {{-- LIVEWIRE + SESSION FALLBACK SONUÇLARI --}}
                        @php
                            $analysis = $aiAnalysis ?? session('ai_last_analysis') ?? null;
                        @endphp
                        
                        @if(!empty($analysis))
                            <div class="ai-result-card">
                                <div style="text-align: center; margin-bottom: 15px;">
                                    <div class="display-4 fw-bold {{ $analysis['overall_score'] >= 80 ? 'text-success' : ($analysis['overall_score'] >= 60 ? 'text-warning' : 'text-danger') }}">
                                        {{ $analysis['overall_score'] ?? 'N/A' }}/100
                                    </div>
                                    <div class="text-muted">🎯 SEO Analiz Skoru</div>
                                </div>
                                
                                @if(isset($analysis['title_score']) || isset($analysis['content_score']))
                                <div class="row g-2 mb-3">
                                    @if(isset($analysis['title_score']))
                                    <div class="col-6">
                                        <div class="card text-center">
                                            <div class="card-body p-2">
                                                <div class="fw-bold text-dark">📝 Başlık</div>
                                                <div class="h5 fw-bold text-primary">{{ $analysis['title_score'] }}/100</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if(isset($analysis['content_score']))
                                    <div class="col-6">
                                        <div class="card text-center">
                                            <div class="card-body p-2">
                                                <div class="fw-bold text-dark">📄 İçerik</div>
                                                <div class="h5 fw-bold text-primary">{{ $analysis['content_score'] }}/100</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                
                                @if(!empty($analysis['suggestions']))
                                <div>
                                    <div class="fw-bold mb-2 text-dark">💡 AI Önerileri:</div>
                                    @foreach($analysis['suggestions'] as $suggestion)
                                    <div class="d-flex align-items-start mb-2 p-2 bg-light rounded">
                                        <span class="me-2">{{ str_contains($suggestion, '✅') ? '✅' : '💡' }}</span>
                                        <span class="flex-fill small {{ str_contains($suggestion, '✅') ? 'text-success' : 'text-dark' }}">{{ $suggestion }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-primary fw-bold">
                                🤖 AI analiz sonuçları burada görünecek...
                                <br><small class="text-muted">Hızlı Analiz butonuna tıklayın</small>
                            </div>
                        @endif
                        
                        {{-- DEBUG BİLGİSİ --}}
                        <div style="margin-top: 10px; padding: 10px; background: #f1f5f9; border-radius: 6px; font-size: 12px; color: #64748b;">
                            <strong>Debug:</strong> 
                            Property = {{ !empty($aiAnalysis) ? 'DOLU' : 'BOŞ' }} | 
                            Session = {{ !empty(session('ai_last_analysis')) ? 'DOLU' : 'BOŞ' }} | 
                            Final = {{ !empty($analysis) ? 'DOLU' : 'BOŞ' }} | 
                            Zaman: {{ now()->format('H:i:s') }}
                        </div>
                    </div>
                </div>

                {{-- AI Suggestions Section --}}
                <div class="ai-suggestions-section" id="aiSuggestions" style="display: block; margin-top: 20px;">
                    <div class="ai-section-title">
                        <i class="fas fa-lightbulb me-2"></i>🎯 AI Önerileri
                    </div>
                    
                    <div class="ai-suggestions-content" id="aiSuggestionsContent" style="background: white; border: 2px solid var(--bs-success) !important; border-radius: 12px; padding: 20px; margin-top: 10px; color: var(--bs-dark);">
                        
                        {{-- AI SUGGESTIONS DATA --}}
                        @php
                            $suggestions = $aiSuggestions ?? session('ai_last_suggestions') ?? null;
                        @endphp
                        
                        @if(!empty($suggestions) && is_array($suggestions))
                            <div class="bg-white border border-success rounded-3 p-3">
                                <div class="fw-bold mb-3 text-dark text-center">
                                    🎯 AI İyileştirme Önerileri ({{ count($suggestions) }} adet)
                                </div>
                                
                                <div class="d-grid gap-2">
                                    @foreach(array_slice($suggestions, 0, 10) as $index => $suggestion)
                                    <div class="d-flex align-items-start p-3 bg-light rounded border-start border-success border-3">
                                        <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 12px;">{{ $index + 1 }}</span>
                                        <span class="flex-fill text-dark small lh-base">{{ $suggestion }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                
                                @if(count($suggestions) > 10)
                                <div class="text-center mt-3 p-2 bg-light rounded">
                                    <small class="text-muted">
                                        Ve {{ count($suggestions) - 10 }} öneri daha... (Form içinde tümünü görün)
                                    </small>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-success fw-bold">
                                🎯 AI önerileri burada görünecek...
                                <br><small class="text-muted">AI Önerileri butonuna tıklayın</small>
                            </div>
                        @endif
                        
                        {{-- SUGGESTIONS DEBUG BİLGİSİ --}}
                        <div style="margin-top: 10px; padding: 10px; background: #ecfdf5; border-radius: 6px; font-size: 12px; color: #059669;">
                            <strong>Suggestions Debug:</strong> 
                            Property = {{ !empty($aiSuggestions) ? (is_array($aiSuggestions) ? count($aiSuggestions).' adet' : 'string') : 'BOŞ' }} | 
                            Session = {{ !empty(session('ai_last_suggestions')) ? (is_array(session('ai_last_suggestions')) ? count(session('ai_last_suggestions')).' adet' : 'string') : 'BOŞ' }} | 
                            Final = {{ !empty($suggestions) ? (is_array($suggestions) ? count($suggestions).' adet' : 'string') : 'BOŞ' }} | 
                            Zaman: {{ now()->format('H:i:s') }}
                        </div>
                    </div>
                </div>

                {{-- Progress Indicator --}}
                <div class="ai-progress" id="aiProgress" style="display: none;">
                    <div class="ai-progress-content">
                        <div class="ai-spinner">
                            <div class="ai-spinner-circle"></div>
                        </div>
                        <div class="ai-progress-text">AI işlemi devam ediyor...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
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
        
        // Close panel
        aiCloseBtn.addEventListener('click', function() {
            aiPanel.style.display = 'none';
        });
        
        // Close panel when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.ai-assistant-panel')) {
                aiPanel.style.display = 'none';
            }
        });
        
        // Auto-scroll chat messages
        function scrollChatToBottom() {
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        }
        
        // Add message to chat
        window.addAiMessage = function(message, isUser = false) {
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
            const textEl = progressEl.querySelector('.ai-progress-text');
            textEl.textContent = text;
            progressEl.style.display = 'block';
        };
        
        // Hide progress
        window.hideAiProgress = function() {
            document.getElementById('aiProgress').style.display = 'none';
        };
        
        // Show results
        window.showAiResults = function(content) {
            const resultsEl = document.getElementById('aiResults');
            const contentEl = document.getElementById('aiResultsContent');
            contentEl.innerHTML = content;
            resultsEl.style.display = 'block';
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
</script>
@endpush