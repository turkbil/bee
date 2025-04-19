<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfalar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .page-item { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sayfalar (Fallback View)</h1>
        
        @if($pages->count() > 0)
            <div class="page-list">
                @foreach($pages as $page)
                    <div class="page-item">
                        <h3><a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a></h3>
                    </div>
                @endforeach
            </div>
            
            {{ $pages->links() }}
        @else
            <p>Henüz sayfa bulunmamaktadır.</p>
        @endif
    </div>
</body>
</html>