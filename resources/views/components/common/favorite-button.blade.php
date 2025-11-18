@props([
    'model' => null,
    'size' => 'md',      // sm, md, lg
    'showText' => true,   // Favori text gösterilsin mi?
    'iconOnly' => false,  // Sadece ikon
])

@php
    if (!$model) {
        return; // Model yoksa render etme
    }

    $favoritesCount = method_exists($model, 'favoritesCount') ? $model->favoritesCount() : 0;
    $isFavorited = auth()->check() && method_exists($model, 'isFavoritedBy') ? $model->isFavoritedBy(auth()->id()) : false;
    
    // Model class ve ID al
    $modelClass = get_class($model);
    $modelId = $model->blog_id ?? $model->product_id ?? $model->page_id ?? $model->id ?? null;
    
    if (!$modelId) {
        return; // Model ID yoksa render etme
    }

    // Size class
    $sizeClasses = [
        'sm' => [
            'icon' => 'text-sm',
            'text' => 'text-xs',
            'gap' => 'gap-1',
        ],
        'md' => [
            'icon' => 'text-lg',
            'text' => 'text-sm',
            'gap' => 'gap-2',
        ],
        'lg' => [
            'icon' => 'text-xl',
            'text' => 'text-base',
            'gap' => 'gap-2',
        ],
    ];

    $classes = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

@auth
    <div class="flex items-center {{ $classes['gap'] }} cursor-pointer hover:scale-110 transition-transform duration-200"
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
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
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
         @click="toggleFavorite()"
         {{ $attributes }}>
        <i :class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-600 dark:text-gray-400'" 
           class="{{ $classes['icon'] }} transition-colors"></i>
        
        @if($showText && !$iconOnly)
            <span class="{{ $classes['text'] }} font-medium text-gray-600 dark:text-gray-400" x-text="count + ' favori'"></span>
        @elseif($iconOnly && $favoritesCount > 0)
            <span class="{{ $classes['text'] }} font-semibold text-gray-600 dark:text-gray-400" x-text="count"></span>
        @endif
    </div>
@else
    <div class="flex items-center {{ $classes['gap'] }} text-gray-400 dark:text-gray-500 cursor-pointer"
         @click="window.location.href = '{{ route('login') }}'"
         {{ $attributes }}>
        <i class="far fa-heart {{ $classes['icon'] }}"></i>
        @if($showText && !$iconOnly)
            <span class="{{ $classes['text'] }} font-medium">{{ $favoritesCount }} favori</span>
        @elseif($iconOnly && $favoritesCount > 0)
            <span class="{{ $classes['text'] }} font-semibold">{{ $favoritesCount }}</span>
        @endif
    </div>
@endauth
