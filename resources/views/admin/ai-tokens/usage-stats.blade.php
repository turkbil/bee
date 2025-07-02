@extends('admin.layout')

@include('ai::admin.helper')

@section('content')
    @livewire('admin.ai-token-usage-stats-component')
@endsection