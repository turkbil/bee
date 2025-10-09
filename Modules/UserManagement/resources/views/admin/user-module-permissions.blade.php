@extends('admin.layout')

@section('title', __('usermanagement::admin.user_module_permissions'))

@section('content')
    <livewire:usermanagement.user-module-permission-component :id="$id" />
@endsection
