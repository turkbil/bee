@extends('themes.blank.layouts.default')

@section('content')
    <div>
        <h2>Sayfalar</h2>
        
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