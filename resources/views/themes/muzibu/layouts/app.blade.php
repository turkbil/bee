@php
    // 🔐 Şifre Koruması - DEVRE DIŞI
    // $constructionPassword = 'nn';
    // $cookieName = 'mzb_auth_' . tenant('id');
    // $cookieValue = md5($constructionPassword . 'salt2024');
    // $isAuthenticated = isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $cookieValue;
    $isAuthenticated = true; // ✅ ŞİFRE KORUMASINI KALDIRDIK
@endphp

@if(!$isAuthenticated)
    @include('themes.muzibu.password-protection')
@else
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="muzibuApp()" x-init="init()" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🔇 CONSOLE FILTER - Suppress tracking/marketing noise --}}
    <script>
    (function(){const p=[/yandex/i,/attestation/i,/topics/i,/googletagmanager/i,/facebook/i,/ERR_BLOCKED_BY_CLIENT/i];const s=m=>!m?false:p.some(x=>x.test(m));const e=console.error;console.error=function(){const m=Array.from(arguments).join(' ');if(!s(m))e.apply(console,arguments);};const w=console.warn;console.warn=function(){const m=Array.from(arguments).join(' ');if(!s(m))w.apply(console,arguments);};const l=console.log;console.log=function(){const m=Array.from(arguments).join(' ');if(!s(m))l.apply(console,arguments);};})();
    </script>

    {{-- User Auth for Frontend JS --}}
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-email" content="{{ auth()->user()->email }}">
    @endauth

    {{-- Device Limit Session Flash --}}
    @if (session('device_limit_exceeded'))
        <meta name="device-limit-exceeded" content="true">
        <meta name="device-limit" content="{{ session('device_limit', 1) }}">
        <meta name="active-device-count" content="{{ session('active_device_count', 2) }}">
    @endif

    <title>@yield('title', 'Muzibu - İşletmenize Yasal ve Telifsiz Müzik')</title>

    {{-- Performance: DNS Prefetch & Preconnect --}}
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Performance: Preload Critical Fonts (FontAwesome) --}}
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-light-300.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>

    {{-- Tailwind CSS - Tenant Aware (tenant-1001.css) --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- FontAwesome Pro 7.1.0 (Local) --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Alpine.js provided by Livewire --}}

    {{-- Audio Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    {{-- SortableJS for Queue Drag & Drop (Mobile + Desktop) --}}
    <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>

    {{-- ⚡ instant.page DISABLED - Conflicts with SPA Router (causes double navigation) --}}
    {{-- <script src="//instant.page/5.2.0" type="module" data-intensity="hover" data-delay="50"></script> --}}

    @livewireStyles

    {{-- Favicon - Tenant-aware dynamic route --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- PWA Manifest (2025 Best Practice) --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon (iOS/Safari) - Uses favicon as fallback --}}
    <link rel="apple-touch-icon" href="/favicon.ico">

    {{-- Theme Color for Mobile Browser Bar (Tenant-aware) --}}
    @php
        $themeColor = setting('site_theme_color') ?: '#000000';
        $themeColorLight = setting('site_theme_color_light') ?: '#ffffff';
        $themeColorDark = setting('site_theme_color_dark') ?: '#1a202c';
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="{{ $themeColorLight }}">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="{{ $themeColorDark }}">

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-custom.css') }}">
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/player-core.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/play-helpers.js') }}"></script>

    <script>
        // 🌍 Global Lang Strings for JS
        window.muzibuLang = {
            queue: {
                added_to_queue: "{{ trans('muzibu::front.player.added_to_queue') }}",
                added_to_queue_next: "{{ trans('muzibu::front.player.added_to_queue_next') }}",
                added_with_duplicates: "{{ trans('muzibu::front.player.added_with_duplicates_removed') }}",
                added_next_with_duplicates: "{{ trans('muzibu::front.player.added_next_with_duplicates_removed') }}",
                song_not_found: "{{ trans('muzibu::front.player.song_not_found_to_add') }}",
                queue_error: "{{ trans('muzibu::front.player.queue_add_error') }}"
            }
        };

        // 🔧 Helper: Replace placeholders in lang strings
        window.trans = function(key, params = {}) {
            let text = key;
            Object.keys(params).forEach(param => {
                text = text.replace(`:${param}`, params[param]);
            });
            return text;
        };

        // 🎯 muzibuApp - Will be loaded from player-core.js
        // REMOVED: Stub was causing Alpine to initialize with empty methods
        // Real muzibuApp is defined in /public/themes/muzibu/js/player/core/player-core.js

        // 🎯 dashboardApp - Dashboard page
        window.dashboardApp = function() {
            return {
                init() {},
                playSong(songId) { if (window.MuzibuPlayer) window.MuzibuPlayer.playById(songId); },
                playAllFavorites() { window.location.href = '/muzibu/favorites?autoplay=1'; },
                shuffleFavorites() { window.location.href = '/muzibu/favorites?shuffle=1'; },
                copyCode(code) {
                    navigator.clipboard.writeText(code).then(() => {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Kod kopyalandı!', type: 'success' } }));
                    });
                },
                async leaveCorporate() {
                    if (!confirm('Kurumsal hesaptan ayrılmak istediğinize emin misiniz?')) return;
                    try {
                        const response = await fetch('/corporate/leave', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            setTimeout(() => window.location.reload(), 1000);
                        } else throw new Error(data.message);
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Bir hata oluştu', type: 'error' } }));
                    }
                }
            };
        };

        // 🎯 corporatePanel - Corporate join/create
        window.corporatePanel = function() {
            return {
                showCreate: false,
                code: '',
                companyName: '',
                joining: false,
                creating: false,
                async joinCorporate() {
                    if (this.code.length < 8 || this.joining) return;
                    this.joining = true;
                    try {
                        const response = await fetch('/corporate/join', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify({ corporate_code: this.code.toUpperCase() })
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            setTimeout(() => window.location.href = data.redirect || '/dashboard', 1000);
                        } else throw new Error(data.message || 'Geçersiz kod');
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message, type: 'error' } }));
                    } finally { this.joining = false; }
                },
                async createCorporate() {
                    if (this.companyName.length < 2 || this.creating) return;
                    this.creating = true;
                    try {
                        const response = await fetch('/corporate/create', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify({ company_name: this.companyName })
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            setTimeout(() => window.location.href = data.redirect || '/corporate/dashboard', 1500);
                        } else throw new Error(data.message || 'Hata oluştu');
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message, type: 'error' } }));
                    } finally { this.creating = false; }
                }
            };
        };

        // 🎯 corporateJoinPage - Corporate join/create with code validation (SPA compatible)
        window.corporateJoinPage = function() {
            return {
                joinCode: '',
                joining: false,
                leaving: false,
                companyName: '',
                createCode: '',
                creating: false,
                codeError: '',
                codeAvailable: null,
                checkingCode: false,
                get createCodeValid() { return this.createCode.length === 8 && this.codeAvailable === true; },
                async validateCreateCode() {
                    if (this.createCode.length === 0) { this.codeError = ''; this.codeAvailable = null; }
                    else if (this.createCode.length < 8) { this.codeError = 'Tam olarak 8 karakter gerekli'; this.codeAvailable = null; }
                    else { this.codeError = ''; await this.checkCodeAvailability(); }
                },
                async checkCodeAvailability() {
                    if (this.createCode.length !== 8 || this.checkingCode) return;
                    this.checkingCode = true; this.codeAvailable = null;
                    try {
                        const response = await fetch('/corporate/check-code', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ code: this.createCode }) });
                        const data = await response.json();
                        this.codeAvailable = data.available;
                        this.codeError = data.available ? '' : (data.message || 'Bu kod zaten kullanımda');
                    } catch (error) { this.codeError = 'Kontrol sirasinda hata olustu'; this.codeAvailable = null; }
                    finally { this.checkingCode = false; }
                },
                async joinWithCode() {
                    if (this.joinCode.length !== 8 || this.joining) return;
                    this.joining = true;
                    try {
                        const response = await fetch('/corporate/join', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ corporate_code: this.joinCode }) });
                        const data = await response.json();
                        if (data.success) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } })); setTimeout(() => { window.location.assign(data.redirect || '/dashboard'); }, 1000); }
                        else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Gecersiz kod', type: 'error' } })); }
                    } catch (error) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Hata olustu', type: 'error' } })); }
                    finally { this.joining = false; }
                },
                async createCorporate() {
                    if (this.companyName.length < 2 || !this.createCodeValid || this.creating) return;
                    this.creating = true; this.codeError = '';
                    try {
                        const response = await fetch('/corporate/create', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ company_name: this.companyName, corporate_code: this.createCode }) });
                        const data = await response.json();
                        if (data.success) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } })); setTimeout(() => { window.location.href = data.redirect || '/corporate/dashboard'; }, 1500); }
                        else { this.codeError = data.message || 'Bu kod zaten kullanımda'; window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Hata olustu', type: 'error' } })); }
                    } catch (error) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Hata olustu', type: 'error' } })); }
                    finally { this.creating = false; }
                },
                showLeaveModal() {
                    const confirmModal = Alpine.store('confirmModal');
                    if (!confirmModal) { if (confirm('Kurumsal hesaptan ayrilmak istediginize emin misiniz?')) { this.doLeave(); } return; }
                    confirmModal.show({ title: 'Kurumdan Ayrıl', message: 'Kurumsal hesaptan ayrılmak istediğinize emin misiniz?', confirmText: 'Evet, Ayrıl', cancelText: 'Vazgeç', type: 'danger', onConfirm: () => this.doLeave() });
                },
                async doLeave() {
                    if (this.leaving) return;
                    this.leaving = true;
                    try {
                        const response = await fetch('/corporate/leave', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                        const data = await response.json();
                        if (data.success) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } })); setTimeout(() => { if (window.muzibuRouter) { window.muzibuRouter.navigateTo('/dashboard'); } else { window.location.href = '/dashboard'; } }, 1000); }
                        else { throw new Error(data.message); }
                    } catch (error) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Hata olustu', type: 'error' } })); this.leaving = false; }
                }
            };
        };

        // 🎯 corporateDashboard - Corporate dashboard (parameter-based for SPA)
        window.corporateDashboard = function(initialData = {}) {
            return {
                corporateCode: initialData.corporateCode || '',
                companyName: initialData.companyName || '',
                loading: false,
                regenerating: false,
                showBranchModal: false,
                showRandomCodeModal: false,
                showEditCodeModal: false,
                showCompanyNameModal: false,
                showDisbandModal: false,
                disbanding: false,
                editingMemberId: null,
                branchName: '',
                saving: false,
                newCode: '',
                savingCode: false,
                savingCompanyName: false,
                disbandConfirmText: '',
                codeError: '',
                init() { this.loading = false; },
                get codeValid() { return this.newCode.length === 8; },
                validateCode() {
                    if (this.newCode.length === 0) this.codeError = '';
                    else if (this.newCode.length < 8) this.codeError = 'Tam olarak 8 karakter gerekli';
                    else if (this.newCode.length > 8) this.codeError = 'Maximum 8 karakter girebilirsiniz';
                    else this.codeError = '';
                },
                copyCode() {
                    navigator.clipboard.writeText(this.corporateCode).then(() => {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Kod kopyalandı!', type: 'success' } }));
                    });
                },
                async saveNewCode() {
                    if (!this.codeValid) { this.validateCode(); return; }
                    this.savingCode = true;
                    this.codeError = '';
                    try {
                        const response = await fetch('/corporate/regenerate-code', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify({ code: this.newCode })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.corporateCode = data.new_code;
                            this.showEditCodeModal = false;
                            this.newCode = '';
                            this.codeError = '';
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Kod güncellendi: ' + data.new_code, type: 'success' } }));
                        } else {
                            this.codeError = data.message || 'Bu kod zaten kullanımda';
                            throw new Error(data.message);
                        }
                    } catch (error) { this.codeError = error.message || 'Hata oluştu';
                    } finally { this.savingCode = false; }
                },
                async saveCompanyName() {
                    if (!this.companyName.trim()) return;
                    this.savingCompanyName = true;
                    try {
                        const response = await fetch('/corporate/update-company-name', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify({ company_name: this.companyName })
                        });
                        const data = await response.json();
                        if (data.success) {
                            // ✅ FIX: Sadece H1 başlığını seç (parent div değil!)
                            const companyNameEl = document.querySelector('h1[data-company-name]');
                            if (companyNameEl) companyNameEl.textContent = this.companyName;

                            // ✅ FIX: SPA için parent div'deki data attribute'u güncelle
                            const parentDiv = document.querySelector('[x-data*="corporateDashboard"]');
                            if (parentDiv) {
                                parentDiv.setAttribute('data-company-name', this.companyName);
                            }

                            this.showCompanyNameModal = false;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Şirket adı güncellendi!', type: 'success' } }));
                        } else throw new Error(data.message || 'Şirket adı güncellenemedi');
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Hata oluştu', type: 'error' } }));
                    } finally { this.savingCompanyName = false; }
                },
                async confirmRandomCode() {
                    this.regenerating = true;
                    try {
                        const response = await fetch('/corporate/regenerate-code', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.corporateCode = data.new_code;
                            this.showRandomCodeModal = false;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Yeni kod oluşturuldu!', type: 'success' } }));
                        } else throw new Error(data.message);
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Hata oluştu', type: 'error' } }));
                    } finally { this.regenerating = false; }
                },
                async confirmDisband() {
                    if (this.disbandConfirmText !== 'Kabul Ediyorum') return;
                    this.disbanding = true;
                    try {
                        const response = await fetch('/corporate/disband', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            setTimeout(() => { window.location.href = data.redirect || '/dashboard'; }, 1500);
                        } else throw new Error(data.message);
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Bir hata oluştu.', type: 'error' } }));
                    } finally { this.disbanding = false; this.showDisbandModal = false; }
                },
                editBranchName(memberId, currentName) {
                    this.editingMemberId = memberId;
                    this.branchName = currentName;
                    this.showBranchModal = true;
                },
                async saveBranchName() {
                    if (!this.editingMemberId) return;
                    this.saving = true;
                    try {
                        const response = await fetch(`/corporate/update-branch/${this.editingMemberId}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify({ branch_name: this.branchName })
                        });
                        const data = await response.json();
                        if (data.success) {
                            const memberCard = document.querySelector(`[data-member-id="${this.editingMemberId}"]`);
                            if (memberCard) {
                                const branchBadge = memberCard.querySelector('.branch-name-badge');
                                if (branchBadge) branchBadge.textContent = this.branchName || '';
                            }
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Şube adı güncellendi!', type: 'success' } }));
                            this.showBranchModal = false;
                        } else throw new Error(data.message || 'Güncelleme başarısız');
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Hata oluştu', type: 'error' } }));
                    } finally { this.saving = false; }
                },
                async removeMember(memberId, memberName) {
                    if (!confirm(memberName + ' kullanıcısını kurumsal hesaptan çıkarmak istediğinize emin misiniz?')) return;
                    try {
                        const response = await fetch(`/corporate/remove-member/${memberId}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            const memberCard = document.querySelector(`[data-member-id="${memberId}"]`);
                            if (memberCard) {
                                memberCard.style.transition = 'opacity 0.3s, transform 0.3s';
                                memberCard.style.opacity = '0';
                                memberCard.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    memberCard.remove();
                                    const totalMembersEl = document.querySelector('[data-total-members]');
                                    if (totalMembersEl) {
                                        const currentTotal = parseInt(totalMembersEl.textContent);
                                        totalMembersEl.textContent = currentTotal - 1;
                                    }
                                }, 300);
                            }
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                        } else throw new Error(data.message || 'Üye çıkarma başarısız');
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: error.message || 'Hata oluştu', type: 'error' } }));
                    }
                }
            };
        };
    </script>

    {{-- 🤖 Universal Schema Auto-Render (Dynamic for ALL modules) --}}
    {{-- SKIP if Controller already shared metaTags with schemas (prevents duplicates) --}}
    @php
        $sharedMetaTags = view()->getShared()['metaTags'] ?? null;
        $hasControllerSchemas = $sharedMetaTags && isset($sharedMetaTags['schemas']) && !empty($sharedMetaTags['schemas']);
    @endphp
    @if(!$hasControllerSchemas && isset($item) && is_object($item) && method_exists($item, 'getUniversalSchemas'))
        {!! \App\Services\SEOService::getAllSchemas($item) !!}
    @endif

    {{-- 🎯 Marketing Platforms Auto-Loader (GTM, GA4, Facebook, Yandex, LinkedIn, TikTok, Clarity) --}}
    <x-marketing.auto-platforms />

    @yield('styles')

</head>
<body class="bg-black text-white overflow-hidden"
      @play-song.window="playSong($event.detail.songId)"
      @play-all-preview.window="
        if ($store.sidebar.previewInfo?.type === 'Playlist') {
            window.playPlaylist ? window.playPlaylist($store.sidebar.previewInfo.id) : $store.player.playPlaylist($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Album') {
            window.playAlbum ? window.playAlbum($store.sidebar.previewInfo.id) : $store.player.playAlbum($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Genre') {
            window.playGenres ? window.playGenres($store.sidebar.previewInfo.id) : $store.player.playGenre($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Sector') {
            window.playSector ? window.playSector($store.sidebar.previewInfo.id) : $store.player.playSector($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Radio') {
            window.playRadio ? window.playRadio($store.sidebar.previewInfo.id) : $store.player.playRadio($store.sidebar.previewInfo.id);
        }
      "
      @play-all-entity.window="
        if ($store.sidebar.entityInfo?.type === 'Playlist') {
            window.playPlaylist ? window.playPlaylist($store.sidebar.entityInfo.id) : $store.player.playPlaylist($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Album') {
            window.playAlbum ? window.playAlbum($store.sidebar.entityInfo.id) : $store.player.playAlbum($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Genre') {
            window.playGenres ? window.playGenres($store.sidebar.entityInfo.id) : $store.player.playGenre($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Sector') {
            window.playSector ? window.playSector($store.sidebar.entityInfo.id) : $store.player.playSector($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Radio') {
            window.playRadio ? window.playRadio($store.sidebar.entityInfo.id) : $store.player.playRadio($store.sidebar.entityInfo.id);
        }
      "
      @play-all-songs.window="
        if ($event.detail.playlistId) {
            window.playPlaylist ? window.playPlaylist($event.detail.playlistId) : $store.player.playPlaylist($event.detail.playlistId);
        } else if ($event.detail.albumId) {
            window.playAlbum ? window.playAlbum($event.detail.albumId) : $store.player.playAlbum($event.detail.albumId);
        } else if ($event.detail.genreId) {
            window.playGenre ? window.playGenre($event.detail.genreId) : $store.player.playGenre($event.detail.genreId);
        }
      "
      @play-all-playlists.window="
        if ($event.detail.sectorId) {
            window.playSector ? window.playSector($event.detail.sectorId) : $store.player.playSector($event.detail.sectorId);
        }
      ">
    {{-- 🎯 GTM Body Snippet (No-Script Fallback) --}}
    <x-marketing.gtm-body />

    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    @php
        // 🚀 HYBRID: PHP initial value + Alpine SPA updates
        // Sağ sidebar gösterilecek route'lar (music pages + dashboard)
        // Mobilde (<768px) GİZLİ, Tablet+ (768px+) GÖRÜNÜR
        $showRightSidebar = in_array(Route::currentRouteName(), [
            'dashboard',
            'muzibu.home',
            'muzibu.songs.index',
            'muzibu.songs.show',
            'muzibu.albums.index',
            'muzibu.albums.show',
            'muzibu.artists.index',
            'muzibu.artists.show',
            'muzibu.playlists.index',
            'muzibu.playlists.show',
            'muzibu.genres.index',
            'muzibu.genres.show',
            'muzibu.sectors.index',
            'muzibu.sectors.show',
            'muzibu.radios.index',
            'muzibu.search',
            'muzibu.favorites',
            'muzibu.my-playlists',
            'muzibu.corporate-playlists',
            'muzibu.listening-history',
        ]);

        // Grid class'ları - PHP initial, Alpine SPA override
        // ⚡ SAĞ SIDEBAR MD+ (768px+) ekranlarda görünür (sadece mobilde GİZLİ)
        $gridColsWithSidebar = 'md:grid-cols-[1fr_280px] lg:grid-cols-[220px_1fr_280px] xl:grid-cols-[220px_1fr_320px] 2xl:grid-cols-[220px_1fr_360px]';
        $gridColsNoSidebar = 'lg:grid-cols-[220px_1fr] xl:grid-cols-[220px_1fr] 2xl:grid-cols-[220px_1fr]';
        $initialGridCols = $showRightSidebar ? $gridColsWithSidebar : $gridColsNoSidebar;
    @endphp

    {{-- Main App Grid - Hybrid: PHP initial + Alpine SPA updates --}}
    {{-- md (768px+): gap, padding, sağ sidebar başlar --}}
    {{-- lg (1024px+): sol sidebar da görünür --}}
    <div
        id="main-app-grid"
        class="grid grid-rows-[56px_1fr_auto] grid-cols-1 {{ $initialGridCols }} h-[100dvh] w-full gap-0 md:gap-3 px-0 pb-0 pt-0 md:px-3 md:pt-3"
        x-bind:class="$store.sidebar?.rightSidebarVisible
            ? '{{ $gridColsWithSidebar }}'
            : '{{ $gridColsNoSidebar }}'"
    >
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')

        {{-- Mobile Menu Overlay - Grid içinde (sidebar ile aynı stacking context) --}}
        <div class="muzibu-mobile-overlay" onclick="toggleMobileMenu()"></div>

        @include('themes.muzibu.components.main-content')

        {{-- Right Sidebar - MD+ screens (768px+), Hybrid: PHP initial + Alpine SPA --}}
        {{-- SADECE MOBİLDE GİZLİ (<768px), TABLET VE DESKTOP'TA GÖSTER (768px+) --}}
        {{-- 768px+: Sağ sidebar görünür, 1024px+: Her iki sidebar da görünür --}}
        <aside
            class="muzibu-right-sidebar row-start-2 overflow-y-auto rounded-2xl {{ $showRightSidebar ? 'hidden md:block' : 'hidden' }}"
            x-bind:class="$store.sidebar?.rightSidebarVisible ? 'md:block' : 'hidden'"
        >
            @include('themes.muzibu.components.sidebar-right')
        </aside>

        @include('themes.muzibu.components.player')
        @include('themes.muzibu.components.queue-overlay')
        @include('themes.muzibu.components.lyrics-overlay')
        @include('themes.muzibu.components.keyboard-shortcuts-overlay')
        @include('themes.muzibu.components.loading-overlay')
    </div>

    {{-- Auth Modal - REMOVED: Users now go to /login and /register pages directly --}}

    {{-- 🔐 NEW DEVICE LIMIT SYSTEM (User chooses what to do) --}}
    @include('themes.muzibu.components.device-limit-warning-modal')
    @include('themes.muzibu.components.device-selection-modal')

    {{-- Create Playlist Modal - Using theme version at bottom of page (global) --}}

    {{-- Play Limits Modals - DEVRE DIŞI (3 şarkı limiti kaldırıldı) --}}
    {{-- @include('themes.muzibu.components.play-limits-modals') --}}

    {{-- Session Check --}}
    @include('themes.muzibu.components.session-check')

    {{-- AI Chat Widget --}}
    @include('themes.muzibu.components.ai-chat-widget')

    {{-- Context Menu System --}}
    @include('themes.muzibu.components.context-menu')
    @include('themes.muzibu.components.rating-modal')
    @include('themes.muzibu.components.playlist-select-modal')
    @include('themes.muzibu.components.confirm-modal')

    {{-- 🍪 COOKIE CONSENT - Design 2 (Compact Modern) --}}
    @include('themes.muzibu.components.cookie-consent')

    {{-- ⚡ CRITICAL: Livewire MUST load BEFORE Muzibu scripts (Alpine.js dependency) --}}
    @livewireScripts

    @once
    {{-- 🎯 MODULAR JAVASCRIPT ARCHITECTURE --}}

    {{-- 1. Core Utilities (önce yükle - diğerleri bağımlı) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/safe-storage.js') }}"></script>

    {{-- 2. Alpine Store --}}
    <script src="{{ versioned_asset('themes/muzibu/js/muzibu-store.js') }}"></script>

    {{-- 3. Player Features (modular - player-core bunları spread eder) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/favorites.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/auth.js') }}"></script>
    {{-- ❌ REMOVED: keyboard.js (klavye kısayolları kaldırıldı) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/api.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/session.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/spot-player.js') }}"></script>
    <script src="{{ asset('themes/muzibu/js/player/features/spa-router.js') }}?v={{ filemtime(public_path('themes/muzibu/js/player/features/spa-router.js')) }}"></script>
    {{-- ❌ REMOVED: play-helpers.js (already loaded in HEAD) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/global-helpers.js') }}"></script>

    {{-- Context Menu System (Hybrid Approach) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/menu-builder.js') }}"></script>

    {{-- Context Menu - Handlers --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/play-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/queue-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/favorite-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/rating-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/playlist-handler.js') }}"></script>

    {{-- Context Menu - Actions (per content type) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/song-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/album-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/playlist-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/genre-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/sector-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/radio-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/artist-actions.js') }}"></script>

    {{-- Context Menu - Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/utils/action-executor.js') }}"></script>

    {{-- 4. Player Core - MOVED TO HEAD for early initialization --}}

    {{-- 5. Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/utils/muzibu-cache.js') }}"></script>

    {{-- 6. UI Components --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-toast.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-theme.js') }}"></script>

    {{-- AI Chat --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ai/tenant1001-ai-chat.js') }}"></script>

    {{-- Corporate Spots Manager (SPA Compatible) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/corporate-spots.js') }}"></script>

    {{-- 7. 🚀 SPA Router - MODULAR VERSION USED (loaded in line 211 as player feature) --}}
    {{-- OLD STANDALONE ROUTER REMOVED - Duplicate initialization fixed --}}
    @endonce

    <script>
        // 🔇 Suppress storage access errors (browser privacy/extension related)
        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason?.message?.includes('Access to storage is not allowed')) {
                event.preventDefault(); // Suppress console error
            }
        });

        // 🌐 Global Alpine defaults (ReferenceError önleme)
        // Device & Modal
        window.showKeyboardHelp = window.showKeyboardHelp || false;
        window.showDeviceSelectionModal = window.showDeviceSelectionModal || false;
        window.showDeviceLimitWarning = window.showDeviceLimitWarning || false;
        window.showDeviceLimitModal = window.showDeviceLimitModal || false;
        window.deviceTerminateLoading = window.deviceTerminateLoading || false;
        window.activeDevices = window.activeDevices || [];
        window.deviceLimit = window.deviceLimit || 1;

        // Player state
        window.isLoading = window.isLoading || false;
        window.isSongLoading = window.isSongLoading || false;
        window.isPlaying = window.isPlaying || false;
        window.currentSong = window.currentSong || null;
        window.currentTime = window.currentTime || 0;
        window.duration = window.duration || 0;
        window.progressPercent = window.progressPercent || 0;
        window.isLiked = window.isLiked || false;

        // Playback controls
        window.shuffle = window.shuffle || false;
        window.repeatMode = window.repeatMode || 'off';

        // Volume & Audio
        window.volume = window.volume ?? 80;
        window.isMuted = window.isMuted || false;

        // Stream info
        window.currentStreamType = window.currentStreamType || 'hls';
        window.lastFallbackReason = window.lastFallbackReason || null;

        // UI panels & Debug
        window.showLyrics = window.showLyrics || false;
        window.showQueue = window.showQueue || false;
        window.showDebugInfo = window.showDebugInfo || false;

        // Auth (fallback - overwritten by config below)
        window.isLoggedIn = window.isLoggedIn || false;
        window.currentUser = window.currentUser || null;

        // Helper functions
        window.formatTime = window.formatTime || function(sec) {
            const t = Math.max(0, Math.floor(sec || 0));
            const m = Math.floor(t / 60);
            const s = (t % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        };

        // Config for Alpine.js
        window.muzibuPlayerConfig = {
            lang: @json(tenant_lang('player')),
            frontLang: @json(tenant_lang('front')),
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: @if(auth()->check())
                @php
                    $user = auth()->user();

                    // 🔴 TEK KAYNAK: users.subscription_expires_at
                    $subscriptionExpiresAt = $user->subscription_expires_at;
                    $subscriptionEndsAt = $subscriptionExpiresAt?->toIso8601String();

                    // 🔥 Device limit (backend'den al - 3-tier hierarchy)
                    $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
                    $deviceLimit = $deviceService->getDeviceLimit($user);
                @endphp
                {
                id: {{ $user->id }},
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                is_premium: {{ $user->isPremium() ? 'true' : 'false' }},
                is_root: {{ $user->hasRole('root') ? 'true' : 'false' }},
                subscription_ends_at: {!! $subscriptionEndsAt ? '"' . $subscriptionEndsAt . '"' : 'null' !!}
            }
            @else
                null
            @endif,
            {{-- todayPlayedCount kaldırıldı - 3 şarkı limiti devre dışı --}}
            tenantId: {{ tenant('id') }},
            // 🔥 Config values (Muzibu module)
            @if(auth()->check())
                deviceLimit: {{ $deviceLimit ?? 1 }},
            @else
                deviceLimit: 1,
            @endif
            sessionPollingInterval: {{ config('muzibu.session.polling_interval', 30000) }},
            crossfadeDuration: {{ config('muzibu.player.crossfade_duration', 4000) }}
        };

        // 🔐 CSRF Token Auto-Renewal (419 hatası önleme)
        if (typeof axios !== 'undefined') {
            // Axios CSRF interceptor
            axios.interceptors.response.use(
                response => response,
                async error => {
                    // CSRF token mismatch (419)
                    if (error.response?.status === 419) {
                        console.warn('🔐 CSRF token expired, refreshing...');

                        try {
                            // Yeni token al
                            await axios.get('/sanctum/csrf-cookie');

                            // Meta tag güncelle
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            if (token) {
                                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                            }

                            // Orijinal isteği tekrar gönder
                            return axios(error.config);
                        } catch (refreshError) {
                            console.error('❌ CSRF token refresh failed:', refreshError);
                            return Promise.reject(error);
                        }
                    }
                    return Promise.reject(error);
                }
            );

            // Initial CSRF token setup
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
        }

        // Mobile Menu Toggle - Enhanced for Mobile & Tablet
        function toggleMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            const hamburger = document.getElementById('hamburgerIcon');
            const isOpen = sidebar.classList.contains('active');

            if (isOpen) {
                // Close
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                if (hamburger) hamburger.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                // Open
                sidebar.classList.add('active');
                overlay.classList.add('active');
                if (hamburger) hamburger.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            const hamburger = document.getElementById('hamburgerIcon');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            if (hamburger) hamburger.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ESC key to close mobile menu
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('leftSidebar');
                if (sidebar && sidebar.classList.contains('active')) {
                    closeMobileMenu();
                }
            }
        });

        // Close mobile menu when clicking on nav links (SPA friendly)
        document.addEventListener('click', (e) => {
            const link = e.target.closest('#leftSidebar a[href]');
            if (link && window.innerWidth < 1024) {
                closeMobileMenu();
            }
        });

        // Close mobile menu on resize to desktop (fixes overlay stuck bug)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (window.innerWidth >= 1024) {
                    closeMobileMenu();
                }
            }, 100);
        });
    </script>

    {{-- 🎯 Livewire Navigation Hook - Alpine Re-Init --}}
    <script>
        // ✅ FIX: Prevent Alpine.js multiple initialization ($nextTick redefine error)
        document.addEventListener('livewire:navigated', () => {
            if (!window.Alpine) return;

            // 🎯 Use Alpine's built-in mutateDom to safely initialize new components
            // This prevents magic property redefinition errors
            setTimeout(() => {
                window.Alpine.mutateDom(() => {
                    // Only initialize uninitialized elements
                    document.querySelectorAll('[x-data]:not([data-alpine-initialized])').forEach(el => {
                        try {
                            window.Alpine.initTree(el);
                            el.setAttribute('data-alpine-initialized', 'true');
                        } catch (e) {
                            // Silently ignore already initialized elements
                            if (!e.message?.includes('redefine') && !e.message?.includes('already')) {
                                console.warn('Alpine init warning:', e.message);
                            }
                        }
                    });
                });
            }, 50);
        });

        // 🎵 Player Store Registration - ULTIMATE FIX (Proxy Pattern - auto-forward everything)
        // Strategy: Use JavaScript Proxy to auto-forward ALL properties/methods to root $data
        document.addEventListener('alpine:initialized', () => {
            const htmlEl = document.querySelector('html');

            // Wait for Alpine to fully initialize the root component
            setTimeout(() => {
                const getRootData = () => {
                    // Try multiple ways to get root data
                    if (htmlEl._x_dataStack && htmlEl._x_dataStack[0]) {
                        return htmlEl._x_dataStack[0];
                    }
                    // Fallback: Alpine might expose it differently
                    return window.Alpine?.$data?.(htmlEl);
                };

                // Create Proxy that forwards everything to root
                const playerProxy = new Proxy({}, {
                    get(target, prop) {
                        const rootData = getRootData();
                        if (!rootData) {
                            console.error('❌ Root data not accessible, prop:', prop);
                            return undefined;
                        }

                        const value = rootData[prop];

                        // Debug log for missing methods
                        if (value === undefined && (prop === 'playPlaylist' || prop === 'playAlbum' || prop === 'playGenre')) {
                            console.error(`❌ Method ${prop} not found in root data. Available methods:`, Object.keys(rootData).filter(k => typeof rootData[k] === 'function'));
                        }

                        // If it's a function, bind it to root context
                        if (typeof value === 'function') {
                            return value.bind(rootData);
                        }

                        return value;
                    },
                    set(target, prop, value) {
                        const rootData = getRootData();
                        if (rootData) {
                            rootData[prop] = value;
                            return true;
                        }
                        return false;
                    }
                });

                window.Alpine.store('player', playerProxy);
            }, 100); // Small delay to ensure Alpine root is ready
        });

        // Context menu store kontrolü
        document.addEventListener('alpine:initialized', () => {
            if (!window.Alpine.store('contextMenu')) {
                console.error('❌ Context Menu Store not found!');
            }

            // Player store da kontrol et
            if (!window.Alpine.store('player')) {
                console.error('❌ Player Store not found!');
            }
        });
    </script>

    @once
    {{-- 🎯 Context Menu Init - SPA Safe --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menu/init.js') }}"></script>
    @endonce

    @once
    {{-- 🎯 Alpine helper: horizontalScroll (SPA route changes için global) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.data('horizontalScroll')) {
                Alpine.data('horizontalScroll', () => ({
                    scrollContainer: null,
                    scrollInterval: null,
                    init() {
                        this.scrollContainer = this.$refs.scrollContainer;
                    },
                    scrollLeft() {
                        this.scrollContainer?.scrollBy({ left: -400, behavior: 'smooth' });
                    },
                    scrollRight() {
                        this.scrollContainer?.scrollBy({ left: 400, behavior: 'smooth' });
                    },
                    startAutoScroll(direction) {
                        this.scrollInterval = setInterval(() => {
                            this.scrollContainer?.scrollBy({ left: direction === 'right' ? 20 : -20 });
                        }, 50);
                    },
                    stopAutoScroll() {
                        if (this.scrollInterval) {
                            clearInterval(this.scrollInterval);
                            this.scrollInterval = null;
                        }
                    }
                }));
            }
        });
    </script>
    @endonce

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />

    {{-- Create Playlist Modal (Global - SPA Compatible) --}}
    @include('themes.muzibu.components.create-playlist-modal')

    {{-- Create Playlist Modal Alpine.js Component (SPA Safe) --}}
    <script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.data('createPlaylistModal')) {
            Alpine.data('createPlaylistModal', () => ({
                open: false,
                loading: false,
                title: '',
                description: '',
                isPublic: true,

                openModal() {
                    this.open = true;
                    this.title = '';
                    this.description = '';
                    this.isPublic = true;
                },

                closeModal() {
                    this.open = false;
                },

                async createPlaylist() {
                    if (!this.title.trim()) return;

                    this.loading = true;

                    try {
                        const response = await fetch('/api/muzibu/playlists/quick-create', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title: this.title,
                                description: this.description,
                                is_public: this.isPublic,
                                song_ids: []
                            })
                        });

                        const data = await response.json();

                        if (data.success || data.playlist) {
                            const newPlaylist = data.playlist || data.data;

                            if (window.$store?.toast) {
                                window.$store.toast.show('Playlist oluşturuldu!', 'success');
                            }
                            this.closeModal();

                            // 🎯 SPA: Dispatch event for playlist-created
                            window.dispatchEvent(new CustomEvent('playlist-created', {
                                detail: { playlist: newPlaylist }
                            }));

                            // 🎯 Check if playlistModal had pending context (song/album adding flow)
                            const pendingContext = window._playlistModalPendingContext;
                            if (pendingContext && pendingContext.contentType) {
                                // Clear pending context
                                window._playlistModalPendingContext = null;

                                // Reopen playlistModal with the same content
                                const playlistModal = Alpine.store('playlistModal');
                                if (playlistModal) {
                                    setTimeout(() => {
                                        if (pendingContext.contentType === 'song') {
                                            playlistModal.showForSong(pendingContext.contentId, pendingContext.contentData);
                                        } else if (pendingContext.contentType === 'album') {
                                            playlistModal.showForAlbum(pendingContext.contentId, pendingContext.contentData);
                                        }
                                    }, 300);
                                }
                            } else {
                                // No pending context - check if on my-playlists page
                                const currentPath = window.location.pathname;
                                if (currentPath.includes('my-playlists')) {
                                    setTimeout(() => window.location.reload(), 500);
                                }
                            }
                        } else {
                            throw new Error(data.message || 'Bir hata oluştu');
                        }
                    } catch (error) {
                        if (window.$store?.toast) {
                            window.$store.toast.show(error.message || 'Bir hata oluştu', 'error');
                        } else {
                            alert(error.message || 'Bir hata oluştu');
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        }
    });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
@endif
