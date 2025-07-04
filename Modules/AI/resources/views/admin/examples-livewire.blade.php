@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Yönetimi')
@section('title', 'AI Kullanım Örnekleri - Yazılımcılar İçin')

@section('content')
    @livewire('ai::admin.a-i-examples')
@endsection