@extends('thememanagement::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('thememanagement.name') !!}</p>
@endsection
