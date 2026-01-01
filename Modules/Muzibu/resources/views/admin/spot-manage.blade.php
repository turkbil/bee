@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
{{ $spotId ? 'Spot Düzenle' : 'Yeni Spot' }}
@endsection

@section('content')
    @livewire('muzibu::admin.spot-manage-component', ['id' => $spotId])
@endsection
