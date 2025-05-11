@extends('portfolio::front.themes.blank.layouts.app')

@section('portfolio_content')
<div class="card">
    <div class="card-header">
        <h1>Portfolyolar</h1>
    </div>
    <div class="card-body">
        @if($portfolios->count() > 0)
        <div class="space-y-4">
            @foreach($portfolios as $portfolio)
            <div class="list-item">
                <h3><a href="{{ route('portfolios.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                <div class="meta">
                    <span>Oluşturma: {{ $portfolio->created_at->format('d.m.Y') }}</span>
                    @if($portfolio->category)
                    <span>Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">{{
                            $portfolio->category->title }}</a></span>
                    @endif
                </div>

                @if($portfolio->getFirstMedia('image'))
                <div class="mt-3 mb-3">
                    <a href="{{ route('portfolios.show', $portfolio->slug) }}">
                        <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" alt="{{ $portfolio->title }}"
                            class="img-fluid">
                    </a>
                </div>
                @endif

                @if($portfolio->metadesc)
                <div class="summary">
                    <p>{{ Str::limit($portfolio->metadesc, 150) }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $portfolios->links() }}
        </div>
        @else
        <p>Henüz portfolyo bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection