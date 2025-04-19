<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $announcement->title }} (Fallback View)</h1>
        
        <div>
            {!! $announcement->body !!}
        </div>
        
        <div>
            <a href="{{ route('announcements.index') }}">← Tüm Duyurular</a>
        </div>
    </div>
</body>
</html>