// Document Ready 
$(document).ready(function() {
    // Tooltip başlat
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Table varsa önce kaldır ve yeniden başlat
    if ($('#table').data('bootstrap.table')) {
        $('#table').bootstrapTable('destroy');
    }
    $('#table').bootstrapTable();

    // Dark mode - Sayfa yüklendiğinde durumu ayarla
    function initDarkMode() {
        var darkModeCookie = Cookies.get("dark");
        if (darkModeCookie && darkModeCookie === "1") {
            $("body").removeClass("light").addClass("dark").attr('data-bs-theme', 'dark');
            $('.dark-switch').prop('checked', true);
        } else {
            $("body").removeClass("dark").addClass("light").attr('data-bs-theme', 'light');
            $('.dark-switch').prop('checked', false);
        }
    }
    
    // İlk yüklemede dark mode durumunu ayarla
    initDarkMode();
    
    // Olay dinleyiciyi doğrudan bağla (off kullanmadan)
    $(document).on('change', '.dark-switch', function() {
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

    $('.table-switch').off('change').on('change', function() {
        if ($(this).is(":checked")) {
            $("table").addClass("table-sm");
            Cookies.set("table", "1", { expires: 365 });
        } else {
            $("table").removeClass("table-sm");
            Cookies.set("table", "0", { expires: 365 });
        }
    });

    // İlk sayfaya dön bağlantısını kontrol et
    var $table = $('#table');
    
    function addFirstPageLink() {
        if ($('#goToFirstPage').length === 0) {
            $('.fixed-table-toolbar').append('<a href="#" id="goToFirstPage" class="ml-3 btn btn-btn fw-lighter">İlk Sayfaya Dön</a>');
        }
    }

    function checkFirstPage() {
        var options = $table.bootstrapTable('getOptions');
        if (options.pageNumber === 1) {
            $('#goToFirstPage').hide();
        } else {
            $('#goToFirstPage').show();
        }
    }

    $(document).ready(function() {
        addFirstPageLink();
        checkFirstPage();
    });

    $table.on('post-body.bs.table', function() {
        addFirstPageLink();
        checkFirstPage();
    });

    $(document).on('click', '#goToFirstPage', function(e) {
        $table.bootstrapTable('selectPage', 1);
    });
});

// CSRF Token ayarla
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

// Silme butonu işlemleri
$(document).on('click', '.btn-delete', function() {
    const module = $(this).data('module');
    const itemId = $(this).data('id');
    const itemTitle = $(this).data('title') || 'Öğe';

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
                                    <button id="confirm-delete" class="btn btn-danger w-100" 
                                            data-module="${module}" 
                                            data-id="${itemId}" 
                                            data-title="${itemTitle}">Sil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modal-delete-item'));
    modal.show();

    $('#modal-delete-item').on('hidden.bs.modal', function() {
        $(this).remove();
    });
});

// Space tuşu ile silme
$(document).on('shown.bs.modal', '#modal-delete-item', function() {
    $(document).on('keydown.modal', function(event) {
        if (event.code === 'Space') {
            event.preventDefault();
            $('#confirm-delete').trigger('click');
        }
    });
});

$(document).on('hidden.bs.modal', '#modal-delete-item', function() {
    $(document).off('keydown.modal');
});

// Silme işlemini onayla
$(document).on('click', '#confirm-delete', function() {
  const module = $(this).data('module');
  const itemId = $(this).data('id');
  const itemTitle = $(this).data('title') || 'Öğe';
  
  $.ajax({
      url: `/admin/${module}/${itemId}`,
      method: 'DELETE',
      success: function(response) {
          if (response.success) {
              // Silinecek satırı seç 
              const $row = $(`#table tr[data-uniqueid="${itemId}"]`);
              
              // Silinen satırın yüksekliğini al
              const rowHeight = $row.outerHeight();
              
              // Bootstrap danger background
              $row.addClass('bg-red text-red-fg');
              
              // Sonraki tüm satırları seç
              const $nextRows = $row.nextAll('tr');
              
              // Sonraki satırların yukarı kayma animasyonu için hazırlık
              $nextRows.css('position', 'relative');
              
              // Silinen satırın animasyonu ve sonraki satırların yukarı kayması
              setTimeout(() => {
                  $row.animate({
                      opacity: 0,
                      height: 0,
                      paddingTop: 0,
                      paddingBottom: 0
                  }, 400);
                  
                  // Sonraki satırların yukarı kayma animasyonu
                  $nextRows.each(function(index) {
                      $(this).animate({
                          top: -rowHeight
                      }, {
                          duration: 400,
                          complete: function() {
                              // Animasyon tamamlandığında
                              if (index === $nextRows.length - 1) {
                                  // Son satırın animasyonu bittiğinde satırı sil
                                  $('#table').bootstrapTable('removeByUniqueId', itemId);
                                  // Sonraki satırların pozisyonlarını resetle
                                  $nextRows.css({
                                      'position': '',
                                      'top': ''
                                  });
                              }
                          }
                      });
                  });
              }, 300);
              
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


// Toast mesajı gösterimi
function showToast(itemTitle, action, status = 'success') {
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>');
    }

    const currentTime = new Date();
    const time = currentTime.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });

    const actionMessages = {
        added: 'eklendi',
        updated: 'güncellendi',
        deleted: 'silindi',
        activated: 'aktif hale getirildi',
        deactivated: 'pasif hale getirildi'
    };

    const actionColors = {
        success: 'text-success',
        error: 'text-danger'
    };

    const message = `<strong>${itemTitle}</strong> adlı öğe ${actionMessages[action]} ${status === 'success' ? 'başarıyla' : 'başarısız.'}`;

    const toastHtml = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fa-solid ${status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} ${actionColors[status]} me-2"></i>
                <strong class="me-auto">${status === 'success' ? 'Başarılı' : 'Hata!'}</strong>
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


// LIVEWIRE Modal işlemleri
$(document).on('hidden.bs.modal', '.modal', function() {
    $(this).find('form').each(function() {
        this.reset();
    });

    $(this).find('input[type="text"], input[type="email"], input[type="number"], textarea').val('');
    $(this).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
    $(this).find('select').prop('selectedIndex', 0);
});







$(document).ready(function () {
    // Silme butonuna tıklandığında
    $('.btn-delete').on('click', function () {
        var itemId = $(this).data('id');
        var itemTitle = $(this).data('title');
        var module = $(this).data('module');

        // Modal içeriğini doldur
        $('#modal-item-title').text(itemTitle);
        $('#confirm-delete').data('id', itemId).data('module', module);

        // Modalı göster
        $('#modal-delete-item').modal('show');
    });

    // Silme işlemini onayla
    $('#confirm-delete').on('click', function () {
        var itemId = $(this).data('id');
        var module = $(this).data('module');

        // Livewire emit ile silme fonksiyonunu çağır
        Livewire.emit('confirmDelete', itemId);

        // Modalı kapat
        $('#modal-delete-item').modal('hide');
    });
});
