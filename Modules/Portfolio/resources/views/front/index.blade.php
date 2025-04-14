<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolyolar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .portfolio-item { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; }
    </style>
</head>
<body>
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
</body>
</html>