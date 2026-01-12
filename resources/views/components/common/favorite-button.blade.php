@props([
    'model' => null,
    'size' => 'md',      // sm, md, lg
    'showCount' => false, // Favori sayısı gösterilsin mi? (varsayılan kapalı)
])

@php
    if (!$model) {
        return;
    }

    $favoritesCount = method_exists($model, 'favoritesCount') ? $model->favoritesCount() : 0;
    $isFavorited = auth()->check() && method_exists($model, 'isFavoritedBy') ? $model->isFavoritedBy(auth()->id()) : false;

    $modelClass = get_class($model);
    $modelId = $model->blog_id
        ?? $model->product_id
        ?? $model->page_id
        ?? $model->song_id
        ?? $model->album_id
        ?? $model->playlist_id
        ?? $model->genre_id
        ?? $model->sector_id
        ?? $model->id
        ?? null;

    if (!$modelId) {
        return;
    }

    // Size classes - buton boyutları
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-lg',
        'lg' => 'w-12 h-12 text-xl',
    ];

    $btnSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

@auth
    <button type="button"
            class="{{ $btnSize }} bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:scale-110 hover:bg-black/70 transition-all duration-200 shadow-lg"
            x-data="{
                favorited: {{ $isFavorited ? 'true' : 'false' }},
                count: {{ $favoritesCount }},
                loading: false,
                toggleFavorite() {
                    if (this.loading) return;
                    this.loading = true;

                    fetch('/api/favorites/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            model_class: '{{ addslashes($modelClass) }}',
                            model_id: {{ $modelId }}
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.favorited = data.data.is_favorited;
                            this.count = data.data.favorites_count;
                        }
                    })
                    .catch(error => console.error('Favorite error:', error))
                    .finally(() => this.loading = false);
                }
            }"
            @click.stop="toggleFavorite()"
            {{ $attributes }}>
        <i :class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-white'"
           class="transition-colors drop-shadow"></i>
    </button>
@else
    <button type="button"
            class="{{ $btnSize }} bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:scale-110 hover:bg-black/70 transition-all duration-200 shadow-lg"
            @click.stop="window.location.href = '{{ route('login') }}'"
            {{ $attributes }}>
        <i class="far fa-heart text-white drop-shadow"></i>
    </button>
@endauth
