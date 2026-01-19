{{--
    Minimal Subheader Component - Sadece başlık ve breadcrumb

    Usage:
    @include('components.subheaders.minimal', [
        'title' => 'Page Title',
        'breadcrumbs' => [
            ['label' => 'Ana Sayfa', 'url' => '/'],
            ['label' => 'Blog']
        ]
    ])
--}}

@php
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

<section class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-6">
        @if(!empty($breadcrumbs))
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                @foreach($breadcrumbs as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">
                            {{ $crumb['label'] }}
                        </a>
                        @if($index < count($breadcrumbs) - 1)
                            <span class="mx-2">/</span>
                        @endif
                    @else
                        <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
    </div>
</section>
