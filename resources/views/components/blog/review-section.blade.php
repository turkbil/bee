@props(['blog'])

@php
    // Review & Rating verileri
    $averageRating = method_exists($blog, 'averageRating') ? $blog->averageRating() : 0;
    $ratingsCount = method_exists($blog, 'ratingsCount') ? $blog->ratingsCount() : 0;
    $reviewsCount = method_exists($blog, 'reviewsCount') ? $blog->reviewsCount() : 0;

    // Onaylı yorumlar
    $reviews = method_exists($blog, 'reviews')
        ? $blog->reviews()->where('is_approved', true)->latest()->take(10)->get()
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

<section class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-8" {{ $attributes }}>
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
        {{-- Ortalama Puan --}}
        <div class="text-center md:text-left">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="text-6xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($averageRating, 1) }}
                    </div>
                    <div class="flex items-center justify-center gap-1 mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($averageRating))
                                <i class="fas fa-star text-yellow-400 text-xl"></i>
                            @elseif($i - $averageRating < 1 && $i - $averageRating > 0)
                                <i class="fas fa-star-half-alt text-yellow-400 text-xl"></i>
                            @else
                                <i class="far fa-star text-gray-300 dark:text-gray-600 text-xl"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
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
                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-yellow-400 h-full transition-all duration-300"
                             style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 w-12 text-right">
                        {{ $data['count'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Puan Verme Widget (Sadece login'li kullanıcılar) --}}
    @auth
        <div class="bg-blue-50 dark:bg-gray-900 rounded-xl p-6 mb-10">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Bu yazıyı değerlendirin
            </h3>
            <div x-data="{
                rating: {{ $userRating ?? 0 }},
                hover: 0,
                saving: false
            }">
                <div class="flex items-center gap-2 mb-3">
                    <template x-for="star in 5" :key="star">
                        <button type="button"
                                @click="if(!saving) { rating = star; $nextTick(() => saveRating(star)); }"
                                @mouseenter="hover = star"
                                @mouseleave="hover = 0"
                                class="text-4xl transition-all duration-200 hover:scale-110">
                            <i :class="star <= (hover || rating) ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600'"></i>
                        </button>
                    </template>
                    <span class="ml-3 text-sm text-gray-600 dark:text-gray-400" x-show="rating > 0">
                        <span x-text="rating"></span> / 5
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-show="rating > 0">
                    Puanınız kaydedildi, teşekkürler!
                </p>
            </div>
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 mb-10 text-center">
            <i class="fas fa-lock text-gray-400 text-3xl mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Puan vermek için giriş yapmanız gerekmektedir
            </p>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                <i class="fas fa-sign-in-alt"></i>
                Giriş Yap
            </a>
        </div>
    @endauth

    {{-- Yorumlar Listesi --}}
    @if($reviews->isNotEmpty())
        <div class="space-y-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-comments text-blue-500"></i>
                Kullanıcı Yorumları
            </h3>

            @foreach($reviews as $review)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 transition-all duration-300 hover:shadow-md">
                    {{-- Yorum Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ substr($review->user->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $review->user->name ?? 'Anonim' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $review->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Yorum Puanı --}}
                        @if($review->rating)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star text-yellow-400"></i>
                                @endfor
                            </div>
                        @endif
                    </div>

                    {{-- Yorum İçeriği --}}
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $review->comment }}
                    </p>

                    {{-- Yardımcı oldu mu? --}}
                    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                class="text-sm text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="far fa-thumbs-up"></i>
                            Yardımcı oldu
                        </button>
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
        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                Yorum Yapın
            </h3>
            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <textarea name="comment"
                              rows="4"
                              placeholder="Düşüncelerinizi paylaşın..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white resize-none"
                              required></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Yorumunuz yayınlanmadan önce onaylanacaktır
                    </p>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Gönder
                    </button>
                </div>
            </form>
        </div>
    @endauth
</section>

@once
@push('scripts')
<script>
// Rating kaydetme fonksiyonu
function saveRating(value) {
    // Bu basitleştirilmiş versiyon - gerçek API entegrasyonu eklenebilir
    console.log('Rating saved:', value);

    // Örnek API çağrısı (isteğe bağlı):
    /*
    fetch('/api/blog/{{ $blog->blog_id }}/rating', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ rating: value })
    })
    .then(response => response.json())
    .then(data => console.log('Rating saved successfully:', data))
    .catch(error => console.error('Error saving rating:', error));
    */
}
</script>
@endpush
@endonce
