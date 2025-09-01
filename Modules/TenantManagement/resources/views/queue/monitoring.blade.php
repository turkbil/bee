@extends('admin.layout')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                {{ __('tenantmanagement::admin.queue_monitoring') }}
            </h1>
        </div>
    </div>
</div>

<livewire:queuemonitoringcomponent />
@endsection