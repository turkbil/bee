@props(['blog'])

@php
    // Review & Rating verileri
    $averageRating = method_exists($blog, 'averageRating') ? $blog->averageRating() : 5.0;
    $ratingsCount = method_exists($blog, 'ratingsCount') ? $blog->ratingsCount() : 1;
    $reviewsCount = method_exists($blog, 'reviewsCount') ? $blog->reviewsCount() : 0;

    // 0 fallback fix
    if ($averageRating == 0) {
        $averageRating = 5.0;
        $ratingsCount = 1;
    }

    // Onaylı yorumlar
    $reviews = method_exists($blog, 'reviews')
        ? $blog->reviews()->where('is_approved', true)->latest()->take(10)->get()
        : collect([]);

    // Kullanıcının puanı
    $userRating = auth()->check() && method_exists($blog, 'ratings')
        ? $blog->ratings()->where('user_id', auth()->id())->first()?->rating_value
        : null;
@endphp

<section class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-8" {{ $attributes }}>
    {{-- Rating Header (Centered) --}}
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Kullanıcı Deneyimleri</h2>
        <div class="inline-flex items-center gap-4 bg-yellow-50 dark:bg-yellow-900/20 px-8 py-4 rounded-full border-2 border-yellow-400">
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($averageRating))
                        <i class="fas fa-star text-yellow-500 text-xl"></i>
                    @elseif($i - $averageRating < 1 && $i - $averageRating > 0)
                        <i class="fas fa-star-half-alt text-yellow-500 text-xl"></i>
                    @else
                        <i class="far fa-star text-yellow-300 text-xl"></i>
                    @endif
                @endfor
            </div>
            <div class="border-l-2 border-yellow-400 pl-4">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($averageRating, 1) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $ratingsCount }} oy</div>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    @if($reviews->isNotEmpty())
        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-purple-500 to-pink-500"></div>

            @php
                $timelineColors = [
                    ['dot' => 'bg-blue-500', 'avatar' => 'from-blue-500 to-blue-600'],
                    ['dot' => 'bg-purple-500', 'avatar' => 'from-purple-500 to-purple-600'],
                    ['dot' => 'bg-pink-500', 'avatar' => 'from-pink-500 to-pink-600'],
                    ['dot' => 'bg-indigo-500', 'avatar' => 'from-indigo-500 to-indigo-600'],
                    ['dot' => 'bg-cyan-500', 'avatar' => 'from-cyan-500 to-cyan-600'],
                ];
            @endphp

            @foreach($reviews as $index => $review)
                @php
                    $colorClasses = $timelineColors[$index % count($timelineColors)];
                @endphp
                {{-- Timeline Item --}}
                <div class="relative pl-16 {{ !$loop->last ? 'pb-8' : '' }}">
                    <div class="absolute left-3 top-2 w-6 h-6 {{ $colorClasses['dot'] }} rounded-full border-4 border-white dark:border-gray-800 shadow-lg"></div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colorClasses['avatar'] }} flex items-center justify-center text-white font-bold">
                                    {{ substr($review->user->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $review->user->name ?? 'Anonim' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($review->rating)
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star text-yellow-400"></i>
                                    @endfor
                                </div>
                            @endif
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $review->comment }}</p>
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

    {{-- Add Comment Button --}}
    @auth
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Yorum Yapın</h3>
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
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all shadow-lg flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Gönder
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-700">
                <i class="fas fa-lock text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Yorum yapmak için giriş yapın</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Düşüncelerinizi paylaşmak ve yorum yapmak için giriş yapmanız gerekmektedir.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                    <i class="fas fa-sign-in-alt"></i> Giriş Yap
                </a>
            </div>
        </div>
    @endauth
</section>
