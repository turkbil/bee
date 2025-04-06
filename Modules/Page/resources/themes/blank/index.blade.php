@extends('resources.themes.blank.layouts.default')

@section('content')
    <div class="page-container">
        <h2>Sayfalar</h2>
        
        @if($pages->count() > 0)
            <ul class="page-list">
                @foreach($pages as $page)
                    <li class="page-item">
                        <h3><a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a></h3>
                        <div class="page-meta">
                            <span>Oluşturulma: {{ $page->created_at->format('d.m.Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            {{ $pages->links() }}
        @else
            <p>Henüz sayfa bulunmamaktadır.</p>
        @endif
    </div>
@endsection