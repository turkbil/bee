@extends('admin.layout')

@section('title', __('usermanagement::admin.role_management'))

@section('content')
    <livewire:usermanagement.role-manage-component :id="$id" />
@endsection
