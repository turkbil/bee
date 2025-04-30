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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

</head>
<body>
    <div class="container">
        <h1>Sayfalar (Fallback View)</h1>

        <div class="card p-3 mb-4">
            @widget(2)
        </div>
        <div class="card p-3 mb-4">
            @widget(1)
        </div>
        
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin/libs/handlebars/handlebars.min.js') }}"></script>

</body>
</html>