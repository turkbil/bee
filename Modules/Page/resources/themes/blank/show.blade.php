@extends('themes.blank.layouts.default')

@section('content')
    <div>
        <h2>{{ $page->title }} (Modül View)</h2>
        
        <div class="page-content">
            {!! $page->body !!}
        </div>
        
        <div>
            <a href="{{ route('pages.index') }}">← Tüm Sayfalar</a>
        </div>
    </div>
@endsection