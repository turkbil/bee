@extends('page::front.themes.blank.layouts.app')

@section('page_content')
<div class="card">
    <div class="card-header">
        <h1>Sayfalar</h1>
    </div>
    <div class="card-body">
        @if($pages->count() > 0)
        <div class="space-y-4">
            @foreach($pages as $page)
            <div class="list-item">
                <h3><a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a></h3>
                <div class="meta">
                    <span>Oluşturma: {{ $page->created_at->format('d.m.Y') }}</span>
                </div>
                @if($page->metadesc)
                <div class="summary">
                    <p>{{ Str::limit($page->metadesc, 150) }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pages->links() }}
        </div>
        @else
        <p>Henüz sayfa bulunmamaktadır.</p>
        @endif
    </div>
</div>
@endsection