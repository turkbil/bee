@extends('portfolio::front.themes.blank.layouts.app')

@section('portfolio_content')
<div class="card">
    <div class="card-header">
        <h1>{{ $category->title }} Kategorisi</h1>
    </div>
    <div class="card-body">
        @if(trim(strip_tags($category->body)) !== '')
        <div class="mb-6">
            {!! $category->body !!}
        </div>
        @endif

        @if($portfolios->count() > 0)
        <div class="space-y-4">
            @foreach($portfolios as $portfolio)
            <div class="list-item">
                <h3><a href="{{ route('portfolios.show', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                <div class="meta">
                    <span>Oluşturma: {{ $portfolio->created_at->format('d.m.Y') }}</span>
                </div>

                @if($portfolio->getFirstMedia('image'))
                <div class="mt-3 mb-3">
                    <a href="{{ route('portfolios.show', $portfolio->slug) }}">
                        <img src="{{ $portfolio->getFirstMedia('image')->getUrl() }}" alt="{{ $portfolio->title }}"
                            class="img-fluid">
                    </a>
                </div>
                @endif

                @if($portfolio->metadesc)
                <div class="summary">
                    <p>{{ Str::limit($portfolio->metadesc, 150) }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $portfolios->links() }}
        </div>
        @else
        <p>Bu kategoride henüz portfolyo bulunmamaktadır.</p>
        @endif

        <div class="mt-6">
            <a href="{{ route('portfolios.index') }}" class="btn btn-secondary">← Tüm Portfolyolar</a>
        </div>
    </div>
</div>
@endsection