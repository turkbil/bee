@include('muzibu::admin.helper')
@extends('admin.layout')

@push('title')
HLS Streaming Dokümantasyonu
@endpush

@section('content')
    <div class="row row-deck row-cards">
        {{-- HLS Nedir --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle text-info me-2"></i>
                        HLS Nedir?
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>HLS (HTTP Live Streaming)</strong>, Apple tarafından geliştirilen bir streaming protokolüdür. Normal MP3 dosyalarının aksine, şarkılar küçük parçalara bölünür ve şifrelenir.</p>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card card-sm bg-primary-lt">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-primary text-white me-3">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                        <div>
                                            <strong>Koruma</strong>
                                            <div class="small">AES-128 şifreleme</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-sm bg-success-lt">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-success text-white me-3">
                                            <i class="fas fa-wifi"></i>
                                        </span>
                                        <div>
                                            <strong>Streaming</strong>
                                            <div class="small">Parça parça yükleme</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-sm bg-warning-lt">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-warning text-white me-3">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <div>
                                            <strong>Güvenlik</strong>
                                            <div class="small">İndirilemez</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Database Yapısı --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database text-purple me-2"></i>
                        Database Yapısı
                    </h3>
                </div>
                <div class="card-body">
                    <h4>songs Tablosu Alanları</h4>
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Alan</th>
                                    <th>Tip</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>file_path</code></td>
                                    <td>string</td>
                                    <td>Orijinal MP3 dosyası (örn: "sarki.mp3")</td>
                                </tr>
                                <tr>
                                    <td><code>hls_path</code></td>
                                    <td>string</td>
                                    <td>HLS playlist yolu (örn: "muzibu/hls/ABC123/playlist.m3u8")</td>
                                </tr>
                                <tr>
                                    <td><code>hls_converted</code></td>
                                    <td>boolean</td>
                                    <td>HLS dönüşümü tamamlandı mı?</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mt-4">HLS Olan vs Olmayan Şarkı</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-green-lt">
                                <div class="card-header">
                                    <span class="badge bg-green">HLS OLAN</span>
                                </div>
                                <div class="card-body">
<pre class="mb-0"><code>$song->file_path = 'ornek.mp3';
$song->hls_path = 'muzibu/hls/ABC123/playlist.m3u8';
$song->hls_converted = true;</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-red-lt">
                                <div class="card-header">
                                    <span class="badge bg-red">HLS OLMAYAN</span>
                                </div>
                                <div class="card-body">
<pre class="mb-0"><code>$song->file_path = 'ornek.mp3';
$song->hls_path = null;
$song->hls_converted = false;</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dosya Sistemi --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder-tree text-orange me-2"></i>
                        Dosya Sistemi Yapısı
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Normal MP3</h4>
                            <div class="bg-dark p-3 rounded">
                                <code class="text-cyan">storage/tenant{{ tenant('id') }}/app/public/muzibu/songs/ornek.mp3</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>HLS Dönüştürülmüş</h4>
                            <div class="bg-dark p-3 rounded">
<pre class="mb-0 text-cyan"><code>storage/tenant{{ tenant('id') }}/app/public/muzibu/hls/ABC123/
├── playlist.m3u8      ← Ana playlist
├── enc.key            ← AES-128 şifreleme anahtarı
├── segment-000.ts     ← Şifreli parça 1
├── segment-001.ts     ← Şifreli parça 2
└── ...</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stream URL Sistemi --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-link text-blue me-2"></i>
                        Stream URL Sistemi (v7)
                    </h3>
                </div>
                <div class="card-body">
                    <p>Sistem <code>/stream/play/{hash}/playlist.m3u8</code> formatında URL kullanır. Bu sayede:</p>
                    <ul>
                        <li>Dosya yolu gizlenir</li>
                        <li>CORS kontrolleri yapılır</li>
                        <li>Gelecekte token/auth eklenebilir</li>
                    </ul>

                    <h4 class="mt-3">URL Formatları</h4>
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Tip</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Playlist</td>
                                    <td><code>/stream/play/{hash}/playlist.m3u8</code></td>
                                </tr>
                                <tr>
                                    <td>Encryption Key</td>
                                    <td><code>/stream/key/{hash}</code></td>
                                </tr>
                                <tr>
                                    <td>Chunk</td>
                                    <td><code>/stream/play/{hash}/segment-000.ts</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Çalma Akışı --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-play-circle text-green me-2"></i>
                        Çalma Akışı
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Modern Flow Diagram --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 my-4">
                        {{-- Step 1 --}}
                        <div class="text-center">
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-mouse-pointer text-white"></i>
                            </div>
                            <div class="small fw-medium">Play Tıkla</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted d-none d-md-block"></i>

                        {{-- Step 2 --}}
                        <div class="text-center">
                            <div class="rounded-circle bg-info d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-hashtag text-white"></i>
                            </div>
                            <div class="small fw-medium">Hash Çıkar</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted d-none d-md-block"></i>

                        {{-- Step 3 --}}
                        <div class="text-center">
                            <div class="rounded-circle bg-purple d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-link text-white"></i>
                            </div>
                            <div class="small fw-medium">Stream URL</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted d-none d-md-block"></i>

                        {{-- Step 4 --}}
                        <div class="text-center">
                            <div class="rounded-circle bg-orange d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-download text-white"></i>
                            </div>
                            <div class="small fw-medium">HLS.js Yükle</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted d-none d-md-block"></i>

                        {{-- Step 5 --}}
                        <div class="text-center">
                            <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="fas fa-volume-up text-white"></i>
                            </div>
                            <div class="small fw-medium">Çal</div>
                        </div>
                    </div>

                    {{-- Detaylı Akış Kartları --}}
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="card bg-primary-lt h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2">1</span>
                                        <strong>Play Butonuna Tıkla</strong>
                                    </div>
                                    <p class="small mb-0">Şarkı listesindeki play butonuna tıklanır, şarkı bilgileri fonksiyona gönderilir.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info-lt h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-info me-2">2</span>
                                        <strong>Hash Kontrolü</strong>
                                    </div>
                                    <p class="small mb-0"><code>hls_hash</code> değeri kontrol edilir. Varsa HLS, yoksa MP3 çalınır.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success-lt h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-success me-2">3</span>
                                        <strong>Streaming Başla</strong>
                                    </div>
                                    <p class="small mb-0">HLS.js playlist'i yükler, parçaları çözer ve ses çalmaya başlar.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-4">JavaScript Akışı</h4>
                    <div class="bg-dark p-3 rounded">
<pre class="mb-0 text-cyan"><code>// 1. Play butonuna tıklandığında
playAdminSong({
    id: 15,
    title: 'Şarkı Adı',
    hls_hash: 'ABC123',  // Direkt hash
    file_url: 'storage/muzibu/songs/ornek.mp3',  // Fallback
    is_hls: true
});

// 2. admin-mini-player.js içinde
if (songData.hls_hash) {
    hlsUrl = `/stream/play/${songData.hls_hash}/playlist.m3u8`;
}

// 3. HLS.js ile çal
hls.loadSource(hlsUrl);
hls.attachMedia(audioElement);</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fallback Sistemi --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-random text-yellow me-2"></i>
                        Fallback Sistemi
                    </h3>
                </div>
                <div class="card-body">
                    <p>HLS hata verirse otomatik olarak MP3'e düşer:</p>
                    <ul>
                        <li><strong class="text-danger">keyLoadError:</strong> Şifreleme anahtarı yüklenemezse → MP3'e fallback</li>
                        <li><strong class="text-warning">Network Error:</strong> 2 deneme sonrası → MP3'e fallback</li>
                        <li><strong class="text-info">Media Error:</strong> Recovery denemesi → Başarısızsa MP3'e fallback</li>
                    </ul>

                    <div class="bg-dark p-3 rounded mt-3">
<pre class="mb-0 text-cyan"><code>// Fallback mantığı
if (hlsRetryCount >= maxHlsRetries || data.details === 'keyLoadError') {
    console.log('⚠️ HLS failed, falling back to direct playback...');
    this.playDirect(this.fallbackUrl);
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Panel Göstergeleri --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags text-pink me-2"></i>
                        Admin Panel Göstergeleri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Görsel</th>
                                    <th>Anlamı</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-green"><i class="fas fa-shield-alt"></i></span></td>
                                    <td>HLS hazır</td>
                                    <td><code>hls_converted = true</code></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-yellow"><i class="fas fa-clock"></i></span></td>
                                    <td>HLS bekliyor</td>
                                    <td><code>file_path</code> var, <code>hls_converted = false</code></td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>Dosya yok</td>
                                    <td><code>file_path = null</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Console Mesajları --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-terminal text-dark me-2"></i>
                        Console Mesajları
                    </h3>
                </div>
                <div class="card-body">
                    <p>Browser Console'da görülen mesajlar:</p>
                    <div class="bg-dark p-3 rounded">
<pre class="mb-0 text-cyan"><code>// HLS çaldığında
🎵 Playing HLS: /stream/play/ABC123/playlist.m3u8

// MP3 çaldığında (fallback)
🎵 Playing direct: https://ixtif.com/storage/muzibu/songs/ornek.mp3

// Fallback olduğunda
⚠️ HLS failed, falling back to direct playback...
🎵 Fallback to direct: https://ixtif.com/storage/...</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- v7 Özellikleri --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-pink-lt">
                    <h3 class="card-title">
                        <i class="fas fa-crown text-pink me-2"></i>
                        v7 Ultimate Edition Özellikleri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>256kbps AAC:</strong> Yüksek kalite ses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>16 Segment:</strong> Hızlı seek ve yükleme</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>AES-128 Şifreleme:</strong> İçerik koruması</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Loudnorm:</strong> Ses seviyesi normalizasyonu</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>EQ + LP Filter:</strong> Dengeli ses profili</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
