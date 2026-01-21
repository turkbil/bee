@extends('themes.t-6.layouts.app')

@php
    // Controller'dan gelen $item'ı $page olarak kullan
    $page = $item;

    // Page icons mapping
    $pageIcons = [
        'hakkimizda' => 'fa-building-columns',
        'iletisim' => 'fa-envelope',
    ];
    $pageIcon = $pageIcons[$page->slug] ?? 'fa-file-lines';
@endphp

@section('content')

{{-- Page Header --}}
<section class="relative pt-24 pb-10 overflow-hidden bg-gradient-to-b from-slate-100 to-white dark:from-slate-950 dark:to-slate-900">
    {{-- Decorative Line --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="mb-6" data-aos="fade-up">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ url('/') }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Ana Sayfa</a></li>
                <li class="text-slate-400 dark:text-slate-500">/</li>
                <li class="text-slate-600 dark:text-slate-300">{{ $page->title }}</li>
            </ol>
        </nav>

        <div class="flex items-center gap-6" data-aos="fade-up" data-aos-delay="100">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-xl bg-amber-500/20 dark:bg-amber-500/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                <i class="fat {{ $pageIcon }} text-3xl text-amber-700 dark:text-amber-400"></i>
            </div>

            <div>
                <h1 class="font-heading text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ $page->title }}
                </h1>
                @if($page->summary)
                <p class="text-slate-700 dark:text-slate-300 text-base max-w-2xl">
                    {{ Str::limit($page->summary, 150) }}
                </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Page Content from DB --}}
@if($page->body)
    {!! $page->body !!}
@else
<section class="py-16 md:py-24 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-slate-500 dark:text-slate-400">Bu sayfa için içerik henüz eklenmemiş.</p>
        </div>
    </div>
</section>
@endif

@endsection
