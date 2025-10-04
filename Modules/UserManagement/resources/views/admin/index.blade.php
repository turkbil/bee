@extends('admin.layout')

@section('title', __('admin.user_management'))

@section('content')
    <livewire:usermanagement::user-component />
@endsection