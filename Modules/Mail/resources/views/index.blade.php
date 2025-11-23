@extends('mail::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('mail.name') !!}</p>
@endsection
