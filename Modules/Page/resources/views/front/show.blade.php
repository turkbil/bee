@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>{{ $page->title }}</h1>

        <div>
            @parsewidgets($page->body)
        </div>

        <div>
            <a href="{{ route('pages.index') }}">← Tüm Sayfalar</a>
        </div>
    </div>
@endsection