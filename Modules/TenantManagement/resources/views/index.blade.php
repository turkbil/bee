@extends('admin.layout')
@include('tenant::helper')
@section('content')
<div class="row row-cards">
    @foreach ($tenants as $tenant)
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <!-- Avatar yerine ID -->
                    <div class="col-auto">
                        <div class="avatar avatar-lg bg-blue text-white text-center">
                            <span class="avatar avatar-xl px-3 rounded">{{ $tenant->id }}</span>
                        </div>
                    </div>
                    <!-- Ana Bilgiler -->
                    <div class="col">
                        <h4 class="card-title m-0">
                            <a href="#">{{ $tenant->name }}</a>
                        </h4>
                        <div class="text-secondary">
                            www.site.com
                            <!-- Geçici açıklama -->
                        </div>
                        <div class="small mt-1">
                            @if($tenant->is_active)
                            <span class="badge bg-green fa-fade"></span> Online
                            @else
                            <span class="badge bg-red fa-fade"></span> Offline
                            @endif
                        </div>
                    </div>
                    <!-- Domainler Butonu -->
                    <div class="col-auto">
                        <a href="#" class="btn">
                            Domainler
                        </a>
                    </div>
                    <!-- Dropdown Menüsü -->
                    <div class="col-auto">
                        <div class="dropdown">
                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item btn-edit me-2" data-id="{{ $tenant->id }}" data-name="{{ $tenant->data['name'] ?? '' }}" data-fullname="{{ $tenant->data['fullname'] ?? '' }}" data-email="{{ $tenant->data['email'] ?? '' }}" data-phone="{{ $tenant->data['phone'] ?? '' }}" data-is_active="{{ $tenant->is_active }}" data-bs-toggle="modal" data-bs-target="#modal-tenant">
                                    Düzenle
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item btn-delete" data-id="{{ $tenant->id }}" data-title="{{ $tenant->data['name'] ?? 'Tenant' }}" data-module="tenant">
                                    Sil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
$(document).ready(function () {
    // Yeni tenant ekleme için modal sıfırlama
    $('#new-tenant-btn').on('click', function () {
        $('#tenant-id').val('');
        $('#tenant-name').val('');
        $('#tenant-fullname').val('');
        $('#tenant-email').val('');
        $('#tenant-phone').val('');
        $('#tenant-is-active').prop('checked', true); // Varsayılan olarak aktif
    });

    // Düzenleme modalini dinamik olarak doldur
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const card = $(this).closest('.card');
        const name = card.find('h4 a').text();
        const fullname = $(this).data('fullname');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const isActive = $(this).data('is_active') === 1;

        $('#tenant-id').val(id);
        $('#tenant-name').val(name);
        $('#tenant-fullname').val(fullname);
        $('#tenant-email').val(email);
        $('#tenant-phone').val(phone);
        $('#tenant-is-active').prop('checked', isActive);
    });

    // Form gönderimi
    $('#tenant-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize(); // Form verilerini al

        $.ajax({
            url: "{{ route('admin.tenant.manage') }}", // Backend rotası
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    const tenantId = response.tenant.id;
                    const tenantData = response.tenant.data;

                    // Kart güncelleme işlemi
                    if ($('#tenant-id').val() !== '') {
                        const card = $(`.btn-edit[data-id="${tenantId}"]`).closest('.card');

                        // Tenant ismini güncelle
                        card.find('h4 a').text(tenantData.name);

                        // Dropdown içindeki data- attribute'leri güncelle
                        const btnEdit = card.find('.btn-edit');
                        btnEdit.attr('data-name', tenantData.name);
                        btnEdit.attr('data-fullname', tenantData.fullname);
                        btnEdit.attr('data-email', tenantData.email);
                        btnEdit.attr('data-phone', tenantData.phone);
                        btnEdit.attr('data-is_active', response.tenant.is_active);

                        const btnDelete = card.find('.btn-delete');
                        btnDelete.attr('data-title', tenantData.name);

                        // Online/Offline durumunu güncelle
                        const badgeContainer = card.find('.small');
                        badgeContainer.html(''); // Önceki içeriği temizle
                        badgeContainer.append(`
                            <span class="badge ${response.tenant.is_active ? 'bg-green fa-fade' : 'bg-red fa-fade'}"></span>
                            ${response.tenant.is_active ? 'Online' : 'Offline'}
                        `);

                        card.hide().fadeIn(); // Güncelleme efekti
                    } else {
                        // Yeni tenant ekleme
                        const newCard = `
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-lg bg-blue text-white text-center">
                                                <span class="avatar avatar-xl px-3 rounded">${tenantId}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h4 class="card-title m-0">
                                                <a href="#">${tenantData.name}</a>
                                            </h4>
                                            <div class="text-secondary">www.site.com</div>
                                            <div class="small mt-1">
                                                <span class="badge ${response.tenant.is_active ? 'bg-green fa-fade' : 'bg-red fa-fade'}"></span>
                                                    ${response.tenant.is_active ? 'Online' : 'Offline'}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="btn">Domainler</a>
                                        </div>
                                        <div class="col-auto">
                                            <div class="dropdown">
                                                <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item btn-edit me-2"
                                                        data-id="${tenantId}"
                                                        data-name="${tenantData.name}"
                                                        data-fullname="${tenantData.fullname}"
                                                        data-email="${tenantData.email}"
                                                        data-phone="${tenantData.phone}"
                                                        data-is_active="${response.tenant.is_active}">
                                                        Düzenle
                                                    </a>
                                                    <a href="javascript:void(0);" class="dropdown-item btn-delete"
                                                        data-id="${tenantId}"
                                                        data-title="${tenantData.name}">
                                                        Sil
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        $('.row.row-cards').prepend(newCard).hide().fadeIn(); // Yeni kartı ekle ve fade efekti
                    }

                    $('#modal-tenant').modal('hide'); // Modal'i kapat
                } else {
                    console.error('Hata:', response.message);
                    alert("Bir hata oluştu: " + response.message);
                }
            },
            error: function (xhr) {
                console.error('AJAX Hatası:', xhr.responseJSON);
                alert("Bir hata oluştu. Lütfen tekrar deneyin.");
            }
        });
    });
});
</script>
@endpush
