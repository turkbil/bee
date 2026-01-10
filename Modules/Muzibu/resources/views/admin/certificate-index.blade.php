@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
Belge Yonetimi
@endsection

@section('content')
    @livewire('muzibu::admin.certificate-component')
@endsection
