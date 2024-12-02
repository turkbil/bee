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
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="btn btn-primary btn-open-domain-modal" data-tenant-id="{{ $tenant->id }}" data-bs-toggle="modal" data-bs-target="#modal-domain-management">
                                Domainler
                            </a>
                        </div>
                    </div>
                    <!-- Dropdown Menüsü -->
                    <div class="col-auto">
                        <div class="dropdown">
                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item btn-edit me-2" data-id="{{ $tenant->id }}" data-name="{{ $tenant->data['name'] ?? '' }}" data-fullname="{{ $tenant->data['fullname'] ?? '' }}" data-email="{{ $tenant->data['email'] ?? '' }}" data-phone="{{ $tenant->data['phone'] ?? '' }}" data-is_active="{{ $tenant->is_active }}" data-bs-toggle="modal" data-bs-target="#modal-tenant">Düzenle
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item btn-delete" data-id="{{ $tenant->id }}" data-title="{{ $tenant->data['name'] ?? 'Tenant' }}" data-module="tenant"> Sil
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
<!-- Domain Yönetimi Modal -->
<!-- Domain Yönetimi Modal -->
<div class="modal fade" id="modal-domain-management" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Domain Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-domain-form">
                    @csrf
                    <!-- Gizli Tenant ID -->
                    <input type="hidden" id="tenant_id" name="tenant_id" value="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="domain" name="domain" placeholder="siteadi.com" required>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </div>
                </form>
                <!-- Ekli Domainler -->
                <h3 class="mt-4">
                    Ekli Domainler
                    <small class="text-muted edit-note d-none">(Düzenlemek için üzerine tıklayın)</small>
                </h3>
                <ul class="list-group" id="domain-list">
                    <!-- Dinamik olarak doldurulacak -->
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
$(document).ready(function() {
    // Modal açıldığında tenant_id'yi form inputuna yaz
    $('#modal-domain-management').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Modal'ı açan butonu al
        const tenantId = button.data('tenant-id'); // Butondan tenant_id'yi al
        $('#tenant_id').val(tenantId); // Formdaki tenant_id inputuna yaz

        console.log('Tenant ID:', tenantId); // Kontrol amaçlı konsola yaz
    });
});

$(document).ready(function () {
    // Modal açıldığında tenant_id'yi form inputuna yaz ve domainleri yükle
    $(document).on('click', '.btn-open-domain-modal', function () {
        const tenantId = $(this).data('tenant-id');
        $('#modal-domain-management #tenant_id').val(tenantId);

        // Domain listesi temizlenip yeniden yüklenecek
        const domainList = $('#modal-domain-management #domain-list');
        const editNote = $('.edit-note'); // Not elemanını seçiyoruz
        domainList.empty();

        // AJAX ile tenant'a ait domainleri getir
        $.get(`{{ route('admin.tenant.getDomains', '') }}/${tenantId}`, function (response) {
            if (response.success && response.domains.length > 0) {
                editNote.removeClass('d-none'); // Notu görünür yap
                response.domains.forEach(function (domain) {
                    const domainItem = `
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${domain.id}">
                            <span class="editable-domain" contenteditable="false" data-original-value="${domain.domain}">${domain.domain}</span>
                            <span class="error-message text-danger small d-none"></span>
                            <div>
                                <button class="btn btn-sm btn-danger btn-delete-domain" data-id="${domain.id}">Sil</button>
                            </div>
                        </li>`;
                    domainList.append(domainItem);
                });
            } else {
                editNote.addClass('d-none'); // Notu gizle
                domainList.append('<li class="list-group-item text-muted no-domains">Kayıtlı domain bulunamadı.</li>');
            }
        });
    });

    // Domain düzenleme (tıklayınca contenteditable aktif olur)
    $(document).on('click', '.editable-domain', function () {
        $(this).attr('contenteditable', 'true').focus();
    });

    // Domain düzenleme sonrası kaydetme (Enter veya blur olayı)
    $(document).on('keydown blur', '.editable-domain', function (e) {
        if (e.type === 'keydown' && e.key !== 'Enter') {
            return; // Enter değilse işlem yapma
        }
        e.preventDefault();

        const domainSpan = $(this); // Düzenlenen alan
        const domainId = domainSpan.closest('li').data('id'); // Domain ID'sini al
        const newDomain = domainSpan.text().trim(); // Yeni domain değerini al
        const oldDomain = domainSpan.data('original-value'); // Eski domain değerini al
        const errorMessage = domainSpan.siblings('.error-message'); // Hata mesajı alanı

        // Hata mesajını gizle
        errorMessage.addClass('d-none').text('');

        // Yeni domain boş olmamalı
        if (newDomain === '') {
            errorMessage.removeClass('d-none').text('Domain adı boş olamaz.');
            domainSpan.text(oldDomain); // Eski değeri geri yükle
            domainSpan.attr('contenteditable', 'false'); // Düzenleme modunu kapat
            return;
        }

        // Eğer değer değişmediyse işlem yapma
        if (newDomain === oldDomain) {
            domainSpan.attr('contenteditable', 'false'); // Düzenleme modunu kapat
            return;
        }

        // Güncelleme isteği gönder
        $.ajax({
            url: `{{ route('admin.tenant.updateDomain', '') }}/${domainId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                domain: newDomain
            },
            success: function (response) {
                if (response.success) {
                    domainSpan.data('original-value', newDomain); // Yeni değeri kaydet
                    domainSpan.attr('contenteditable', 'false'); // Düzenleme modunu kapat
                } else {
                    errorMessage.removeClass('d-none').text(response.message || 'Domain güncellenirken bir hata oluştu.');
                    domainSpan.text(oldDomain); // Eski değeri geri yükle
                }
            },
            error: function (xhr) {
                console.error('Hata:', xhr.responseText);
                errorMessage.removeClass('d-none').text('Domain güncellenirken bir hata oluştu.');
                domainSpan.text(oldDomain); // Eski değeri geri yükle
            }
        });
    });

    // Domain silme
    $(document).on('click', '.btn-delete-domain', function () {
        const domainId = $(this).data('id');
        $.ajax({
            url: `{{ route('admin.tenant.deleteDomain', '') }}/${domainId}`,
            type: 'DELETE',
            data: { _token: "{{ csrf_token() }}" },
            success: function (response) {
                if (response.success) {
                    $(`li[data-id="${domainId}"]`).remove();

                    // Eğer listede domain kalmadıysa mesaj ekle
                    if ($('#modal-domain-management #domain-list li').length === 0) {
                        $('#modal-domain-management #domain-list').append('<li class="list-group-item text-muted no-domains">Kayıtlı domain bulunamadı.</li>');
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                console.error('Hata:', xhr.responseText);
                alert('Domain silinirken bir hata oluştu.');
            }
        });
    });

    // Yeni domain ekleme
    $('#add-domain-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post("{{ route('admin.tenant.addDomain') }}", formData, function (response) {
            if (response.success) {
                const newDomain = `
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${response.domain.id}">
                        <span class="editable-domain" contenteditable="false" data-original-value="${response.domain.domain}">${response.domain.domain}</span>
                        <span class="error-message text-danger small d-none"></span>
                        <div>
                            <button class="btn btn-sm btn-danger btn-delete-domain" data-id="${response.domain.id}">Sil</button>
                        </div>
                    </li>`;
                $('#modal-domain-management #domain-list').append(newDomain);
                $('#add-domain-form #domain').val(''); // Input'u temizle

                // "Kayıtlı domain bulunamadı" mesajını kaldır ve notu görünür yap
                $('#modal-domain-management #domain-list .no-domains').remove();
                $('.edit-note').removeClass('d-none');
            } else {
                alert(response.message || 'Domain eklenirken bir hata oluştu.');
            }
        }).fail(function (xhr) {
            console.error('Hata:', xhr.responseText);
            alert('Domain eklenirken bir hata oluştu.');
        });
    });
});




$(document).ready(function () {
    // Yeni tenant ekleme modalini sıfırla
    $('#new-tenant-btn').on('click', function () {
        $('#tenant-id').val('');
        $('#tenant-name').val('');
        $('#tenant-fullname').val('');
        $('#tenant-email').val('');
        $('#tenant-phone').val('');
        $('#tenant-is-active').prop('checked', true); // Varsayılan olarak aktif
    });

    // Tenant düzenleme modalini doldur
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const fullname = $(this).data('fullname');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const isActive = $(this).data('is_active') === 1;

        // Modal alanlarını doldur
        $('#tenant-id').val(id);
        $('#tenant-name').val(name);
        $('#tenant-fullname').val(fullname);
        $('#tenant-email').val(email);
        $('#tenant-phone').val(phone);
        $('#tenant-is-active').prop('checked', isActive);
    });

    // Tenant ekleme ve düzenleme form gönderimi
    $('#tenant-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize(); // Form verilerini al
        const tenantId = $('#tenant-id').val(); // Düzenlenen tenant'ın ID'si

        $.ajax({
            url: "{{ route('admin.tenant.manage') }}", // Backend rotası
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    const tenant = response.tenant; // Gelen tenant verisi

                    // Güncellenmiş kart içeriği
                    const updatedCard = `
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-4 align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar avatar-lg bg-blue text-white text-center">
                                            <span class="avatar avatar-xl px-3 rounded">${tenant.id}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h4 class="card-title m-0">
                                            <a href="#">${tenant.data.name}</a>
                                        </h4>
                                        <div class="text-secondary">www.site.com</div>
                                        <div class="small mt-1">
                                            <span class="badge ${tenant.is_active ? 'bg-green fa-fade' : 'bg-red fa-fade'}"></span>
                                            ${tenant.is_active ? 'Online' : 'Offline'}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0);" class="btn btn-primary btn-open-domain-modal" data-tenant-id="${tenant.id}" data-bs-toggle="modal" data-bs-target="#modal-domain-management">
                                            Domainler
                                        </a>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="#" class="dropdown-item btn-edit me-2"
                                                   data-id="${tenant.id}"
                                                   data-name="${tenant.data.name}"
                                                   data-fullname="${tenant.data.fullname}"
                                                   data-email="${tenant.data.email}"
                                                   data-phone="${tenant.data.phone}"
                                                   data-is_active="${tenant.is_active}">
                                                    Düzenle
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item btn-delete"
                                                   data-id="${tenant.id}" data-title="${tenant.data.name}">
                                                    Sil
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    if (tenantId) {
                        // Eğer tenant ID varsa, kartı güncelle
                        const card = $(`.btn-edit[data-id="${tenantId}"]`).closest('.col-md-6');
                        card.html(updatedCard);
                    } else {
                        // Yeni tenant ise, sayfaya ekle
                        const newCard = `<div class="col-md-6">${updatedCard}</div>`;
                        $('.row.row-cards').prepend(newCard).hide().fadeIn();
                    }

                    $('#modal-tenant').modal('hide'); // Modal'ı kapat
                } else {
                    console.error('Hata:', response.message);
                }
            },
            error: function (xhr) {
                console.error('Hata:', xhr.responseText);
            }
        });
    });

    // Tenant silme işlemi
    $(document).on('click', '.btn-delete', function () {
        const tenantId = $(this).data('id'); // Silinecek tenant'ın ID'si
        const tenantCard = $(this).closest('.col-md-6'); // İlgili tenant kartı

        // Silme modalini çağır
        openDeleteModal({
            id: tenantId,
            onConfirm: function () {
                $.ajax({
                    url: `{{ url('admin/tenant/delete') }}/${tenantId}`, // Backend rotası
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response.success) {
                            tenantCard.fadeOut(300, function () {
                                $(this).remove(); // DOM'dan tamamen kaldır
                            });
                        } else {
                            console.error('Silme hatası:', response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Hata:', xhr.responseText);
                    }
                });
            }
        });
    });
});


</script>
@endpush
@push('css')
<style>
    .editable-domain {
        border-bottom: 1px dashed #ccc;
        cursor: pointer;
        padding-bottom: 2px;
        display: inline-block;
    }

    .editable-domain[contenteditable="true"] {
        border-bottom: none; /* Düzenleme sırasında alt çizgi kaybolsun */
        outline: none;       /* Kenarlık görünmesin */
    }
</style>
@endpush
