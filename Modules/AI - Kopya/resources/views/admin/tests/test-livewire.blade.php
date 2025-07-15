@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Yönetimi')
@section('title', 'AI Test Paneli - Adminler İçin')

@section('content')
    @livewire('ai::admin.a-i-test-panel')
@endsection