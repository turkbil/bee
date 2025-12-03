@extends('admin.layout')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-paint-brush me-2"></i>
                    Admin UI Tasarım Alternatifleri
                </h2>
                <div class="text-muted mt-1">Product Manage sayfası için 4 farklı UI/UX tasarımı</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            
            <!-- Tasarım 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-link card-link-pop">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-lg" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                    <i class="fas fa-folder-open text-white" style="font-size: 1.5rem;"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <h3 class="card-title mb-1">Tasarım 1</h3>
                                </div>
                                <div class="text-muted">Klasik Tab Sistemi</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col"><small class="text-muted">Öğrenme</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                            <div class="row">
                                <div class="col"><small class="text-muted">Hız</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 4/5</small></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('test.admin.ui.v1') }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasarım 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-link card-link-pop">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-lg" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <i class="fas fa-grip text-white" style="font-size: 1.5rem;"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <h3 class="card-title mb-1">Tasarım 2</h3>
                                </div>
                                <div class="text-muted">Card Grid Sistemi</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col"><small class="text-muted">Tek Bakış</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                            <div class="row">
                                <div class="col"><small class="text-muted">Hız</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('test.admin.ui.v2') }}" class="btn btn-success w-100">
                                <i class="fas fa-eye me-2"></i>Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasarım 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-link card-link-pop">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-lg" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                    <i class="fas fa-bars text-white" style="font-size: 1.5rem;"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <h3 class="card-title mb-1">Tasarım 3</h3>
                                </div>
                                <div class="text-muted">Accordion Sistemi</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col"><small class="text-muted">Mobil</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                            <div class="row">
                                <div class="col"><small class="text-muted">Kompakt</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('test.admin.ui.v3') }}" class="btn btn-warning w-100">
                                <i class="fas fa-eye me-2"></i>Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasarım 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-link card-link-pop">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-lg" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                    <i class="fas fa-list-ol text-white" style="font-size: 1.5rem;"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <h3 class="card-title mb-1">Tasarım 4</h3>
                                </div>
                                <div class="text-muted">Vertical Stepper</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col"><small class="text-muted">Öğrenme</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                            <div class="row">
                                <div class="col"><small class="text-muted">Rehberli</small></div>
                                <div class="col-auto"><small><i class="fas fa-star text-warning"></i> 5/5</small></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('test.admin.ui.v4') }}" class="btn btn-danger w-100">
                                <i class="fas fa-eye me-2"></i>Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Karşılaştırma Tablosu -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-table me-2"></i>Hızlı Karşılaştırma</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Özellik</th>
                                    <th class="text-center">v1<br><small>Tab</small></th>
                                    <th class="text-center">v2<br><small>Card</small></th>
                                    <th class="text-center">v3<br><small>Accordion</small></th>
                                    <th class="text-center">v4<br><small>Stepper</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Öğrenme Kolaylığı</strong></td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                </tr>
                                <tr>
                                    <td><strong>Hız (Deneyimli Kullanıcı)</strong></td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐</td>
                                </tr>
                                <tr>
                                    <td><strong>Görsel Çekicilik</strong></td>
                                    <td class="text-center">⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobil Uyum</strong></td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐</td>
                                </tr>
                                <tr>
                                    <td><strong>Tek Bakışta Bilgi</strong></td>
                                    <td class="text-center">⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐⭐⭐</td>
                                    <td class="text-center">⭐⭐</td>
                                    <td class="text-center">⭐⭐⭐</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
