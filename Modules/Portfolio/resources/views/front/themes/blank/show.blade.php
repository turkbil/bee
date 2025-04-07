@extends('portfolio-themes.blank.layouts.default')

@section('portfolio_content')
    <div>
        <h2>{{ $portfolio->title }}</h2>
        
        <div class="meta">
            <span>Oluşturma: {{ $portfolio->created_at->format('d.m.Y') }}</span>
            @if($portfolio->category)
                <span class="category">
                    | Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">
                        {{ $portfolio->category->title }}
                    </a>
                </span>
            @endif
        </div>
        
        @if($portfolio->getFirstMedia('image'))
            <div class="portfolio-image">
                <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" 
                     alt="{{ $portfolio->title }}" class="img-fluid">
            </div>
        @endif
        
        <div class="portfolio-content">
            {!! $portfolio->body !!}
        </div>
        
        <div class="portfolio-actions">
            <a href="{{ route('portfolios.index') }}">← Tüm Portfolyolar</a>
        </div>
    </div>
@endsection