@extends('admin.layout')

@section('title', __('admin.user_management'))

@section('content')
    <livewire:usermanagement.user-manage-component :id="$id" />
@endsection