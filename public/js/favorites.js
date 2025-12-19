/**
 * Favorites - Global Alpine.js Component
 * Tema bağımsız favoriye ekle/çıkar işlemleri
 */

/**
 * Guest kullanıcı için favori kaydet ve login'e yönlendir
 * Login olduktan sonra otomatik olarak favoriye eklenecek
 */
window.savePendingFavorite = async function(modelClass, modelId, returnUrl) {
    try {
        // Session'a pending favorite bilgisini kaydet
        const response = await fetch('/api/favorites/save-pending', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                model_class: modelClass,
                model_id: modelId,
                return_url: returnUrl
            })
        });

        if (response.ok) {
            // Session'a kaydedildi, login sayfasına yönlendir
            window.location.href = '/login?intended_favorite=true';
        } else {
            // Hata durumunda direkt login'e yönlendir
            console.warn('Pending favorite save failed, redirecting to login anyway');
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Pending favorite error:', error);
        // Hata olsa bile login'e yönlendir
        window.location.href = '/login';
    }
};

document.addEventListener("alpine:init", () => {
    Alpine.data("favoriteButton", (modelClass, modelId, initialState = false, initialCount = 0) => ({
        favorited: initialState,
        count: initialCount,
        loading: false,

        async toggleFavorite() {
            if (this.loading) return;

            this.loading = true;
            const previousState = this.favorited;
            const previousCount = this.count;

            // Optimistic update - anında görsel feedback
            this.favorited = !this.favorited;
            if (initialCount > 0) {
                this.count = this.favorited ? this.count + 1 : Math.max(0, this.count - 1);
            }

            try {
                const response = await fetch("/api/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content") || "",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        model_class: modelClass,
                        model_id: modelId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.favorited = data.data.is_favorited;
                    // API den count gelirse güncelle
                    if (data.data.favorites_count !== undefined) {
                        this.count = data.data.favorites_count;
                    }
                } else {
                    // API başarısız, eski haline döndür
                    this.favorited = previousState;
                    this.count = previousCount;
                    console.warn("Favorite toggle failed:", data.message || "Unknown error");
                }
            } catch (error) {
                // Hata durumunda eski haline döndür
                this.favorited = previousState;
                this.count = previousCount;
                console.error("Favorite error:", error);
            } finally {
                this.loading = false;
            }
        }
    }));
});
