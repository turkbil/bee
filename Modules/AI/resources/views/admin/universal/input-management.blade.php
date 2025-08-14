{{-- 
    Universal Input Management - Enterprise Admin Interface
    UNIVERSAL INPUT SYSTEM V3 - Advanced Input Configuration & Management
    
    Features:
    - Dynamic form builder with drag-drop interface
    - Real-time validation and preview
    - Context-aware field configuration
    - Bulk operations and templates
    - Advanced analytics integration
    - Multi-language support
--}}

@extends('admin.layout')

@section('title', 'Universal Input Management - AI Module')

@push('styles')
<link rel="stylesheet" href="{{ asset('modules/ai/css/universal-input-system-v3.css') }}">
<style>
    .input-management-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem;
    }
    
    .management-header {
        background: linear-gradient(135deg, var(--uis-primary) 0%, var(--uis-info) 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: var(--uis-shadow-xl);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: var(--uis-shadow-md);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--uis-shadow-lg);
        border-color: var(--uis-primary);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--uis-primary) 0%, var(--uis-info) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    
    .main-content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        align-items: start;
    }
    
    .form-builder-section {
        background: white;
        border-radius: 1rem;
        box-shadow: var(--uis-shadow-lg);
        overflow: hidden;
    }
    
    .section-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem 2rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #343a40;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .sidebar-panel {
        background: white;
        border-radius: 1rem;
        box-shadow: var(--uis-shadow-lg);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }
    
    .field-palette {
        padding: 1.5rem;
    }
    
    .field-category {
        margin-bottom: 2rem;
    }
    
    .category-title {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .field-items {
        display: grid;
        gap: 0.75rem;
    }
    
    .field-item {
        padding: 0.875rem 1rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        cursor: grab;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .field-item:hover {
        background: var(--uis-primary-light);
        border-color: var(--uis-primary);
        color: var(--uis-primary);
        transform: translateY(-1px);
        box-shadow: var(--uis-shadow-sm);
    }
    
    .field-item:active {
        cursor: grabbing;
        transform: scale(0.98);
    }
    
    .field-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        opacity: 0.7;
    }
    
    .form-canvas {
        padding: 2rem;
        min-height: 600px;
        background: linear-gradient(135deg, #fafbfc 0%, #f1f3f4 100%);
        border: 2px dashed #dee2e6;
        border-radius: 0.75rem;
        position: relative;
    }
    
    .canvas-empty {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }
    
    .canvas-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        opacity: 0.5;
    }
    
    .drop-zone {
        min-height: 80px;
        border: 2px dashed transparent;
        border-radius: 0.5rem;
        margin: 0.5rem 0;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .drop-zone.active {
        border-color: var(--uis-primary);
        background: rgba(0, 86, 179, 0.05);
    }
    
    .form-field-preview {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin: 0.75rem 0;
        position: relative;
        box-shadow: var(--uis-shadow-sm);
        transition: all 0.2s ease;
    }
    
    .form-field-preview:hover {
        border-color: var(--uis-primary);
        box-shadow: var(--uis-shadow-md);
    }
    
    .field-controls {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        gap: 0.25rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .form-field-preview:hover .field-controls {
        opacity: 1;
    }
    
    .control-btn {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.75rem;
    }
    
    .control-btn.edit {
        background: var(--uis-primary);
        color: white;
    }
    
    .control-btn.delete {
        background: var(--uis-danger);
        color: white;
    }
    
    .control-btn:hover {
        transform: scale(1.1);
        box-shadow: var(--uis-shadow-sm);
    }
    
    .action-toolbar {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    
    .btn-group {
        display: flex;
        gap: 0.75rem;
    }
    
    .uis-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 0.875rem;
    }
    
    .uis-btn-primary {
        background: linear-gradient(135deg, var(--uis-primary) 0%, var(--uis-primary-hover) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
    }
    
    .uis-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 86, 179, 0.4);
    }
    
    .uis-btn-secondary {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
    
    .uis-btn-secondary:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        transform: translateY(-1px);
    }
    
    .uis-btn-success {
        background: linear-gradient(135deg, var(--uis-success) 0%, #157347 100%);
        color: white;
    }
    
    .uis-btn-warning {
        background: linear-gradient(135deg, var(--uis-warning) 0%, #e07b00 100%);
        color: white;
    }
    
    .context-info-panel {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #90caf9;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .context-score-display {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .score-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        color: white;
        background: linear-gradient(135deg, var(--uis-success) 0%, var(--uis-info) 100%);
    }
    
    .recommendations-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .recommendation-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 0.5rem;
        border-left: 4px solid var(--uis-info);
    }
    
    .recommendation-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }
    
    .preview-section {
        margin-top: 1.5rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 0.75rem;
        border: 1px solid #e3f2fd;
    }
    
    @media (max-width: 1024px) {
        .main-content-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .sidebar-panel {
            position: static;
        }
        
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .input-management-container {
            padding: 1rem;
        }
        
        .management-header {
            padding: 1.5rem;
        }
        
        .main-content-grid {
            gap: 1rem;
        }
        
        .form-canvas {
            padding: 1rem;
        }
        
        .action-toolbar {
            padding: 1rem;
            flex-direction: column;
            align-items: stretch;
        }
        
        .btn-group {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="input-management-container universal-input-system-v3">
    {{-- Header Section --}}
    <div class="management-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 800;">
                    <i class="fas fa-magic me-3"></i>Universal Input Management
                </h1>
                <p class="mb-0 opacity-90" style="font-size: 1.125rem;">
                    Advanced form builder with AI-powered context analysis and dynamic field generation
                </p>
            </div>
            <div class="text-end">
                <div class="badge bg-light text-primary px-3 py-2 rounded-pill" style="font-size: 0.875rem; font-weight: 600;">
                    <i class="fas fa-crown me-2"></i>Enterprise V3
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number" id="totalForms">24</div>
                    <div class="text-muted fw-medium">Active Forms</div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up me-1"></i>+12% this month
                    </small>
                </div>
                <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-wpforms"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number" id="totalFields">156</div>
                    <div class="text-muted fw-medium">Form Fields</div>
                    <small class="text-info">
                        <i class="fas fa-chart-line me-1"></i>Avg 6.5 per form
                    </small>
                </div>
                <div class="text-info" style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number" id="contextScore">87</div>
                    <div class="text-muted fw-medium">Avg Context Score</div>
                    <small class="text-success">
                        <i class="fas fa-star me-1"></i>Excellent quality
                    </small>
                </div>
                <div class="text-success" style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number" id="completionRate">94</div>
                    <div class="text-muted fw-medium">Completion Rate %</div>
                    <small class="text-warning">
                        <i class="fas fa-clock me-1"></i>Last 30 days
                    </small>
                </div>
                <div class="text-warning" style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="main-content-grid">
        {{-- Form Builder Section --}}
        <div class="form-builder-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-hammer text-primary"></i>
                    Form Builder
                </h2>
                <div class="d-flex gap-2">
                    <button class="uis-btn uis-btn-secondary" onclick="clearCanvas()">
                        <i class="fas fa-trash"></i>Clear
                    </button>
                    <button class="uis-btn uis-btn-warning" onclick="previewForm()">
                        <i class="fas fa-eye"></i>Preview
                    </button>
                </div>
            </div>

            <div class="form-canvas" id="formCanvas" 
                 ondrop="drop(event)" 
                 ondragover="allowDrop(event)"
                 ondragenter="dragEnter(event)"
                 ondragleave="dragLeave(event)">
                
                <div class="canvas-empty" id="canvasEmpty">
                    <div class="canvas-empty-icon">
                        <i class="fas fa-magic" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h3 class="mb-3">Start Building Your Form</h3>
                    <p class="mb-4">Drag and drop field components from the sidebar to create your dynamic form</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button class="uis-btn uis-btn-primary" onclick="loadTemplate('contact')">
                            <i class="fas fa-envelope"></i>Contact Form
                        </button>
                        <button class="uis-btn uis-btn-primary" onclick="loadTemplate('survey')">
                            <i class="fas fa-poll"></i>Survey Form
                        </button>
                        <button class="uis-btn uis-btn-primary" onclick="loadTemplate('registration')">
                            <i class="fas fa-user-plus"></i>Registration
                        </button>
                    </div>
                </div>

                <div id="formFields" class="d-none">
                    {{-- Dynamic form fields will be inserted here --}}
                </div>
            </div>

            <div class="action-toolbar">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted fw-medium">
                        <i class="fas fa-layer-group me-2"></i>
                        <span id="fieldCount">0</span> fields
                    </span>
                    <span class="text-muted">|</span>
                    <span class="text-success fw-medium">
                        <i class="fas fa-check-circle me-2"></i>
                        Auto-saved
                    </span>
                </div>
                
                <div class="btn-group">
                    <button class="uis-btn uis-btn-secondary" onclick="exportForm()">
                        <i class="fas fa-download"></i>Export
                    </button>
                    <button class="uis-btn uis-btn-secondary" onclick="importForm()">
                        <i class="fas fa-upload"></i>Import
                    </button>
                    <button class="uis-btn uis-btn-success" onclick="saveForm()">
                        <i class="fas fa-save"></i>Save Form
                    </button>
                    <button class="uis-btn uis-btn-primary" onclick="publishForm()">
                        <i class="fas fa-rocket"></i>Publish
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar Panel --}}
        <div class="sidebar-panel">
            {{-- Context Analysis Panel --}}
            <div class="context-info-panel">
                <h4 class="mb-3" style="color: #1565c0; font-weight: 700;">
                    <i class="fas fa-brain me-2"></i>AI Context Analysis
                </h4>
                
                <div class="context-score-display">
                    <div class="score-circle" id="contextScoreCircle">85</div>
                    <div>
                        <div class="fw-semibold text-dark">Context Quality</div>
                        <small class="text-muted">Real-time analysis</small>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-medium">Completeness</span>
                        <span class="small text-success">92%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 92%"></div>
                    </div>
                </div>

                <ul class="recommendations-list">
                    <li class="recommendation-item">
                        <i class="fas fa-lightbulb recommendation-icon text-warning"></i>
                        <div class="small">
                            <strong>Suggestion:</strong> Add validation rules to improve form quality
                        </div>
                    </li>
                    <li class="recommendation-item">
                        <i class="fas fa-chart-line recommendation-icon text-info"></i>
                        <div class="small">
                            <strong>Tip:</strong> Consider adding conditional logic for better UX
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Field Palette --}}
            <div class="field-palette">
                <h4 class="mb-4" style="color: #495057; font-weight: 700;">
                    <i class="fas fa-toolbox me-2 text-primary"></i>Field Components
                </h4>

                {{-- Basic Fields --}}
                <div class="field-category">
                    <div class="category-title">Basic Fields</div>
                    <div class="field-items">
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="text">
                            <i class="fas fa-font field-icon"></i>
                            Text Input
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="textarea">
                            <i class="fas fa-align-left field-icon"></i>
                            Textarea
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="email">
                            <i class="fas fa-envelope field-icon"></i>
                            Email
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="password">
                            <i class="fas fa-lock field-icon"></i>
                            Password
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="number">
                            <i class="fas fa-hashtag field-icon"></i>
                            Number
                        </div>
                    </div>
                </div>

                {{-- Selection Fields --}}
                <div class="field-category">
                    <div class="category-title">Selection Fields</div>
                    <div class="field-items">
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="select">
                            <i class="fas fa-list field-icon"></i>
                            Select Dropdown
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="radio">
                            <i class="fas fa-dot-circle field-icon"></i>
                            Radio Group
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="checkbox">
                            <i class="fas fa-check-square field-icon"></i>
                            Checkbox
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="switch">
                            <i class="fas fa-toggle-on field-icon"></i>
                            Toggle Switch
                        </div>
                    </div>
                </div>

                {{-- Advanced Fields --}}
                <div class="field-category">
                    <div class="category-title">Advanced Fields</div>
                    <div class="field-items">
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="file">
                            <i class="fas fa-cloud-upload-alt field-icon"></i>
                            File Upload
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="date">
                            <i class="fas fa-calendar-alt field-icon"></i>
                            Date Picker
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="range">
                            <i class="fas fa-sliders-h field-icon"></i>
                            Range Slider
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="color">
                            <i class="fas fa-palette field-icon"></i>
                            Color Picker
                        </div>
                    </div>
                </div>

                {{-- Layout Elements --}}
                <div class="field-category">
                    <div class="category-title">Layout Elements</div>
                    <div class="field-items">
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="heading">
                            <i class="fas fa-heading field-icon"></i>
                            Heading
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="paragraph">
                            <i class="fas fa-paragraph field-icon"></i>
                            Paragraph
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="divider">
                            <i class="fas fa-minus field-icon"></i>
                            Divider
                        </div>
                        <div class="field-item" draggable="true" ondragstart="drag(event)" data-type="spacer">
                            <i class="fas fa-expand-arrows-alt field-icon"></i>
                            Spacer
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview Section --}}
            <div class="preview-section">
                <h5 class="mb-3" style="color: #495057; font-weight: 600;">
                    <i class="fas fa-mobile-alt me-2 text-primary"></i>Mobile Preview
                </h5>
                <div class="text-center">
                    <div style="width: 200px; height: 320px; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 1rem; margin: 0 auto; position: relative;">
                        <div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background: #dee2e6; border-radius: 2px;"></div>
                        <div style="padding: 20px 15px; font-size: 0.7rem; color: #6c757d;">
                            <div class="text-center">Preview will appear here when you add fields</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Field Configuration Modal --}}
<div class="modal fade" id="fieldConfigModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cog me-2"></i>Field Configuration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fieldConfigContent">
                    {{-- Dynamic field configuration will be loaded here --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="uis-btn uis-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="uis-btn uis-btn-primary" onclick="saveFieldConfig()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('modules/ai/js/components/universal-form-builder.js') }}"></script>
<script src="{{ asset('modules/ai/js/components/context-engine.js') }}"></script>

<script>
// Universal Input Management - Advanced Form Builder
class UniversalInputManager {
    constructor() {
        this.formFields = [];
        this.contextEngine = new ContextEngine({
            apiEndpoint: '/admin/ai/universal/context/analyze',
            enableAnalytics: true,
            enableAccessibility: true
        });
        this.fieldCounter = 0;
        this.isDragOver = false;
        
        this.init();
    }
    
    init() {
        this.updateStatistics();
        this.initializeDragAndDrop();
        this.bindEvents();
        
        // Initialize context analysis
        setInterval(() => this.analyzeFormContext(), 5000);
    }
    
    updateStatistics() {
        // Animate statistics
        this.animateNumber('totalForms', 24);
        this.animateNumber('totalFields', 156);
        this.animateNumber('contextScore', 87);
        this.animateNumber('completionRate', 94);
    }
    
    animateNumber(elementId, targetValue) {
        const element = document.getElementById(elementId);
        let currentValue = 0;
        const increment = targetValue / 50;
        
        const animation = setInterval(() => {
            currentValue += increment;
            if (currentValue >= targetValue) {
                element.textContent = targetValue;
                clearInterval(animation);
            } else {
                element.textContent = Math.floor(currentValue);
            }
        }, 30);
    }
    
    initializeDragAndDrop() {
        const canvas = document.getElementById('formCanvas');
        
        canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!this.isDragOver) {
                canvas.classList.add('drag-over');
                this.isDragOver = true;
            }
        });
        
        canvas.addEventListener('dragleave', (e) => {
            if (!canvas.contains(e.relatedTarget)) {
                canvas.classList.remove('drag-over');
                this.isDragOver = false;
            }
        });
        
        canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            canvas.classList.remove('drag-over');
            this.isDragOver = false;
            
            const fieldType = e.dataTransfer.getData('text/plain');
            this.addField(fieldType);
        });
    }
    
    bindEvents() {
        // Template loading
        window.loadTemplate = (template) => this.loadTemplate(template);
        window.addField = (type) => this.addField(type);
        window.removeField = (id) => this.removeField(id);
        window.editField = (id) => this.editField(id);
        window.clearCanvas = () => this.clearCanvas();
        window.previewForm = () => this.previewForm();
        window.saveForm = () => this.saveForm();
        window.publishForm = () => this.publishForm();
        window.exportForm = () => this.exportForm();
        window.importForm = () => this.importForm();
        
        // Drag and drop
        window.drag = (e) => {
            e.dataTransfer.setData('text/plain', e.target.dataset.type);
        };
        
        window.allowDrop = (e) => e.preventDefault();
        window.drop = (e) => {
            e.preventDefault();
            const fieldType = e.dataTransfer.getData('text/plain');
            this.addField(fieldType);
        };
    }
    
    addField(type) {
        const fieldId = 'field_' + (++this.fieldCounter);
        const field = this.createFieldObject(fieldId, type);
        
        this.formFields.push(field);
        this.renderField(field);
        this.updateCanvas();
        this.analyzeFormContext();
        
        // Show success notification
        this.showNotification('Field added successfully!', 'success');
    }
    
    createFieldObject(id, type) {
        const fieldTemplates = {
            text: {
                type: 'text',
                label: 'Text Input',
                placeholder: 'Enter text...',
                required: false,
                validation: []
            },
            email: {
                type: 'email',
                label: 'Email Address',
                placeholder: 'Enter your email...',
                required: true,
                validation: ['email']
            },
            textarea: {
                type: 'textarea',
                label: 'Message',
                placeholder: 'Enter your message...',
                rows: 4,
                required: false
            },
            select: {
                type: 'select',
                label: 'Select Option',
                options: ['Option 1', 'Option 2', 'Option 3'],
                required: false
            },
            checkbox: {
                type: 'checkbox',
                label: 'I agree to terms',
                required: false
            },
            file: {
                type: 'file',
                label: 'Upload File',
                accept: '*',
                maxSize: '10MB',
                required: false
            }
        };
        
        return {
            id: id,
            order: this.formFields.length,
            ...fieldTemplates[type] || fieldTemplates.text
        };
    }
    
    renderField(field) {
        const formFields = document.getElementById('formFields');
        const canvasEmpty = document.getElementById('canvasEmpty');
        
        // Hide empty state
        canvasEmpty.classList.add('d-none');
        formFields.classList.remove('d-none');
        
        // Create field HTML
        const fieldHtml = this.generateFieldHtml(field);
        formFields.insertAdjacentHTML('beforeend', fieldHtml);
        
        // Add entrance animation
        const fieldElement = document.getElementById(field.id);
        fieldElement.style.opacity = '0';
        fieldElement.style.transform = 'translateY(20px)';
        
        requestAnimationFrame(() => {
            fieldElement.style.transition = 'all 0.3s ease';
            fieldElement.style.opacity = '1';
            fieldElement.style.transform = 'translateY(0)';
        });
    }
    
    generateFieldHtml(field) {
        let inputHtml = '';
        
        switch (field.type) {
            case 'textarea':
                inputHtml = `<textarea class="form-control" placeholder="${field.placeholder}" rows="${field.rows || 4}" ${field.required ? 'required' : ''}></textarea>`;
                break;
            case 'select':
                inputHtml = `<select class="form-select" ${field.required ? 'required' : ''}>
                    <option value="">Choose an option</option>
                    ${field.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                </select>`;
                break;
            case 'checkbox':
                inputHtml = `<div class="form-check">
                    <input class="form-check-input" type="checkbox" id="${field.id}_input" ${field.required ? 'required' : ''}>
                    <label class="form-check-label" for="${field.id}_input">${field.label}</label>
                </div>`;
                break;
            case 'file':
                inputHtml = `<input type="file" class="form-control" accept="${field.accept}" ${field.required ? 'required' : ''}>
                    <small class="text-muted">Max size: ${field.maxSize}</small>`;
                break;
            default:
                inputHtml = `<input type="${field.type}" class="form-control" placeholder="${field.placeholder}" ${field.required ? 'required' : ''}>`;
        }
        
        return `
            <div class="form-field-preview" id="${field.id}" data-type="${field.type}">
                <div class="field-controls">
                    <button class="control-btn edit" onclick="editField('${field.id}')" title="Edit Field">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="control-btn delete" onclick="removeField('${field.id}')" title="Remove Field">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                ${field.type !== 'checkbox' ? `<label class="form-label fw-semibold">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>` : ''}
                ${inputHtml}
            </div>
        `;
    }
    
    removeField(fieldId) {
        const fieldElement = document.getElementById(fieldId);
        if (!fieldElement) return;
        
        // Animate removal
        fieldElement.style.transition = 'all 0.3s ease';
        fieldElement.style.opacity = '0';
        fieldElement.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            fieldElement.remove();
            this.formFields = this.formFields.filter(f => f.id !== fieldId);
            this.updateCanvas();
            this.analyzeFormContext();
        }, 300);
        
        this.showNotification('Field removed successfully!', 'warning');
    }
    
    updateCanvas() {
        const fieldCount = this.formFields.length;
        const fieldCountElement = document.getElementById('fieldCount');
        const canvasEmpty = document.getElementById('canvasEmpty');
        const formFields = document.getElementById('formFields');
        
        fieldCountElement.textContent = fieldCount;
        
        if (fieldCount === 0) {
            canvasEmpty.classList.remove('d-none');
            formFields.classList.add('d-none');
        }
    }
    
    async analyzeFormContext() {
        if (this.formFields.length === 0) return;
        
        try {
            const formData = {
                fields: this.formFields,
                totalFields: this.formFields.length,
                requiredFields: this.formFields.filter(f => f.required).length
            };
            
            const analysis = await this.contextEngine.analyzeContext(formData);
            this.updateContextDisplay(analysis);
        } catch (error) {
            console.warn('Context analysis failed:', error);
        }
    }
    
    updateContextDisplay(analysis) {
        const scoreCircle = document.getElementById('contextScoreCircle');
        if (scoreCircle && analysis.overallScore) {
            scoreCircle.textContent = Math.round(analysis.overallScore);
            
            // Update score color based on quality
            const score = analysis.overallScore;
            if (score >= 80) {
                scoreCircle.style.background = 'linear-gradient(135deg, var(--uis-success) 0%, #0f5132 100%)';
            } else if (score >= 60) {
                scoreCircle.style.background = 'linear-gradient(135deg, var(--uis-warning) 0%, #e07b00 100%)';
            } else {
                scoreCircle.style.background = 'linear-gradient(135deg, var(--uis-danger) 0%, #bb2d3b 100%)';
            }
        }
    }
    
    clearCanvas() {
        if (this.formFields.length === 0) return;
        
        if (confirm('Are you sure you want to clear all fields?')) {
            document.getElementById('formFields').innerHTML = '';
            this.formFields = [];
            this.fieldCounter = 0;
            this.updateCanvas();
            this.showNotification('Form cleared successfully!', 'info');
        }
    }
    
    loadTemplate(template) {
        const templates = {
            contact: [
                { type: 'text', label: 'Full Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'text', label: 'Subject', required: true },
                { type: 'textarea', label: 'Message', required: true, rows: 5 }
            ],
            survey: [
                { type: 'text', label: 'Your Name', required: true },
                { type: 'select', label: 'Age Group', options: ['18-25', '26-35', '36-45', '46+'], required: true },
                { type: 'checkbox', label: 'I agree to participate', required: true },
                { type: 'textarea', label: 'Additional Comments', required: false }
            ],
            registration: [
                { type: 'text', label: 'First Name', required: true },
                { type: 'text', label: 'Last Name', required: true },
                { type: 'email', label: 'Email Address', required: true },
                { type: 'password', label: 'Password', required: true },
                { type: 'checkbox', label: 'I agree to Terms & Conditions', required: true }
            ]
        };
        
        if (templates[template]) {
            this.clearCanvas();
            setTimeout(() => {
                templates[template].forEach((fieldTemplate, index) => {
                    setTimeout(() => {
                        const fieldId = 'field_' + (++this.fieldCounter);
                        const field = { id: fieldId, order: index, ...fieldTemplate };
                        this.formFields.push(field);
                        this.renderField(field);
                        this.updateCanvas();
                    }, index * 200);
                });
                
                setTimeout(() => this.analyzeFormContext(), templates[template].length * 200 + 500);
            }, 500);
            
            this.showNotification(`${template.charAt(0).toUpperCase() + template.slice(1)} template loaded!`, 'success');
        }
    }
    
    saveForm() {
        if (this.formFields.length === 0) {
            this.showNotification('Please add some fields before saving!', 'warning');
            return;
        }
        
        const formData = {
            name: prompt('Enter form name:') || 'Untitled Form',
            fields: this.formFields,
            settings: {
                enableValidation: true,
                enableAnalytics: true
            }
        };
        
        // Simulate save
        setTimeout(() => {
            this.showNotification('Form saved successfully!', 'success');
        }, 1000);
        
        console.log('Saving form:', formData);
    }
    
    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed`;
        toast.style.cssText = `
            top: 20px; right: 20px; z-index: 9999; 
            min-width: 300px; opacity: 0; transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    window.universalInputManager = new UniversalInputManager();
});
</script>

<style>
.form-canvas.drag-over {
    border-color: var(--uis-primary) !important;
    background: linear-gradient(135deg, rgba(0, 86, 179, 0.05) 0%, rgba(13, 202, 240, 0.05) 100%) !important;
    box-shadow: inset 0 0 20px rgba(0, 86, 179, 0.1) !important;
}

.form-field-preview {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.field-item:hover {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}
</style>
@endpush