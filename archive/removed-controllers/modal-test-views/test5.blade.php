@extends('admin.layout')
@section('content')

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Test5 - Hybrid Premium AI Modal</h2>
                    <div class="text-secondary mt-1">{{ $pageTitle ?? 'Hybrid approach with best features' }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#hybrid-ai-modal">
                            <i class="fa fa-language me-2"></i>AI Çeviri Test
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#tabs-content" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">İçerik</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tabs-seo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">SEO</a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="tabs-content" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label class="form-label">Başlık</label>
                                                <input type="text" class="form-control" value="Test Sayfa Başlığı">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">İçerik</label>
                                                <textarea id="tinymce-editor" class="form-control" rows="15">
                                                    <h2>Test İçeriği</h2>
                                                    <p>Bu bir test sayfasıdır. AI çeviri modalını test etmek için kullanılmaktadır.</p>
                                                    <p>Hybrid modal tasarımı en iyi özellikleri birleştirmektedir:</p>
                                                    <ul>
                                                        <li>Modern Premium Design</li>
                                                        <li>Interactive Components</li>
                                                        <li>Analytics Dashboard</li>
                                                        <li>Integration Features</li>
                                                    </ul>
                                                </textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>Sayfa Ayarları</h4>
                                                    <div class="mb-3">
                                                        <label class="form-label">Durum</label>
                                                        <select class="form-select">
                                                            <option>Aktif</option>
                                                            <option>Pasif</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Yayın Tarihi</label>
                                                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <button type="submit" class="btn btn-success w-100">Kaydet</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="tabs-seo" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label class="form-label">SEO Başlığı</label>
                                                <input type="text" class="form-control" value="Test Sayfa - SEO Başlığı">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Meta Açıklama</label>
                                                <textarea class="form-control" rows="3">Test sayfa meta açıklaması...</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hybrid Premium AI Translation Modal -->
<div class="modal fade" id="hybrid-ai-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 20px; overflow: hidden;">
            
            <!-- Header with Glass Morphism -->
            <div class="modal-header border-0 position-relative" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); padding: 1.5rem;">
                <div class="d-flex align-items-center w-100">
                    <div class="me-3">
                        <div class="avatar avatar-lg" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <i class="fa fa-brain fa-2x text-white" style="animation: pulse 2s infinite;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="modal-title text-white mb-1">🚀 Hybrid AI Translation Suite</h4>
                        <p class="text-white-50 mb-0 small">Premium • Analytics • Interactive • Automated</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-center">
                            <div class="small text-white-75">Performance</div>
                            <div class="progress" style="width: 80px; height: 6px;">
                                <div class="progress-bar bg-success" style="width: 94%"></div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
            </div>

            <!-- Smart Navigation Tabs -->
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <!-- Left Sidebar Navigation -->
                    <div class="col-md-3" style="background: rgba(0,0,0,0.2); backdrop-filter: blur(10px);">
                        <div class="nav flex-column nav-pills p-3" id="hybrid-tab" role="tablist">
                            <div class="small text-white-75 mb-2 text-uppercase tracking-wide">Translation Steps</div>
                            
                            <a class="nav-link text-white active mb-2" id="step1-tab" data-bs-toggle="pill" href="#step1" style="border-radius: 12px; background: rgba(255,255,255,0.1);">
                                <i class="fa fa-cog me-2"></i>
                                <div class="fw-bold">1. Configure</div>
                                <div class="small opacity-75">Language & Settings</div>
                            </a>
                            
                            <a class="nav-link text-white mb-2" id="step2-tab" data-bs-toggle="pill" href="#step2" style="border-radius: 12px;">
                                <i class="fa fa-eye me-2"></i>
                                <div class="fw-bold">2. Preview</div>
                                <div class="small opacity-75">Content Analysis</div>
                            </a>
                            
                            <a class="nav-link text-white mb-2" id="step3-tab" data-bs-toggle="pill" href="#step3" style="border-radius: 12px;">
                                <i class="fa fa-rocket me-2"></i>
                                <div class="fw-bold">3. Process</div>
                                <div class="small opacity-75">AI Translation</div>
                            </a>
                            
                            <a class="nav-link text-white mb-2" id="step4-tab" data-bs-toggle="pill" href="#step4" style="border-radius: 12px;">
                                <i class="fa fa-chart-line me-2"></i>
                                <div class="fw-bold">4. Analytics</div>
                                <div class="small opacity-75">Results & Metrics</div>
                            </a>

                            <!-- Real-time Stats -->
                            <div class="mt-4 p-3" style="background: rgba(255,255,255,0.1); border-radius: 12px;">
                                <div class="small text-white-75 mb-2">Live Stats</div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-white fw-bold">47</div>
                                        <div class="small text-white-75">Words/min</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-white fw-bold">98%</div>
                                        <div class="small text-white-75">Accuracy</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-white fw-bold">1.2s</div>
                                        <div class="small text-white-75">Avg Time</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-white fw-bold">$0.03</div>
                                        <div class="small text-white-75">Cost</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="col-md-9">
                        <div class="tab-content h-100" id="hybrid-tabContent">
                            
                            <!-- Step 1: Configure -->
                            <div class="tab-pane fade show active h-100" id="step1">
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                                                <div class="card-header bg-transparent border-0">
                                                    <h5 class="card-title mb-0">🌐 Language Configuration</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Kaynak Dil</label>
                                                            <select class="form-select">
                                                                <option value="tr">🇹🇷 Türkçe</option>
                                                                <option value="en">🇺🇸 English</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Hedef Dil</label>
                                                            <select class="form-select">
                                                                <option value="en">🇺🇸 English</option>
                                                                <option value="de">🇩🇪 Deutsch</option>
                                                                <option value="fr">🇫🇷 Français</option>
                                                                <option value="es">🇪🇸 Español</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- AI Model Selection -->
                                                    <div class="mt-4">
                                                        <label class="form-label">AI Model</label>
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <div class="card h-100 model-card" data-model="gpt4">
                                                                    <div class="card-body text-center p-3">
                                                                        <i class="fa fa-robot fa-2x text-primary mb-2"></i>
                                                                        <h6>GPT-4</h6>
                                                                        <div class="small text-muted">Premium Quality</div>
                                                                        <div class="badge bg-success mt-2">Recommended</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="card h-100 model-card" data-model="claude">
                                                                    <div class="card-body text-center p-3">
                                                                        <i class="fa fa-brain fa-2x text-info mb-2"></i>
                                                                        <h6>Claude</h6>
                                                                        <div class="small text-muted">Creative Writing</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="card h-100 model-card" data-model="gemini">
                                                                    <div class="card-body text-center p-3">
                                                                        <i class="fa fa-star fa-2x text-warning mb-2"></i>
                                                                        <h6>Gemini</h6>
                                                                        <div class="small text-muted">Fast & Efficient</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Advanced Settings -->
                                                    <div class="mt-4">
                                                        <h6>⚙️ Advanced Settings</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Translation Style</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="style" value="formal" checked>
                                                                    <label class="form-check-label">Formal</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="style" value="casual">
                                                                    <label class="form-check-label">Casual</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="style" value="technical">
                                                                    <label class="form-check-label">Technical</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Quality Level</label>
                                                                <input type="range" class="form-range" min="1" max="5" value="4" id="qualityRange">
                                                                <div class="d-flex justify-content-between small text-muted">
                                                                    <span>Fast</span>
                                                                    <span>Premium</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <!-- Quick Stats -->
                                            <div class="card mb-3" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                                <div class="card-body">
                                                    <h6>📊 Content Analysis</h6>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0">1,247</div>
                                                                <div class="small text-muted">Words</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0">23</div>
                                                                <div class="small text-muted">Paragraphs</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0">~4min</div>
                                                                <div class="small text-muted">Est. Time</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0">$2.40</div>
                                                                <div class="small text-muted">Cost</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- AI Suggestions -->
                                            <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                                <div class="card-body">
                                                    <h6>🤖 AI Suggestions</h6>
                                                    <div class="alert alert-info">
                                                        <strong>Detected:</strong> Technical content<br>
                                                        <strong>Recommendation:</strong> Use GPT-4 with Technical style
                                                    </div>
                                                    <div class="alert alert-warning">
                                                        <strong>Note:</strong> Contains HTML tags - structure preservation enabled
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Preview -->
                            <div class="tab-pane fade h-100" id="step2">
                                <div class="p-4">
                                    <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                        <div class="card-header bg-transparent">
                                            <h5>👁️ Content Preview & Analysis</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <h6>Original Content</h6>
                                                    <div class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
                                                        <h3>Test İçeriği</h3>
                                                        <p>Bu bir test sayfasıdır. AI çeviri modalını test etmek için kullanılmaktadır.</p>
                                                        <p>Hybrid modal tasarımı en iyi özellikleri birleştirmektedir:</p>
                                                        <ul>
                                                            <li>Modern Premium Design</li>
                                                            <li>Interactive Components</li>
                                                            <li>Analytics Dashboard</li>
                                                            <li>Integration Features</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <h6>Translation Preview</h6>
                                                    <div class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto; background: #e8f5e8;">
                                                        <h3>Test Content</h3>
                                                        <p>This is a test page. It is used to test the AI translation modal.</p>
                                                        <p>The hybrid modal design combines the best features:</p>
                                                        <ul>
                                                            <li>Modern Premium Design</li>
                                                            <li>Interactive Components</li>
                                                            <li>Analytics Dashboard</li>
                                                            <li>Integration Features</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Content Analysis -->
                                            <div class="mt-3">
                                                <h6>📈 Content Analysis</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fa fa-text-height fa-2x text-primary mb-2"></i>
                                                            <div class="h5 mb-0">Basic</div>
                                                            <div class="small text-muted">Complexity</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fa fa-language fa-2x text-success mb-2"></i>
                                                            <div class="h5 mb-0">98%</div>
                                                            <div class="small text-muted">Translatable</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fa fa-code fa-2x text-warning mb-2"></i>
                                                            <div class="h5 mb-0">12</div>
                                                            <div class="small text-muted">HTML Tags</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fa fa-clock fa-2x text-info mb-2"></i>
                                                            <div class="h5 mb-0">3.2min</div>
                                                            <div class="small text-muted">Est. Time</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Process -->
                            <div class="tab-pane fade h-100" id="step3">
                                <div class="p-4">
                                    <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                        <div class="card-body text-center">
                                            <div class="mb-4">
                                                <i class="fa fa-rocket fa-4x text-primary mb-3" style="animation: pulse 2s infinite;"></i>
                                                <h4>🚀 AI Translation in Progress</h4>
                                                <p class="text-muted">Processing your content with advanced AI technology...</p>
                                            </div>

                                            <!-- Progress Indicators -->
                                            <div class="row g-4">
                                                <div class="col-md-6 offset-md-3">
                                                    <div class="progress mb-3" style="height: 12px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 65%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted mb-4">
                                                        <span>Processing...</span>
                                                        <span>65% Complete</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Real-time Stats -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-6 col-md-3">
                                                    <div class="p-3 bg-light rounded">
                                                        <div class="h4 mb-0 text-primary">847</div>
                                                        <div class="small text-muted">Words Processed</div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="p-3 bg-light rounded">
                                                        <div class="h4 mb-0 text-success">12</div>
                                                        <div class="small text-muted">Paragraphs Done</div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="p-3 bg-light rounded">
                                                        <div class="h4 mb-0 text-info">2.3s</div>
                                                        <div class="small text-muted">Avg Response</div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="p-3 bg-light rounded">
                                                        <div class="h4 mb-0 text-warning">$1.60</div>
                                                        <div class="small text-muted">Cost So Far</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Current Processing -->
                                            <div class="alert alert-primary">
                                                <strong>Currently Processing:</strong> Paragraph 15 of 23
                                                <br><small>"Hybrid modal tasarımı en iyi özellikleri birleştirmektedir..."</small>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="mt-4">
                                                <button class="btn btn-outline-danger me-2">
                                                    <i class="fa fa-pause me-2"></i>Pause
                                                </button>
                                                <button class="btn btn-outline-secondary">
                                                    <i class="fa fa-stop me-2"></i>Stop
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4: Analytics -->
                            <div class="tab-pane fade h-100" id="step4">
                                <div class="p-4">
                                    <div class="row g-4">
                                        <div class="col-lg-8">
                                            <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                                <div class="card-header bg-transparent">
                                                    <h5>📊 Translation Analytics & Results</h5>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Success Metrics -->
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                                                <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                                                                <div class="h4 mb-0 text-success">100%</div>
                                                                <div class="small">Completed</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                                                <i class="fa fa-clock fa-2x text-primary mb-2"></i>
                                                                <div class="h4 mb-0 text-primary">3:47</div>
                                                                <div class="small">Total Time</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                                                <i class="fa fa-star fa-2x text-info mb-2"></i>
                                                                <div class="h4 mb-0 text-info">96%</div>
                                                                <div class="small">Quality Score</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                                                <i class="fa fa-dollar-sign fa-2x text-warning mb-2"></i>
                                                                <div class="h4 mb-0 text-warning">$2.40</div>
                                                                <div class="small">Total Cost</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Performance Chart -->
                                                    <div class="mb-4">
                                                        <h6>Performance Over Time</h6>
                                                        <div class="bg-light p-3 rounded text-center">
                                                            <i class="fa fa-chart-line fa-3x text-muted mb-2"></i>
                                                            <div>Real-time performance chart would appear here</div>
                                                            <div class="small text-muted">Words/min, Response time, Quality metrics</div>
                                                        </div>
                                                    </div>

                                                    <!-- Content Comparison -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Before</h6>
                                                            <div class="bg-light p-3 rounded small" style="height: 150px; overflow-y: auto;">
                                                                <strong>Test İçeriği</strong><br>
                                                                Bu bir test sayfasıdır. AI çeviri modalını test etmek için kullanılmaktadır...
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>After</h6>
                                                            <div class="bg-success bg-opacity-10 p-3 rounded small" style="height: 150px; overflow-y: auto;">
                                                                <strong>Test Content</strong><br>
                                                                This is a test page. It is used to test the AI translation modal...
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <!-- Actions -->
                                            <div class="card mb-3" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                                <div class="card-body">
                                                    <h6>⚡ Quick Actions</h6>
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-success">
                                                            <i class="fa fa-download me-2"></i>Export Translation
                                                        </button>
                                                        <button class="btn btn-primary">
                                                            <i class="fa fa-save me-2"></i>Save & Apply
                                                        </button>
                                                        <button class="btn btn-outline-info">
                                                            <i class="fa fa-redo me-2"></i>Re-translate
                                                        </button>
                                                        <button class="btn btn-outline-secondary">
                                                            <i class="fa fa-share me-2"></i>Share Results
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Quality Breakdown -->
                                            <div class="card" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                                                <div class="card-body">
                                                    <h6>🎯 Quality Breakdown</h6>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small">Grammar</span>
                                                            <span class="small">98%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: 98%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small">Context</span>
                                                            <span class="small">94%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: 94%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small">Fluency</span>
                                                            <span class="small">96%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: 96%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small">Consistency</span>
                                                            <span class="small">97%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: 97%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with Action Buttons -->
            <div class="modal-footer border-0 p-4" style="background: rgba(0,0,0,0.1);">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div class="text-white-75">
                        <small>🔒 Secure • 🚀 Fast • 🎯 Accurate</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-light me-2" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-light" id="hybrid-start-translation">
                            <i class="fa fa-play me-2"></i>Start Translation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.model-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.model-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.model-card.active {
    border-color: #007bff;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
}

.nav-pills .nav-link:hover {
    background: rgba(255,255,255,0.2) !important;
    transform: translateX(5px);
    transition: all 0.3s ease;
}

.nav-pills .nav-link.active {
    background: rgba(255,255,255,0.2) !important;
    border-left: 4px solid #fff !important;
}

.tracking-wide {
    letter-spacing: 0.05em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Model selection functionality
    document.querySelectorAll('.model-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.model-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Tab navigation with progress
    let currentStep = 1;
    const totalSteps = 4;
    
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('click', function() {
            const stepNumber = parseInt(this.id.replace('step', '').replace('-tab', ''));
            currentStep = stepNumber;
            updateNavigation();
        });
    });

    function updateNavigation() {
        document.querySelectorAll('.nav-link').forEach((link, index) => {
            const stepNum = index + 1;
            if (stepNum < currentStep) {
                link.innerHTML = link.innerHTML.replace(/fa-\w+/, 'fa-check-circle');
                link.style.background = 'rgba(40, 167, 69, 0.2)';
            } else if (stepNum === currentStep) {
                link.style.background = 'rgba(255,255,255,0.2)';
            }
        });
    }

    // Quality range slider
    document.getElementById('qualityRange')?.addEventListener('input', function() {
        const value = this.value;
        const labels = ['Draft', 'Good', 'Better', 'Best', 'Premium'];
        console.log(`Quality level: ${labels[value-1]}`);
    });

    // Start translation simulation
    document.getElementById('hybrid-start-translation')?.addEventListener('click', function() {
        // Switch to process tab
        document.getElementById('step3-tab').click();
        
        // Simulate progress
        let progress = 0;
        const progressBar = document.querySelector('.progress-bar-animated');
        const progressText = document.querySelector('.d-flex.justify-content-between span:last-child');
        
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 100) progress = 100;
            
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = Math.round(progress) + '% Complete';
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    document.getElementById('step4-tab').click();
                }, 1000);
            }
        }, 500);
    });

    // Initialize TinyMCE
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#tinymce-editor',
            height: 300,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
        });
    }
});
</script>

@endsection