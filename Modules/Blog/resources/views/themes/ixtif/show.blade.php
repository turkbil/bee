@extends('themes.blank.layouts.app')

@push('head')
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('module_content')
    @include('blog::themes.blank.partials.show-content', ['item' => $item])
@endsection
