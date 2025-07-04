@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Yönetimi')
@section('title', 'Gerçek Zamanlı Token Dashboard')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .dashboard-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    /* Hızlı işlemler ve aktiviteler için ikon düzenlemeleri */
    .list-group-item {
        padding: 0.75rem 1rem !important;
    }
    
    .list-group-item i {
        font-size: 1.25rem !important;
        width: 24px !important;
        text-align: center !important;
    }
    
    .activity-item {
        padding: 0.75rem 1rem !important;
    }
    
    .activity-item i {
        font-size: 1.25rem !important;
        width: 24px !important;
        text-align: center !important;
    }
    .refresh-indicator {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .chart-container {
        position: relative;
        height: 200px;
    }
    .activity-item {
        border-left: 3px solid #0d6efd;
        padding-left: 15px;
        margin-bottom: 10px;
    }
    .activity-item.recent {
        border-left-color: #20c997;
        background: rgba(32, 201, 151, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('content')
    @livewire('ai::admin.a-i-features-dashboard')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh timer
    let refreshTimer;
    
    // Listen for Livewire events
    window.addEventListener('refreshTimer', function() {
        clearTimeout(refreshTimer);
        refreshTimer = setTimeout(function() {
            if (window.Livewire && window.Livewire.all().length > 0) {
                window.Livewire.all()[0].call('loadData');
            }
        }, 5000); // 5 seconds default
    });
    
    window.addEventListener('dataRefreshed', function() {
        // Show success indicator
        const indicator = document.querySelector('.refresh-indicator');
        if (indicator) {
            indicator.classList.add('text-success');
            setTimeout(() => {
                indicator.classList.remove('text-success');
            }, 1000);
        }
    });
});

// Manual refresh function
function refreshDashboard() {
    if (window.Livewire && window.Livewire.all().length > 0) {
        window.Livewire.all()[0].call('loadData');
    }
}
    
// Make function global
window.refreshDashboard = refreshDashboard;
</script>
@endpush