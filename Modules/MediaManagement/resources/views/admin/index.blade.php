@extends('admin.layout')

@section('content')
    @include('mediamanagement::admin.helper')

    <div class="mt-1">
        <livewire:mediamanagement::media-library-manager />
    </div>
@endsection
