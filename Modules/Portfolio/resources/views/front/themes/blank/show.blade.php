@extends('portfolio::front.themes.blank.layouts.app')

@section('portfolio_content')
<div class="card">
    <div class="card-header">
        <h1>{{ $portfolio->title }}</h1>
        <div class="meta">
            <span>Oluşturma: {{ $portfolio->created_at->format('d.m.Y') }}</span>
            <span>Görüntülenme: {{ views($portfolio)->count() }}</span>
            @if($portfolio->category)
            <span>Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">{{
                    $portfolio->category->title }}</a></span>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($portfolio->getFirstMedia('image'))
        <div class="mb-6">
            <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" alt="{{ $portfolio->title }}"
                class="img-fluid">
        </div>
        @endif

        <div class="portfolio-content">
            {!! $portfolio->body !!}
        </div>

        <div class="mt-6">
            <a href="{{ route('portfolios.index') }}" class="btn btn-secondary">← Tüm Portfolyolar</a>
        </div>
    </div>
</div>
@endsection