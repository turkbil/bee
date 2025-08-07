<div>
    {{-- Modern Welcome Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #206bc4 0%, #1d4ed8 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M12 1l3 6l6 3l-6 3l-3 6l-3 -6l-6 -3l6 -3z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="page-title mb-1">
                                {{ __('admin.dashboard_welcome') }}
                            </h2>
                            <div>
                                {{ __('admin.welcome') }}, <strong>{{ auth()->user()->name }}</strong>! {{ tenant('title') ?? config('app.name') }} {{ __('admin.dashboard_subtitle') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.page.manage') }}" class="btn btn-outline-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            {{ __('admin.create_content') }}
                        </a>
                        <button class="btn btn-primary d-none d-sm-inline-block" onclick="refreshDashboard()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                            </svg>
                            {{ __('admin.refresh') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Content --}}
    <div class="page-body">
        <div class="container-xl">
            
            {{-- Modern Stats Cards Row --}}
            <div class="row row-deck row-cards mb-4">
                {{-- 🎯 EN ÖNEMLİ KART: KALAN KREDİ --}}
                @if(function_exists('ai_get_credit_balance'))
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm border-warning">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                        💰
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-warning">
                                        {{ format_credit(ai_get_credit_balance()) }}
                                    </div>
                                    <div class="small">
                                        💰 Mevcut Bakiye
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- System Status Card --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ __('admin.system_online') }}
                                    </div>
                                    <div class="small">
                                        {{ __('admin.account_status') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Role Card --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ auth()->user()->roles->first()->name ?? __('admin.admin') }}
                                    </div>
                                    <div class="small">
                                        {{ __('admin.user_role') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(in_array('page', $activeModules))
                {{-- Pages Count --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalPages ?? 0 }}
                                    </div>
                                    <div class="small">
                                        {{ __('admin.pages') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('portfolio', $activeModules))
                {{-- Portfolio Count --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalPortfolios ?? 0 }}
                                    </div>
                                    <div class="small">
                                        {{ __('admin.portfolio') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Main Content Row --}}
            <div class="row row-deck row-cards">
                {{-- Quick Actions Card --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('admin.quick_actions') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Create New Page --}}
                                @if(in_array('page', $activeModules))
                                <div class="col-md-6">
                                    <a href="{{ route('admin.page.manage') }}" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">{{ __('admin.create') }} {{ __('admin.pages') }}</div>
                                                    <div>{{ __('admin.create') }} yeni sayfa</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif

                                {{-- Create Portfolio --}}
                                @if(in_array('portfolio', $activeModules))
                                <div class="col-md-6">
                                    <a href="{{ route('admin.portfolio.manage') }}" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">{{ __('admin.create') }} Portfolio</div>
                                                    <div>Yeni portfolio projesi</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif

                                {{-- Create Announcement --}}
                                @if(in_array('announcement', $activeModules))
                                <div class="col-md-6">
                                    <a href="{{ route('admin.announcement.manage') }}" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M18 8a3 3 0 0 1 0 6" />
                                                            <path d="M10 8v11a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-5" />
                                                            <path d="M12 8h0l4.524 -3.77a.9 .9 0 0 1 1.476 .692v12.156a.9 .9 0 0 1 -1.476 .692l-4.524 -3.77h-8a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h8" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">{{ __('admin.create') }} Duyuru</div>
                                                    <div>Yeni duyuru oluştur</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif

                                {{-- AI Features --}}
                                @if(in_array('ai', $activeModules))
                                <div class="col-md-6">
                                    <a href="{{ route('admin.ai.index') }}" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M8 9h8" />
                                                            <path d="M8 13h6" />
                                                            <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">AI {{ __('admin.assistant') }}</div>
                                                    <div>Yapay zeka yardımcısı</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif

                                {{-- System Settings --}}
                                <div class="col-md-6">
                                    <a href="{{ route('admin.dashboard') }}" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c.996 .608 2.296 .07 2.572 -1.065z" />
                                                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">{{ __('admin.system_settings') }}</div>
                                                    <div>Dil ve sistem ayarları</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                {{-- Cache Clear --}}
                                <div class="col-md-6">
                                    <a href="#" onclick="clearSystemCache()" class="card card-link">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium">{{ __('admin.clear_cache') }}</div>
                                                    <div>Sistem önbelleğini temizle</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            {{-- AI Sohbet Widget --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M8 9h8" />
                                                    <path d="M8 13h6" />
                                                    <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                                </svg>
                                                AI Asistan
                                            </h3>
                                            <span class="badge">Hızlı Sohbet</span>
                                        </div>
                                        <div class="card-body">
                                            {{-- Chat Messages --}}
                                            <div class="ai-chat-widget-messages border rounded" id="aiChatWidgetMessages" style="min-height: 300px; max-height: 400px; overflow-y: auto; padding: 20px; margin-bottom: 20px;">
                                                <div class="ai-message-widget assistant mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar avatar-sm me-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                                <path d="M12 1l3 6l6 3l-6 3l-3 6l-3 -6l-6 -3l6 -3z" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <div class="card p-3 mb-2">
                                                                <div>Selam! Nasıl yardımcı olabilirim?</div>
                                                            </div>
                                                            <small>Az önce</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Quick Action Buttons --}}
                                            <div class="mb-4">
                                                <div class="row g-2">
                                                </div>
                                            </div>

                                            {{-- Chat Input --}}
                                            <div class="input-group input-group-lg">
                                                <input type="text" 
                                                       class="form-control" 
                                                       placeholder="Mesaj yazın..."
                                                       id="aiChatWidgetInput"
                                                       wire:model.live="aiChatMessage"
                                                       wire:keydown.enter.prevent="sendAiMessage">
                                                <button class="btn" type="button" wire:click="sendAiMessage">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 14l11 -11" />
                                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity & Info --}}
                <div class="col-lg-4">
                    <div class="row row-cards">
                        {{-- Account Info --}}
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('admin.account_summary') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-muted">{{ __('admin.user_id') }}</div>
                                                <div class="col-auto">
                                                    <span class="badge">#{{ auth()->user()->id }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-muted">{{ __('admin.email') }}</div>
                                                <div class="col-auto text-end">
                                                    <small>{{ auth()->user()->email }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-muted">{{ __('admin.registration_date') }}</div>
                                                <div class="col-auto text-end">
                                                    <small>{{ auth()->user()->created_at->format('d.m.Y') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-muted">{{ __('admin.last_update') }}</div>
                                                <div class="col-auto text-end">
                                                    <small>{{ auth()->user()->updated_at->format('d.m.Y') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-muted">{{ __('admin.ip_address') }}</div>
                                                <div class="col-auto text-end">
                                                    <small class="font-monospace">{{ request()->ip() }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Pages --}}
                        @if(in_array('page', $activeModules))
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('admin.pages') }}</h3>
                                    <div class="card-actions">
                                        <a href="{{ route('admin.page.index') }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('admin.view_all') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($recentPages && count($recentPages) > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentPages->take(3) as $page)
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="font-weight-medium">
                                                            {{ is_array($page->title) ? ($page->title[app()->getLocale()] ?? array_first($page->title)) : $page->title }}
                                                        </div>
                                                        <div class="text-muted">{{ $page->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="badge badge-outline text-{{ $page->is_active ? 'green' : 'gray' }}">
                                                            {{ $page->is_active ? __('admin.active') : __('admin.draft') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="text-muted">{{ __('admin.no_pages_yet') }}</div>
                                            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary btn-sm mt-2">
                                                {{ __('admin.create_first_page') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- System Status --}}
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('admin.system') }} {{ __('admin.status') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="d-flex align-items-center">
                                                        <span class="status-dot status-dot-animated bg-green me-2"></span>
                                                        PHP {{ PHP_VERSION }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="badge text-green">{{ __('admin.active') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="d-flex align-items-center">
                                                        <span class="status-dot status-dot-animated bg-green me-2"></span>
                                                        Laravel {{ app()->version() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="badge text-green">{{ __('admin.active') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="d-flex align-items-center">
                                                        <span class="status-dot status-dot-animated bg-green me-2"></span>
                                                        Database
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="badge text-green">{{ __('admin.connected') }}</span>
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

    {{-- Create Modal --}}
    <div class="modal modal-blur fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.create') }} {{ __('admin.content') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @if(in_array('page', $activeModules))
                        <div class="col-md-6">
                            <a href="{{ route('admin.page.manage') }}" class="card card-link h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <span class="avatar avatar-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <h3 class="card-title">{{ __('admin.pages') }}</h3>
                                    <p class="text-muted">Yeni sayfa oluştur</p>
                                </div>
                            </a>
                        </div>
                        @endif

                        @if(in_array('portfolio', $activeModules))
                        <div class="col-md-6">
                            <a href="{{ route('admin.portfolio.manage') }}" class="card card-link h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <span class="avatar avatar-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </span>
                                    </div>
                                    <h3 class="card-title">Portfolio</h3>
                                    <p class="text-muted">Yeni portfolio projesi</p>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
/* AI Assistant Panel - Modern Floating Design */
.ai-assistant-panel {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 99999;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Toggle Button */
.ai-toggle-btn {
    position: relative;
    width: 64px;
    height: 64px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    cursor: pointer;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.ai-toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
}

.ai-icon {
    font-size: 24px;
    z-index: 2;
}

.ai-pulse {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: aiPulse 2s infinite;
}

@keyframes aiPulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

/* Main Panel */
.ai-panel {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 400px;
    max-height: 600px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: aiPanelSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes aiPanelSlideIn {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Panel Header */
.ai-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.ai-panel-title {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 16px;
}

.ai-status-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-left: 12px;
}

.ai-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.ai-close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Panel Content */
.ai-panel-content {
    max-height: 520px;
    overflow-y: auto;
    padding: 24px;
}

.ai-section-title {
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}

/* Chat Section */
.ai-chat-messages {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
}

.ai-message {
    display: flex;
    margin-bottom: 16px;
}

.ai-message:last-child {
    margin-bottom: 0;
}

.ai-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    margin-right: 12px;
    flex-shrink: 0;
}

.ai-message-content {
    flex: 1;
}

.ai-message-text {
    background: white;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    color: #374151;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ai-message-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
    margin-left: 16px;
}

/* Chat Input */
.ai-input-container {
    display: flex;
    gap: 8px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.ai-chat-field {
    flex: 1;
    border: none;
    background: none;
    outline: none;
    font-size: 14px;
    color: #374151;
}

.ai-chat-field::placeholder {
    color: #9ca3af;
}

.ai-send-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-send-btn:hover {
    transform: scale(1.05);
}

/* Quick Actions */
.ai-action-grid {
    display: grid;
    gap: 12px;
}

.ai-action-card {
    display: flex;
    align-items: center;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.ai-action-card:hover {
    border-color: #667eea;
    background: #f8faff;
    transform: translateX(4px);
}

.ai-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 18px;
    color: white;
}

.ai-action-icon.seo { background: linear-gradient(135deg, #10b981, #059669); }
.ai-action-icon.content { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.ai-action-icon.analysis { background: linear-gradient(135deg, #10b981, #059669); }

.ai-action-content {
    flex: 1;
}

.ai-action-title {
    font-weight: 600;
    font-size: 14px;
    color: #111827;
    margin-bottom: 4px;
}

.ai-action-desc {
    font-size: 12px;
    color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    .ai-assistant-panel {
        bottom: 20px;
        right: 20px;
    }
    
    .ai-panel {
        width: calc(100vw - 40px);
        max-width: 360px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function refreshDashboard() {
    // Show loading animation
    const refreshBtn = document.querySelector('a[onclick="refreshDashboard()"]');
    const icon = refreshBtn.querySelector('.icon');
    
    icon.classList.add('icon-tabler-rotate');
    
    // Reload page after animation
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function clearSystemCache() {
    if (!confirm('{{ __("admin.confirm_clear_cache") }}')) {
        return;
    }
    
    fetch('/admin/cache/clear-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('{{ __("admin.cache_cleared_successfully") }}', 'success');
        } else {
            showToast('{{ __("admin.error") }}: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('{{ __("admin.error") }}: ' + error.message, 'error');
    });
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible`;
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Auto-refresh dashboard data every 5 minutes
setInterval(() => {
    // Livewire refresh if available
    if (typeof Livewire !== 'undefined') {
        Livewire.emit('refreshDashboard');
    }
}, 300000);

// AI Chat Functions  
document.addEventListener('DOMContentLoaded', function() {
    const aiToggleBtn = document.getElementById('aiToggleBtn');
    const aiPanel = document.getElementById('aiPanel');
    const aiCloseBtn = document.getElementById('aiCloseBtn');
    const aiChatMessages = document.getElementById('aiChatWidgetMessages');
    
    // Panel toggle
    if (aiToggleBtn && aiPanel) {
        aiToggleBtn.addEventListener('click', function() {
            if (aiPanel.style.display === 'none' || aiPanel.style.display === '') {
                aiPanel.style.display = 'block';
                aiPanel.style.animation = 'aiPanelSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            } else {
                aiPanel.style.animation = 'aiPanelSlideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                setTimeout(() => {
                    aiPanel.style.display = 'none';
                }, 300);
            }
        });
    }
    
    // Close panel
    if (aiCloseBtn && aiPanel) {
        aiCloseBtn.addEventListener('click', function() {
            aiPanel.style.display = 'none';
        });
    }
    
    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
        if (aiPanel && !event.target.closest('.ai-assistant-panel')) {
            aiPanel.style.display = 'none';
        }
    });
    
    // Auto-scroll chat messages
    function scrollChatToBottom() {
        if (aiChatMessages) {
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        }
    }
    
    // Add message to chat
    window.addAiMessage = function(message, isUser = false) {
        if (!aiChatMessages) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `ai-message ${isUser ? 'user' : 'assistant'}`;
        
        const now = new Date().toLocaleTimeString('tr-TR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageEl.innerHTML = `
            <div class="ai-message-avatar">
                <i class="fas fa-${isUser ? 'user' : 'robot'}"></i>
            </div>
            <div class="ai-message-content">
                <div class="ai-message-text">${message}</div>
                <div class="ai-message-time">${now}</div>
            </div>
        `;
        
        aiChatMessages.appendChild(messageEl);
        scrollChatToBottom();
    };
    
    // Livewire message sent listener
    Livewire.on('message-sent', (data) => {
        console.log('Livewire message-sent data:', data); // Debug
        
        // Add user message  
        addMessageToChat(data.userMessage, true);
        
        // Add AI response after delay
        setTimeout(() => {
            addMessageToChat(data.aiResponse, false);
        }, 1000);
    });
    
    // Add message to chat function
    function addMessageToChat(message, isUser) {
        if (!aiChatMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-message-widget ${isUser ? 'user' : 'assistant'} mb-3`;
        
        const now = new Date().toLocaleTimeString('tr-TR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="avatar avatar-sm me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        ${isUser ? 
                            '<path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />' : 
                            '<path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M12 1l3 6l6 3l-6 3l-3 6l-3 -6l-6 -3l6 -3z" />'
                        }
                    </svg>
                </div>
                <div class="flex-fill">
                    <div class="card p-3 mb-2">
                        <div>${message || 'Mesaj boş'}</div>
                    </div>
                    <small>${now}</small>
                </div>
            </div>
        `;
        
        aiChatMessages.appendChild(messageDiv);
        aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
    }
});
</script>
@endpush