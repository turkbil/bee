@extends('admin.layout')

@include('ai::helper')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'AI Prowess & Skills')

@push('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.ai.index') }}">AI Module</a>
        </li>
        <li class="breadcrumb-item active">AI Prowess</li>
    </ol>
</nav>
@endpush

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .prowess-card {
        transition: all 0.3s ease;
        border: 1px solid var(--tblr-border-color);
        background: var(--tblr-card-bg);
        overflow: hidden;
        position: relative;
    }

    .prowess-card:hover {
        box-shadow: var(--tblr-box-shadow-lg);
        border-color: var(--tblr-primary);
    }

    .prowess-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .prowess-card:hover::before {
        opacity: 1;
    }

    .skill-icon {
        font-size: 3rem;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .category-header {
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-blue));
        color: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .skill-badge {
        background: var(--tblr-success);
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
    }

    .test-btn {
        background: linear-gradient(45deg, var(--tblr-primary), var(--tblr-purple));
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        transition: all 0.3s ease;
    }

    .test-btn:hover {
        box-shadow: var(--tblr-box-shadow);
        color: white;
    }

    .result-showcase {
        background: var(--tblr-card-bg);
        border: 1px solid var(--tblr-border-color);
        border-radius: 1rem;
        margin-top: 1.5rem;
        overflow: hidden;
    }

    .result-header {
        background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .result-content {
        padding: 1.5rem;
        line-height: 1.7;
        font-family: inherit;
        text-align: left;
    }

    .stats-showcase {
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-purple));
        color: white;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .btn-outline-primary {
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        /* Sabit duracak */
    }

    .fs-2 {
        font-size: 1.75rem !important;
    }

    /* Search & Navigation Styles */
    .skill-card {
        transition: all 0.3s ease;
    }

    .skill-card.hidden {
        display: none !important;
    }

    .category-section.no-results {
        opacity: 0.3;
    }

    .search-highlight {
        background: linear-gradient(120deg, #ffd700 0%, #ffd700 100%);
        background-size: 100% 0.2em;
        background-repeat: no-repeat;
        background-position: 0 88%;
        font-weight: 600;
    }

    #category-nav .btn {
        font-size: 0.85rem;
        padding: 0.375rem 0.75rem;
        margin-right: 0.25rem;
        border-radius: 1.5rem;
    }

    .category-nav-active {
        background: linear-gradient(45deg, var(--tblr-primary), var(--tblr-purple)) !important;
        color: white !important;
        border-color: var(--tblr-primary) !important;
    }

    html {
        scroll-behavior: smooth;
    }
    
    /* Hide unwanted elements */
    .hidden-field {
        display: none !important;
    }
    
    /* Accordion hover effects - dark/light uyumlu */
    .accordion-button:hover {
        background-color: var(--tblr-hover-bg, rgba(0, 0, 0, 0.075));
        color: var(--tblr-primary);
    }
    
    [data-bs-theme="dark"] .accordion-button:hover {
        background-color: var(--tblr-hover-bg, rgba(255, 255, 255, 0.05));
        color: var(--tblr-primary);
    }
    
    /* Choices.js styling fixes */
    .choices__inner {
        text-align: left !important;
    }

    .choices__list--single .choices__item {
        text-align: left !important;
    }

    .choices__list--dropdown .choices__item {
        text-align: left !important;
    }
</style>
@endpush

@section('content')
    <!-- Search & Navigation Section -->
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="skill-search" placeholder="{{ __('ai::admin.prowess.search_placeholder', ['default' => 'Search AI capabilities... (Name, description, category)']) }}" />
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-muted small mt-1">
                <span id="search-results-count"></span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <strong class="me-3">{{ __('ai::admin.prowess.quick_access', ['default' => 'Quick Access:']) }}</strong>
                        <div class="btn-group" role="group" id="category-nav">
                            <!-- JavaScript ile doldurulacak -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Dashboard -->
    <div class="row mb-5">
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold text-success">{{ count($features->flatten()) }}</div>
                    <p class="text-muted mb-0">{{ __('ai::admin.prowess.ai_skills') }}</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold text-info">{{ count($features) }}</div>
                    <p class="text-muted mb-0">{{ __('ai::admin.prowess.categories') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(empty($features))
    <!-- No Skills Available -->
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-img">
                    <i class="fas fa-robot fa-4x text-muted"></i>
                </div>
                <p class="empty-title h3">{{ __('ai::admin.prowess.no_skills_title') }}</p>
                <p class="empty-subtitle text-muted">
                    {{ __('ai::admin.prowess.no_skills_subtitle') }}
                </p>
                <div class="empty-action">
                    <a href="{{ route('admin.ai.features.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>{{ __('ai::admin.prowess.configure_skills') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- AI Skills Showcase -->
    @foreach($features as $category => $categoryFeatures)
    <div class="category-section mb-5" data-category="{{ $category }}" id="category-{{ Str::slug($category) }}">
        <div class="category-header">
            <h2 class="mb-0 position-relative">
                <i class="fas fa-magic me-3"></i>
                {{ $categoryNames[$category] ?? ucfirst($category) }}
                <span class="badge skill-badge ms-3">{{ __('ai::admin.prowess.skills_count', ['count' => count($categoryFeatures)]) }}</span>
            </h2>
            <p class="mb-0 mt-2 opacity-75">{{ __('ai::admin.prowess.unleash_power', ['category' => strtolower($categoryNames[$category] ?? $category), 'default' => 'Unleash the power of AI in ' . strtolower($categoryNames[$category] ?? $category)]) }}</p>
        </div>

        <div class="row">
            @foreach($categoryFeatures as $feature)
            <div class="col-6 mb-4 skill-card" 
                 data-skill-name="{{ strtolower($feature->name) }}" 
                 data-skill-description="{{ strtolower($feature->description) }}"
                 data-skill-category="{{ strtolower($categoryNames[$category] ?? $category) }}">
                <div class="card prowess-card h-100">
                    <div class="card-body text-center">
                        <!-- Skill Icon & Title -->
                        <div class="skill-icon">{{ $feature->emoji ?? '🤖' }}</div>
                        <h3 class="card-title fw-bold mb-3 fs-2">
                            <a href="{{ route('admin.ai.features.show', $feature->id) }}" class="text-decoration-none text-reset">
                                {{ $feature->name }}
                            </a>
                        </h3>

                        <!-- Description -->
                        <p class="text-muted mb-4 fs-5">{{ $feature->description }}</p>


                        <!-- Example Inputs -->
                        @if($feature->example_inputs)
                        <div class="mb-3">
                            <div class="text-muted small mb-2">{{ __('ai::admin.prowess.try_examples') }}</div>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @php
                                    $exampleInputs = is_string($feature->example_inputs) 
                                        ? json_decode($feature->example_inputs, true) ?? []
                                        : (is_array($feature->example_inputs) ? $feature->example_inputs : []);
                                @endphp
                                @foreach(array_slice($exampleInputs, 0, 3) as $example)
                                @php
                                    $exampleText = is_array($example) ? ($example['text'] ?? '') : $example;
                                    $exampleLabel = is_array($example) ? ($example['label'] ?? Str::limit($exampleText, 20)) : Str::limit($exampleText, 20);
                                @endphp
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="setExamplePrompt({{ $feature->id }}, '{{ addslashes($exampleText) }}'); console.log('Example clicked: {{ addslashes($exampleText) }}');">
                                    {{ $exampleLabel }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Universal Input System Integration via Livewire -->
                        @if($feature->slug === 'blog-yazisi-olusturucu' || strpos($feature->slug, 'blog') !== false)
                            @livewire('ai::admin.features.universal-input-component', ['featureId' => $feature->id])
                        @else
                        <!-- Standard Input for other features -->
                        <div class="mb-4">
                            <textarea id="input-{{ $feature->id }}" class="form-control form-control-lg" rows="3"
                                placeholder="{{ $feature->input_placeholder ?? __('ai::admin.prowess.enter_challenge') }}"></textarea>
                        </div>
                        @endif

                        <!-- Test Button for non-blog features -->
                        @if($feature->slug !== 'blog-yazisi-olusturucu' && strpos($feature->slug, 'blog') === false)
                        <button class="test-btn w-100 mb-3" onclick="console.log('Test button clicked for feature:', {{ $feature->id }}); testSkill({{ $feature->id }})"
                            id="btn-{{ $feature->id }}">
                            <span class="btn-text">
                                <i class="fas fa-magic me-2"></i>{{ __('ai::admin.prowess.experience_magic') }}
                            </span>
                            <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"
                                style="display: none;"></span>
                        </button>

                        <!-- Result Showcase for non-blog features -->
                        <div class="result-showcase" id="result-{{ $feature->id }}" style="display: none;">
                            <div class="result-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-sparkles"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('ai::admin.prowess.ai_result') }}</div>
                                        <small class="opacity-75" id="result-meta-{{ $feature->id }}">{{ __('ai::admin.prowess.processing_complete') }}
                                            complete</small>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-light" onclick="clearResult({{ $feature->id }})"
                                    title="{{ __('ai::admin.prowess.clear_result') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="result-content" id="result-content-{{ $feature->id }}"></div>
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer Stats -->
                    <div class="card-footer bg-light text-center">
                        <div class="row">
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.usage') }}</div>
                                <div class="fw-bold">{{ number_format($feature->usage_count ?? 0) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.rating') }}</div>
                                <div class="fw-bold">
                                    {{ ($feature->avg_rating ?? 0) > 0 ? number_format($feature->avg_rating, 1) : '-' }}
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.category') }}</div>
                                <div class="fw-bold">{{ $feature->getCategoryName() }}</div>
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

    // Search & Navigation System
    let allSkillCards = [];
    let allCategories = [];
    let activeCategory = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializeSearchAndNavigation();
        initializeAIProfiles();
        initializeChoicesJS();
        initializeContentLengthSliders();
    });

    function initializeSearchAndNavigation() {
        // Collect all skill cards and categories
        allSkillCards = document.querySelectorAll('.skill-card');
        allCategories = Array.from(document.querySelectorAll('.category-section')).map(section => ({
            id: section.id,
            name: section.dataset.category,
            displayName: section.querySelector('.category-header h2').textContent.trim(),
            element: section
        }));

        // Setup search functionality
        setupSearchFunctionality();
        
        // Setup category navigation
        setupCategoryNavigation();
        
        // Update search count
        updateSearchCount(allSkillCards.length, allSkillCards.length);
    }

    function initializeAIProfiles() {
        // Blog feature'larını bulup AI Profiles kontrolü yap
        const blogFeatures = document.querySelectorAll('[id*="blog-topic-"]');
        
        blogFeatures.forEach(element => {
            // Feature ID'sini al
            const featureId = element.id.replace('blog-topic-', '');
            
            // AI Profiles kontrolünü başlat
            console.log('Initializing AI Profiles for feature:', featureId);
            checkAIProfiles(featureId);
        });
        
        console.log(`AI Profiles initialization completed for ${blogFeatures.length} blog features`);
        
        // Initialize target audience select change handlers
        initializeTargetAudienceHandlers();
    }

    function setupSearchFunctionality() {
        const searchInput = document.getElementById('skill-search');
        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            performSearch(searchTerm);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = this.value.toLowerCase().trim();
                if (searchTerm) {
                    // İlk bulunan sonuca scroll
                    const firstVisible = document.querySelector('.skill-card:not(.hidden)');
                    if (firstVisible) {
                        firstVisible.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        });
    }

    function setupCategoryNavigation() {
        const navContainer = document.getElementById('category-nav');
        if (!navContainer || allCategories.length === 0) return;

        // "{{ __('ai::admin.prowess.all_categories', ['default' => 'All']) }}" butonu
        const allBtn = document.createElement('button');
        allBtn.className = 'btn btn-outline-primary category-nav-active';
        allBtn.textContent = '{{ __('ai::admin.prowess.all_categories', ['default' => 'All']) }}';
        allBtn.onclick = () => filterByCategory(null);
        navContainer.appendChild(allBtn);

        // Kategori butonları
        allCategories.forEach(category => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-primary';
            
            // Kategori adını temizle ve sadece skill sayısını ekle
            const cleanName = category.displayName.replace(/^\d+\s*/, '').replace(/\s*\d+.*$/, '');
            const skillCount = category.element.querySelectorAll('.skill-card').length;
            btn.textContent = `${cleanName} (${skillCount})`;
            
            btn.onclick = () => filterByCategory(category.id);
            navContainer.appendChild(btn);
        });
    }

    function performSearch(searchTerm) {
        let visibleCount = 0;
        let totalCount = allSkillCards.length;

        if (!searchTerm) {
            // Arama terimi yoksa tümünü göster
            allSkillCards.forEach(card => {
                card.classList.remove('hidden');
                removeHighlights(card);
                visibleCount++;
            });
            
            allCategories.forEach(category => {
                category.element.classList.remove('no-results');
            });
        } else {
            // Arama yap
            allSkillCards.forEach(card => {
                const name = card.dataset.skillName;
                const description = card.dataset.skillDescription;
                const category = card.dataset.skillCategory;
                
                const searchableText = `${name} ${description} ${category}`;
                
                if (searchableText.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    highlightSearchTerm(card, searchTerm);
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                    removeHighlights(card);
                }
            });

            // Kategorileri kontrol et - hiç görünür skill yoksa kategoriyi gizle
            allCategories.forEach(category => {
                const visibleSkillsInCategory = category.element.querySelectorAll('.skill-card:not(.hidden)').length;
                if (visibleSkillsInCategory === 0) {
                    category.element.classList.add('no-results');
                } else {
                    category.element.classList.remove('no-results');
                }
            });
        }

        updateSearchCount(visibleCount, totalCount);
    }

    function filterByCategory(categoryId) {
        activeCategory = categoryId;
        
        // Buton aktif durumlarını güncelle
        document.querySelectorAll('#category-nav .btn').forEach(btn => {
            btn.classList.remove('category-nav-active');
            btn.classList.add('btn-outline-primary');
        });

        if (categoryId) {
            // Belirli kategoriyi göster
            allCategories.forEach(category => {
                if (category.id === categoryId) {
                    category.element.style.display = 'block';
                    category.element.classList.remove('no-results');
                    
                    // Butonu aktif yap
                    const btn = Array.from(document.querySelectorAll('#category-nav .btn')).find(b => 
                        b.textContent.trim() === category.displayName.replace(/^\d+\s*/, '').replace(/\s*\d+$/, '')
                    );
                    if (btn) {
                        btn.classList.add('category-nav-active');
                        btn.classList.remove('btn-outline-primary');
                    }
                    
                    // Kategoriye scroll
                    setTimeout(() => {
                        category.element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                } else {
                    category.element.style.display = 'none';
                }
            });
        } else {
            // Tümünü göster
            allCategories.forEach(category => {
                category.element.style.display = 'block';
                category.element.classList.remove('no-results');
            });
            
            // "Tümü" butonunu aktif yap
            const allBtn = document.querySelector('#category-nav .btn');
            if (allBtn) {
                allBtn.classList.add('category-nav-active');
                allBtn.classList.remove('btn-outline-primary');
            }
        }

        // Arama sonuçlarını güncelle
        const visibleCards = document.querySelectorAll('.skill-card:not(.hidden)');
        updateSearchCount(visibleCards.length, allSkillCards.length);
    }

    function highlightSearchTerm(card, searchTerm) {
        const title = card.querySelector('.card-title');
        const description = card.querySelector('.text-muted');
        
        if (title) {
            const titleText = title.textContent;
            const highlightedTitle = titleText.replace(
                new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi'),
                '<span class="search-highlight">$1</span>'
            );
            title.innerHTML = highlightedTitle;
        }
        
        if (description) {
            const descText = description.textContent;
            const highlightedDesc = descText.replace(
                new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi'),
                '<span class="search-highlight">$1</span>'
            );
            description.innerHTML = highlightedDesc;
        }
    }

    function removeHighlights(card) {
        const highlights = card.querySelectorAll('.search-highlight');
        highlights.forEach(highlight => {
            highlight.outerHTML = highlight.textContent;
        });
    }

    function updateSearchCount(visible, total) {
        const countElement = document.getElementById('search-results-count');
        if (!countElement) return;

        if (visible === total) {
            countElement.textContent = `${total} {{ __('ai::admin.prowess.capabilities_showing', ['default' => 'AI capabilities showing']) }}`;
        } else {
            countElement.textContent = `${visible} / ${total} {{ __('ai::admin.prowess.capabilities_showing', ['default' => 'AI capabilities showing']) }}`;
        }
    }

    function clearSearch() {
        const searchInput = document.getElementById('skill-search');
        if (searchInput) {
            searchInput.value = '';
            performSearch('');
            searchInput.focus();
        }
    }

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Initialize Choices.js for all select elements with data-choices attribute
    function initializeChoicesJS() {
        const choicesElements = document.querySelectorAll('[data-choices]');
        
        choicesElements.forEach(element => {
            try {
                // Skip if already initialized
                if (element.classList.contains('choices__input')) {
                    return;
                }
                
                // Configuration based on element attributes
                const config = {
                    searchEnabled: false,
                    itemSelectText: '',
                    shouldSort: false,
                    removeItemButton: element.hasAttribute('multiple')
                };
                
                // Initialize Choices.js
                new Choices(element, config);
                
                console.log('Choices.js initialized for:', element.id);
                
            } catch (error) {
                console.error('Failed to initialize Choices.js for element:', element.id, error);
            }
        });
        
        console.log(`Choices.js initialization completed for ${choicesElements.length} elements`);
    }

// Test skill function
async function testSkill(featureId) {
    console.log('testSkill called for feature:', featureId);
    
    // Universal Input System için özel kontrol
    const blogTopicElement = document.getElementById(`blog-topic-${featureId}`);
    const inputElement = document.getElementById(`input-${featureId}`);
    const btnElement = document.getElementById(`btn-${featureId}`);
    const resultElement = document.getElementById(`result-${featureId}`);
    const resultContentElement = document.getElementById(`result-content-${featureId}`);
    const btnText = btnElement.querySelector('.btn-text');
    const loadingSpinner = btnElement.querySelector('.loading-spinner');
    
    let inputText = '';
    let universalInputData = {};
    
    // Blog writer için Universal Input System kullan
    if (blogTopicElement) {
        console.log('Using Universal Input System for blog writer');
        
        // Universal Input System verilerini topla (doğru fonksiyon)
        universalInputData = collectUniversalInputData(featureId);
        
        // Main input kontrolü
        if (!universalInputData.main_input) {
            alert('Lütfen blog konusunu girin!');
            blogTopicElement.focus();
            return;
        }
        
        // Get audience select element and selected values
        const audienceSelect = document.getElementById(`targetAudience-${featureId}`);
        const customAudienceText = document.getElementById(`customAudience-${featureId}`);
        
        let audience = '';
        if (audienceSelect && audienceSelect.selectedOptions.length > 0) {
            const selectedValues = Array.from(audienceSelect.selectedOptions).map(option => option.value);
            const selectedLabels = Array.from(audienceSelect.selectedOptions).map(option => option.text);
            
            // Check if "Diğer" is selected
            if (selectedValues.includes('diğer') && customAudienceText && customAudienceText.value.trim()) {
                // Replace "Diğer" with custom text
                const customText = customAudienceText.value.trim();
                const filteredLabels = selectedLabels.filter(label => label !== 'Diğer');
                filteredLabels.push(customText);
                audience = filteredLabels.join(', ');
            } else {
                audience = selectedLabels.join(', ');
            }
        }
        
        // Extract values from universalInputData
        const blogTopic = universalInputData.main_input;
        const useProfile = universalInputData.use_company_profile;
        
        if (!blogTopic) {
            alert('Lütfen blog konusunu girin!');
            blogTopicElement.focus();
            return;
        }
        
        // Ana input text'i oluştur
        const contentLengthLabels = {
            1: 'Çok Kısa',
            2: 'Kısa', 
            3: 'Normal',
            4: 'Uzun',
            5: 'Çok Detaylı'
        };
        
        const writingToneLabels = {
            90021: 'Profesyonel',
            90022: 'Samimi',
            90023: 'Eğitici', 
            90024: 'Eğlenceli',
            90025: 'Uzman'
        };
        
        const contentLengthLabel = contentLengthLabels[universalInputData.content_length] || 'Normal';
        const writingToneLabel = writingToneLabels[universalInputData.writing_tone] || 'Profesyonel';
        
        inputText = `Blog Konusu: ${blogTopic}
Yazım Tonu: ${writingToneLabel}
İçerik Uzunluğu: ${contentLengthLabel}${audience ? `
Hedef Kitle: ${audience}` : ''}${useProfile ? `
Şirket bilgilerini kullan: Evet` : ''}`;
        
    } else {
        // Standart input kullan
        inputText = inputElement ? inputElement.value.trim() : '';
        
        if (inputElement && !inputText) {
            alert('{{ addslashes(__('ai::admin.prowess.enter_challenge_alert')) }}');
            inputElement.focus();
            return;
        }
    }
    
    console.log('Elements found:', {
        inputElement: !!inputElement,
        blogTopicElement: !!blogTopicElement,
        btnElement: !!btnElement,
        resultElement: !!resultElement,
        resultContentElement: !!resultContentElement,
        btnText: !!btnText,
        loadingSpinner: !!loadingSpinner
    });

    // UI state - loading
    btnElement.disabled = true;
    btnText.innerHTML = '<i class="fas fa-cogs me-2"></i>{{ addslashes(__('ai::admin.prowess.ai_working')) }}';
    loadingSpinner.style.display = 'inline-block';
    resultElement.style.display = 'none';

    try {
        // API request body'sini hazırla
        const requestBody = {
            feature_id: featureId,
            input_text: inputText
        };
        
        // Universal Input System verilerini ekle
        if (Object.keys(universalInputData).length > 0) {
            requestBody.universal_inputs = universalInputData;
            console.log('Sending universal inputs:', universalInputData);
        }
        
        const response = await fetch('{{ route("admin.ai.test-feature") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        const data = await response.json();

        if (data.success) {
            // Update meta info
            const metaElement = document.getElementById(`result-meta-${featureId}`);
            metaElement.innerHTML = `
                <i class="fas fa-check-circle me-1"></i>
                ${data.tokens_used_formatted || (data.tokens_used + ' kullanıldı')}
                ${data.processing_time ? ` • ${data.processing_time}ms` : ''}
            `;
            
            // Update token display real-time
            if (data.new_balance_formatted !== undefined) {
                const tokenDisplay = document.getElementById('token-display');
                if (tokenDisplay) {
                    tokenDisplay.textContent = data.new_balance_formatted;
                }
            }
            
            // 🚀 WORD BUFFER SYSTEM ENTEGRASYONU
            resultElement.style.display = 'block';
            
            // Word buffer config kontrolü
            if (data.word_buffer_enabled && data.word_buffer_config && window.AIWordBuffer) {
                console.log('🎯 Using word buffer system for prowess result');
                
                // Word buffer ile display
                const buffer = new window.AIWordBuffer(resultContentElement, {
                    wordDelay: data.word_buffer_config.delay_between_words || 200,
                    fadeEffect: true,
                    enableMarkdown: true,
                    scrollCallback: () => {
                        // Smooth scroll to result
                        resultElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                
                // Buffer'ı başlat
                buffer.start();
                
                // Content'i ekle (formatlanmış AI yanıtı)
                const formattedResponse = formatAIResponse(data.response || data.ai_result || 'No result received');
                buffer.addContent(formattedResponse);
                
                // Showcase mode efektleri
                setTimeout(() => {
                    buffer.flush();
                    
                    // Prowess showcase için özel glow efekti
                    if (data.word_buffer_config.showcase_mode) {
                        resultElement.style.transition = 'all 0.3s ease';
                        resultElement.style.boxShadow = '0 4px 12px rgba(0,123,255,0.3)';
                        setTimeout(() => {
                            resultElement.style.boxShadow = '';
                        }, 2000);
                    }
                }, 100);
                
            } else {
                // Normal display (fallback)
                console.log('💭 Using normal display for prowess result');
                resultContentElement.innerHTML = formatAIResponse(data.response || data.ai_result || 'No result received');
            }
        } else {
            // Error state
            resultContentElement.innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ __('ai::admin.prowess.error_occurred', ['default' => 'Error']) }}: ${data.message || '{{ __('ai::admin.prowess.unknown_error', ['default' => 'Unknown error occurred']) }}'}
                </div>
            `;
            resultElement.style.display = 'block';
        }

    } catch (error) {
        console.error('Skill test error:', error);
        resultContentElement.innerHTML = `
            <div class="text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                {{ __('ai::admin.prowess.connection_error', ['default' => 'Connection error']) }}: ${error.message}
            </div>
        `;
        resultElement.style.display = 'block';
    } finally {
        // Reset UI state
        btnElement.disabled = false;
        btnText.innerHTML = '<i class="fas fa-magic me-2"></i>{{ addslashes(__('ai::admin.prowess.experience_magic')) }}';
        loadingSpinner.style.display = 'none';
    }
}

// Clear result function
function clearResult(featureId) {
    const resultElement = document.getElementById(`result-${featureId}`);
    resultElement.style.display = 'none';
}

// Set example prompt
function setExamplePrompt(featureId, prompt) {
    console.log('setExamplePrompt called:', { featureId, prompt });
    const inputElement = document.getElementById(`input-${featureId}`);
    console.log('Found input element:', inputElement);
    if (inputElement) {
        inputElement.value = prompt;
        inputElement.focus();
        console.log('Input value set to:', prompt);
    } else {
        console.error('Input element not found for feature:', featureId);
    }
}

// AI Profiles global storage
window.aiProfilesData = {};

// Check AI Profiles availability for a feature
async function checkAIProfiles(featureId) {
    const statusElement = document.getElementById(`profile-status-${featureId}`);
    const checkboxElement = document.getElementById(`use-profile-${featureId}`);
    
    if (!statusElement || !checkboxElement) {
        console.warn('AI Profiles elements not found for feature:', featureId);
        return;
    }
    
    try {
        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Şirket profili kontrol ediliyor...';
        
        const response = await fetch('/admin/ai/api/profiles/company-info', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.available) {
            // Şirket profili mevcut
            statusElement.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Şirket profili hazır';
            statusElement.classList.add('text-success');
            statusElement.classList.remove('text-muted');
            checkboxElement.disabled = false;
            
            // Store profile data for later use
            window.aiProfilesData[featureId] = data.profile_data;
            
            console.log('AI Profiles available for feature:', featureId, data);
        } else {
            // Şirket profili yok
            statusElement.innerHTML = `<i class="fas fa-exclamation-triangle text-warning me-1"></i>${data.message || 'Şirket profili bulunamadı'}`;
            statusElement.classList.add('text-warning');
            statusElement.classList.remove('text-muted');
            checkboxElement.disabled = true;
            
            // Add setup link if available
            if (data.setup_url) {
                statusElement.innerHTML += ` <a href="${data.setup_url}" class="text-warning text-decoration-underline">Kurulum</a>`;
            }
            
            console.log('AI Profiles not available for feature:', featureId, data);
        }
        
    } catch (error) {
        console.error('AI Profiles check failed:', error);
        statusElement.innerHTML = '<i class="fas fa-exclamation-circle text-danger me-1"></i>Bağlantı hatası';
        statusElement.classList.add('text-danger');
        statusElement.classList.remove('text-muted');
        checkboxElement.disabled = true;
    }
}

// Collect Universal Input System data for blog features
function collectUniversalInputData(featureId) {
    const universalData = {};
    
    console.log('🔍 Collecting universal inputs for feature:', featureId);
    
    // Blog topic (main input)
    const topicElement = document.getElementById(`blog-topic-${featureId}`);
    if (topicElement && topicElement.value.trim()) {
        universalData.main_input = topicElement.value.trim();
        console.log('✓ Blog topic found:', universalData.main_input);
    }
    
    // Writing tone (CORRECT ID: writingTone-{featureId})
    const writingToneElement = document.getElementById(`writingTone-${featureId}`);
    if (writingToneElement && writingToneElement.value) {
        universalData.writing_tone = writingToneElement.value;
        console.log('✓ Writing tone found:', universalData.writing_tone);
    }
    
    // Content length (CORRECT ID: contentLength-{featureId})
    const contentLengthElement = document.getElementById(`contentLength-${featureId}`);
    if (contentLengthElement && contentLengthElement.value) {
        universalData.content_length = parseInt(contentLengthElement.value);
        console.log('✓ Content length found:', universalData.content_length);
    }
    
    // Target audience (CORRECT ID: targetAudience-{featureId})
    const targetAudienceElement = document.getElementById(`targetAudience-${featureId}`);
    if (targetAudienceElement && targetAudienceElement.value.trim()) {
        universalData.target_audience = targetAudienceElement.value.trim();
        console.log('✓ Target audience found:', universalData.target_audience);
    }
    
    // Company profile usage (CORRECT ID: useCompanyProfile-{featureId})
    const companyProfileCheckbox = document.getElementById(`useCompanyProfile-${featureId}`);
    if (companyProfileCheckbox && companyProfileCheckbox.checked) {
        universalData.use_company_profile = true;
        console.log('✓ Company profile enabled');
    }
    
    console.log('📦 Final universal data collected:', universalData);
    return universalData;
}

// Format AI response for elegant display
function formatAIResponse(aiResult) {
    if (!aiResult) return '{{ __('ai::admin.prowess.no_result_available', ['default' => 'No result available']) }}';
    
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
    
    // Clean up markdown and convert to elegant HTML
    let formatted = aiResult
        // Remove markdown headers and make them elegant
        .replace(/^### (.*$)/gim, '<div class="fw-bold text-primary mb-2 mt-3">$1</div>')
        .replace(/^## (.*$)/gim, '<div class="h5 text-primary mb-2 mt-3">$1</div>')
        .replace(/^# (.*$)/gim, '<div class="h4 text-primary mb-3 mt-3">$1</div>')
        
        // Bold and italic
        .replace(/\*\*(.*?)\*\*/g, '<span class="fw-bold text-dark">$1</span>')
        .replace(/\*(.*?)\*/g, '<span class="fst-italic">$1</span>')
        
        // Convert ugly bullet points to elegant format
        .replace(/^[\s]*[-•\*] (.+)$/gm, '<div class="d-flex align-items-start mb-2"><i class="fas fa-check-circle text-success me-2 mt-1"></i><span>$1</span></div>')
        
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

// Initialize target audience select change handlers
function initializeTargetAudienceHandlers() {
    // Find all blog features and add event listeners
    const blogFeatures = document.querySelectorAll('[id*="blog-topic-"]');
    
    blogFeatures.forEach(element => {
        const featureId = element.id.replace('blog-topic-', '');
        const audienceSelect = document.getElementById(`targetAudience-${featureId}`);
        const customAudienceContainer = document.getElementById(`customAudienceContainer-${featureId}`);
        
        if (audienceSelect && customAudienceContainer) {
            audienceSelect.addEventListener('change', function() {
                handleTargetAudienceChange(featureId);
            });
            
            console.log(`Target audience handler initialized for feature: ${featureId}`);
        }
    });
}

// Handle target audience select change
function handleTargetAudienceChange(featureId) {
    const audienceSelect = document.getElementById(`targetAudience-${featureId}`);
    const customAudienceContainer = document.getElementById(`customAudienceContainer-${featureId}`);
    
    if (!audienceSelect || !customAudienceContainer) {
        return;
    }
    
    // Show/hide custom input based on "custom" selection
    if (audienceSelect.value === 'custom') {
        customAudienceContainer.style.display = 'block';
        // Focus on custom input after a short delay
        setTimeout(() => {
            const customInput = document.getElementById(`customAudience-${featureId}`);
            if (customInput) {
                customInput.focus();
            }
        }, 100);
    } else {
        customAudienceContainer.style.display = 'none';
        // Clear custom input value
        const customInput = document.getElementById(`customAudience-${featureId}`);
        if (customInput) {
            customInput.value = '';
        }
    }
}

// Initialize content length sliders
function initializeContentLengthSliders() {
    // Find all content length sliders
    const sliders = document.querySelectorAll('[id*="contentLength-"]');
    
    sliders.forEach(slider => {
        // Extract feature ID from slider ID
        const featureId = slider.id.replace('contentLength-', '');
        const badge = document.getElementById(`contentLengthDisplay-${featureId}`);
        const labels = Array.from(document.querySelectorAll('.range-label')).map(label => label.textContent.trim());
        
        if (!badge || labels.length === 0) return;
        
        // Set initial badge text
        const initialValue = parseInt(slider.value);
        if (labels[initialValue - 1]) {
            badge.textContent = labels[initialValue - 1];
        }
        
        // Add event listener for slider changes
        slider.addEventListener('input', function() {
            const value = parseInt(this.value);
            const labelIndex = value - 1;
            
            if (labels[labelIndex]) {
                badge.textContent = labels[labelIndex];
            }
            
            // Update active range label styling
            document.querySelectorAll('.range-label').forEach((label, index) => {
                if (index === labelIndex) {
                    label.style.fontWeight = 'bold';
                    label.style.color = 'var(--tblr-primary)';
                } else {
                    label.style.fontWeight = 'normal';
                    label.style.color = '';
                }
            });
        });
        
        console.log(`Content length slider initialized for feature: ${featureId}`);
    });
}
</script>
@endpush
@endsection