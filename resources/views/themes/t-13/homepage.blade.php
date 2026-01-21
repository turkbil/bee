@extends('themes.t-13.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">{{ setting('site_title') ?: 'Hoş Geldiniz' }}</h1>
        <p class="text-gray-600">{{ setting('site_description') }}</p>
    </div>
</div>
@endsection