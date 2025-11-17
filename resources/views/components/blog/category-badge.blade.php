@props(['category', 'size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'text-xs px-2 py-1',
        'md' => 'text-sm px-3 py-1.5',
        'lg' => 'text-base px-4 py-2',
    ];

    $class = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

@if($category)
    <a href="{{ url('/blog/category/' . ($category->slug ?? '')) }}"
       class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 dark:from-blue-500 dark:to-blue-400 dark:hover:from-blue-600 dark:hover:to-blue-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-300 {{ $class }}"
       {{ $attributes }}>
        <i class="fas fa-folder"></i>
        <span>{{ $category }}</span>
    </a>
@endif
