@extends('admin.layout')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Tasar1m v3</div>
                <h2 class="page-title">Accordion Sistemi</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('test.admin.ui.selector') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Tasar1mlar
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
                        <h3 class="card-title">Tasar1mlar</h3>
                        <div class="list-group list-group-transparent">
                            <a href="{{ route('test.admin.ui.v1') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-folder-open me-2"></i>v1 - Tab
                            </a>
                            <a href="{{ route('test.admin.ui.v2') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-grip me-2"></i>v2 - Card
                            </a>
                            <a href="{{ route('test.admin.ui.v3') }}" class="list-group-item list-group-item-action active">
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
                <div class="card">
                    <div class="card-body">
                        <div class="accordion" id="accordionExample">
                            <!-- Ürün Bilgileri -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        <i class="fas fa-box me-3"></i>Ürün Bilgileri & Fiyatland1rma
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" value="Elektrikli Forklift 2 Ton">
                                                    <label>Ba_l1k</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-control">
                                                        <option>Forkliftler</option>
                                                    </select>
                                                    <label>Kategori</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" value="85000.00">
                                                    <label>KDV Hariç (º)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" value="102000.00">
                                                    <label>KDV Dahil (º)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" value="20.00">
                                                    <label>KDV Oran1 (%)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stok Yönetimi -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        <i class="fas fa-warehouse me-3"></i>Stok Yönetimi
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" checked>
                                            <label class="form-check-label">Stok Takibini Aktif Et</label>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" value="15">
                                                    <label>Mevcut Stok</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" value="5">
                                                    <label>Min Stok</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" value="FORK-001">
                                                    <label>SKU</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" value="8690123">
                                                    <label>Barkod</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Teknik Özellikler -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        <i class="fas fa-list-check me-3"></i>Teknik Özellikler
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Ürüne özel teknik özellikleri ekleyin
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control">
                                                    <label>Özellik Ad1</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control">
                                                    <label>Deer</label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-outline-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>Yeni Özellik
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                        <i class="fas fa-search me-3"></i>SEO Ayarlar1
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control">
                                                    <label>SEO Ba_l1k</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" style="height: 80px;"></textarea>
                                                    <label>SEO Aç1klama</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <button class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                            <button class="btn btn-link ms-auto">0ptal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection