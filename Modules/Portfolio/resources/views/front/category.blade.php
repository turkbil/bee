@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>{{ $category->title }} Kategorisi (Fallback View)</h1>
        
        @if(trim(strip_tags($category->body)) !== '')
            <div class="category-description">
                {!! $category->body !!}
            </div>
        @endif
        
        @if($portfolios->count() > 0)
            <div class="portfolio-list">
                @foreach($portfolios as $portfolio)
                    <div class="portfolio-item">
                        <h3><a href="{{ route('portfolios.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                        
                        @if($portfolio->getFirstMedia('image'))
                            <img src="{{ $portfolio->getFirstMedia('image')->getUrl('thumb') }}" 
                                 alt="{{ $portfolio->title }}" style="max-width: 150px;">
                        @endif
                        
                        @if($portfolio->metadesc)
                            <p>{{ Str::limit($portfolio->metadesc, 150) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{ $portfolios->links() }}
        @else
            <p>Bu kategoride henüz portfolyo bulunmamaktadır.</p>
        @endif
        
        <div>
            <a href="{{ route('portfolios.index') }}">← Tüm Portfolyolar</a>
        </div>
    </div>
@endsection