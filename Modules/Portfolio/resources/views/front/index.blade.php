@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>Portfolyolar (Fallback View)</h1>
        
        @if($portfolios->count() > 0)
            <div class="portfolio-list">
                @foreach($portfolios as $portfolio)
                    <div class="portfolio-item">
                        <h3><a href="{{ route('portfolios.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                        @if($portfolio->category)
                            <div class="category">
                                Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">{{ $portfolio->category->title }}</a>
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