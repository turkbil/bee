@extends('admin.layout')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Tasarım v2</div>
                <h2 class="page-title">Card Grid Sistemi</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('test.admin.ui.selector') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Tasarımlar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Tasarımlar</h3>
                        <div class="list-group list-group-transparent">
                            <a href="{{ route('test.admin.ui.v1') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-folder-open me-2"></i>v1 - Tab
                            </a>
                            <a href="{{ route('test.admin.ui.v2') }}" class="list-group-item list-group-item-action active">
                                <i class="fas fa-grip me-2"></i>v2 - Card
                            </a>
                            <a href="{{ route('test.admin.ui.v3') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-bars me-2"></i>v3 - Accordion
                            </a>
                            <a href="{{ route('test.admin.ui.v4') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-list-ol me-2"></i>v4 - Stepper
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row g-4">
                    <!-- Ürün Bilgileri Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-box me-2"></i>Ürün Bilgileri
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="Elektrikli Forklift 2 Ton">
                                            <label>Başlık</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <select class="form-control">
                                                <option>Forkliftler</option>
                                            </select>
                                            <label>Kategori</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" style="height: 80px;">2 ton taşıma kapasiteli...</textarea>
                                            <label>Açıklama</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fiyatlandırma Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-dollar-sign me-2"></i>Fiyatlandırma (KDV)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="85000.00">
                                            <label>KDV Hariç</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="102000.00">
                                            <label>KDV Dahil</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="20.00">
                                            <label>KDV Oranı (%)</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-success mb-0">
                                            <strong>KDV:</strong> 17.000,00 ₺
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Yönetimi Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-warehouse me-2"></i>Stok Yönetimi
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label">Stok Takibi</label>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="15">
                                            <label>Mevcut</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="5">
                                            <label>Minimum</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="FORK-001">
                                            <label>SKU</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="8690123">
                                            <label>Barkod</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teknik Özellikler Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-list-check me-2"></i>Teknik Özellikler
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Özellik">
                                            <label>Özellik</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Değer">
                                            <label>Değer</label>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-outline-danger btn-sm w-100 mb-3">
                                    <i class="fas fa-plus me-1"></i>Ekle
                                </button>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td>Taşıma Kapasitesi</td>
                                                <td>2.000 kg</td>
                                            </tr>
                                            <tr>
                                                <td>Çatal Uzunluğu</td>
                                                <td>1.200 mm</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-search me-2"></i>SEO Ayarları
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="Elektrikli Forklift">
                                            <label>SEO Başlık</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <textarea class="form-control">SEO açıklaması...</textarea>
                                            <label>SEO Açıklama</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kaydet Butonu -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <button class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Tüm Bilgileri Kaydet
                                    </button>
                                    <button class="btn btn-link ms-auto">İptal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
