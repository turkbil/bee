<div>
    <!-- AI Content Builder Modal -->
    <div class="modal fade {{ $isOpen ? 'show' : '' }}"
         tabindex="-1"
         style="{{ $isOpen ? 'display: block;' : 'display: none;' }}"
         aria-labelledby="aiContentBuilderLabel"
         aria-hidden="{{ $isOpen ? 'false' : 'true' }}">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-magic fs-4 me-2"></i>
                        <h5 class="modal-title mb-0" id="aiContentBuilderLabel">AI İçerik Üretici</h5>
                    </div>
                    <button type="button" wire:click="close" class="btn-close btn-close-white" aria-label="Close"></button>
                </div>

        <!-- Tema Bilgisi -->
        <div class="bg-light p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Tema: {{ $themePreview['theme_name'] ?? 'Default' }}</small>
                <div class="d-flex gap-1">
                    <span class="badge rounded-circle" style="width: 20px; height: 20px; background-color: {{ $themePreview['primary_color'] ?? '#3B82F6' }}"></span>
                    <span class="badge rounded-circle" style="width: 20px; height: 20px; background-color: {{ $themePreview['secondary_color'] ?? '#6B7280' }}"></span>
                </div>
            </div>
        </div>

                <!-- Content -->
                <div class="modal-body">

            <!-- Prompt Input -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-edit me-1"></i>
                    Ne oluşturmak istersiniz?
                </label>
                <textarea
                    wire:model.lazy="userPrompt"
                    wire:keyup.debounce.500ms="updatePrompt"
                    class="form-control"
                    rows="4"
                    placeholder="Örn: Hero section, 3 kolonlu özellikler, fiyatlandırma tablosu..."
                    {{ $isGenerating ? 'disabled' : '' }}></textarea>
                <small class="form-text text-muted">Boş bırakırsanız sayfa başlığına göre içerik üretilir</small>
            </div>


            <!-- İçerik Uzunluğu -->
            <div class="mb-3">
                <label class="form-label fw-semibold">İçerik Uzunluğu</label>
                <div class="btn-group w-100" role="group">
                    <button type="button"
                            wire:click="$set('contentLength', 'short')"
                            class="btn {{ $contentLength === 'short' ? 'btn-primary' : 'btn-outline-primary' }}"
                            {{ $isGenerating ? 'disabled' : '' }}>
                        Kısa
                    </button>
                    <button type="button"
                            wire:click="$set('contentLength', 'medium')"
                            class="btn {{ $contentLength === 'medium' ? 'btn-primary' : 'btn-outline-primary' }}"
                            {{ $isGenerating ? 'disabled' : '' }}>
                        Orta
                    </button>
                    <button type="button"
                            wire:click="$set('contentLength', 'long')"
                            class="btn {{ $contentLength === 'long' ? 'btn-primary' : 'btn-outline-primary' }}"
                            {{ $isGenerating ? 'disabled' : '' }}>
                        Uzun
                    </button>
                </div>
            </div>

            <!-- Gelişmiş Ayarlar -->
            <div class="mb-3">
                <div class="accordion" id="advancedSettings">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseAdvanced" aria-expanded="false">
                                <i class="fas fa-cog me-2"></i>
                                Gelişmiş Ayarlar
                            </button>
                        </h2>
                        <div id="collapseAdvanced" class="accordion-collapse collapse" data-bs-parent="#advancedSettings">
                            <div class="accordion-body">
                                <textarea
                                    wire:model="customInstructions"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Özel talimatlar... (opsiyonel)"
                                    {{ $isGenerating ? 'disabled' : '' }}></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Önizleme Alanı -->
            @if($generatedContent)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Üretilen İçerik</span>
                        <button type="button" wire:click="regenerate" class="btn btn-sm btn-link text-primary p-0">
                            <i class="fas fa-redo me-1"></i>
                            Yeniden Üret
                        </button>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        {!! $generatedContent !!}
                    </div>
                </div>
            @endif
            <!-- Kredi Bilgisi -->
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                <span>
                    <i class="fas fa-coins me-1"></i>
                    Kredi: <strong>{{ $creditsAvailable }}</strong>
                </span>
                <span class="text-muted">
                    Tahmini: -{{ $estimatedCredits }}
                </span>
            </div>

            <!-- Uyarı Mesajı -->
            <div class="alert alert-warning d-flex align-items-center mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Önemli:</strong> İçerik üretildiğinde otomatik olarak editöre eklenecek ve
                    <span class="text-danger fw-bold">mevcut içerik silinecektir!</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <button type="button"
                        onclick="startAIGeneration()"
                        class="btn btn-primary btn-lg"
                        id="generateButton"
                        {{ $creditsAvailable < $estimatedCredits ? 'disabled' : '' }}>
                    <i class="fas fa-wand-magic-sparkles me-2"></i>
                    İçerik Oluştur ve Editöre Ekle
                </button>

                <button type="button" wire:click="close" class="btn btn-outline-secondary">
                    Kapat
                </button>
            </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    @if($isOpen)
        <div class="modal-backdrop fade show" wire:click="close"></div>
    @endif
</div>

@push('scripts')
<script>
// jQuery modal fonksiyonları - Bootstrap 5 uyumlu
$.fn.modal = $.fn.modal || function(options) {
    return this.each(function() {
        const $modal = $(this);

        if (typeof options === 'string') {
            if (options === 'show') {
                $modal.addClass('show').css('display', 'block');
                $('body').addClass('modal-open');

                // Backdrop ekle
                if (!$('.modal-backdrop').length) {
                    $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                }
            } else if (options === 'hide') {
                $modal.removeClass('show').css('display', 'none');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }
        } else if (options && options.show) {
            $(this).modal('show');
        }
    });
};

// AI İçerik üretim başlat
function startAIGeneration() {
    console.log('🚀 AI içerik üretimi başlatılıyor...');

    // Progress modal göster
    showProgressModal();

    // Livewire metodunu çağır
    @this.generateContent();
}

// Progress modal göster
function showProgressModal() {
    // Overlay HTML - Translation component tarzı
    const overlayHtml = `
        <div id="aiGenerationOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
             style="background-color: rgba(0,0,0,0.7); z-index: 9999;">
            <div class="bg-white rounded-4 p-5 text-center" style="min-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="mb-4">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
                <h4 class="mb-3">AI İçerik Üretiliyor</h4>
                <p class="text-muted mb-4">
                    Lütfen bekleyin, içeriğiniz hazırlanıyor...<br>
                    <small>Bu işlem 5-10 saniye sürebilir.</small>
                </p>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         role="progressbar"
                         style="width: 100%">
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        İçerik hazır olduğunda otomatik olarak editöre eklenecek
                    </small>
                </div>
            </div>
        </div>
    `;

    // Overlay yoksa ekle
    if (!$('#aiGenerationOverlay').length) {
        $('body').append(overlayHtml);
    }

    // Body scroll'ı kapat
    $('body').css('overflow', 'hidden');
}

// Progress overlay kapat
function hideProgressModal() {
    const $overlay = $('#aiGenerationOverlay');
    if ($overlay.length) {
        // Fade out animasyonu ile kapat
        $overlay.fadeOut(300, function() {
            $(this).remove();
        });
    }

    // Body scroll'ı geri aç
    $('body').css('overflow', '');
}

function contentBuilder() {
    return {
        init() {
            // Event listeners
            window.addEventListener('content-builder-opened', () => {
                document.body.style.overflow = 'hidden';
            });

            window.addEventListener('content-builder-closed', () => {
                document.body.style.overflow = '';
            });

            window.addEventListener('show-content-preview', (event) => {
                // İçerik önizleme modalini aç
                console.log('Content preview:', event.detail.content);
            });

            window.addEventListener('show-toast', (event) => {
                // Toast mesajı göster
                const { type, message } = event.detail;
                // Progress modal'ı kapat
                hideProgressModal();

                // SweetAlert toast göster
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type === 'success' ? 'success' : 'error',
                        title: type === 'success' ? 'Başarılı!' : 'Hata!',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        }
    }
}

// Editor entegrasyonu - ESKİ İÇERİĞİ SİL VE YENİSİNİ EKLE
Livewire.on('replaceContentInEditor', (event) => {
    const { field, content } = event.detail || event;

    console.log('🔄 Editör içeriği tamamen değiştiriliyor:', field);
    console.log('📊 Gelen içerik bilgileri:', {
        field: field,
        contentLength: content ? content.length : 0,
        contentPreview: content ? content.substring(0, 200) + '...' : 'BOŞ İÇERİK',
        eventDetail: event.detail,
        fullEvent: event
    });

    // Modern Tailwind içeriği hazırla
    const processedContent = processContentForEditor(content);
    console.log('🎨 İşlenmiş içerik:', {
        processedLength: processedContent.length,
        processedPreview: processedContent.substring(0, 200) + '...'
    });

    // HugeRTE'ye ekle - ADVANCED DEBUG
    console.log('🔎 ULTRA DEBUG: Editör aramaya başlanıyor...');
    console.log('🔎 Field parameter:', field);
    console.log('🔎 Content length:', content ? content.length : 0);
    console.log('🔎 Event detail full:', event);

    // Tüm textarea'ları listele
    const allTextareas = document.querySelectorAll('textarea');
    console.log('📋 Tüm textarea\'lar (' + allTextareas.length + ' adet):');
    allTextareas.forEach((ta, index) => {
        console.log(`  ${index}: ID="${ta.id}", Name="${ta.name}", Classes="${ta.className}"`);
    });

    // Mümkün tüm selektorörleri dene
    const selectors = [
        `textarea[name="${field}"]`,
        `#${field}`,
        `textarea#${field}`,
        'textarea.hugerteEditor',
        'textarea[id*="body"]',
        'textarea[name*="body"]',
        'textarea[id*="editor"]',
        'textarea[name*="editor"]'
    ];

    let targetTextarea = null;
    for (const selector of selectors) {
        const found = document.querySelector(selector);
        console.log(`🔍 Selektor "${selector}": ${found ? 'BULUNDU (' + found.id + ')' : 'YOK'}`);
        if (found && !targetTextarea) {
            targetTextarea = found;
        }
    }

    // HugeRTE container'larını ara
    const containers = document.querySelectorAll('.hugerteContainer, [id*="hugerte"], [class*="hugerte"]');
    console.log('📋 HugeRTE container\'lar (' + containers.length + ' adet):', containers);

    setTimeout(() => {
        if (targetTextarea) {
            console.log('✅ HEDEF TEXTAREA BULUNDU:', {
                id: targetTextarea.id,
                name: targetTextarea.name,
                tagName: targetTextarea.tagName,
                className: targetTextarea.className
            });

            // Mevcut içeriği kaydet
            const currentContent = targetTextarea.value;
            console.log('🗑️ MEVCUT İÇERİK:', {
                length: currentContent.length,
                preview: currentContent.substring(0, 200) + '...'
            });

            // YENİ İÇERİĞİ EKLE
            console.log('📝 YENİ İÇERİK EKLENİYOR:', {
                newLength: processedContent.length,
                newPreview: processedContent.substring(0, 200) + '...'
            });

            targetTextarea.value = processedContent;
            console.log('✏️ Textarea.value set edildi');

            // HugeRTE iframe'ini bul ve güncelle
            const parentContainer = targetTextarea.closest('.hugerteContainer') ||
                                  targetTextarea.closest('[id*="hugerte"]') ||
                                  targetTextarea.parentElement;

            console.log('📋 Parent container:', parentContainer);

            if (parentContainer) {
                const iframe = parentContainer.querySelector('iframe') ||
                             parentContainer.querySelector('iframe.hugerteEditor');
                console.log('🖼️ Iframe bulundu:', iframe);

                if (iframe && iframe.contentDocument) {
                    console.log('📄 Iframe content document mevcut, güncelleniyor...');
                    iframe.contentDocument.body.innerHTML = processedContent;
                    console.log('✅ Iframe içeriği güncellendi');
                } else {
                    console.log('⚠️ Iframe content document yok veya erişilemez');
                }
            }

            // Event'leri tetikle
            console.log('🔥 Event\'ler tetikleniyor...');
            targetTextarea.dispatchEvent(new Event('change', { bubbles: true }));
            targetTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            targetTextarea.dispatchEvent(new Event('keyup', { bubbles: true }));
            console.log('✅ Change, input ve keyup eventleri tetiklendi');

            // HugeRTE'ye özel event varsa tetikle
            if (window.hugerteUpdate && typeof window.hugerteUpdate === 'function') {
                window.hugerteUpdate(targetTextarea.id);
                console.log('📄 HugeRTE update fonksiyonu çağrıldı');
            }

            // KRİTİK: Livewire sync tetikle
            console.log('🔌 Livewire sync tetikleniyor...');

            // Livewire component'ı bul ve sync et
            const livewireComponent = targetTextarea.closest('[wire\\:id]');
            if (livewireComponent) {
                const wireId = livewireComponent.getAttribute('wire:id');
                console.log('🔌 Livewire component bulundu:', wireId);

                // Wire modeli güncelle
                if (window.Livewire && window.Livewire.find) {
                    const component = window.Livewire.find(wireId);
                    if (component) {
                        console.log('🔌 Livewire component sync ediliyor...');

                        // HugeRTE alanının wire:model'ini bul - multiLangInputs formatında
                        const fieldName = targetTextarea.id.includes('_tr_') ? 'multiLangInputs.tr.body' :
                                        targetTextarea.id.includes('_en_') ? 'multiLangInputs.en.body' :
                                        targetTextarea.id.includes('_ar_') ? 'multiLangInputs.ar.body' : 'multiLangInputs.tr.body';

                        console.log('🔌 Field name belirlendi:', fieldName);

                        // Component data'yı güncelle - ÇOKLU YÖNTEM
                        let syncSuccess = false;

                        // Yöntem 1: component.set()
                        try {
                            component.set(fieldName, processedContent);
                            console.log('✅ Yöntem 1: component.set() başarılı');
                            syncSuccess = true;
                        } catch (error) {
                            console.log('⚠️ Yöntem 1 başarısız:', error.message);
                        }

                        // Yöntem 2: Direct property update - multiLangInputs için özel
                        if (!syncSuccess) {
                            try {
                                if (fieldName.includes('multiLangInputs')) {
                                    const parts = fieldName.split('.');
                                    if (parts.length === 3) { // multiLangInputs.tr.body
                                        const lang = parts[1];
                                        const field = parts[2];

                                        if (!component.data.multiLangInputs) component.data.multiLangInputs = {};
                                        if (!component.data.multiLangInputs[lang]) component.data.multiLangInputs[lang] = {};
                                        component.data.multiLangInputs[lang][field] = processedContent;
                                    }
                                } else if (fieldName.includes('.')) {
                                    const [obj, prop] = fieldName.split('.');
                                    if (!component.data[obj]) component.data[obj] = {};
                                    component.data[obj][prop] = processedContent;
                                } else {
                                    component.data[fieldName] = processedContent;
                                }
                                console.log('✅ Yöntem 2: Direct property başarılı');
                                syncSuccess = true;
                            } catch (error) {
                                console.log('⚠️ Yöntem 2 başarısız:', error.message);
                            }
                        }

                        // Yöntem 3: Force call ile update
                        if (!syncSuccess) {
                            try {
                                component.call('updateBody', {
                                    language: fieldName.includes('_tr_') ? 'tr' :
                                             fieldName.includes('_en_') ? 'en' :
                                             fieldName.includes('_ar_') ? 'ar' : 'tr',
                                    content: processedContent
                                });
                                console.log('✅ Yöntem 3: Force call başarılı');
                                syncSuccess = true;
                            } catch (error) {
                                console.log('⚠️ Yöntem 3 başarısız:', error.message);
                            }
                        }

                        // Yöntem 4: Global refresh
                        if (syncSuccess) {
                            // Başarılı sync sonrası yenile
                            setTimeout(() => {
                                component.call('$refresh');
                                console.log('✅ Component refresh edildi');
                            }, 100);
                        }
                    }
                }
            }

            // Ekstra: Tüm wire:model tetikleyicilerini çalıştır
            const wireModelElements = document.querySelectorAll('[wire\\:model*="body"]');
            wireModelElements.forEach(el => {
                if (el.id === targetTextarea.id || el === targetTextarea) {
                    console.log('🔌 Wire:model elementi güncelleniyor:', el.id);
                    el.dispatchEvent(new Event('blur', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            // Son güvenlik: Global Livewire sync
            if (window.Livewire && window.Livewire.rescan) {
                setTimeout(() => {
                    window.Livewire.rescan();
                    console.log('🔌 Global Livewire rescan yapıldı');
                }, 500);
            }

            console.log('✅ Livewire sync işlemleri tamamlandı');

            // Progress modal'ı kapat
            hideProgressModal();

            // Kontrol - gerçekten değişti mi?
            setTimeout(() => {
                const finalContent = targetTextarea.value;
                console.log('🔍 FİNAL KONTROL:', {
                    finalLength: finalContent.length,
                    changed: finalContent !== currentContent,
                    matchesNew: finalContent === processedContent
                });

                // Livewire state kontrolü
                const livewireComponent = targetTextarea.closest('[wire\\:id]');
                if (livewireComponent && window.Livewire) {
                    const wireId = livewireComponent.getAttribute('wire:id');
                    const component = window.Livewire.find(wireId);
                    if (component) {
                        const fieldName = targetTextarea.id.includes('_tr_') ? 'multiLangInputs.tr.body' :
                                        targetTextarea.id.includes('_en_') ? 'multiLangInputs.en.body' :
                                        targetTextarea.id.includes('_ar_') ? 'multiLangInputs.ar.body' : 'multiLangInputs.tr.body';

                        let livewireValue = '';
                        if (fieldName.includes('multiLangInputs')) {
                            const parts = fieldName.split('.');
                            if (parts.length === 3) {
                                const lang = parts[1];
                                const field = parts[2];
                                livewireValue = component.data.multiLangInputs?.[lang]?.[field] || '';
                            }
                        } else if (fieldName.includes('.')) {
                            const [obj, prop] = fieldName.split('.');
                            livewireValue = component.data[obj] ? component.data[obj][prop] : '';
                        } else {
                            livewireValue = component.data[fieldName] || '';
                        }

                        console.log('🔍 LIVEWIRE STATE KONTROL:', {
                            fieldName,
                            livewireLength: livewireValue.length,
                            textareaLength: finalContent.length,
                            synced: livewireValue === finalContent
                        });

                        if (livewireValue !== finalContent) {
                            console.log('⚠️ LIVEWIRE SYNC HATASI - Tekrar deneniyor...');
                            // Son deneme: Tüm yöntemleri dene
                            try {
                                component.set(fieldName, finalContent);
                            } catch (e1) {
                                try {
                                    if (fieldName.includes('multiLangInputs')) {
                                        const parts = fieldName.split('.');
                                        if (parts.length === 3) {
                                            const lang = parts[1];
                                            const field = parts[2];
                                            if (!component.data.multiLangInputs) component.data.multiLangInputs = {};
                                            if (!component.data.multiLangInputs[lang]) component.data.multiLangInputs[lang] = {};
                                            component.data.multiLangInputs[lang][field] = finalContent;
                                        }
                                    } else if (fieldName.includes('.')) {
                                        const [obj, prop] = fieldName.split('.');
                                        component.data[obj][prop] = finalContent;
                                    } else {
                                        component.data[fieldName] = finalContent;
                                    }
                                    component.call('$refresh');
                                } catch (e2) {
                                    console.error('❌ Tüm sync yöntemleri başarısız:', e2);
                                }
                            }
                        } else {
                            console.log('✅ LIVEWIRE SYNC BAŞARILI!');
                        }
                    }
                }
            }, 1000);

            // Toast mesajı
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'İçerik Eklendi!',
                    text: `İçerik (${processedContent.length} karakter) editöre eklendi.`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }

        } else {
            console.error('❌ HEDEF TEXTAREA BULUNAMADI!');
            console.log('🔍 Tüm denenen selektorörler başarısız');
            hideProgressModal();

            // Hata mesajı
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Editör Bulunamadı!',
                    text: 'HugeRTE editörü bulunamadı. Sayfa yeniden yüklensin mi?',
                    showCancelButton: true,
                    confirmButtonText: 'Yenile',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        }
    }, 2000); // Daha uzun bekleme süresi

});

// Eski event listener'ı da bırak (opsiyonel ekleme için)
Livewire.on('insertContentToEditor', (event) => {
    const { field, content, immediate } = event.detail || event;
    const processedContent = processContentForEditor(content);

    // HugeRTE için
    const targetTextarea = document.querySelector(`textarea[name="${field}"]`) ||
                          document.querySelector(`#${field}`);
    if (targetTextarea) {
        // Mevcut içeriğin sonuna ekle
        targetTextarea.value += processedContent;
        targetTextarea.dispatchEvent(new Event('change', { bubbles: true }));

        // HugeRTE iframe'ini güncelle
        const iframe = targetTextarea.closest('.hugerteContainer')?.querySelector('iframe.hugerteEditor');
        if (iframe && iframe.contentDocument) {
            iframe.contentDocument.body.innerHTML += processedContent;
        }
    }
});

// İçeriği editör için işle
function processContentForEditor(content) {
    // Modern Tailwind class'ları ekle
    let processed = content;

    // Container wrapper ekle
    if (!processed.includes('container')) {
        processed = '<div class="container mx-auto px-4 py-8">\n' + processed + '\n</div>';
    }

    // Tailwind utilities kontrol et
    processed = processed
        // Responsive prefix'ler
        .replace(/class="([^"]*?)"/g, (match, classes) => {
            // Modern spacing utilities
            classes = classes.replace(/\bp-(\d+)\b/g, 'p-$1 sm:p-$1 md:p-$1 lg:p-$1');
            classes = classes.replace(/\bm-(\d+)\b/g, 'm-$1 sm:m-$1 md:m-$1 lg:m-$1');

            // Modern color utilities
            classes = classes.replace(/bg-blue/g, 'bg-blue-500 hover:bg-blue-600');
            classes = classes.replace(/text-gray/g, 'text-gray-700 dark:text-gray-300');

            return `class="${classes}"`;
        });

    // Dark mode support
    processed = processed.replace(/class="([^"]*?)"/g, (match, classes) => {
        if (classes.includes('bg-') && !classes.includes('dark:')) {
            classes += ' dark:bg-gray-800';
        }
        if (classes.includes('text-') && !classes.includes('dark:')) {
            classes += ' dark:text-gray-100';
        }
        return `class="${classes}"`;
    });

    return processed;
}
</script>
@endpush