@extends('admin.layout')

@section('content')
<div class="page">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h1 class="page-title">🚀 AI Modal Tasarım Alternatifleri</h1>
                        <div class="text-muted">Hangi tasarımı beğenirseniz uygularız</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <div class="row g-4">
                    <!-- Design 1: Minimal & Clean -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">🎨 Tasarım 1: Minimal & Clean</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Sade, temiz ve işlevsel tasarım</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal1">
                                    Tasarım 1'i Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Design 2: Modern Card Style -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">💎 Tasarım 2: Modern Card Style</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Kartlı yapı ve gradient renkler</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal2">
                                    Tasarım 2'yi Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Design 3: Professional Dashboard -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">🏢 Tasarım 3: Professional Dashboard</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Kurumsal görünüm ve detaylı alanlar</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal3">
                                    Tasarım 3'ü Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Design 4: Creative & Colorful -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">🌈 Tasarım 4: Creative & Colorful</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Renkli, yaratıcı ve eğlenceli</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal4">
                                    Tasarım 4'ü Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Design 5: Advanced Step-by-Step -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">⚡ Tasarım 5: Advanced Step-by-Step</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Adım adım süreç ve ilerleme takibi</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal5">
                                    Tasarım 5'i Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- NEW DESIGN: Minimal + Accordion Help -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-2 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title">🎯 YENİ: Minimal + Yardım</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Sade açılış + Accordion yardım</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNew">
                                    Yeni Tasarımı Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- All Designs Comparison -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-dashed border-2 border-success">
                            <div class="card-header bg-success-lt">
                                <h3 class="card-title text-success">✨ Tüm Tasarımları Karşılaştır</h3>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted mb-3">Tüm alternatifleri tek seferde açın</p>
                                <button type="button" class="btn btn-success" onclick="openAllModals()">
                                    Hepsini Aç
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-blue-lt">
                            <div class="card-body">
                                <h3 class="card-title text-blue">📋 Test Talimatları</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>🎯 Değerlendirme Kriterleri:</h4>
                                        <ul>
                                            <li>Kullanım kolaylığı</li>
                                            <li>Görsel çekicilik</li>
                                            <li>İşlevsellik</li>
                                            <li>Responsive tasarım</li>
                                            <li>Performans</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>💬 Geri Bildirim:</h4>
                                        <p>Hangi tasarımı beğendiğinizi belirtin:</p>
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectDesign(1)">Tasarım 1</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="selectDesign(2)">Tasarım 2</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="selectDesign(3)">Tasarım 3</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="selectDesign(4)">Tasarım 4</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="selectDesign(5)">Tasarım 5</button>
                                            <button type="button" class="btn btn-outline-success" onclick="selectDesign('new')">YENİ</button>
                                        </div>
                                        <div id="selectedDesign" class="mt-2 text-center" style="display: none;">
                                            <div class="alert alert-success">
                                                <strong>Seçiminiz:</strong> <span id="selectedDesignText"></span>
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

<!-- Modal 1: Minimal & Clean -->
<div class="modal modal-blur fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="modal1Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="modal1Label">🤖 AI İçerik Üretici</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <!-- File Upload -->
                <div class="mb-4">
                    <label class="form-label text-muted mb-2">📄 Dosya Yükle (İsteğe Bağlı)</label>
                    <div class="border-2 border-dashed rounded p-4 text-center bg-light">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                        <p class="mb-0 text-muted">PDF veya görseli sürükleyin ya da <a href="#" class="text-primary">dosya seçin</a></p>
                        <small class="text-muted">Desteklenen formatlar: PDF, JPG, PNG, WEBP</small>
                    </div>
                </div>

                <!-- Content Brief -->
                <div class="mb-4">
                    <label class="form-label fw-medium">✍️ İçerik Brifingi</label>
                    <textarea class="form-control" rows="4" placeholder="İçeriğiniz hakkında kısa bir açıklama yazın..."></textarea>
                    <div class="form-text">Konu, hedef kitle, ton gibi detayları belirtebilirsiniz.</div>
                </div>

                <!-- Options -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="replaceContent1" checked>
                            <label class="form-check-label" for="replaceContent1">
                                Mevcut içeriği değiştir
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="preserveStyle1">
                            <label class="form-check-label" for="preserveStyle1">
                                Mevcut stili koru
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Progress (Hidden initially) -->
                <div class="mb-3" id="progress1" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                    </div>
                    <small class="text-muted">İçerik üretiliyor...</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="startGeneration(1)">
                    <i class="fas fa-magic me-2"></i>İçerik Üret
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Modern Card Style -->
<div class="modal modal-blur fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="modal2Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h4 class="modal-title fw-bold" id="modal2Label">🚀 Yapay Zeka İçerik Asistanı</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Upload Card -->
                    <div class="col-12">
                        <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3">
                                    <i class="fas fa-file-upload me-2"></i>Dosya Yükleme
                                </h5>
                                <div class="border-2 border-dashed border-primary rounded p-3 text-center">
                                    <div class="avatar avatar-lg bg-primary text-white rounded-circle mx-auto mb-2">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <p class="mb-2">Dosya yükle veya sürükle</p>
                                    <button class="btn btn-primary btn-sm">Dosya Seç</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brief Card -->
                    <div class="col-12">
                        <div class="card bg-info bg-opacity-10 border-info border-opacity-25">
                            <div class="card-body">
                                <h5 class="card-title text-info mb-3">
                                    <i class="fas fa-lightbulb me-2"></i>İçerik Brifingi
                                </h5>
                                <textarea class="form-control border-info" rows="3" placeholder="İçeriğiniz hakkında detay verin..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Card -->
                    <div class="col-12">
                        <div class="card bg-success bg-opacity-10 border-success border-opacity-25">
                            <div class="card-body">
                                <h5 class="card-title text-success mb-3">
                                    <i class="fas fa-cogs me-2"></i>Üretim Ayarları
                                </h5>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="switch1" checked>
                                            <label class="form-check-label" for="switch1">Üzerine yaz</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="switch2">
                                            <label class="form-check-label" for="switch2">SEO optimizasyonu</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress -->
                <div class="mt-3" id="progress2" style="display: none;">
                    <div class="card bg-warning bg-opacity-10 border-warning border-opacity-25">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                                <span class="text-warning fw-medium">AI içeriği oluşturuyor...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                <button type="button" class="btn btn-gradient-primary px-4" onclick="startGeneration(2)">
                    <i class="fas fa-robot me-2"></i>Üretmeye Başla
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Professional Dashboard -->
<div class="modal modal-blur fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="modal3Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title fw-bold" id="modal3Label">
                    <i class="fas fa-brain me-2"></i>AI Content Generation Dashboard
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left Panel -->
                    <div class="col-md-4 bg-light border-end">
                        <div class="p-4">
                            <h5 class="mb-3">📋 Configuration</h5>

                            <!-- File Upload Section -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">Source Files</label>
                                <div class="border rounded p-3 bg-white">
                                    <div class="text-center py-3">
                                        <i class="fas fa-file-import fa-2x text-muted mb-2"></i>
                                        <p class="mb-0 small">Drop files here</p>
                                        <button class="btn btn-sm btn-outline-primary mt-2">Browse</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Type -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">Content Type</label>
                                <select class="form-select">
                                    <option>Web Page Content</option>
                                    <option>Blog Article</option>
                                    <option>Product Description</option>
                                    <option>Marketing Copy</option>
                                </select>
                            </div>

                            <!-- Language & Tone -->
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-medium">Language</label>
                                    <select class="form-select form-select-sm">
                                        <option>Türkçe</option>
                                        <option>English</option>
                                        <option>العربية</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Tone</label>
                                    <select class="form-select form-select-sm">
                                        <option>Professional</option>
                                        <option>Casual</option>
                                        <option>Friendly</option>
                                        <option>Formal</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Advanced Options -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Advanced Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="seoOpt">
                                    <label class="form-check-label small" for="seoOpt">SEO Optimization</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="preserveFormat">
                                    <label class="form-check-label small" for="preserveFormat">Preserve Formatting</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeImages">
                                    <label class="form-check-label small" for="includeImages">Include Image Descriptions</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel -->
                    <div class="col-md-8">
                        <div class="p-4">
                            <h5 class="mb-3">✍️ Content Brief & Instructions</h5>

                            <div class="mb-3">
                                <label class="form-label">Project Brief</label>
                                <textarea class="form-control" rows="6" placeholder="Describe your content requirements in detail...

Examples:
• Create engaging web content for our new product launch
• Transform this PDF brochure into website copy
• Generate SEO-optimized content targeting specific keywords
• Maintain brand voice and include call-to-action elements"></textarea>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Target Keywords</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="keyword1, keyword2, keyword3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Content Length</label>
                                    <select class="form-select form-select-sm">
                                        <option>Auto (AI decides)</option>
                                        <option>Short (200-500 words)</option>
                                        <option>Medium (500-1000 words)</option>
                                        <option>Long (1000+ words)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Excluded Terms / Forbidden Words</label>
                                <input type="text" class="form-control form-control-sm" placeholder="word1, word2, phrase to avoid">
                                <div class="form-text">Specify words or phrases that should not appear in the generated content.</div>
                            </div>

                            <!-- Real-time Preview Area -->
                            <div class="border rounded p-3 bg-light" style="min-height: 150px;">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-eye fa-2x mb-2"></i>
                                    <p class="mb-0">Content preview will appear here</p>
                                    <small>Start generation to see real-time progress</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Footer -->
                <div class="border-top p-3 bg-light" id="progress3" style="display: none;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 45%"></div>
                            </div>
                            <small class="text-muted">Processing files and generating content... (45%)</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary">Processing PDF</span>
                            <span class="badge bg-info ms-1">Analyzing Images</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary">Save as Template</button>
                <button type="button" class="btn btn-primary px-4" onclick="startGeneration(3)">
                    <i class="fas fa-play me-2"></i>Start Generation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Creative & Colorful -->
<div class="modal modal-blur fade" id="modal4" tabindex="-1" role="dialog" aria-labelledby="modal4Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="modal-header border-0 text-white">
                <h4 class="modal-title fw-bold" id="modal4Label">🌟 Creative AI Content Creator</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Magic Upload Zone -->
                    <div class="col-12">
                        <div class="card border-0 shadow" style="background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 100%);">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <div class="avatar avatar-xl bg-white text-primary rounded-circle mx-auto">
                                        <i class="fas fa-magic fa-2x"></i>
                                    </div>
                                </div>
                                <h5 class="text-white mb-2">✨ Magic File Upload</h5>
                                <p class="text-white opacity-75 mb-3">Drop your files and watch the magic happen!</p>
                                <div class="border-2 border-dashed border-white rounded p-3">
                                    <button class="btn btn-white btn-pill">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Choose Files
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Creative Brief -->
                    <div class="col-12">
                        <div class="card border-0 shadow" style="background: linear-gradient(45deg, #a8edea 0%, #fed6e3 100%);">
                            <div class="card-body">
                                <h5 class="text-dark mb-3">
                                    <i class="fas fa-palette me-2"></i>🎨 Creative Brief
                                </h5>
                                <textarea class="form-control border-0 shadow-sm" rows="4"
                                    placeholder="Describe your dream content! Be creative, be bold! 🚀

💡 Ideas:
• Make it fun and engaging
• Add personality and flair
• Include storytelling elements
• Make it memorable and unique"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Fun Options -->
                    <div class="col-12">
                        <div class="card border-0 shadow" style="background: linear-gradient(45deg, #ffecd2 0%, #fcb69f 100%);">
                            <div class="card-body">
                                <h5 class="text-dark mb-3">
                                    <i class="fas fa-sliders-h me-2"></i>🎯 Creative Options
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="funMode" checked>
                                            <label class="form-check-label fw-medium" for="funMode">
                                                🎉 Fun Mode ON
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emojiMode">
                                            <label class="form-check-label fw-medium" for="emojiMode">
                                                😊 Add Emojis
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="storyMode">
                                            <label class="form-check-label fw-medium" for="storyMode">
                                                📚 Storytelling Style
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="modernMode">
                                            <label class="form-check-label fw-medium" for="modernMode">
                                                🚀 Modern & Trendy
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Animated Progress -->
                <div class="mt-3" id="progress4" style="display: none;">
                    <div class="card border-0 shadow" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center text-white">
                                <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                                <div>
                                    <div class="fw-medium">🎨 Creating your masterpiece...</div>
                                    <div class="small opacity-75">AI is painting with words!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0" style="background: rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Not Today</button>
                <button type="button" class="btn btn-warning btn-pill px-4" onclick="startGeneration(4)">
                    <i class="fas fa-rocket me-2"></i>Create Magic! ✨
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NEW MODAL: Minimal + Accordion Help -->
<div class="modal modal-blur fade" id="modalNew" tabindex="-1" role="dialog" aria-labelledby="modalNewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h4 class="modal-title fw-normal" id="modalNewLabel">🤖 AI İçerik Üretici</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">

                <!-- Main Content Area - Minimal Design -->
                <div class="mb-4">
                    <!-- Content Brief - Main Field -->
                    <div class="mb-3">
                        <label class="form-label mb-2">İçerik Açıklaması</label>
                        <textarea class="form-control" rows="3" placeholder="Ne tür içerik istediğinizi yazın..."></textarea>
                    </div>

                    <!-- File Upload - Compact -->
                    <div class="mb-3">
                        <label class="form-label mb-2">Dosya (İsteğe Bağlı)</label>
                        <div class="border border-dashed rounded p-3 text-center bg-light position-relative d-flex align-items-center justify-content-center"
                             style="cursor: pointer; min-height: 80px;"
                             onclick="document.getElementById('fileInput').click()"
                             ondrop="handleDrop(event)"
                             ondragover="handleDragOver(event)"
                             ondragenter="handleDragEnter(event)"
                             ondragleave="handleDragLeave(event)">

                            <input type="file" id="fileInput" class="d-none" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple>

                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-cloud-upload-alt me-2 fs-5"></i>
                                <span>Dosya seç veya sürükle</span>
                            </div>
                        </div>
                        <small>PDF, JPG, PNG, WEBP - Max 10MB</small>
                    </div>

                    <!-- Info Message -->
                    <div class="alert alert-info py-2" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <small><strong>Bilgi:</strong> Üretilen içerik mevcut içeriğin yerine geçecektir.</small>
                        </div>
                    </div>

                    <!-- Action Buttons - Above Accordion -->
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" onclick="startGeneration('new')">
                            <i class="fas fa-magic me-2"></i>İçerik Üret
                        </button>
                    </div>

                    <!-- Progress (Hidden initially) -->
                    <div class="mb-3" id="progressNew" style="display: none;">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                        </div>
                        <small class="mt-1 d-block text-center">İçerik üretiliyor...</small>
                    </div>
                </div>

                <!-- Help Section - Fixed Accordion -->
                <div class="accordion" id="helpAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="helpHeading1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpCollapse1" aria-expanded="false" aria-controls="helpCollapse1">
                                <i class="fas fa-question-circle text-primary me-2"></i>
                                Nasıl kullanırım?
                            </button>
                        </h2>
                        <div id="helpCollapse1" class="accordion-collapse collapse" aria-labelledby="helpHeading1" data-bs-parent="#helpAccordion">
                            <div class="accordion-body py-3">
                                <!-- Simple Steps -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-primary bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-primary text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">1</span>
                                                </div>
                                                <h6 class="card-title">Açıklama Yazın</h6>
                                                <p class="small mb-0">Ne istediğinizi kısa ve açık yazın</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-info bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-info text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">2</span>
                                                </div>
                                                <h6 class="card-title">Dosya Yükleyin</h6>
                                                <p class="small mb-0">İsteğe bağlı - PDF veya görsel</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-success bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-success text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">3</span>
                                                </div>
                                                <h6 class="card-title">İçerik Üret</h6>
                                                <p class="small mb-0">Butona tıklayın ve bekleyin</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <div>
                                            <strong>İpucu:</strong> Ne istediğinizi açık yazın. Örnek: "Şirketimiz hakkında sayfa, samimi dil"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="helpHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpCollapse4" aria-expanded="false" aria-controls="helpCollapse4">
                                <i class="fas fa-list text-success me-2"></i>
                                İçerik örnekleri ve kategoriler
                            </button>
                        </h2>
                        <div id="helpCollapse4" class="accordion-collapse collapse" aria-labelledby="helpHeading4" data-bs-parent="#helpAccordion">
                            <div class="accordion-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-primary">🏢 Kurumsal Sayfalar</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Hakkımızda sayfası, samimi dil"</li>
                                            <li>• "Hizmetlerimiz sayfası, profesyonel"</li>
                                            <li>• "İletişim sayfası, davetkar ton"</li>
                                            <li>• "Kariyer sayfası, motivasyonel"</li>
                                            <li>• "Vizyonumuz sayfası, ilham verici"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-success">🛍️ E-ticaret</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Ürün açıklaması, satış odaklı"</li>
                                            <li>• "Kategori tanıtımı, SEO dostu"</li>
                                            <li>• "Kampanya sayfası, aciliyet hissi"</li>
                                            <li>• "İndirim duyurusu, çekici"</li>
                                            <li>• "Ürün karşılaştırması, detaylı"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-info">📝 Blog & İçerik</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Blog yazısı, bilgilendirici"</li>
                                            <li>• "Rehber içerik, adım adım"</li>
                                            <li>• "Haber yazısı, objektif dil"</li>
                                            <li>• "Röportaj metni, samimi"</li>
                                            <li>• "Analiz yazısı, derinlemesine"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-warning">🎯 Pazarlama</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Landing page, dönüşüm odaklı"</li>
                                            <li>• "Email metni, kişisel ton"</li>
                                            <li>• "Sosyal medya yazısı, viral"</li>
                                            <li>• "Bülten içeriği, güncel"</li>
                                            <li>• "Reklam metni, dikkat çekici"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-danger">📄 PDF/Görsel Değişiklikleri</h6>
                                        <ul class="list-unstyled small mb-0">
                                            <li>• "ABC şirketi yerine XYZ şirketi yaz"</li>
                                            <li>• "2023 fiyatları yerine 2024 fiyatları"</li>
                                            <li>• "Eski adres yerine yeni adres"</li>
                                            <li>• "Demo marka yerine gerçek marka"</li>
                                            <li>• "Placeholder metinleri kaldır"</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="helpHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpCollapse2" aria-expanded="false" aria-controls="helpCollapse2">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                Hangi dosyaları yükleyebilirim?
                            </button>
                        </h2>
                        <div id="helpCollapse2" class="accordion-collapse collapse" aria-labelledby="helpHeading2" data-bs-parent="#helpAccordion">
                            <div class="accordion-body py-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="avatar avatar-sm bg-danger text-white me-3">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">PDF Dosyaları</h6>
                                                <small>Metinleri okuyup anlıyoruz</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="avatar avatar-sm bg-success text-white me-3">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Görsel Dosyalar</h6>
                                                <small>JPG, PNG, WEBP</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Hatırlatma:</strong> Dosya yükleme zorunlu değil! Sadece açıklama yazarak da içerik üretebilirsiniz.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="helpHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpCollapse3" aria-expanded="false" aria-controls="helpCollapse3">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                İpuçları ve özel talimatlar
                            </button>
                        </h2>
                        <div id="helpCollapse3" class="accordion-collapse collapse" aria-labelledby="helpHeading3" data-bs-parent="#helpAccordion">
                            <div class="accordion-body py-3">
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <div>
                                            <strong>İpucu:</strong> Dil (Türkçe/İngilizce), ton (samimi/resmi), hedef kitle ve amaç belirtin. PDF/görsel yüklerseniz, içeriği nasıl değiştirmek istediğinizi de yazın.
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <div>
                                            <strong>PDF/Görsel değişiklikleri:</strong> "ABC şirketi yerine XYZ şirketi yaz", "eski logo yerine yeni logo", "fiyatları güncelle" gibi spesifik talimatlar verin.
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

<!-- Modal 5: Advanced Step-by-Step -->
<div class="modal modal-blur fade" id="modal5" tabindex="-1" role="dialog" aria-labelledby="modal5Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 bg-gradient-primary text-white">
                <h4 class="modal-title fw-bold" id="modal5Label">
                    <i class="fas fa-cogs me-2"></i>Advanced AI Content Wizard
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Step Progress -->
                <div class="bg-light px-4 py-3 border-bottom">
                    <div class="steps steps-green steps-counter">
                        <a href="#" class="step-item active">
                            <span class="step-counter">1</span>
                            <span class="step-name">Upload</span>
                        </a>
                        <a href="#" class="step-item">
                            <span class="step-counter">2</span>
                            <span class="step-name">Configure</span>
                        </a>
                        <a href="#" class="step-item">
                            <span class="step-counter">3</span>
                            <span class="step-name">Brief</span>
                        </a>
                        <a href="#" class="step-item">
                            <span class="step-counter">4</span>
                            <span class="step-name">Generate</span>
                        </a>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="p-4" id="stepContent">
                    <!-- Step 1: Upload -->
                    <div class="step-content" id="step1">
                        <h5 class="mb-3">
                            <i class="fas fa-upload text-primary me-2"></i>Step 1: File Upload
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="border-2 border-dashed rounded p-4 text-center bg-light">
                                    <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                    <h6>Drag & Drop Your Files</h6>
                                    <p class="text-muted mb-3">or click to browse</p>
                                    <button class="btn btn-primary">Select Files</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info bg-opacity-10">
                                    <div class="card-body">
                                        <h6 class="card-title text-info">📋 Supported Formats</h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li>📄 PDF Documents</li>
                                            <li>🖼️ Images (JPG, PNG)</li>
                                            <li>📝 Text Files</li>
                                            <li>📊 Word Documents</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" onclick="nextStep(2)">Next: Configure Settings</button>
                        </div>
                    </div>

                    <!-- Step 2: Configure -->
                    <div class="step-content" id="step2" style="display: none;">
                        <h5 class="mb-3">
                            <i class="fas fa-cog text-primary me-2"></i>Step 2: Configuration
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Content Type</label>
                                <select class="form-select">
                                    <option>Website Content</option>
                                    <option>Blog Article</option>
                                    <option>Product Description</option>
                                    <option>Marketing Copy</option>
                                    <option>Technical Documentation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Output Language</label>
                                <select class="form-select">
                                    <option>🇹🇷 Turkish</option>
                                    <option>🇬🇧 English</option>
                                    <option>🇸🇦 Arabic</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Writing Style</label>
                                <select class="form-select">
                                    <option>Professional</option>
                                    <option>Conversational</option>
                                    <option>Academic</option>
                                    <option>Creative</option>
                                    <option>Technical</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Content Length</label>
                                <select class="form-select">
                                    <option>Auto-detect</option>
                                    <option>Short (200-500 words)</option>
                                    <option>Medium (500-1000 words)</option>
                                    <option>Long (1000+ words)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-outline-secondary" onclick="prevStep(1)">Previous</button>
                            <button class="btn btn-primary ms-2" onclick="nextStep(3)">Next: Content Brief</button>
                        </div>
                    </div>

                    <!-- Step 3: Brief -->
                    <div class="step-content" id="step3" style="display: none;">
                        <h5 class="mb-3">
                            <i class="fas fa-edit text-primary me-2"></i>Step 3: Content Brief
                        </h5>
                        <div class="mb-3">
                            <label class="form-label">Detailed Instructions</label>
                            <textarea class="form-control" rows="5" placeholder="Provide detailed instructions for content generation...

Example:
- Target audience: business professionals
- Key message: emphasize reliability and innovation
- Include call-to-action buttons
- Maintain SEO-friendly structure
- Use specific brand terminology"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Keywords to Include</label>
                                <input type="text" class="form-control" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keywords to Avoid</label>
                                <input type="text" class="form-control" placeholder="avoid1, avoid2, avoid3">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-outline-secondary" onclick="prevStep(2)">Previous</button>
                            <button class="btn btn-primary ms-2" onclick="nextStep(4)">Next: Generate Content</button>
                        </div>
                    </div>

                    <!-- Step 4: Generate -->
                    <div class="step-content" id="step4" style="display: none;">
                        <h5 class="mb-3">
                            <i class="fas fa-rocket text-primary me-2"></i>Step 4: Generate Content
                        </h5>
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">✅ Ready to Generate</h6>
                                <p class="card-text">All configurations completed. Click the button below to start content generation.</p>
                                <div class="row g-2 small text-muted">
                                    <div class="col-6">
                                        <strong>Files:</strong> <span id="fileCount">1 PDF</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Type:</strong> <span id="contentType">Website Content</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Language:</strong> <span id="language">Turkish</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Style:</strong> <span id="style">Professional</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time Progress -->
                        <div class="mt-3" id="progress5" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-medium">Generation Progress</span>
                                        <span class="text-muted">65%</span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 65%"></div>
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-4">
                                            <span class="badge bg-success">✓ File Analysis</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="badge bg-success">✓ Content Extraction</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="badge bg-primary">⏳ AI Generation</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-outline-secondary" onclick="prevStep(3)">Previous</button>
                            <button class="btn btn-success ms-2 px-4" onclick="startGeneration(5)">
                                <i class="fas fa-play me-2"></i>Start Generation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Gradient Buttons */
.btn-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    color: white;
}

.btn-gradient-primary:hover {
    background: linear-gradient(45deg, #0056b3, #004085);
    color: white;
}

/* Step Wizard Styles */
.steps {
    display: flex;
    justify-content: center;
}

.steps .step-item {
    text-decoration: none;
    color: #6c757d;
    margin: 0 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.steps .step-item.active {
    color: #198754;
}

.step-counter {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    line-height: 2rem;
    text-align: center;
    border-radius: 50%;
    background: #e9ecef;
    margin-bottom: 0.5rem;
}

.steps .step-item.active .step-counter {
    background: #198754;
    color: white;
}

/* Custom Modal Styles */
.modal-blur .modal-content {
    backdrop-filter: blur(10px);
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.btn-pill {
    border-radius: 50px;
}

/* Animation for progress bars */
@keyframes progress-bar-stripes {
    0% {
        background-position: 1rem 0;
    }
    100% {
        background-position: 0 0;
    }
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

/* Accordion Hover Effects */
.accordion-button {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.accordion-button:hover {
    background-color: var(--tblr-primary) !important;
    color: white !important;
    border-color: var(--tblr-primary) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.accordion-button:hover i {
    color: white !important;
}

.accordion-button:not(.collapsed) {
    background-color: var(--tblr-primary) !important;
    color: white !important;
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.accordion-button:not(.collapsed) i {
    color: white !important;
}

.accordion-button:focus {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25);
}

.accordion-item {
    border: 1px solid var(--tblr-border-color);
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
    overflow: hidden;
}

.accordion-button::after {
    transition: transform 0.3s ease;
}
</style>

<script>
// Design selection function
function selectDesign(designNumber) {
    // Update UI
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active', 'btn-primary');
        btn.classList.add('btn-outline-primary');
    });

    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary', 'active');

    // Show selection
    document.getElementById('selectedDesign').style.display = 'block';
    const designText = designNumber === 'new' ? 'Yeni Minimal + Accordion Tasarımı' : `Tasarım ${designNumber}`;
    document.getElementById('selectedDesignText').textContent = designText;

    // Log selection (for demonstration)
    console.log(`User selected Design ${designNumber}`);

    // You could make an AJAX call here to save the preference
    // Example: saveDesignPreference(designNumber);
}

// Open all modals function
function openAllModals() {
    // Open modals with slight delays for better UX
    const modals = ['modal1', 'modal2', 'modal3', 'modal4', 'modal5'];

    modals.forEach((modalId, index) => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }, index * 200); // 200ms delay between each modal
    });
}

// Mock content generation function
function startGeneration(modalNumber) {
    const progressElement = document.getElementById(`progress${modalNumber}`);
    if (progressElement) {
        progressElement.style.display = 'block';

        // Simulate generation process
        setTimeout(() => {
            const designName = modalNumber === 'new' ? 'Yeni Minimal + Accordion' : `Tasarım ${modalNumber}`;
            alert(`${designName}: İçerik üretimi tamamlandı! (Demo)`);
            progressElement.style.display = 'none';
        }, 3000);
    }
}

// Step wizard functions for Modal 5
function nextStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.style.display = 'none';
    });

    // Show target step
    document.getElementById(`step${stepNumber}`).style.display = 'block';

    // Update step indicators
    document.querySelectorAll('.step-item').forEach((item, index) => {
        if (index < stepNumber) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

function prevStep(stepNumber) {
    nextStep(stepNumber);
}

// Drag & Drop functions for file upload
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDragEnter(e) {
    e.preventDefault();
    e.stopPropagation();
    e.target.closest('.border-dashed').classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    // Only remove classes if we're actually leaving the drop zone
    if (!e.target.closest('.border-dashed').contains(e.relatedTarget)) {
        e.target.closest('.border-dashed').classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    }
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();

    const dropZone = e.target.closest('.border-dashed');
    dropZone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        console.log('Files dropped:', files);
        // Process files here
        displayUploadedFiles(files);
    }
}

function displayUploadedFiles(files) {
    const dropZone = document.querySelector('.border-dashed');
    const fileList = Array.from(files).map(file =>
        `<div class="d-flex align-items-center justify-content-between mb-1">
            <span class="small">${file.name}</span>
            <span class="badge bg-success small">${formatFileSize(file.size)}</span>
        </div>`
    ).join('');

    dropZone.innerHTML = `
        <div class="text-success">
            <i class="fas fa-check-circle me-2"></i>
            <span class="small">${files.length} dosya yüklendi</span>
        </div>
        <div class="mt-2">${fileList}</div>
    `;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// File input change handler
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                displayUploadedFiles(e.target.files);
            }
        });
    }
});

// Initialize tooltips and other Bootstrap components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    console.log('AI Modal Test Page Loaded');
});
</script>
@endsection