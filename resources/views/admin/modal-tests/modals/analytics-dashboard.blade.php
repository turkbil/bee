<!-- Analytics Dashboard AI Translation Modal -->
<div class="modal fade" id="analyticsTranslationModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="ti ti-chart-dots-2 me-2"></i>
                    AI Translation Analytics Dashboard
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeAnalyticsModal()"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Top Stats Bar -->
                <div class="bg-primary text-white">
                    <div class="container-fluid py-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-1 fw-bold" id="totalProcessed">245</div>
                                    <div class="text-primary-fg-subtle">Total Processed</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-1 fw-bold" id="avgQuality">94.7%</div>
                                    <div class="text-primary-fg-subtle">Avg Quality</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-1 fw-bold" id="tokensUsed">12.8K</div>
                                    <div class="text-primary-fg-subtle">Tokens Used</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-1 fw-bold" id="avgTime">2.4s</div>
                                    <div class="text-primary-fg-subtle">Avg Time</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard -->
                <div class="container-fluid py-4">
                    <div class="row">
                        <!-- Translation Panel -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-edit me-2 text-primary"></i>
                                        Translation Workspace
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label">Source Language</label>
                                                <select class="form-select">
                                                    <option value="tr" selected>Türkçe</option>
                                                    <option value="en">English</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Target Language</label>
                                                <select class="form-select">
                                                    <option value="en" selected>English</option>
                                                    <option value="de">German</option>
                                                    <option value="fr">French</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Content to Translate</label>
                                        <textarea class="form-control" rows="8" id="analyticsSourceContent">Test içerik buraya gelecek...</textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="startAnalyticsTranslation()">
                                            <i class="ti ti-play me-1"></i>Start Translation
                                        </button>
                                        <button class="btn btn-outline-info" onclick="showAnalyticsHistory()">
                                            <i class="ti ti-history me-1"></i>History
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Real-time Metrics -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title">Real-time Performance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="progress mb-2">
                                                <div class="progress-bar bg-success" style="width: 0%" id="analyticsProgress"></div>
                                            </div>
                                            <small class="text-muted" id="progressStatus">Ready to process</small>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="badge bg-info" id="currentTokens">0 tokens</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Panel -->
                        <div class="col-md-6">
                            <!-- Live Charts -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Performance Analytics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <canvas id="qualityChart" width="200" height="200"></canvas>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>Quality Score</span>
                                                    <span class="badge bg-success">95.2%</span>
                                                </div>
                                                <div class="progress mt-1">
                                                    <div class="progress-bar bg-success" style="width: 95.2%"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>Speed</span>
                                                    <span class="badge bg-warning">87.3%</span>
                                                </div>
                                                <div class="progress mt-1">
                                                    <div class="progress-bar bg-warning" style="width: 87.3%"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>Efficiency</span>
                                                    <span class="badge bg-info">92.1%</span>
                                                </div>
                                                <div class="progress mt-1">
                                                    <div class="progress-bar bg-info" style="width: 92.1%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Translation Output -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="ti ti-check me-2 text-success"></i>
                                        Translation Result
                                        <div class="ms-auto">
                                            <span class="badge bg-success" id="qualityBadge" style="display: none;">Quality: 0%</span>
                                        </div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="analyticsOutput" class="border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                        <div class="text-muted text-center mt-5">
                                            <i class="ti ti-chart-line fs-1 mb-2"></i>
                                            <p>Start translation to see analytics and results</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-success" disabled id="analyticsApplyBtn">
                                            <i class="ti ti-check me-1"></i>Apply Translation
                                        </button>
                                        <button class="btn btn-outline-info" onclick="exportAnalytics()">
                                            <i class="ti ti-download me-1"></i>Export Analytics
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Analytics -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Translation History & Insights</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Source</th>
                                                    <th>Target</th>
                                                    <th>Tokens</th>
                                                    <th>Quality</th>
                                                    <th>Duration</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="analyticsHistory">
                                                <tr>
                                                    <td>14:23:45</td>
                                                    <td>TR</td>
                                                    <td>EN</td>
                                                    <td>245</td>
                                                    <td><span class="badge bg-success">96%</span></td>
                                                    <td>2.1s</td>
                                                    <td><i class="ti ti-check text-success"></i></td>
                                                </tr>
                                                <tr>
                                                    <td>14:20:12</td>
                                                    <td>TR</td>
                                                    <td>DE</td>
                                                    <td>189</td>
                                                    <td><span class="badge bg-warning">89%</span></td>
                                                    <td>1.8s</td>
                                                    <td><i class="ti ti-check text-success"></i></td>
                                                </tr>
                                            </tbody>
                                        </table>
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
.text-primary-fg-subtle {
    opacity: 0.8;
    font-size: 0.875rem;
}
#qualityChart {
    max-width: 150px;
    max-height: 150px;
}
</style>

<script>
function closeAnalyticsModal() {
    document.getElementById('analyticsTranslationModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function startAnalyticsTranslation() {
    const progressBar = document.getElementById('analyticsProgress');
    const progressStatus = document.getElementById('progressStatus');
    const currentTokens = document.getElementById('currentTokens');
    const analyticsOutput = document.getElementById('analyticsOutput');
    const applyBtn = document.getElementById('analyticsApplyBtn');
    const qualityBadge = document.getElementById('qualityBadge');
    
    progressBar.style.width = '0%';
    applyBtn.disabled = true;
    qualityBadge.style.display = 'none';
    
    const steps = [
        { progress: 15, text: 'Initializing analytics...', tokens: 45 },
        { progress: 35, text: 'Processing with AI model...', tokens: 120 },
        { progress: 55, text: 'Analyzing quality metrics...', tokens: 198 },
        { progress: 75, text: 'Generating insights...', tokens: 234 },
        { progress: 100, text: 'Analytics complete!', tokens: 267 }
    ];
    
    steps.forEach((step, index) => {
        setTimeout(() => {
            progressBar.style.width = step.progress + '%';
            progressStatus.textContent = step.text;
            currentTokens.textContent = step.tokens + ' tokens';
            
            if (step.progress === 100) {
                analyticsOutput.innerHTML = `
                    <div class="p-3">
                        <h6 class="text-success mb-3"><i class="ti ti-check me-2"></i>Translation Completed with Analytics</h6>
                        <p class="mb-3">Test content will come here...</p>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="bg-success bg-opacity-10 border border-success rounded p-2">
                                    <small class="text-success"><strong>Quality:</strong> 95.2%</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-info bg-opacity-10 border border-info rounded p-2">
                                    <small class="text-info"><strong>Speed:</strong> 2.3s</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-warning bg-opacity-10 border border-warning rounded p-2">
                                    <small class="text-warning"><strong>Cost:</strong> $0.012</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6>AI Insights:</h6>
                            <ul class="small text-muted">
                                <li>Translation maintains context accuracy</li>
                                <li>Technical terms properly handled</li>
                                <li>Tone consistency: 98%</li>
                            </ul>
                        </div>
                    </div>
                `;
                
                qualityBadge.textContent = 'Quality: 95.2%';
                qualityBadge.style.display = 'inline-block';
                applyBtn.disabled = false;
                
                // Add to history
                const now = new Date();
                const timeStr = now.toTimeString().substring(0, 8);
                const historyTable = document.getElementById('analyticsHistory');
                const newRow = historyTable.insertRow(0);
                newRow.innerHTML = `
                    <td>${timeStr}</td>
                    <td>TR</td>
                    <td>EN</td>
                    <td>267</td>
                    <td><span class="badge bg-success">95.2%</span></td>
                    <td>2.3s</td>
                    <td><i class="ti ti-check text-success"></i></td>
                `;
                
                applyBtn.onclick = function() {
                    if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                        tinymce.get('content').setContent('<p>Test content will come here...</p>');
                    }
                    closeAnalyticsModal();
                };
            }
        }, index * 1000);
    });
}

function showAnalyticsHistory() {
    alert('Analytics history would be displayed here with detailed metrics and insights.');
}

function exportAnalytics() {
    alert('Analytics data would be exported as PDF/CSV report.');
}

// Initialize quality chart (simple placeholder)
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('qualityChart');
    if (ctx) {
        const context = ctx.getContext('2d');
        context.beginPath();
        context.arc(75, 75, 50, 0, 2 * Math.PI);
        context.stroke();
        context.fillStyle = '#0080ff';
        context.fill();
        context.fillStyle = 'white';
        context.font = '16px Arial';
        context.textAlign = 'center';
        context.fillText('95.2%', 75, 80);
    }
});
</script>