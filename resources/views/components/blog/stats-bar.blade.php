@props(['blog'])

@php
    // Favorites count
    $favoritesCount = method_exists($blog, 'favoritesCount') ? $blog->favoritesCount() : 0;
    $isFavorited = auth()->check() && method_exists($blog, 'isFavoritedBy') ? $blog->isFavoritedBy(auth()->id()) : false;

    // Ratings
    $averageRating = method_exists($blog, 'averageRating') ? $blog->averageRating() : 0;
    $ratingsCount = method_exists($blog, 'ratingsCount') ? $blog->ratingsCount() : 0;

    // Views (views_count kolonu varsa)
    $viewsCount = $blog->views_count ?? 0;

    // Reading time
    $readingTime = $blog->calculateReadingTime(app()->getLocale());
@endphp

<div class="flex flex-wrap items-center gap-4 md:gap-6" {{ $attributes }}>
    {{-- Reading Time --}}
    @if($readingTime)
        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <i class="fas fa-clock text-blue-500"></i>
            <span class="text-sm font-medium">{{ $readingTime }} dk okuma</span>
        </div>
    @endif

    {{-- Views Count (eğer kolonu varsa) --}}
    @if($viewsCount > 0)
        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <i class="fas fa-eye text-green-500"></i>
            <span class="text-sm font-medium">{{ number_format($viewsCount) }} görüntülenme</span>
        </div>
    @endif

    {{-- Favorites --}}
    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
        <i class="far fa-heart text-lg"></i>
        <span class="text-sm font-medium">{{ $favoritesCount }} favori</span>
    </div>

    {{-- Ratings --}}
    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
        <div class="flex items-center gap-1">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= floor($averageRating))
                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                @elseif($i - $averageRating < 1 && $i - $averageRating > 0)
                    <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                @else
                    <i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>
                @endif
            @endfor
        </div>
        <span class="text-sm font-medium">
            {{ number_format($averageRating, 1) }}
            @if($ratingsCount > 0)
                ({{ $ratingsCount }})
            @endif
        </span>
    </div>
</div>
