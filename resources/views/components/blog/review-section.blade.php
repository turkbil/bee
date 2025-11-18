@props(['blog'])

@php
    // Review & Rating verileri
    $averageRating = method_exists($blog, 'averageRating') ? $blog->averageRating() : 0;
    $ratingsCount = method_exists($blog, 'ratingsCount') ? $blog->ratingsCount() : 0;
    $reviewsCount = method_exists($blog, 'reviewsCount') ? $blog->reviewsCount() : 0;

    // Onaylı yorumlar
    $reviews = method_exists($blog, 'reviews')
        ? $blog->reviews()
            ->with(['user', 'replies' => function($query) {
                $query->where('is_approved', true)->with('user');
            }])
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->latest()
            ->take(10)
            ->get()
        : collect([]);

    // Kullanıcının puanı
    $userRating = auth()->check() && method_exists($blog, 'ratings')
        ? $blog->ratings()->where('user_id', auth()->id())->first()?->rating_value
        : null;

    // Puan dağılımı (1-5 yıldız)
    $distribution = [];
    if (method_exists($blog, 'ratings')) {
        for ($i = 5; $i >= 1; $i--) {
            $count = $blog->ratings()->where('rating_value', $i)->count();
            $percentage = $ratingsCount > 0 ? ($count / $ratingsCount) * 100 : 0;
            $distribution[$i] = [
                'count' => $count,
                'percentage' => round($percentage, 1)
            ];
        }
    }
@endphp

<section class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600 mb-8" {{ $attributes }}>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            <i class="fas fa-comments text-blue-500"></i>
            Yorumlar ve Değerlendirmeler
        </h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $reviewsCount }} yorum
        </div>
    </div>

    {{-- Rating Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
        {{-- Ortalama Puan + Interactive Rating --}}
        <div>
            <div class="flex flex-col items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="text-6xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ number_format($averageRating, 1) }}
                    </div>

                    {{-- Interactive Rating Widget (Inline - Hatasız) --}}
                    <div class="flex items-center gap-2"
                         x-data="{
                             modelClass: '{{ addslashes(get_class($blog)) }}',
                             modelId: {{ $blog->id }},
                             currentRating: {{ $userRating ?? 0 }},
                             averageRating: {{ $averageRating }},
                             ratingsCount: {{ $ratingsCount }},
                             isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                             hoverRating: 0,
                             showMessage: false,
                             message: '',
                             messageType: 'info',

                             handleStarClick(value) {
                                 if (!this.isAuthenticated) {
                                     window.location.href = '{{ route('login') }}';
                                     return;
                                 }
                                 this.rateItem(value);
                             },

                             async rateItem(value) {
                                 try {
                                     const response = await fetch('/api/reviews/rating', {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                             'Accept': 'application/json'
                                         },
                                         body: JSON.stringify({
                                             model_class: this.modelClass,
                                             model_id: this.modelId,
                                             rating_value: value
                                         })
                                     });
                                     const data = await response.json();
                                     if (data.success) {
                                         this.currentRating = value;
                                         this.averageRating = parseFloat(data.data.average_rating);
                                         this.ratingsCount = parseInt(data.data.ratings_count);
                                         this.showToast('Puanınız kaydedildi! ⭐', 'success');
                                     } else {
                                         this.showToast(data.message || 'Bir hata oluştu', 'error');
                                     }
                                 } catch (error) {
                                     console.error('Rating error:', error);
                                     this.showToast('Bir hata oluştu', 'error');
                                 }
                             },

                             showToast(msg, type) {
                                 this.message = msg;
                                 this.messageType = type;
                                 this.showMessage = true;
                                 setTimeout(() => { this.showMessage = false; }, 3000);
                             },

                             getStarClass(star) {
                                 const rating = this.hoverRating > 0 ? this.hoverRating : (this.currentRating || this.averageRating);
                                 return star <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600';
                             }
                         }">

                        {{-- Yıldızlar --}}
                        <div class="flex items-center gap-0.5" @mouseleave="hoverRating = 0">
                            <template x-for="star in 5" :key="star">
                                <button type="button"
                                        @click="handleStarClick(star)"
                                        @mouseenter="hoverRating = star"
                                        class="text-3xl cursor-pointer hover:scale-110 transition-all duration-150">
                                    <i x-bind:class="getStarClass(star)"></i>
                                </button>
                            </template>
                        </div>

                        {{-- Toast Message --}}
                        <div x-show="showMessage"
                             x-transition
                             class="absolute top-full mt-2 left-0 px-4 py-2 rounded-lg text-sm font-medium shadow-lg z-10"
                             x-bind:class="{
                                 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': messageType === 'success',
                                 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': messageType === 'error'
                             }">
                            <span x-text="message"></span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                        {{ $ratingsCount }} değerlendirme
                    </p>
                </div>
            </div>
        </div>

        {{-- Puan Dağılımı --}}
        <div class="space-y-2">
            @foreach($distribution as $stars => $data)
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-12">
                        {{ $stars }} <i class="fas fa-star text-yellow-400 text-xs"></i>
                    </span>
                    <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden" style="height: 12px;">
                        <div class="bg-amber-400 dark:bg-amber-400 transition-all duration-300 rounded-full"
                             style="height: 12px; width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 w-12 text-right">
                        {{ $data['count'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Yorumlar Listesi --}}
    @if($reviews->isNotEmpty())
        <div class="space-y-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-comments text-blue-500"></i>
                Kullanıcı Yorumları
            </h3>

            @foreach($reviews as $review)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 transition-all duration-300 hover:shadow-md"
                     x-data="{
                         showReplyForm: false,
                         replyBody: '',
                         helpfulCount: {{ $review->helpful_count ?? 0 }},
                         unhelpfulCount: {{ $review->unhelpful_count ?? 0 }},
                         hasVoted: false,
                         loading: false,
                         isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},

                         async markHelpful(isHelpful) {
                             if (this.hasVoted) return;

                             this.loading = true;

                             try {
                                 const response = await fetch('/api/reviews/helpful/{{ $review->id }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                         'Accept': 'application/json'
                                     },
                                     body: JSON.stringify({ is_helpful: isHelpful })
                                 });

                                 const data = await response.json();

                                 if (data.success) {
                                     this.helpfulCount = data.data.helpful_count;
                                     this.unhelpfulCount = data.data.unhelpful_count;
                                     this.hasVoted = true;
                                 }
                             } catch (error) {
                                 console.error('Helpful error:', error);
                             } finally {
                                 this.loading = false;
                             }
                         },

                         async submitReply() {
                             if (!this.replyBody.trim()) return;

                             if (!this.isAuthenticated) {
                                 window.location.href = '{{ route("login") }}';
                                 return;
                             }

                             this.loading = true;

                             try {
                                 const response = await fetch('/api/reviews/add', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                         'Accept': 'application/json'
                                     },
                                     body: JSON.stringify({
                                         model_class: '{{ addslashes(get_class($blog)) }}',
                                         model_id: {{ $blog->id }},
                                         review_body: this.replyBody,
                                         parent_id: {{ $review->id }}
                                     })
                                 });

                                 const data = await response.json();

                                 if (data.success) {
                                     this.replyBody = '';
                                     this.showReplyForm = false;
                                     alert('Yanıtınız onay bekliyor');
                                 } else {
                                     alert(data.message || 'Bir hata oluştu');
                                 }
                             } catch (error) {
                                 console.error('Reply error:', error);
                                 alert('Bir hata oluştu');
                             } finally {
                                 this.loading = false;
                             }
                         }
                     }">
                    {{-- Yorum Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ substr($review->author_name ?? $review->user?->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $review->author_name ?? $review->user?->name ?? 'Anonim' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $review->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Yorum Puanı --}}
                        @if($review->rating_value)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating_value ? 'fas' : 'far' }} fa-star text-yellow-400"></i>
                                @endfor
                            </div>
                        @endif
                    </div>

                    {{-- Yorum İçeriği --}}
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $review->review_body }}
                    </p>

                    {{-- Yanıtlar --}}
                    @if($review->replies && $review->replies->count() > 0)
                        <div class="mt-4 ml-8 space-y-3">
                            @foreach($review->replies as $reply)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-l-2 border-blue-500">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $reply->author_name ?? $reply->user?->name ?? 'Anonim' }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->review_body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Yardımcı oldu mu? + Yanıtla --}}
                    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="markHelpful(true)"
                                :disabled="hasVoted || loading"
                                :class="(hasVoted || loading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-50 dark:hover:bg-green-900/20'"
                                class="text-sm transition-all flex items-center gap-2 px-3 py-1.5 rounded-lg">
                            <i class="far fa-thumbs-up text-gray-500 dark:text-gray-400"></i>
                            <span x-text="helpfulCount" class="font-semibold text-green-600 dark:text-green-400"></span>
                        </button>
                        <button type="button"
                                @click="markHelpful(false)"
                                :disabled="hasVoted || loading"
                                :class="(hasVoted || loading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-50 dark:hover:bg-red-900/20'"
                                class="text-sm transition-all flex items-center gap-2 px-3 py-1.5 rounded-lg">
                            <i class="far fa-thumbs-down text-gray-500 dark:text-gray-400"></i>
                            <span x-text="unhelpfulCount" class="font-semibold text-red-600 dark:text-red-400"></span>
                        </button>
                        <button type="button"
                                @click="showReplyForm = !showReplyForm"
                                :disabled="loading"
                                class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
                            <i class="far fa-reply"></i>
                            Yanıtla
                        </button>
                    </div>

                    {{-- Yanıt Formu --}}
                    <div x-show="showReplyForm" x-transition class="mt-4 ml-8">
                        <textarea x-model="replyBody"
                                  rows="3"
                                  placeholder="Yanıtınızı yazın..."
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white text-sm resize-none"></textarea>
                        <div class="flex items-center gap-2 mt-2">
                            <button type="button"
                                    @click="submitReply"
                                    :disabled="loading"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors">
                                <span x-text="loading ? 'Gönderiliyor...' : 'Gönder'">Gönder</span>
                            </button>
                            <button type="button"
                                    @click="showReplyForm = false; replyBody = ''"
                                    :disabled="loading"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white text-sm font-medium rounded-lg transition-colors">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">
                Henüz yorum yapılmamış. İlk yorumu siz yapın!
            </p>
        </div>
    @endif

    {{-- Yorum Yazma Formu (Sadece login'li kullanıcılar) --}}
    @auth
        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700"
             x-data="{
                 reviewBody: '',
                 loading: false,
                 message: '',
                 messageType: '',
                 showMessage: false,

                 async submitReview() {
                     if (!this.reviewBody.trim()) {
                         this.displayMessage('Lütfen yorumunuzu yazın', 'error');
                         return;
                     }

                     this.loading = true;

                     try {
                         const response = await fetch('/api/reviews/add', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                 'Accept': 'application/json'
                             },
                             body: JSON.stringify({
                                 model_class: '{{ addslashes(get_class($blog)) }}',
                                 model_id: {{ $blog->id }},
                                 review_body: this.reviewBody
                             })
                         });

                         const data = await response.json();

                         if (data.success) {
                             this.displayMessage('Yorumunuz onay bekliyor. Teşekkürler!', 'success');
                             this.reviewBody = '';
                         } else {
                             this.displayMessage(data.message || 'Bir hata oluştu', 'error');
                         }
                     } catch (error) {
                         console.error('Review submit error:', error);
                         this.displayMessage('Bir hata oluştu', 'error');
                     } finally {
                         this.loading = false;
                     }
                 },

                 displayMessage(msg, type) {
                     this.message = msg;
                     this.messageType = type;
                     this.showMessage = true;

                     setTimeout(() => {
                         this.showMessage = false;
                     }, 5000);
                 }
             }">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                Yorum Yapın
            </h3>
            <form @submit.prevent="submitReview" class="space-y-4">
                <div>
                    <textarea x-model="reviewBody"
                              rows="4"
                              placeholder="Düşüncelerinizi paylaşın..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white resize-none"
                              required></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Yorumunuz yayınlanmadan önce onaylanacaktır
                        </p>
                        <div x-show="showMessage" x-transition class="mt-2 text-sm font-medium" :class="{
                            'text-green-600 dark:text-green-400': messageType === 'success',
                            'text-red-600 dark:text-red-400': messageType === 'error'
                        }">
                            <span x-text="message"></span>
                        </div>
                    </div>
                    <button type="submit"
                            :disabled="loading"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                        <span x-text="loading ? 'Gönderiliyor...' : 'Gönder'">Gönder</span>
                    </button>
                </div>
            </form>
        </div>
    @endauth
</section>
