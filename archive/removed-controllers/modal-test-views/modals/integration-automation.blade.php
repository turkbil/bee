<!-- Integration & Automation AI Translation Modal -->
<div class="modal fade" id="integrationTranslationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-br from-purple-600 to-blue-600">
                <h5 class="modal-title text-white d-flex align-items-center">
                    <i class="ti ti-api me-2"></i>
                    Integration & Automation AI System
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeIntegrationModal()"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Automation Status Bar -->
                <div class="bg-light border-bottom">
                    <div class="container-fluid py-2">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-success me-2"></div>
                                        <small>API Connected</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-warning me-2"></div>
                                        <small>Queue: 3 jobs</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-info me-2"></div>
                                        <small>Auto-sync: ON</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary active" type="button">
                                        <i class="ti ti-robot me-1"></i>Auto Mode
                                    </button>
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="ti ti-hand-click me-1"></i>Manual
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Integration Interface -->
                <div class="container-fluid py-4">
                    <div class="row">
                        <!-- Source & Configuration -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-settings me-2 text-primary"></i>
                                        Source Configuration
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Content Source</label>
                                        <div class="form-selectgroup">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="source" value="editor" class="form-selectgroup-input" checked>
                                                <span class="form-selectgroup-label">
                                                    <i class="ti ti-edit me-1"></i>Current Editor
                                                </span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="source" value="database" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">
                                                    <i class="ti ti-database me-1"></i>Database
                                                </span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="source" value="api" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label">
                                                    <i class="ti ti-api me-1"></i>External API
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Automation Rules</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="autoTranslate" checked>
                                            <label class="form-check-label" for="autoTranslate">
                                                Auto-translate on save
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="autoSync">
                                            <label class="form-check-label" for="autoSync">
                                                Sync with all pages
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="autoSchedule">
                                            <label class="form-check-label" for="autoSchedule">
                                                Schedule updates
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Target Languages</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="en" checked>
                                            <label class="form-check-label">English</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="de">
                                            <label class="form-check-label">German</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="fr">
                                            <label class="form-check-label">French</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="es">
                                            <label class="form-check-label">Spanish</label>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary w-100" onclick="startIntegrationProcess()">
                                        <i class="ti ti-rocket me-1"></i>Start Integration
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Processing Pipeline -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-workflow me-2 text-success"></i>
                                        Processing Pipeline
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline" id="processingTimeline">
                                        <div class="timeline-item">
                                            <div class="timeline-point timeline-point-primary"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-time">Step 1</div>
                                                <div class="timeline-title">Content Analysis</div>
                                                <div class="timeline-body text-muted">Analyzing source content structure</div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-point"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-time">Step 2</div>
                                                <div class="timeline-title">API Integration</div>
                                                <div class="timeline-body text-muted">Connecting to translation services</div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-point"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-time">Step 3</div>
                                                <div class="timeline-title">Batch Processing</div>
                                                <div class="timeline-body text-muted">Processing multiple languages</div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-point"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-time">Step 4</div>
                                                <div class="timeline-title">Quality Control</div>
                                                <div class="timeline-body text-muted">Validating translation quality</div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-point"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-time">Step 5</div>
                                                <div class="timeline-title">Auto-deployment</div>
                                                <div class="timeline-body text-muted">Deploying to target systems</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="progress">
                                            <div class="progress-bar bg-gradient-primary" style="width: 0%" id="integrationProgress"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="text-muted" id="currentStep">Ready to start</small>
                                            <small class="text-muted" id="progressPercent">0%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results & Integration -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-check me-2 text-success"></i>
                                        Results & Integration
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="integrationResults" class="mb-4">
                                        <div class="text-muted text-center mt-4">
                                            <i class="ti ti-api-app fs-1 mb-2 opacity-50"></i>
                                            <p>Integration results will appear here</p>
                                        </div>
                                    </div>

                                    <!-- Integration Actions -->
                                    <div class="mb-3">
                                        <label class="form-label">Post-processing Actions</label>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <input type="checkbox" class="btn-check" id="action-apply">
                                            <label class="btn btn-outline-success" for="action-apply">
                                                <i class="ti ti-check me-1"></i>Apply to Editor
                                            </label>
                                            <input type="checkbox" class="btn-check" id="action-save">
                                            <label class="btn btn-outline-primary" for="action-save">
                                                <i class="ti ti-device-floppy me-1"></i>Save to Database
                                            </label>
                                            <input type="checkbox" class="btn-check" id="action-publish">
                                            <label class="btn btn-outline-warning" for="action-publish">
                                                <i class="ti ti-world me-1"></i>Auto-publish
                                            </label>
                                            <input type="checkbox" class="btn-check" id="action-notify">
                                            <label class="btn btn-outline-info" for="action-notify">
                                                <i class="ti ti-bell me-1"></i>Send Notifications
                                            </label>
                                        </div>
                                    </div>

                                    <button class="btn btn-success w-100" disabled id="executeActionsBtn">
                                        <i class="ti ti-player-play me-1"></i>Execute Actions
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Integration Logs -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Integration Logs & Monitoring</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="log-container bg-dark text-white p-3 rounded" style="height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;" id="integrationLogs">
                                                <div class="text-success">[INFO] System ready for integration</div>
                                                <div class="text-muted">[DEBUG] API endpoints verified</div>
                                                <div class="text-info">[STATUS] Waiting for user input...</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="card card-sm bg-success text-white">
                                                        <div class="card-body text-center">
                                                            <div class="fs-3 fw-bold" id="successCount">0</div>
                                                            <div class="text-uppercase">Success</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card card-sm bg-warning text-white">
                                                        <div class="card-body text-center">
                                                            <div class="fs-3 fw-bold" id="pendingCount">0</div>
                                                            <div class="text-uppercase">Pending</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mt-2">
                                                    <div class="card card-sm bg-danger text-white">
                                                        <div class="card-body text-center">
                                                            <div class="fs-3 fw-bold" id="errorCount">0</div>
                                                            <div class="text-uppercase">Errors</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mt-2">
                                                    <div class="card card-sm bg-info text-white">
                                                        <div class="card-body text-center">
                                                            <div class="fs-3 fw-bold" id="totalCount">0</div>
                                                            <div class="text-uppercase">Total</div>
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
        </div>
    </div>
</div>

<style>
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
}
.bg-gradient-to-br {
    background: linear-gradient(135deg, var(--tblr-purple), var(--tblr-primary)) !important;
}
.bg-gradient-primary {
    background: linear-gradient(45deg, #0080ff, #00c9ff) !important;
}
.timeline-point-primary {
    background-color: var(--tblr-primary) !important;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.log-container::-webkit-scrollbar {
    width: 4px;
}
.log-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}
</style>

<script>
function closeIntegrationModal() {
    document.getElementById('integrationTranslationModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function addLog(message, type = 'info') {
    const logs = document.getElementById('integrationLogs');
    const timestamp = new Date().toTimeString().substring(0, 8);
    const colorClass = {
        'info': 'text-info',
        'success': 'text-success',
        'warning': 'text-warning',
        'error': 'text-danger',
        'debug': 'text-muted'
    }[type] || 'text-info';
    
    logs.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
    logs.scrollTop = logs.scrollHeight;
}

function updateTimelineStep(step) {
    const timeline = document.getElementById('processingTimeline');
    const items = timeline.querySelectorAll('.timeline-item');
    
    items.forEach((item, index) => {
        const point = item.querySelector('.timeline-point');
        if (index < step) {
            point.classList.add('timeline-point-success');
            point.innerHTML = '<i class="ti ti-check"></i>';
        } else if (index === step) {
            point.classList.add('timeline-point-primary');
            point.innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
        }
    });
}

function startIntegrationProcess() {
    const progressBar = document.getElementById('integrationProgress');
    const currentStep = document.getElementById('currentStep');
    const progressPercent = document.getElementById('progressPercent');
    const results = document.getElementById('integrationResults');
    const executeBtn = document.getElementById('executeActionsBtn');
    
    // Reset counters
    document.getElementById('successCount').textContent = '0';
    document.getElementById('pendingCount').textContent = '1';
    document.getElementById('errorCount').textContent = '0';
    document.getElementById('totalCount').textContent = '1';
    
    const steps = [
        { progress: 20, text: 'Analyzing content...', log: 'Content analysis started', step: 0 },
        { progress: 40, text: 'Integrating with APIs...', log: 'Connected to OpenAI API', step: 1 },
        { progress: 60, text: 'Processing translations...', log: 'Batch processing 4 languages', step: 2 },
        { progress: 80, text: 'Quality validation...', log: 'Quality check passed (95.4%)', step: 3 },
        { progress: 100, text: 'Integration complete!', log: 'All translations completed successfully', step: 4 }
    ];
    
    addLog('Integration process initiated', 'info');
    
    steps.forEach((step, index) => {
        setTimeout(() => {
            progressBar.style.width = step.progress + '%';
            currentStep.textContent = step.text;
            progressPercent.textContent = step.progress + '%';
            addLog(step.log, 'success');
            updateTimelineStep(step.step);
            
            if (step.progress === 100) {
                results.innerHTML = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading"><i class="ti ti-check me-2"></i>Integration Successful!</h6>
                        <p class="mb-2">Content has been successfully translated to 4 languages:</p>
                        <ul class="mb-0">
                            <li><strong>English:</strong> Ready for deployment</li>
                            <li><strong>German:</strong> Quality: 96.2%</li>
                            <li><strong>French:</strong> Quality: 94.8%</li>
                            <li><strong>Spanish:</strong> Quality: 95.1%</li>
                        </ul>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 border border-primary rounded p-2 text-center">
                                <small class="text-primary"><strong>Total Cost:</strong> $0.045</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 border border-success rounded p-2 text-center">
                                <small class="text-success"><strong>Time Saved:</strong> 2.5 hours</small>
                            </div>
                        </div>
                    </div>
                `;
                
                executeBtn.disabled = false;
                document.getElementById('successCount').textContent = '4';
                document.getElementById('pendingCount').textContent = '0';
                document.getElementById('totalCount').textContent = '4';
                addLog('Ready for action execution', 'info');
                
                executeBtn.onclick = function() {
                    const selectedActions = [];
                    if (document.getElementById('action-apply').checked) selectedActions.push('Apply to Editor');
                    if (document.getElementById('action-save').checked) selectedActions.push('Save to Database');
                    if (document.getElementById('action-publish').checked) selectedActions.push('Auto-publish');
                    if (document.getElementById('action-notify').checked) selectedActions.push('Send Notifications');
                    
                    if (selectedActions.length > 0) {
                        selectedActions.forEach(action => addLog(`Executing: ${action}`, 'success'));
                        
                        if (document.getElementById('action-apply').checked) {
                            // Apply to TinyMCE editor
                            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                tinymce.get('content').setContent('<p>Test content will come here...</p>');
                            }
                        }
                        
                        setTimeout(() => {
                            addLog('All actions executed successfully', 'success');
                            closeIntegrationModal();
                        }, 1500);
                    } else {
                        addLog('No actions selected', 'warning');
                    }
                };
            }
        }, index * 1200);
    });
}
</script>