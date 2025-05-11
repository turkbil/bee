@extends('announcement::front.themes.blank.layouts.app')

@section('announcement_content')
<div class="card">
    <div class="card-header">
        <h1>Duyurular</h1>
    </div>
    <div class="card-body">
        @if($announcements->count() > 0)
        <div class="space-y-4">
            @foreach($announcements as $announcement)
            <div class="list-item">
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

        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
        @else
        <p>Henüz duyuru bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection