@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>Duyurular</h1>

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
@endsection