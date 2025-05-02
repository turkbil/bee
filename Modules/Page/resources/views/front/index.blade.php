@extends('themes.blank.layouts.default')

@section('content')
    <div class="container">
        <h1>Sayfalar</h1>

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
@endsection