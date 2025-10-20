@extends('admin.layout')

@section('content')
    @include('mediamanagement::admin.helper')

    <div class="row g-4 mt-1">
        <div class="col-xl-9 col-lg-8">
            <livewire:mediamanagement::media-library-manager />
        </div>
        <div class="col-xl-3 col-lg-4">
            @include('mediamanagement::admin.partials.about-card')
        </div>
    </div>
@endsection
