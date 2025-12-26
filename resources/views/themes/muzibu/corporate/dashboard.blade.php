@extends('themes.muzibu.layouts.app')

@section('title', ($account->company_name ?? __('muzibu::front.footer.corporate')) . ' - ' . __('muzibu::front.corporate.corporate_panel'))

@section('content')
{{-- SPA-safe: Data from HTML attributes (survives SPA navigation) --}}
<div
    x-data="corporateDashboard()"
    data-corporate-code="{{ $account->corporate_code }}"
    data-company-name="{{ addslashes($account->company_name ?? '') }}"
    x-init="corporateCode = $el.dataset.corporateCode; companyName = $el.dataset.companyName"
    class="min-h-screen"
>
    {{-- Hero Banner --}}
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 via-pink-600/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-pink-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    {{-- Company Logo/Icon --}}
                    <div class="relative group">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 via-pink-500 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25 group-hover:shadow-purple-500/40 transition-all duration-300 group-hover:scale-105">
                            <i class="fas fa-building text-white text-3xl"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-slate-900 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    {{-- Company Info --}}
                    <div>
                        <div class="flex items-center gap-3 cursor-pointer group" @click="showCompanyNameModal = true">
                            <h1 class="text-3xl font-bold text-white group-hover:text-purple-300 transition-colors" data-company-name>{{ $account->company_name ?? $account->owner->name }}</h1>
                            <button class="opacity-0 group-hover:opacity-100 transition-all p-2 hover:bg-white/10 rounded-lg">
                                <i class="fas fa-pen text-purple-400 text-sm"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-purple-300/80 text-sm">{{ __('muzibu::front.corporate.corporate_panel') }}</span>
                            <span class="w-1.5 h-1.5 bg-purple-400/50 rounded-full"></span>
                            <span class="text-green-400 text-sm font-medium">
                                <i class="fas fa-circle text-[6px] mr-1 animate-pulse"></i>{{ __('muzibu::front.corporate.active') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="/dashboard" class="px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-xl transition-all flex items-center gap-2 text-sm" data-spa>
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">{{ __('muzibu::front.corporate.dashboard') }}</span>
                    </a>
                    <a href="/corporate/subscriptions" class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-xl transition-all flex items-center gap-2 text-sm shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40" data-spa>
                        <i class="fas fa-crown"></i>
                        <span>Uyelikleri Yonet</span>
                    </a>
                    {{-- More Options --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2.5 bg-white/5 hover:bg-white/10 border border-white/10 text-gray-400 hover:text-white rounded-xl transition-all">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-56 bg-slate-800/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-xl z-50 overflow-hidden">
                            <button @click="open = false; showDisbandModal = true"
                                    class="w-full px-4 py-3 text-left text-red-400 hover:bg-red-500/10 transition flex items-center gap-3">
                                <i class="fas fa-power-off w-5"></i>
                                <span>Kurumsal Uyeligi Sonlandir</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24">
        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Total Members --}}
            <div class="group relative bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-2xl p-5 hover:border-blue-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-blue-300/70 text-sm mb-1">{{ __('muzibu::front.corporate.total_members') }}</p>
                        <p class="text-white text-3xl font-bold" data-total-members>{{ $branchStats['totals']['total_members'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-blue-400 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Total Plays --}}
            <div class="group relative bg-gradient-to-br from-green-500/10 to-green-600/5 border border-green-500/20 rounded-2xl p-5 hover:border-green-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-green-300/70 text-sm mb-1">{{ __('muzibu::front.corporate.total_listening') }}</p>
                        <p class="text-white text-3xl font-bold">{{ number_format($branchStats['totals']['total_plays']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-play text-green-400 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Weekly Plays --}}
            <div class="group relative bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-2xl p-5 hover:border-amber-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-amber-300/70 text-sm mb-1">{{ __('muzibu::front.corporate.this_week') }}</p>
                        <p class="text-white text-3xl font-bold">{{ number_format($branchStats['totals']['weekly_plays']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line text-amber-400 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Total Hours --}}
            <div class="group relative bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-2xl p-5 hover:border-purple-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-purple-300/70 text-sm mb-1">{{ __('muzibu::front.corporate.total_duration') }}</p>
                        <p class="text-white text-3xl font-bold">{{ $branchStats['totals']['total_hours'] }}<span class="text-lg font-normal text-purple-300/70 ml-1">{{ __('muzibu::front.corporate.hours') }}</span></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-clock text-purple-400 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Corporate Code Card --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-purple-900/40 via-pink-900/30 to-purple-900/40 border border-purple-500/30 rounded-2xl p-6 mb-8">
            <div class="absolute top-0 right-0 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-key text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ __('muzibu::front.corporate.corporate_code') }}</h2>
                        <p class="text-purple-300/60 text-sm">{{ __('muzibu::front.corporate.share_code_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Code Display --}}
                    <div class="bg-black/30 backdrop-blur rounded-xl px-6 py-3 border border-white/10">
                        <code class="text-2xl font-mono font-bold text-white tracking-[0.3em]" x-text="corporateCode"></code>
                    </div>
                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button @click="showEditCodeModal = true" class="p-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-xl transition-all hover:scale-105" title="Kodu Duzenle">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button @click="copyCode()" class="p-3 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all hover:scale-105" title="{{ __('muzibu::front.corporate.copy_code') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button @click="showRandomCodeModal = true" class="p-3 bg-amber-500/20 hover:bg-amber-500/30 text-amber-400 rounded-xl transition-all hover:scale-105" title="{{ __('muzibu::front.corporate.regenerate_code') }}">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Members Section --}}
        <div class="bg-white/[0.02] border border-white/10 rounded-2xl overflow-hidden">
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-400"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ __('muzibu::front.corporate.corporate_members') }}</h2>
                        <p class="text-gray-500 text-sm">Kurumunuzdaki tum uyeler</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-blue-500/10 text-blue-400 rounded-xl text-sm font-medium">
                        <i class="fas fa-user-friends mr-2"></i>{{ __('muzibu::front.corporate.members_count', ['count' => $branchStats['totals']['total_members']]) }}
                    </span>
                </div>
            </div>

            @if(count($branchStats['members']) > 0)
                {{-- Members Grid --}}
                <div class="grid md:grid-cols-2 gap-4 p-6">
                    @foreach($branchStats['members'] as $memberId => $member)
                        <div class="group bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-white/10 rounded-xl p-5 transition-all duration-300"
                             x-data="{ expanded: false }"
                             data-member-id="{{ $memberId }}">

                            {{-- Member Header --}}
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="relative flex-shrink-0">
                                    @php
                                        $colors = ['from-blue-500 to-cyan-500', 'from-purple-500 to-pink-500', 'from-amber-500 to-orange-500', 'from-green-500 to-emerald-500', 'from-rose-500 to-red-500'];
                                        $colorIndex = ((int) $memberId) % count($colors);
                                    @endphp
                                    <div class="w-14 h-14 bg-gradient-to-br {{ $colors[$colorIndex] }} rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                        {{ mb_strtoupper(mb_substr($member['user']->name ?? 'U', 0, 2, 'UTF-8')) }}
                                    </div>
                                    @if($member['is_owner'] ?? false)
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-crown text-white text-[10px]"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="text-white font-semibold">{{ $member['user']->name ?? __('muzibu::front.user.user') }}</p>
                                        @if($member['is_owner'] ?? false)
                                            <span class="text-[10px] text-yellow-400 bg-yellow-500/20 px-2 py-0.5 rounded-full font-medium">
                                                {{ __('muzibu::front.corporate.main_branch') }}
                                            </span>
                                        @elseif($member['account']->branch_name)
                                            <span class="text-[10px] text-purple-400 bg-purple-500/20 px-2 py-0.5 rounded-full branch-name-badge">
                                                {{ $member['account']->branch_name }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 text-sm truncate mb-2">{{ $member['user']->email ?? '' }}</p>

                                    {{-- Subscription Badge --}}
                                    @if(isset($member['subscription']) && $member['subscription']['is_active'])
                                        @if($member['subscription']['days_left'] <= 7)
                                            {{-- 7 gün veya az kaldı - Sarı/Uyarı --}}
                                            <span class="inline-flex items-center gap-1.5 text-xs text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>{{ $member['subscription']['ends_at'] }}</span>
                                                <span class="text-amber-500/60">({{ $member['subscription']['days_left'] }} gun)</span>
                                            </span>
                                        @else
                                            {{-- 7 günden fazla - Yeşil/Aktif --}}
                                            <span class="inline-flex items-center gap-1.5 text-xs text-green-400 bg-green-500/10 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-crown"></i>
                                                <span>{{ $member['subscription']['ends_at'] }}</span>
                                                <span class="text-green-500/60">({{ $member['subscription']['days_left'] }} gun)</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs text-red-400 bg-red-500/10 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-times-circle"></i>
                                            <span>Uyelik yok</span>
                                        </span>
                                    @endif
                                </div>

                                {{-- Expand Button --}}
                                <button @click="expanded = !expanded" class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                                    <i class="fas fa-chevron-down text-gray-500 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"></i>
                                </button>
                            </div>

                            {{-- Quick Stats --}}
                            <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-white/5">
                                <div class="text-center">
                                    <p class="text-white font-bold">{{ number_format($member['total_plays']) }}</p>
                                    <p class="text-gray-600 text-xs">{{ __('muzibu::front.corporate.listening_count') }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-white font-bold">{{ $member['weekly_plays'] }}</p>
                                    <p class="text-gray-600 text-xs">{{ __('muzibu::front.corporate.this_week') }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-white font-bold">{{ $member['total_hours'] }}{{ __('muzibu::front.dashboard.hours_short') }}</p>
                                    <p class="text-gray-600 text-xs">Toplam</p>
                                </div>
                            </div>

                            {{-- Expanded Content --}}
                            <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-white/5">
                                {{-- Last Played --}}
                                <div class="mb-4">
                                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-2">{{ __('muzibu::front.corporate.last_played') }}</p>
                                    @if($member['last_play'])
                                        @php
                                            $song = $member['last_play']->song;
                                            $cover = $song->coverMedia ?? ($song->album ? $song->album->coverMedia : null) ?? null;
                                            $coverUrl = $cover ? thumb($cover, 80, 80) : null;
                                        @endphp
                                        <div class="flex items-center gap-3 bg-white/[0.02] rounded-lg p-3">
                                            @if($coverUrl)
                                                <img src="{{ $coverUrl }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                                    <i class="fas fa-music text-white/60 text-sm"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-white text-sm font-medium truncate">{{ $song->title }}</p>
                                                <p class="text-gray-500 text-xs">{{ $song->album->artist->name ?? __('muzibu::front.dashboard.unknown_artist') }}</p>
                                            </div>
                                            <span class="text-gray-600 text-xs">{{ $member['last_play_time'] }}</span>
                                        </div>
                                    @else
                                        <p class="text-gray-600 text-sm bg-white/[0.02] rounded-lg p-3">{{ __('muzibu::front.corporate.no_listening_yet') }}</p>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-wrap gap-2">
                                    {{-- Dinleme Gecmisi (herkes icin) --}}
                                    <a href="/corporate/member/{{ $member['account']->id }}/history"
                                       class="flex-1 px-4 py-2 bg-green-500/10 hover:bg-green-500/20 text-green-400 rounded-lg text-sm transition-colors flex items-center justify-center gap-2"
                                       data-spa>
                                        <i class="fas fa-history"></i>
                                        <span>Dinleme Gecmisi</span>
                                    </a>

                                    @if(!($member['is_owner'] ?? false))
                                        <button @click="editBranchName({{ $memberId }}, '{{ addslashes($member['account']->branch_name ?? '') }}')"
                                                class="flex-1 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-edit"></i>
                                            <span>{{ __('muzibu::front.corporate.branch_name') }}</span>
                                        </button>
                                        <button @click="removeMember({{ $memberId }}, '{{ addslashes($member['user']->name ?? __('muzibu::front.user.user')) }}')"
                                                class="flex-1 px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-user-minus"></i>
                                            <span>{{ __('muzibu::front.corporate.remove_member') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if(isset($branchStats['pagination']) && $branchStats['pagination']['total_pages'] > 1)
                    <div class="flex items-center justify-between p-6 border-t border-white/10">
                        <p class="text-gray-500 text-sm">
                            Sayfa {{ $branchStats['pagination']['current_page'] }} / {{ $branchStats['pagination']['total_pages'] }}
                            <span class="text-gray-600 ml-2">({{ $branchStats['pagination']['total_items'] }} uye)</span>
                        </p>
                        <div class="flex items-center gap-2">
                            @if($branchStats['pagination']['has_prev'])
                                <a href="?page={{ $branchStats['pagination']['current_page'] - 1 }}"
                                   class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-colors flex items-center gap-2"
                                   data-spa>
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="hidden sm:inline">Onceki</span>
                                </a>
                            @endif

                            {{-- Page numbers --}}
                            <div class="hidden sm:flex items-center gap-1">
                                @for($i = max(1, $branchStats['pagination']['current_page'] - 2); $i <= min($branchStats['pagination']['total_pages'], $branchStats['pagination']['current_page'] + 2); $i++)
                                    @if($i == $branchStats['pagination']['current_page'])
                                        <span class="w-10 h-10 bg-purple-500 text-white rounded-lg flex items-center justify-center font-medium">
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a href="?page={{ $i }}"
                                           class="w-10 h-10 bg-white/5 hover:bg-white/10 text-gray-400 rounded-lg flex items-center justify-center transition-colors"
                                           data-spa>
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor
                            </div>

                            @if($branchStats['pagination']['has_next'])
                                <a href="?page={{ $branchStats['pagination']['current_page'] + 1 }}"
                                   class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-colors flex items-center gap-2"
                                   data-spa>
                                    <span class="hidden sm:inline">Sonraki</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-plus text-gray-600 text-3xl"></i>
                    </div>
                    <p class="text-white text-lg font-medium mb-2">{{ __('muzibu::front.corporate.no_members_yet') }}</p>
                    <p class="text-gray-500 text-sm mb-6">{{ __('muzibu::front.corporate.invite_members') }}</p>
                    <button @click="copyCode()" class="px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-xl transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-copy"></i>
                        <span>Kodu Kopyala ve Paylas</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- MODALS --}}

    {{-- Edit Branch Name Modal --}}
    <div x-show="showBranchModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showBranchModal = false">
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tag text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white">{{ __('muzibu::front.corporate.edit_branch_name') }}</h3>
            </div>
            <input type="text" x-model="branchName" placeholder="{{ __('muzibu::front.corporate.branch_name_placeholder') }}"
                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 mb-4 transition-colors">
            <div class="flex gap-3">
                <button @click="showBranchModal = false" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                    {{ __('muzibu::front.corporate.cancel') }}
                </button>
                <button @click="saveBranchName()" :disabled="saving"
                        class="flex-1 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition-colors disabled:opacity-50">
                    <span x-show="!saving">{{ __('muzibu::front.corporate.save') }}</span>
                    <span x-show="saving"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Edit Code Modal --}}
    <div x-show="showEditCodeModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showEditCodeModal = false; codeError = ''; newCode = ''">
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pen text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Kurumsal Kodu Duzenle</h3>
                <p class="text-gray-400 text-sm">Tam olarak 8 karakter girin</p>
            </div>

            {{-- Code Preview --}}
            <div class="bg-black/30 rounded-xl p-4 mb-4 text-center border border-white/5">
                <p class="text-gray-500 text-xs mb-2">Yeni kodunuz</p>
                <div class="text-3xl font-mono font-bold tracking-[0.3em]">
                    <span class="text-white" x-text="newCode || '________'"></span>
                </div>
            </div>

            {{-- Input --}}
            <div class="relative mb-2">
                <input type="text"
                       x-model="newCode"
                       @input="newCode = newCode.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8); validateCode()"
                       maxlength="8"
                       class="w-full px-4 py-3 bg-white/5 border rounded-xl text-white text-center text-2xl font-mono font-bold tracking-widest placeholder-gray-600 focus:outline-none transition-colors"
                       :class="codeError ? 'border-red-500' : (codeValid ? 'border-green-500' : 'border-white/20 focus:border-blue-500')"
                       placeholder="XXXXXXXX">
                <div class="absolute right-4 top-1/2 -translate-y-1/2">
                    <i x-show="codeValid && !codeError" class="fas fa-check-circle text-green-400"></i>
                    <i x-show="codeError" class="fas fa-exclamation-circle text-red-400"></i>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <p class="text-xs" :class="codeValid ? 'text-green-400' : 'text-gray-500'">
                    <span x-text="newCode.length"></span>/8 karakter
                </p>
                <p x-show="codeError" x-text="codeError" class="text-red-400 text-xs"></p>
            </div>

            <div class="flex gap-3">
                <button @click="showEditCodeModal = false; codeError = ''; newCode = ''" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                    Vazgec
                </button>
                <button @click="saveNewCode()" :disabled="savingCode || !codeValid"
                        class="flex-1 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition-colors disabled:opacity-50">
                    <span x-show="!savingCode">Kaydet</span>
                    <span x-show="savingCode"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Company Name Modal --}}
    <div x-show="showCompanyNameModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showCompanyNameModal = false">
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-purple-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Sirket Adini Duzenle</h3>
                <p class="text-gray-400 text-sm">Kurumsal hesabinizin gorunen adini degistirin</p>
            </div>
            <input type="text"
                   x-model="companyName"
                   maxlength="100"
                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 mb-4 transition-colors"
                   placeholder="Sirket Adi">
            <div class="flex gap-3">
                <button @click="showCompanyNameModal = false" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                    Vazgec
                </button>
                <button @click="saveCompanyName()" :disabled="savingCompanyName || !companyName.trim()"
                        class="flex-1 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-xl transition-colors disabled:opacity-50">
                    <span x-show="!savingCompanyName">Kaydet</span>
                    <span x-show="savingCompanyName"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Random Code Modal --}}
    <div x-show="showRandomCodeModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showRandomCodeModal = false">
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-amber-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sync-alt text-amber-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Kodu Yenile</h3>
                <p class="text-gray-400 text-sm">Yeni bir kurumsal kod olusturulacak. Eski kod artik calismayacak.</p>
            </div>
            <div class="flex gap-3">
                <button @click="showRandomCodeModal = false" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                    Vazgec
                </button>
                <button @click="confirmRandomCode()" :disabled="regenerating"
                        class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition-colors disabled:opacity-50">
                    <span x-show="!regenerating">Yeni Kod Olustur</span>
                    <span x-show="regenerating"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Disband Modal --}}
    <div x-show="showDisbandModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="bg-slate-900 border border-red-500/30 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-4">Kurumsal Uyeligi Sonlandir</h3>
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 text-left mb-4">
                    <p class="text-red-400 text-sm font-semibold mb-2">Bu islem geri alinamaz!</p>
                    <ul class="text-gray-400 text-sm space-y-1">
                        <li class="flex items-center gap-2"><i class="fas fa-times text-red-400 text-xs"></i>Kurumsal hesabiniz silinecek</li>
                        <li class="flex items-center gap-2"><i class="fas fa-times text-red-400 text-xs"></i>Tum uyeler kurumdan cikarilacak</li>
                        <li class="flex items-center gap-2"><i class="fas fa-times text-red-400 text-xs"></i>Kurumsal kod gecersiz olacak</li>
                        <li class="flex items-center gap-2"><i class="fas fa-times text-red-400 text-xs"></i>Uyelerin premium haklari sona erecek</li>
                    </ul>
                </div>
                <p class="text-gray-400 text-sm mb-3">Onaylamak icin asagiya <strong class="text-red-400">Kabul Ediyorum</strong> yazin</p>
                <input type="text"
                       x-model="disbandConfirmText"
                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white text-center placeholder-gray-600 focus:outline-none focus:border-red-500 transition-colors"
                       placeholder="Kabul Ediyorum">
            </div>
            <div class="flex gap-3">
                <button @click="showDisbandModal = false; disbandConfirmText = ''" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                    Vazgec
                </button>
                <button @click="confirmDisband()" :disabled="disbanding || disbandConfirmText !== 'Kabul Ediyorum'"
                        class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors disabled:opacity-50">
                    <span x-show="!disbanding">Onayla</span>
                    <span x-show="disbanding"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
