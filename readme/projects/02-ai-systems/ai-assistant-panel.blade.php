{{-- AI Asistan Panel - Modern Floating Design --}}
{{-- DEBUG: AI PANEL INCLUDE BAŞLADI! --}}
@php
    \Log::info('🎨 AI ASSISTANT PANEL INCLUDE BAŞLADI!', [
        'timestamp' => now()->format('H:i:s'),
        'user' => auth()->user()->name ?? 'Guest',
        'route' => request()->route()->getName() ?? 'Unknown'
    ]);
@endphp

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