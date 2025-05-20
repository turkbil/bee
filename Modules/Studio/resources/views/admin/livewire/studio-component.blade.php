@include('studio::admin.helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Aramak için yazmaya başlayın...">
                </div>
            </div>
            <div class="col position-relative">
                <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div style="min-width: 70px">
                        <select class="form-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Başlık</th>
                        <th>Durum</th>
                        <th class="text-center">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">Studio kullanımı için:</p>
                                <p class="empty-subtitle text-muted">
                                    Sayfaları düzenlemek için sayfa yönetiminden "Studio ile Düzenle" seçeneğini kullanın.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.page.index') }}" class="btn btn-primary">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Sayfa Yönetimine Git
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>