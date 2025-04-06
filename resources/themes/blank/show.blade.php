@extends('themes.blank.layouts.default')

@section('content')
    <div>
        <h2>{{ $page->title }}</h2>
        
        <div class="meta">
            <span>Oluşturma: {{ $page->created_at->format('d.m.Y') }}</span>
        </div>
        
        <div class="page-content">
            {!! $page->body !!}
        </div>
        
        <div class="page-actions">
            <a href="{{ route('pages.index') }}">← Tüm Sayfalar</a>
        </div>
    </div>
@endsection