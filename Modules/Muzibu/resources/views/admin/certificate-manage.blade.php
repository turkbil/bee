@extends('admin.layout')
@include('muzibu::admin.helper')

@section('pretitle')
{{ $certificateId ? 'Sertifika Duzenle' : 'Yeni Sertifika' }}
@endsection

@section('content')
    @livewire('muzibu::admin.certificate-manage-component', ['certificateId' => $certificateId])
@endsection
