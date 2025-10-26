/**
 * 🎯 GOOGLE TAG MANAGER - DATALAYER EVENT TRACKING
 *
 * Bu dosya tüm platformlara (GA4, Google Ads, Facebook, Yandex vb.)
 * event gönderir. GTM üzerinden yönetilir.
 *
 * @version 1.0
 * @date 2025-10-26
 */

(function() {
    'use strict';

    // dataLayer kontrolü
    window.dataLayer = window.dataLayer || [];

    /**
     * 📊 FORM SUBMIT EVENT
     * Form gönderildiğinde tetiklenir
     */
    function trackFormSubmit(formName, formId) {
        window.dataLayer.push({
            'event': 'form_submit',
            'form_name': formName || 'contact_form',
            'form_id': formId || 'unknown',
            'event_category': 'engagement',
            'event_label': 'form_submission'
        });

        console.log('📊 GTM Event: form_submit', { form_name: formName, form_id: formId });
    }

    /**
     * 📞 PHONE CLICK EVENT
     * Telefon numarasına tıklandığında tetiklenir
     */
    function trackPhoneClick(phoneNumber, location) {
        window.dataLayer.push({
            'event': 'phone_click',
            'phone_number': phoneNumber || 'unknown',
            'click_location': location || 'unknown',
            'event_category': 'engagement',
            'event_label': 'phone_call_attempt'
        });

        console.log('📞 GTM Event: phone_click', { phone: phoneNumber, location: location });
    }

    /**
     * 💬 WHATSAPP CLICK EVENT
     * WhatsApp butonuna tıklandığında tetiklenir
     */
    function trackWhatsAppClick(phoneNumber, location) {
        window.dataLayer.push({
            'event': 'whatsapp_click',
            'phone_number': phoneNumber || 'unknown',
            'click_location': location || 'unknown',
            'event_category': 'engagement',
            'event_label': 'whatsapp_message_attempt'
        });

        console.log('💬 GTM Event: whatsapp_click', { phone: phoneNumber, location: location });
    }

    /**
     * 📧 EMAIL CLICK EVENT
     * Email linkine tıklandığında tetiklenir
     */
    function trackEmailClick(email, location) {
        window.dataLayer.push({
            'event': 'email_click',
            'email': email || 'unknown',
            'click_location': location || 'unknown',
            'event_category': 'engagement',
            'event_label': 'email_attempt'
        });

        console.log('📧 GTM Event: email_click', { email: email, location: location });
    }

    /**
     * 📄 FILE DOWNLOAD EVENT
     * PDF veya dosya indirildiğinde tetiklenir
     */
    function trackFileDownload(fileName, fileUrl) {
        window.dataLayer.push({
            'event': 'file_download',
            'file_name': fileName || 'unknown',
            'file_url': fileUrl || 'unknown',
            'event_category': 'engagement',
            'event_label': 'file_download'
        });

        console.log('📄 GTM Event: file_download', { file: fileName });
    }

    /**
     * 🛒 PRODUCT VIEW EVENT
     * Ürün detay sayfası görüntülendiğinde tetiklenir
     */
    function trackProductView(productId, productName, productCategory, productPrice) {
        window.dataLayer.push({
            'event': 'view_item',
            'ecommerce': {
                'items': [{
                    'item_id': productId || 'unknown',
                    'item_name': productName || 'unknown',
                    'item_category': productCategory || 'unknown',
                    'price': productPrice || 0
                }]
            },
            'event_category': 'ecommerce',
            'event_label': 'product_view'
        });

        console.log('🛒 GTM Event: view_item', { product_id: productId, name: productName });
    }

    /**
     * 📜 SCROLL DEPTH TRACKING
     * Sayfa scroll yüzdesi takibi
     */
    let scrollTracked = {
        25: false,
        50: false,
        75: false,
        100: false
    };

    function trackScrollDepth() {
        const scrollPercentage = Math.round(
            ((window.scrollY + window.innerHeight) / document.documentElement.scrollHeight) * 100
        );

        [25, 50, 75, 100].forEach(function(threshold) {
            if (scrollPercentage >= threshold && !scrollTracked[threshold]) {
                scrollTracked[threshold] = true;

                window.dataLayer.push({
                    'event': 'scroll_depth',
                    'scroll_percentage': threshold,
                    'event_category': 'engagement',
                    'event_label': 'scroll_' + threshold + '%'
                });

                console.log('📜 GTM Event: scroll_depth', { percentage: threshold + '%' });
            }
        });
    }

    /**
     * 🎬 OTOMATIK EVENT LISTENER'LAR
     * Sayfa yüklendiğinde çalışır
     */
    function initAutoTracking() {
        // Scroll tracking (throttled)
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(trackScrollDepth, 100);
        });

        // Telefon linkleri
        document.querySelectorAll('a[href^="tel:"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const phoneNumber = this.getAttribute('href').replace('tel:', '');
                const location = this.closest('[data-location]')?.getAttribute('data-location') ||
                                this.closest('header') ? 'header' :
                                this.closest('footer') ? 'footer' :
                                this.closest('.sidebar') ? 'sidebar' : 'page';
                trackPhoneClick(phoneNumber, location);
            });
        });

        // WhatsApp linkleri
        document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp.com"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const phoneMatch = href.match(/\d{10,}/);
                const phoneNumber = phoneMatch ? phoneMatch[0] : 'unknown';
                const location = this.closest('[data-location]')?.getAttribute('data-location') ||
                                this.closest('header') ? 'header' :
                                this.closest('footer') ? 'footer' :
                                this.closest('.sidebar') ? 'sidebar' : 'page';
                trackWhatsAppClick(phoneNumber, location);
            });
        });

        // Email linkleri
        document.querySelectorAll('a[href^="mailto:"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const email = this.getAttribute('href').replace('mailto:', '');
                const location = this.closest('[data-location]')?.getAttribute('data-location') || 'page';
                trackEmailClick(email, location);
            });
        });

        // PDF ve dosya indirme
        document.querySelectorAll('a[href$=".pdf"], a[href$=".doc"], a[href$=".docx"], a[href$=".xls"], a[href$=".xlsx"], a[href$=".zip"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const fileUrl = this.getAttribute('href');
                const fileName = fileUrl.split('/').pop();
                trackFileDownload(fileName, fileUrl);
            });
        });

        // Form submit (Livewire + normal forms)
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                const formName = e.target.getAttribute('name') ||
                                e.target.getAttribute('id') ||
                                e.target.querySelector('[name]')?.getAttribute('name') ||
                                'contact_form';
                const formId = e.target.getAttribute('id') || 'unknown';
                trackFormSubmit(formName, formId);
            }
        });

        // Livewire form success event (eğer varsa)
        document.addEventListener('form-success', function(e) {
            trackFormSubmit(e.detail?.formName || 'livewire_form', e.detail?.formId || 'unknown');
        });

        console.log('✅ GA Events Auto-Tracking başlatıldı');
    }

    // DOM hazır olduğunda başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoTracking);
    } else {
        initAutoTracking();
    }

    // Livewire navigasyonlarında yeniden başlat
    document.addEventListener('livewire:navigated', function() {
        console.log('🔄 Livewire navigated - Event listeners yenilendi');
        initAutoTracking();
    });

    // Global fonksiyonlar (manuel kullanım için)
    window.trackFormSubmit = trackFormSubmit;
    window.trackPhoneClick = trackPhoneClick;
    window.trackWhatsAppClick = trackWhatsAppClick;
    window.trackEmailClick = trackEmailClick;
    window.trackFileDownload = trackFileDownload;
    window.trackProductView = trackProductView;

})();
