// Livewire olaylarını dinle
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Livewire !== 'undefined') {
        Livewire.on('toast', function(data) {
            const toastData = Array.isArray(data) ? data[0] : data;
            if (toastData && toastData.title && toastData.message) {
                showToast(toastData.title, toastData.message, toastData.type || 'success');
            } else {
                console.error('Invalid toast data structure:', data);
            }
        });
    }
});

function showToast(title, message, type = 'success') {
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>');
    }

    // İkon ve renk seçimi
    let headerIcon, bodyIcon, bgColor, textColorClass;
    switch (type) {
        case 'success':
            headerIcon = '<i class="fa-solid fa-circle-check me-2"></i>'; // Başarı simgesi
            bodyIcon = '<i class="fa-solid fa-check me-2"></i>'; // Onay simgesi
            bgColor = 'bg-success';
            textColorClass = 'text-success';
            break;
        case 'danger':
        case 'error':
            headerIcon = '<i class="fa-solid fa-circle-xmark me-2"></i>'; // Hata simgesi
            bodyIcon = '<i class="fa-solid fa-ban me-2"></i>'; // Engelleme/tehlike simgesi
            bgColor = 'bg-danger';
            textColorClass = 'text-danger';
            break;
        case 'warning':
            headerIcon = '<i class="fa-solid fa-triangle-exclamation me-2"></i>'; // Uyarı simgesi
            bodyIcon = '<i class="fa-solid fa-circle-exclamation me-2"></i>'; // Dikkat simgesi
            bgColor = 'bg-warning';
            textColorClass = 'text-warning';
            break;
        case 'info':
            headerIcon = '<i class="fa-solid fa-circle-info me-2"></i>'; // Bilgi simgesi
            bodyIcon = '<i class="fa-solid fa-lightbulb me-2"></i>'; // Fikir/bilgi simgesi
            bgColor = 'bg-info';
            textColorClass = 'text-info';
            break;
        default:
            headerIcon = '<i class="fa-solid fa-bell me-2"></i>'; // Bildirim simgesi
            bodyIcon = '<i class="fa-solid fa-envelope me-2"></i>'; // Mesaj simgesi
            bgColor = 'bg-primary';
            textColorClass = 'text-primary';
    }


    // Şu anki saat ve dakika
    const now = new Date();
    const time = now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });

    const toastHtml =
        `<div class="toast card card-shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header ${bgColor} text-white py-2">
                ${headerIcon}
                <strong class="me-auto">${title}</strong>
                <small class="text-white opacity-75">
                    <i class="far fa-clock me-1"></i>${time}
                </small>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body card-body">
                <div class="d-flex align-items-center">
                    <span class="${textColorClass}">${bodyIcon}</span>
                    <span>${message}</span>
                </div>
            </div>
        </div>`;

    const $toast = $(toastHtml);
    $('.toast-container').append($toast);

    // Toast'a hover efekti ekle
    $toast.hover(
        function() { $(this).css('opacity', '1'); },
        function() { $(this).css('opacity', '0.95'); }
    );

    // Toast animasyonu
    $toast.css({
        'transform': 'translateY(100px)',
        'transition': 'all 0.3s ease-out',
        'opacity': '0'
    });

    setTimeout(() => {
        $toast.css({
            'transform': 'translateY(0)',
            'opacity': '0.95'
        });
    }, 50);

    // Bootstrap Toast kontrolü - bu kısmı düzelteceğiz
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        // Bootstrap mevcut ise Toast API'sini kullan
        const toastInstance = new bootstrap.Toast($toast[0]);
        toastInstance.show();
    } else {
        // Bootstrap mevcut değilse manuel olarak toast'ı göster ve gizle
        setTimeout(() => {
            $toast.addClass('show');
            
            // 5 saniye sonra toast'ı kaldır
            setTimeout(() => {
                $toast.css({
                    'transform': 'translateY(100px)',
                    'opacity': '0'
                });
                
                // Animasyon tamamlandıktan sonra toast'ı kaldır
                setTimeout(() => {
                    $toast.remove();
                }, 300);
            }, 5000);
        }, 100);
    }

    // Toast kapanma olayı
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
    
    // Toast kapatma butonuna tıklama işlemi
    $toast.find('.btn-close').on('click', function() {
        $toast.css({
            'transform': 'translateY(100px)',
            'opacity': '0'
        });
        
        // Animasyon tamamlandıktan sonra toast'ı kaldır
        setTimeout(() => {
            $toast.remove();
        }, 300);
    });
}