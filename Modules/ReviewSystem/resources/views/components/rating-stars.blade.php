{{--
    ⭐ Self-Contained Rating Stars Component

    Kullanım (Blog):
    @include('reviewsystem::components.rating-stars', [
        'model' => $item,
        'readonly' => false,
        'showCount' => true,
        'size' => 'sm'
    ])

    Kullanım (Product):
    @include('reviewsystem::components.rating-stars', [
        'model' => $product,
        'readonly' => false,
        'showCount' => true,
        'size' => 'md'
    ])

    Size Options: sm, md, lg
    Component kendi JS/CSS/Alpine logic'ini içinde taşır - @push gerekmez!
--}}

@php
    $modelClass = get_class($model);
    $modelId = $model->id;
    $averageRating = $model->averageRating();
    $ratingsCount = $model->ratingsCount();
    $userRating = auth()->check() ? $model->ratings()->where('user_id', auth()->id())->first()?->rating_value : null;
    $readonly = $readonly ?? false;
    $showCount = $showCount ?? true;
    $size = $size ?? 'md';

    // Unique ID for this component instance
    $uniqueId = 'rating-' . uniqid();

    $starSizes = [
        'sm' => 'text-sm',
        'md' => 'text-xl',
        'lg' => 'text-3xl'
    ];
    $starClass = $starSizes[$size] ?? $starSizes['md'];
@endphp

<div id="{{ $uniqueId }}" class="rating-stars-wrapper">
    <div x-data="{
        modelClass: '{{ addslashes($modelClass) }}',
        modelId: {{ $modelId }},
        currentRating: {{ $userRating ?? 0 }},
        averageRating: {{ $averageRating }},
        ratingsCount: {{ $ratingsCount }},
        readonly: {{ $readonly ? 'true' : 'false' }},
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
        hoverRating: 0,
        showMessage: false,
        message: '',
        messageType: 'info',

        handleClick(value) {
            if (this.readonly) return;

            if (!this.isAuthenticated) {
                // Direkt login popup aç
                this.openLoginModal();
                return;
            }

            // Authenticated ise puan gönder
            this.rate(value);
        },

        openLoginModal() {
            // Global login modal varsa aç
            if (window.openLoginModal) {
                window.openLoginModal();
            } else if (typeof Alpine !== 'undefined' && Alpine.store('auth')) {
                Alpine.store('auth').showLoginModal = true;
            } else {
                // Fallback: Login sayfasına yönlendir
                window.location.href = '{{ route("login") }}';
            }
        },

        rate(value) {
            if (this.readonly) return;

            // API'ye puan gönder
            fetch('/api/reviews/rating', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    model_class: this.modelClass,
                    model_id: this.modelId,
                    rating_value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Rating API Response:', data);
                if (data.success) {
                    this.currentRating = value;
                    this.averageRating = parseFloat(data.data.average_rating);
                    this.ratingsCount = parseInt(data.data.ratings_count);
                    console.log('Updated - Average:', this.averageRating, 'Count:', this.ratingsCount);
                    this.displayMessage('Puanınız kaydedildi! ⭐', 'success');

                    // Force Alpine reactivity
                    this.$nextTick(() => {
                        console.log('Alpine updated - Average:', this.averageRating, 'Count:', this.ratingsCount);
                    });
                } else {
                    this.displayMessage(data.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Rating error:', error);
                this.displayMessage('Bir hata oluştu', 'error');
            });
        },

        displayMessage(msg, type = 'info') {
            this.message = msg;
            this.messageType = type;
            this.showMessage = true;

            setTimeout(() => {
                this.showMessage = false;
            }, 3000);
        }
    }">
        <div class="flex items-center gap-2 relative">
            <!-- Yıldızlar -->
            <div class="flex items-center gap-0.5 relative" @mouseleave="hoverRating = 0">
                @auth
                {{-- Authenticated: İnteraktif yıldızlar --}}
                <template x-for="star in 5" :key="star">
                    <button
                        type="button"
                        @click="handleClick(star)"
                        @mouseenter="hoverRating = star"
                        class="{{ $starClass }} cursor-pointer hover:scale-110 transition-all duration-150">
                        <i :class="(hoverRating > 0 && star <= hoverRating) || (hoverRating === 0 && star <= (currentRating || averageRating)) ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600'"></i>
                    </button>
                </template>
                @else
                {{-- Guest: Login için tıklanabilir yıldızlar --}}
                <template x-for="star in 5" :key="star">
                    <button
                        type="button"
                        @click="openLoginModal()"
                        class="{{ $starClass }} cursor-pointer transition-all duration-150">
                        <i :class="star <= averageRating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600'"></i>
                    </button>
                </template>
                @endauth
            </div>

            <!-- Ortalama puan ve sayı -->
            @if($showCount)
            <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold" x-text="averageRating.toFixed(1)">{{ number_format($averageRating, 1) }}</span>
                <span class="text-xs opacity-75">(<span x-text="ratingsCount">{{ $ratingsCount }}</span>)</span>
            </div>
            @endif
        </div>

        <!-- Kullanıcı feedback mesajı -->
        <div x-show="showMessage"
             x-transition
             class="mt-2 text-sm font-medium"
             :class="{
                 'text-green-600 dark:text-green-400': messageType === 'success',
                 'text-red-600 dark:text-red-400': messageType === 'error',
                 'text-blue-600 dark:text-blue-400': messageType === 'info'
             }">
            <span x-text="message"></span>
        </div>
    </div>
</div>
