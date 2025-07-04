@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('title', 'AI Modülü')

@push('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">AI Modülü</li>
        </ol>
    </nav>
@endpush

@push('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">AI Modülü</div>
            <h2 class="page-title">AI Sohbet Paneli</h2>
            <div class="page-subtitle text-muted">
                Yapay zeka ile sohbet edin ve AI özelliklerini yönetin
            </div>
        </div>
        <div class="col-auto">
            <div class="btn-list">
                <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-primary">
                    <i class="fas fa-cog me-2"></i>Ayarlar
                </a>
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-magic me-2"></i>AI Özellikleri
                </a>
            </div>
        </div>
    </div>
@endpush

@section('content')
    @livewire('ai::admin.chat-panel')
@endsection