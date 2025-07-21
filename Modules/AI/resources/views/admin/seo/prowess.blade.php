@extends('admin.layout')

@include('ai::helper')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'SEO AI Expert Center')

@push('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.ai.index') }}">AI Module</a>
        </li>
        <li class="breadcrumb-item active">SEO AI Center</li>
    </ol>
</nav>
@endpush

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* SEO-specific color scheme */
    :root {
        --seo-primary: #10b981;
        --seo-secondary: #059669;
        --seo-accent: #065f46;
        --seo-light: #d1fae5;
    }

    /* Tabler-uyumlu temiz tasarım */
    .seo-prowess-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid var(--seo-primary);
    }

    .seo-prowess-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        border-left-color: var(--seo-secondary);
    }

    .seo-skill-icon {
        font-size: 3rem;
        line-height: 1;
        margin-bottom: 1rem;
        color: var(--seo-primary);
    }

    .seo-hero-section {
        background: linear-gradient(135deg, var(--seo-primary) 0%, var(--seo-secondary) 100%);
        border-radius: var(--tblr-border-radius-lg);
        padding: 3rem;
        margin-bottom: 2rem;
        text-align: center;
        color: white;
    }

    .seo-category-header {
        background: linear-gradient(135deg, var(--seo-accent) 0%, var(--seo-primary) 100%);
        border-radius: var(--tblr-border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .seo-skill-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        background: var(--seo-light);
        color: var(--seo-accent);
    }

    .seo-test-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.925rem;
        border-radius: var(--tblr-border-radius);
        background: linear-gradient(135deg, var(--seo-primary) 0%, var(--seo-secondary) 100%);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .seo-test-btn:hover {
        background: linear-gradient(135deg, var(--seo-secondary) 0%, var(--seo-accent) 100%);
        transform: translateY(-1px);
        color: white;
    }

    .seo-result-showcase {
        margin-top: 1rem;
        border-radius: var(--tblr-border-radius);
        border: 2px solid var(--seo-light);
    }

    .seo-result-header {
        background: var(--seo-light);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--seo-primary);
        border-radius: var(--tblr-border-radius) var(--tblr-border-radius) 0 0;
        color: var(--seo-accent);
    }

    .seo-result-content {
        padding: 1.25rem;
        line-height: 1.7;
        background: #fefefe;
    }

    .seo-search-highlight {
        background: var(--seo-light);
        padding: 0 0.25rem;
        border-radius: 0.25rem;
        font-weight: 600;
        color: var(--seo-accent);
    }

    .seo-skill-card {
        transition: opacity 0.2s ease;
    }

    .seo-skill-card.hidden {
        display: none !important;
    }

    .seo-category-section.no-results {
        opacity: 0.3;
    }

    .seo-nav-btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border-radius: var(--tblr-border-radius-lg);
        border: 2px solid var(--seo-primary);
        color: var(--seo-primary);
        background: white;
        transition: all 0.2s ease;
    }

    .seo-nav-active {
        background: var(--seo-primary) !important;
        color: white !important;
        border-color: var(--seo-primary) !important;
    }

    .seo-nav-btn:hover {
        background: var(--seo-light);
        border-color: var(--seo-secondary);
    }

    /* Grid düzeni - SEO optimized */
    .seo-skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .seo-skills-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .seo-hero-section {
            padding: 2rem 1rem;
        }
    }

    /* SEO-themed card heights */
    .seo-prowess-card .card-body {
        display: flex;
        flex-direction: column;
        min-height: 350px;
    }

    .seo-prowess-card .card-footer {
        margin-top: auto;
        background: var(--seo-light);
        border-top: 1px solid var(--seo-primary);
    }

    /* SEO Performance Dashboard */
    .seo-performance-card {
        background: linear-gradient(135deg, #ffffff 0%, var(--seo-light) 100%);
        border: 2px solid var(--seo-primary);
        transition: transform 0.2s ease;
    }

    .seo-performance-card:hover {
        transform: scale(1.02);
    }

    .seo-metric-icon {
        font-size: 2rem;
        color: var(--seo-primary);
        margin-bottom: 0.5rem;
    }

    /* SEO Input styling */
    .seo-input-area {
        border: 2px solid var(--seo-light);
        border-radius: var(--tblr-border-radius);
        transition: border-color 0.2s ease;
    }

    .seo-input-area:focus {
        border-color: var(--seo-primary);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    /* Example buttons styling */
    .seo-example-btn {
        background: white;
        border: 1px solid var(--seo-primary);
        color: var(--seo-primary);
        padding: 0.25rem 0.75rem;
        border-radius: var(--tblr-border-radius);
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }

    .seo-example-btn:hover {
        background: var(--seo-primary);
        color: white;
    }

    /* SEO Stats Cards */
    .stats-showcase {
        background: linear-gradient(135deg, var(--seo-primary) 0%, var(--seo-secondary) 100%);
        color: white;
    }
</style>
@endpush

@section('content')
    <!-- SEO Hero Section -->
    <div class="seo-hero-section">
        <div class="row align-items-center">
            <div class="col-md-8 mx-auto">
                <h1 class="display-5 fw-bold mb-3">
                    🚀 SEO AI Expert Center
                </h1>
                <p class="lead mb-4">
                    Google'da üst sıralarda çıkmak için yapay zeka destekli SEO araçları
                </p>
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="seo-metric-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <strong>Anahtar Kelime</strong><br>
                        <small>Analiz & Araştırma</small>
                    </div>
                    <div class="col-md-4">
                        <div class="seo-metric-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <strong>İçerik Optimizasyon</strong><br>
                        <small>AI Destekli Analiz</small>
                    </div>
                    <div class="col-md-4">
                        <div class="seo-metric-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <strong>Ranking Stratejisi</strong><br>
                        <small>Profesyonel Öneriler</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Navigation Section -->
    <div class="row mt-4 mb-4">
        <div class="col-md-8">
            <div class="input-group input-group-lg">
                <span class="input-group-text" style="background: var(--seo-primary); color: white; border-color: var(--seo-primary);">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="seo-skill-search" 
                       placeholder="SEO araçlarında ara... (İsim, açıklama, kategori)" 
                       style="border-color: var(--seo-primary);" />
                <button class="btn btn-outline-secondary" type="button" onclick="clearSEOSearch()" 
                        style="border-color: var(--seo-primary); color: var(--seo-primary);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-muted small mt-1">
                <span id="seo-search-results-count"></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-grid">
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-success btn-lg">
                    <i class="fas fa-cog me-2"></i>SEO Araçlarını Yönet
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center flex-wrap">
                <strong class="me-3 text-muted">🎯 Hızlı Erişim:</strong>
                <div class="btn-group flex-wrap" role="group" id="seo-category-nav">
                    <!-- JavaScript ile doldurulacak -->
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Performance Dashboard -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card seo-performance-card text-center h-100">
                <div class="card-body">
                    <div class="seo-metric-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="h4 fw-bold text-success" id="seo-token-display">{{ ai_format_token_count($tokenStatus['remaining']) }}</div>
                    <p class="text-muted mb-0 small">Kullanılabilir Kredi</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card seo-performance-card text-center h-100">
                <div class="card-body">
                    <div class="seo-metric-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="h4 fw-bold" style="color: var(--seo-primary);">{{ count($seoFeatures->flatten()) }}</div>
                    <p class="text-muted mb-0 small">SEO Aracı</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card seo-performance-card text-center h-100">
                <div class="card-body">
                    <div class="seo-metric-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="h4 fw-bold" style="color: var(--seo-secondary);">{{ count($seoFeatures) }}</div>
                    <p class="text-muted mb-0 small">SEO Kategorisi</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card seo-performance-card text-center h-100">
                <div class="card-body">
                    <div class="seo-metric-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="h4 fw-bold text-info">{{ $tokenStatus['provider_active'] ? '🟢' : '🔴' }}</div>
                    <p class="text-muted mb-0 small">{{ $tokenStatus['provider'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(empty($seoFeatures))
    <!-- No SEO Tools Available -->
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-img">
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <p class="empty-title h3">Henüz SEO aracı bulunmuyor</p>
                <p class="empty-subtitle text-muted">
                    SEO AI araçlarını yapılandırmak için seeder'ları çalıştırın.
                </p>
                <div class="empty-action">
                    <a href="{{ route('admin.ai.features.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>SEO Araçlarını Yapılandır
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- SEO Tools Showcase -->
    @foreach($seoFeatures as $category => $categoryFeatures)
    <div class="seo-category-section mb-5" data-category="{{ $category }}" id="seo-category-{{ Str::slug($category) }}">
        <div class="card seo-category-header text-white">
            <div class="card-body">
                <h2 class="mb-2 h3">
                    <i class="fas fa-search-plus me-2"></i>
                    {{ $categoryNames[$category] ?? ucfirst($category) }}
                    <span class="badge seo-skill-badge ms-2">{{ count($categoryFeatures) }} araç</span>
                </h2>
                <p class="mb-0 text-white-50">Google'da üst sıralarda çıkmak için {{ strtolower($categoryNames[$category] ?? $category) }} araçları</p>
            </div>
        </div>

        <div class="seo-skills-grid">
            @foreach($categoryFeatures as $feature)
            <div class="seo-skill-card" 
                 data-skill-name="{{ strtolower($feature->name) }}" 
                 data-skill-description="{{ strtolower($feature->description) }}"
                 data-skill-category="{{ strtolower($categoryNames[$category] ?? $category) }}">
                <div class="card seo-prowess-card h-100">
                    <div class="card-body text-center">
                        <!-- SEO Tool Icon & Title -->
                        <div class="seo-skill-icon">{{ $feature->emoji ?? '🔍' }}</div>
                        <h3 class="card-title fw-bold mb-3 fs-3" style="color: var(--seo-accent);">{{ $feature->name }}</h3>

                        <!-- Description -->
                        <p class="text-muted mb-4 fs-6">{{ $feature->description }}</p>

                        <!-- SEO Tool Level -->
                        <div class="mb-4">
                            <span class="badge text-white px-3 py-2" 
                                  style="background: linear-gradient(135deg, var(--seo-primary) 0%, var(--seo-secondary) 100%);">
                                {{ $feature->getComplexityName() }} Seviye
                            </span>
                        </div>

                        <!-- Example Inputs -->
                        @if($feature->example_inputs)
                        <div class="mb-3">
                            <div class="text-muted small mb-2">💡 Örnek kullanımlar:</div>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @php
                                    $exampleInputs = is_string($feature->example_inputs) 
                                        ? json_decode($feature->example_inputs, true) ?? []
                                        : (is_array($feature->example_inputs) ? $feature->example_inputs : []);
                                @endphp
                                @foreach(array_slice($exampleInputs, 0, 3) as $example)
                                <button class="btn seo-example-btn"
                                    onclick="setSEOExamplePrompt({{ $feature->id }}, '{{ addslashes($example) }}');">
                                    {{ Str::limit($example, 25) }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- SEO Input -->
                        <div class="mb-4">
                            <textarea id="seo-input-{{ $feature->id }}" class="form-control form-control-lg seo-input-area" rows="3"
                                placeholder="{{ $feature->input_placeholder ?? 'SEO analizi için içeriğinizi buraya yazın...' }}"></textarea>
                        </div>

                        <!-- SEO Test Button -->
                        <button class="seo-test-btn w-100 mb-3" onclick="testSEOSkill({{ $feature->id }})"
                            id="seo-btn-{{ $feature->id }}">
                            <span class="btn-text">
                                <i class="fas fa-rocket me-2"></i>{{ $feature->button_text ?? 'SEO Analizi Yap' }}
                            </span>
                            <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"
                                style="display: none;"></span>
                        </button>

                        <!-- SEO Result Showcase -->
                        <div class="seo-result-showcase" id="seo-result-{{ $feature->id }}" style="display: none;">
                            <div class="seo-result-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">🎯 SEO Analiz Sonucu</div>
                                            <small class="opacity-75" id="seo-result-meta-{{ $feature->id }}">Analiz tamamlandı</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-light" onclick="clearSEOResult({{ $feature->id }})"
                                        title="Sonucu temizle">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="seo-result-content" id="seo-result-content-{{ $feature->id }}"></div>
                        </div>
                    </div>

                    <!-- SEO Tool Stats -->
                    <div class="card-footer text-center">
                        <div class="row">
                            <div class="col-4">
                                <div class="text-muted small">Kullanım</div>
                                <div class="fw-bold" style="color: var(--seo-primary);">{{ number_format($feature->usage_count ?? 0) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Başarı</div>
                                <div class="fw-bold" style="color: var(--seo-secondary);">
                                    {{ ($feature->avg_rating ?? 0) > 0 ? number_format($feature->avg_rating, 1) : '-' }}
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Tür</div>
                                <div class="fw-bold text-muted">{{ $feature->getCategoryName() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif

@push('js')
<!-- Universal AI Word Buffer System -->
<script src="{{ asset('admin-assets/libs/ai-word-buffer/ai-word-buffer.js') }}"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // SEO Search & Navigation System
    let allSEOSkillCards = [];
    let allSEOCategories = [];
    let activeSEOCategory = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializeSEOSearchAndNavigation();
    });

    function initializeSEOSearchAndNavigation() {
        // Collect all SEO skill cards and categories
        allSEOSkillCards = document.querySelectorAll('.seo-skill-card');
        allSEOCategories = Array.from(document.querySelectorAll('.seo-category-section')).map(section => ({
            id: section.id,
            name: section.dataset.category,
            displayName: section.querySelector('.seo-category-header h2').textContent.trim(),
            element: section
        }));

        // Setup SEO search functionality
        setupSEOSearchFunctionality();
        
        // Setup SEO category navigation
        setupSEOCategoryNavigation();
        
        // Update SEO search count
        updateSEOSearchCount(allSEOSkillCards.length, allSEOSkillCards.length);
    }

    function setupSEOSearchFunctionality() {
        const searchInput = document.getElementById('seo-skill-search');
        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            performSEOSearch(searchTerm);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = this.value.toLowerCase().trim();
                if (searchTerm) {
                    // İlk bulunan SEO sonucuna scroll
                    const firstVisible = document.querySelector('.seo-skill-card:not(.hidden)');
                    if (firstVisible) {
                        firstVisible.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        });
    }

    function setupSEOCategoryNavigation() {
        const navContainer = document.getElementById('seo-category-nav');
        if (!navContainer || allSEOCategories.length === 0) return;

        // "Tümü" butonu
        const allBtn = document.createElement('button');
        allBtn.className = 'btn seo-nav-btn seo-nav-active';
        allBtn.textContent = 'Tüm SEO Araçları';
        allBtn.onclick = () => filterSEOByCategory(null);
        navContainer.appendChild(allBtn);

        // SEO kategori butonları
        allSEOCategories.forEach(category => {
            const btn = document.createElement('button');
            btn.className = 'btn seo-nav-btn';
            
            // Kategori adını temizle ve SEO tool sayısını ekle
            const cleanName = category.displayName.replace(/^\d+\s*/, '').replace(/\s*\d+.*$/, '');
            const skillCount = category.element.querySelectorAll('.seo-skill-card').length;
            btn.textContent = `${cleanName} (${skillCount})`;
            
            btn.onclick = () => filterSEOByCategory(category.id);
            navContainer.appendChild(btn);
        });
    }

    function performSEOSearch(searchTerm) {
        let visibleCount = 0;
        let totalCount = allSEOSkillCards.length;

        if (!searchTerm) {
            // Arama terimi yoksa tümünü göster
            allSEOSkillCards.forEach(card => {
                card.classList.remove('hidden');
                removeSEOHighlights(card);
                visibleCount++;
            });
            
            allSEOCategories.forEach(category => {
                category.element.classList.remove('no-results');
            });
        } else {
            // SEO arama yap
            allSEOSkillCards.forEach(card => {
                const name = card.dataset.skillName;
                const description = card.dataset.skillDescription;
                const category = card.dataset.skillCategory;
                
                const searchableText = `${name} ${description} ${category}`;
                
                if (searchableText.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    highlightSEOSearchTerm(card, searchTerm);
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                    removeSEOHighlights(card);
                }
            });

            // SEO kategorileri kontrol et - hiç görünür skill yoksa kategoriyi gizle
            allSEOCategories.forEach(category => {
                const visibleSkillsInCategory = category.element.querySelectorAll('.seo-skill-card:not(.hidden)').length;
                if (visibleSkillsInCategory === 0) {
                    category.element.classList.add('no-results');
                } else {
                    category.element.classList.remove('no-results');
                }
            });
        }

        updateSEOSearchCount(visibleCount, totalCount);
    }

    function filterSEOByCategory(categoryId) {
        activeSEOCategory = categoryId;
        
        // SEO buton aktif durumlarını güncelle
        document.querySelectorAll('#seo-category-nav .btn').forEach(btn => {
            btn.classList.remove('seo-nav-active');
        });

        if (categoryId) {
            // Belirli SEO kategorisini göster
            allSEOCategories.forEach(category => {
                if (category.id === categoryId) {
                    category.element.style.display = 'block';
                    category.element.classList.remove('no-results');
                    
                    // Butonu aktif yap
                    const btn = Array.from(document.querySelectorAll('#seo-category-nav .btn')).find(b => 
                        b.textContent.includes(category.displayName.replace(/^\d+\s*/, '').replace(/\s*\d+.*$/, ''))
                    );
                    if (btn) {
                        btn.classList.add('seo-nav-active');
                    }
                    
                    // SEO kategorisine scroll
                    setTimeout(() => {
                        category.element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                } else {
                    category.element.style.display = 'none';
                }
            });
        } else {
            // Tüm SEO araçlarını göster
            allSEOCategories.forEach(category => {
                category.element.style.display = 'block';
                category.element.classList.remove('no-results');
            });
            
            // "Tümü" butonunu aktif yap
            const allBtn = document.querySelector('#seo-category-nav .btn');
            if (allBtn) {
                allBtn.classList.add('seo-nav-active');
            }
        }

        // SEO arama sonuçlarını güncelle
        const visibleCards = document.querySelectorAll('.seo-skill-card:not(.hidden)');
        updateSEOSearchCount(visibleCards.length, allSEOSkillCards.length);
    }

    function highlightSEOSearchTerm(card, searchTerm) {
        const title = card.querySelector('.card-title');
        const description = card.querySelector('.text-muted');
        
        if (title) {
            const titleText = title.textContent;
            const highlightedTitle = titleText.replace(
                new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi'),
                '<span class="seo-search-highlight">$1</span>'
            );
            title.innerHTML = highlightedTitle;
        }
        
        if (description) {
            const descText = description.textContent;
            const highlightedDesc = descText.replace(
                new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi'),
                '<span class="seo-search-highlight">$1</span>'
            );
            description.innerHTML = highlightedDesc;
        }
    }

    function removeSEOHighlights(card) {
        const highlights = card.querySelectorAll('.seo-search-highlight');
        highlights.forEach(highlight => {
            highlight.outerHTML = highlight.textContent;
        });
    }

    function updateSEOSearchCount(visible, total) {
        const countElement = document.getElementById('seo-search-results-count');
        if (!countElement) return;

        if (visible === total) {
            countElement.textContent = `${total} SEO aracı gösteriliyor`;
        } else {
            countElement.textContent = `${visible} / ${total} SEO aracı gösteriliyor`;
        }
    }

    function clearSEOSearch() {
        const searchInput = document.getElementById('seo-skill-search');
        if (searchInput) {
            searchInput.value = '';
            performSEOSearch('');
            searchInput.focus();
        }
    }

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // SEO Test skill function
    async function testSEOSkill(featureId) {
        console.log('testSEOSkill called for feature:', featureId);
        const inputElement = document.getElementById(`seo-input-${featureId}`);
        const btnElement = document.getElementById(`seo-btn-${featureId}`);
        const resultElement = document.getElementById(`seo-result-${featureId}`);
        const resultContentElement = document.getElementById(`seo-result-content-${featureId}`);
        const btnText = btnElement.querySelector('.btn-text');
        const loadingSpinner = btnElement.querySelector('.loading-spinner');

        const inputText = inputElement ? inputElement.value.trim() : '';
        
        if (inputElement && !inputText) {
            alert('SEO analizi için içerik giriniz!');
            inputElement.focus();
            return;
        }

        // UI state - loading
        btnElement.disabled = true;
        btnText.innerHTML = '<i class="fas fa-cogs me-2"></i>🔍 SEO Analizi Yapılıyor...';
        loadingSpinner.style.display = 'inline-block';
        resultElement.style.display = 'none';

        try {
            const response = await fetch('{{ route("admin.ai.test-feature") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    feature_id: featureId,
                    input_text: inputText
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update SEO meta info
                const metaElement = document.getElementById(`seo-result-meta-${featureId}`);
                metaElement.innerHTML = `
                    <i class="fas fa-check-circle me-1"></i>
                    ${data.tokens_used_formatted || (data.tokens_used + ' kredi kullanıldı')}
                    ${data.processing_time ? ` • ${data.processing_time}ms` : ''}
                `;
                
                // Update token display real-time
                if (data.new_balance_formatted !== undefined) {
                    const tokenDisplay = document.getElementById('seo-token-display');
                    if (tokenDisplay) {
                        tokenDisplay.textContent = data.new_balance_formatted;
                    }
                }
                
                // 🚀 WORD BUFFER SYSTEM ENTEGRASYONU
                resultElement.style.display = 'block';
                
                // Word buffer config kontrolü
                if (data.word_buffer_enabled && data.word_buffer_config && window.AIWordBuffer) {
                    console.log('🎯 Using word buffer system for SEO result');
                    
                    // Word buffer ile display
                    const buffer = new window.AIWordBuffer(resultContentElement, {
                        wordDelay: data.word_buffer_config.delay_between_words || 150,
                        fadeEffect: true,
                        enableMarkdown: true,
                        scrollCallback: () => {
                            // Smooth scroll to SEO result
                            resultElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                    
                    // Buffer'ı başlat
                    buffer.start();
                    
                    // SEO Content'i ekle (formatlanmış AI yanıtı)
                    const formattedResponse = formatSEOAIResponse(data.response || data.ai_result || 'SEO analizi alınamadı');
                    buffer.addContent(formattedResponse);
                    
                    // SEO showcase mode efektleri
                    setTimeout(() => {
                        buffer.flush();
                        
                        // SEO prowess showcase için özel glow efekti
                        if (data.word_buffer_config.showcase_mode) {
                            resultElement.style.transition = 'all 0.3s ease';
                            resultElement.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.3)';
                            setTimeout(() => {
                                resultElement.style.boxShadow = '';
                            }, 2000);
                        }
                    }, 100);
                    
                } else {
                    // Normal display (fallback)
                    console.log('💭 Using normal display for SEO result');
                    resultContentElement.innerHTML = formatSEOAIResponse(data.response || data.ai_result || 'SEO analizi alınamadı');
                }
            } else {
                // Error state
                resultContentElement.innerHTML = `
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Hata: ${data.message || 'Bilinmeyen hata oluştu'}
                    </div>
                `;
                resultElement.style.display = 'block';
            }

        } catch (error) {
            console.error('SEO Skill test error:', error);
            resultContentElement.innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Bağlantı hatası: ${error.message}
                </div>
            `;
            resultElement.style.display = 'block';
        } finally {
            // Reset UI state
            btnElement.disabled = false;
            btnText.innerHTML = '<i class="fas fa-rocket me-2"></i>SEO Analizi Yap';
            loadingSpinner.style.display = 'none';
        }
    }

    // Clear SEO result function
    function clearSEOResult(featureId) {
        const resultElement = document.getElementById(`seo-result-${featureId}`);
        resultElement.style.display = 'none';
    }

    // Set SEO example prompt
    function setSEOExamplePrompt(featureId, prompt) {
        console.log('setSEOExamplePrompt called:', { featureId, prompt });
        const inputElement = document.getElementById(`seo-input-${featureId}`);
        console.log('Found SEO input element:', inputElement);
        if (inputElement) {
            inputElement.value = prompt;
            inputElement.focus();
            console.log('SEO Input value set to:', prompt);
        } else {
            console.error('SEO Input element not found for feature:', featureId);
        }
    }

    // Format SEO AI response for elegant display
    function formatSEOAIResponse(aiResult) {
        if (!aiResult) return 'SEO analiz sonucu alınamadı';
        
        // If HTML formatted, convert to clean formatted text
        if (aiResult.includes('<')) {
            // HTML'i temizle ama yapıyı koru
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = aiResult;
            
            // Paragrafları ve satır sonlarını koru
            const paragraphs = tempDiv.querySelectorAll('p');
            let cleanText = '';
            
            paragraphs.forEach(p => {
                const text = p.textContent || p.innerText || '';
                if (text.trim()) {
                    cleanText += text.trim() + '\n\n';
                }
            });
            
            // Eğer paragraf bulunamadıysa normal strip yap
            if (!cleanText.trim()) {
                cleanText = tempDiv.textContent || tempDiv.innerText || aiResult;
            }
            
            aiResult = cleanText.trim();
            // Şimdi normal formatlamaya devam et
        }
        
        // Clean up markdown and convert to SEO-friendly HTML
        let formatted = aiResult
            // Remove markdown headers and make them SEO-themed
            .replace(/^### (.*$)/gim, '<div class="fw-bold mb-2 mt-3" style="color: var(--seo-accent);">🎯 $1</div>')
            .replace(/^## (.*$)/gim, '<div class="h5 mb-2 mt-3" style="color: var(--seo-primary);">📊 $1</div>')
            .replace(/^# (.*$)/gim, '<div class="h4 mb-3 mt-3" style="color: var(--seo-secondary);">🚀 $1</div>')
            
            // Bold and italic with SEO colors
            .replace(/\*\*(.*?)\*\*/g, '<span class="fw-bold" style="color: var(--seo-accent);">$1</span>')
            .replace(/\*(.*?)\*/g, '<span class="fst-italic">$1</span>')
            
            // Convert ugly bullet points to SEO-themed format
            .replace(/^[\s]*[-•\*] (.+)$/gm, '<div class="d-flex align-items-start mb-2"><i class="fas fa-check-circle me-2 mt-1" style="color: var(--seo-primary);"></i><span>$1</span></div>')
            
            // Remove code blocks completely or make them simple
            .replace(/```[\s\S]*?```/g, '')
            .replace(/`([^`]+)`/g, '<span class="badge bg-light text-dark">$1</span>')
            
            // Clean up excessive line breaks
            .replace(/\n{3,}/g, '\n\n')
            .replace(/\n\n/g, '</p><p class="mb-3">')
            .replace(/\n/g, '<br>');
        
        // Wrap in paragraph if not already formatted
        if (!formatted.includes('<p>') && !formatted.includes('<div')) {
            formatted = '<p class="mb-3">' + formatted + '</p>';
        }
        
        // Add proper paragraph wrapper
        if (formatted.includes('<p>') && !formatted.startsWith('<p>')) {
            formatted = '<p class="mb-3">' + formatted;
        }
        if (formatted.includes('</p>') && !formatted.endsWith('</p>')) {
            formatted = formatted + '</p>';
        }
        
        return formatted;
    }
</script>
@endpush
@endsection