@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
Kurumsal Hesaplar
@endsection

@section('content')
    @livewire('muzibu::admin.corporate-account-component')
@endsection
