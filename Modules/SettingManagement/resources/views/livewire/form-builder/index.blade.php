<div class="container my-4">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title">Form Builder</h3>
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Ayarlar Modülüne Dön
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-vcenter table-hover">
                    <thead>
                        <tr>
                            <th>Grup Adı</th>
                            <th>Açıklama</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->description }}</td>
                            <td>
                                <a href="{{ route('admin.settingmanagement.form-builder.edit', $group->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i> Form Düzenle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>