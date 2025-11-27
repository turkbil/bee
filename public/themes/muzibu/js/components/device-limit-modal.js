/**
 * Device Limit Modal - Alpine.js Component
 * 
 * KULLANIM: Bu modal şu anda KAPALI - Backend otomatik hallediyor
 * Açmak için: layouts/app.blade.php'de include satırını uncomment et
 */

function deviceLimitModal() {
    return {
        open: false,
        devices: [],
        loading: false,
        errorMessage: '',

        /**
         * Check device limit via API
         * NOT: Şu anda kullanılmıyor - backend otomatik temizliyor
         */
        async checkDeviceLimit() {
            try {
                const response = await fetch('/api/devices/check', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.limit_exceeded) {
                    this.devices = data.active_devices;
                    this.open = true;
                }
            } catch (error) {
                console.error('Device check error:', error);
            }
        },

        /**
         * Terminate specific device session
         */
        async terminateDevice(sessionId) {
            if (!confirm('Bu cihazdan çıkış yapmak istediğinizden emin misiniz?')) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch(`/api/devices/${sessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.devices = this.devices.filter(d => d.id !== sessionId);

                    if (this.devices.length === 0) {
                        this.open = false;
                        const loginForm = document.querySelector('form[action*="login"]');
                        if (loginForm) loginForm.submit();
                    }
                } else {
                    this.errorMessage = data.message || 'Bir hata oluştu';
                }
            } catch (error) {
                this.errorMessage = 'Bağlantı hatası oluştu';
            } finally {
                this.loading = false;
            }
        },

        /**
         * Terminate all devices
         */
        async terminateAllDevices() {
            if (!confirm('Tüm cihazlardan çıkış yapmak istediğinizden emin misiniz?')) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('/api/devices/all', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.open = false;
                    const loginForm = document.querySelector('form[action*="login"]');
                    if (loginForm) loginForm.submit();
                } else {
                    this.errorMessage = data.message || 'Bir hata oluştu';
                }
            } catch (error) {
                this.errorMessage = 'Bağlantı hatası oluştu';
            } finally {
                this.loading = false;
            }
        },

        /**
         * Get device icon emoji
         */
        getDeviceIcon(deviceType) {
            const icons = {
                'mobile': '📱',
                'tablet': '💻',
                'desktop': '🖥️'
            };
            return icons[deviceType] || '🖥️';
        }
    };
}
