@extends('admin.layout')

@section('title', __('usermanagement::admin.user_activity_logs'))

@section('content')
    <livewire:usermanagement.user-activity-log-component :id="$id" />
@endsection
