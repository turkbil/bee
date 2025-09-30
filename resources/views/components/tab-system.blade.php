<div class="card-header" style="padding-bottom: 0px;">
    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" id="dynamic-tabs" wire:ignore>
        @foreach($tabs as $index => $tab)
        <li class="nav-item">
            <a href="#{{ $index }}"
               class="nav-link {{ $index === 0 ? 'active' : '' }}"
               data-bs-toggle="tab"
               data-tab-key="{{ $index }}">
                <i class="{{ $tab['icon'] ?? 'fas fa-file' }} me-2"></i>{{ $tab['name'] ?? $tab['title'] ?? 'Tab' }}
            </a>
        </li>
        @endforeach

        {{ $slot ?? '' }}
    </ul>
</div>

@push('scripts')
<script>
// ✅ UNIFIED TAB MANAGEMENT SYSTEM
// Bu sistem manage.js ile koordine çalışır - conflict yok!
document.addEventListener('DOMContentLoaded', function() {
    const storageKey = '{{ $storageKey }}';

    console.log('📋 Unified Tab System başlatılıyor, storage key:', storageKey);

    // 🎯 GLOBAL TAB MANAGER ENTEGRASYONU
    // manage.js'te bulunan sistemle uyumlu çalışır
    if (typeof window.setupUnifiedTabSystem === 'function') {
        console.log('✅ manage.js TabSystem entegrasyonu - delegate to manage.js');
        return; // manage.js handle etsin
    }

    // 🔄 LIVEWIRE UPDATE SONRASI TAB PERSISTENCE WITH LOOP PREVENTION
    let restoreTabInProgress = false;

    function restoreActiveTab() {
        if (restoreTabInProgress) {
            console.log('🔒 Tab restore already in progress, skipping to prevent loop');
            return;
        }

        restoreTabInProgress = true;
        console.log('🔄 Tab restore işlemi başlıyor...');

        const savedTab = localStorage.getItem(storageKey);
        console.log('📋 Kaydedilmiş tab:', savedTab);

        if (savedTab) {
            const targetTab = document.querySelector(`[href="${savedTab}"]`);
            const targetPane = document.getElementById(savedTab.replace('#', ''));

            console.log('🎯 Hedef tab element:', targetTab);
            console.log('🎯 Hedef pane element:', targetPane);

            if (targetTab && targetPane) {
                // 🚨 KRİTİK FİX: DOM değişikliklerini Livewire morph sisteminden gizle
                // Livewire'ın DOM tracking'ini geçici olarak durdur
                const livewireIgnore = document.createElement('div');
                livewireIgnore.setAttribute('wire:ignore', '');

                // Tüm tab'ları deaktif et
                document.querySelectorAll('.nav-link').forEach(tab => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Hedef tab'ı aktif et - Livewire morph'u tetiklemesin
                targetTab.classList.add('active');
                targetTab.setAttribute('aria-selected', 'true');
                targetPane.classList.add('show', 'active');

                console.log('✅ Tab restore tamamlandı:', savedTab);

                // INSTANT: Dil senkronizasyonu - NO setTimeout
                if (window.currentLanguage) {
                    safeSwitchLanguageContent(window.currentLanguage);
                }
            } else {
                console.log('❌ Tab/Pane bulunamadı, varsayılan aktif');
                // Varsayılan tab'ı aktif et
                const firstTab = document.querySelector('.nav-link');
                const firstPane = document.querySelector('.tab-pane');
                if (firstTab && firstPane) {
                    firstTab.classList.add('active');
                    firstPane.classList.add('show', 'active');

                    // INSTANT: Dil senkronizasyonu - NO setTimeout
                    if (window.currentLanguage) {
                        safeSwitchLanguageContent(window.currentLanguage);
                    }
                }
            }
        } else {
            console.log('📋 Kaydedilmiş tab yok, ilk tab aktif');
            // İlk tab'ı aktif et
            const firstTab = document.querySelector('.nav-link');
            const firstPane = document.querySelector('.tab-pane');
            if (firstTab && firstPane) {
                firstTab.classList.add('active');
                firstPane.classList.add('show', 'active');

                // INSTANT: Dil senkronizasyonu - NO setTimeout
                if (window.currentLanguage) {
                    safeSwitchLanguageContent(window.currentLanguage);
                }
            }
        }

        // Reset flag after completion
        setTimeout(() => {
            restoreTabInProgress = false;
        }, 50);
    }

    // İlk yükleme
    restoreActiveTab();

    // Tab click events - kaydetme
    function attachTabEvents() {
        document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(tab => {
            // Duplicate event prevent
            if (tab.dataset.tabEventAttached) return;
            tab.dataset.tabEventAttached = 'true';

            tab.addEventListener('click', function(e) {
                const tabHref = this.getAttribute('href');
                console.log('📋 Tab tıklandı, kaydediliyor:', tabHref);
                localStorage.setItem(storageKey, tabHref);

                // INSTANT: Dil senkronizasyonu - NO setTimeout
                if (window.currentLanguage && window.switchLanguageContent) {
                    window.switchLanguageContent(window.currentLanguage);
                }
            });
        });
    }

    // İlk attach
    attachTabEvents();

    // 🚀 LIVEWIRE UPDATE HOOK - OPTIMIZED FOR RAPID SAVES
    let livewireHookInProgress = false;
    let lastLivewireUpdate = 0;

    if (typeof Livewire !== 'undefined') {
        // Livewire 3.x - MINIMAL THROTTLE for rapid saves
        Livewire.hook('morph.updated', () => {
            const now = Date.now();

            // Minimal throttle - sadece 50ms (yüzlerce kayıt için yeterli)
            if (livewireHookInProgress || (now - lastLivewireUpdate) < 50) {
                return;
            }

            livewireHookInProgress = true;
            lastLivewireUpdate = now;

            // Immediate execution - NO setTimeout
            restoreActiveTab();
            attachTabEvents();
            livewireHookInProgress = false;
        });

        // Livewire 2.x fallback - DISABLED (causing infinite loops)
        // document.addEventListener('livewire:updated', () => {
        //     console.log('🔄 Livewire updated - DISABLED to prevent infinite loops');
        // });

        // Component load - ONCE ONLY
        let componentLoadExecuted = false;
        document.addEventListener('livewire:load', () => {
            if (componentLoadExecuted) {
                console.log('🔒 Livewire load - already executed, skipping');
                return;
            }
            componentLoadExecuted = true;
            console.log('🔄 Livewire load - tab restore (once only)');
            setTimeout(() => {
                restoreActiveTab();
                attachTabEvents();
            }, 100);
        });
    }

    // 🎯 GLOBAL FUNCTION EXPORT WITH LOOP PREVENTION
    let tabRestoreInProgress = false;
    let languageSyncInProgress = false;

    // Wrapper function to prevent language sync loops
    function safeSwitchLanguageContent(language) {
        if (languageSyncInProgress) {
            console.log('🔒 Language sync already in progress, skipping to prevent loop');
            return;
        }

        languageSyncInProgress = true;
        console.log('🔄 Tab restore sonrası dil senkronizasyon:', language);

        if (window.switchLanguageContent) {
            window.switchLanguageContent(language);
        }

        setTimeout(() => {
            languageSyncInProgress = false;
        }, 50); // Short reset delay
    }

    window.forceTabRestore = function() {
        if (tabRestoreInProgress) {
            console.log('🔒 Tab restore already in progress, skipping to prevent loop');
            return;
        }

        tabRestoreInProgress = true;
        console.log('🔄 Manual tab restore tetiklendi');

        // 🚨 ULTRA SAFE TAB RESTORE: Multiple attempts with fallbacks
        const maxAttempts = 3;
        let attempt = 1;

        function attemptRestore() {
            console.log(`🎯 Tab restore attempt ${attempt}/${maxAttempts}`);

            const savedTab = localStorage.getItem(storageKey) || '#1';
            const targetTab = document.querySelector(`[href="${savedTab}"]`);
            const targetPane = document.getElementById(savedTab.replace('#', ''));

            if (targetTab && targetPane) {
                // Force remove all active states
                document.querySelectorAll('.nav-link').forEach(tab => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Force activate target tab
                targetTab.classList.add('active');
                targetTab.setAttribute('aria-selected', 'true');
                targetPane.classList.add('show', 'active');

                console.log(`✅ Tab restore attempt ${attempt} SUCCESS`);
                return true;
            } else if (attempt < maxAttempts) {
                attempt++;
                setTimeout(attemptRestore, 100);
                return false;
            } else {
                console.log('❌ Tab restore failed after all attempts, using fallback');
                // Final fallback: activate first tab
                const firstTab = document.querySelector('.nav-link');
                const firstPane = document.querySelector('.tab-pane');
                if (firstTab && firstPane) {
                    firstTab.classList.add('active');
                    firstPane.classList.add('show', 'active');
                }
                return false;
            }
        }

        attemptRestore();
        attachTabEvents();

        // Dil senkronizasyonu - LOOP PREVENTION
        setTimeout(() => {
            if (window.currentLanguage) {
                safeSwitchLanguageContent(window.currentLanguage);
            }
            tabRestoreInProgress = false; // Reset flag after completion
        }, 300); // Increased timeout for multiple attempts
    };
});
</script>
@endpush