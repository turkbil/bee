function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((resolve, reject) => {
            if (document.execCommand('copy')) {
                textArea.remove();
                resolve();
            } else {
                textArea.remove();
                reject();
            }
        });
    }
}

function showCopyFeedback(element) {
    // Önceki bildirimleri temizle
    const oldFeedbacks = document.querySelectorAll('.copy-feedback');
    oldFeedbacks.forEach(el => el.remove());
    
    // Yeni bildirimi oluştur ve hemen göster
    const feedback = document.createElement('div');
    feedback.className = 'copy-feedback';
    feedback.textContent = 'Kopyalandı!';
    feedback.style.transition = 'none';
    feedback.style.animation = 'none';
    feedback.style.opacity = '1';
    
    // Element pozisyonunu ayarla
    if (getComputedStyle(element).position === 'static') {
        element.style.position = 'relative';
    }
    
    // Bildirimi ekle
    element.appendChild(feedback);
    
    // Bildirimin pozisyonunu ayarla (sol tarafta görünmesi için)
    const rect = element.getBoundingClientRect();
    if (rect.left < 100) {
        // Eğer element sayfanın soluna çok yakınsa, sağda göster
        feedback.style.left = 'auto';
        feedback.style.right = '-80px';
    }
    
    // Kısa süre sonra kaldır
    setTimeout(() => {
        if (feedback && feedback.parentNode) {
            feedback.parentNode.removeChild(feedback);
        }
    }, 600);
}

function showHandlebarsModal() {
    const modal = new bootstrap.Modal(document.getElementById('handlebarsModal'));
    modal.show();
}

document.addEventListener('click', function(e) {
    const copyableCode = e.target.closest('.copyable-code');
    const variableRow = e.target.closest('.variable-code');
    
    if (copyableCode) {
        e.preventDefault();
        const copyText = copyableCode.getAttribute('data-copy') || copyableCode.textContent;
        const cleanText = copyText.replace(/&#123;/g, '{').replace(/&#125;/g, '}');
        
        copyToClipboard(cleanText).then(() => {
            showCopyFeedback(copyableCode);
        }).catch(() => {
            console.error('Kopyalama başarısız');
        });
    } else if (variableRow) {
        e.preventDefault();
        const copyText = variableRow.getAttribute('data-copy');
        const cleanText = copyText.replace(/&#123;/g, '{').replace(/&#125;/g, '}');
        
        copyToClipboard(cleanText).then(() => {
            showCopyFeedback(variableRow);
        }).catch(() => {
            console.error('Kopyalama başarısız');
        });
    }
});