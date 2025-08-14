{{-- Bulk Operation Modal --}}
<div class="modal fade" id="bulkOperationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-database"></i> Toplu İşlem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">İşlem Türü</label>
                        <select class="form-select" id="operationType">
                            <option value="bulk_translate">Toplu Çeviri</option>
                            <option value="bulk_seo">SEO Optimize</option>
                            <option value="bulk_generate">İçerik Üret</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Seçilen Kayıt: <span id="selectedCount">0</span></label>
                        <div class="form-text">İşlem yapılacak kayıt sayısı</div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="startBulkOperation">
                    <i class="ti ti-play"></i> İşlemi Başlat
                </button>
            </div>
        </div>
    </div>
</div>
