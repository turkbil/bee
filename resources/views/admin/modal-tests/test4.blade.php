@php
    View::share('pretitle', 'Test 4: Integration & Automation Enhanced');
@endphp

<div>
    {{-- Page Helper - birebir aynı --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $testName }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Test modal butonları --}}
                        <button type="button" class="btn btn-primary" onclick="openIntegrationModal()">
                            <i class="fa-solid fa-puzzle-piece me-1"></i>
                            Integration & Automation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content area - Page manage benzeri --}}
    <div class="page-body">
        <div class="container-xl">
            <form method="post">
                <div class="card">
                    
                    {{-- Tab System - Page ile aynı --}}
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-content" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                    <i class="fa-solid fa-file-text me-1"></i>
                                    İçerik
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-seo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-search me-1"></i>
                                    SEO
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Content Tab --}}
                            <div class="tab-pane fade show active" id="tab-content" role="tabpanel">
                                {{-- Title Field --}}
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Test başlığı" value="Integration & Automation Test">
                                            <label>Başlık ★</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="test-sayfa-url" value="integration-test">
                                            <label>URL Slug</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Content Editor Area --}}
                                <div class="mb-3">
                                    <label class="form-label">İçerik ★</label>
                                    <div style="border: 1px solid #dee2e6; border-radius: 0.375rem; min-height: 300px; padding: 15px; background: #f8f9fa;">
                                        <div style="color: #6c757d; text-align: center; margin-top: 120px;">
                                            <i class="fa-solid fa-puzzle-piece" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="mt-3">Integration & Automation Enhanced</p>
                                            <p>Smart workflow automation burada test edilir</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Active Checkbox --}}
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_active" checked />
                                        <div class="state p-success p-on ms-2">
                                            <label>Aktif</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>Pasif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Tab --}}
                            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" placeholder="Meta başlık" value="Integration Automation SEO">
                                    <label>Meta Başlık</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Advanced integration and automation workflows for enhanced productivity</textarea>
                                    <label>Meta Açıklama</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <button type="button" class="btn btn-link">İptal</button>
                            <button type="button" class="btn btn-primary ms-auto">
                                <i class="fa-solid fa-save me-1"></i>
                                Kaydet
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- INTEGRATION & AUTOMATION ENHANCED MODAL --}}
<div class="modal modal-blur fade" id="integrationModal" tabindex="-1" role="dialog" aria-labelledby="integrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); border: none; border-radius: 16px; box-shadow: 0 30px 60px rgba(0,0,0,0.3); overflow: hidden;">
            
            {{-- Advanced Integration Header --}}
            <div class="modal-header border-0" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); position: relative;">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 20\"><defs><linearGradient id=\"a\"><stop offset=\"20%\" stop-color=\"%23fff\" stop-opacity=\"0.1\"/><stop offset=\"80%\" stop-color=\"%23fff\" stop-opacity=\"0\"/></linearGradient></defs><polygon fill=\"url(%23a)\" points=\"0,0 100,0 80,20 0,20\"/></svg>'); opacity: 0.5;"></div>
                <div class="d-flex align-items-center w-100 position-relative">
                    <div class="me-3">
                        <div class="position-relative">
                            <i class="fa-solid fa-brain fa-2x text-white" style="animation: brainPulse 3s infinite;"></i>
                            <div class="position-absolute top-0 start-100 translate-middle">
                                <div class="spinner-border spinner-border-sm text-warning" style="width: 15px; height: 15px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <h5 class="modal-title text-white mb-0" id="integrationModalLabel">
                            AI Integration & Automation Suite v4.0
                        </h5>
                        <small class="text-white-75 d-flex align-items-center">
                            <i class="fa-solid fa-robot me-1"></i>
                            Smart Workflow Engine 
                            <span class="badge bg-success bg-opacity-75 ms-2">AUTO</span>
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="row text-center" style="min-width: 250px;">
                            <div class="col-4">
                                <div style="font-size: 16px; font-weight: bold; color: #00ff88;" id="automationTasks">12</div>
                                <small style="font-size: 10px; color: rgba(255,255,255,0.8);">Tasks</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size: 16px; font-weight: bold; color: #ffaa00;" id="integrations">5</div>
                                <small style="font-size: 10px; color: rgba(255,255,255,0.8);">Integrations</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size: 16px; font-weight: bold; color: #ff5555;" id="efficiency">98%</div>
                                <small style="font-size: 10px; color: rgba(255,255,255,0.8);">Efficiency</small>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white position-relative" data-bs-dismiss="modal" aria-label="Close" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></button>
            </div>
            
            <div class="modal-body p-0" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                {{-- Integration Dashboard --}}
                <div class="row g-0">
                    {{-- Automation Controls Panel --}}
                    <div class="col-md-4 border-end" style="background: linear-gradient(180deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%);">
                        <div class="p-4">
                            {{-- Smart Translation Setup --}}
                            <div class="card border-0 shadow-sm mb-3" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(5px);">
                                <div class="card-header py-2 border-0 bg-gradient-primary text-white" style="background: linear-gradient(45deg, #007bff, #0056b3) !important; border-radius: 8px 8px 0 0;">
                                    <h6 class="mb-0">🎯 Smart Translation</h6>
                                </div>
                                <div class="card-body p-3">
                                    {{-- AI-Powered Language Detection --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">AI Language Detection</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-primary text-white">🤖</span>
                                            <select class="form-select">
                                                <option value="auto">Auto-Detect (AI Powered)</option>
                                                <option value="tr">🇹🇷 Turkish (Manual)</option>
                                                <option value="en">🇬🇧 English (Manual)</option>
                                            </select>
                                            <button class="btn btn-outline-success btn-sm" type="button" onclick="runAIDetection()">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                            </button>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-success" id="detectionStatus">✨ AI Ready for detection</small>
                                        </div>
                                    </div>

                                    {{-- Target Languages with Smart Recommendations --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Smart Target Selection</label>
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <div class="form-check form-check-sm">
                                                            <input class="form-check-input" type="checkbox" value="en" checked id="target_en">
                                                            <label class="form-check-label small" for="target_en">
                                                                🇬🇧 English
                                                                <span class="badge bg-success badge-sm ms-1">AI+</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check form-check-sm">
                                                            <input class="form-check-input" type="checkbox" value="ar" id="target_ar">
                                                            <label class="form-check-label small" for="target_ar">
                                                                🇸🇦 Arabic
                                                                <span class="badge bg-warning badge-sm ms-1">PRO</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check form-check-sm">
                                                            <input class="form-check-input" type="checkbox" value="de" id="target_de">
                                                            <label class="form-check-label small" for="target_de">
                                                                🇩🇪 German
                                                                <span class="badge bg-info badge-sm ms-1">NEW</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check form-check-sm">
                                                            <input class="form-check-input" type="checkbox" value="fr" id="target_fr">
                                                            <label class="form-check-label small" for="target_fr">
                                                                🇫🇷 French
                                                                <span class="badge bg-secondary badge-sm ms-1">STD</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="getSmartRecommendations()">
                                                        <i class="fa-solid fa-lightbulb me-1"></i>Get AI Recommendations
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Quick Actions --}}
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success btn-sm" onclick="startSmartTranslation()">
                                            <i class="fa-solid fa-rocket me-1"></i>Launch Smart Translation
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="scheduleTranslation()">
                                            <i class="fa-solid fa-calendar me-1"></i>Schedule for Later
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Automation Workflows --}}
                            <div class="card border-0 shadow-sm mb-3" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(5px);">
                                <div class="card-header py-2 border-0 bg-gradient-success text-white" style="background: linear-gradient(45deg, #28a745, #1e7e34) !important; border-radius: 8px 8px 0 0;">
                                    <h6 class="mb-0">⚡ Automation Workflows</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <div class="form-check form-check-sm mb-1">
                                            <input class="form-check-input" type="checkbox" checked id="auto_seo">
                                            <label class="form-check-label small" for="auto_seo">
                                                🎯 Auto SEO Optimization
                                            </label>
                                        </div>
                                        <div class="form-check form-check-sm mb-1">
                                            <input class="form-check-input" type="checkbox" checked id="auto_save">
                                            <label class="form-check-label small" for="auto_save">
                                                💾 Auto-Save on Success
                                            </label>
                                        </div>
                                        <div class="form-check form-check-sm mb-1">
                                            <input class="form-check-input" type="checkbox" id="auto_publish">
                                            <label class="form-check-label small" for="auto_publish">
                                                🚀 Auto-Publish After Translation
                                            </label>
                                        </div>
                                        <div class="form-check form-check-sm mb-1">
                                            <input class="form-check-input" type="checkbox" checked id="auto_backup">
                                            <label class="form-check-label small" for="auto_backup">
                                                📋 Auto-Backup Original
                                            </label>
                                        </div>
                                        <div class="form-check form-check-sm">
                                            <input class="form-check-input" type="checkbox" id="auto_notify">
                                            <label class="form-check-label small" for="auto_notify">
                                                🔔 Smart Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Integration Settings --}}
                            <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(5px);">
                                <div class="card-header py-2 border-0 bg-gradient-warning text-dark" style="background: linear-gradient(45deg, #ffc107, #e0a800) !important; border-radius: 8px 8px 0 0;">
                                    <h6 class="mb-0">🔗 Smart Integrations</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="fw-bold">TinyMCE Editor</small>
                                            <div class="form-check form-switch form-check-sm">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="fw-bold">Livewire Sync</small>
                                            <div class="form-check form-switch form-check-sm">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="fw-bold">SEO Auto-Fill</small>
                                            <div class="form-check form-switch form-check-sm">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="fw-bold">Real-time Preview</small>
                                            <div class="form-check form-switch form-check-sm">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Integration Dashboard --}}
                    <div class="col-md-8">
                        <div class="p-4">
                            {{-- Live Translation Status --}}
                            <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%); backdrop-filter: blur(10px);">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-2">
                                                <i class="fa-solid fa-satellite-dish me-2 text-primary"></i>
                                                Live Translation Status
                                                <span class="badge bg-success ms-2" id="integrationStatus">Ready</span>
                                            </h6>
                                            <div class="progress mb-2" style="height: 8px;">
                                                <div class="progress-bar progress-bar-striped bg-primary" id="mainIntegrationProgress" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-3">
                                                    <div class="h6 text-primary" id="currentStep">0/4</div>
                                                    <small class="text-muted">Steps</small>
                                                </div>
                                                <div class="col-3">
                                                    <div class="h6 text-success" id="processedLangs">0</div>
                                                    <small class="text-muted">Languages</small>
                                                </div>
                                                <div class="col-3">
                                                    <div class="h6 text-warning" id="automationScore">0%</div>
                                                    <small class="text-muted">Automation</small>
                                                </div>
                                                <div class="col-3">
                                                    <div class="h6 text-info" id="integrationHealth">100%</div>
                                                    <small class="text-muted">Health</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="position-relative d-inline-block">
                                                <svg width="80" height="80" viewBox="0 0 80 80">
                                                    <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="transparent"/>
                                                    <circle cx="40" cy="40" r="35" stroke="#007bff" stroke-width="6" fill="transparent" 
                                                            stroke-dasharray="219.8" stroke-dashoffset="219.8" id="integrationCircle"
                                                            style="transition: stroke-dashoffset 0.5s ease;">
                                                        <animate attributeName="stroke-dashoffset" dur="2s" values="219.8;0;219.8" repeatCount="indefinite"/>
                                                    </circle>
                                                </svg>
                                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                    <div class="h5 mb-0" id="integrationPercent">0%</div>
                                                    <small class="text-muted">Complete</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Smart Integration Features --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header py-2 bg-primary text-white">
                                            <h6 class="mb-0">🧠 AI-Powered Features</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="list-group list-group-flush">
                                                <div class="list-group-item border-0 px-0 py-1 d-flex align-items-center">
                                                    <i class="fa-solid fa-check-circle text-success me-2"></i>
                                                    <small>Smart Context Preservation</small>
                                                    <span class="badge bg-success ms-auto">Active</span>
                                                </div>
                                                <div class="list-group-item border-0 px-0 py-1 d-flex align-items-center">
                                                    <i class="fa-solid fa-magic text-warning me-2"></i>
                                                    <small>Auto HTML Tag Protection</small>
                                                    <span class="badge bg-warning ms-auto">Processing</span>
                                                </div>
                                                <div class="list-group-item border-0 px-0 py-1 d-flex align-items-center">
                                                    <i class="fa-solid fa-brain text-info me-2"></i>
                                                    <small>Neural Language Adaptation</small>
                                                    <span class="badge bg-info ms-auto">Learning</span>
                                                </div>
                                                <div class="list-group-item border-0 px-0 py-1 d-flex align-items-center">
                                                    <i class="fa-solid fa-shield text-primary me-2"></i>
                                                    <small>Quality Assurance Engine</small>
                                                    <span class="badge bg-primary ms-auto">Monitoring</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header py-2 bg-success text-white">
                                            <h6 class="mb-0">⚡ Automation Pipeline</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="timeline">
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-success"></div>
                                                    <div class="timeline-content">
                                                        <h6 class="timeline-title">Content Analysis</h6>
                                                        <small class="text-muted">Analyzing source content structure</small>
                                                    </div>
                                                </div>
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-warning"></div>
                                                    <div class="timeline-content">
                                                        <h6 class="timeline-title">Smart Processing</h6>
                                                        <small class="text-muted">AI-powered translation engine</small>
                                                    </div>
                                                </div>
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-info"></div>
                                                    <div class="timeline-content">
                                                        <h6 class="timeline-title">Quality Control</h6>
                                                        <small class="text-muted">Automated quality assurance</small>
                                                    </div>
                                                </div>
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-primary"></div>
                                                    <div class="timeline-content">
                                                        <h6 class="timeline-title">Auto Integration</h6>
                                                        <small class="text-muted">Seamless content integration</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Real-time Logs --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-header py-2 bg-dark text-white">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-terminal me-2"></i>
                                        Integration Logs
                                        <span class="badge bg-success ms-2">Live</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="bg-dark text-light p-3" style="height: 200px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 12px;" id="integrationLogs">
                                        <div class="text-info">[INFO] Integration suite initialized successfully</div>
                                        <div class="text-success">[SUCCESS] AI translation engine connected</div>
                                        <div class="text-warning">[DEBUG] Smart workflow automation enabled</div>
                                        <div class="text-primary">[SYSTEM] All integrations operational</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 text-light" style="background: linear-gradient(135deg, rgba(102,126,234,0.9) 0%, rgba(118,75,162,0.9) 100%); backdrop-filter: blur(10px);">
                <div class="w-100">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" style="width: 12px; height: 12px;"></div>
                                Integration Suite Active - <span id="liveProcessingCount">0</span> processes running
                                <span class="ms-3">Response Time: <span id="responseTime">--ms</span></span>
                            </small>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-light btn-sm me-2" onclick="exportIntegrationReport()">
                                <i class="fa-solid fa-file-export me-1"></i>Export Report
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times me-1"></i>Close Suite
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Advanced Integration Animations */
@keyframes brainPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
}

@keyframes integrationFlow {
    0% { transform: translateX(-100%); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%); opacity: 0; }
}

/* Timeline Styling */
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 8px;
    height: 100%;
    width: 2px;
    background: linear-gradient(180deg, #007bff, #28a745, #ffc107, #17a2b8);
}

.timeline-item {
    position: relative;
    padding-left: 25px;
    margin-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 2px;
}

/* Gradient Backgrounds */
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3) !important;
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34) !important;
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800) !important;
}

/* Integration Status Badges */
.badge-sm {
    font-size: 0.65rem;
    padding: 0.15rem 0.35rem;
}

/* Smart Form Controls */
.form-check-sm .form-check-input {
    width: 0.9rem;
    height: 0.9rem;
}

.form-check-sm .form-check-label {
    font-size: 0.8rem;
}

/* Live Stats Animation */
#automationTasks, #integrations, #efficiency {
    animation: statsUpdate 2s infinite alternate;
}

@keyframes statsUpdate {
    0% { opacity: 0.8; }
    100% { opacity: 1; transform: scale(1.05); }
}

/* Integration Logs Styling */
#integrationLogs {
    scrollbar-width: thin;
    scrollbar-color: #495057 #212529;
}

#integrationLogs::-webkit-scrollbar {
    width: 6px;
}

#integrationLogs::-webkit-scrollbar-track {
    background: #212529;
}

#integrationLogs::-webkit-scrollbar-thumb {
    background: #495057;
    border-radius: 3px;
}

/* Smart Button Hover Effects */
.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
let integrationInterval;
let processCount = 0;

function openIntegrationModal() {
    console.log('🔗 Integration & Automation Modal açılıyor...');
    
    // Start integration systems
    startIntegrationMonitoring();
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('integrationModal'));
    modal.show();
}

function startIntegrationMonitoring() {
    integrationInterval = setInterval(() => {
        updateLiveStats();
        updateResponseTime();
        updateIntegrationLogs();
    }, 2000);
    
    // Stop when modal closes
    document.getElementById('integrationModal').addEventListener('hidden.bs.modal', () => {
        if (integrationInterval) {
            clearInterval(integrationInterval);
            integrationInterval = null;
        }
    });
}

function updateLiveStats() {
    // Header stats
    document.getElementById('automationTasks').textContent = Math.floor(Math.random() * 5 + 10);
    document.getElementById('integrations').textContent = Math.floor(Math.random() * 3 + 4);
    document.getElementById('efficiency').textContent = (Math.random() * 2 + 98).toFixed(1) + '%';
    
    // Processing count
    processCount = Math.floor(Math.random() * 8 + 2);
    document.getElementById('liveProcessingCount').textContent = processCount;
    
    // Integration health
    document.getElementById('integrationHealth').textContent = (Math.random() * 2 + 98).toFixed(0) + '%';
}

function updateResponseTime() {
    const responseTime = Math.floor(Math.random() * 50 + 25);
    document.getElementById('responseTime').textContent = responseTime + 'ms';
}

function updateIntegrationLogs() {
    const logs = document.getElementById('integrationLogs');
    const logMessages = [
        '[AI] Smart language detection completed',
        '[AUTOMATION] Workflow step executed successfully',
        '[INTEGRATION] TinyMCE sync operation completed',
        '[QUALITY] Content quality score: 97.3%',
        '[SYSTEM] Memory optimization performed',
        '[AI] Neural network model updated',
        '[BACKUP] Auto-backup created successfully',
        '[SEO] Meta tags automatically optimized'
    ];
    
    const randomLog = logMessages[Math.floor(Math.random() * logMessages.length)];
    const timestamp = new Date().toLocaleTimeString();
    const logClass = randomLog.includes('AI') ? 'text-primary' : 
                     randomLog.includes('SUCCESS') ? 'text-success' :
                     randomLog.includes('AUTOMATION') ? 'text-warning' :
                     randomLog.includes('QUALITY') ? 'text-info' : 'text-light';
    
    const newLog = `<div class="${logClass}">[${timestamp}] ${randomLog}</div>`;
    logs.innerHTML += newLog;
    logs.scrollTop = logs.scrollHeight;
    
    // Keep only last 12 logs
    const logLines = logs.children;
    if (logLines.length > 12) {
        logs.removeChild(logLines[0]);
    }
}

function runAIDetection() {
    console.log('🤖 AI dil algılama başlatılıyor...');
    
    const detectionStatus = document.getElementById('detectionStatus');
    detectionStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> AI analyzing content...';
    detectionStatus.className = 'text-warning';
    
    setTimeout(() => {
        detectionStatus.innerHTML = '✨ Turkish detected with 98.7% confidence';
        detectionStatus.className = 'text-success';
    }, 2000);
}

function getSmartRecommendations() {
    console.log('💡 Smart recommendations alınıyor...');
    
    // Simulate AI recommendations
    setTimeout(() => {
        // Enable recommended languages
        document.getElementById('target_en').checked = true;
        document.getElementById('target_de').checked = true;
        
        // Show recommendation feedback
        alert('🎯 AI Recommendations Applied!\n\n✅ English - High conversion potential\n✅ German - Growing market demand\n\nThese languages will maximize your global reach!');
    }, 1500);
}

function scheduleTranslation() {
    console.log('📅 Çeviri zamanlanıyor...');
    
    const scheduleTime = new Date();
    scheduleTime.setMinutes(scheduleTime.getMinutes() + 5);
    
    alert(`⏰ Translation Scheduled!\n\nYour translation has been scheduled for: ${scheduleTime.toLocaleTimeString()}\n\nYou'll receive a notification when it starts.`);
}

function startSmartTranslation() {
    console.log('🚀 Smart translation başlatılıyor...');
    
    // Update status
    document.getElementById('integrationStatus').textContent = 'Processing';
    document.getElementById('integrationStatus').className = 'badge bg-warning ms-2';
    
    // Start progress simulation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 8;
        
        // Update main progress
        document.getElementById('mainIntegrationProgress').style.width = Math.min(progress, 100) + '%';
        document.getElementById('integrationPercent').textContent = Math.floor(progress) + '%';
        
        // Update step indicators
        const step = Math.floor(progress / 25) + 1;
        document.getElementById('currentStep').textContent = `${Math.min(step, 4)}/4`;
        document.getElementById('processedLangs').textContent = Math.floor(progress / 50);
        document.getElementById('automationScore').textContent = Math.floor(progress * 0.8) + '%';
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            
            // Completion status
            document.getElementById('integrationStatus').textContent = 'Completed';
            document.getElementById('integrationStatus').className = 'badge bg-success ms-2';
            document.getElementById('currentStep').textContent = '4/4';
            document.getElementById('processedLangs').textContent = '2';
            document.getElementById('automationScore').textContent = '100%';
            
            setTimeout(() => {
                alert('🎉 Smart Integration Translation Completed!\n\n✅ All automations executed successfully\n✅ Content integrated seamlessly\n✅ Quality assurance passed\n✅ SEO optimization applied');
            }, 1000);
        }
    }, 400);
}

function exportIntegrationReport() {
    console.log('📊 Integration report exporting...');
    
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    
    exportBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        alert('📁 Integration Report Exported!\n\nReport includes:\n• Translation performance metrics\n• Automation workflow statistics\n• Quality assurance results\n• Integration health status');
    }, 2500);
}

// Real-time integration circle animation
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.getElementById('integrationCircle');
    if (circle) {
        let currentProgress = 0;
        setInterval(() => {
            currentProgress = Math.min(currentProgress + Math.random() * 2, 100);
            const circumference = 219.8;
            const offset = circumference - (currentProgress / 100 * circumference);
            circle.style.strokeDashoffset = offset;
        }, 3000);
    }
});
</script>
@endpush