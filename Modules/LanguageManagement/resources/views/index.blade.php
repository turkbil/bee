@extends('languagemanagement::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('languagemanagement.name') !!}</p>
@endsection
