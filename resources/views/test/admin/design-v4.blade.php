@extends('admin.layout')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Tasarım v4</div>
                <h2 class="page-title">Vertical Stepper</h2>
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
                            <a href="{{ route('test.admin.ui.v2') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-grip me-2"></i>v2 - Card
                            </a>
                            <a href="{{ route('test.admin.ui.v3') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-bars me-2"></i>v3 - Accordion
                            </a>
                            <a href="{{ route('test.admin.ui.v4') }}" class="list-group-item list-group-item-action active">
                                <i class="fas fa-list-ol me-2"></i>v4 - Stepper
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="steps steps-vertical">
                            <div class="step-item active">
                                <div class="h4 m-0">Ürün Bilgileri</div>
                                <div class="text-muted">Temel ürün bilgilerini girin</div>
                                <div class="mt-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" value="Elektrikli Forklift">
                                                <label>Başlık</label>
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
                                    </div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="h4 m-0">Fiyatlandırma</div>
                                <div class="text-muted">KDV ve fiyat bilgilerini girin</div>
                                <div class="mt-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" value="85000.00">
                                                <label>KDV Hariç</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" value="102000.00">
                                                <label>KDV Dahil</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" value="20.00">
                                                <label>KDV (%)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="h4 m-0">Stok Yönetimi</div>
                                <div class="text-muted">Stok bilgilerini yönetin</div>
                                <div class="mt-3">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label>Stok Takibi</label>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" value="15">
                                                <label>Mevcut</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" value="FORK-001">
                                                <label>SKU</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" value="8690123">
                                                <label>Barkod</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="h4 m-0">Teknik Özellikler</div>
                                <div class="text-muted">Ürüne özgü özellikleri ekleyin</div>
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Özellik ekleyin (Taşıma Kapasitesi, Motor Gücü vb.)
                                    </div>
                                    <button class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Özellik Ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <button class="btn btn-danger">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                            <button class="btn btn-link ms-auto">İptal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
