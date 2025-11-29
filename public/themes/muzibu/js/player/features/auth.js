/**
 * Muzibu Auth Manager
 * Handles login, registration, validation, logout
 */

function muzibuAuth() {
    return {
        // Auth UI states
        showAuthModal: null,
        authLoading: false,
        authError: '',
        authSuccess: '',
        showPassword: false,
        showLoginPassword: false,

        // Forms
        loginForm: {
            email: safeStorage.getItem('remembered_email') || '',
            password: '',
            remember: safeStorage.getItem('remembered_email') ? true : false
        },
        registerForm: {
            name: '',
            email: '',
            phone: '',
            password: '',
            password_confirmation: ''
        },
        forgotForm: { email: '' },

        // Validation state
        validation: {
            name: { valid: false, checked: false, message: '' },
            email: { valid: false, checked: false, message: '' },
            phone: { valid: false, checked: false, message: '' },
            password: { valid: false, checked: false, message: '' },
            password_confirmation: { valid: false, checked: false, message: '' }
        },

        // Phone country selector
        phoneCountry: {
            code: '+90',
            flag: '🇹🇷',
            name: 'Türkiye',
            placeholder: '5__ ___ __ __',
            format: 'XXX XXX XX XX'
        },
        phoneCountries: [
            { code: '+90', flag: '🇹🇷', name: 'Türkiye', placeholder: '5__ ___ __ __', format: 'XXX XXX XX XX' },
            { code: '+1', flag: '🇺🇸', name: 'Amerika', placeholder: '(___) ___-____', format: '(XXX) XXX-XXXX' },
            { code: '+44', flag: '🇬🇧', name: 'İngiltere', placeholder: '____ ______', format: 'XXXX XXXXXX' },
            { code: '+49', flag: '🇩🇪', name: 'Almanya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+33', flag: '🇫🇷', name: 'Fransa', placeholder: '_ __ __ __ __', format: 'X XX XX XX XX' },
            { code: '+39', flag: '🇮🇹', name: 'İtalya', placeholder: '___ _______', format: 'XXX XXXXXXX' },
            { code: '+34', flag: '🇪🇸', name: 'İspanya', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+31', flag: '🇳🇱', name: 'Hollanda', placeholder: '_ ________', format: 'X XXXXXXXX' },
            { code: '+32', flag: '🇧🇪', name: 'Belçika', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+41', flag: '🇨🇭', name: 'İsviçre', placeholder: '__ ___ __ __', format: 'XX XXX XX XX' },
            { code: '+43', flag: '🇦🇹', name: 'Avusturya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+7', flag: '🇷🇺', name: 'Rusya', placeholder: '(___) ___-__-__', format: '(XXX) XXX-XX-XX' },
            { code: '+86', flag: '🇨🇳', name: 'Çin', placeholder: '___ ____ ____', format: 'XXX XXXX XXXX' },
            { code: '+81', flag: '🇯🇵', name: 'Japonya', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+82', flag: '🇰🇷', name: 'Güney Kore', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+971', flag: '🇦🇪', name: 'BAE', placeholder: '__ ___ ____', format: 'XX XXX XXXX' },
            { code: '+966', flag: '🇸🇦', name: 'Suudi Arabistan', placeholder: '__ ___ ____', format: 'XX XXX XXXX' }
        ],

        // Validation functions
        validateName() {
            const name = this.registerForm.name.trim();
            this.validation.name.checked = true;

            if (name.length === 0) {
                this.validation.name.valid = false;
                this.validation.name.message = 'Ad soyad gereklidir';
            } else if (name.length < 3) {
                this.validation.name.valid = false;
                this.validation.name.message = 'En az 3 karakter olmalıdır';
            } else if (!/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/.test(name)) {
                this.validation.name.valid = false;
                this.validation.name.message = 'Sadece harf kullanılabilir';
            } else {
                this.validation.name.valid = true;
                this.validation.name.message = '';
            }
        },

        validateEmail() {
            const email = this.registerForm.email.trim();
            this.validation.email.checked = true;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email.length === 0) {
                this.validation.email.valid = false;
                this.validation.email.message = 'E-posta adresi gereklidir';
            } else if (!emailRegex.test(email)) {
                this.validation.email.valid = false;
                this.validation.email.message = 'Geçerli bir e-posta adresi girin';
            } else {
                this.validation.email.valid = true;
                this.validation.email.message = '';
            }
        },

        validatePhone() {
            const phone = this.registerForm.phone.trim();
            this.validation.phone.checked = true;

            if (phone.length === 0) {
                this.validation.phone.valid = false;
                this.validation.phone.message = 'Telefon numarası gereklidir';
            } else if (phone.length < 10) {
                this.validation.phone.valid = false;
                this.validation.phone.message = 'En az 10 haneli olmalıdır';
            } else if (!/^5[0-9]{9}$/.test(phone)) {
                this.validation.phone.valid = false;
                this.validation.phone.message = '5 ile başlamalı ve 10 haneli olmalıdır';
            } else {
                this.validation.phone.valid = true;
                this.validation.phone.message = '';
            }
        },

        validatePassword() {
            const password = this.registerForm.password;
            this.validation.password.checked = true;

            if (password.length === 0) {
                this.validation.password.valid = false;
                this.validation.password.message = 'Şifre gereklidir';
            } else if (password.length < 8) {
                this.validation.password.valid = false;
                this.validation.password.message = 'En az 8 karakter olmalıdır';
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                this.validation.password.valid = false;
                this.validation.password.message = 'Büyük harf, küçük harf ve rakam içermelidir';
            } else {
                this.validation.password.valid = true;
                this.validation.password.message = '';
            }
        },

        validatePasswordConfirmation() {
            const password = this.registerForm.password;
            const confirmation = this.registerForm.password_confirmation;
            this.validation.password_confirmation.checked = true;

            if (confirmation.length === 0) {
                this.validation.password_confirmation.valid = false;
                this.validation.password_confirmation.message = 'Şifre tekrarı gereklidir';
            } else if (password !== confirmation) {
                this.validation.password_confirmation.valid = false;
                this.validation.password_confirmation.message = 'Şifreler eşleşmiyor';
            } else {
                this.validation.password_confirmation.valid = true;
                this.validation.password_confirmation.message = '';
            }
        },

        // Login/Register/Logout (will be added from original file)
        async handleLogin() {
            // Implementation from original muzibu-player.js
        },

        async handleRegister() {
            // Implementation from original muzibu-player.js
        },

        async handleLogout() {
            // Implementation from original muzibu-player.js
        }
    };
}

// Make globally accessible
window.muzibuAuth = muzibuAuth;
