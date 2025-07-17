{{-- resources\views\admin\index.blade.php --}}
@extends('admin.layout')
@section('content')
    <div>
        {{-- Livewire Dashboard Widget Component --}}
        @livewire('admin.dashboard-widget')
    </div>
@endsection

