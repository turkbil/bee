<div>
    <div class="row row-deck row-cards" id="dashboard-cards" style="opacity: 0; transition: opacity 0.3s ease;">
        
        {{-- First Row - Main Statistics (4 cards) --}}
        @if(in_array('ai', $activeModules))
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center drag-handle">
                        <div class="subheader">{{ __('ai::admin.dashboard.ai_profile') }}</div>
                        <div class="ms-auto lh-1">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('ai::admin.dashboard.details') }}</a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.ai.profile.show') }}">{{ __('ai::admin.view_profile') }}</a>
                                    <a class="dropdown-item" href="{{ route('admin.ai.profile.edit', 1) }}">{{ __('ai::admin.edit_profile') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($aiProfile)
                        <div class="h1 mb-3">{{ $aiCompletionPercentage }}%</div>
                        <div class="d-flex mb-2">
                            <div>{{ __('ai::admin.dashboard.completion_rate') }}</div>
                            <div class="ms-auto">
                                <span class="text-{{ $aiIsCompleted ? 'green' : 'yellow' }} d-inline-flex align-items-center lh-1">
                                    {{ $aiIsCompleted ? '✓' : '⏳' }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon ms-1 icon-2">
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-{{ $aiIsCompleted ? 'success' : 'primary' }}" style="width: {{ $aiCompletionPercentage }}%" role="progressbar" aria-valuenow="{{ $aiCompletionPercentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $aiCompletionPercentage }}% Complete">
                                <span class="visually-hidden">{{ $aiCompletionPercentage }}% Complete</span>
                            </div>
                        </div>
                    @else
                        <div class="h1 mb-3">0%</div>
                        <div class="d-flex mb-2">
                            <div>{{ __('ai::admin.dashboard.no_profile') }}</div>
                            <div class="ms-auto">
                                <span class="text-red d-inline-flex align-items-center lh-1">
                                    {{ __('ai::admin.dashboard.setup_needed') }}
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-muted" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span class="visually-hidden">0% Complete</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(in_array('ai', $activeModules))
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center drag-handle">
                        <div class="subheader">AI Kredileri</div>
                        <div class="ms-auto lh-1">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manage</a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.ai.credits.index') }}">Kredi Yönetimi</a>
                                    <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">Usage History</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $remainingTokensFormatted }}</div>
                    <div class="d-flex mb-2">
                        <div>Kalan Krediler</div>
                        <div class="ms-auto">
                            <span class="text-{{ $statusColor }} d-inline-flex align-items-center lh-1">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-{{ $statusColor }}" style="width: {{ 100 - ($usagePercentage ?? 0) }}%" role="progressbar" aria-valuenow="{{ 100 - ($usagePercentage ?? 0) }}" aria-valuemin="0" aria-valuemax="100">
                            <span class="visually-hidden">{{ 100 - ($usagePercentage ?? 0) }}% Remaining</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('page', $activeModules))
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center drag-handle">
                        <div class="subheader">Pages</div>
                        <div class="ms-auto lh-1">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manage</a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.page.index') }}">All Pages</a>
                                    <a class="dropdown-item" href="{{ route('admin.page.manage') }}">Create Page</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalPages }}</div>
                    <div class="d-flex mb-2">
                        <div>{{ __('page::admin.total_pages') }}</div>
                        <div class="ms-auto">
                            <span class="text-blue d-inline-flex align-items-center lh-1">
                                {{ __('ai::admin.dashboard.active') }}
                            </span>
                        </div>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width: {{ min(100, ($totalPages ?? 0) * 10) }}%" role="progressbar">
                            <span class="visually-hidden">{{ $totalPages ?? 0 }} pages</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('portfolio', $activeModules))
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center drag-handle">
                        <div class="subheader">Portfolio</div>
                        <div class="ms-auto lh-1">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manage</a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">All Portfolios</a>
                                    <a class="dropdown-item" href="{{ route('admin.portfolio.manage') }}">Create Portfolio</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalPortfolios }}</div>
                    <div class="d-flex mb-2">
                        <div>{{ __('portfolio::admin.total_portfolios') }}</div>
                        <div class="ms-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                {{ __('ai::admin.dashboard.active') }}
                            </span>
                        </div>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: {{ min(100, ($totalPortfolios ?? 0) * 15) }}%" role="progressbar">
                            <span class="visually-hidden">{{ $totalPortfolios ?? 0 }} portfolios</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Second Row - Charts and Stats (3 equal columns) --}}
        @if(in_array('ai', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('ai::admin.dashboard.activity_overview') }}</h3>
                        <div class="dropdown">
                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('ai::admin.dashboard.this_month') }}</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Bu Hafta</a>
                                <a class="dropdown-item" href="#">Bu Ay</a>
                                <a class="dropdown-item" href="#">Bu Yıl</a>
                            </div>
                        </div>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="chart-mentions"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('ai', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('ai::admin.dashboard.performance') }}</h3>
                        <span class="badge bg-success">+12%</span>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="chart-performance"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Third Row - AI Chat --}}
        @if(in_array('ai', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('ai::admin.dashboard.quick_ai_chat') }}</h3>
                        <a href="{{ route('admin.ai.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-expand me-1"></i>
                            {{ __('ai::admin.dashboard.full_screen') }}
                        </a>
                    </div>
                    
                    <div class="dashboard-chat-container">
                        <div id="dashboard-chat-messages" class="chat-messages"></div>
                        <div class="chat-input-container">
                            <div class="d-flex">
                                <textarea id="dashboard-chat-input" class="form-control" rows="2" placeholder="{{ __('ai::admin.dashboard.chat_placeholder') }}" style="resize: none;"></textarea>
                                <button id="dashboard-chat-send" class="btn btn-primary ms-2">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Token: {{ $remainingTokens ?? 0 }} {{ __('ai::admin.dashboard.tokens_left') }}</small>
                                <div id="dashboard-chat-status" class="text-muted"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Fourth Row - Recent Items (3 equal columns) --}}
        @if(in_array('page', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('page::admin.recent_pages') }}</h3>
                        <a href="{{ route('admin.page.index') }}" class="btn btn-sm btn-outline-primary">{{ __('page::admin.view_all') }}</a>
                    </div>
                    @if($recentPages && count($recentPages) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPages as $page)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ is_array($page->title) ? ($page->title[app()->getLocale()] ?? array_first($page->title)) : $page->title }}</div>
                                    <small class="text-muted">{{ $page->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $page->is_active ? 'success' : 'secondary' }} rounded-pill">
                                    {{ $page->is_active ? __('ai::admin.dashboard.active') : __('ai::admin.dashboard.draft') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('page::admin.no_pages_yet') }}</p>
                            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary btn-sm">{{ __('page::admin.create_first_page') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(in_array('portfolio', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('portfolio::admin.recent_portfolios') }}</h3>
                        <a href="{{ route('admin.portfolio.index') }}" class="btn btn-sm btn-outline-primary">{{ __('portfolio::admin.view_all') }}</a>
                    </div>
                    @if($recentPortfolios && count($recentPortfolios) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPortfolios as $portfolio)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ is_array($portfolio->title) ? ($portfolio->title[app()->getLocale()] ?? array_first($portfolio->title)) : $portfolio->title }}</div>
                                    <small class="text-muted">{{ $portfolio->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $portfolio->is_active ? 'success' : 'secondary' }} rounded-pill">
                                    {{ $portfolio->is_active ? __('ai::admin.dashboard.active') : __('ai::admin.dashboard.draft') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('portfolio::admin.no_portfolios_yet') }}</p>
                            <a href="{{ route('admin.portfolio.manage') }}" class="btn btn-primary btn-sm">{{ __('portfolio::admin.create_first_portfolio') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(in_array('announcement', $activeModules))
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">Duyurular</h3>
                        <span class="badge bg-primary">{{ $totalAnnouncements ?? 0 }}</span>
                    </div>
                    @if($recentAnnouncements && count($recentAnnouncements) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentAnnouncements as $announcement)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ is_array($announcement->title) ? ($announcement->title[app()->getLocale()] ?? array_first($announcement->title)) : $announcement->title }}</div>
                                    <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $announcement->is_active ? 'success' : 'secondary' }} rounded-pill">
                                    {{ $announcement->is_active ? __('ai::admin.dashboard.active') : __('ai::admin.dashboard.draft') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Henüz duyuru yok</p>
                            <a href="{{ route('admin.announcement.manage') }}" class="btn btn-primary btn-sm">İlk Duyuru Oluştur</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@push('styles')
<style>
.dashboard-chat-container {
    height: 250px;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.375rem;
    overflow: hidden;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background-color: var(--bs-body-bg);
}

.chat-input-container {
    padding: 1rem;
    background-color: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
}

.user-message-compact {
    background-color: #206bc4;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    margin-bottom: 0.5rem;
    margin-left: 2rem;
    text-align: right;
}

.ai-message-compact {
    background-color: var(--bs-secondary-bg);
    color: var(--bs-body-color);
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    margin-bottom: 0.5rem;
    margin-right: 2rem;
}

.drag-handle {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.4;
}

.sortable-chosen {
    transform: scale(1.02);
}

.sortable-drag {
    transform: rotate(2deg);
}

#dashboard-cards {
    min-height: 400px;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
}

.dashboard-toast {
    background-color: var(--bs-primary);
    color: var(--bs-white);
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<!-- Sortable Library -->
<script src="/admin-assets/libs/sortable/sortable.min.js"></script>
<!-- Chart.js - Local version to avoid source map errors -->
<script src="/admin-assets/libs/apexcharts/dist/apexcharts.min.js"></script>

<script>
// Global variables
let dashboardConversationId = null;
let dashboardEventSource = null;
let dashboardWordBuffer = null;

// Dashboard sortable initialization
$(document).ready(function() {
    console.log('🚀 Dashboard sortable initializing...');
    
    const dashboardContainer = document.getElementById('dashboard-cards');
    if (!dashboardContainer) {
        console.error('❌ Dashboard container not found');
        return;
    }

    // Initialize Sortable
    window.dashboardSortable = Sortable.create(dashboardContainer, {
        animation: 250,
        delay: 50,
        delayOnTouchOnly: true,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        forceFallback: false,
        fallbackClass: 'sortable-fallback',
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        
        onStart: function(evt) {
            document.body.style.cursor = 'grabbing';
            document.body.classList.add('sortable-grabbing');
            $(evt.item).addClass('dragging');
        },
        
        onEnd: function(evt) {
            document.body.style.cursor = 'default';
            document.body.classList.remove('sortable-grabbing');
            $(evt.item).removeClass('dragging');
            
            // Save layout
            saveDashboardLayout();
        }
    });

    // Show dashboard with animation
    setTimeout(function() {
        dashboardContainer.style.opacity = '1';
        console.log('✅ Dashboard visible');
    }, 100);

    // Initialize charts
    setTimeout(initializeCharts, 300);
});

// Chart initialization with Chart.js
function initializeCharts() {
    if (!window.Chart) {
        console.warn('⚠️ Chart.js not loaded, retrying...');
        setTimeout(initializeCharts, 500);
        return;
    }
    
    setTimeout(function() {
        console.log('📊 Initializing charts with Chart.js...');
        
        // Activity chart
        const mentionsElement = document.getElementById('chart-mentions');
        if (mentionsElement) {
            try {
                const mentionsData = [{{ count($recentLogins ?? []) }}, {{ count($newUsers ?? []) }}, {{ $totalPages ?? 0 }}, {{ $totalPortfolios ?? 0 }}, {{ $totalAnnouncements ?? 0 }}];
                
                window.mentionsChart = new Chart(mentionsElement, {
                    type: 'line',
                    data: {
                        labels: ['Logins', 'New Users', 'Pages', 'Portfolio', 'Announcements'],
                        datasets: [{
                            label: 'Activity',
                            data: mentionsData,
                            borderColor: '#206bc4',
                            backgroundColor: 'rgba(32, 107, 196, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e9ecef'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                
                console.log('✅ Activity chart rendered');
            } catch (e) {
                console.error('❌ Activity chart error:', e);
            }
        }

        // Performance chart
        const performanceElement = document.getElementById('chart-performance');
        if (performanceElement) {
            try {
                const performanceData = [65, 59, 80, 81, 56, 55, 40];
                
                window.performanceChart = new Chart(performanceElement, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Performance',
                            data: performanceData,
                            borderColor: '#2fb344',
                            backgroundColor: 'rgba(47, 179, 68, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e9ecef'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                
                console.log('✅ Performance chart rendered');
            } catch (e) {
                console.error('❌ Performance chart error:', e);
            }
        }

        console.log('📊 All charts initialized');
    }, 300);
}

// Save dashboard layout
function saveDashboardLayout() {
    try {
        const layout = [];
        $('#dashboard-cards .col-12, #dashboard-cards .col-sm-6, #dashboard-cards .col-lg-3, #dashboard-cards .col-lg-4, #dashboard-cards .col-lg-6, #dashboard-cards .col-lg-8').each(function(index) {
            layout.push($(this).attr('class'));
        });
        
        localStorage.setItem('dashboard_layout', JSON.stringify(layout));
        console.log('✅ Dashboard layout saved:', layout);
        
        // Also save to server
        fetch('/admin/dashboard/save-layout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ layout: layout })
        }).catch(e => console.error('❌ Server save error:', e));
    } catch (e) {
        console.error('❌ Dashboard layout save error:', e);
    }
}

// AI Chat Functions
function sendDashboardAIMessage() {
    const input = document.getElementById('dashboard-chat-input');
    const message = input.value.trim();
    
    if (!message) {
        showDashboardToast('Lütfen bir mesaj yazın');
        return;
    }
    
    // Add user message
    addDashboardMessage('user', message);
    input.value = '';
    
    // Show AI thinking
    const aiMessageId = addDashboardMessage('ai', '');
    const aiMessageElement = document.getElementById(aiMessageId);
    
    // Initialize conversation
    if (!dashboardConversationId) {
        dashboardConversationId = 'dashboard_' + Date.now();
    }
    
    // Setup Server-Sent Events
    const eventSource = new EventSource(`/admin/ai/stream?message=${encodeURIComponent(message)}&conversation_id=${dashboardConversationId}`);
    dashboardEventSource = eventSource;
    
    // Initialize word buffer
    dashboardWordBuffer = createAIWordBuffer(aiMessageElement);
    
    eventSource.onmessage = function(event) {
        try {
            const data = JSON.parse(event.data);
            
            if (data.type === 'content') {
                dashboardWordBuffer.addContent(data.content);
            } else if (data.type === 'done') {
                dashboardWordBuffer.finalize();
                eventSource.close();
                dashboardEventSource = null;
            } else if (data.type === 'error') {
                aiMessageElement.innerHTML = `<div class="text-danger">Error: ${data.message}</div>`;
                eventSource.close();
                dashboardEventSource = null;
            }
        } catch (e) {
            console.error('Stream parsing error:', e);
        }
    };
    
    eventSource.onerror = function(event) {
        console.error('EventSource error:', event);
        aiMessageElement.innerHTML = '<div class="text-danger">Connection error occurred.</div>';
        eventSource.close();
        dashboardEventSource = null;
    };
}

function addDashboardMessage(type, content) {
    const messagesContainer = document.getElementById('dashboard-chat-messages');
    const messageId = 'message_' + Date.now();
    
    const messageDiv = document.createElement('div');
    messageDiv.id = messageId;
    messageDiv.className = type === 'user' ? 'user-message-compact' : 'ai-message-compact';
    messageDiv.innerHTML = content;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    return messageId;
}

function createAIWordBuffer(element) {
    let buffer = '';
    let isProcessing = false;
    
    return {
        addContent: function(content) {
            buffer += content;
            if (!isProcessing) {
                this.processBuffer();
            }
        },
        
        processBuffer: function() {
            if (buffer.length === 0) return;
            
            isProcessing = true;
            const words = buffer.split(/(\s+)/);
            buffer = '';
            
            let currentContent = element.innerHTML;
            let wordIndex = 0;
            
            const processWord = () => {
                if (wordIndex < words.length) {
                    currentContent += words[wordIndex];
                    element.innerHTML = currentContent;
                    
                    // Auto-scroll
                    const messagesContainer = document.getElementById('dashboard-chat-messages');
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    
                    wordIndex++;
                    setTimeout(processWord, 50);
                } else {
                    isProcessing = false;
                    if (buffer.length > 0) {
                        this.processBuffer();
                    }
                }
            };
            
            processWord();
        },
        
        finalize: function() {
            if (buffer.length > 0) {
                element.innerHTML += buffer;
                buffer = '';
            }
            isProcessing = false;
        }
    };
}

function showDashboardToast(message) {
    const toast = document.createElement('div');
    toast.className = 'dashboard-toast';
    toast.textContent = message;
    
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // AI Chat send button
    const sendButton = document.getElementById('dashboard-chat-send');
    if (sendButton) {
        sendButton.addEventListener('click', sendDashboardAIMessage);
    }
    
    // AI Chat input enter key
    const chatInput = document.getElementById('dashboard-chat-input');
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendDashboardAIMessage();
            }
        });
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (dashboardEventSource) {
        dashboardEventSource.close();
    }
});
</script>
@endpush