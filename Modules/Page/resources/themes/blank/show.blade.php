@extends('resources.themes.blank.layouts.default')

@section('content')
    <div class="page-detail">
        <h2>{{ $page->title }}</h2>
        
        <div class="page-meta">
            <span>Oluşturulma: {{ $page->created_at->format('d.m.Y') }}</span>
        </div>
        
        <div class="page-content">
            {!! $page->body !!}
        </div>
        
        <div class="page-actions">
            <a href="{{ route('pages.index') }}" class="btn btn-back">← Tüm Sayfalar</a>
        </div>
    </div>
@endsection