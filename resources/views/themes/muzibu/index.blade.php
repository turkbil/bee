@extends('themes.muzibu.layouts.app-new')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
{{-- Top Pills --}}
<div class="muzibu-top-pills">
    <button class="muzibu-pill active">Tümü</button>
    <button class="muzibu-pill">Müzik</button>
    <button class="muzibu-pill">Podcast'ler</button>
</div>

{{-- Horizontal Scroll Section --}}
<div class="muzibu-horizontal-section">
    <div class="muzibu-horizontal-cards">
        @for($i = 0; $i < 8; $i++)
        <div class="muzibu-horizontal-card">
            <div class="muzibu-card-image" style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                <span style="font-size: 48px;">🎵</span>
                <div class="muzibu-play-overlay">
                    <i class="fas fa-play"></i>
                </div>
            </div>
            <div class="muzibu-card-title">Playlist {{ $i + 1 }}</div>
            <div class="muzibu-card-subtitle">{{ mt_rand(10, 150) }} şarkı</div>
        </div>
        @endfor
    </div>
</div>

{{-- Content Section 1 --}}
<div class="muzibu-content-section">
    <div class="muzibu-section-header">
        <h2 class="muzibu-section-title">Beğenebileceğin bölümler</h2>
        <a href="#" class="muzibu-section-link">Tümünü göster</a>
    </div>
    <div class="muzibu-cards-grid">
        @for($i = 0; $i < 10; $i++)
        <div class="muzibu-card">
            <div class="muzibu-card-image" style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                <span style="font-size: 48px;">{{ ['🎵', '🎸', '🎤', '🎧', '🎹'][mt_rand(0, 4)] }}</span>
                <div class="muzibu-play-overlay">
                    <i class="fas fa-play"></i>
                </div>
            </div>
            <div class="muzibu-card-title">Album {{ $i + 1 }}</div>
            <div class="muzibu-card-subtitle">Sanatçı • {{ mt_rand(5, 20) }} şarkı</div>
        </div>
        @endfor
    </div>
</div>

{{-- Content Section 2 --}}
<div class="muzibu-content-section">
    <div class="muzibu-section-header">
        <h2 class="muzibu-section-title">Yeni çıkanlar</h2>
        <a href="#" class="muzibu-section-link">Tümünü göster</a>
    </div>
    <div class="muzibu-cards-grid">
        @for($i = 0; $i < 5; $i++)
        <div class="muzibu-card">
            <div class="muzibu-card-image" style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                <span style="font-size: 48px;">{{ ['🎼', '🎺', '🥁', '🎻', '🎷'][mt_rand(0, 4)] }}</span>
                <div class="muzibu-play-overlay">
                    <i class="fas fa-play"></i>
                </div>
            </div>
            <div class="muzibu-card-title">Yeni Album {{ $i + 1 }}</div>
            <div class="muzibu-card-subtitle">{{ date('Y') }} • {{ mt_rand(8, 15) }} şarkı</div>
        </div>
        @endfor
    </div>
</div>
@endsection
