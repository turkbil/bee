@extends('announcement::front.themes.blank.layouts.default')

@section('announcement_content')
    <div>
        <h2>Duyurular</h2>
        
        @if($announcements->count() > 0)
            <div class="announcement-list">
                @foreach($announcements as $announcement)
                    <div class="announcement-item">
                        <h3><a href="{{ route('announcements.show', $announcement->slug) }}">{{ $announcement->title }}</a></h3>
                        <div class="meta">
                            <span>Oluşturma: {{ $announcement->created_at->format('d.m.Y') }}</span>
                        </div>
                        @if($announcement->metadesc)
                            <div class="summary">
                                <p>{{ Str::limit($announcement->metadesc, 150) }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{ $announcements->links() }}
        @else
            <p>Henüz duyuru bulunmamaktadır.</p>
        @endif
    </div>
@endsection