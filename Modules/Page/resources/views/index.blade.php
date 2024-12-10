@extends('admin.layout')
@include('page::helper')

@section('content')
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    @livewire('page-component')
@endsection
