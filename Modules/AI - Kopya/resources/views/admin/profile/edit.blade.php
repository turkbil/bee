@extends('admin.layout')

@include('ai::helper')

@section('content')
@livewire('ai::admin.ai-profile-management', ['initialStep' => $initialStep ?? 1])
@endsection