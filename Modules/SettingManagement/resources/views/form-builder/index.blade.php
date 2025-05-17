@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Builder</h3>
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