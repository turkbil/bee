@include('subscription::admin.helper')

<div>
    {{-- Custom Styles --}}
    <style>
        /* Dark/Light Mode Uyumlu Değişkenler */
        :root {
            --sub-card-bg: #ffffff;
            --sub-card-border: #e6e7e9;
            --sub-card-hover: #f8fafc;
            --sub-input-bg: #ffffff;
            --sub-text-primary: #1e293b;
            --sub-text-secondary: #64748b;
            --sub-text-muted: #94a3b8;
            --sub-accent: #206bc4;
            --sub-accent-light: #e7f1ff;
            --sub-success: #2fb344;
            --sub-success-light: #d4edda;
            --sub-warning: #f59f00;
            --sub-warning-light: #fff3cd;
            --sub-shadow: 0 4px 24px rgba(0,0,0,0.06);
            --sub-shadow-hover: 0 8px 32px rgba(0,0,0,0.12);
            --sub-dropdown-bg: #ffffff;
            --sub-dropdown-hover: #f1f5f9;
        }

        [data-bs-theme="dark"], .theme-dark {
            --sub-card-bg: #1e2433;
            --sub-card-border: #2c3548;
            --sub-card-hover: #252d3d;
            --sub-input-bg: #181d26;
            --sub-text-primary: #f1f5f9;
            --sub-text-secondary: #94a3b8;
            --sub-text-muted: #64748b;
            --sub-accent: #4dabf7;
            --sub-accent-light: #1e3a5f;
            --sub-success: #51cf66;
            --sub-success-light: #1e3a2f;
            --sub-warning: #fcc419;
            --sub-warning-light: #3d3520;
            --sub-shadow: 0 4px 24px rgba(0,0,0,0.2);
            --sub-shadow-hover: 0 8px 32px rgba(0,0,0,0.3);
            --sub-dropdown-bg: #252d3d;
            --sub-dropdown-hover: #2c3548;
        }

        /* Ana Container */
        .subscription-form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Büyük Kartlar */
        .sub-card {
            background: var(--sub-card-bg);
            border: 2px solid var(--sub-card-border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: var(--sub-shadow);
        }

        .sub-card:hover {
            box-shadow: var(--sub-shadow-hover);
        }

        .sub-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--sub-card-border);
        }

        .sub-card-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .sub-card-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--sub-text-primary);
            margin: 0;
        }

        .sub-card-subtitle {
            font-size: 0.9rem;
            color: var(--sub-text-secondary);
            margin: 0;
        }

        /* Büyük Input'lar */
        .sub-input {
            background: var(--sub-input-bg) !important;
            border: 2px solid var(--sub-card-border) !important;
            border-radius: 12px !important;
            padding: 1rem 1.25rem !important;
            font-size: 1.1rem !important;
            color: var(--sub-text-primary) !important;
            transition: all 0.2s ease !important;
            height: auto !important;
        }

        .sub-input:focus {
            border-color: var(--sub-accent) !important;
            box-shadow: 0 0 0 4px var(--sub-accent-light) !important;
        }

        .sub-input::placeholder {
            color: var(--sub-text-muted) !important;
        }

        .sub-input-group {
            position: relative;
        }

        .sub-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--sub-text-muted);
            font-size: 1.2rem;
            z-index: 5;
        }

        .sub-input-with-icon {
            padding-left: 3.5rem !important;
        }

        /* Search Dropdown */
        .sub-search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--sub-dropdown-bg);
            border: 2px solid var(--sub-card-border);
            border-radius: 12px;
            margin-top: 0.5rem;
            box-shadow: var(--sub-shadow-hover);
            z-index: 1050;
            max-height: 350px;
            overflow-y: auto;
        }

        .sub-search-item {
            padding: 1rem 1.25rem;
            cursor: pointer;
            transition: all 0.15s ease;
            border-bottom: 1px solid var(--sub-card-border);
        }

        .sub-search-item:last-child {
            border-bottom: none;
        }

        .sub-search-item:hover {
            background: var(--sub-dropdown-hover);
        }

        .sub-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
        }

        /* Plan Kartları */
        .sub-plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .sub-plan-card {
            background: var(--sub-card-bg);
            border: 2px solid var(--sub-card-border);
            border-radius: 14px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .sub-plan-card:hover {
            border-color: var(--sub-accent);
            transform: translateY(-2px);
            box-shadow: var(--sub-shadow-hover);
        }

        .sub-plan-card.active {
            border-color: var(--sub-accent);
            background: var(--sub-accent-light);
        }

        .sub-plan-card .plan-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .sub-plan-card .plan-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--sub-text-primary);
            margin-bottom: 0.5rem;
        }

        /* Cycle Kartları */
        .sub-cycle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .sub-cycle-card {
            background: var(--sub-card-bg);
            border: 2px solid var(--sub-card-border);
            border-radius: 14px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            position: relative;
        }

        .sub-cycle-card:hover {
            border-color: var(--sub-accent);
            transform: translateY(-2px);
        }

        .sub-cycle-card.active {
            border-color: var(--sub-success);
            background: var(--sub-success-light);
        }

        .sub-cycle-card .cycle-duration {
            font-size: 2rem;
            font-weight: 800;
            color: var(--sub-accent);
            line-height: 1;
        }

        .sub-cycle-card .cycle-label {
            font-size: 0.9rem;
            color: var(--sub-text-secondary);
            margin-bottom: 0.75rem;
        }

        .sub-cycle-card .cycle-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sub-text-primary);
        }

        .sub-cycle-card .cycle-old-price {
            font-size: 0.9rem;
            color: var(--sub-text-muted);
            text-decoration: line-through;
        }

        .sub-cycle-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Tarih Grid */
        .sub-date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .sub-date-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Özet Paneli */
        .sub-summary {
            background: linear-gradient(135deg, var(--sub-accent) 0%, #1971c2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .sub-summary::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        }

        .sub-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .sub-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .sub-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .sub-summary-item {
            text-align: center;
        }

        .sub-summary-item .icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.25rem;
        }

        .sub-summary-item .label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .sub-summary-item .value {
            font-size: 1.25rem;
            font-weight: 700;
        }

        /* Submit Button */
        .sub-submit-btn {
            background: linear-gradient(135deg, var(--sub-success) 0%, #37b24d 100%);
            border: none;
            border-radius: 14px;
            padding: 1.25rem 2.5rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(47, 179, 68, 0.3);
        }

        .sub-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(47, 179, 68, 0.4);
        }

        .sub-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Seçili Kullanıcı Kartı */
        .sub-selected-user {
            background: var(--sub-success-light);
            border: 2px solid var(--sub-success);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Loading Spinner */
        .sub-loading {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--sub-text-muted);
            padding: 0.75rem;
        }

        /* Step Indicator */
        .sub-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .sub-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            background: var(--sub-card-bg);
            border: 2px solid var(--sub-card-border);
            color: var(--sub-text-muted);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .sub-step.active {
            border-color: var(--sub-accent);
            color: var(--sub-accent);
            background: var(--sub-accent-light);
        }

        .sub-step.completed {
            border-color: var(--sub-success);
            color: var(--sub-success);
            background: var(--sub-success-light);
        }

        .sub-step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            background: currentColor;
            color: white;
        }

        .sub-step.active .sub-step-number,
        .sub-step.completed .sub-step-number {
            background: currentColor;
        }

        .sub-step-line {
            width: 40px;
            height: 2px;
            background: var(--sub-card-border);
        }
    </style>

    <form wire:submit="save">
        <div class="subscription-form-container">

            {{-- Step Indicator --}}
            <div class="sub-step-indicator">
                <div class="sub-step {{ $user_id ? 'completed' : 'active' }}">
                    <span class="sub-step-number">
                        @if($user_id)
                            <i class="fas fa-check"></i>
                        @else
                            1
                        @endif
                    </span>
                    <span>Kullanıcı</span>
                </div>
                <div class="sub-step-line"></div>
                <div class="sub-step {{ $subscription_plan_id ? 'completed' : ($user_id ? 'active' : '') }}">
                    <span class="sub-step-number">
                        @if($subscription_plan_id)
                            <i class="fas fa-check"></i>
                        @else
                            2
                        @endif
                    </span>
                    <span>Plan</span>
                </div>
                <div class="sub-step-line"></div>
                <div class="sub-step {{ $cycle_key ? 'completed' : ($subscription_plan_id ? 'active' : '') }}">
                    <span class="sub-step-number">
                        @if($cycle_key)
                            <i class="fas fa-check"></i>
                        @else
                            3
                        @endif
                    </span>
                    <span>Süre</span>
                </div>
            </div>

            {{-- CARD 1: Kullanıcı Seçimi --}}
            <div class="sub-card">
                <div class="sub-card-header">
                    <div class="sub-card-icon bg-primary-lt text-primary">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="sub-card-title">Kullanıcı Seçin</h3>
                        <p class="sub-card-subtitle">Abonelik verilecek kullanıcıyı arayın</p>
                    </div>
                </div>

                @if($subscriptionId && $selectedUser)
                    {{-- Düzenleme modunda --}}
                    <div class="sub-selected-user">
                        <div class="sub-avatar bg-primary text-white">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-size: 1.1rem; font-weight: 600; color: var(--sub-text-primary);">
                                {{ $selectedUser->name }}
                            </div>
                            <div style="color: var(--sub-text-secondary);">
                                <i class="fas fa-envelope me-1"></i> {{ $selectedUser->email }}
                                <span class="ms-2"><i class="fas fa-hashtag me-1"></i>{{ $selectedUser->id }}</span>
                            </div>
                        </div>
                        <i class="fas fa-lock text-muted" title="Düzenleme modunda değiştirilemez"></i>
                    </div>
                @else
                    {{-- Yeni abonelik --}}
                    <div class="sub-input-group position-relative" x-data="{ showDropdown: @entangle('showUserDropdown').live }">
                        <i class="sub-input-icon fas fa-search"></i>
                        <input type="text"
                               class="sub-input sub-input-with-icon w-100 @error('user_id') is-invalid @enderror"
                               wire:model.live.debounce.300ms="userSearch"
                               placeholder="İsim, email veya #ID yazarak arayın..."
                               autocomplete="off"
                               @focus="showDropdown = true">

                        @if($user_id)
                            <button type="button"
                                    class="btn btn-link position-absolute"
                                    style="right: 1rem; top: 50%; transform: translateY(-50%); color: var(--sub-text-muted);"
                                    wire:click="clearUserSelection">
                                <i class="fas fa-times-circle fa-lg"></i>
                            </button>
                        @endif

                        {{-- Loading --}}
                        <div wire:loading wire:target="userSearch" class="sub-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Aranıyor...</span>
                        </div>

                        {{-- Dropdown --}}
                        @if(count($userSearchResults) > 0)
                            <div x-show="showDropdown"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 @click.outside="showDropdown = false"
                                 class="sub-search-dropdown">
                                @foreach($userSearchResults as $searchUser)
                                    <div class="sub-search-item"
                                         wire:click="selectUser({{ $searchUser['id'] }})"
                                         wire:key="user-{{ $searchUser['id'] }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="sub-avatar bg-primary-lt text-primary">
                                                {{ strtoupper(substr($searchUser['name'], 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div style="font-size: 1.05rem; font-weight: 600; color: var(--sub-text-primary);">
                                                    {{ $searchUser['name'] }}
                                                </div>
                                                <div style="font-size: 0.9rem; color: var(--sub-text-secondary);">
                                                    {{ $searchUser['email'] }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-secondary" style="font-size: 0.85rem;">#{{ $searchUser['id'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Hint Messages --}}
                        <div wire:loading.remove wire:target="userSearch" class="mt-2">
                            @if(strlen($userSearch) > 0 && strlen($userSearch) < 2)
                                <small style="color: var(--sub-warning);">
                                    <i class="fas fa-info-circle me-1"></i>
                                    En az 2 karakter girin
                                </small>
                            @elseif(strlen($userSearch) >= 2 && count($userSearchResults) === 0 && !$user_id)
                                <small style="color: #e03131;">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Sonuç bulunamadı
                                </small>
                            @elseif(!$user_id && strlen($userSearch) === 0)
                                <small style="color: var(--sub-text-muted);">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Örnek: "Ahmet", "ahmet@email.com" veya "#123"
                                </small>
                            @endif
                        </div>

                        @error('user_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Seçili Kullanıcı --}}
                    @if($selectedUser)
                        <div class="sub-selected-user mt-3">
                            <div class="sub-avatar bg-success text-white">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-size: 1.1rem; font-weight: 600; color: var(--sub-text-primary);">
                                    {{ $selectedUser->name }}
                                </div>
                                <div style="color: var(--sub-text-secondary);">
                                    <i class="fas fa-envelope me-1"></i> {{ $selectedUser->email }}
                                    <span class="ms-3"><i class="fas fa-hashtag me-1"></i>{{ $selectedUser->id }}</span>
                                    @if($selectedUser->subscription_expires_at)
                                        <span class="ms-3">
                                            <i class="fas fa-clock me-1"></i>
                                            Mevcut bitiş: {{ \Carbon\Carbon::parse($selectedUser->subscription_expires_at)->format('d.m.Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm" wire:click="clearUserSelection">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                @endif
            </div>

            {{-- CARD 2: Plan Seçimi --}}
            @if($user_id)
            <div class="sub-card" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'center' })">
                <div class="sub-card-header">
                    <div class="sub-card-icon bg-success-lt text-success">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <h3 class="sub-card-title">Plan Seçin</h3>
                        <p class="sub-card-subtitle">Kullanıcıya verilecek abonelik planı</p>
                    </div>
                </div>

                <div class="sub-plan-grid">
                    @foreach($plans as $plan)
                        <div class="sub-plan-card {{ $subscription_plan_id == $plan->subscription_plan_id ? 'active' : '' }}"
                             wire:click="$set('subscription_plan_id', {{ $plan->subscription_plan_id }})"
                             wire:key="plan-{{ $plan->subscription_plan_id }}">
                            <div class="plan-icon {{ $subscription_plan_id == $plan->subscription_plan_id ? 'bg-primary text-white' : 'bg-primary-lt text-primary' }}">
                                <i class="fas fa-gem"></i>
                            </div>
                            <div class="plan-name">{{ $plan->getTranslated('title', 'tr') }}</div>
                            @if($plan->is_featured)
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i> Önerilen
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @error('subscription_plan_id')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            @endif

            {{-- CARD 3: Süre Seçimi --}}
            @if($subscription_plan_id && !empty($available_cycles))
            <div class="sub-card" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'center' })">
                <div class="sub-card-header">
                    <div class="sub-card-icon bg-warning-lt text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="sub-card-title">Süre Seçin</h3>
                        <p class="sub-card-subtitle">Abonelik süresi ve fiyatlandırma</p>
                    </div>
                </div>

                <div class="sub-cycle-grid">
                    @foreach($available_cycles as $key => $cycle)
                        <div class="sub-cycle-card {{ $cycle_key === $key ? 'active' : '' }}"
                             wire:click="$set('cycle_key', '{{ $key }}')"
                             wire:key="cycle-{{ $key }}">

                            @if(!empty($cycle['badge']['text']))
                                <span class="sub-cycle-badge bg-{{ $cycle['badge']['color'] ?? 'primary' }}">
                                    {{ $cycle['badge']['text'] }}
                                </span>
                            @endif

                            <div class="cycle-duration">{{ $cycle['duration_days'] }}</div>
                            <div class="cycle-label">{{ $cycle['label']['tr'] ?? $cycle['label']['en'] ?? $key }}</div>

                            @if(!empty($cycle['compare_price']) && $cycle['compare_price'] > $cycle['price'])
                                <div class="cycle-old-price">
                                    {{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($cycle['compare_price'], 2) }}
                                </div>
                            @endif

                            <div class="cycle-price">
                                {{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($cycle['price'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('cycle_key')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            @elseif($subscription_plan_id)
                <div class="sub-card">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bu plan için henüz süre seçeneği tanımlanmamış.
                    </div>
                </div>
            @endif

            {{-- CARD 4: Tarihler --}}
            @if($cycle_key)
            <div class="sub-card">
                <div class="sub-card-header">
                    <div class="sub-card-icon bg-info-lt text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="sub-card-title">Tarih Ayarları</h3>
                        <p class="sub-card-subtitle">Başlangıç ve bitiş tarihleri (otomatik hesaplanır)</p>
                    </div>
                </div>

                <div class="sub-date-grid">
                    <div>
                        <label style="font-weight: 600; color: var(--sub-text-primary); margin-bottom: 0.5rem; display: block;">
                            <i class="fas fa-play-circle text-success me-2"></i>
                            Başlangıç Tarihi
                        </label>
                        <input type="datetime-local"
                               class="sub-input w-100 @error('started_at') is-invalid @enderror"
                               wire:model.live="started_at">
                        @error('started_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="font-weight: 600; color: var(--sub-text-primary); margin-bottom: 0.5rem; display: block;">
                            <i class="fas fa-stop-circle text-danger me-2"></i>
                            Bitiş Tarihi
                        </label>
                        <input type="datetime-local"
                               class="sub-input w-100 @error('current_period_end') is-invalid @enderror"
                               wire:model="current_period_end">
                        @error('current_period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- Dinamik Özet Paneli --}}
            @if($user_id && $subscription_plan_id && $cycle_key)
                @php
                    $selectedPlan = $plans->firstWhere('subscription_plan_id', $subscription_plan_id);
                    $selectedCycleData = $available_cycles[$cycle_key] ?? null;
                @endphp

                <div class="sub-summary">
                    <div class="sub-summary-grid">
                        <div class="sub-summary-item">
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="label">Kullanıcı</div>
                            <div class="value">{{ $selectedUser->name ?? '-' }}</div>
                        </div>

                        <div class="sub-summary-item">
                            <div class="icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="label">Plan</div>
                            <div class="value">{{ $selectedPlan?->getTranslated('title', 'tr') ?? '-' }}</div>
                        </div>

                        <div class="sub-summary-item">
                            <div class="icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="label">Süre</div>
                            <div class="value">{{ $selectedCycleData['duration_days'] ?? 0 }} Gün</div>
                        </div>

                        <div class="sub-summary-item">
                            <div class="icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="label">Tutar</div>
                            <div class="value">
                                {{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($selectedCycleData['price'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 position-relative" style="z-index: 1;">
                        <button type="submit" class="sub-submit-btn" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-check-circle me-2"></i>
                                Aboneliği Oluştur
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Kaydediliyor...
                            </span>
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </form>
</div>
