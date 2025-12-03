@extends('admin.layout')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Tasarım v1</div>
                <h2 class="page-title">Klasik Tab Sistemi</h2>
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
                            <a href="{{ route('test.admin.ui.v1') }}" class="list-group-item list-group-item-action active">
                                <i class="fas fa-folder-open me-2"></i>v1 - Tab
                            </a>
                            <a href="{{ route('test.admin.ui.v2') }}" class="list-group-item list-group-item-action">
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
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-product" class="nav-link active" data-bs-toggle="tab">
                                    <i class="fas fa-box me-2"></i>Ürün Bilgileri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-stock" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-warehouse me-2"></i>Stok Yönetimi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-specs" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-list-check me-2"></i>Özellikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-seo" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-search me-2"></i>SEO
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-product">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Ürün Başlığı" value="Elektrikli Forklift 2 Ton">
                                            <label>Ürün Başlığı</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-control">
                                                <option>Forkliftler</option>
                                                <option>Transpaletler</option>
                                                <option>İstif Makineleri</option>
                                            </select>
                                            <label>Kategori</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="85000.00">
                                            <label>KDV Hariç Fiyat (₺)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="102000.00">
                                            <label>KDV Dahil Fiyat (₺)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" value="20.00">
                                            <label>KDV Oranı (%)</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" style="height: 100px;">Elektrikli forklift, 2 ton taşıma kapasiteli, endüstriyel kullanım için idealdir.</textarea>
                                            <label>Kısa Açıklama</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-stock">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label">
                                        <strong>Stok Takibini Aktif Et</strong>
                                    </label>
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
                                            <label>Minimum Stok</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="FORK-EL-2T-001">
                                            <label>SKU</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="8690123456789">
                                            <label>Barkod</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Stok Durumu:</strong> Normal seviyede (15 adet)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-specs">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ürüne özel teknik özellikleri ekleyin
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="Taşıma Kapasitesi">
                                            <label>Özellik Adı</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="2.000 kg">
                                            <label>Değer</label>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Yeni Özellik Ekle
                                </button>
                                
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Özellik</th>
                                                <th>Değer</th>
                                                <th width="100">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Taşıma Kapasitesi</td>
                                                <td>2.000 kg</td>
                                                <td><button class="btn btn-sm btn-danger">Sil</button></td>
                                            </tr>
                                            <tr>
                                                <td>Çatal Uzunluğu</td>
                                                <td>1.200 mm</td>
                                                <td><button class="btn btn-sm btn-danger">Sil</button></td>
                                            </tr>
                                            <tr>
                                                <td>Kaldırma Yüksekliği</td>
                                                <td>3.000 mm</td>
                                                <td><button class="btn btn-sm btn-danger">Sil</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-seo">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="Elektrikli Forklift 2 Ton - En İyi Fiyat">
                                            <label>SEO Başlık</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" style="height: 80px;">2 ton taşıma kapasiteli elektrikli forklift. Endüstriyel kullanım için ideal. Uygun fiyat, hızlı teslimat.</textarea>
                                            <label>SEO Açıklama</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <button class="btn btn-primary">
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
