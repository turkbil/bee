{{--
    Review List Component
    Kullanım:
    @include('reviewsystem::components.review-list', [
        'model' => $product,
        'showForm' => true,
        'perPage' => 10
    ])
--}}

@php
    use Modules\ReviewSystem\App\Services\ReviewService;

    $modelClass = get_class($model);
    $modelId = $model->id;
    $reviewService = app(ReviewService::class);
    $reviews = $reviewService->getReviews($modelClass, $modelId);
    $showForm = $showForm ?? true;
    $perPage = $perPage ?? 10;
@endphp

<div class="review-list-wrapper" x-data="reviewList({
    modelClass: '{{ addslashes($modelClass) }}',
    modelId: {{ $modelId }},
    isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
    userName: '{{ auth()->check() ? addslashes(auth()->user()->name) : '' }}'
})">

    <!-- Yorum Formu -->
    @if($showForm && auth()->check())
    <div class="review-form bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Yorum Yaz</h3>

        <form @submit.prevent="submitReview">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Puanınız (İsteğe Bağlı)
                </label>
                <div class="flex items-center gap-1">
                    <template x-for="star in 5" :key="star">
                        <button
                            type="button"
                            @click="formData.rating_value = star"
                            @mouseenter="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            class="text-2xl transition-colors duration-150 cursor-pointer">
                            <i :class="{
                                'fas fa-star text-yellow-400': star <= (hoverRating || formData.rating_value),
                                'far fa-star text-gray-300 dark:text-gray-600': star > (hoverRating || formData.rating_value)
                            }"></i>
                        </button>
                    </template>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yorumunuz *
                </label>
                <textarea
                    x-model="formData.review_body"
                    rows="4"
                    required
                    placeholder="Deneyiminizi paylaşın..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!loading">Gönder</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        Gönderiliyor...
                    </span>
                </button>

                <div x-show="showFormMessage"
                     x-transition
                     :class="{
                         'text-green-600 dark:text-green-400': formMessageType === 'success',
                         'text-red-600 dark:text-red-400': formMessageType === 'error'
                     }"
                     class="text-sm">
                    <span x-text="formMessage"></span>
                </div>
            </div>
        </form>
    </div>
    @elseif($showForm && !auth()->check())
    <div class="review-form-login bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800 dark:text-blue-200">
            <i class="fas fa-info-circle mr-2"></i>
            Yorum yazmak için <a href="{{ route('login') }}" class="font-semibold underline hover:no-underline">giriş yapmalısınız</a>.
        </p>
    </div>
    @endif

    <!-- Yorum Listesi -->
    <div class="reviews-list">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Yorumlar (<span x-text="reviews.length">{{ $reviews->count() }}</span>)
        </h3>

        <template x-if="reviews.length === 0">
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-comments text-4xl mb-2"></i>
                <p>Henüz yorum yapılmamış. İlk yorumu siz yazın!</p>
            </div>
        </template>

        <div class="space-y-4">
            <template x-for="review in reviews" :key="review.id">
                <div class="review-item bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <!-- Yorum Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                <span x-text="review.author_name?.charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white" x-text="review.author_name"></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formatDate(review.created_at)"></p>
                            </div>
                        </div>

                        <!-- Rating (varsa) -->
                        <div x-show="review.rating_value" class="flex items-center gap-1">
                            <template x-for="star in 5" :key="star">
                                <i :class="{
                                    'fas fa-star text-yellow-400': star <= review.rating_value,
                                    'far fa-star text-gray-300 dark:text-gray-600': star > review.rating_value
                                }" class="text-sm"></i>
                            </template>
                        </div>
                    </div>

                    <!-- Yorum İçeriği -->
                    <div class="text-gray-700 dark:text-gray-300" x-text="review.review_body"></div>

                    <!-- Yanıtlar (varsa) -->
                    <div x-show="review.replies && review.replies.length > 0" class="mt-4 ml-8 space-y-3">
                        <template x-for="reply in review.replies" :key="reply.id">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border-l-2 border-blue-500">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white" x-text="reply.author_name"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(reply.created_at)"></span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="reply.review_body"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reviewList', (config) => ({
        modelClass: config.modelClass,
        modelId: config.modelId,
        isAuthenticated: config.isAuthenticated,
        userName: config.userName,
        reviews: @json($reviews),
        loading: false,
        hoverRating: 0,
        showFormMessage: false,
        formMessage: '',
        formMessageType: 'info',
        formData: {
            review_body: '',
            rating_value: null
        },

        submitReview() {
            if (!this.isAuthenticated) {
                alert('Lütfen giriş yapın');
                return;
            }

            if (!this.formData.review_body.trim()) {
                this.displayFormMessage('Lütfen yorumunuzu yazın', 'error');
                return;
            }

            this.loading = true;

            fetch('/api/reviews/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    model_class: this.modelClass,
                    model_id: this.modelId,
                    review_body: this.formData.review_body,
                    rating_value: this.formData.rating_value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.displayFormMessage('Yorumunuz onay bekliyor. Teşekkürler!', 'success');
                    this.formData.review_body = '';
                    this.formData.rating_value = null;
                } else {
                    this.displayFormMessage(data.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Review submit error:', error);
                this.displayFormMessage('Bir hata oluştu', 'error');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        displayFormMessage(msg, type = 'info') {
            this.formMessage = msg;
            this.formMessageType = type;
            this.showFormMessage = true;

            setTimeout(() => {
                this.showFormMessage = false;
            }, 5000);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }));
});
</script>
@endpush
@endonce
