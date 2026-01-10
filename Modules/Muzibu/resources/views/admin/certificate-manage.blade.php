@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
{{ $certificateId ? 'Belge Duzenle' : 'Yeni Belge' }}
@endsection

@section('content')
    @livewire('muzibu::admin.certificate-manage-component', ['certificateId' => $certificateId])
@endsection
