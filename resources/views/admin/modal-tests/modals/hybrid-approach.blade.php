<!-- Hybrid Approach AI Translation Modal -->
<div class="modal fade" id="hybridTranslationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-hybrid">
                <h5 class="modal-title text-white d-flex align-items-center">
                    <i class="ti ti-layout-grid-add me-2"></i>
                    Hybrid AI Translation Suite
                    <span class="badge bg-light text-dark ms-2 fs-6">v2.0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeHybridModal()"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Smart Mode Selection -->
                <div class="bg-light border-bottom">
                    <div class="container-fluid py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="hybridMode" id="quickMode" checked>
                                    <label class="btn btn-outline-success" for="quickMode">
                                        <i class="ti ti-zap me-1"></i>Quick Mode
                                    </label>
                                    <input type="radio" class="btn-check" name="hybridMode" id="smartMode">
                                    <label class="btn btn-outline-primary" for="smartMode">
                                        <i class="ti ti-brain me-1"></i>Smart Mode
                                    </label>
                                    <input type="radio" class="btn-check" name="hybridMode" id="proMode">
                                    <label class="btn btn-outline-warning" for="proMode">
                                        <i class="ti ti-adjustments me-1"></i>Pro Mode
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success" id="aiStatus">AI Ready</span>
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ti ti-settings-2 me-1"></i>Config
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ti ti-palette me-1"></i>Theme Preferences</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ti ti-device-floppy me-1"></i>Save Settings</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#"><i class="ti ti-refresh me-1"></i>Reset to Default</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hybrid Content Area -->
                <div class="container-fluid py-4">
                    <div class="row">
                        <!-- Left Panel: Input & Configuration -->
                        <div class="col-md-6">
                            <!-- Dynamic Mode Panel -->
                            <div class="card mb-4" id="modePanel">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center" id="modePanelTitle">
                                        <i class="ti ti-zap me-2 text-success"></i>
                                        Quick Translation
                                    </h6>
                                </div>
                                <div class="card-body" id="modePanelContent">
                                    <!-- Quick Mode Content (Default) -->
                                    <div id="quickModeContent">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label class="form-label">From</label>
                                                <select class="form-select">
                                                    <option value="tr" selected>Turkish</option>
                                                    <option value="en">English</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">To</label>
                                                <select class="form-select">
                                                    <option value="en" selected>English</option>
                                                    <option value="de">German</option>
                                                    <option value="fr">French</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button class="btn btn-success w-100" onclick="startHybridTranslation('quick')">
                                            <i class="ti ti-zap me-1"></i>Quick Translate
                                        </button>
                                    </div>

                                    <!-- Smart Mode Content -->
                                    <div id="smartModeContent" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Translation Style</label>
                                            <select class="form-select">
                                                <option>Professional</option>
                                                <option>Casual</option>
                                                <option>Technical</option>
                                                <option>Creative</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Target Audience</label>
                                            <select class="form-select">
                                                <option>General Public</option>
                                                <option>Business Professionals</option>
                                                <option>Academic</option>
                                                <option>Technical Specialists</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="startHybridTranslation('smart')">
                                            <i class="ti ti-brain me-1"></i>Smart Translate
                                        </button>
                                    </div>

                                    <!-- Pro Mode Content -->
                                    <div id="proModeContent" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">AI Model</label>
                                            <select class="form-select">
                                                <option>GPT-4 Turbo</option>
                                                <option>GPT-3.5</option>
                                                <option>Claude 3</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Custom Prompt</label>
                                            <textarea class="form-control" rows="3" placeholder="Enter custom instructions..."></textarea>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="proAnalytics">
                                            <label class="form-check-label" for="proAnalytics">
                                                Enable detailed analytics
                                            </label>
                                        </div>
                                        <button class="btn btn-warning w-100" onclick="startHybridTranslation('pro')">
                                            <i class="ti ti-adjustments me-1"></i>Pro Translate
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Source Content -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-file-text me-2 text-primary"></i>
                                        Source Content
                                        <div class="ms-auto">
                                            <span class="badge bg-secondary" id="wordCount">3 words</span>
                                        </div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="hybridSourceContent" style="height: 200px;" 
                                                onkeyup="updateWordCount()" placeholder="Enter content to translate...">Test içerik buraya gelecek...</textarea>
                                        <label for="hybridSourceContent">Enter your content here</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Output & Analytics -->
                        <div class="col-md-6">
                            <!-- Translation Output -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-check me-2 text-success"></i>
                                        Translation Result
                                        <div class="ms-auto" id="outputActions" style="display: none;">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" onclick="copyTranslation()">
                                                    <i class="ti ti-copy"></i>
                                                </button>
                                                <button class="btn btn-outline-warning" onclick="editTranslation()">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="applyTranslation()">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="hybridOutput" class="border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                        <div class="text-muted text-center mt-4">
                                            <i class="ti ti-layout-grid-add fs-1 mb-2 opacity-50"></i>
                                            <p>Choose a mode and start translation</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Adaptive Analytics Panel -->
                            <div class="card mt-4" id="analyticsPanel" style="display: none;">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-chart-bar me-2 text-info"></i>
                                        Translation Analytics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="analyticsContent">
                                        <!-- Dynamic analytics content based on selected mode -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress and Status Bar -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-gradient-mixed" style="width: 0%" id="hybridProgress"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted" id="hybridStatus">Ready to translate</small>
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

<style>
.bg-gradient-hybrid {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
}
.bg-gradient-mixed {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
}
.btn-check:checked + .btn-outline-success {
    background: linear-gradient(45deg, #10b981, #059669);
    border-color: #10b981;
    color: white;
}
.btn-check:checked + .btn-outline-primary {
    background: linear-gradient(45deg, #3b82f6, #1d4ed8);
    border-color: #3b82f6;
    color: white;
}
.btn-check:checked + .btn-outline-warning {
    background: linear-gradient(45deg, #f59e0b, #d97706);
    border-color: #f59e0b;
    color: white;
}
</style>

<script>
function closeHybridModal() {
    document.getElementById('hybridTranslationModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function updateWordCount() {
    const content = document.getElementById('hybridSourceContent').value;
    const words = content.trim().split(/\s+/).filter(word => word.length > 0).length;
    document.getElementById('wordCount').textContent = words + ' words';
}

// Mode switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const modeInputs = document.querySelectorAll('input[name="hybridMode"]');
    
    modeInputs.forEach(input => {
        input.addEventListener('change', function() {
            switchMode(this.id);
        });
    });
});

function switchMode(modeId) {
    const modes = {
        'quickMode': {
            title: '<i class="ti ti-zap me-2 text-success"></i>Quick Translation',
            content: 'quickModeContent'
        },
        'smartMode': {
            title: '<i class="ti ti-brain me-2 text-primary"></i>Smart Translation',
            content: 'smartModeContent'
        },
        'proMode': {
            title: '<i class="ti ti-adjustments me-2 text-warning"></i>Professional Translation',
            content: 'proModeContent'
        }
    };
    
    // Hide all content divs
    document.getElementById('quickModeContent').style.display = 'none';
    document.getElementById('smartModeContent').style.display = 'none';
    document.getElementById('proModeContent').style.display = 'none';
    
    // Show selected content and update title
    document.getElementById(modes[modeId].content).style.display = 'block';
    document.getElementById('modePanelTitle').innerHTML = modes[modeId].title;
}

function startHybridTranslation(mode) {
    const progressBar = document.getElementById('hybridProgress');
    const status = document.getElementById('hybridStatus');
    const output = document.getElementById('hybridOutput');
    const outputActions = document.getElementById('outputActions');
    const analyticsPanel = document.getElementById('analyticsPanel');
    const aiStatus = document.getElementById('aiStatus');
    
    // Reset
    progressBar.style.width = '0%';
    outputActions.style.display = 'none';
    analyticsPanel.style.display = 'none';
    aiStatus.textContent = 'Processing...';
    aiStatus.className = 'badge bg-warning';
    
    const modeConfigs = {
        'quick': {
            steps: [
                { progress: 30, text: 'Quick analysis...', duration: 500 },
                { progress: 70, text: 'Fast translation...', duration: 800 },
                { progress: 100, text: 'Complete!', duration: 300 }
            ],
            analytics: `
                <div class="col-12">
                    <div class="bg-success bg-opacity-10 border border-success rounded p-2 text-center">
                        <small class="text-success"><strong>Speed:</strong> 1.6s | <strong>Quality:</strong> 94%</small>
                    </div>
                </div>
            `
        },
        'smart': {
            steps: [
                { progress: 20, text: 'Context analysis...', duration: 700 },
                { progress: 45, text: 'Style optimization...', duration: 900 },
                { progress: 75, text: 'Quality enhancement...', duration: 800 },
                { progress: 100, text: 'Smart complete!', duration: 400 }
            ],
            analytics: `
                <div class="col-6">
                    <div class="bg-primary bg-opacity-10 border border-primary rounded p-2 text-center">
                        <small class="text-primary"><strong>Context:</strong> 97%</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-success bg-opacity-10 border border-success rounded p-2 text-center">
                        <small class="text-success"><strong>Style:</strong> 95%</small>
                    </div>
                </div>
            `
        },
        'pro': {
            steps: [
                { progress: 15, text: 'Model initialization...', duration: 600 },
                { progress: 35, text: 'Custom prompt processing...', duration: 800 },
                { progress: 60, text: 'Advanced translation...', duration: 1000 },
                { progress: 85, text: 'Analytics generation...', duration: 700 },
                { progress: 100, text: 'Professional complete!', duration: 400 }
            ],
            analytics: `
                <div class="col-4">
                    <div class="bg-warning bg-opacity-10 border border-warning rounded p-2 text-center">
                        <small class="text-warning"><strong>Model:</strong> GPT-4</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-info bg-opacity-10 border border-info rounded p-2 text-center">
                        <small class="text-info"><strong>Tokens:</strong> 245</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-success bg-opacity-10 border border-success rounded p-2 text-center">
                        <small class="text-success"><strong>Score:</strong> 96.8%</small>
                    </div>
                </div>
            `
        }
    };
    
    const config = modeConfigs[mode];
    let currentStep = 0;
    
    function executeStep() {
        if (currentStep < config.steps.length) {
            const step = config.steps[currentStep];
            progressBar.style.width = step.progress + '%';
            status.textContent = step.text;
            
            setTimeout(() => {
                currentStep++;
                executeStep();
            }, step.duration);
        } else {
            // Translation complete
            output.innerHTML = `
                <div class="p-3">
                    <h6 class="text-success mb-3">
                        <i class="ti ti-check me-2"></i>
                        ${mode.charAt(0).toUpperCase() + mode.slice(1)} Translation Complete
                    </h6>
                    <p class="mb-3">Test content will come here...</p>
                    <div class="mt-3">
                        <small class="text-muted">
                            Translated using ${mode} mode with optimized ${mode === 'quick' ? 'speed' : mode === 'smart' ? 'intelligence' : 'precision'}.
                        </small>
                    </div>
                </div>
            `;
            
            outputActions.style.display = 'block';
            analyticsPanel.style.display = 'block';
            document.getElementById('analyticsContent').innerHTML = config.analytics;
            aiStatus.textContent = 'Complete';
            aiStatus.className = 'badge bg-success';
        }
    }
    
    executeStep();
}

function copyTranslation() {
    navigator.clipboard.writeText('Test content will come here...');
    // Show toast or notification
}

function editTranslation() {
    // Open edit interface
    alert('Edit mode would open an inline editor here');
}

function applyTranslation() {
    if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
        tinymce.get('content').setContent('<p>Test content will come here...</p>');
    }
    closeHybridModal();
}

// Initialize
updateWordCount();
</script>