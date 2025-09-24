<!-- AI Content Generation Modal - Minimal + Accordion Design -->
<div class="modal modal-blur fade" id="aiContentModal" tabindex="-1" role="dialog" aria-labelledby="aiContentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h4 class="modal-title fw-normal" id="aiContentModalLabel">🤖 AI İçerik Üretici</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeContentModal()"></button>
            </div>
            <div class="modal-body p-4">

                <!-- Main Content Area - Minimal Design -->
                <div class="mb-4">
                    <!-- Content Brief - Main Field -->
                    <div class="mb-3">
                        <label class="form-label mb-2">İçerik Açıklaması</label>
                        <textarea id="contentTopic" class="form-control" rows="3" placeholder="Ne tür içerik istediğinizi yazın..."></textarea>
                    </div>

                    <!-- File Upload - Compact -->
                    <div class="mb-3">
                        <label class="form-label mb-2">Dosya (İsteğe Bağlı)</label>
                        <div class="border border-dashed rounded p-3 text-center bg-light position-relative d-flex align-items-center justify-content-center"
                             style="cursor: pointer; min-height: 80px;"
                             onclick="document.getElementById('aiModalFileInput').click()"
                             ondrop="handleAIModalDrop(event)"
                             ondragover="handleAIModalDragOver(event)"
                             ondragenter="handleAIModalDragEnter(event)"
                             ondragleave="handleAIModalDragLeave(event)">

                            <input type="file" id="aiModalFileInput" class="d-none" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple>

                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-cloud-upload-alt me-2 fs-5"></i>
                                <span>Dosya seç veya sürükle</span>
                            </div>
                        </div>
                        <small>PDF, JPG, PNG, WEBP - Max 10MB</small>
                    </div>

                    <!-- Info Message -->
                    <div class="alert alert-info py-2" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <small><strong>Bilgi:</strong> Üretilen içerik mevcut içeriğin yerine geçecektir.</small>
                        </div>
                    </div>

                    <!-- Action Buttons - Above Accordion -->
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="closeContentModal()">Kapat</button>
                        <button type="button" id="startGeneration" class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>İçerik Üret
                            <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;" role="status" aria-hidden="true"></span>
                        </button>
                    </div>

                    <!-- Progress (Hidden initially) -->
                    <div class="mb-3" id="contentProgress" style="display: none;">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-indeterminate bg-primary" id="progressBar"></div>
                        </div>
                        <small class="mt-1 d-block text-center" id="progressMessage">İçerik üretiliyor...</small>
                    </div>
                </div>

                <!-- Help Section - Accordion -->
                <div class="accordion" id="aiHelpAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="aiHelpHeading1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aiHelpCollapse1" aria-expanded="false" aria-controls="aiHelpCollapse1">
                                <i class="fas fa-question-circle text-primary me-2"></i>
                                Nasıl kullanırım?
                            </button>
                        </h2>
                        <div id="aiHelpCollapse1" class="accordion-collapse collapse" aria-labelledby="aiHelpHeading1" data-bs-parent="#aiHelpAccordion">
                            <div class="accordion-body py-3">
                                <!-- Simple Steps -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-primary bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-primary text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">1</span>
                                                </div>
                                                <h6 class="card-title">Açıklama Yazın</h6>
                                                <p class="small mb-0">Ne istediğinizi kısa ve açık yazın</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-info bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-info text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">2</span>
                                                </div>
                                                <h6 class="card-title">Dosya Yükleyin</h6>
                                                <p class="small mb-0">İsteğe bağlı - PDF veya görsel</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="card bg-success bg-opacity-10 border-0 h-100">
                                            <div class="card-body py-3">
                                                <div class="avatar avatar-lg bg-success text-white rounded-circle mx-auto mb-2">
                                                    <span class="fw-bold">3</span>
                                                </div>
                                                <h6 class="card-title">İçerik Üret</h6>
                                                <p class="small mb-0">Butona tıklayın ve bekleyin</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <div>
                                            <strong>İpucu:</strong> Ne istediğinizi açık yazın. Örnek: "Şirketimiz hakkında sayfa, samimi dil"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="aiHelpHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aiHelpCollapse2" aria-expanded="false" aria-controls="aiHelpCollapse2">
                                <i class="fas fa-list text-success me-2"></i>
                                İçerik örnekleri ve kategoriler
                            </button>
                        </h2>
                        <div id="aiHelpCollapse2" class="accordion-collapse collapse" aria-labelledby="aiHelpHeading2" data-bs-parent="#aiHelpAccordion">
                            <div class="accordion-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-primary">🏢 Kurumsal Sayfalar</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Hakkımızda sayfası, samimi dil"</li>
                                            <li>• "Hizmetlerimiz sayfası, profesyonel"</li>
                                            <li>• "İletişim sayfası, davetkar ton"</li>
                                            <li>• "Kariyer sayfası, motivasyonel"</li>
                                            <li>• "Vizyonumuz sayfası, ilham verici"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-success">🛍️ E-ticaret</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Ürün açıklaması, satış odaklı"</li>
                                            <li>• "Kategori tanıtımı, SEO dostu"</li>
                                            <li>• "Kampanya sayfası, aciliyet hissi"</li>
                                            <li>• "İndirim duyurusu, çekici"</li>
                                            <li>• "Ürün karşılaştırması, detaylı"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-info">📝 Blog & İçerik</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Blog yazısı, bilgilendirici"</li>
                                            <li>• "Rehber içerik, adım adım"</li>
                                            <li>• "Haber yazısı, objektif dil"</li>
                                            <li>• "Röportaj metni, samimi"</li>
                                            <li>• "Analiz yazısı, derinlemesine"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-warning">🎯 Pazarlama</h6>
                                        <ul class="list-unstyled small mb-3">
                                            <li>• "Landing page, dönüşüm odaklı"</li>
                                            <li>• "Email metni, kişisel ton"</li>
                                            <li>• "Sosyal medya yazısı, viral"</li>
                                            <li>• "Bülten içeriği, güncel"</li>
                                            <li>• "Reklam metni, dikkat çekici"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2 text-danger">📄 PDF/Görsel Değişiklikleri</h6>
                                        <ul class="list-unstyled small mb-0">
                                            <li>• "ABC şirketi yerine XYZ şirketi yaz"</li>
                                            <li>• "2023 fiyatları yerine 2024 fiyatları"</li>
                                            <li>• "Eski adres yerine yeni adres"</li>
                                            <li>• "Demo marka yerine gerçek marka"</li>
                                            <li>• "Placeholder metinleri kaldır"</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="aiHelpHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aiHelpCollapse3" aria-expanded="false" aria-controls="aiHelpCollapse3">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                Hangi dosyaları yükleyebilirim?
                            </button>
                        </h2>
                        <div id="aiHelpCollapse3" class="accordion-collapse collapse" aria-labelledby="aiHelpHeading3" data-bs-parent="#aiHelpAccordion">
                            <div class="accordion-body py-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="avatar avatar-sm bg-danger text-white me-3">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">PDF Dosyaları</h6>
                                                <small>Metinleri okuyup anlıyoruz</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="avatar avatar-sm bg-success text-white me-3">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Görsel Dosyalar</h6>
                                                <small>JPG, PNG, WEBP</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Hatırlatma:</strong> Dosya yükleme zorunlu değil! Sadece açıklama yazarak da içerik üretebilirsiniz.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="aiHelpHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aiHelpCollapse4" aria-expanded="false" aria-controls="aiHelpCollapse4">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                İpuçları ve özel talimatlar
                            </button>
                        </h2>
                        <div id="aiHelpCollapse4" class="accordion-collapse collapse" aria-labelledby="aiHelpHeading4" data-bs-parent="#aiHelpAccordion">
                            <div class="accordion-body py-3">
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <div>
                                            <strong>İpucu:</strong> Dil (Türkçe/İngilizce), ton (samimi/resmi), hedef kitle ve amaç belirtin. PDF/görsel yüklerseniz, içeriği nasıl değiştirmek istediğinizi de yazın.
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <div>
                                            <strong>PDF/Görsel değişiklikleri:</strong> "ABC şirketi yerine XYZ şirketi yaz", "eski logo yerine yeni logo", "fiyatları güncelle" gibi spesifik talimatlar verin.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* AI Content Modal Specific Styles */
#aiContentModal .progress {
    background-color: rgba(var(--tblr-primary-rgb), 0.1);
}

#aiContentModal .progress-bar {
    transition: width 0.3s ease;
}

#aiContentModal .card-sm {
    border: 1px solid rgba(var(--tblr-border-color-rgb), 0.5);
}

#aiContentModal #contentTopic {
    min-height: 100px;
    resize: vertical;
}

#aiContentModal #progressDetails {
    font-size: 0.875rem;
}

#aiContentModal .avatar-xs {
    width: 2rem;
    height: 2rem;
}
</style>

<script>
// Brief yardım tooltiplerini modal açıldığında initialize et
document.addEventListener('DOMContentLoaded', function () {
  var modalEl = document.getElementById('aiContentModal');
  if (!modalEl || !window.bootstrap) return;
  modalEl.addEventListener('shown.bs.modal', function () {
    var tips = [].slice.call(modalEl.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tips.forEach(function (el) {
      try { new bootstrap.Tooltip(el); } catch (e) {}
    });
  });

  // Modal kapandığında file uploader'ı reset et
  modalEl.addEventListener('hidden.bs.modal', function () {
    // Alpine.js file uploader'ı reset et
    const fileUploaderEl = document.querySelector('[x-data*="fileUploader"]');
    if (fileUploaderEl && fileUploaderEl.__x) {
      const data = fileUploaderEl.__x.$data;
      if (data) {
        data.files = [];
        data.hasFiles = false;
        data.uploading = false;
        data.uploadProgress = 0;
      }
    }

    // File input'u da temizle
    const fileInput = modalEl.querySelector('input[type="file"]');
    if (fileInput) {
      fileInput.value = '';
    }
  });
});

// Alpine.js File Uploader Component
function fileUploader() {
  return {
    files: [],
    hasFiles: false,
    uploading: false,
    uploadProgress: 0,

    handleFiles(fileList) {
      if (fileList && fileList.length > 0) {
        this.files = Array.from(fileList);
        this.hasFiles = true;

        // AI sistem'e dosyaları gönder
        if (window.aiContentSystem && window.aiContentSystem.handleFileUpload) {
          window.aiContentSystem.handleFileUpload(this.files);
        }
      }
    },

    handleDrop(event) {
      const dt = event.dataTransfer;
      const files = dt.files;
      this.handleFiles(files);
    },

    removeFile(file) {
      const index = this.files.indexOf(file);
      if (index > -1) {
        this.files.splice(index, 1);
      }
      this.hasFiles = this.files.length > 0;

      // Eğer dosya kalmadıysa state'i reset et
      if (!this.hasFiles) {
        this.reset();
      }
    },

    reset() {
      this.files = [];
      this.hasFiles = false;
      this.uploading = false;
      this.uploadProgress = 0;

      // Input'u da temizle
      const input = this.$refs.fileInput;
      if (input) {
        input.value = '';
      }
    },

    getFileIcon(type) {
      if (type.includes('pdf')) return '📄';
      if (type.includes('image')) return '🖼️';
      return '📎';
    },

    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
  }
}
</script>
