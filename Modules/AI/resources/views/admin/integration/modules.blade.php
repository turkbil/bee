@extends('admin.layout')

@section('title', 'Modül Entegrasyon Ayarları')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">AI Modülü</div>
                <h2 class="page-title">Modül Entegrasyon Ayarları</h2>
                <p class="text-secondary mt-1">Her modül için AI özelliklerini yapılandırın ve entegrasyon ayarlarını yönetin.</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-success" onclick="saveAllChanges()">
                        <i class="ti ti-device-floppy"></i>
                        Tüm Değişiklikleri Kaydet
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIntegrationModal">
                        <i class="ti ti-plus"></i>
                        Yeni Entegrasyon
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            
            <!-- Module Integration Cards -->
            <div class="col-12">
                <div class="row row-cards" id="moduleCardsContainer">
                    
                    <!-- Page Module Integration -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-status-start bg-primary"></div>
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="ti ti-file-text me-2 text-primary"></i>
                                    <strong>Page Modülü</strong>
                                </div>
                                <div class="card-actions">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="pageModuleEnabled" checked>
                                        <label class="form-check-label" for="pageModuleEnabled">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Entegrasyon Tipi</label>
                                    <select class="form-select form-select-sm" data-module="page" data-field="integration_type">
                                        <option value="modal" selected>Modal Pencere</option>
                                        <option value="inline">Satır İçi</option>
                                        <option value="button">Buton</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Hedef Alanlar</label>
                                    <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="page_fields" value="title" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Başlık (Title)</strong>
                                                    <div class="text-secondary">Sayfa başlığı için AI desteği</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="page_fields" value="body" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>İçerik (Body)</strong>
                                                    <div class="text-secondary">Ana içerik için AI desteği</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="page_fields" value="meta_description" class="form-selectgroup-input">
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Meta Açıklama</strong>
                                                    <div class="text-secondary">SEO meta için AI desteği</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kullanılabilir Özellikler</label>
                                    <span class="text-secondary">12 özellik</span>
                                    <div class="progress progress-sm mt-1">
                                        <div class="progress-bar bg-primary" style="width: 75%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <button class="btn btn-sm btn-outline-primary" onclick="configureModule('page')">
                                            <i class="ti ti-settings"></i>
                                            Yapılandır
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-outline-success" onclick="testModule('page')">
                                            <i class="ti ti-play"></i>
                                            Test Et
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio Module Integration -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-status-start bg-success"></div>
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="ti ti-briefcase me-2 text-success"></i>
                                    <strong>Portfolio Modülü</strong>
                                </div>
                                <div class="card-actions">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="portfolioModuleEnabled" checked>
                                        <label class="form-check-label" for="portfolioModuleEnabled">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Entegrasyon Tipi</label>
                                    <select class="form-select form-select-sm" data-module="portfolio" data-field="integration_type">
                                        <option value="modal">Modal Pencere</option>
                                        <option value="inline" selected>Satır İçi</option>
                                        <option value="button">Buton</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Hedef Alanlar</label>
                                    <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="portfolio_fields" value="title" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Proje Adı</strong>
                                                    <div class="text-secondary">Portfolio başlığı için AI</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="portfolio_fields" value="description" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Proje Açıklaması</strong>
                                                    <div class="text-secondary">Detaylı açıklama için AI</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="portfolio_fields" value="technologies" class="form-selectgroup-input">
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Teknolojiler</strong>
                                                    <div class="text-secondary">Kullanılan teknolojiler</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kullanılabilir Özellikler</label>
                                    <span class="text-secondary">8 özellik</span>
                                    <div class="progress progress-sm mt-1">
                                        <div class="progress-bar bg-success" style="width: 60%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <button class="btn btn-sm btn-outline-primary" onclick="configureModule('portfolio')">
                                            <i class="ti ti-settings"></i>
                                            Yapılandır
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-outline-success" onclick="testModule('portfolio')">
                                            <i class="ti ti-play"></i>
                                            Test Et
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Module Integration -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-status-start bg-warning"></div>
                            <div class="card-header">
                                <div class="card-title d-flex align-items-center">
                                    <i class="ti ti-speakerphone me-2 text-warning"></i>
                                    <strong>Announcement Modülü</strong>
                                </div>
                                <div class="card-actions">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="announcementModuleEnabled">
                                        <label class="form-check-label" for="announcementModuleEnabled">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Entegrasyon Tipi</label>
                                    <select class="form-select form-select-sm" data-module="announcement" data-field="integration_type" disabled>
                                        <option value="modal" selected>Modal Pencere</option>
                                        <option value="inline">Satır İçi</option>
                                        <option value="button">Buton</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3 opacity-50">
                                    <label class="form-label">Hedef Alanlar</label>
                                    <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="announcement_fields" value="title" class="form-selectgroup-input" disabled>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Duyuru Başlığı</strong>
                                                    <div class="text-secondary">Başlık için AI desteği</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" name="announcement_fields" value="content" class="form-selectgroup-input" disabled>
                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div>
                                                    <strong>Duyuru İçeriği</strong>
                                                    <div class="text-secondary">Ana içerik için AI</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kullanılabilir Özellikler</label>
                                    <span class="text-secondary">6 özellik</span>
                                    <div class="progress progress-sm mt-1">
                                        <div class="progress-bar bg-warning" style="width: 30%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <button class="btn btn-sm btn-outline-primary" onclick="configureModule('announcement')" disabled>
                                            <i class="ti ti-settings"></i>
                                            Yapılandır
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-warning-lt">Pasif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <!-- Advanced Configuration Panel -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-global-settings" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                    <i class="ti ti-world me-2"></i>
                                    Global Ayarlar
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-permissions" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-lock me-2"></i>
                                    İzinler
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-performance" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-dashboard me-2"></i>
                                    Performans
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-logs" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="ti ti-file-text me-2"></i>
                                    Loglar
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            
                            <!-- Global Settings Tab -->
                            <div class="tab-pane active show" id="tabs-global-settings" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default AI Provider</label>
                                            <select class="form-select" name="default_ai_provider">
                                                <option value="openai" selected>OpenAI GPT-4</option>
                                                <option value="anthropic">Anthropic Claude</option>
                                                <option value="google">Google Gemini</option>
                                                <option value="local">Local Model</option>
                                            </select>
                                            <small class="form-hint">Tüm modüller için varsayılan AI sağlayıcısı</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Cache Duration (dakika)</label>
                                            <input type="number" class="form-control" name="cache_duration" value="60" min="5" max="1440">
                                            <small class="form-hint">AI yanıtları ne kadar süre cache'de tutulsun</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Max Tokens per Request</label>
                                            <input type="number" class="form-control" name="max_tokens" value="2000" min="100" max="8000">
                                            <small class="form-hint">Her AI isteği için maksimum token sayısı</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Retry Policy</label>
                                            <select class="form-select" name="retry_policy">
                                                <option value="aggressive">Aggressive (3 retry)</option>
                                                <option value="normal" selected>Normal (2 retry)</option>
                                                <option value="conservative">Conservative (1 retry)</option>
                                                <option value="none">None (0 retry)</option>
                                            </select>
                                            <small class="form-hint">AI isteği başarısız olduğunda tekrar deneme politikası</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Rate Limiting</label>
                                            <input type="number" class="form-control" name="rate_limit" value="100" min="10" max="1000">
                                            <small class="form-hint">Dakika başına maksimum istek sayısı</small>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableAnalytics" checked>
                                                <label class="form-check-label" for="enableAnalytics">
                                                    Analytics Toplama
                                                </label>
                                            </div>
                                            <small class="form-hint">AI kullanım istatistiklerini topla ve analiz et</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permissions Tab -->
                            <div class="tab-pane" id="tabs-permissions" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Rol Bazlı İzinler</h4>
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <strong>Super Admin</strong>
                                                        <div class="text-secondary">Tüm AI özelliklerine erişim</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" checked disabled>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <strong>Admin</strong>
                                                        <div class="text-secondary">Temel AI özelliklerine erişim</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" checked>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <strong>Editor</strong>
                                                        <div class="text-secondary">İçerik oluşturma AI'ları</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <strong>Author</strong>
                                                        <div class="text-secondary">Kendi içerikleri için AI</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h4>Modül İzinleri</h4>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Modül</th>
                                                        <th>Okuma</th>
                                                        <th>Yazma</th>
                                                        <th>Yapılandırma</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Page</td>
                                                        <td><input type="checkbox" class="form-check-input" checked></td>
                                                        <td><input type="checkbox" class="form-check-input" checked></td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Portfolio</td>
                                                        <td><input type="checkbox" class="form-check-input" checked></td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Announcement</td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                        <td><input type="checkbox" class="form-check-input"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Performance Tab -->
                            <div class="tab-pane" id="tabs-performance" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h4>Performans Metrikleri</h4>
                                        <div class="row">
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-sm">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <span class="bg-primary text-white avatar">
                                                                    <i class="ti ti-clock"></i>
                                                                </span>
                                                            </div>
                                                            <div class="col">
                                                                <div class="font-weight-medium">Ortalama Yanıt Süresi</div>
                                                                <div class="text-secondary">245ms</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-sm">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <span class="bg-success text-white avatar">
                                                                    <i class="ti ti-database"></i>
                                                                </span>
                                                            </div>
                                                            <div class="col">
                                                                <div class="font-weight-medium">Cache Hit Rate</div>
                                                                <div class="text-secondary">89.5%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-sm">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <span class="bg-warning text-white avatar">
                                                                    <i class="ti ti-alert-triangle"></i>
                                                                </span>
                                                            </div>
                                                            <div class="col">
                                                                <div class="font-weight-medium">Hata Oranı</div>
                                                                <div class="text-secondary">0.2%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-sm">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <span class="bg-info text-white avatar">
                                                                    <i class="ti ti-activity"></i>
                                                                </span>
                                                            </div>
                                                            <div class="col">
                                                                <div class="font-weight-medium">Aktif Bağlantı</div>
                                                                <div class="text-secondary">24</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h3 class="card-title">Son 24 Saat İstek Grafiği</h3>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="requestChart" width="600" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <h4>Optimizasyon Ayarları</h4>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableCompression" checked>
                                                <label class="form-check-label" for="enableCompression">
                                                    Response Compression
                                                </label>
                                            </div>
                                            <small class="form-hint">AI yanıtlarını sıkıştırarak bant genişliği tasarrufu</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableStreaming">
                                                <label class="form-check-label" for="enableStreaming">
                                                    Stream Responses
                                                </label>
                                            </div>
                                            <small class="form-hint">AI yanıtlarını parça parça gönder</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enablePreloading" checked>
                                                <label class="form-check-label" for="enablePreloading">
                                                    Preload Common Prompts
                                                </label>
                                            </div>
                                            <small class="form-hint">Sık kullanılan prompt'ları önceden yükle</small>
                                        </div>
                                        
                                        <h5 class="mt-4">Cache Ayarları</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Memory Cache Size (MB)</label>
                                            <input type="number" class="form-control" value="256" min="64" max="1024">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Disk Cache Size (MB)</label>
                                            <input type="number" class="form-control" value="512" min="128" max="2048">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <button class="btn btn-outline-warning w-100" onclick="clearAllCaches()">
                                                <i class="ti ti-trash"></i>
                                                Tüm Cache'leri Temizle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logs Tab -->
                            <div class="tab-pane" id="tabs-logs" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Log ara..." id="logSearch">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="ti ti-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadLogs('all')">Tümü</button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="loadLogs('info')">Info</button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="loadLogs('warning')">Warning</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="loadLogs('error')">Error</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Zaman</th>
                                                    <th>Level</th>
                                                    <th>Modül</th>
                                                    <th>Mesaj</th>
                                                    <th>İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody id="logsTableBody">
                                                <tr>
                                                    <td>2025-01-15 10:30:15</td>
                                                    <td><span class="badge bg-success-lt">INFO</span></td>
                                                    <td>Page</td>
                                                    <td>AI content generation successful</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Detay</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2025-01-15 10:25:42</td>
                                                    <td><span class="badge bg-warning-lt">WARNING</span></td>
                                                    <td>Portfolio</td>
                                                    <td>Rate limit approaching (85/100)</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Detay</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2025-01-15 10:20:18</td>
                                                    <td><span class="badge bg-danger-lt">ERROR</span></td>
                                                    <td>Announcement</td>
                                                    <td>AI service timeout after 30s</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Detay</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2025-01-15 10:15:33</td>
                                                    <td><span class="badge bg-success-lt">INFO</span></td>
                                                    <td>Page</td>
                                                    <td>Cache hit: seo_meta_generation</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Detay</button>
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

<!-- Add Integration Modal -->
<div class="modal modal-blur fade" id="addIntegrationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Modül Entegrasyonu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addIntegrationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Modül Adı</label>
                                <input type="text" class="form-control" name="module_name" placeholder="Örn: Blog">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Entegrasyon Tipi</label>
                                <select class="form-select" name="integration_type">
                                    <option value="modal">Modal Pencere</option>
                                    <option value="inline">Satır İçi</option>
                                    <option value="button">Buton</option>
                                    <option value="bulk">Toplu İşlem</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hedef Alan</label>
                        <input type="text" class="form-control" name="target_field" placeholder="Örn: content, title, description">
                        <small class="form-hint">AI desteği verilecek alan adı</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hedef İşlem</label>
                        <select class="form-select" name="target_action">
                            <option value="generate">İçerik Oluştur</option>
                            <option value="optimize">Optimize Et</option>
                            <option value="translate">Çevir</option>
                            <option value="analyze">Analiz Et</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Buton Konfigürasyonu (JSON)</label>
                        <textarea class="form-control font-monospace" name="button_config" rows="4" 
                            placeholder='{"text":"AI İle Oluştur","icon":"ti-sparkles","color":"primary"}'></textarea>
                        <small class="form-hint">Buton görünümü ve davranışı ayarları</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kullanılabilir Özellikler</label>
                        <select class="form-select" name="features_available" multiple>
                            <option value="1">Blog Yazısı Oluştur</option>
                            <option value="2">SEO Meta Etiket</option>
                            <option value="3">İçerik Çeviri</option>
                            <option value="4">Sosyal Medya Paylaşımı</option>
                        </select>
                        <small class="form-hint">Bu modülde kullanılabilecek AI özelliklerini seçin</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">İptal</a>
                <button type="button" class="btn btn-primary" onclick="saveIntegration()">Entegrasyon Oluştur</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load request chart
    loadRequestChart();
    
    // Module enable/disable handlers
    document.querySelectorAll('[id$="ModuleEnabled"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const moduleName = this.id.replace('ModuleEnabled', '').toLowerCase();
            toggleModule(moduleName, this.checked);
        });
    });
});

function loadRequestChart() {
    const ctx = document.getElementById('requestChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    label: 'AI Requests',
                    data: [12, 8, 45, 78, 65, 43, 23],
                    borderColor: '#206bc4',
                    backgroundColor: 'rgba(32, 107, 196, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

function toggleModule(moduleName, enabled) {
    const card = document.querySelector(`[data-module="${moduleName}"]`)?.closest('.card');
    if (card) {
        const elements = card.querySelectorAll('select, input[type="checkbox"]:not([id$="ModuleEnabled"])');
        elements.forEach(el => {
            el.disabled = !enabled;
        });
        
        card.style.opacity = enabled ? '1' : '0.6';
        
        // Show status
        showToast(`${moduleName.charAt(0).toUpperCase() + moduleName.slice(1)} modülü ${enabled ? 'etkinleştirildi' : 'pasifleştirildi'}`, 
                  enabled ? 'success' : 'warning');
    }
}

function configureModule(moduleName) {
    showToast(`${moduleName.charAt(0).toUpperCase() + moduleName.slice(1)} modülü yapılandırma paneli açılıyor...`, 'info');
    
    // Here you would typically open a detailed configuration modal
    // For now, just show a placeholder message
    setTimeout(() => {
        showToast('Yapılandırma paneli geliştirme aşamasında...', 'info');
    }, 1000);
}

function testModule(moduleName) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader spinning"></i> Test ediliyor...';
    btn.disabled = true;
    
    // Simulate module test
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        const success = Math.random() > 0.3; // 70% success rate
        showToast(
            `${moduleName} modülü testi ${success ? 'başarılı' : 'başarısız'}!`, 
            success ? 'success' : 'error'
        );
    }, 2000);
}

function saveAllChanges() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader spinning"></i> Kaydediliyor...';
    btn.disabled = true;
    
    // Collect all form data
    const formData = {};
    
    // Global settings
    document.querySelectorAll('[name^="default_"], [name^="cache_"], [name^="max_"], [name^="retry_"], [name^="rate_"]').forEach(el => {
        formData[el.name] = el.type === 'checkbox' ? el.checked : el.value;
    });
    
    // Module settings
    document.querySelectorAll('[data-module]').forEach(el => {
        const module = el.dataset.module;
        const field = el.dataset.field;
        if (module && field) {
            if (!formData.modules) formData.modules = {};
            if (!formData.modules[module]) formData.modules[module] = {};
            formData.modules[module][field] = el.type === 'checkbox' ? el.checked : el.value;
        }
    });
    
    console.log('Saving configuration:', formData);
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Tüm değişiklikler başarıyla kaydedildi!', 'success');
    }, 2000);
}

function saveIntegration() {
    const form = document.getElementById('addIntegrationForm');
    const formData = new FormData(form);
    
    // Basic validation
    if (!formData.get('module_name') || !formData.get('target_field')) {
        showToast('Modül adı ve hedef alan zorunludur!', 'error');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader spinning"></i> Oluşturuluyor...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Close modal and show success
        bootstrap.Modal.getInstance(document.getElementById('addIntegrationModal')).hide();
        form.reset();
        
        showToast('Yeni entegrasyon başarıyla oluşturuldu!', 'success');
        
        // Reload module cards (in a real app, you'd add the new card to the DOM)
        setTimeout(() => {
            location.reload();
        }, 1500);
    }, 2000);
}

function clearAllCaches() {
    if (!confirm('Tüm cache\'ler silinecek. Bu işlem geri alınamaz. Devam etmek istiyor musunuz?')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader spinning"></i> Temizleniyor...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Tüm cache\'ler başarıyla temizlendi!', 'success');
    }, 3000);
}

function loadLogs(level) {
    const tbody = document.getElementById('logsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <div class="mt-2">${level} logları yükleniyor...</div>
            </td>
        </tr>
    `;
    
    setTimeout(() => {
        // Simulate loading filtered logs
        const logs = [
            {
                time: '2025-01-15 10:30:15',
                level: 'INFO',
                module: 'Page',
                message: `AI content generation successful (${level} filter)`,
                levelClass: 'success'
            }
        ];
        
        tbody.innerHTML = logs.map(log => `
            <tr>
                <td>${log.time}</td>
                <td><span class="badge bg-${log.levelClass}-lt">${log.level}</span></td>
                <td>${log.module}</td>
                <td>${log.message}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary">Detay</button>
                </td>
            </tr>
        `).join('');
    }, 500);
}

function showToast(message, type = 'info') {
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