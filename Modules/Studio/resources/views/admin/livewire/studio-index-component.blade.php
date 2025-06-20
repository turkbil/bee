<div>
    @include('studio::admin.helper')

    <div class="row row-deck row-cards">
                
                <div class="col-12 mb-4">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar avatar-lg rounded-3">
                                                <i class="fas fa-file-alt fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="h2 mb-0">{{ $totalPages }}</div>
                                            <div class="text-muted">Toplam Sayfa</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar avatar-lg rounded-3">
                                                <i class="fas fa-puzzle-piece fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="h2 mb-0">{{ $activeTenantWidgets }}</div>
                                            <div class="text-muted">Aktif Bileşen</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-orange text-white avatar avatar-lg rounded-3">
                                                <i class="fas fa-magic fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="h2 mb-0">∞</div>
                                            <div class="text-muted">Sınırsız Düzenleme</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-purple text-white avatar avatar-lg rounded-3">
                                                <i class="fas fa-mobile-alt fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="h2 mb-0">100%</div>
                                            <div class="text-muted">Responsive</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-0 bg-transparent">
                            <h3 class="card-title">
                                <i class="fas fa-clock me-2 text-muted"></i>
                                Son Düzenlenen Sayfalar
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if($recentPages->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentPages as $page)
                                        <div class="list-group-item border-start-0 border-end-0">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="avatar avatar-md rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; display: flex; align-items: center; justify-content: center;">
                                                        {{ substr($page->title, 0, 2) }}
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="text-body fw-bold">{{ $page->title }}</div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-edit me-1"></i>
                                                        {{ $page->updated_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $page->page_id]) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-wand-magic-sparkles me-1"></i>
                                                        Düzenle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('admin.page.index') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-list me-2"></i>
                                        Tüm Sayfaları Görüntüle
                                    </a>
                                </div>
                            @else
                                <div class="empty py-5">
                                    <div class="empty-img">
                                        <i class="fas fa-file-alt fa-4x text-muted opacity-50"></i>
                                    </div>
                                    <p class="empty-title h4 mt-3">Henüz sayfa yok</p>
                                    <p class="empty-subtitle text-muted">
                                        İlk sayfayı oluşturmak için aşağıdaki butona tıklayın
                                    </p>
                                    <div class="empty-action mt-3">
                                        <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            İlk Sayfayı Oluştur
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-rocket me-2"></i>
                                Hızlı Başlangıç
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('admin.page.manage') }}" class="btn btn-primary w-100">
                                    <i class="ti ti-plus me-2"></i>
                                    Yeni Sayfa Oluştur
                                </a>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.page.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="ti ti-list me-2"></i>
                                    Tüm Sayfalar
                                </a>
                            </div>
                            <div class="mb-0">
                                <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="ti ti-components me-2"></i>
                                    Widget Yönetimi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-0 bg-transparent">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle me-2 text-muted"></i>
                                Nasıl Kullanılır
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <span class="avatar avatar-sm bg-blue text-white">1</span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="fw-bold">Sayfa Seç</div>
                                        <div class="text-muted small">Düzenlemek istediğiniz sayfayı seçin</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <span class="avatar avatar-sm bg-green text-white">2</span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="fw-bold">Studio Aç</div>
                                        <div class="text-muted small">"Studio ile Düzenle" butonuna tıklayın</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <span class="avatar avatar-sm bg-orange text-white">3</span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="fw-bold">Tasarımla</div>
                                        <div class="text-muted small">Bileşenleri sürükleyip bırakın</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <span class="avatar avatar-sm bg-purple text-white">4</span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="fw-bold">Kaydet</div>
                                        <div class="text-muted small">Değişikliklerinizi kaydedin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>