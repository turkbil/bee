@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
Kurumsal Spotlar
@endsection

@section('content')
    @livewire('muzibu::admin.spot-component')
@endsection
