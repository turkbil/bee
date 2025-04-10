<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->title }} Kategorisi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .portfolio-item { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; }
    </style>
</head>
<body>
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
</body>
</html>