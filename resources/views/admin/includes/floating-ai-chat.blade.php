{{-- 🤖 Modern Floating AI Assistant Widget --}}
<div id="floating-ai-widget" class="floating-ai-widget ai-widget-theme" style="display: none;">
    
    {{-- History Sidebar - Sol tarafta gösterilen sonuçlar --}}
    <div id="ai-history-sidebar" class="ai-history-sidebar" style="display: none;">
        <div class="history-header">
            <h6 class="history-title">
                <i class="fas fa-history me-2"></i>Son AI Sonuçları
            </h6>
            <button class="btn-clear-history" onclick="clearAIHistory()" title="Geçmişi Temizle">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="history-content" id="ai-history-content">
            <div class="history-empty">
                <i class="fas fa-robot opacity-50"></i>
                <p>Henüz AI sonucu yok</p>
            </div>
        </div>
    </div>
    
    {{-- Header with Gradient --}}
    <div class="widget-header" style="cursor: move;">
        <div class="widget-header-content">
            <div class="widget-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <div class="widget-title">AI Asistan</div>
                <div class="widget-status">Hazır</div>
            </div>
        </div>
        <div class="widget-header-actions">
            <button id="ai-history-toggle" class="btn-widget-control" onclick="toggleAIHistory()" title="Geçmişi Göster/Gizle">
                <i class="fas fa-history"></i>
            </button>
            <button id="ai-widget-close" class="btn-widget-control">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    {{-- Content Area --}}
    <div class="widget-content">
        
        {{-- Section Title --}}
        <div class="widget-section-header">
            <h6 class="widget-section-title">
                <i class="fas fa-search-plus me-2 text-warning"></i>Sayfa SEO Analiz Araçları
            </h6>
        </div>
        
        {{-- AI Features from Database - Kategori 1 (SEO Uzmanları) --}}
        <div class="widget-features" wire:ignore>
            @php
                // Floating widget için SADECE kategori 1 (SEO) feature'ları
                $quickFeatures = \Modules\AI\App\Models\AIFeature::where('status', 'active')
                    ->where('ai_feature_category_id', 1) // SEO kategorisi
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
                    
                // İlk 6 tanesini üstte featured göster, kalanları normal göster
                $featuredFeatures = $quickFeatures->take(6);
                $otherFeatures = $quickFeatures->skip(6);
            @endphp
            
            @if($featuredFeatures->count() > 0)
                {{-- Featured AI Features (Top 8) --}}
                @foreach($featuredFeatures as $index => $feature)
<div class="feature-item {{ $index === 0 ? 'featured' : '' }}" 
                     onclick="event.preventDefault(); event.stopPropagation(); executeAIFeature('{{ $feature->slug }}', {{ $pageId ?? 'null' }}); return false;">
                    <div class="feature-content">
                        <div class="feature-icon {{ $index === 0 ? 'featured-icon' : '' }}">
                            @if($feature->emoji)
                                {{ $feature->emoji }}
                            @else
                                <i class="{{ $feature->icon ?? 'fas fa-sparkles' }}"></i>
                            @endif
                        </div>
                        <div class="feature-text">
                            <div class="feature-name">{{ $feature->name }}</div>
                            <div class="feature-desc">{{ Str::limit($feature->description, 45) }}</div>
                        </div>
                        <i class="fas fa-chevron-right feature-arrow"></i>
                    </div>
                </div>
                @endforeach
                
                {{-- Collapsible Section for Other Features --}}
                @if($otherFeatures->count() > 0)
                <div class="more-features-toggle" onclick="toggleMoreFeatures()" 
                     style="padding: 12px; text-align: center; border: 1px dashed var(--tblr-border-color); 
                            border-radius: 8px; cursor: pointer; margin: 12px 0; color: var(--tblr-muted);">
                    <i class="fas fa-chevron-down me-2" id="more-features-icon"></i>
                    <span id="more-features-text">{{ $otherFeatures->count() }} Daha Fazla AI Özelliği Göster</span>
                </div>
                
                <div id="more-features" style="display: none;">
                    @foreach($otherFeatures as $feature)
                    <div class="feature-item" 
                         onclick="event.preventDefault(); event.stopPropagation(); executeAIFeature('{{ $feature->slug }}', {{ $pageId ?? 'null' }}); return false;">
                        <div class="feature-content">
                            <div class="feature-icon">
                                @if($feature->emoji)
                                    {{ $feature->emoji }}
                                @else
                                    <i class="{{ $feature->icon ?? 'fas fa-sparkles' }}"></i>
                                @endif
                            </div>
                            <div class="feature-text">
                                <div class="feature-name">{{ $feature->name }}</div>
                                <div class="feature-desc">{{ Str::limit($feature->description, 45) }}</div>
                            </div>
                            <i class="fas fa-chevron-right feature-arrow"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @else
                {{-- No features available message --}}
                <div class="feature-item" style="opacity: 0.7; cursor: default;">
                    <div class="feature-content">
                        <div class="feature-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="feature-text">
                            <div class="feature-name">AI Özellikleri</div>
                            <div class="feature-desc">Sayfa ile alakalı AI özellikleri yükleniyor...</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- AI Chat Section --}}
        <div class="ai-chat-section">
            <div class="widget-section-header">
                <h6 class="widget-section-title">
                    <i class="fas fa-comments me-2 text-primary"></i>AI Chat
                </h6>
            </div>
            
            {{-- Chat Messages --}}
            <div class="ai-chat-messages" id="floating-chat-messages">
                <div class="chat-message ai-message">
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="message-content">
                        <div class="message-text">Merhaba! Size nasıl yardımcı olabilirim?</div>
                        <div class="message-time">Az önce</div>
                    </div>
                </div>
            </div>
            
            {{-- Chat Input --}}
            <div class="ai-chat-input">
                <div class="chat-input-group">
                    <input type="text" id="floating-chat-input" class="chat-input" placeholder="Mesajınızı yazın...">
                    <button id="floating-chat-send" class="chat-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Bottom Actions --}}
        <div class="widget-actions">
            <button onclick="openAIPage()" class="btn-full-ai">
                <i class="fas fa-external-link-alt me-2"></i>Tam AI Sayfası
            </button>
        </div>
    </div>
</div>

{{-- Floating Robot Button (Minimized) --}}
<div id="floating-ai-minimized" class="floating-ai-minimized" style="display: block; position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button onclick="toggleAIWidget()" 
            style="width: 64px; height: 64px; border-radius: 50%; border: none; 
                   background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); 
                   color: white; cursor: pointer; box-shadow: 0 8px 32px rgba(99, 102, 241, 0.4);
                   display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;
                   position: relative;">
        <i class="fas fa-robot" style="font-size: 24px;"></i>
        <div style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; 
                    background: #10b981; border-radius: 50%; border: 2px solid #2c2f36;
                    display: flex; align-items: center; justify-content: center;">
            <div style="width: 8px; height: 8px; background: white; border-radius: 50%;"></div>
        </div>
    </button>
</div>

{{-- Enhanced JavaScript for AI Widget with Real Features & Chat --}}
<script>
// Force refresh token - prevent caching
const AI_WIDGET_VERSION = '{{ time() }}';
// Set global page context for AI features
@if(isset($pageId) && $pageId)
window.pageId = {{ $pageId }};
@endif

document.addEventListener('DOMContentLoaded', function() {
    const aiWidget = document.getElementById('floating-ai-widget');
    const aiMinimized = document.getElementById('floating-ai-minimized');
    const closeBtn = document.getElementById('ai-widget-close');
    const chatInput = document.getElementById('floating-chat-input');
    const chatSend = document.getElementById('floating-chat-send');
    const chatMessages = document.getElementById('floating-chat-messages');
    
    let isMinimized = true; // Başlangıçta minimize
    
    // Close/Minimize Widget
    closeBtn.addEventListener('click', function() {
        minimizeWidget();
    });
    
    function minimizeWidget() {
        aiWidget.style.display = 'none';
        aiMinimized.style.display = 'block';
        isMinimized = true;
    }
    
    // Global function for toggle
    window.toggleAIWidget = function() {
        if (isMinimized) {
            aiWidget.style.display = 'block';
            aiMinimized.style.display = 'none';
            isMinimized = false;
        } else {
            minimizeWidget();
        }
    }
    
    // AI History Management
    let aiHistoryItems = JSON.parse(localStorage.getItem('aiHistoryItems') || '[]');
    let isHistoryVisible = false;
    
    window.toggleAIHistory = function() {
        const sidebar = document.getElementById('ai-history-sidebar');
        const toggleBtn = document.getElementById('ai-history-toggle');
        
        if (!sidebar) return;
        
        isHistoryVisible = !isHistoryVisible;
        
        if (isHistoryVisible) {
            sidebar.style.display = 'block';
            sidebar.classList.add('active');
            toggleBtn.querySelector('i').classList.add('text-warning');
            loadHistoryItems();
        } else {
            sidebar.classList.remove('active');
            toggleBtn.querySelector('i').classList.remove('text-warning');
            setTimeout(() => {
                sidebar.style.display = 'none';
            }, 300);
        }
    }
    
    window.addToAIHistory = function(featureName, result, timestamp = null) {
        const now = timestamp || new Date().toISOString();
        const preview = result.replace(/<[^>]*>/g, '').substring(0, 100) + '...';
        
        const historyItem = {
            id: Date.now(),
            featureName: featureName,
            result: result,
            preview: preview,
            timestamp: now,
            date: new Date(now).toLocaleString('tr-TR')
        };
        
        // En yeni önce gelsin
        aiHistoryItems.unshift(historyItem);
        
        // Son 20 öğeyi tut
        if (aiHistoryItems.length > 20) {
            aiHistoryItems = aiHistoryItems.slice(0, 20);
        }
        
        // LocalStorage'a kaydet
        localStorage.setItem('aiHistoryItems', JSON.stringify(aiHistoryItems));
        
        // History görünürse güncelle
        if (isHistoryVisible) {
            loadHistoryItems();
        }
    }
    
    window.loadHistoryItems = function() {
        const historyContent = document.getElementById('ai-history-content');
        if (!historyContent) return;
        
        if (aiHistoryItems.length === 0) {
            historyContent.innerHTML = `
                <div class="history-empty">
                    <i class="fas fa-robot opacity-50"></i>
                    <p>Henüz AI sonucu yok</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        aiHistoryItems.forEach(item => {
            html += `
                <div class="history-item" onclick="showHistoryResult('${item.id}')">
                    <div class="history-item-title">${item.featureName}</div>
                    <div class="history-item-preview">${item.preview}</div>
                    <div class="history-item-time">${item.date}</div>
                </div>
            `;
        });
        
        historyContent.innerHTML = html;
    }
    
    window.showHistoryResult = function(itemId) {
        const item = aiHistoryItems.find(h => h.id == itemId);
        if (!item) return;
        
        // Modern modal ile sonucu göster
        createModernAIModal('history-' + itemId, item.featureName, item.result);
    }
    
    window.clearAIHistory = function() {
        if (confirm('Tüm AI geçmişini temizlemek istediğinizden emin misiniz?')) {
            aiHistoryItems = [];
            localStorage.removeItem('aiHistoryItems');
            loadHistoryItems();
        }
    }
    
    // Widget kapandığında history de kapansın
    const originalMinimizeWidget = minimizeWidget;
    minimizeWidget = function() {
        if (isHistoryVisible) {
            toggleAIHistory();
        }
        originalMinimizeWidget();
    }
    
    // Safe CSRF Token getter
    function getCsrfToken() {
        const metaElement = document.querySelector('meta[name="csrf-token"]');
        if (metaElement) {
            return metaElement.getAttribute('content');
        }
        
        // Fallback: Try to get from any form
        const formToken = document.querySelector('input[name="_token"]');
        if (formToken) {
            return formToken.value;
        }
        
        // Fallback: Try to get from Livewire
        if (window.Livewire && window.Livewire.first()) {
            const firstComponent = window.Livewire.first();
            if (firstComponent.snapshot && firstComponent.snapshot.memo && firstComponent.snapshot.memo.csrf) {
                return firstComponent.snapshot.memo.csrf;
            }
        }
        
        console.warn('CSRF token not found, using empty string');
        return '';
    }

    // Advanced AI Feature Execution - Smart routing for page vs chat features
    window.executeAIFeature = function(featureSlug, recordId = null) {
        // Critical: Prevent any navigation or page changes
        if (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
        }
        
        console.log('🚀 Executing AI Feature:', featureSlug, 'Record:', recordId);
        
        // Map feature slugs to actual Page component methods (only for page-related features)
        const pageRelatedFeatures = {
            'seo-puani-analizi': 'runQuickAnalysis',
            'hizli-seo-analizi': 'runQuickAnalysis', 
            'anahtar-kelime-arastirmasi': 'runQuickAnalysis',
            'icerik-optimizasyonu': 'runQuickAnalysis',
            'seo-content-generation': 'generateAISuggestions',
            'icerik-genisletme': 'generateAISuggestions',
            'baslik-uretici': 'generateAISuggestions',
            'sayfa-gelistirme-onerileri': 'generateAISuggestions',
            'alt-baslik-onerileri': 'generateAISuggestions',
            'kullanici-deneyimi-analizi': 'generateAISuggestions',
            'icerik-ozetleme': 'generateAISuggestions',
            'meta-aciklama-uretici': 'generateAISuggestions',
            'schema-markup-onerileri': 'generateAISuggestions',
            'coklu-dil-cevirisi': 'generateAISuggestions',
            'rekabet-analizi': 'generateAISuggestions',
            'trending-konu-onerileri': 'generateAISuggestions',
            'link-onerileri': 'generateAISuggestions',
            'icerik-kalite-skoru': 'generateAISuggestions',
            'dil-kalitesi-kontrolu': 'generateAISuggestions'
        };
        
        // Kategori 1 feature'ları için modern modal gösterimi
        const category1Features = [
            'seo-puan-analizi', 'hizli-seo-analizi', 'anahtar-kelime-arastirmasi', 
            'icerik-optimizasyonu', 'icerik-genisletme', 'icerik-ozetleme',
            'baslik-uretici', 'meta-aciklama-uretici', 'alt-baslik-onerileri',
            'sayfa-gelistirme-onerileri', 'kullanici-deneyimi-analizi', 'icerik-kalite-skoru',
            'coklu-dil-cevirisi', 'dil-kalitesi-kontrolu', 'rekabet-analizi',
            'trending-konu-onerileri', 'schema-markup-onerileri', 'link-onerileri'
        ];
        
        const isCategory1Feature = category1Features.includes(featureSlug);
        const hasLivewireComponents = window.Livewire && window.Livewire.all().length > 0;
        const isPageRelatedFeature = pageRelatedFeatures[featureSlug];
        
        if (isCategory1Feature) {
            // Kategori 1 özelliği - Direkt modern modal göster
            console.log('🎯 Category 1 feature, executing modern modal:', featureSlug);
            
            // Feature name mapping for display
            const featureNames = {
                'seo-puan-analizi': 'SEO Puanı Analizi',
                'hizli-seo-analizi': 'Hızlı SEO Analizi',
                'anahtar-kelime-analizi': 'Anahtar Kelime Analizi',
                'icerik-optimizasyonu': 'İçerik Optimizasyonu',
                'icerik-genisletme': 'İçerik Genişletme',
                'icerik-ozetleme': 'İçerik Özetleme',
                'baslik-uretici': 'Başlık Üretici',
                'meta-aciklama-uretici': 'Meta Açıklama Üretici',
                'alt-baslik-onerileri': 'Alt Başlık Önerileri',
                'sayfa-gelistirme-onerileri': 'Sayfa Geliştirme Önerileri',
                'kullanici-deneyimi-analizi': 'Kullanıcı Deneyimi Analizi',
                'icerik-kalite-skoru': 'İçerik Kalite Skoru',
                'coklu-dil-cevirisi': 'Çoklu Dil Çevirisi',
                'dil-kalitesi-kontrolu': 'Dil Kalitesi Kontrolü',
                'rekabet-analizi': 'Rekabet Analizi',
                'trending-konu-onerileri': 'Trending Konu Önerileri',
                'schema-markup-onerileri': 'Schema Markup Önerileri',
                'link-onerileri': 'Link Önerileri'
            };
            const featureName = featureNames[featureSlug] || featureSlug;
            
            createModernAIModal(featureSlug, featureName);
        } else if (isPageRelatedFeature && hasLivewireComponents) {
            // Try to execute via page component (existing page functionality)
            const method = pageRelatedFeatures[featureSlug];
            try {
                // Try to find the page component by different methods
                let pageComponent = null;
                
                // Method 1: Find by element with page manage component
                const pageElement = document.querySelector('[wire\\:key*="page-manage"]');
                if (pageElement) {
                    pageComponent = window.Livewire.find(pageElement.getAttribute('wire:id'));
                }
                
                // Method 2: Find by component that has the method we need
                if (!pageComponent) {
                    pageComponent = window.Livewire.all().find(comp => {
                        try {
                            // Check if component has the method we want to call
                            return comp.fingerprint && (
                                comp.fingerprint.name.includes('page-manage') || 
                                comp.fingerprint.name.includes('Page') ||
                                comp.serverMemo?.checksum // Has server state, likely main component
                            );
                        } catch (e) {
                            return false;
                        }
                    });
                }
                
                // Method 3: Use first component as fallback (usually the main page component)
                if (!pageComponent && window.Livewire.all().length > 0) {
                    pageComponent = window.Livewire.all()[0];
                }
                
                if (pageComponent) {
                    console.log('🔍 Found component:', pageComponent.fingerprint?.name || 'unknown');
                    pageComponent.call(method).then(() => {
                        console.log('✅ Page component method executed successfully:', method);
                        addChatMessage(`🤖 ${featureSlug} analizi tamamlandı! Sonuçlar sayfada görünüyor.`, 'ai');
                    }).catch(error => {
                        console.error('Page Component Error, switching to chat mode:', error);
                        // If page component fails, fallback to chat mode
                        createModernAIModal(featureSlug, featureSlug);
                    });
                } else {
                    // No suitable page component found, use chat mode
                    console.log('No suitable page component found, using chat mode');
                    createModernAIModal(featureSlug, featureSlug);
                }
            } catch (error) {
                console.error('Livewire Error, switching to chat mode:', error);
                createModernAIModal(featureSlug, featureSlug);
            }
        } else {
            // Feature is not page-related OR no Livewire components available
            // Execute via AI chat system (for all other features)
            console.log('Using chat mode for feature:', featureSlug);
            executeFeatureViaChat(featureSlug);
        }
    }
    
    // Execute Category 60 feature - MODERN FULL-SCREEN MODAL DISPLAY
    function executeFeatureForLeftPanel(featureSlug) {
        console.log('🎯 Executing AI feature with modern modal:', featureSlug);
        
        // Feature name mapping for user-friendly display
        const featureNames = {
            'seo-puan-analizi': 'SEO Puanı Analizi',
            'hizli-seo-analizi': 'Hızlı SEO Analizi',
            'anahtar-kelime-analizi': 'Anahtar Kelime Analizi',
            'icerik-optimizasyonu': 'İçerik Optimizasyonu',
            'icerik-genisletme': 'İçerik Genişletme',
            'icerik-ozetleme': 'İçerik Özetleme',
            'baslik-uretici': 'Başlık Üretici',
            'meta-aciklama-uretici': 'Meta Açıklama Üretici',
            'alt-baslik-onerileri': 'Alt Başlık Önerileri',
            'sayfa-gelistirme-onerileri': 'Sayfa Geliştirme Önerileri',
            'kullanici-deneyimi-analizi': 'Kullanıcı Deneyimi Analizi',
            'icerik-kalite-skoru': 'İçerik Kalite Skoru',
            'coklu-dil-cevirisi': 'Çoklu Dil Çevirisi',
            'dil-kalitesi-kontrolu': 'Dil Kalitesi Kontrolü',
            'rekabet-analizi': 'Rekabet Analizi',
            'trending-konu-onerileri': 'Trending Konu Önerileri',
            'schema-markup-onerileri': 'Schema Markup Önerileri',
            'link-onerileri': 'Link Önerileri'
        };
        
        const featureName = featureNames[featureSlug] || featureSlug;
        
        // Create MODERN FULL-SCREEN MODAL instead of narrow left panel
        createModernAIModal(featureSlug, featureName);
    }
    
    // NEW: Create Modern AI Modal for better code display (Schema Markup, etc.)
    function createModernAIModal(featureSlug, featureName) {
        // Check if modal already exists
        const existingModal = document.getElementById('modern-ai-modal');
        if (existingModal) {
            console.log('🔄 Modal already exists, updating content instead of recreating');
            
            // Update modal title
            const titleElement = existingModal.querySelector('.modal-title');
            if (titleElement) titleElement.textContent = featureName;
            
            // Clear content and show loading
            const contentElement = existingModal.querySelector('#modern-ai-content');
            if (contentElement) {
                contentElement.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <div class="text-muted">AI analizi başlatılıyor...</div>
                        </div>
                    </div>
                `;
            }
            
            // Show modal if hidden and execute feature
            existingModal.style.display = 'block';
            executeAIFeatureForModernModal(featureSlug, featureName);
            return;
        }
        document.body.style.overflow = '';
        
        // Remove all backdrop elements
        const modalBackdrops = document.querySelectorAll('.modal-backdrop, [style*="z-index"]');
        modalBackdrops.forEach(backdrop => backdrop.remove());
        
        // Wait a bit to ensure DOM is clean
        setTimeout(() => {
            // Create modern modal HTML
        const modalHTML = `
            <div id="modern-ai-modal" class="modal fade show" 
                 style="display: block; z-index: 10000; background: rgba(0,0,0,0.5);" 
                 tabindex="-1" aria-hidden="false">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 90vw;">
                    <div class="modal-content">
                        <!-- Header -->
                        <div class="modal-header" style="background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%); color: white; position: relative;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-robot me-2"></i>
                                <h4 class="modal-title mb-0">${featureName}</h4>
                            </div>
                            <button type="button" class="btn btn-light btn-sm" onclick="minimizeAIModal()" 
                                    style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px;">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        
                        <!-- Body -->
                        <div class="modal-body p-4" id="modern-ai-content" style="min-height: 400px; max-height: 70vh; overflow-y: auto;">
                            <!-- Loading State -->
                            <div class="text-center py-5" id="ai-loading">
                                <div class="loading-spinner mx-auto mb-3" style="width: 48px; height: 48px; border-width: 4px;"></div>
                                <h5 class="text-primary mb-2">${featureName}</h5>
                                <p class="text-dark">AI analizi çalışıyor, lütfen bekleyin...</p>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="modal-footer bg-light">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <small class="text-dark">
                                    <i class="fas fa-robot me-1 text-primary"></i>AI ile oluşturuldu - ${new Date().toLocaleTimeString('tr-TR')}
                                </small>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="retryAIFeature('${featureSlug}', '${featureName}')">
                                        <i class="fas fa-redo me-1"></i>Tekrar Dene
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="minimizeAIModal()">
                                        <i class="fas fa-minus me-1"></i>Simge Durumuna Küçült
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add ESC key handler - minimize instead of close
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                minimizeAIModal();
            }
        };
        document.addEventListener('keydown', escapeHandler);
        
        // Store handler for cleanup
        document.getElementById('modern-ai-modal').escapeHandler = escapeHandler;
        
        // Execute AI feature for modal display
        executeAIFeatureForModernModal(featureSlug, featureName);
        }, 100); // setTimeout end
    }
    
    // Modern Modal Management System - Facebook-style
    let activeModals = [];
    
    // Close Modern AI Modal
    // DEPRECATED: Modal asla kapanmasın, sadece minimize olsun
    window.closeModernAIModal = function() {
        // Modal'ı kapat yerine minimize yap
        minimizeAIModal();
    }
    
    // Minimize Modal (Facebook-style)
    window.minimizeAIModal = function() {
        const modal = document.getElementById('modern-ai-modal');
        if (!modal) return;
        
        // Modal bilgilerini al
        const titleElement = modal.querySelector('.modal-title');
        const featureName = titleElement ? titleElement.textContent : 'AI Modal';
        const content = modal.querySelector('#modern-ai-content').innerHTML;
        
        // Duplicate modal kontrolü - aynı başlıkta modal zaten varsa güncelle
        const existingModalIndex = activeModals.findIndex(m => m.title === featureName);
        if (existingModalIndex !== -1) {
            activeModals[existingModalIndex].content = content;
            activeModals[existingModalIndex].timestamp = new Date().toLocaleTimeString('tr-TR');
            console.log('🔄 Updated existing minimized modal:', featureName);
        } else {
            // Active modal'lara ekle
            const modalData = {
                id: Date.now(),
                title: featureName,
                content: content,
                timestamp: new Date().toLocaleTimeString('tr-TR')
            };
            
            activeModals.unshift(modalData);
            
            // Maximum 5 modal tutulur
            if (activeModals.length > 5) {
                activeModals = activeModals.slice(0, 5);
            }
        }
        
        // Modal'ı DOM'dan kaldır (infinite loop olmadan)
        if (modal.escapeHandler) {
            document.removeEventListener('keydown', modal.escapeHandler);
        }
        modal.remove();
        
        // Minimized modal list'i güncelle
        updateMinimizedModalsList();
    }
    
    // Minimized modal'ları göster
    window.updateMinimizedModalsList = function() {
        // Widget yanında minimized modal listesi oluştur
        let minimizedContainer = document.getElementById('minimized-modals-container');
        
        if (!minimizedContainer) {
            minimizedContainer = document.createElement('div');
            minimizedContainer.id = 'minimized-modals-container';
            minimizedContainer.style.cssText = `
                position: fixed;
                right: 460px;
                bottom: 20px;
                z-index: 9998;
                display: flex;
                flex-direction: column;
                gap: 8px;
                max-height: 400px;
                overflow-y: auto;
            `;
            document.body.appendChild(minimizedContainer);
        }
        
        minimizedContainer.innerHTML = '';
        
        activeModals.forEach((modalData, index) => {
            const minimizedModal = document.createElement('div');
            minimizedModal.className = 'minimized-modal-item';
            minimizedModal.style.cssText = `
                background: var(--tblr-bg-surface);
                border: 1px solid var(--tblr-border-color);
                border-radius: 8px;
                padding: 12px 16px;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: var(--tblr-shadow-sm);
                max-width: 280px;
                position: relative;
            `;
            
            minimizedModal.innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-truncate" style="font-size: 13px;">${modalData.title}</div>
                        <div class="text-muted small">${modalData.timestamp}</div>
                    </div>
                    <div class="ms-2">
                        <i class="fas fa-window-restore text-primary"></i>
                    </div>
                </div>
            `;
            
            // Click to restore
            minimizedModal.onclick = () => restoreAIModal(modalData.id);
            
            // Hover effect
            minimizedModal.addEventListener('mouseenter', () => {
                minimizedModal.style.background = 'var(--tblr-bg-surface-secondary)';
                minimizedModal.style.borderColor = 'var(--tblr-primary)';
            });
            
            minimizedModal.addEventListener('mouseleave', () => {
                minimizedModal.style.background = 'var(--tblr-bg-surface)';
                minimizedModal.style.borderColor = 'var(--tblr-border-color)';
            });
            
            minimizedContainer.appendChild(minimizedModal);
        });
    }
    
    // Modal'ı restore et
    window.restoreAIModal = function(modalId) {
        const modalData = activeModals.find(m => m.id === modalId);
        if (!modalData) return;
        
        // Yeni modal oluştur
        createRestoredModal(modalData);
        
        // Active modal'lardan çıkar
        activeModals = activeModals.filter(m => m.id !== modalId);
        updateMinimizedModalsList();
    }
    
    // Restore edilmiş modal oluştur
    function createRestoredModal(modalData) {
        const modalHTML = `
            <div id="modern-ai-modal" class="modal fade show" 
                 style="display: block; z-index: 10000; background: rgba(0,0,0,0.5);" 
                 tabindex="-1" aria-hidden="false">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 90vw;">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%); color: white;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-robot me-2"></i>
                                <h4 class="modal-title mb-0">${modalData.title}</h4>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn-close btn-close-white" onclick="minimizeAIModal()"></button>
                            </div>
                        </div>
                        <div class="modal-body p-4" id="modern-ai-content" style="min-height: 400px; max-height: 70vh; overflow-y: auto;">
                            ${modalData.content}
                        </div>
                        <div class="modal-footer bg-light">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-robot me-1"></i>Restore edildi - ${modalData.timestamp}
                                </small>
                                <div>
                                    <!-- Sadece minimize butonu kalsın, footer'da yazı olmasın -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
    
    // Retry AI Feature in modal
    window.retryAIFeature = function(featureSlug, featureName) {
        // Reset content to loading state
        const content = document.getElementById('modern-ai-content');
        if (content) {
            content.innerHTML = `
                <div class="text-center py-5" id="ai-loading">
                    <div class="loading-spinner mx-auto mb-3" style="width: 48px; height: 48px; border-width: 4px;"></div>
                    <h5 class="text-muted mb-2">${featureName}</h5>
                    <p class="text-muted small">AI analizi yeniden çalışıyor...</p>
                </div>
            `;
        }
        
        // Re-execute feature
        executeAIFeatureForModernModal(featureSlug, featureName);
    }
    
    // Execute AI Feature for Modern Modal Display
    function executeAIFeatureForModernModal(featureSlug, featureName) {
        // Create specific prompt for the feature
        const featurePrompts = {
            'seo-puan-analizi': 'Bu sayfanın SEO puanını analiz et ve detaylı rapor ver',
            'hizli-seo-analizi': 'Bu sayfada hızlı SEO analizi yap',
            'anahtar-kelime-analizi': 'Bu sayfanın anahtar kelimelerini analiz et',
            'icerik-optimizasyonu': 'Bu sayfanın içeriğini optimize etmek için öneriler sun',
            'icerik-genisletme': 'Bu sayfanın içeriğini genişlet',
            'icerik-ozetleme': 'Bu sayfanın içeriğini özetle',
            'baslik-uretici': 'Bu sayfa için alternatif başlıklar öner',
            'meta-aciklama-uretici': 'Bu sayfa için meta açıklama oluştur',
            'alt-baslik-onerileri': 'Bu sayfa için alt başlıklar öner',
            'sayfa-gelistirme-onerileri': 'Bu sayfayı geliştirmek için öneriler sun',
            'kullanici-deneyimi-analizi': 'Bu sayfanın kullanıcı deneyimini analiz et',
            'icerik-kalite-skoru': 'Bu sayfanın içerik kalitesini skorla',
            'coklu-dil-cevirisi': 'Bu sayfayı farklı dillere çevir',
            'dil-kalitesi-kontrolu': 'Bu sayfanın dil kalitesini kontrol et',
            'rekabet-analizi': 'Bu sayfa için rekabet analizi yap',
            'trending-konu-onerileri': 'Bu sayfa için trending konu önerileri',
            'schema-markup-onerileri': 'Bu sayfa için schema markup önerileri sun',
            'link-onerileri': 'Bu sayfa için link önerileri sun'
        };

        const customPrompt = featurePrompts[featureSlug] || `${featureName} için analiz yap`;
        
        // Make AI API call
        const aiApiUrl = '/admin/ai/test-feature';
        
        fetch(aiApiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                feature_slug: featureSlug,
                custom_prompt: customPrompt
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('AI API Response:', data);
            
            const contentArea = document.getElementById('modern-ai-content');
            if (!contentArea) return;
            
            if (data.success) {
                // Success - Display AI response beautifully
                const formattedResponse = formatAIResponseForDisplay(data.response);
                
                // AI HTML response'u direkt kullan, wrapper ekleme
                contentArea.innerHTML = `
                    <!-- Success Indicator -->
                    <div class="alert alert-success border-0 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="alert-text">
                                <strong>${featureName}</strong> analizi tamamlandı
                            </div>
                        </div>
                    </div>
                    
                    <!-- Direct AI Response (Clean HTML) -->
                    ${formattedResponse}
                `;
                
                // AI sonucunu history'ye ekle
                addToAIHistory(featureName, formattedResponse);
            } else {
                // Error - Show error message with retry option
                const errorMessage = data.message || data.error || 'Bilinmeyen bir hata oluştu.';
                contentArea.innerHTML = `
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Hata!</strong>
                            <div class="mt-2">${errorMessage}</div>
                        </div>
                        
                        <button class="btn btn-primary" onclick="retryAIFeature('${featureSlug}', '${featureName}')">
                            <i class="fas fa-redo me-1"></i>Tekrar Dene
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('AI API Error:', error);
            
            const contentArea = document.getElementById('modern-ai-content');
            if (contentArea) {
                contentArea.innerHTML = `
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Bağlantı Hatası!</strong>
                            <div class="mt-2">AI servisi ile bağlantı kurulamadı.</div>
                        </div>
                        
                        <button class="btn btn-primary" onclick="retryAIFeature('${featureSlug}', '${featureName}')">
                            <i class="fas fa-redo me-1"></i>Tekrar Dene
                        </button>
                    </div>
                `;
            }
        });
    }
    
    // Format AI Response for better display (JSON-aware, code highlighting, markdown, etc.)
    function formatAIResponseForDisplay(response) {
        if (!response) return 'Yanıt alınamadı.';
        
        // Check if response is JSON and format it nicely
        try {
            const jsonData = JSON.parse(response);
            return formatJSONResponse(jsonData);
        } catch (e) {
            // Not JSON, check if it's already HTML
        }
        
        // HTML Detection - AI'dan gelen modern HTML'yi direkt kullan
        const htmlTags = ['<div', '<card', '<span', '<ul', '<li', '<h1', '<h2', '<h3', '<h4', '<h5', '<h6', '<p', '<i class="fas'];
        const isHTML = htmlTags.some(tag => response.includes(tag));
        
        if (isHTML) {
            // AI'dan gelen HTML direkt kullanıyoruz, ek formatting yapma
            return response;
        }
        
        // Sadece plain text için formatting yap
        // Convert line breaks to HTML
        let formatted = response.replace(/\n/g, '<br>');
        
        // Format code blocks (```code```)
        formatted = formatted.replace(/```([\s\S]*?)```/g, '<div class="code-block" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 12px; margin: 12px 0; font-family: monospace; font-size: 13px; overflow-x: auto; white-space: pre-wrap;">$1</div>');
        
        // Format inline code (`code`)
        formatted = formatted.replace(/`([^`]+)`/g, '<code style="background: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-family: monospace; font-size: 13px;">$1</code>');
        
        // Format bold text (**text**)
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Format headers (### Header)
        formatted = formatted.replace(/### (.*?)<br>/g, '<h5 style="color: var(--tblr-primary); margin: 20px 0 10px 0; font-weight: 600;">$1</h5>');
        formatted = formatted.replace(/## (.*?)<br>/g, '<h4 style="color: var(--tblr-primary); margin: 20px 0 12px 0; font-weight: 600;">$1</h4>');
        formatted = formatted.replace(/# (.*?)<br>/g, '<h3 style="color: var(--tblr-primary); margin: 20px 0 15px 0; font-weight: 600;">$1</h3>');
        
        // Format lists (- item)
        formatted = formatted.replace(/- (.*?)<br>/g, '<div style="margin: 6px 0; padding-left: 20px; position: relative;"><span style="position: absolute; left: 0; color: var(--tblr-primary);">•</span>$1</div>');
        
        return formatted;
    }
    
    // Format JSON Response as beautiful cards
    function formatJSONResponse(jsonData) {
        let html = '';
        
        // Hero Score Section
        if (jsonData.hero_score) {
            const score = jsonData.hero_score;
            const statusClass = score.status === 'success' ? 'success' : score.status === 'warning' ? 'warning' : 'primary';
            html += `
                <div class="alert alert-${statusClass} mb-4">
                    <div class="d-flex align-items-center">
                        <i class="${score.icon || 'fas fa-chart-line'} me-3 fs-3"></i>
                        <div>
                            <h4 class="alert-heading mb-1">${score.value}</h4>
                            <div>${score.label}</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Analysis Section
        if (jsonData.analysis && jsonData.analysis.items) {
            html += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">${jsonData.analysis.title || 'Analiz Sonuçları'}</h5>
                    </div>
                    <div class="card-body">
            `;
            
            jsonData.analysis.items.forEach(item => {
                const statusIcon = item.status === 'success' ? 'fas fa-check-circle text-success' : 
                                 item.status === 'warning' ? 'fas fa-exclamation-triangle text-warning' : 
                                 'fas fa-info-circle text-primary';
                html += `
                    <div class="row mb-3">
                        <div class="col">
                            <div class="d-flex align-items-start">
                                <i class="${statusIcon} me-2 mt-1"></i>
                                <div>
                                    <strong>${item.label}</strong>
                                    <div class="text-muted small mt-1">${item.detail}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div></div>';
        }
        
        // Recommendations Section
        if (jsonData.recommendations && jsonData.recommendations.cards) {
            html += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">${jsonData.recommendations.title || 'Öneriler'}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
            `;
            
            jsonData.recommendations.cards.forEach(card => {
                const priorityClass = card.priority === 'high' ? 'danger' : card.priority === 'medium' ? 'warning' : 'info';
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card border border-${priorityClass}">
                            <div class="card-body p-3">
                                <h6 class="card-title text-${priorityClass}">${card.title}</h6>
                                <p class="card-text small mb-2">${card.action}</p>
                                <span class="badge bg-${priorityClass}">${card.priority} priority</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div></div></div>';
        }
        
        // Technical Details Section
        if (jsonData.technical_details) {
            html += `
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">${jsonData.technical_details.title || 'Teknik Detaylar'}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${jsonData.technical_details.content}</p>
                    </div>
                </div>
            `;
        }
        
        return html || '<div class="text-muted">JSON formatı tanımlanamadı.</div>';
    }
    
    // Copy AI Response to clipboard
    window.copyAIResponse = function() {
        const content = document.querySelector('#modern-ai-content .ai-content-display');
        if (!content) return;
        
        // Get text content without HTML tags
        const textContent = content.innerText || content.textContent;
        
        navigator.clipboard.writeText(textContent).then(() => {
            // Show success message
            showAINotification('Başarılı', 'AI yanıtı panoya kopyalandı!', 'success');
        }).catch(() => {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = textContent;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            showAINotification('Başarılı', 'AI yanıtı panoya kopyalandı!', 'success');
        });
    }
    
    // Download AI Response as text file
    window.downloadAIResponse = function(featureName) {
        const content = document.querySelector('#modern-ai-content .ai-content-display');
        if (!content) return;
        
        const textContent = content.innerText || content.textContent;
        const filename = `${featureName.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.txt`;
        
        const blob = new Blob([textContent], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        showAINotification('Başarılı', 'AI yanıtı dosya olarak indirildi!', 'success');
    }
    
    function findLeftPanelElement() {
        // Try different selectors to find the left AI results panel
        const possibleSelectors = [
            '.ai-analysis-results',
            '.ai-results-panel',
            '[id*="ai-results"]',
            '[class*="ai-analiz"]',
            '.analysis-panel',
            '.sidebar-analysis',
            '.ai-sidebar-results'
        ];
        
        for (const selector of possibleSelectors) {
            leftPanelElement = document.querySelector(selector);
            if (leftPanelElement) {
                console.log('Found left panel:', selector);
                break;
            }
        }
        
        // If no specific panel found, try to find any element containing "AI analiz sonuçları"
        if (!leftPanelElement) {
            const allElements = document.querySelectorAll('*');
            for (const element of allElements) {
                if (element.textContent && element.textContent.includes('AI analiz sonuçları')) {
                    leftPanelElement = element;
                    console.log('Found AI results element by text content');
                    break;
                }
            }
        }
        
        if (leftPanelElement) {
            // Show loading state in left panel
            showLeftPanelLoading(leftPanelElement, featureName);
            
            // Execute AI feature via API
            executeAIFeatureForLeftPanel(featureSlug, featureName, leftPanelElement);
        } else {
            // Create embedded sidebar panel if no left panel found
            console.log('No left panel found, creating embedded sidebar panel');
            showAINotification('Bilgi', 'AI sonuçları yan panelde gösterilecek', 'info');
            createModernAIModal(featureSlug, featureName);
        }
    }
    
    // Create Embedded AI Panel (no navigation, stays on current page)
    function createEmbeddedAIPanel(featureSlug, featureName) {
        console.log('🪟 Creating single AI panel for:', featureSlug);
        
        // Create Windows-style taskbar if it doesn't exist
        initializeAITaskbar();
        
        // SINGLE PANEL MODE: Smart panel management
        const existingPanels = document.querySelectorAll('.ai-window-panel');
        
        // Check if same feature is already open (and maybe minimized)
        const existingPanel = document.getElementById(`ai-panel-${featureSlug}`);
        if (existingPanel) {
            // Same feature clicked - just restore if minimized
            const isMinimized = existingPanel.getAttribute('data-minimized') === 'true';
            if (isMinimized) {
                console.log('🔄 Restoring minimized panel:', featureSlug);
                restoreAIPanel(`ai-panel-${featureSlug}`);
                return;
            } else {
                console.log('📍 Panel already open and visible, focusing:', featureSlug);
                focusAIPanel(existingPanel);
                return;
            }
        }
        
        // Different feature - close all existing panels first
        existingPanels.forEach(panel => {
            closeAIPanel(panel.id);
        });
        
        // Generate unique panel ID
        const panelId = `ai-panel-${featureSlug}`;
        
        // Wait for cleanup then create new panel
        setTimeout(() => {
            createSingleAIPanel(panelId, featureSlug, featureName);
        }, 100);
    }
    
    // Create Single AI Panel (bigger and centered)
    function createSingleAIPanel(panelId, featureSlug, featureName) {
        
        // Create Windows-style AI panel
        const panel = document.createElement('div');
        panel.id = panelId;
        panel.className = 'ai-window-panel';
        panel.setAttribute('data-feature', featureSlug);
        panel.setAttribute('data-minimized', 'false');
        
        panel.innerHTML = `
            <div class="ai-window-content" style="
                position: fixed; bottom: 70px; left: 20px; 
                width: 500px; height: 600px; 
                background: var(--tblr-bg-surface); 
                border: 1px solid var(--tblr-border-color);
                border-radius: 8px 8px 0 0;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                z-index: 9998; 
                display: flex; flex-direction: column;
                transform: translateY(100%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                resize: both; min-width: 400px; min-height: 300px; max-width: 800px; max-height: 900px;
            ">
                <!-- Window Header with controls -->
                <div class="ai-window-header" style="
                    background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%);
                    color: white; padding: 12px 16px; cursor: move;
                    display: flex; align-items: center; justify-content: space-between;
                    border-radius: 8px 8px 0 0;
                ">
                    <div class="window-title" style="display: flex; align-items: center;">
                        <i class="fas fa-robot me-2"></i>
                        <span style="font-weight: 600; font-size: 14px;">${featureName}</span>
                    </div>
                    <div class="window-controls" style="display: flex; gap: 4px;">
                        <button onclick="minimizeAIPanel('${panelId}')" style="
                            background: rgba(255,255,255,0.2); border: none; color: white;
                            width: 24px; height: 24px; border-radius: 3px; cursor: pointer;
                            display: flex; align-items: center; justify-content: center;
                            font-size: 10px; transition: all 0.2s;
                        " title="Minimize">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button onclick="closeAIPanel('${panelId}')" style="
                            background: rgba(255,255,255,0.2); border: none; color: white;
                            width: 24px; height: 24px; border-radius: 3px; cursor: pointer;
                            display: flex; align-items: center; justify-content: center;
                            font-size: 10px; transition: all 0.2s;
                        " title="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Window Content -->
                <div class="ai-panel-content" id="ai-panel-content-${featureSlug}" style="
                    flex: 1; padding: 16px; overflow-y: auto;
                    display: flex; align-items: center; justify-content: center;
                ">
                    <!-- Loading State -->
                    <div class="ai-panel-loading" style="text-align: center;">
                        <div class="loading-spinner" style="margin: 0 auto 12px auto; width: 32px; height: 32px; border-width: 3px;"></div>
                        <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 6px; font-size: 13px;">${featureName}</div>
                        <div style="color: var(--tblr-muted); font-size: 11px;">AI analizi çalışıyor...</div>
                    </div>
                </div>
                
                <!-- Status Bar -->
                <div class="ai-panel-status" style="
                    padding: 8px 16px; border-top: 1px solid var(--tblr-border-color);
                    background: var(--tblr-bg-surface-secondary); font-size: 11px; color: var(--tblr-muted);
                    border-radius: 0 0 8px 8px;
                ">
                    <i class="fas fa-robot me-1"></i>AI Panel - ${new Date().toLocaleTimeString('tr-TR')}
                </div>
            </div>
        `;
        
        // Add to body and initialize
        document.body.appendChild(panel);
        
        // Create taskbar button
        const taskbarButton = createTaskbarButton(panelId, featureName, featureSlug);
        
        // Make panel draggable
        makeWindowDraggable(panel);
        
        // Show panel with animation (centered)
        setTimeout(() => {
            panel.querySelector('.ai-window-content').style.transform = 'translateY(0)';
        }, 100);
        
        // Execute AI feature
        executeAIFeatureForModernModal(featureSlug, featureName);
    }
    
    // Execute AI feature for embedded panel display  
    function executeAIFeatureForEmbeddedPanel(featureSlug, featureName) {
        // Create specific prompt for the feature
        const featurePrompts = {
            'seo-puan-analizi': 'Bu sayfanın SEO puanını analiz et ve detaylı rapor ver',
            'hizli-seo-analizi': 'Bu sayfada hızlı SEO analizi yap',
            'anahtar-kelime-analizi': 'Bu sayfanın anahtar kelimelerini analiz et',
            'icerik-optimizasyonu': 'Bu sayfanın içeriğini optimize etmek için öneriler sun',
            'icerik-genisletme': 'Bu sayfanın içeriğini genişlet',
            'icerik-ozetleme': 'Bu sayfanın içeriğini özetle',
            'baslik-uretici': 'Bu sayfa için alternatif başlıklar öner',
            'meta-aciklama-uretici': 'Bu sayfa için meta açıklama oluştur',
            'alt-baslik-onerileri': 'Bu sayfa için alt başlıklar öner',
            'sayfa-gelistirme-onerileri': 'Bu sayfayı geliştirmek için öneriler sun',
            'kullanici-deneyimi-analizi': 'Bu sayfanın kullanıcı deneyimini analiz et',
            'icerik-kalite-skoru': 'Bu sayfanın içerik kalitesini skorla',
            'coklu-dil-cevirisi': 'Bu sayfayı farklı dillere çevir',
            'dil-kalitesi-kontrolu': 'Bu sayfanın dil kalitesini kontrol et',
            'rekabet-analizi': 'Bu sayfa için rekabet analizi yap',
            'trending-konu-onerileri': 'Bu konuda trending öneriler sun',
            'schema-markup-onerileri': 'Bu sayfa için schema markup önerileri sun',
            'link-onerileri': 'Bu sayfa için link önerileri sun'
        };
        
        const prompt = featurePrompts[featureSlug] || `${featureName} analizini yap`;
        
        fetch('/admin/ai/test-feature', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ 
                feature_slug: featureSlug,
                custom_prompt: prompt
            })
        })
        .then(response => {
            console.log('🌐 HTTP Response Status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('🎯 AI API Response received:', data);
            console.log('📊 Response fields check:', {
                success: data.success,
                hasMessage: !!data.message,
                hasResponse: !!data.response, 
                hasContent: !!data.content,
                messageLength: data.message ? data.message.length : 0,
                responseLength: data.response ? data.response.length : 0,
                contentLength: data.content ? data.content.length : 0
            });
            
            if (data.success && (data.response || data.formatted_response)) {
                // AI Feature API response format
                const rawContent = data.formatted_response || data.response;
                
                // Safe content extraction and type checking
                let content;
                if (typeof rawContent === 'string') {
                    content = rawContent;
                } else if (rawContent && typeof rawContent === 'object') {
                    // If it's an object, try to extract string content
                    content = rawContent.content || rawContent.text || JSON.stringify(rawContent);
                } else {
                    content = String(rawContent || 'Yanıt formatı okunamadı');
                }
                
                console.log('✅ AI Feature content type:', typeof rawContent);
                console.log('✅ Raw content preview:', rawContent);
                console.log('✅ Processed content preview:', content ? content.substring(0, 100) + '...' : 'empty content');
                console.log('🎯 AI Feature data:', {
                    success: data.success,
                    hasResponse: !!data.response,
                    hasFormattedResponse: !!data.formatted_response,
                    contentType: typeof rawContent,
                    contentLength: content ? content.length : 0,
                    feature: data.feature,
                    tokensUsed: data.tokens_used
                });
                showEmbeddedPanelResults(featureName, content, featureSlug, data.feature);
                console.log('✅ showEmbeddedPanelResults called successfully');
            } else {
                console.error('❌ AI Feature API response format error:', data);
                showEmbeddedPanelError(featureName, data.message || 'AI Feature yanıt formatı hatalı', featureSlug);
            }
        })
        .catch(error => {
            console.error('❌ Embedded Panel AI Error:', error);
            showEmbeddedPanelError(featureName, 'Bağlantı hatası: ' + error.message, featureSlug);
        });
    }
    
    // Show AI results in embedded panel
    function showEmbeddedPanelResults(featureName, content, featureSlug, featureData = null) {
        console.log('🎨 showEmbeddedPanelResults called with:', {
            featureName: featureName,
            contentType: typeof content,
            contentLength: content ? content.length : 0,
            contentPreview: (content && typeof content === 'string') ? content.substring(0, 150) : 'not a string: ' + String(content).substring(0, 100),
            featureSlug: featureSlug
        });
        
        const timestamp = new Date().toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Ensure content is a safe string
        const safeContent = (content && typeof content === 'string') ? content : String(content || 'İçerik yüklenemedi');
        
        const contentElement = document.getElementById(`ai-panel-content-${featureSlug}`);
        console.log('🔍 Panel content element found:', !!contentElement);
        
        if (contentElement) {
            contentElement.innerHTML = `
                <div class="ai-panel-results" style="width: 100%; height: 100%;">
                    <div style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--tblr-border-color);">
                        <div style="font-weight: 600; font-size: 14px; color: var(--tblr-body-color); margin-bottom: 4px;">
                            📊 Analiz Raporu
                        </div>
                        <div style="font-size: 11px; color: var(--tblr-muted);">${timestamp}</div>
                    </div>
                    
                    <div class="ai-panel-content-wrapper" style="
                        background: var(--tblr-bg-surface-secondary); 
                        border: 1px solid var(--tblr-border-color); 
                        border-radius: 6px; padding: 16px; 
                        font-size: 13px; line-height: 1.6; 
                        color: var(--tblr-body-color);
                        max-height: calc(100vh - 250px); overflow-y: auto;
                        margin-bottom: 16px;
                    ">
                        ${safeContent.replace(/\n/g, '<br>')}
                    </div>
                    
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button onclick="copyToClipboard('${safeContent.replace(/'/g, "\\'")}'); alert('Sonuçlar panoya kopyalandı!');" 
                                style="background: var(--tblr-primary); color: white; border: none; padding: 8px 12px; 
                                       border-radius: 4px; font-size: 11px; cursor: pointer; flex: 1;">
                            <i class="fas fa-copy me-1"></i>Kopyala
                        </button>
                        <button onclick="downloadAIResults('${featureName}', '${safeContent.replace(/'/g, "\\'")}');" 
                                style="background: var(--tblr-success); color: white; border: none; padding: 8px 12px; 
                                       border-radius: 4px; font-size: 11px; cursor: pointer; flex: 1;">
                            <i class="fas fa-download me-1"></i>İndir
                        </button>
                    </div>
                </div>
            `;
            console.log('✅ Embedded panel content updated successfully');
        } else {
            console.error('❌ Panel content element not found!');
        }
    }
    
    // Show error in embedded panel
    function showEmbeddedPanelError(featureName, errorMessage, featureSlug = '') {
        const contentElement = document.getElementById(`ai-panel-content-${featureSlug}`);
        if (contentElement) {
            contentElement.innerHTML = `
                <div class="ai-panel-error" style="text-align: center; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                    <div style="color: var(--tblr-danger); margin-bottom: 12px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 36px;"></i>
                    </div>
                    <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 8px; font-size: 14px;">${featureName}</div>
                    <div style="color: var(--tblr-muted); font-size: 12px; margin-bottom: 16px;">${errorMessage}</div>
                    <div style="display: flex; gap: 8px;">
                        ${featureSlug ? `
                        <button onclick="executeAIFeatureForModernModal('${featureSlug}', '${featureName}')" 
                                style="background: var(--tblr-primary); color: white; border: none; padding: 8px 12px; 
                                       border-radius: 4px; font-size: 11px; cursor: pointer;">
                            <i class="fas fa-redo me-1"></i>Tekrar Dene
                        </button>
                        ` : ''}
                        <!-- Kapat butonu kaldırıldı, sadece header'daki minimize yeterli -->
                    </div>
                </div>
            `;
        }
    }
    
    // Windows-style AI Panel Management Functions
    
    // Initialize AI Taskbar with Dynamic Layout
    function initializeAITaskbar() {
        let taskbar = document.getElementById('ai-taskbar');
        if (!taskbar) {
            taskbar = document.createElement('div');
            taskbar.id = 'ai-taskbar';
            taskbar.style.cssText = `
                position: fixed; bottom: 20px; left: 20px; right: 20px; 
                height: 50px; z-index: 9997; pointer-events: none;
                display: flex; align-items: end; justify-content: flex-start; 
                gap: 10px; flex-wrap: wrap; overflow-x: auto;
            `;
            document.body.appendChild(taskbar);
        }
    }
    
    // Create Taskbar Button and Position Panel Above It
    function createTaskbarButton(panelId, featureName, featureSlug) {
        const taskbar = document.getElementById('ai-taskbar');
        if (!taskbar) return;
        
        const button = document.createElement('div');
        button.id = `taskbar-${panelId}`;
        button.className = 'ai-taskbar-button';
        button.style.cssText = `
            background: var(--tblr-bg-surface); border: 1px solid var(--tblr-border-color);
            padding: 8px 12px; border-radius: 6px 6px 0 0; cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); pointer-events: all;
            display: flex; align-items: center; gap: 8px; min-width: 120px; max-width: 200px;
            transition: all 0.2s; transform: translateY(0); position: relative;
        `;
        button.innerHTML = `
            <i class="fas fa-robot" style="color: var(--tblr-primary); font-size: 12px;"></i>
            <span style="font-size: 11px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${featureName}
            </span>
        `;
        
        button.onclick = () => toggleAIPanel(panelId);
        taskbar.appendChild(button);
        
        // Position panel above this button
        setTimeout(() => {
            positionPanelAboveButton(panelId, button);
        }, 50);
        
        return button;
    }
    
    // Smart Panel Positioning System - Cascade & Anti-Overlap
    function positionPanelAboveButton(panelId, button) {
        const panel = document.getElementById(panelId);
        if (!panel || !button) return;
        
        const content = panel.querySelector('.ai-window-content');
        if (!content) return;
        
        // Get existing panels for collision detection
        const existingPanels = document.querySelectorAll('.ai-window-panel .ai-window-content');
        const panelWidth = 380;
        const panelHeight = 500;
        const minDistance = 20; // Minimum distance between panels
        
        // Get button position as starting point
        const buttonRect = button.getBoundingClientRect();
        let baseLeft = Math.max(20, buttonRect.left - (panelWidth / 2) + (buttonRect.width / 2));
        let baseBottom = 70; // Above taskbar
        
        // Ensure base position doesn't go off screen
        const maxLeft = window.innerWidth - panelWidth - 20;
        baseLeft = Math.min(baseLeft, maxLeft);
        
        // Find non-overlapping position using cascade system
        let finalLeft = baseLeft;
        let finalBottom = baseBottom;
        let attempts = 0;
        const maxAttempts = 10;
        
        while (attempts < maxAttempts) {
            let hasOverlap = false;
            
            // Check collision with existing panels
            for (let existingPanel of existingPanels) {
                if (existingPanel.parentElement.id === panelId) continue; // Skip self
                
                const existingRect = existingPanel.getBoundingClientRect();
                const existingLeft = parseInt(existingPanel.style.left) || existingRect.left;
                const existingBottom = parseInt(existingPanel.style.bottom) || (window.innerHeight - existingRect.bottom);
                
                // Check if panels would overlap
                const horizontalOverlap = (finalLeft < existingLeft + panelWidth + minDistance) && 
                                        (finalLeft + panelWidth + minDistance > existingLeft);
                const verticalOverlap = (finalBottom < existingBottom + panelHeight + minDistance) && 
                                      (finalBottom + panelHeight + minDistance > existingBottom);
                
                if (horizontalOverlap && verticalOverlap) {
                    hasOverlap = true;
                    break;
                }
            }
            
            if (!hasOverlap) break; // Found good position
            
            // Try cascade positions
            if (attempts < 5) {
                // Try horizontal cascade
                finalLeft += (panelWidth / 2) + minDistance;
                if (finalLeft + panelWidth > window.innerWidth - 20) {
                    finalLeft = 20; // Reset to left
                    finalBottom += 60; // Move up
                }
            } else {
                // Try vertical cascade
                finalBottom += 80;
                if (finalBottom + panelHeight > window.innerHeight - 100) {
                    finalBottom = baseBottom; // Reset to base
                    finalLeft = 20 + (attempts - 5) * 100; // Spread horizontally
                }
            }
            
            attempts++;
        }
        
        // Apply final position
        content.style.left = `${finalLeft}px`;
        content.style.bottom = `${finalBottom}px`;
        
        console.log('🎯 Smart Panel Positioned:', {
            panelId: panelId,
            buttonLeft: buttonRect.left,
            finalLeft: finalLeft,
            finalBottom: finalBottom,
            attempts: attempts,
            existingPanelsCount: existingPanels.length - 1 // Exclude self
        });
    }
    
    // Toggle AI Panel (minimize/restore)
    window.toggleAIPanel = function(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        
        const isMinimized = panel.getAttribute('data-minimized') === 'true';
        if (isMinimized) {
            restoreAIPanel(panelId);
        } else {
            minimizeAIPanel(panelId);
        }
    }
    
    // Minimize AI Panel
    window.minimizeAIPanel = function(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        
        const content = panel.querySelector('.ai-window-content');
        if (content) {
            content.style.transform = 'translateY(100%)';
            panel.setAttribute('data-minimized', 'true');
            
            // Update taskbar button state
            const taskbarButton = document.getElementById(`taskbar-${panelId}`);
            if (taskbarButton) {
                taskbarButton.style.background = 'var(--tblr-bg-surface-secondary)';
            }
        }
    }
    
    // Restore AI Panel
    window.restoreAIPanel = function(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        
        const content = panel.querySelector('.ai-window-content');
        if (content) {
            content.style.transform = 'translateY(0)';
            panel.setAttribute('data-minimized', 'false');
            focusAIPanel(panel);
            
            // Update taskbar button state
            const taskbarButton = document.getElementById(`taskbar-${panelId}`);
            if (taskbarButton) {
                taskbarButton.style.background = 'var(--tblr-bg-surface)';
            }
        }
    }
    
    // Close AI Panel
    window.closeAIPanel = function(panelId) {
        const panel = document.getElementById(panelId);
        if (panel) {
            // Animate out
            const content = panel.querySelector('.ai-window-content');
            if (content) {
                content.style.transform = 'translateY(100%)';
            }
            
            // Remove taskbar button
            const taskbarButton = document.getElementById(`taskbar-${panelId}`);
            if (taskbarButton) {
                taskbarButton.remove();
            }
            
            // Remove panel after animation
            setTimeout(() => {
                panel.remove();
                
                // Clean up taskbar if empty
                const taskbar = document.getElementById('ai-taskbar');
                if (taskbar && taskbar.children.length === 0) {
                    taskbar.remove();
                }
            }, 300);
        }
    }
    
    // Focus AI Panel (bring to front)
    function focusAIPanel(panel) {
        // Bring to front
        panel.style.zIndex = '9999';
        
        // Reset other panels z-index
        document.querySelectorAll('.ai-window-panel').forEach(p => {
            if (p !== panel) {
                p.style.zIndex = '9998';
            }
        });
    }
    
    // Make Window Draggable
    function makeWindowDraggable(panel) {
        const header = panel.querySelector('.ai-window-header');
        let isDragging = false;
        let currentX, currentY, initialX, initialY, xOffset = 0, yOffset = 0;
        
        header.addEventListener('mousedown', dragStart);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', dragEnd);
        
        function dragStart(e) {
            initialX = e.clientX - xOffset;
            initialY = e.clientY - yOffset;
            
            if (e.target === header || header.contains(e.target)) {
                isDragging = true;
                focusAIPanel(panel);
            }
        }
        
        function drag(e) {
            if (isDragging) {
                e.preventDefault();
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;
                xOffset = currentX;
                yOffset = currentY;
                
                const content = panel.querySelector('.ai-window-content');
                content.style.transform = `translate(${currentX}px, ${currentY}px)`;
            }
        }
        
        function dragEnd() {
            isDragging = false;
        }
    }
    
    // Legacy function for backward compatibility
    window.closeEmbeddedAIPanel = function() {
        // Find and close all AI panels
        document.querySelectorAll('.ai-window-panel').forEach(panel => {
            closeAIPanel(panel.id);
        });
    }
    
    // Create AI Results Overlay (iframe-like modal)
    function createAIResultsOverlay(featureSlug, featureName) {
        console.log('🎯 Creating AI results overlay for:', featureSlug);
        
        // Remove existing overlay if any
        const existingOverlay = document.getElementById('ai-results-overlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }
        
        // Create overlay container
        const overlay = document.createElement('div');
        overlay.id = 'ai-results-overlay';
        overlay.innerHTML = `
            <div class="ai-overlay-backdrop" style="
                position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
                background: rgba(0,0,0,0.5); z-index: 10000; 
                backdrop-filter: blur(4px);
            ">
                <div class="ai-overlay-container" style="
                    position: absolute; top: 50%; left: 50%; 
                    transform: translate(-50%, -50%);
                    width: 90vw; max-width: 800px; max-height: 80vh;
                    background: var(--tblr-bg-surface); 
                    border-radius: 12px; 
                    box-shadow: var(--tblr-shadow-xl);
                    border: 1px solid var(--tblr-border-color);
                    display: flex; flex-direction: column;
                ">
                    <!-- Header -->
                    <div class="ai-overlay-header" style="
                        background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%);
                        color: white; padding: 20px; border-radius: 12px 12px 0 0;
                        display: flex; align-items: center; justify-content: space-between;
                    ">
                        <div>
                            <div style="font-weight: 700; font-size: 18px; margin-bottom: 4px;">
                                <i class="fas fa-robot me-2"></i>${featureName}
                            </div>
                            <div style="font-size: 12px; opacity: 0.9;">AI Analiz Sonuçları</div>
                        </div>
                        <button onclick="closeAIResultsOverlay()" style="
                            background: rgba(255,255,255,0.2); border: none; color: white;
                            width: 36px; height: 36px; border-radius: 50%; cursor: pointer;
                            display: flex; align-items: center; justify-content: center;
                            transition: all 0.2s;
                        ">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div class="ai-overlay-content" id="ai-overlay-content" style="
                        flex: 1; padding: 24px; overflow-y: auto;
                        min-height: 400px; display: flex; align-items: center; justify-content: center;
                    ">
                        <!-- Loading State -->
                        <div class="ai-overlay-loading" style="text-align: center;">
                            <div class="loading-spinner" style="margin: 0 auto 16px auto; width: 48px; height: 48px; border-width: 4px;"></div>
                            <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 8px; font-size: 16px;">${featureName}</div>
                            <div style="color: var(--tblr-muted); font-size: 14px;">AI analizi çalışıyor, lütfen bekleyin...</div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="ai-overlay-footer" style="
                        padding: 16px 24px; border-top: 1px solid var(--tblr-border-color);
                        background: var(--tblr-bg-surface-secondary); border-radius: 0 0 12px 12px;
                        display: flex; align-items: center; justify-content: between;
                    ">
                        <div style="font-size: 12px; color: var(--tblr-muted);">
                            <i class="fas fa-info-circle me-2"></i>AI tarafından oluşturulan analiz sonucu
                        </div>
                        <div style="margin-left: auto;">
                            <!-- Kapat butonu kaldırıldı -->
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(overlay);
        
        // Add click outside to close
        overlay.querySelector('.ai-overlay-backdrop').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAIResultsOverlay();
            }
        });
        
        // Execute AI feature
        executeAIFeatureForOverlay(featureSlug, featureName);
    }
    
    // Execute AI feature for overlay display
    function executeAIFeatureForOverlay(featureSlug, featureName) {
        // Create specific prompt for the feature
        const featurePrompts = {
            'seo-puan-analizi': 'Bu sayfanın SEO puanını analiz et ve detaylı rapor ver',
            'hizli-seo-analizi': 'Bu sayfada hızlı SEO analizi yap',
            'anahtar-kelime-analizi': 'Bu sayfanın anahtar kelimelerini analiz et',
            'icerik-optimizasyonu': 'Bu sayfanın içeriğini optimize etmek için öneriler sun',
            'icerik-genisletme': 'Bu sayfanın içeriğini genişlet',
            'icerik-ozetleme': 'Bu sayfanın içeriğini özetle',
            'baslik-uretici': 'Bu sayfa için alternatif başlıklar öner',
            'meta-aciklama-uretici': 'Bu sayfa için meta açıklama oluştur',
            'alt-baslik-onerileri': 'Bu sayfa için alt başlıklar öner',
            'sayfa-gelistirme-onerileri': 'Bu sayfayı geliştirmek için öneriler sun',
            'kullanici-deneyimi-analizi': 'Bu sayfanın kullanıcı deneyimini analiz et',
            'icerik-kalite-skoru': 'Bu sayfanın içerik kalitesini skorla',
            'coklu-dil-cevirisi': 'Bu sayfayı farklı dillere çevir',
            'dil-kalitesi-kontrolu': 'Bu sayfanın dil kalitesini kontrol et',
            'rekabet-analizi': 'Bu sayfa için rekabet analizi yap',
            'trending-konu-onerileri': 'Bu konuda trending öneriler sun',
            'schema-markup-onerileri': 'Bu sayfa için schema markup önerileri sun',
            'link-onerileri': 'Bu sayfa için link önerileri sun'
        };
        
        const prompt = featurePrompts[featureSlug] || `${featureName} analizini yap`;
        
        if (window.fetch) {
            fetch('/admin/ai/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    message: prompt,
                    conversation_id: null,
                    feature_context: featureSlug
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && (data.message || data.response || data.content)) {
                    const content = data.message || data.response || data.content;
                    showOverlayResults(featureName, content, featureSlug);
                    showAINotification('Tamamlandı', `${featureName} tamamlandı!`, 'success');
                } else {
                    showOverlayError(featureName, 'AI yanıt veremedi');
                    showAINotification('Hata', 'AI yanıt veremedi', 'error');
                }
            })
            .catch(error => {
                console.error('Overlay AI Error:', error);
                showOverlayError(featureName, 'Bağlantı hatası');
                showAINotification('Hata', 'Bağlantı hatası', 'error');
            });
        } else {
            // Demo mode
            setTimeout(() => {
                showOverlayResults(featureName, `${featureName} demo sonuçları burada görünüyor.\n\nBu demo modunda çalışmaktadır. Gerçek AI entegrasyonu yakında aktif olacak!\n\n🎯 Analiz detayları burada yer alacak.\n📊 Skorlamalar ve öneriler listelenecek.\n✨ Profesyonel raporlama sistemi hazır.`, featureSlug);
                showAINotification('Demo', `${featureName} demo modunda`, 'info');
            }, 2000);
        }
    }
    
    // Show AI results in overlay
    function showOverlayResults(featureName, content, featureSlug) {
        const timestamp = new Date().toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const contentElement = document.getElementById('ai-overlay-content');
        if (contentElement) {
            contentElement.innerHTML = `
                <div class="ai-overlay-results" style="width: 100%;">
                    <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--tblr-border-color);">
                        <div style="display: flex; align-items: center; justify-content: between; margin-bottom: 8px;">
                            <div style="font-weight: 600; font-size: 16px; color: var(--tblr-body-color);">
                                📊 Analiz Raporu
                            </div>
                            <div style="font-size: 12px; color: var(--tblr-muted);">${timestamp}</div>
                        </div>
                        <div style="font-size: 13px; color: var(--tblr-muted);">
                            ${featureName} - AI destekli analiz sonuçları
                        </div>
                    </div>
                    
                    <div class="ai-content-wrapper" style="
                        background: var(--tblr-bg-surface-secondary); 
                        border: 1px solid var(--tblr-border-color); 
                        border-radius: 8px; padding: 20px; 
                        font-size: 14px; line-height: 1.7; 
                        color: var(--tblr-body-color);
                        max-height: 400px; overflow-y: auto;
                    ">
                        ${safeContent.replace(/\n/g, '<br>')}
                    </div>
                    
                    <div style="margin-top: 20px; text-align: center;">
                        <button onclick="copyToClipboard('${safeContent.replace(/'/g, "\\'")}'); alert('Sonuçlar panoya kopyalandı!');" 
                                style="background: var(--tblr-primary); color: white; border: none; padding: 10px 20px; 
                                       border-radius: 8px; font-size: 13px; cursor: pointer; margin-right: 12px;">
                            <i class="fas fa-copy me-2"></i>Sonuçları Kopyala
                        </button>
                        <button onclick="downloadAIResults('${featureName}', '${safeContent.replace(/'/g, "\\'")}');" 
                                style="background: var(--tblr-success); color: white; border: none; padding: 10px 20px; 
                                       border-radius: 8px; font-size: 13px; cursor: pointer; margin-right: 12px;">
                            <i class="fas fa-download me-2"></i>İndir
                        </button>
                        <button onclick="shareAIResults('${featureName}', '${featureSlug}');" 
                                style="background: var(--tblr-info); color: white; border: none; padding: 10px 20px; 
                                       border-radius: 8px; font-size: 13px; cursor: pointer;">
                            <i class="fas fa-share me-2"></i>Paylaş
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    // Show error in overlay
    function showOverlayError(featureName, errorMessage) {
        const contentElement = document.getElementById('ai-overlay-content');
        if (contentElement) {
            contentElement.innerHTML = `
                <div class="ai-overlay-error" style="text-align: center;">
                    <div style="color: var(--tblr-danger); margin-bottom: 16px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px;"></i>
                    </div>
                    <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 12px; font-size: 18px;">${featureName}</div>
                    <div style="color: var(--tblr-muted); font-size: 14px; margin-bottom: 24px;">${errorMessage}</div>
                    <button onclick="executeAIFeatureForOverlay('${featureSlug}', '${featureName}')" 
                            style="background: var(--tblr-primary); color: white; border: none; padding: 10px 20px; 
                                   border-radius: 8px; font-size: 13px; cursor: pointer;">
                        <i class="fas fa-redo me-2"></i>Tekrar Dene
                    </button>
                    <!-- Kapat butonu kaldırıldı -->
                </div>
            `;
        }
    }
    
    // Close AI Results Overlay
    window.closeAIResultsOverlay = function() {
        const overlay = document.getElementById('ai-results-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            overlay.style.transform = 'scale(0.9)';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }
    
    // Download AI Results
    window.downloadAIResults = function(featureName, content) {
        const timestamp = new Date().toISOString().slice(0, 10);
        const filename = `${featureName}_${timestamp}.txt`;
        const blob = new Blob([content.replace(/<br>/g, '\n')], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        alert('Sonuçlar indirildi!');
    }
    
    // Share AI Results 
    window.shareAIResults = function(featureName, featureSlug) {
        if (navigator.share) {
            navigator.share({
                title: featureName + ' - AI Analiz Sonuçları',
                text: 'AI destekli ' + featureName + ' analizi tamamlandı.',
                url: window.location.href
            });
        } else {
            // Fallback: copy URL to clipboard
            navigator.clipboard.writeText(window.location.href);
            alert('URL panoya kopyalandı!');
        }
    }
    
    // Show loading state in left panel
    function showLeftPanelLoading(element, featureName) {
        element.innerHTML = `
            <div class="ai-loading-state" style="padding: 20px; text-align: center; background: var(--tblr-bg-surface-secondary); border-radius: 8px; border: 1px solid var(--tblr-border-color);">
                <div style="margin-bottom: 16px;">
                    <div class="loading-spinner" style="margin: 0 auto 12px auto; width: 32px; height: 32px; border-width: 3px;"></div>
                    <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 8px;">${featureName}</div>
                    <div style="font-size: 13px; color: var(--tblr-muted);">AI analizi çalışıyor...</div>
                </div>
                <div style="font-size: 12px; color: var(--tblr-muted); opacity: 0.7;">
                    <i class="fas fa-robot me-2"></i>Sonuçlar burada görünecek
                </div>
            </div>
        `;
    }
    
    // Execute AI feature and display in left panel
    function executeAIFeatureForLeftPanel(featureSlug, featureName, leftPanelElement) {
        // Create specific prompt for the feature
        const featurePrompts = {
            'seo-puan-analizi': 'Bu sayfanın SEO puanını analiz et ve detaylı rapor ver',
            'hizli-seo-analizi': 'Bu sayfada hızlı SEO analizi yap',
            'anahtar-kelime-analizi': 'Bu sayfanın anahtar kelimelerini analiz et',
            'icerik-optimizasyonu': 'Bu sayfanın içeriğini optimize etmek için öneriler sun',
            'icerik-genisletme': 'Bu sayfanın içeriğini genişlet',
            'icerik-ozetleme': 'Bu sayfanın içeriğini özetle',
            'baslik-uretici': 'Bu sayfa için alternatif başlıklar öner',
            'meta-aciklama-uretici': 'Bu sayfa için meta açıklama oluştur',
            'alt-baslik-onerileri': 'Bu sayfa için alt başlıklar öner',
            'sayfa-gelistirme-onerileri': 'Bu sayfayı geliştirmek için öneriler sun',
            'kullanici-deneyimi-analizi': 'Bu sayfanın kullanıcı deneyimini analiz et',
            'icerik-kalite-skoru': 'Bu sayfanın içerik kalitesini skorla',
            'coklu-dil-cevirisi': 'Bu sayfayı farklı dillere çevir',
            'dil-kalitesi-kontrolu': 'Bu sayfanın dil kalitesini kontrol et',
            'rekabet-analizi': 'Bu sayfa için rekabet analizi yap',
            'trending-konu-onerileri': 'Bu konuda trending öneriler sun',
            'schema-markup-onerileri': 'Bu sayfa için schema markup önerileri sun',
            'link-onerileri': 'Bu sayfa için link önerileri sun'
        };
        
        const prompt = featurePrompts[featureSlug] || `${featureName} analizini yap`;
        
        if (window.fetch) {
            fetch('/admin/ai/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    message: prompt,
                    conversation_id: null,
                    feature_context: featureSlug
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && (data.message || data.response || data.content)) {
                    const content = data.message || data.response || data.content;
                    showLeftPanelResults(leftPanelElement, featureName, content, featureSlug);
                    showAINotification('Tamamlandı', `${featureName} tamamlandı!`, 'success');
                } else {
                    showLeftPanelError(leftPanelElement, featureName, 'AI yanıt veremedi');
                    showAINotification('Hata', 'AI yanıt veremedi', 'error');
                }
            })
            .catch(error => {
                console.error('Left Panel AI Error:', error);
                showLeftPanelError(leftPanelElement, featureName, 'Bağlantı hatası');
                showAINotification('Hata', 'Bağlantı hatası', 'error');
            });
        } else {
            // Demo mode
            setTimeout(() => {
                showLeftPanelResults(leftPanelElement, featureName, `${featureName} demo sonuçları burada görünüyor. Gerçek AI entegrasyonu yakında aktif olacak!`, featureSlug);
                showAINotification('Demo', `${featureName} demo modunda`, 'info');
            }, 1500);
        }
    }
    
    // Show AI results in left panel
    function showLeftPanelResults(element, featureName, content, featureSlug) {
        const timestamp = new Date().toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        element.innerHTML = `
            <div class="ai-results-content" style="padding: 0;">
                <div class="ai-results-header" style="background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%); 
                     color: white; padding: 16px; border-radius: 8px 8px 0 0; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: between;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">
                                <i class="fas fa-robot me-2"></i>${featureName}
                            </div>
                            <div style="font-size: 12px; opacity: 0.9;">${timestamp}</div>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                            AI Analiz
                        </div>
                    </div>
                </div>
                <div class="ai-results-body" style="padding: 0 16px 16px 16px;">
                    <div style="background: var(--tblr-bg-surface-secondary); border: 1px solid var(--tblr-border-color); 
                         border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.6; color: var(--tblr-body-color);">
                        ${safeContent.replace(/\n/g, '<br>')}
                    </div>
                    <div style="margin-top: 16px; text-align: center;">
                        <button onclick="copyToClipboard('${safeContent.replace(/'/g, "\\'")}'); alert('Sonuçlar panoya kopyalandı!');" 
                                style="background: var(--tblr-primary); color: white; border: none; padding: 8px 16px; 
                                       border-radius: 6px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-copy me-2"></i>Kopyala
                        </button>
                        <button onclick="clearLeftPanelResults()" 
                                style="background: var(--tblr-bg-surface-tertiary); color: var(--tblr-muted); border: 1px solid var(--tblr-border-color); 
                                       padding: 8px 16px; border-radius: 6px; font-size: 12px; cursor: pointer; margin-left: 8px;">
                            <i class="fas fa-times me-2"></i>Temizle
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Show error in left panel
    function showLeftPanelError(element, featureName, errorMessage) {
        element.innerHTML = `
            <div class="ai-error-state" style="padding: 20px; text-align: center; background: var(--tblr-bg-surface-secondary); 
                 border-radius: 8px; border: 1px solid var(--tblr-danger-darken-10);">
                <div style="color: var(--tblr-danger); margin-bottom: 12px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 32px;"></i>
                </div>
                <div style="font-weight: 600; color: var(--tblr-body-color); margin-bottom: 8px;">${featureName}</div>
                <div style="font-size: 13px; color: var(--tblr-muted); margin-bottom: 16px;">${errorMessage}</div>
                <button onclick="createModernAIModal('${featureSlug}', '${featureName}')" 
                        style="background: var(--tblr-primary); color: white; border: none; padding: 8px 16px; 
                               border-radius: 6px; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-redo me-2"></i>Tekrar Dene
                </button>
            </div>
        `;
    }
    
    // Clear left panel results
    window.clearLeftPanelResults = function() {
        const leftPanel = document.querySelector('.ai-results-content')?.parentElement;
        if (leftPanel) {
            leftPanel.innerHTML = `
                <div style="padding: 40px 20px; text-align: center; color: var(--tblr-muted); border: 2px dashed var(--tblr-border-color); 
                     border-radius: 8px; background: var(--tblr-bg-surface-secondary);">
                    <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;">
                        🤖
                    </div>
                    <div style="font-size: 14px; font-weight: 500; margin-bottom: 8px;">AI Analiz Sonuçları</div>
                    <div style="font-size: 12px; opacity: 0.7;">Floating robot'tan bir AI özelliğini seçin</div>
                </div>
            `;
        }
    }
    
    // Copy to clipboard utility
    window.copyToClipboard = function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text.replace(/<br>/g, '\n'));
        } else {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = text.replace(/<br>/g, '\n');
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
    }
    
    // Execute AI feature via chat system (for non-page features or fallback)
    function executeFeatureViaChat(featureSlug) {
        console.log('🤖 Executing via AI Chat:', featureSlug);
        
        // Create a smart prompt based on feature slug
        const featurePrompts = {
            // Marketing & Business
            'pazarlama-stratejisi': 'Benim için etkili bir pazarlama stratejisi öner',
            'marka-konumlandirma': 'Marka konumlandırma stratejisi oluştur',
            'hedef-kitle-analizi': 'Hedef kitlemi analiz et ve öneriler sun',
            'rekabet-analizi': 'Rekabet analizi yap ve stratejiler öner',
            'sosyal-medya-stratejisi': 'Sosyal medya stratejisi oluştur',
            
            // Content & Writing
            'icerik-plani': 'Benim için içerik planı oluştur',
            'makale-yazimi': 'Profesyonel makale yaz',
            'e-posta-sablonu': 'Etkili e-posta şablonu oluştur',
            'blog-yazisi': 'SEO uyumlu blog yazısı yaz',
            'kurumsal-metin': 'Kurumsal metin yaz',
            'yaratici-yazim': 'Yaratıcı metin yaz',
            
            // Technical & Analysis
            'teknik-analiz': 'Teknik analiz yap',
            'veri-analizi': 'Veri analizimi yap',
            'performans-raporu': 'Performans raporu oluştur',
            'optimizasyon-onerileri': 'Optimizasyon önerileri sun',
            
            // Translation & Language
            'ceviri': 'Metni çevir',
            'dil-duzeltme': 'Dil ve yazım hatalarını düzelt',
            'ton-degistirme': 'Metnin tonunu değiştir',
            
            // Creative & Design
            'tasarim-onerileri': 'Tasarım önerileri sun',
            'renk-paleti': 'Renk paleti öner',
            'logo-konsepti': 'Logo konsepti oluştur',
            
            // Default fallback
            'default': `${featureSlug} özelliğini kullanmak istiyorum, nasıl yardımcı olabilirsin?`
        };
        
        const prompt = featurePrompts[featureSlug] || featurePrompts['default'];
        
        // Add to chat input and send
        const chatInput = document.getElementById('floating-chat-input');
        if (chatInput) {
            chatInput.value = prompt;
            sendChatMessage();
        } else {
            // Direct API call if no chat input available
            sendAIFeatureRequest(prompt, featureSlug);
        }
    }
    
    // Direct AI API call for features
    function sendAIFeatureRequest(prompt, featureSlug) {
        if (window.fetch) {
            try {
                fetch('/admin/ai/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ 
                        message: prompt,
                        conversation_id: null,
                        feature_context: featureSlug
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && (data.message || data.response)) {
                        const content = data.message || data.response;
                        showAINotification('Tamamlandı', `${featureSlug} tamamlandı!`, 'success');
                        addChatMessage(`🤖 ${featureSlug} sonucu:\n\n${content}`, 'ai');
                    } else {
                        showAINotification('Hata', 'AI yanıt veremedi', 'error');
                        addChatMessage('❌ AI yanıt veremedi. Lütfen tekrar deneyin.', 'ai');
                    }
                })
                .catch(error => {
                    console.error('AI Feature API Error:', error);
                    showAINotification('Hata', 'Bağlantı hatası', 'error');
                    addChatMessage('❌ AI ile bağlantı kurulamadı. Lütfen daha sonra tekrar deneyin.', 'ai');
                });
            } catch (error) {
                console.error('AI Feature Request Error:', error);
                showAINotification('Hata', 'İstek hatası', 'error');
                addChatMessage('❌ AI isteği gönderilemedi.', 'ai');
            }
        } else {
            // Fallback for no fetch support
            showAINotification('Bilgi', `${featureSlug} demo modunda`, 'info');
            addChatMessage(`🤖 ${featureSlug} demo modunda çalışıyor. Bu özellik yakında aktif olacak!`, 'ai');
        }
    }
    
    // Open AI page
    window.openAIPage = function() {
        window.open('/admin/ai', '_blank');
    }
    
    // Toggle more features
    window.toggleMoreFeatures = function() {
        const moreFeatures = document.getElementById('more-features');
        const icon = document.getElementById('more-features-icon');
        const text = document.getElementById('more-features-text');
        
        if (moreFeatures.style.display === 'none') {
            moreFeatures.style.display = 'block';
            icon.className = 'fas fa-chevron-up me-2';
            text.textContent = 'Daha Az Göster';
        } else {
            moreFeatures.style.display = 'none';
            icon.className = 'fas fa-chevron-down me-2';
            text.textContent = text.textContent.replace('Daha Az Göster', 'Daha Fazla AI Özelliği Göster');
        }
    }
    
    // Enhanced Chat functionality with feature context support
    function sendChatMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        // Add user message
        addChatMessage(message, 'user');
        chatInput.value = '';
        
        // Show typing indicator
        showTypingIndicator();
        
        // Call real AI chat through global AI system
        if (window.fetch) {
            try {
                // Use AI chat endpoint
                fetch('/admin/ai/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ 
                        message: message,
                        conversation_id: null // Yeni konuşma başlat
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideTypingIndicator();
                    if (data.success && (data.message || data.response || data.content)) {
                        const content = data.message || data.response || data.content;
                        addChatMessage(content, 'ai');
                    } else {
                        addChatMessage('AI yanıt veremedi. Lütfen sorunuzu daha detaylı sorabilirsiniz.', 'ai');
                    }
                })
                .catch(error => {
                    hideTypingIndicator();
                    console.error('AI Chat Error:', error);
                    addChatMessage('AI ile bağlantı kurulamadı. Lütfen daha sonra tekrar deneyin.', 'ai');
                });
            } catch (error) {
                hideTypingIndicator();
                console.error('AI Chat Error:', error);
                addChatMessage('AI sisteminde hata oluştu.', 'ai');
            }
        } else {
            hideTypingIndicator();
            // Simple demo response
            setTimeout(() => {
                addChatMessage('Demo modunda çalışıyorum. Gerçek AI özelliği yakında aktif olacak!', 'ai');
            }, 1500);
        }
    }
    
    // Add message to chat
    function addChatMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}-message`;
        
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                          now.getMinutes().toString().padStart(2, '0');
        
        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-${sender === 'ai' ? 'robot' : 'user'}"></i>
            </div>
            <div class="message-content">
                <div class="message-text">${text}</div>
                <div class="message-time">${timeString}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message ai-message typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="message-text">
                    <span class="loading-spinner"></span>
                    <span style="margin-left: 8px;">AI yazıyor...</span>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function hideTypingIndicator() {
        const typingIndicator = chatMessages.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    // Chat event listeners
    chatSend.addEventListener('click', sendChatMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendChatMessage();
        }
    });
    
    // Enhanced notification system with theme support
    function showAINotification(title, message, type = 'info') {
        // Remove existing notifications
        const existingToasts = document.querySelectorAll('.ai-toast');
        existingToasts.forEach(toast => toast.remove());
        
        const colors = {
            'info': 'var(--tblr-primary)',
            'success': 'var(--tblr-success)',
            'warning': 'var(--tblr-warning)',
            'error': 'var(--tblr-danger)'
        };
        
        const toast = document.createElement('div');
        toast.className = 'ai-toast';
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: ${colors[type]}; 
                           border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-robot" style="color: white; font-size: 16px;"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 14px; color: var(--tblr-body-color); margin-bottom: 2px;">${title}</div>
                    <div style="font-size: 13px; color: var(--tblr-muted); line-height: 1.4;">${message}</div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="width: 24px; height: 24px; border: none; background: var(--tblr-bg-surface-secondary); 
                               border-radius: 50%; cursor: pointer; display: flex; align-items: center; 
                               justify-content: center; color: var(--tblr-muted); flex-shrink: 0;">
                    <i class="fas fa-times" style="font-size: 10px;"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 4000);
    }
    
    // Drag functionality
    let isDragging = false;
    let currentX = 0;
    let currentY = 0;
    let initialX = 0;
    let initialY = 0;
    
    const header = aiWidget.querySelector('.widget-header');
    
    header.addEventListener('mousedown', function(e) {
        if (e.target.closest('button')) return;
        
        isDragging = true;
        initialX = e.clientX - currentX;
        initialY = e.clientY - currentY;
        
        aiWidget.style.cursor = 'grabbing';
        header.style.cursor = 'grabbing';
    });
    
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        e.preventDefault();
        currentX = e.clientX - initialX;
        currentY = e.clientY - initialY;
        
        // Keep within screen bounds
        const rect = aiWidget.getBoundingClientRect();
        const maxX = window.innerWidth - rect.width;
        const maxY = window.innerHeight - rect.height;
        
        currentX = Math.max(0, Math.min(currentX, maxX));
        currentY = Math.max(0, Math.min(currentY, maxY));
        
        aiWidget.style.transform = `translate(${currentX}px, ${currentY}px)`;
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            aiWidget.style.cursor = 'default';
            header.style.cursor = 'move';
        }
    });
    
    // Widget hazır - bildirim yok
});
</script>

{{-- Modern AI Widget CSS with Light/Dark Mode Support --}}
<style>
/* Base Widget Styling */
.ai-widget-theme {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    width: 420px;
    max-height: 750px;
    border-radius: 16px;
    box-shadow: var(--tblr-shadow-lg);
    transition: all 0.3s ease;
    font-family: inherit;
    overflow: hidden;
    user-select: none;
    background: var(--tblr-bg-surface);
    border: 1px solid var(--tblr-border-color);
}

/* History Sidebar */
.ai-history-sidebar {
    position: absolute;
    left: -320px;
    top: 0;
    bottom: 0;
    width: 300px;
    background: var(--tblr-bg-surface);
    border: 1px solid var(--tblr-border-color);
    border-radius: 16px 0 0 16px;
    box-shadow: var(--tblr-shadow-md);
    z-index: 10;
    transition: all 0.3s ease;
    overflow: hidden;
}

.ai-history-sidebar.active {
    left: -300px;
}

.history-header {
    background: var(--tblr-bg-surface-secondary);
    padding: 16px;
    border-bottom: 1px solid var(--tblr-border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.history-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--tblr-body-color);
}

.btn-clear-history {
    background: none;
    border: none;
    color: var(--tblr-muted);
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.btn-clear-history:hover {
    background: var(--tblr-danger);
    color: white;
}

.history-content {
    padding: 12px;
    height: calc(100% - 60px);
    overflow-y: auto;
}

.history-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--tblr-muted);
}

.history-empty i {
    font-size: 32px;
    margin-bottom: 12px;
    display: block;
}

.history-empty p {
    margin: 0;
    font-size: 13px;
}

.history-item {
    background: var(--tblr-bg-surface-secondary);
    border: 1px solid var(--tblr-border-color);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.history-item:hover {
    background: var(--tblr-bg-surface-tertiary);
    border-color: var(--tblr-primary);
}

.history-item-title {
    font-weight: 600;
    font-size: 13px;
    color: var(--tblr-body-color);
    margin-bottom: 4px;
}

.history-item-preview {
    font-size: 12px;
    color: var(--tblr-muted);
    line-height: 1.3;
    max-height: 32px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.history-item-time {
    font-size: 11px;
    color: var(--tblr-muted);
    margin-top: 6px;
    text-align: right;
}

/* Header */
.widget-header {
    background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%);
    color: white;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.widget-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.widget-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.widget-icon {
    background: rgba(255,255,255,0.2);
    padding: 8px;
    border-radius: 8px;
    font-size: 18px;
}

.widget-title {
    font-weight: 700;
    font-size: 18px;
}

.widget-status {
    font-size: 12px;
    opacity: 0.8;
}

.btn-widget-control {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-widget-control:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

/* Content */
.widget-content {
    max-height: 650px;
    overflow-y: auto;
}

.widget-section-header {
    padding: 20px 20px 15px 20px;
    border-bottom: 1px solid var(--tblr-border-color);
}

.widget-section-title {
    margin: 0;
    font-weight: 600;
    font-size: 14px;
    color: var(--tblr-body-color);
}

/* Features */
.widget-features {
    padding: 20px;
}

.feature-item {
    background: var(--tblr-bg-surface-secondary);
    padding: 16px;
    border-radius: 12px;
    cursor: pointer;
    margin-bottom: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--tblr-border-color);
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--tblr-shadow-md);
}

.feature-item.featured {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #000;
    margin-bottom: 16px;
}

.feature-item.featured:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(251, 191, 36, 0.3);
}

.feature-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.feature-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    background: var(--tblr-primary-lt);
    color: var(--tblr-primary);
}

.feature-icon.featured-icon {
    background: rgba(0,0,0,0.1);
    color: #000;
}

.feature-text {
    flex: 1;
}

.feature-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--tblr-body-color);
    margin-bottom: 2px;
}

.feature-item.featured .feature-name {
    color: #000;
}

.feature-desc {
    font-size: 12px;
    color: var(--tblr-muted);
}

.feature-item.featured .feature-desc {
    color: rgba(0,0,0,0.8);
}

.feature-arrow {
    font-size: 12px;
    color: var(--tblr-muted);
}

.feature-item.featured .feature-arrow {
    color: rgba(0,0,0,0.6);
}

/* AI Chat Section */
.ai-chat-section {
    border-top: 1px solid var(--tblr-border-color);
}

.ai-chat-messages {
    max-height: 200px;
    overflow-y: auto;
    padding: 15px 20px;
}

.chat-message {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--tblr-primary-lt);
    color: var(--tblr-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.message-content {
    flex: 1;
}

.message-text {
    background: var(--tblr-bg-surface-secondary);
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.4;
    color: var(--tblr-body-color);
}

.message-time {
    font-size: 10px;
    color: var(--tblr-muted);
    margin-top: 4px;
}

.user-message {
    flex-direction: row-reverse;
}

.user-message .message-avatar {
    background: var(--tblr-primary);
    color: white;
}

.user-message .message-text {
    background: var(--tblr-primary);
    color: white;
}

/* Chat Input */
.ai-chat-input {
    padding: 15px 20px;
    border-top: 1px solid var(--tblr-border-color);
}

.chat-input-group {
    display: flex;
    gap: 8px;
}

.chat-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid var(--tblr-border-color);
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    background: var(--tblr-bg-surface);
    color: var(--tblr-body-color);
}

.chat-input:focus {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.125rem rgba(var(--tblr-primary-rgb), 0.25);
}

.chat-send-btn {
    padding: 10px 16px;
    background: var(--tblr-primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.chat-send-btn:hover {
    background: var(--tblr-primary-darken-10);
    transform: translateY(-1px);
}

/* Widget Actions */
.widget-actions {
    padding: 15px 20px 20px 20px;
    border-top: 1px solid var(--tblr-border-color);
}

.btn-full-ai {
    width: 100%;
    padding: 12px;
    background: var(--tblr-bg-surface-secondary);
    border: 1px solid var(--tblr-border-color);
    color: var(--tblr-body-color);
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-full-ai:hover {
    background: var(--tblr-bg-surface-tertiary);
    transform: translateY(-1px);
}

/* Minimized Button */
.floating-ai-minimized button {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-purple) 100%);
    color: white;
    cursor: pointer;
    box-shadow: var(--tblr-shadow-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.floating-ai-minimized button:hover {
    transform: translateY(-2px);
    box-shadow: var(--tblr-shadow-xl);
}

.floating-ai-minimized button i {
    font-size: 24px;
}

/* Online Status Indicator */
.floating-ai-minimized button::after {
    content: '';
    position: absolute;
    top: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    background: var(--tblr-success);
    border-radius: 50%;
    border: 3px solid var(--tblr-bg-surface);
}

/* Scrollbar Styling */
.widget-content::-webkit-scrollbar,
.ai-chat-messages::-webkit-scrollbar {
    width: 4px;
}

.widget-content::-webkit-scrollbar-track,
.ai-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.widget-content::-webkit-scrollbar-thumb,
.ai-chat-messages::-webkit-scrollbar-thumb {
    background: var(--tblr-border-color);
    border-radius: 2px;
}

.widget-content::-webkit-scrollbar-thumb:hover,
.ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--tblr-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .ai-widget-theme {
        width: 90vw;
        max-height: 80vh;
        right: 5px;
        bottom: 10px;
    }
    
    .floating-ai-minimized {
        right: 10px !important;
        bottom: 10px !important;
    }
    
    .floating-ai-minimized button {
        width: 56px;
        height: 56px;
    }
    
    .floating-ai-minimized button i {
        font-size: 20px;
    }
}

/* AI Toast Notifications */
.ai-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10001;
    min-width: 350px;
    max-width: 400px;
    background: var(--tblr-bg-surface);
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: var(--tblr-shadow-lg);
    border: 1px solid var(--tblr-border-color);
    font-family: inherit;
    transform: translateX(100%);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.ai-toast.show {
    transform: translateX(0);
}

/* Loading States */
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid var(--tblr-border-color);
    border-radius: 50%;
    border-top-color: var(--tblr-primary);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Dark Mode Enhancements */
[data-bs-theme="dark"] .feature-item.featured {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

[data-bs-theme="dark"] .ai-toast {
    backdrop-filter: blur(12px);
}

/* AI Results Overlay Animations */
#ai-results-overlay {
    animation: overlayFadeIn 0.3s ease-out;
}

#ai-results-overlay .ai-overlay-container {
    animation: overlaySlideIn 0.3s ease-out;
}

@keyframes overlayFadeIn {
    from { 
        opacity: 0; 
        backdrop-filter: blur(0px); 
    }
    to { 
        opacity: 1; 
        backdrop-filter: blur(4px); 
    }
}

@keyframes overlaySlideIn {
    from { 
        opacity: 0; 
        transform: translate(-50%, -50%) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translate(-50%, -50%) scale(1); 
    }
}

/* AI Overlay Responsive */
@media (max-width: 768px) {
    #ai-results-overlay .ai-overlay-container {
        width: 95vw !important;
        max-height: 90vh !important;
        margin: 20px;
    }
    
    #ai-results-overlay .ai-overlay-header {
        padding: 16px !important;
    }
    
    #ai-results-overlay .ai-overlay-content {
        padding: 16px !important;
    }
    
    #ai-results-overlay .ai-overlay-footer {
        padding: 12px 16px !important;
        flex-direction: column;
        gap: 8px;
    }
    
    #ai-results-overlay .ai-content-wrapper {
        max-height: 300px !important;
    }
}

// ========================================
// DİNAMİK SEO UYGULAMA FONKSİYONLARI
// ========================================

// SEO önerilerini sayfaya uygula
window.applySEOSuggestion = function(type, language, value) {
    console.log('🎯 Applying SEO suggestion:', {type, language, value});
    
    try {
        // Livewire component'ini bul
        const pageComponent = window.Livewire.all().find(comp => {
            return comp.fingerprint && (
                comp.fingerprint.name.includes('page-manage') || 
                comp.fingerprint.name.includes('Page')
            );
        });
        
        if (pageComponent) {
            // SEO değerini uygula
            if (type === 'title') {
                pageComponent.call('updateSEOTitle', language, value).then(() => {
                    showSEOSuccessMessage('Başlık güncellendi!');
                }).catch(error => {
                    console.error('SEO title update error:', error);
                    showSEOErrorMessage('Başlık güncellenemedi.');
                });
            } else if (type === 'meta') {
                pageComponent.call('updateSEOMeta', language, value).then(() => {
                    showSEOSuccessMessage('Meta açıklama güncellendi!');
                }).catch(error => {
                    console.error('SEO meta update error:', error);
                    showSEOErrorMessage('Meta açıklama güncellenemedi.');
                });
            }
        } else {
            showSEOErrorMessage('Sayfa bileşeni bulunamadı.');
        }
    } catch (error) {
        console.error('Apply SEO suggestion error:', error);
        showSEOErrorMessage('Bir hata oluştu.');
    }
}

// Anahtar kelimeleri sisteme ekle
window.applySEOKeywords = function(keywords) {
    console.log('🔑 Adding SEO keywords:', keywords);
    
    try {
        const pageComponent = window.Livewire.all().find(comp => {
            return comp.fingerprint && (
                comp.fingerprint.name.includes('page-manage') || 
                comp.fingerprint.name.includes('Page')
            );
        });
        
        if (pageComponent) {
            pageComponent.call('addSEOKeywords', keywords).then(() => {
                showSEOSuccessMessage('Anahtar kelimeler eklendi!');
            }).catch(error => {
                console.error('SEO keywords error:', error);
                showSEOErrorMessage('Anahtar kelimeler eklenemedi.');
            });
        } else {
            showSEOErrorMessage('Sayfa bileşeni bulunamadı.');
        }
    } catch (error) {
        console.error('Apply SEO keywords error:', error);
        showSEOErrorMessage('Bir hata oluştu.');
    }
}

// Başarı mesajı göster
function showSEOSuccessMessage(message) {
    // Toast notification sistemi varsa kullan
    if (typeof showToast === 'function') {
        showToast(message, 'success');
    } else {
        // Basit alert fallback
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // 3 saniye sonra otomatik kaldır
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 3000);
    }
}

// Hata mesajı göster
function showSEOErrorMessage(message) {
    if (typeof showToast === 'function') {
        showToast(message, 'error');
    } else {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 3000);
    }
}

/* AI Overlay Scrollbar */
.ai-content-wrapper::-webkit-scrollbar {
    width: 6px;
}

.ai-content-wrapper::-webkit-scrollbar-track {
    background: var(--tblr-bg-surface);
    border-radius: 3px;
}

.ai-content-wrapper::-webkit-scrollbar-thumb {
    background: var(--tblr-border-color);
    border-radius: 3px;
}

.ai-content-wrapper::-webkit-scrollbar-thumb:hover {
    background: var(--tblr-muted);
}
</style>