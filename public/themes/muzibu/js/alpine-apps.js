/**
 * 🎯 ALPINE.JS APP COMPONENTS
 *
 * Bu dosya tüm Alpine.js app/component tanımlarını içerir.
 * Inline script yerine harici dosya olarak yüklenir.
 */

// 🌍 Global Lang Strings for JS
window.muzibuLang = {
    queue: {
        added_to_queue: ":count şarkı sıraya eklendi",
        added_to_queue_next: ":count şarkı sırada bir sonraki olarak eklendi",
        added_with_duplicates: ":count şarkı eklendi (:removed tekrar kaldırıldı)",
        added_next_with_duplicates: ":count şarkı sonraki olarak eklendi (:removed tekrar kaldırıldı)",
        song_not_found: "Eklenecek şarkı bulunamadı",
        queue_error: "Sıraya eklenirken hata oluştu"
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

// 🎯 playlistEditor - My Playlist edit page with drag & drop (SPA compatible)
window.playlistEditor = function() {
    return {
        playlistId: window.playlistEditData?.playlistId,
        title: window.playlistEditData?.title,
        description: window.playlistEditData?.description,
        isPublic: window.playlistEditData?.isPublic,
        songs: window.playlistEditData?.songs || [],
        saving: false,

        initEditor() {
            if (this.$refs.songList && window.Sortable) {
                new Sortable(this.$refs.songList, {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const movedItem = this.songs[evt.oldIndex];
                        this.songs.splice(evt.oldIndex, 1);
                        this.songs.splice(evt.newIndex, 0, movedItem);
                        this.saveSongOrder();
                    }
                });
            }
        },

        async savePlaylist() {
            this.saving = true;
            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({
                        title: this.title,
                        description: this.description,
                        is_public: this.isPublic
                    })
                });
                const data = await response.json();
                if (data.success) {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show('Playlist güncellendi', 'success');
                    }
                    setTimeout(() => {
                        if (window.muzibuRouter) {
                            window.muzibuRouter.navigateTo('/muzibu/my-playlists');
                        } else {
                            window.location.href = '/muzibu/my-playlists';
                        }
                    }, 1000);
                } else {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                    }
                    this.saving = false;
                }
            } catch (err) {
                console.error('Playlist save error:', err);
                if (window.Alpine?.store('toast')) {
                    window.Alpine.store('toast').show('Bağlantı hatası', 'error');
                }
                this.saving = false;
            }
        },

        async removeSong(songId) {
            if (!confirm('Bu şarkıyı playlist\'ten çıkarmak istediğinize emin misiniz?')) return;
            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}/remove-song/${songId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.songs = this.songs.filter(s => s.song_id !== songId);
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show('Şarkı çıkarıldı', 'success');
                    }
                } else {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                    }
                }
            } catch (err) {
                console.error('Song remove error:', err);
                if (window.Alpine?.store('toast')) {
                    window.Alpine.store('toast').show('Bağlantı hatası', 'error');
                }
            }
        },

        async saveSongOrder() {
            const songPositions = this.songs.map((song, index) => ({
                song_id: song.song_id,
                position: index + 1
            }));
            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}/reorder`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({ song_positions: songPositions })
                });
                const data = await response.json();
                if (data.success) {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show('Sıralama kaydedildi', 'success');
                    }
                } else {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                    }
                }
            } catch (err) {
                console.error('Reorder error:', err);
                if (window.Alpine?.store('toast')) {
                    window.Alpine.store('toast').show('Bağlantı hatası', 'error');
                }
            }
        }
    };
};

// 🎯 certificateForm - Certificate form with text correction (SPA compatible)
window.certificateForm = function() {
    return {
        skipCorrection: window.certificateFormData?.skipCorrection ?? false,
        memberName: window.certificateFormData?.memberName ?? '',
        taxOffice: window.certificateFormData?.taxOffice ?? '',
        taxNumber: window.certificateFormData?.taxNumber ?? '',
        address: window.certificateFormData?.address ?? '',

        init() {
            // Data already loaded from certificateFormData
        },

        correctText(text) {
            if (!text) return text;
            text = text.replace(/\s*\/\s*/g, '/');
            const toUpperTR = (char) => {
                if (char === 'i') return 'İ';
                if (char === 'ı') return 'I';
                return char.toUpperCase();
            };
            const toLowerTR = (char) => {
                if (char === 'I') return 'ı';
                if (char === 'İ') return 'i';
                return char.toLowerCase();
            };
            let result = '';
            let capitalizeNext = true;
            for (let i = 0; i < text.length; i++) {
                const char = text[i];
                if (char === ' ' || char === '\n' || char === '\r') {
                    result += char;
                    capitalizeNext = true;
                } else if (char === '.' || char === ':' || char === '/') {
                    result += char;
                    capitalizeNext = true;
                } else if (capitalizeNext) {
                    result += toUpperTR(char);
                    capitalizeNext = false;
                } else {
                    result += toLowerTR(char);
                }
            }
            return result;
        },

        formatMemberName() {
            if (!this.skipCorrection) {
                this.memberName = this.correctText(this.memberName);
            }
        },

        formatTaxOffice() {
            this.taxOffice = this.correctText(this.taxOffice);
        },

        formatAddress() {
            this.address = this.correctText(this.address);
        }
    }
};

// 🎯 corporateIndexApp - Corporate landing page (/corporate) with join & create forms
window.corporateIndexApp = function() {
    return {
        // Join with code
        code: '',
        loading: false,

        // Create corporate
        companyName: '',
        createCode: '',
        creating: false,
        codeAvailable: null,
        codeError: '',
        checkingCode: false,
        checkCodeTimer: null,

        get createCodeValid() {
            return this.createCode.length === 8 && this.codeAvailable === true;
        },

        async joinWithCode() {
            if (this.code.length !== 8) return;
            this.loading = true;
            try {
                const response = await fetch('/corporate/join', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ corporate_code: this.code })
                });
                const data = await response.json();
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    setTimeout(() => {
                        window.location.href = data.redirect || '/corporate/my-corporate';
                    }, 1000);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'error' }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Bir hata oluştu', type: 'error' }
                }));
            } finally {
                this.loading = false;
            }
        },

        validateCreateCode() {
            if (this.createCode.length !== 8) {
                this.codeAvailable = null;
                this.codeError = '';
                return;
            }
            clearTimeout(this.checkCodeTimer);
            this.checkingCode = true;
            this.codeError = '';
            this.checkCodeTimer = setTimeout(async () => {
                try {
                    const response = await fetch('/api/corporate/check-code?code=' + this.createCode, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();
                    this.codeAvailable = data.available;
                    this.codeError = data.available ? '' : 'Bu kod zaten kullanımda';
                } catch (error) {
                    this.codeError = 'Kontrol edilemedi';
                    this.codeAvailable = null;
                } finally {
                    this.checkingCode = false;
                }
            }, 500);
        },

        async createCorporate() {
            if (this.companyName.length < 2 || !this.createCodeValid) return;
            this.creating = true;
            try {
                const response = await fetch('/api/corporate/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        company_name: this.companyName,
                        corporate_code: this.createCode
                    })
                });
                const data = await response.json();
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    setTimeout(() => {
                        window.location.href = data.redirect || '/corporate/dashboard';
                    }, 1000);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'error' }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Bir hata oluştu', type: 'error' }
                }));
            } finally {
                this.creating = false;
            }
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
                    const companyNameEl = document.querySelector('h1[data-company-name]');
                    if (companyNameEl) companyNameEl.textContent = this.companyName;
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
