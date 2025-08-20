<!-- Dil İşlem Onay Modalı -->
<div wire:ignore.self class="modal modal-blur fade" id="language-action-modal" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status" id="modal-status-bar"></div>
            <div class="modal-body text-center py-4">
                <svg class="icon mb-2 icon-lg" id="modal-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <!-- Icon will be set by JavaScript -->
                </svg>
                <h3 id="modal-title">İşlem Onayı</h3>
                <div class="text-muted" id="modal-message">Bu işlemi yapmak istediğinize emin misiniz?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button class="btn w-100" data-bs-dismiss="modal">
                                İptal
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn w-100" id="modal-confirm-btn">
                                <span class="confirm-text">Onayla</span>
                                <span class="loading-text d-none">İşleniyor...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Singleton pattern ile modal çakışmasını önle
if (!window.languageModalInitialized) {
    window.languageModalInitialized = true;

    document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap Modal'ı başlat (Tabler.io uyumlu)
    const modalElement = document.getElementById('language-action-modal');
    let modal;
    
    // Bootstrap yüklenmişse kullan, yoksa jQuery fallback
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
    } else if (typeof jQuery !== 'undefined') {
        modal = {
            show: () => $(modalElement).modal({
                backdrop: 'static',
                keyboard: false
            }).modal('show'),
            hide: () => $(modalElement).modal('hide')
        };
    } else {
        console.error('Bootstrap veya jQuery bulunamadı');
        return;
    }
    const statusBar = document.getElementById('modal-status-bar');
    const modalIcon = document.getElementById('modal-icon');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    
    // Modal konfigürasyonları
    const configs = {
        delete: {
            color: 'danger',
            icon: `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01"/><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>`,
            title: 'Silme Onayı',
            message: 'Bu dili silmek istediğinize emin misiniz? Bu işlem geri alınamaz!',
            btnText: 'Sil'
        },
        activate: {
            color: 'success',
            icon: `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>`,
            title: 'Dili Aktif Et',
            message: 'Bu dili aktif yapmak istediğinize emin misiniz?',
            btnText: 'Aktif Et'
        },
        deactivate: {
            color: 'warning',
            icon: `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19h12a2 2 0 0 0 2 -2v-6a2 2 0 0 0 -2 -2h-12a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2z"/><path d="M6 9v-2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v2"/>`,
            title: 'Dili Pasif Et',
            message: 'Bu dili pasif yapmak istediğinize emin misiniz?',
            btnText: 'Pasif Et'
        },
        show: {
            color: 'info',
            icon: `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>`,
            title: 'Dili Görünür Yap',
            message: 'Bu dili görünür yapmak istediğinize emin misiniz?',
            btnText: 'Görünür Yap'
        },
        hide: {
            color: 'danger',
            icon: `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0m-10 4l0 6m4 -6l0 6m-9 -10l1 0m8 0l1 0m-9 -3l2 0m6 0l2 0"/>`,
            title: 'Dili Sil',
            message: 'Bu dili silmek istediğinize emin misiniz? Silinen diller "Diğer Diller" bölümüne taşınır.',
            btnText: 'Sil'
        }
    };
    
    // Modal açma fonksiyonu
    function openModalWithConfig(action, languageId, languageName) {
        const config = configs[action];
        if (!config) {
            console.error('Modal config bulunamadı:', action);
            return;
        }
        
        // Modal içeriğini ayarla
        statusBar.className = `modal-status bg-${config.color}`;
        modalIcon.innerHTML = config.icon;
        modalIcon.className = `icon mb-2 text-${config.color} icon-lg`;
        modalTitle.textContent = config.title;
        modalMessage.innerHTML = config.message.replace('Bu dili', `<strong>${languageName}</strong> dilini`);
        
        // Confirm butonunu ayarla
        confirmBtn.className = `btn btn-${config.color} w-100`;
        confirmBtn.querySelector('.confirm-text').textContent = config.btnText;
        
        // Mevcut scroll pozisyonunu kaydet
        const savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        
        // Click handler'ı her seferinde yeniden ata (önceki handler temizlenir)
        confirmBtn.onclick = null; // Önceki handler'ı temizle
        confirmBtn.onclick = function() {
            // Loading state
            confirmBtn.querySelector('.confirm-text').classList.add('d-none');
            confirmBtn.querySelector('.loading-text').classList.remove('d-none');
            confirmBtn.disabled = true;
            
            // Livewire metodunu çağır
            switch(action) {
                case 'delete':
                    @this.call('delete', languageId);
                    break;
                case 'activate':
                case 'deactivate':
                    @this.call('toggleActive', languageId);
                    break;
                case 'show':
                case 'hide':
                    @this.call('toggleVisibility', languageId);
                    break;
            }
            
            // Modal'ı kapat ve scroll pozisyonunu geri yükle
            setTimeout(() => {
                modal.hide();
                
                // Scroll pozisyonunu derhal geri yükle
                setTimeout(() => {
                    document.body.classList.remove('modal-open-fixed');
                    document.body.style.top = '';
                    window.scrollTo(0, savedScrollPosition);
                    
                    // Reset loading state
                    confirmBtn.querySelector('.confirm-text').classList.remove('d-none');
                    confirmBtn.querySelector('.loading-text').classList.add('d-none');
                    confirmBtn.disabled = false;
                }, 50);
            }, 300);
        };
        
        modal.show();
    }
    
    // Tek global fonksiyon tanımla
    window.showLanguageActionModal = function(action, languageId, languageName) {
        openModalWithConfig(action, languageId, languageName);
    };
    }); // DOMContentLoaded içi bitiş
} // Singleton if bitiş
</script>

<style>
/* Modal scroll pozisyonunu koruma için CSS */
.modal-open-fixed {
    position: fixed;
    width: 100%;
    overflow: hidden;
}
</style>