<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyurular</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .announcement-item { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Duyurular (Fallback View)</h1>
        
        @if($announcements->count() > 0)
            <div class="announcement-list">
                @foreach($announcements as $announcement)
                    <div class="announcement-item">
                        <h3><a href="{{ route('announcements.show', $announcement->slug) }}">{{ $announcement->title }}</a></h3>
                    </div>
                @endforeach
            </div>
            
            {{ $announcements->links() }}
        @else
            <p>Henüz duyuru bulunmamaktadır.</p>
        @endif
    </div>
</body>
</html>