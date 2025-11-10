{{--
    Rating Stars Component
    Kullanım:
    @include('reviewsystem::components.rating-stars', [
        'model' => $product,
        'readonly' => false,
        'showCount' => true,
        'size' => 'md' // sm, md, lg
    ])
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

    $starSizes = [
        'sm' => 'text-base',
        'md' => 'text-xl',
        'lg' => 'text-3xl'
    ];
    $starClass = $starSizes[$size] ?? $starSizes['md'];
@endphp

<div class="rating-stars-wrapper" x-data="ratingStars({
    modelClass: '{{ addslashes($modelClass) }}',
    modelId: {{ $modelId }},
    currentRating: {{ $userRating ?? 0 }},
    averageRating: {{ $averageRating }},
    ratingsCount: {{ $ratingsCount }},
    readonly: {{ $readonly ? 'true' : 'false' }},
    isAuthenticated: {{ auth()->check() ? 'true' : 'false' }}
})">
    <div class="flex items-center gap-2">
        <!-- Yıldızlar -->
        <div class="flex items-center gap-1">
            <template x-for="star in 5" :key="star">
                <button
                    type="button"
                    @click="!readonly && isAuthenticated && rate(star)"
                    @mouseenter="!readonly && isAuthenticated && (hoverRating = star)"
                    @mouseleave="!readonly && isAuthenticated && (hoverRating = 0)"
                    :class="{
                        'cursor-pointer': !readonly && isAuthenticated,
                        'cursor-default': readonly || !isAuthenticated
                    }"
                    class="{{ $starClass }} transition-colors duration-150">
                    <i :class="{
                        'fas fa-star text-yellow-400': star <= (hoverRating || currentRating || averageRating),
                        'far fa-star text-gray-300 dark:text-gray-600': star > (hoverRating || currentRating || averageRating)
                    }"></i>
                </button>
            </template>
        </div>

        <!-- Ortalama puan ve sayı -->
        @if($showCount)
        <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-semibold" x-text="averageRating.toFixed(1)">{{ number_format($averageRating, 1) }}</span>
            <span>(<span x-text="ratingsCount">{{ $ratingsCount }}</span>)</span>
        </div>
        @endif
    </div>

    <!-- Kullanıcı feedback mesajı -->
    <div x-show="showMessage"
         x-transition
         class="mt-2 text-sm"
         :class="{
             'text-green-600 dark:text-green-400': messageType === 'success',
             'text-red-600 dark:text-red-400': messageType === 'error',
             'text-blue-600 dark:text-blue-400': messageType === 'info'
         }">
        <span x-text="message"></span>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ratingStars', (config) => ({
        modelClass: config.modelClass,
        modelId: config.modelId,
        currentRating: config.currentRating,
        averageRating: config.averageRating,
        ratingsCount: config.ratingsCount,
        readonly: config.readonly,
        isAuthenticated: config.isAuthenticated,
        hoverRating: 0,
        showMessage: false,
        message: '',
        messageType: 'info',

        rate(value) {
            if (this.readonly) return;

            if (!this.isAuthenticated) {
                this.displayMessage('Puan vermek için giriş yapmalısınız', 'error');
                return;
            }

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
                if (data.success) {
                    this.currentRating = value;
                    this.averageRating = data.data.average_rating;
                    this.ratingsCount = data.data.ratings_count;
                    this.displayMessage('Puanınız kaydedildi', 'success');
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
    }));
});
</script>
@endpush
@endonce
