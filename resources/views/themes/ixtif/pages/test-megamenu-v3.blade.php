@extends('themes.ixtif.layout')

@section('title', 'Test Mega Menu V3')

@section('content')
<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold mb-8">Test Mega Menu V3</h1>

    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Transpalet (Category ID: 2)</h2>
        <livewire:theme.mega-menu-v3 :categoryId="2" />
    </div>
</div>
@endsection
