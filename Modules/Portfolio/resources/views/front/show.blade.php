<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portfolio->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .featured-image { max-width: 100%; height: auto; margin-bottom: 20px; }
        .category { margin-bottom: 15px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $portfolio->title }} (Fallback View)</h1>
        
        @if($portfolio->category)
            <div class="category">
                Kategori: <a href="{{ route('portfolios.category', $portfolio->category->slug) }}">{{ $portfolio->category->title }}</a>
            </div>
        @endif
        
        @if($portfolio->getFirstMedia('image'))
            <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" class="featured-image" alt="{{ $portfolio->title }}">
        @endif
        
        <div>
            {!! $portfolio->body !!}
        </div>
        
        <div>
            <a href="{{ route('portfolios.index') }}">← Tüm Portfolyolar</a>
        </div>
    </div>
</body>
</html>