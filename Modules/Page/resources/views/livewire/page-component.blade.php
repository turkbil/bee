<div>
    <div class="d-flex justify-content-between mb-3">
        <input type="text" class="form-control w-50" placeholder="Ara..." wire:model.debounce.500ms="search">
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Başlık</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td>{{ $page->page_id }}</td>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->is_active ? 'Aktif' : 'Pasif' }}</td>
                    <td>
                        <a href="{{ route('admin.page.manage', $page->page_id) }}" class="btn btn-sm btn-info">Düzenle</a>
                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $page->page_id }})">Sil</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Kayıt bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $pages->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bu sayfayı silmek istediğinize emin misiniz?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePage" data-bs-dismiss="modal">Sil</button>
                </div>
            </div>
        </div>
    </div>
</div>
