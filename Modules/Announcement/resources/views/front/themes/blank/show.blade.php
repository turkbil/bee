@extends('announcement-themes.blank.layouts.default')

@section('announcement_content')
    <div>
        <h2>{{ $announcement->title }}</h2>
        
        <div class="meta">
            <span>Oluşturma: {{ $announcement->created_at->format('d.m.Y') }}</span>
            <span>Görüntülenme: {{ views($announcement)->count() }}</span>
        </div>
        
        <div class="announcement-content">
            {!! $announcement->body !!}
        </div>
        
        <div class="announcement-actions">
            <a href="{{ route('announcements.index') }}">← Tüm Duyurular</a>
        </div>
    </div>
@endsection