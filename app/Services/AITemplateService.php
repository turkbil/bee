<?php

namespace App\Services;

/**
 * Global AI Template Service
 * Tüm modüllerde kullanılabilir AI yanıt template sistemi
 */
class AITemplateService
{
    /**
     * JSON formatındaki AI yanıtını modern template'e çevir
     */
    public function buildModernTemplate(string $text, string $featureSlug = null): string
    {
        \Log::info('🔍 AITemplateService.buildModernTemplate', [
            'text_start' => substr($text, 0, 100),
            'is_json' => str_starts_with(trim($text), '{'),
            'feature_slug' => $featureSlug
        ]);
        
        // JSON formatında gelen yanıtları parse et
        $jsonData = null;
        if (str_starts_with(trim($text), '{')) {
            try {
                $jsonData = json_decode(trim($text), true);
                \Log::info('✅ JSON Parse Success', ['keys' => array_keys($jsonData ?? [])]);
            } catch (\Exception $e) {
                \Log::warning('❌ JSON Parse Failed: ' . $e->getMessage());
            }
        }
        
        if ($jsonData && isset($jsonData['hero_score'])) {
            // JSON formatında structured yanıt var
            return $this->buildFromJSONStructure($jsonData, $featureSlug);
        }
        
        // JSON format bekleniyordu ama gelmedi - hata mesajı göster
        \Log::error('❌ AI JSON FORMAT HATASI', [
            'expected_format' => 'JSON with hero_score structure',
            'received_format' => substr($text, 0, 200) . '...',
            'text_length' => strlen($text)
        ]);
        
        return $this->buildErrorTemplate();
    }

    /**
     * JSON yapılandırılmış yanıttan modern template oluştur
     */
    private function buildFromJSONStructure(array $jsonData, ?string $featureSlug): string
    {
        // Hero score parse et
        $heroScore = $jsonData['hero_score'] ?? [];
        $score = intval(str_replace('/100', '', $heroScore['value'] ?? '75'));
        $scoreColor = $heroScore['status'] ?? 'warning';
        $scoreLabel = $heroScore['label'] ?? 'AI Skoru';
        
        // Analysis items parse et
        $analysisSection = $jsonData['analysis'] ?? [];
        $analysisItems = $analysisSection['items'] ?? [];
        
        // Recommendations parse et
        $recommendationsSection = $jsonData['recommendations'] ?? [];
        $recommendations = $recommendationsSection['cards'] ?? [];
        
        // Technical details parse et
        $technicalDetails = $jsonData['technical_details']['content'] ?? 'Teknik detaylar mevcut değil.';
        
        \Log::info('🏗️ Building from JSON Structure', [
            'score' => $score,
            'analysis_count' => count($analysisItems),
            'recommendations_count' => count($recommendations),
            'feature' => $featureSlug
        ]);
        
        return '
        <div class="ai-response-template">
            <div class="row">
                <!-- Hero Score Section - Sol Taraf -->
                <div class="col-lg-4 col-md-6">
                    <div class="hero-score-card">
                        <div class="circular-score score-' . $this->getScoreClass($scoreColor) . '">
                            ' . $score . '
                        </div>
                        <p class="score-label">' . $scoreLabel . '</p>
                    </div>
                </div>
                
                <!-- Analysis Section - Sağ Taraf -->
                <div class="col-lg-8 col-md-6">
                    <div class="analysis-section">
                        <h5 class="analysis-section-title">
                            <i class="fas fa-chart-line"></i>
                            ' . ($analysisSection['title'] ?? 'Analiz Sonuçları') . '
                        </h5>
                        <div class="analysis-items">
                            ' . $this->buildAnalysisItems($analysisItems) . '
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recommendations Section - Full Width -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="recommendations-section">
                        <h5 class="analysis-section-title">
                            <i class="fas fa-lightbulb"></i>
                            ' . ($recommendationsSection['title'] ?? 'Önerilerim') . '
                        </h5>
                        <div class="recommendations-list">
                            ' . $this->buildRecommendations($recommendations) . '
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Technical Details -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="technical-details-section">
                        <h6 class="technical-details-title">
                            <i class="fas fa-cog"></i>
                            Teknik Detaylar
                        </h6>
                        <div class="technical-content">
                            ' . nl2br(htmlspecialchars($technicalDetails)) . '
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Analysis items'ları HTML'e çevir
     */
    private function buildAnalysisItems(array $items): string
    {
        $html = '';
        foreach ($items as $item) {
            $status = $item['status'] ?? 'info';
            $label = $item['label'] ?? '';
            $detail = $item['detail'] ?? '';
            $iconClass = $this->getStatusIconClass($status);
            
            $html .= '
            <div class="analysis-item">
                <div class="analysis-item-icon ' . $iconClass . '">
                    <i class="fas fa-' . $this->getStatusIcon($status) . '"></i>
                </div>
                <div class="analysis-item-content">
                    <h6 class="analysis-item-title">' . $label . '</h6>
                    <p class="analysis-item-description">' . $detail . '</p>
                </div>
            </div>';
        }
        return $html ?: '<div class="analysis-item"><div class="analysis-item-content"><p>Analiz sonuçları yükleniyor...</p></div></div>';
    }

    /**
     * Recommendations'ları HTML'e çevir
     */
    private function buildRecommendations(array $recommendations): string
    {
        $html = '';
        foreach ($recommendations as $rec) {
            $title = $rec['title'] ?? '';
            $action = $rec['action'] ?? '';
            
            $html .= '
            <div class="recommendation-item">
                <h6 class="recommendation-title">' . $title . '</h6>
                <p class="recommendation-description">' . $action . '</p>
            </div>';
        }
        return $html ?: '<div class="recommendation-item"><p class="recommendation-description">Öneriler hazırlanıyor...</p></div>';
    }

    /**
     * Error template
     */
    private function buildErrorTemplate(): string
    {
        return '
        <div class="ai-response-template">
            <div class="ai-error-message">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">🤖 AI Yanıt Format Sorunu</h5>
                        <p class="mb-2">AI sistemi beklenen JSON formatında yanıt vermedi. Lütfen tekrar deneyin.</p>
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Teknik: JSON structured response bekleniyor ancak farklı format geldi.
                        </small>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row">
                    <div class="col-md-8">
                        <h6><i class="fas fa-tools me-2"></i>Çözüm Önerileri:</h6>
                        <ul class="mb-0">
                            <li>AI analiz butonuna tekrar tıklayın</li>
                            <li>İçeriğinizi kontrol edin ve güncelleyin</li>
                            <li>Birkaç dakika bekleyip tekrar deneyin</li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-sm" onclick="window.location.reload()">
                            <i class="fas fa-redo me-1"></i>Sayfayı Yenile
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Score class helper
     */
    private function getScoreClass(string $status): string
    {
        return match($status) {
            'success' => 'excellent',
            'warning' => 'warning',
            'danger' => 'poor',
            default => 'good'
        };
    }

    /**
     * Status icon helper
     */
    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'success' => 'check',
            'warning' => 'exclamation-triangle',
            'danger' => 'times',
            'info' => 'info-circle',
            default => 'circle'
        };
    }

    /**
     * Status icon class helper
     */
    private function getStatusIconClass(string $status): string
    {
        return match($status) {
            'success' => 'icon-excellent',
            'warning' => 'icon-warning', 
            'danger' => 'icon-poor',
            'info' => 'icon-good',
            default => 'icon-neutral'
        };
    }
}