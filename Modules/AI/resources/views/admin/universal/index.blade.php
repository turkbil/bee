@extends('admin.layout')

@section('title', 'Universal Input System Yönetimi')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">AI Modülü</div>
                <h2 class="page-title">Universal Input System Yönetimi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                        <i class="ti ti-plus"></i>
                        Yeni Template
                    </button>
                    <button type="button" class="btn btn-success" onclick="refreshSystemCache()">
                        <i class="ti ti-refresh"></i>
                        Cache Yenile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            
            <!-- System Overview Cards -->
            <div class="col-12">
                <div class="row row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Toplam Feature</div>
                                    <div class="ms-auto lh-1">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Son 24 saat</a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item active" href="#">Son 24 saat</a>
                                                <a class="dropdown-item" href="#">Son hafta</a>
                                                <a class="dropdown-item" href="#">Son ay</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="h1 mb-3" id="totalFeatures">74</div>
                                <div class="d-flex mb-2">
                                    <div class="flex-fill">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" style="width: 85%" role="progressbar">
                                                <span class="visually-hidden">85% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="text-green d-inline-flex align-items-center lh-1">
                                            85% <i class="ti ti-trending-up ms-1" style="font-size: 0.75rem"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-secondary">Aktif feature oranı</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Template Sayısı</div>
                                </div>
                                <div class="h1 mb-3" id="totalTemplates">--</div>
                                <div class="d-flex mb-2">
                                    <div class="flex-fill">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: 60%" role="progressbar">
                                                <span class="visually-hidden">60% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="text-green d-inline-flex align-items-center lh-1">
                                            +12% <i class="ti ti-trending-up ms-1" style="font-size: 0.75rem"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-secondary">Bu ay eklenen</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Günlük Kullanım</div>
                                </div>
                                <div class="h1 mb-3" id="dailyUsage">--</div>
                                <div class="d-flex mb-2">
                                    <div class="flex-fill">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-warning" style="width: 75%" role="progressbar">
                                                <span class="visually-hidden">75% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="text-green d-inline-flex align-items-center lh-1">
                                            +5% <i class="ti ti-trending-up ms-1" style="font-size: 0.75rem"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-secondary">Önceki güne göre</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Cache Hit Rate</div>
                                </div>
                                <div class="h1 mb-3" id="cacheHitRate">--</div>
                                <div class="d-flex mb-2">
                                    <div class="flex-fill">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" style="width: 90%" role="progressbar">
                                                <span class="visually-hidden">90% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="text-green d-inline-flex align-items-center lh-1">
                                            90% <i class="ti ti-trending-up ms-1" style="font-size: 0.75rem"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-secondary">Performans optimum</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Management Tab -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-features" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                    <i class="ti ti-sparkles me-2"></i>
                                    AI Features
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-templates" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-template me-2"></i>
                                    Templates
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-context" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-settings me-2"></i>
                                    Context Rules
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-analytics" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-chart-line me-2"></i>
                                    Analytics
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            
                            <!-- AI Features Tab -->
                            <div class="tab-pane active show" id="tabs-features" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Feature ara..." id="featureSearch">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="ti ti-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadFeatures('all')">Tümü</button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="loadFeatures('active')">Aktif</button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="loadFeatures('inactive')">Pasif</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table" id="featuresTable">
                                        <thead>
                                            <tr>
                                                <th>Feature Adı</th>
                                                <th>Kategori</th>
                                                <th>Modül Tipi</th>
                                                <th>Templates</th>
                                                <th>Kullanım</th>
                                                <th>Durum</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody id="featuresTableBody">
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Yükleniyor...</span>
                                                    </div>
                                                    <div class="mt-2">Feature listesi yükleniyor...</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Templates Tab -->
                            <div class="tab-pane" id="tabs-templates" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Template ara..." id="templateSearch">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="ti ti-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                                            <i class="ti ti-plus"></i>
                                            Yeni Template
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row" id="templatesContainer">
                                    <div class="col-12 text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Yükleniyor...</span>
                                        </div>
                                        <div class="mt-2">Template listesi yükleniyor...</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Context Rules Tab -->
                            <div class="tab-pane" id="tabs-context" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Kural ara..." id="contextSearch">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="ti ti-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addContextRule()">
                                            <i class="ti ti-plus"></i>
                                            Yeni Kural
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table" id="contextRulesTable">
                                        <thead>
                                            <tr>
                                                <th>Kural Adı</th>
                                                <th>Tip</th>
                                                <th>Koşullar</th>
                                                <th>Öncelik</th>
                                                <th>Durum</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody id="contextRulesTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Yükleniyor...</span>
                                                    </div>
                                                    <div class="mt-2">Context kuralları yükleniyor...</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Analytics Tab -->
                            <div class="tab-pane" id="tabs-analytics" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">En Popüler Features</h3>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="popularFeaturesChart" width="400" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Günlük Kullanım Trendi</h3>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="usageTrendChart" width="400" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Performans Metrikleri</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-vcenter">
                                                        <thead>
                                                            <tr>
                                                                <th>Metrik</th>
                                                                <th>Değer</th>
                                                                <th>Trend</th>
                                                                <th>Hedef</th>
                                                                <th>Durum</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="metricsTableBody">
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4">
                                                                    <div class="spinner-border text-primary" role="status">
                                                                        <span class="visually-hidden">Yükleniyor...</span>
                                                                    </div>
                                                                    <div class="mt-2">Metrikler yükleniyor...</div>
                                                                </td>
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
            
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal modal-blur fade" id="addTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Template Oluştur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTemplateForm">
                    <div class="mb-3">
                        <label class="form-label">Template Adı</label>
                        <input type="text" class="form-control" name="template_name" placeholder="Örn: Blog Yazısı Template">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Template Tipi</label>
                                <select class="form-select" name="template_type">
                                    <option value="feature">Feature Template</option>
                                    <option value="module">Modül Template</option>
                                    <option value="page">Sayfa Template</option>
                                    <option value="component">Component Template</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Modül Tipi</label>
                                <select class="form-select" name="module_type">
                                    <option value="">Seçiniz</option>
                                    <option value="blog">Blog</option>
                                    <option value="page">Sayfa</option>
                                    <option value="portfolio">Portfolio</option>
                                    <option value="announcement">Duyuru</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Yapısı (JSON)</label>
                        <textarea class="form-control font-monospace" name="template_structure" rows="8" 
                            placeholder='{"fields":[{"name":"title","type":"text","required":true}]}'></textarea>
                        <small class="form-hint">Template alan yapısını JSON formatında girin.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">İptal</a>
                <button type="button" class="btn btn-primary" onclick="saveTemplate()">Template Oluştur</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadFeatures('all');
    loadTemplates();
    loadContextRules();
    loadAnalytics();
    loadSystemStats();
});

// Feature Management
function loadFeatures(status = 'all') {
    const tbody = document.getElementById('featuresTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </td>
        </tr>
    `;
    
    // Simulate API call
    setTimeout(() => {
        const features = [
            {
                name: 'Blog Yazısı Oluştur',
                category: 'Content Creation',
                module_type: 'blog',
                templates: 3,
                usage: 156,
                status: 'active'
            },
            {
                name: 'SEO Meta Etiket',
                category: 'SEO Optimization',
                module_type: 'page',
                templates: 2,
                usage: 89,
                status: 'active'
            },
            {
                name: 'Çeviri Servisi',
                category: 'Translation',
                module_type: 'all',
                templates: 1,
                usage: 234,
                status: 'active'
            }
        ];
        
        tbody.innerHTML = features.map(feature => `
            <tr>
                <td>
                    <div class="d-flex py-1 align-items-center">
                        <span class="avatar me-2" style="background-image: url('/admin/assets/avatars/ai-feature.png')"></span>
                        <div class="flex-fill">
                            <div class="font-weight-medium">${feature.name}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-primary-lt">${feature.category}</span>
                </td>
                <td>
                    <span class="badge bg-secondary-lt">${feature.module_type}</span>
                </td>
                <td>
                    <span class="text-secondary">${feature.templates} template</span>
                </td>
                <td>
                    <span class="text-secondary">${feature.usage} kullanım</span>
                </td>
                <td>
                    <span class="badge bg-${feature.status === 'active' ? 'success' : 'warning'}-lt">
                        ${feature.status === 'active' ? 'Aktif' : 'Pasif'}
                    </span>
                </td>
                <td>
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                        <button class="btn btn-sm btn-outline-secondary">Test Et</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }, 500);
}

// Template Management
function loadTemplates() {
    const container = document.getElementById('templatesContainer');
    container.innerHTML = `
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        </div>
    `;
    
    setTimeout(() => {
        const templates = [
            {
                name: 'Blog Yazısı Template',
                type: 'feature',
                usage: 45,
                preview: '/admin/assets/previews/blog-template.jpg'
            },
            {
                name: 'SEO Meta Template',
                type: 'module', 
                usage: 23,
                preview: '/admin/assets/previews/seo-template.jpg'
            }
        ];
        
        container.innerHTML = templates.map(template => `
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top" 
                         style="background-image: url(${template.preview})"></div>
                    <div class="card-body">
                        <h3 class="card-title">${template.name}</h3>
                        <p class="text-secondary">
                            <span class="badge bg-secondary-lt">${template.type}</span>
                            ${template.usage} kez kullanıldı
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-secondary">Önizle</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }, 500);
}

// Context Rules Management
function loadContextRules() {
    const tbody = document.getElementById('contextRulesTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </td>
        </tr>
    `;
    
    setTimeout(() => {
        const rules = [
            {
                name: 'Blog Modül Kuralı',
                type: 'module',
                conditions: 'module=blog',
                priority: 100,
                status: 'active'
            },
            {
                name: 'Gece Modu Kuralı',
                type: 'time',
                conditions: 'hour>=22',
                priority: 80,
                status: 'active'
            }
        ];
        
        tbody.innerHTML = rules.map(rule => `
            <tr>
                <td>${rule.name}</td>
                <td>
                    <span class="badge bg-primary-lt">${rule.type}</span>
                </td>
                <td>
                    <code class="text-secondary">${rule.conditions}</code>
                </td>
                <td>
                    <span class="text-secondary">${rule.priority}</span>
                </td>
                <td>
                    <span class="badge bg-${rule.status === 'active' ? 'success' : 'warning'}-lt">
                        ${rule.status === 'active' ? 'Aktif' : 'Pasif'}
                    </span>
                </td>
                <td>
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                        <button class="btn btn-sm btn-outline-danger">Sil</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }, 500);
}

// Analytics
function loadAnalytics() {
    // Popular Features Chart
    setTimeout(() => {
        const ctx1 = document.getElementById('popularFeaturesChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Blog Yazısı', 'SEO Meta', 'Çeviri', 'E-posta', 'Sosyal Medya'],
                    datasets: [{
                        data: [35, 25, 20, 12, 8],
                        backgroundColor: [
                            '#206bc4',
                            '#79a6dc',
                            '#fab005',
                            '#fd7e14',
                            '#d63384'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }, 600);
    
    // Usage Trend Chart
    setTimeout(() => {
        const ctx2 = document.getElementById('usageTrendChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                    datasets: [{
                        label: 'Kullanım',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: '#206bc4',
                        backgroundColor: 'rgba(32, 107, 196, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }, 700);
    
    // Metrics Table
    setTimeout(() => {
        const tbody = document.getElementById('metricsTableBody');
        const metrics = [
            {
                name: 'Ortalama Yanıt Süresi',
                value: '245ms',
                trend: '+5%',
                target: '<500ms',
                status: 'success'
            },
            {
                name: 'Cache Hit Rate',
                value: '89.5%',
                trend: '+12%',
                target: '>60%',
                status: 'success'
            },
            {
                name: 'Hata Oranı',
                value: '0.2%',
                trend: '-3%',
                target: '<1%',
                status: 'success'
            }
        ];
        
        tbody.innerHTML = metrics.map(metric => `
            <tr>
                <td>${metric.name}</td>
                <td><strong>${metric.value}</strong></td>
                <td>
                    <span class="text-${metric.trend.includes('+') ? 'green' : 'red'}">
                        ${metric.trend}
                    </span>
                </td>
                <td>${metric.target}</td>
                <td>
                    <span class="badge bg-${metric.status}-lt">
                        ${metric.status === 'success' ? 'Başarılı' : 'Uyarı'}
                    </span>
                </td>
            </tr>
        `).join('');
    }, 800);
}

// System Stats
function loadSystemStats() {
    // Simulate API calls to load system statistics
    setTimeout(() => {
        document.getElementById('totalFeatures').textContent = '74';
        document.getElementById('totalTemplates').textContent = '12';
        document.getElementById('dailyUsage').textContent = '1,245';
        document.getElementById('cacheHitRate').textContent = '89.5%';
    }, 300);
}

// Helper Functions
function refreshSystemCache() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-refresh spinning"></i> Yenileniyor...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Show success notification
        showToast('Cache başarıyla yenilendi!', 'success');
    }, 2000);
}

function saveTemplate() {
    const form = document.getElementById('addTemplateForm');
    const formData = new FormData(form);
    
    // Basic validation
    if (!formData.get('template_name')) {
        showToast('Template adı zorunludur!', 'error');
        return;
    }
    
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader spinning"></i> Kaydediliyor...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Close modal and refresh
        bootstrap.Modal.getInstance(document.getElementById('addTemplateModal')).hide();
        form.reset();
        loadTemplates();
        
        showToast('Template başarıyla oluşturuldu!', 'success');
    }, 1500);
}

function addContextRule() {
    showToast('Context kuralı ekleme özelliği geliştirilme aşamasında...', 'info');
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// CSS for spinning animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spinning {
        animation: spin 1s linear infinite;
    }
`;
document.head.appendChild(style);
</script>
@endpush