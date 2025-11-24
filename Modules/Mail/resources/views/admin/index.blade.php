@extends('admin.layout')

@section('pretitle')
Mail Şablonları
@endsection

@push('title')
Mail Yönetimi
@endpush

@section('content')
    @livewire('mail::admin.mail-component')
@endsection
