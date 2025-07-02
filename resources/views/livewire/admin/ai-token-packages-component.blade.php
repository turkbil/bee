<div>
    <!-- Token Durum Kartları -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.status.current_balance') }}</div>
                    </div>
                    <div class="h1 mb-0 text-green">{{ number_format($tokenInfo['balance']) }}</div>
                    <div class="text-muted">{{ __('ai::admin.status.token') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.status.monthly_usage') }}</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($tokenInfo['monthly_used']) }}</div>
                    <div class="text-muted">
                        @if($tokenInfo['monthly_limit'] > 0)
                            / {{ number_format($tokenInfo['monthly_limit']) }} {{ __('ai::admin.status.token') }}
                        @else
                            {{ __('ai::admin.status.token') }} ({{ __('ai::admin.status.unlimited') }})
                        @endif
                    </div>
                    @if($tokenInfo['monthly_limit'] > 0)
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar" style="width: {{ min(100, ($tokenInfo['monthly_used'] / $tokenInfo['monthly_limit']) * 100) }}%"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.status.ai_status') }}</div>
                    </div>
                    <div class="h1 mb-0">
                        @if($tokenInfo['ai_enabled'])
                            <span class="text-green">{{ __('ai::admin.active') }}</span>
                        @else
                            <span class="text-red">{{ __('ai::admin.inactive') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.status.availability') }}</div>
                    </div>
                    <div class="h1 mb-0">
                        @if($tokenInfo['ai_enabled'] && $tokenInfo['balance'] > 0)
                            <span class="text-green">{{ __('ai::admin.status.available') }}</span>
                        @else
                            <span class="text-orange">{{ __('ai::admin.status.limited') }}</span>
                        @endif
                    </div>
                    <div class="text-muted small">
                        @if($tokenInfo['balance'] > 0)
                            {{ __('ai::admin.status.token_available') }}
                        @else
                            {{ __('ai::admin.status.token_required') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Token Paketleri -->
    <div class="row row-cards">
                @forelse($packages as $index => $package)
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-md">
                        @if($package->is_popular)
                        <div class="ribbon ribbon-top ribbon-bookmark bg-green">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="m12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="card-body text-center">
                            <div class="text-uppercase text-secondary font-weight-medium">{{ $package->name }}</div>
                            <div class="display-5 fw-bold my-3">{{ number_format($package->token_amount) }}</div>
                            <div class="text-muted mb-1">{{ __('ai::admin.status.token') }}</div>
                            <div class="h3 text-success mb-3">{{ number_format($package->price, 0) }} {{ $package->currency }}</div>
                            <div class="text-muted small mb-3">
                                ~{{ number_format($package->price / $package->token_amount, 4) }} {{ $package->currency }}/token
                            </div>
                            
                            @if($package->features)
                            <ul class="list-unstyled lh-lg">
                                @foreach($package->features as $feature)
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="m5 12l5 5l10 -10"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            
                            <div class="text-center mt-4">
                                <a href="#" class="btn {{ $package->is_popular ? 'btn-success' : 'btn-primary' }} w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M17 17h-11v-14h-2"/>
                                        <path d="M6 5l14 1l-1 7h-13"/>
                                    </svg>
                                    {{ __('ai::admin.packages.buy') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img"><img src="/static/illustrations/undraw_quitting_time_dm8t.svg" height="128" alt=""></div>
                        <p class="empty-title">{{ __('ai::admin.packages.no_packages') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('ai::admin.packages.no_packages_description') }}
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
</div>