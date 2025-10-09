@extends('admin.layout')

@section('title', __('usermanagement::admin.permission_management'))

@section('content')
    <livewire:usermanagement.permission-manage-component :id="$id" />
@endsection
