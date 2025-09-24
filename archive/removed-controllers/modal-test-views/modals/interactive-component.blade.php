<!-- Interactive Component System AI Translation Modal -->
<div class="modal fade" id="interactiveTranslationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-r from-blue-600 to-purple-600">
                <h5 class="modal-title text-white d-flex align-items-center">
                    <i class="ti ti-components me-2"></i>
                    Interactive AI Translation System
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeInteractiveModal()"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Component Selection Bar -->
                <div class="border-bottom bg-light">
                    <div class="container-fluid py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="component-type" id="comp-translate" checked>
                                    <label class="btn btn-outline-primary" for="comp-translate">
                                        <i class="ti ti-language me-1"></i>Translate
                                    </label>
                                    <input type="radio" class="btn-check" name="component-type" id="comp-enhance">
                                    <label class="btn btn-outline-success" for="comp-enhance">
                                        <i class="ti ti-sparkles me-1"></i>Enhance
                                    </label>
                                    <input type="radio" class="btn-check" name="component-type" id="comp-optimize">
                                    <label class="btn btn-outline-warning" for="comp-optimize">
                                        <i class="ti ti-rocket me-1"></i>Optimize
                                    </label>
                                    <input type="radio" class="btn-check" name="component-type" id="comp-analyze">
                                    <label class="btn btn-outline-info" for="comp-analyze">
                                        <i class="ti ti-analyze me-1"></i>Analyze
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-settings me-1"></i>Settings
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ti ti-palette me-1"></i>Theme</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ti ti-adjustments me-1"></i>Preferences</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Workspace -->
                <div class="container-fluid py-4" style="min-height: 500px;">
                    <div class="row">
                        <!-- Input Panel -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-edit me-2 text-primary"></i>
                                        Source Content
                                        <span class="badge bg-primary ms-auto">Turkish</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="sourceContent" style="height: 300px;" placeholder="Enter content to translate...">Test içerik buraya gelecek...</textarea>
                                            <label for="sourceContent">Enter your content here</label>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="startInteractiveTranslation()">
                                            <i class="ti ti-play me-1"></i>Process
                                        </button>
                                        <button class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh me-1"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Output Panel -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-check me-2 text-success"></i>
                                        Processed Content
                                        <div class="ms-auto">
                                            <select class="form-select form-select-sm" style="width: auto;">
                                                <option value="en">English</option>
                                                <option value="de">German</option>
                                                <option value="fr">French</option>
                                                <option value="es">Spanish</option>
                                            </select>
                                        </div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div id="outputContent" class="border rounded p-3" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
                                            <div class="text-muted text-center mt-5">
                                                <i class="ti ti-robot fs-1 mb-2"></i>
                                                <p>Click "Process" to see AI-generated content</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success" disabled id="applyBtn">
                                            <i class="ti ti-check me-1"></i>Apply
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="ti ti-copy me-1"></i>Copy
                                        </button>
                                        <button class="btn btn-outline-warning">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress and Status -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-gradient" role="progressbar" style="width: 0%" id="interactiveProgress"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted" id="statusText">Ready to process</small>
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
.bg-gradient-to-r {
    background: linear-gradient(to right, var(--tblr-primary), var(--tblr-purple)) !important;
}
.bg-gradient {
    background: linear-gradient(45deg, #3b82f6, #8b5cf6) !important;
}
.btn-check:checked + .btn-outline-primary {
    background: linear-gradient(45deg, #3b82f6, #1d4ed8);
    border-color: #3b82f6;
    color: white;
}
.btn-check:checked + .btn-outline-success {
    background: linear-gradient(45deg, #10b981, #059669);
    border-color: #10b981;
    color: white;
}
.btn-check:checked + .btn-outline-warning {
    background: linear-gradient(45deg, #f59e0b, #d97706);
    border-color: #f59e0b;
    color: white;
}
.btn-check:checked + .btn-outline-info {
    background: linear-gradient(45deg, #06b6d4, #0891b2);
    border-color: #06b6d4;
    color: white;
}
</style>

<script>
function closeInteractiveModal() {
    document.getElementById('interactiveTranslationModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function startInteractiveTranslation() {
    const progressBar = document.getElementById('interactiveProgress');
    const statusText = document.getElementById('statusText');
    const outputContent = document.getElementById('outputContent');
    const applyBtn = document.getElementById('applyBtn');
    
    // Reset
    progressBar.style.width = '0%';
    applyBtn.disabled = true;
    
    // Simulate processing
    const steps = [
        { progress: 20, text: 'Analyzing content...' },
        { progress: 40, text: 'Processing with AI...' },
        { progress: 60, text: 'Optimizing translation...' },
        { progress: 80, text: 'Generating result...' },
        { progress: 100, text: 'Complete!' }
    ];
    
    steps.forEach((step, index) => {
        setTimeout(() => {
            progressBar.style.width = step.progress + '%';
            statusText.textContent = step.text;
            
            if (step.progress === 100) {
                outputContent.innerHTML = `
                    <div class="p-3">
                        <h6 class="text-success mb-3"><i class="ti ti-check me-2"></i>Translation Complete</h6>
                        <p>Test content will come here...</p>
                        <div class="bg-success bg-opacity-10 border border-success rounded p-2 mt-3">
                            <small class="text-success"><i class="ti ti-info-circle me-1"></i>Quality Score: 95%</small>
                        </div>
                    </div>
                `;
                applyBtn.disabled = false;
                applyBtn.onclick = function() {
                    // Apply to editor
                    if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                        tinymce.get('content').setContent('<p>Test content will come here...</p>');
                    }
                    closeInteractiveModal();
                };
            }
        }, index * 800);
    });
}
</script>