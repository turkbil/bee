@extends('admin.layout')
@include('tenant::helper')
@section('content')
<div class="row row-cards">
    @foreach ($tenants as $tenant)
    <div class="col-md-6 col-lg-3" id="row-{{ $tenant->id }}">
        <div class="card">
            <div class="card-body p-4 text-center">
                <span class="avatar avatar-xl mb-3 rounded">{{ $tenant->id }}</span>
                <h3 class="m-0 mb-1"><a href="#">{{ $tenant->data['name'] ?? 'Unnamed' }}</a></h3>
                <div class="text-secondary">{{ $tenant->data['email'] ?? 'Email belirtilmedi' }}</div>
                <div class="mt-3">
                    <span class="badge {{ $tenant->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">{{ $tenant->is_active ? 'Aktif' : 'Pasif' }}</span>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <a href="#" class="card-btn btn-edit me-2" data-id="{{ $tenant->id }}" data-name="{{ $tenant->data['name'] ?? '' }}" data-fullname="{{ $tenant->data['fullname'] ?? '' }}" data-email="{{ $tenant->data['email'] ?? '' }}" data-phone="{{ $tenant->data['phone'] ?? '' }}" data-is_active="{{ $tenant->is_active }}" data-bs-toggle="modal" data-bs-target="#modal-tenant">
                    <i class="fa-solid fa-pen-to-square me-2 text-muted"></i>Düzenle
                </a>
                <a href="javascript:void(0);" class="card-btn btn-delete" data-id="{{ $tenant->id }}" data-title="{{ $tenant->data['name'] ?? 'Tenant' }}" data-module="tenant">
                    <i class="fa-solid fa-trash me-2 text-muted"></i>Sil
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<!-- Modal -->
<div class="modal modal-blur fade" id="modal-tenant" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tenant Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tenant-form">
                @csrf
                <input type="hidden" name="id" id="tenant-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="tenant-name" name="data[name]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yetkili Adı Soyadı</label>
                        <input type="text" class="form-control" id="tenant-fullname" name="data[fullname]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Adresi</label>
                        <input type="email" class="form-control" id="tenant-email" name="data[email]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon Numarası</label>
                        <input type="text" class="form-control" id="tenant-phone" name="data[phone]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Aktiflik Durumu</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="tenant-is-active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="tenant-is-active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
$(document).ready(function() {
    // Yeni tenant ekleme için modal sıfırlama
    $('#new-tenant-btn').on('click', function() {
        $('#tenant-id').val('');
        $('#tenant-name').val('');
        $('#tenant-fullname').val('');
        $('#tenant-email').val('');
        $('#tenant-phone').val('');
        $('#tenant-is-active').prop('checked', true); // Varsayılan olarak aktif
    });

    // Düzenleme modalini doldur
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const fullname = $(this).data('fullname');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const isActive = $(this).data('is_active') === 1; // Aktif mi?

        $('#tenant-id').val(id);
        $('#tenant-name').val(name);
        $('#tenant-fullname').val(fullname);
        $('#tenant-email').val(email);
        $('#tenant-phone').val(phone);
        $('#tenant-is-active').prop('checked', isActive);
    });

    // Form gönderimi
    $('#tenant-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize(); // Form verilerini al

        $.ajax({
            url: "{{ route('admin.tenant.manage') }}", // Backend rotası
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    const tenantId = response.tenant.id;
                    const tenantData = response.tenant.data;

                    if ($('#tenant-id').val() === '') {
                        // Yeni tenant ekleme (listenin başına ekle)
                        const newCard = `
                                <div class="col-md-6 col-lg-3 fade-in">
                                    <div class="card">
                                        <div class="card-body p-4 text-center">
                                            <span class="avatar avatar-xl mb-3 rounded">${tenantId}</span>
                                            <h3 class="m-0 mb-1"><a href="#">${tenantData.name}</a></h3>
                                            <div class="text-secondary">${tenantData.email || 'Belirtilmedi'}</div>
                                            <div class="mt-3">
                                                <span class="badge ${response.tenant.is_active ? 'bg-green-lt' : 'bg-red-lt'}">${response.tenant.is_active ? 'Aktif' : 'Pasif'}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <a href="#" class="card-btn btn-edit me-2"
                                                data-id="${tenantId}" data-name="${tenantData.name}" data-fullname="${tenantData.fullname}" data-email="${tenantData.email}" data-phone="${tenantData.phone}" data-is_active="${response.tenant.is_active}"
                                                data-bs-toggle="modal" data-bs-target="#modal-tenant">
                                                <i class="fa-solid fa-pen-to-square me-2 text-muted"></i>Düzenle
                                            </a>
                                            <a href="#" class="card-btn btn-delete" data-id="${tenantId}">
                                                <i class="fa-solid fa-trash me-2 text-muted"></i>Sil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        $('.row.row-cards').prepend(newCard); // Yeni kartı listenin başına ekle
                    } else {
                        // Mevcut tenant güncelle
                        const card = $(`.btn-edit[data-id="${tenantId}"]`).closest('.card');
                        card.find('h3 a').text(tenantData.name); // Name güncelle
                        card.find('.text-secondary').text(tenantData.email || 'Belirtilmedi'); // Email güncelle
                        card.find('.badge').removeClass('bg-green-lt bg-red-lt').addClass(response.tenant.is_active ? 'bg-green-lt' : 'bg-red-lt').text(response.tenant.is_active ? 'Aktif' : 'Pasif'); // Aktiflik durumu
                    }

                    $('#modal-tenant').modal('hide'); // Modal'i kapat
                } else {
                    console.error('Hata:', response.message);
                    alert("Bir hata oluştu: " + response.message);
                }
            },
            error: function(xhr) {
                console.error('AJAX Hatası:', xhr.responseJSON);
                alert("Bir hata oluştu. Lütfen tekrar deneyin.");
            }
        });
    });
});

</script>
@endpush
