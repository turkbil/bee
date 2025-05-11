@extends('page::front.themes.blank.layouts.app')

@section('page_content')
<div class="card">
    <div class="card-header">
        <h1>{{ $page->title }}</h1>
        <div class="meta">
            <span>Oluşturma: {{ $page->created_at->format('d.m.Y') }}</span>
            <span>Görüntülenme: {{ views($page)->count() }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="page-content">
            @parsewidgets($page->body)
        </div>

        <div class="mt-6">
            <a href="{{ route('pages.index') }}" class="btn btn-secondary">← Tüm Sayfalar</a>
        </div>
    </div>
</div>
@endsection