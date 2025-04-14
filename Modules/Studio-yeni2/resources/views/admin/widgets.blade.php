<x-admin-layout>
    <div class="container-xl py-4">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Widget Yönetimi</h2>
                    <div class="text-muted mt-1">Tüm widgetları yönetin</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="d-flex">
                        <a href="{{ route('admin.studio.widgets.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <i class="fas fa-plus me-2"></i> Yeni Widget
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Widgetlar</h3>
                        <div class="card-actions">
                            <form action="{{ route('admin.studio.widgets.index') }}" method="GET" class="d-flex">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Widget ara...">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Widget Adı</th>
                                    <th>Açıklama</th>
                                    <th>Kategori</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($widgets as $widget)
                                <tr>
                                    <td>
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-puzzle-piece text-primary me-2"></i>
                                            {{ $widget->name }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($widget->description, 50) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $widget->data['category'] == 'widget' ? 'primary' : 'secondary' }}">
                                            {{ $categories[$widget->data['category']] ?? $widget->data['category'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($widget->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.studio.widgets.edit', $widget->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="previewWidget('{{ $widget->id }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form action="{{ route('admin.studio.widgets.destroy', $widget->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu widgetı silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                                            </div>
                                            <p class="empty-title">Widget Bulunamadı</p>
                                            <p class="empty-subtitle text-muted">
                                                Arama kriterlerinize uygun widget bulunamadı.
                                            </p>
                                            <div class="empty-action">
                                                <a href="{{ route('admin.studio.widgets.index') }}" class="btn btn-primary">
                                                    <i class="fas fa-sync me-2"></i> Tümünü Göster
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($widgets->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $widgets->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Widget Önizleme Modalı -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Widget Önizleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="widget-preview border rounded p-3">
                        <div id="widgetPreviewContent"></div>
                    </div>
                    
                    <hr>
                    
                    <ul class="nav nav-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#tabs-html" class="nav-link active" data-bs-toggle="tab">HTML</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabs-css" class="nav-link" data-bs-toggle="tab">CSS</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabs-js" class="nav-link" data-bs-toggle="tab">JavaScript</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tabs-html">
                            <div class="my-3">
                                <pre class="language-html"><code id="previewHtml"></code></pre>
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-css">
                            <div class="my-3">
                                <pre class="language-css"><code id="previewCss"></code></pre>
                            </div>
                        </div>
                        <div class="tab-pane" id="tabs-js">
                            <div class="my-3">
                                <pre class="language-javascript"><code id="previewJs"></code></pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function previewWidget(widgetId) {
            // Widget içeriğini API'den al
            fetch(`/admin/studio/api/widget/${widgetId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // HTML içeriğini yerleştir
                        document.getElementById('widgetPreviewContent').innerHTML = data.data.html;
                        
                        // Code preview için HTML içeriğini yerleştir
                        document.getElementById('previewHtml').textContent = data.data.html;
                        
                        // CSS içeriğini yerleştir
                        document.getElementById('previewCss').textContent = data.data.css || '';
                        
                        // JavaScript içeriğini yerleştir
                        document.getElementById('previewJs').textContent = data.data.js || '';
                        
                        // Modalı aç
                        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                        previewModal.show();
                        
                        // JavaScript varsa çalıştır
                        if (data.data.js) {
                            try {
                                const script = document.createElement('script');
                                script.innerHTML = data.data.js;
                                document.getElementById('widgetPreviewContent').appendChild(script);
                            } catch (error) {
                                console.error('Widget JavaScript çalıştırılırken hata:', error);
                            }
                        }
                    } else {
                        alert('Widget içeriği alınamadı: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Widget önizleme hatası:', error);
                    alert('Widget önizleme işlemi başarısız: ' + error.message);
                });
        }
    </script>
    @endpush
</x-admin-layout>