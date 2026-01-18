@extends('themes.simple.layouts.app')

@section('content')

    {{-- Hero Section --}}
    <section class="bg-gradient-to-r from-slate-800 to-slate-900 py-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                {{ setting('site_slogan') ?? setting('site_name') }}
            </h1>
            <p class="text-xl opacity-90 mb-8">
                {{ setting('site_description') }}
            </p>
            <a href="{{ url('/iletisim') }}" class="inline-flex items-center px-8 py-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow-lg transition-all">
                <i class="fas fa-phone-alt mr-2"></i>
                Teklif Al
            </a>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            @if(isset($item) && $item->content)
                <div class="prose dark:prose-invert max-w-none">
                    {!! $item->content !!}
                </div>
            @endif
        </div>
    </section>

@endsection
