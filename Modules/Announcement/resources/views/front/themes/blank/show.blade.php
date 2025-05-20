@extends('announcement::front.themes.blank.layouts.app')

@section('announcement_content')
<div class="card">
    <div class="card-header">
        <h1>{{ $announcement->title }}</h1>
        <div class="meta">
            <span>Oluşturma: {{ $announcement->created_at->format('d.m.Y') }}</span>
            <span>Görüntülenme: {{ views($announcement)->count() }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="announcement-content">
            {!! $announcement->body !!}
        </div>

        <div class="mt-6">
            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">← Tüm Duyurular</a>
        </div>
    </div>
</div>
@endsection