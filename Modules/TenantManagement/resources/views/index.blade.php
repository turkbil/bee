{{-- Modules/TenantManagement/resources/views/index.blade.php --}}
@extends('admin.layout')
@include('tenant::helper')

@section('content')
    @livewire('tenant-component')
@endsection
