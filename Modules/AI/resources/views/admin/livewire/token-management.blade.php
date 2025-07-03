@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', 'Token Yönetimi')

@section('content')
<div class="row">
    <div class="col-12">
        @livewire('token-management')
    </div>
</div>
@endsection