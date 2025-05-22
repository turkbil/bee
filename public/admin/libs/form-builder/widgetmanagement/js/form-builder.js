document.addEventListener("DOMContentLoaded", function() {
  if (window.widgetFormBuilderInitialized) {
    return;
  }
  window.widgetFormBuilderInitialized = true;

  window.elementCounter = 0;
  window.selectedElement = null;
  window.formCanvas = document.getElementById("form-canvas");
  window.emptyCanvas = document.getElementById("empty-canvas");
  window.elementPalette = document.getElementById("element-palette");
  window.propertiesPanel = document.getElementById("properties-panel");
  window.undoStack = [];
  window.redoStack = [];
  window.canvasLoading = document.getElementById("canvas-loading");
  
  window.slugifyTurkish = function(text) {
    if (!text) return '';
    
    const turkishChars = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'i': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 
                          'Ç': 'C', 'Ğ': 'G', 'I': 'I', 'İ': 'I', 'Ö': 'O', 'Ş': 'S', 'Ü': 'U' };
    
    let slug = text.replace(/[çğıiöşüÇĞIİÖŞÜ]/g, function(char) {
      return turkishChars[char] || char;
    });
    
    slug = slug.toLowerCase()
              .replace(/[^a-z0-9_]+/g, '_')
              .replace(/^_+|_+$/g, '')
              .replace(/_+/g, '_');
    
    if (/^[0-9]/.test(slug)) {
      slug = 'a_' + slug;
    }
    
    return slug;
  };

  console.log("Widget Form Builder başlatılıyor...");
  
  if (window.canvasLoading) {
    window.canvasLoading.style.display = "flex";
  }
  
  const loadSavedForm = function() {
    const widgetId = document.getElementById('widget-id')?.value;
    const schemaType = document.getElementById('schema-type')?.value;
    
    if (widgetId && schemaType) {
      console.log("Widget form yapısı yükleniyor, Widget ID:", widgetId, "Schema Type:", schemaType);
      
      fetch(`/admin/widgetmanagement/form-builder/${widgetId}/load?schema=${schemaType}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success && data.layout) {
          console.log('Widget form yapısı yükleniyor:', data.layout);
          if (typeof window.loadFormFromJSON === 'function') {
            window.loadFormFromJSON(data.layout);
          } else {
            console.error('loadFormFromJSON fonksiyonu bulunamadı');
          }
        }
        
        setTimeout(() => {
          if (window.canvasLoading) {
            window.canvasLoading.style.opacity = "0";
            setTimeout(() => {
              window.canvasLoading.style.display = "none";
            }, 300);
          }
        }, 500);
      })
      .catch(error => {
        console.error('Widget form yükleme hatası:', error);
        
        if (window.canvasLoading) {
          window.canvasLoading.style.display = "none";
        }
      });
    } else {
      if (window.canvasLoading) {
        window.canvasLoading.style.display = "none";
      }
    }
  };

  const saveBtn = document.getElementById("save-btn");
  if (saveBtn && !saveBtn.hasAttribute('data-listener-added')) {
    saveBtn.setAttribute('data-listener-added', 'true');
    saveBtn.addEventListener("click", function() {
      if (!window.getFormJSON) return;
      
      const formData = window.getFormJSON();
      console.log("Widget Form Verisi:", formData);
      
      const widgetId = document.getElementById('widget-id').value;
      const schemaType = document.getElementById('schema-type').value;
      
      if (widgetId && schemaType) {
        const originalContent = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
        saveBtn.disabled = true;
        
        fetch(`/admin/widgetmanagement/form-builder/${widgetId}/save?schema=${schemaType}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            layout: formData
          })
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          saveBtn.innerHTML = originalContent;
          saveBtn.disabled = false;
          
          window.showToast(data.success ? 'success' : 'error', 
                         data.success ? 'Başarılı!' : 'Hata!', 
                         data.success ? 'Widget form yapısı başarıyla kaydedildi.' : data.error);
        })
        .catch(error => {
          console.error('Widget kayıt hatası:', error);
          
          saveBtn.innerHTML = originalContent;
          saveBtn.disabled = false;
          
          window.showToast('error', 'Hata!', 'Widget form yapısı kaydedilirken bir hata oluştu.');
        });
      }
    });
  }
  
  window.showToast = function(type, title, message) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = `toast show bg-${type === 'success' ? 'success' : 'danger'} text-white`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Kapat"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
    
    const closeBtn = toast.querySelector('.btn-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        toast.classList.remove('show');
        setTimeout(() => {
          toast.remove();
        }, 300);
      });
    }
  };
  
  // Form yükleme işlemini sadece bir kere gerçekleştirmek için kontrol
  // Eğer form edit.blade.php'den yüklendiyse tekrar yükleme yapma
  if (window.formLoadedFromBlade) {
    console.log("Form zaten edit.blade.php'den yüklenmiş, tekrar yükleme yapılmayacak.");
    return;
  }
  
  // Yükleme işlemini gerçekleştir ve yüklendiğini işaretle
  if (!window.formLoaded) {
    window.formLoaded = true;
    console.log("Form form-builder.js tarafından yükleniyor...");
    loadSavedForm();
  }
});