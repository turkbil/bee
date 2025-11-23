@extends('subscription::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('subscription.name') !!}</p>
@endsection
