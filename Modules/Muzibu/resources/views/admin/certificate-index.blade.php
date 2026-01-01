@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
Sertifika Yonetimi
@endsection

@section('content')
    @livewire('muzibu::admin.certificate-component')
@endsection
