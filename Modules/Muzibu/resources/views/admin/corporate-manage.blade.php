@include('muzibu::admin.helper')
@extends('admin.layout')

@section('content')
    @livewire('muzibu::admin.corporate-account-manage-component', ['corporateId' => $corporateId])
@endsection
