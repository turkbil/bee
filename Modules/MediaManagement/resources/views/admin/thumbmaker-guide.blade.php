@extends('admin.layout')

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2"/><circle cx="9.5" cy="9.5" r=".5" fill="currentColor"/><path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"/><path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"/>
                        </svg>
                        Thumbmaker Kullanım Kılavuzu
                    </h2>
                    <div class="text-muted">
                        Universal görsel boyutlandırma ve optimizasyon sistemi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Giriş -->
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/>
                        </svg>
                        Thumbmaker Nedir?
                    </h3>
                    <p class="text-muted">
                        Thumbmaker, görselleri anında boyutlandıran, format dönüştüren ve optimize eden bir sistemdir.
                        Intervention Image kütüphanesi ile çalışır ve WebP, JPG, PNG formatlarını destekler.
                    </p>
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/>
                        </svg>
                        <strong>Önemli:</strong> Oluşturulan thumbnail'ler 30 gün boyunca cache'lenir. Aynı parametrelerle yapılan istekler anında döner.
                    </div>
                </div>
            </div>

            <!-- Parametreler -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">📋 Parametreler</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Parametre</th>
                                <th>Açıklama</th>
                                <th style="width: 150px;">Değerler</th>
                                <th style="width: 100px;">Varsayılan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code class="text-danger">src</code></td>
                                <td>Kaynak görsel URL'i <span class="badge bg-red-lt ms-2">Zorunlu</span></td>
                                <td>URL string</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td><code>w</code></td>
                                <td><strong>Width</strong> - Genişlik (piksel)</td>
                                <td>1-9999</td>
                                <td>null</td>
                            </tr>
                            <tr>
                                <td><code>h</code></td>
                                <td><strong>Height</strong> - Yükseklik (piksel)</td>
                                <td>1-9999</td>
                                <td>null</td>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><strong>Quality</strong> - Kalite</td>
                                <td>1-100</td>
                                <td>85</td>
                            </tr>
                            <tr>
                                <td><code>a</code></td>
                                <td><strong>Alignment</strong> - Hizalama (scale=1 için)</td>
                                <td>c, t, b, l, r, tl, tr, bl, br</td>
                                <td>c</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td>
                                    <strong>Scale</strong> - Ölçeklendirme<br>
                                    <small class="text-muted">0=Fit (sığdır), 1=Fill (doldur), 2=Stretch (esnet)</small>
                                </td>
                                <td>0, 1, 2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><code>f</code></td>
                                <td><strong>Format</strong> - Çıktı formatı</td>
                                <td>webp, jpg, png, gif</td>
                                <td>webp</td>
                            </tr>
                            <tr>
                                <td><code>c</code></td>
                                <td><strong>Cache</strong> - Cache kullan</td>
                                <td>0 (hayır), 1 (evet)</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hizalama Şeması -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">🎯 Hizalama (Alignment) Şeması</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="border rounded p-3" style="background: #f8f9fa;">
                                <div class="row g-2 mb-2">
                                    <div class="col-4"><span class="badge bg-primary">tl</span> Top Left</div>
                                    <div class="col-4"><span class="badge bg-primary">t</span> Top</div>
                                    <div class="col-4"><span class="badge bg-primary">tr</span> Top Right</div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-4"><span class="badge bg-success">l</span> Left</div>
                                    <div class="col-4"><span class="badge bg-danger">c</span> Center</div>
                                    <div class="col-4"><span class="badge bg-success">r</span> Right</div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-4"><span class="badge bg-primary">bl</span> Bottom Left</div>
                                    <div class="col-4"><span class="badge bg-primary">b</span> Bottom</div>
                                    <div class="col-4"><span class="badge bg-primary">br</span> Bottom Right</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <strong>Not:</strong> Hizalama sadece <code>scale=1</code> (Fill) modunda kullanılır.
                                Bu mod görseli kırpar ve belirtilen boyuta doldurur.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kullanım Örnekleri -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">💡 Kullanım Örnekleri</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="examples">
                        <!-- Örnek 1: Blade -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#example1">
                                    <strong>1. Blade Template'de Helper Kullanımı</strong>
                                </button>
                            </h2>
                            <div id="example1" class="accordion-collapse collapse show" data-bs-parent="#examples">
                                <div class="accordion-body">
                                    <pre class="bg-dark text-light p-3 rounded"><code>{{-- Basit kullanım --}}
&lt;img src="@{{ thumb($media, 400, 300) }}" alt="Thumbnail"&gt;

{{-- Detaylı kullanım --}}
&lt;img src="@{{ thumb($media, 800, 600, ['quality' => 90, 'alignment' => 'c']) }}" alt="Optimized"&gt;

{{-- URL ile kullanım --}}
&lt;img src="@{{ thumb('https://example.com/image.jpg', 1200, null, ['format' => 'webp']) }}" alt="WebP"&gt;</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Örnek 2: Direkt URL -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example2">
                                    <strong>2. Direkt URL Kullanımı</strong>
                                </button>
                            </h2>
                            <div id="example2" class="accordion-collapse collapse" data-bs-parent="#examples">
                                <div class="accordion-body">
                                    <pre class="bg-dark text-light p-3 rounded"><code>// 400x300, kalite 85, WebP
{{ url('/thumbmaker?src=https://ixtif.com/image.jpg&w=400&h=300&q=85&f=webp') }}

// 800 genişlik, yükseklik orantılı
{{ url('/thumbmaker?src=https://ixtif.com/image.jpg&w=800') }}

// 600x400, doldur (fill), merkez hizalı
{{ url('/thumbmaker?src=https://ixtif.com/image.jpg&w=600&h=400&s=1&a=c') }}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Örnek 3: Scale Modları -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example3">
                                    <strong>3. Scale (Ölçeklendirme) Modları</strong>
                                </button>
                            </h2>
                            <div id="example3" class="accordion-collapse collapse" data-bs-parent="#examples">
                                <div class="accordion-body">
                                    <pre class="bg-dark text-light p-3 rounded"><code>// Fit (s=0): Orantılı sığdır - en yaygın
@{{ thumb($media, 400, 300, ['scale' => 0]) }}

// Fill (s=1): Kes ve doldur - kare thumbnail'ler için
@{{ thumb($media, 400, 400, ['scale' => 1, 'alignment' => 'c']) }}

// Stretch (s=2): Esnet (orantı bozulur)
@{{ thumb($media, 800, 200, ['scale' => 2]) }}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Örnek 4: Format Dönüştürme -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#example4">
                                    <strong>4. Format Dönüştürme</strong>
                                </button>
                            </h2>
                            <div id="example4" class="accordion-collapse collapse" data-bs-parent="#examples">
                                <div class="accordion-body">
                                    <pre class="bg-dark text-light p-3 rounded"><code>// PNG'yi WebP'ye çevir (daha küçük dosya)
@{{ thumb($media, 1200, null, ['format' => 'webp', 'quality' => 85]) }}

// JPG'yi yüksek kalite PNG yap
@{{ thumb($media, 800, 600, ['format' => 'png']) }}

// WebP'yi JPG'ye çevir (eski tarayıcı desteği)
@{{ thumb($media, 1024, 768, ['format' => 'jpg', 'quality' => 90]) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">✨ En İyi Kullanım Pratikleri</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-success-lt">
                                <div class="card-body">
                                    <h4 class="card-title text-success">✅ Yapılması Gerekenler</h4>
                                    <ul class="mb-0">
                                        <li>WebP formatı kullanın (daha küçük dosya)</li>
                                        <li>loading="lazy" ekleyin (sayfa hızı)</li>
                                        <li>Thumbnail için scale=1 kullanın</li>
                                        <li>Kalite 80-90 aralığında olsun</li>
                                        <li>Cache her zaman aktif olsun</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger-lt">
                                <div class="card-body">
                                    <h4 class="card-title text-danger">❌ Yapılmaması Gerekenler</h4>
                                    <ul class="mb-0">
                                        <li>Gereksiz yüksek kalite kullanmayın</li>
                                        <li>Orijinal boyuttan büyütmeyin</li>
                                        <li>Cache'i devre dışı bırakmayın</li>
                                        <li>Scale=2 (stretch) kullanmayın</li>
                                        <li>9999px gibi dev boyutlar vermeyin</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
