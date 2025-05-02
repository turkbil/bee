@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>{{ $announcement->title }}</h1>

        <div>
            {!! $announcement->body !!}
        </div>

        <div>
            <a href="{{ route('announcements.index') }}">← Tüm Duyurular</a>
        </div>
    </div>
@endsection