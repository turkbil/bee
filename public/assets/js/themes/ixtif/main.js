// iXtif Theme - Main JavaScript

// 🎯 ALPINE.JS GLOBAL COMPONENTS
// Component definitions MUST load before component usage (moved from product-card.blade.php)
document.addEventListener('alpine:init', () => {
    // Product card price component WITH add to cart
    Alpine.data('productCard', (hasTryPrice = false, productId = null) => ({
        priceHovered: false,
        showTryPrice: false,
        priceTimer: null,
        hasTryPrice: hasTryPrice,
        productId: productId,
        loading: false,
        success: false,
        init() {
            if (this.hasTryPrice) {
                this.startPriceCycle();
            }
        },
        startPriceCycle() {
            this.priceTimer = setInterval(() => {
                if (!this.priceHovered) {
                    this.showTryPrice = true;
                    setTimeout(() => {
                        if (!this.priceHovered) {
                            this.showTryPrice = false;
                        }
                    }, 1500); // TL: 1.5 saniye
                }
            }, 4500); // Döngü: 4.5 saniye (USD 3s + TRY 1.5s)
        },
        async addToCart() {
            this.loading = true;

            // 🚀 OPTIMISTIC UPDATE: Badge'i hemen güncelle
            const currentCartId = localStorage.getItem('cart_id');
            if (typeof Livewire !== 'undefined' && currentCartId) {
                Livewire.dispatch('optimisticAdd', { quantity: 1 });
            }

            // 🎯 Cart icon animasyonu için window event
            window.dispatchEvent(new CustomEvent('optimistic-add', { detail: { quantity: 1 } }));

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        quantity: 1,
                        cart_id: currentCartId ? parseInt(currentCartId) : null
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.success = true;

                    // API'den dönen cart_id'yi localStorage'a kaydet
                    if (data.data && data.data.cart_id) {
                        localStorage.setItem('cart_id', data.data.cart_id);
                    }

                    // CartWidget'ı gerçek veriyle güncelle (optimistic update'i onayla)
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('cartUpdated', {
                            cartId: data.data?.cart_id,
                            itemCount: data.data?.item_count
                        });
                    }

                    setTimeout(() => { this.success = false; }, 2000);
                } else {
                    console.error('❌ Alpine: API returned error', data.message);

                    // ❌ OPTIMISTIC UPDATE ROLLBACK: Hata varsa geri al
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('optimisticRollback', { quantity: 1 });
                    }

                    // Toast notification (alert yerine)
                    if (typeof window.notify === 'function') {
                        window.notify('error', data.message || 'Ürün sepete eklenirken hata oluştu');
                    }
                }
            } catch (error) {
                console.error('❌ Alpine: Fetch error', error);

                // ❌ OPTIMISTIC UPDATE ROLLBACK: Network hatası varsa geri al
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('optimisticRollback', { quantity: 1 });
                }

                // Toast notification (alert yerine)
                if (typeof window.notify === 'function') {
                    window.notify('error', 'Ürün sepete eklenirken hata oluştu');
                }
            } finally {
                this.loading = false;
            }
        },
        destroy() {
            if (this.priceTimer) clearInterval(this.priceTimer);
        }
    }));

    // Add to cart button component
    Alpine.data('addToCartButton', (productId) => ({
        loading: false,
        success: false,
        async addToCart() {
            console.log('🛒 Alpine: addToCart clicked', { productId });
            this.loading = true;

            try {
                // CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // 🔑 localStorage'dan cart_id al (varsa)
                const cartId = localStorage.getItem('cart_id');

                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1,
                        cart_id: cartId ? parseInt(cartId) : null  // 🔑 cart_id gönder
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.success = true;

                    // 🔑 API'den dönen cart_id'yi localStorage'a kaydet
                    if (data.data && data.data.cart_id) {
                        localStorage.setItem('cart_id', data.data.cart_id);
                    }

                    // CartWidget'ı güncelle - Livewire event dispatch
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('cartUpdated');
                    }

                    setTimeout(() => { this.success = false; }, 2000);
                } else {
                    console.error('❌ Alpine: API returned error', data.message);
                    alert(data.message || 'Ürün sepete eklenirken hata oluştu');
                }
            } catch (error) {
                console.error('❌ Alpine: Fetch error', error);
                alert('Ürün sepete eklenirken hata oluştu');
            } finally {
                this.loading = false;
            }
        }
    }));

    // 🔄 Sayfa yüklendiğinde localStorage cart_id ile sepeti senkronize et
    document.addEventListener('DOMContentLoaded', () => {
        const cartId = localStorage.getItem('cart_id');
        if (cartId && typeof Livewire !== 'undefined') {
            // CartWidget'ı refresh et (Livewire event)
            setTimeout(() => {
                Livewire.dispatch('cartUpdated');
            }, 500); // Livewire init bekle
        }
    });
});

// Theme-specific initialization
document.addEventListener("DOMContentLoaded", function () {
    // Theme-specific initialization can go here
});
