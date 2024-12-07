$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();

    $('#table').bootstrapTable();

    // Dark mode
    var darkModeCookie = Cookies.get("dark");
    if (darkModeCookie && darkModeCookie === "1") {
        $("body").removeClass("light").addClass("dark").attr('data-bs-theme', 'dark');
        $('.dark-switch').prop('checked', true);
    } else {
        $("body").removeClass("dark").addClass("light").attr('data-bs-theme', 'light');
        $('.dark-switch').prop('checked', false);
    }

    $('.dark-switch').change(function() {
        if ($(this).is(":checked")) {
            $("body").removeClass("light").addClass("dark").attr('data-bs-theme', 'dark');
            Cookies.set("dark", "1", { expires: 365 });
        } else {
            $("body").removeClass("dark").addClass("light").attr('data-bs-theme', 'light');
            Cookies.set("dark", "0", { expires: 365 });
        }
    });


    // Table mode
    var tableModeCookie = Cookies.get("table");
    if (tableModeCookie && tableModeCookie === "1") {
        $("table").addClass("table-sm");
        $('.table-switch').prop('checked', true);
    } else {
        $("table").removeClass("table-sm");
        $('.table-switch').prop('checked', false);
    }

    $('.table-switch').change(function() {
        if ($(this).is(":checked")) {
            $("table").addClass("table-sm");
            Cookies.set("table", "1", { expires: 365 });
        } else {
            $("table").removeClass("table-sm");
            Cookies.set("table", "0", { expires: 365 });
        }
    });



    // Tablo ilk sayfaya dön bağlantısını kontrol et
    var $table = $('#table');

    // Sayfa yüklendiğinde kontrol et
    $(document).ready(function() {
        addFirstPageLink();
        checkFirstPage();
    });

    // Tablo içeriği değiştikçe kontrol et
    $table.on('post-body.bs.table', function() {
        addFirstPageLink();
        checkFirstPage();
    });

    // "İlk Sayfaya Dön" bağlantısını tabloya ekle
    function addFirstPageLink() {
        if ($('#goToFirstPage').length === 0) {
            $('.fixed-table-toolbar').append('<a href="#" id="goToFirstPage" class="ml-3 btn btn-btn fw-lighter">İlk Sayfaya Dön</a>');
        }
    }

    // Sayfa numarasına göre bağlantının görünürlüğünü ayarla
    function checkFirstPage() {
        var options = $table.bootstrapTable('getOptions');
        if (options.pageNumber === 1) {
            $('#goToFirstPage').hide();
        } else {
            $('#goToFirstPage').show();
        }
    }

    // "İlk Sayfaya Dön" bağlantısına tıklama olayı
    $(document).on('click', '#goToFirstPage', function(e) {
        $table.bootstrapTable('selectPage', 1);
    });



});



// Tüm modallar kapandığında form ve input alanlarını sıfırla
$(document).on('hidden.bs.modal', '.modal', function() {
    // Formları sıfırla
    $(this).find('form').each(function() {
        this.reset();
    });

    // Tüm input alanlarını sıfırla
    $(this).find('input[type="text"], input[type="email"], input[type="number"], textarea').val('');
    $(this).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
    $(this).find('select').prop('selectedIndex', 0);
});




// Asagisi silme ve sonrasında toast olusturma ile ilgili.
// CSRF Token'ı ayarla
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

// Silme butonuna tıklama olayı
$(document).on('click', '.btn-delete', function() {
    const module = $(this).data('module');
    const itemId = $(this).data('id');
    const itemTitle = $(this).data('title') || 'Öğe';

    // Dinamik modal oluşturma
    const modalHtml = `
        <div class="modal fade" id="modal-delete-item" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <i class="fa-solid fa-triangle-exclamation fa-4x mb-3 text-danger fa-shake"></i>
                        <h3>Silmek istediğinize emin misiniz?</h3>
                        <div class="text-secondary">
                            <strong>${itemTitle}</strong> adlı öğeyi kalıcı olarak silmek üzeresiniz.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button class="btn w-100" data-bs-dismiss="modal">Vazgeç</button>
                                </div>
                                <div class="col">
                                    <button id="confirm-delete" class="btn btn-danger w-100" data-module="${module}" data-id="${itemId}" data-title="${itemTitle}">Sil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Modalı body'ye ekle ve göster
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modal-delete-item'));
    modal.show();

    // Modal kapandıktan sonra DOM'dan kaldır
    $('#modal-delete-item').on('hidden.bs.modal', function() {
        $(this).remove();
    });
});

// Modal içinde Space tuşu ile silme
$(document).on('shown.bs.modal', '#modal-delete-item', function() {
    // Modal gösterildiğinde keydown olayını dinle
    $(document).on('keydown.modal', function(event) {
        if (event.code === 'Space') { // Space tuşuna basıldığında
            event.preventDefault(); // Sayfanın kaymasını engelle
            $('#confirm-delete').trigger('click'); // Silme butonunu tetikle
        }
    });
});

// Modal kapandığında keydown olayını kaldır
$(document).on('hidden.bs.modal', '#modal-delete-item', function() {
    $(document).off('keydown.modal'); // Event listener'ı kaldır
});

// Silme işlemini onayla
$(document).on('click', '#confirm-delete', function() {
    const module = $(this).data('module');
    const itemId = $(this).data('id');
    const itemTitle = $(this).data('title') || 'Öğe';

    // AJAX DELETE isteği gönder
    $.ajax({
        url: `/admin/${module}/${itemId}`,
        method: 'DELETE',
        success: function(response) {
            if (response.success) {
                $(`#row-${itemId}`).fadeOut(500, function() {
                    $(this).remove();
                });
                showToast(`${itemTitle}`, 'success');
                $('#modal-delete-item').modal('hide');
            } else {
                showToast(`${itemTitle}`, 'error');
            }
        },
        error: function(xhr) {
            console.error(xhr);
            showToast(`${itemTitle}`, 'error');
        }
    });
});

// Dinamik Toast Mesajı Gösterimi
function showToast(itemTitle, type = 'success') {
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>');
    }

    const currentTime = new Date();
    const time = currentTime.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });

    const message = type === 'success' ?
        `<strong>${itemTitle}</strong> adlı öğe başarıyla silindi.` :
        `<strong>${itemTitle}</strong> adlı öğe silinemedi.`;

    const toastHtml = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fa-solid ${type === 'success' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-2"></i>
                <strong class="me-auto">${type === 'success' ? 'Başarılı' : 'Hata!'}</strong>
                <small>${time}</small>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    const $toast = $(toastHtml);
    $('.toast-container').append($toast);

    const toastInstance = new bootstrap.Toast($toast[0]);
    toastInstance.show();

    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
