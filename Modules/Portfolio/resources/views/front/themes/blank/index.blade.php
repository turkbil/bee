@extends('portfolio-themes.blank.layouts.default')

@section('portfolio_content')
    <div>
        <h2>Portfolyolar</h2>
        
        @if($portfolios->count() > 0)
            <div class="portfolio-list">
                @foreach($portfolios as $portfolio)
                    <div class="portfolio-item">
                        <h3><a href="{{ route('portfolios.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
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
                                <a href="{{ route('portfolios.show', $portfolio->slug) }}">
                                    <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" 
                                        alt="{{ $portfolio->title }}" class="img-fluid">
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
            
            {{ $portfolios->links() }}
        @else
            <p>Henüz portfolyo bulunmamaktadır.</p>
        @endif
    </div>
@endsection