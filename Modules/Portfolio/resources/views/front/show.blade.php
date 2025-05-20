@extends('themes.blank.layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $portfolio->title }}</h1>

        @if($portfolio->category)
            <div class="category">
                Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">{{ $portfolio->category->title }}</a>
            </div>
        @endif

        @if($portfolio->getFirstMedia('image'))
            <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" alt="{{ $portfolio->title }}">
        @endif

        <div>
            {!! $portfolio->body !!}
        </div>

        <div>
            <a href="{{ route('portfolios.index') }}">← Tüm Portfolyolar</a>
        </div>
    </div>
@endsection