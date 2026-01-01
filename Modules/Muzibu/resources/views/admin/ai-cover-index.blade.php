@include('muzibu::admin.helper')
@extends('admin.layout')

@section('content')
    @livewire(\Modules\Muzibu\App\Http\Livewire\Admin\AICoverComponent::class)
@endsection
