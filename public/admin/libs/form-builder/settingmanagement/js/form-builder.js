// Form Builder Ana Başlatma Kodu
document.addEventListener("DOMContentLoaded", function() {
  // Global değişkenler
  window.elementCounter = 0;
  window.selectedElement = null;
  window.formCanvas = document.getElementById("form-canvas");
  window.emptyCanvas = document.getElementById("empty-canvas");
  window.elementPalette = document.getElementById("element-palette");
  window.propertiesPanel = document.getElementById("properties-panel");
  window.undoStack = [];
  window.redoStack = [];
  window.canvasLoading = document.getElementById("canvas-loading");
  
  // Türkçe karakterleri İngilizce karakterlere dönüştürme fonksiyonu
  window.slugifyTurkish = function(text) {
    if (!text) return '';
    
    // Türkçe karakter çevrimi
    const turkishChars = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'i': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 
                          'Ç': 'C', 'Ğ': 'G', 'I': 'I', 'İ': 'I', 'Ö': 'O', 'Ş': 'S', 'Ü': 'U' };
    
    // Türkçe karakterleri değiştir
    let slug = text.replace(/[çğıiöşüÇĞIİÖŞÜ]/g, function(char) {
      return turkishChars[char] || char;
    });
    
    // Diğer özel karakterleri ve boşlukları alt çizgi ile değiştir
    slug = slug.toLowerCase()
              .replace(/[^a-z0-9_]+/g, '_')  // Harfler, rakamlar ve alt çizgi hariç tüm karakterleri alt çizgiye çevir
              .replace(/^_+|_+$/g, '')       // Baştaki ve sondaki alt çizgileri temizle
              .replace(/_+/g, '_');          // Ardışık alt çizgileri tek alt çizgiye indir
    
    // Rakamla başlayamaz, kontrolü
    if (/^[0-9]/.test(slug)) {
      slug = 'a_' + slug;  // Rakamla başlıyorsa başına 'a_' ekle
    }
    
    return slug;
  };

  console.log("Form Builder başlatılıyor...");
  
  // Loading gösterimini başlat
  if (window.canvasLoading) {
    window.canvasLoading.style.display = "flex";
  }
  
  // Form yükleme fonksiyonu
  const loadSavedForm = function() {
    // Grup ID'sini al
    const groupId = document.getElementById('group-id')?.value;
    
    // Eğer form zaten blade dosyasından yüklendiyse tekrar yükleme
    if (window.formLoadedFromBlade) {
      console.log("Form zaten blade dosyasından yüklenmiş, tekrar yüklenmeyecek.");
      
      // Loading animasyonunu gizle
      setTimeout(() => {
        if (window.canvasLoading) {
          window.canvasLoading.style.opacity = "0";
          setTimeout(() => {
            window.canvasLoading.style.display = "none";
          }, 300);
        }
      }, 500);
      
      return;
    }
    
    if (groupId) {
      console.log("Form yapısı yükleniyor, Group ID:", groupId);
      
      // Kayıtlı form yapısını yükle
      fetch(`/admin/settingmanagement/form-builder/${groupId}/load`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.layout) {
          console.log('Form yapısı yükleniyor:', data.layout);
          if (typeof window.loadFormFromJSON === 'function') {
            window.loadFormFromJSON(data.layout);
          } else {
            console.error('loadFormFromJSON fonksiyonu bulunamadı');
          }
        }
        
        // Loading animasyonunu gizle
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
        console.error('Form yükleme hatası:', error);
        
        // Hata durumunda da loading'i gizle
        if (window.canvasLoading) {
          window.canvasLoading.style.display = "none";
        }
      });
      
      // Ayarları yükle
      fetch(`/admin/settingmanagement/api/settings?group=${groupId}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(response => response.json())
      .then(settings => {
        console.log('Ayarlar yüklendi:', settings);
        window.availableSettings = settings;
        
        // Setting dropdown'larını doldur
        if (typeof window.populateSettingDropdowns === 'function') {
          window.populateSettingDropdowns(groupId);
        }
      })
      .catch(error => {
        console.error('Ayarlar yüklenirken hata:', error);
      });
    } else {
      // Grup ID yoksa loading'i gizle
      if (window.canvasLoading) {
        window.canvasLoading.style.display = "none";
      }
    }
  };

  // Kaydet butonu
  const saveBtn = document.getElementById("save-btn");
  if (saveBtn) {
    saveBtn.addEventListener("click", function() {
      if (!window.getFormJSON) return;
      
      const formData = window.getFormJSON();
      console.log("Form Verisi:", formData);
      
      // Form verisi hazır, şimdi Livewire'a gönder
      const groupId = document.getElementById('group-id').value;
      if (groupId && typeof Livewire !== 'undefined') {
        Livewire.dispatch('save-form-layout', {
          groupId: groupId, 
          formData: formData
        });
      } else {
        // Klasik form gönderimi için
        const layoutDataInput = document.getElementById('layout-data');
        if (layoutDataInput) {
          layoutDataInput.value = JSON.stringify(formData);
          const saveForm = document.getElementById('save-form');
          if (saveForm) {
            saveForm.submit();
          }
        }
      }
    });
  }
  
  // Sayfa yüklendikten sonra form yükle
  setTimeout(() => {
    // Form yükleme
    loadSavedForm();
    
    // Livewire event listener'larını ekle (Livewire varsa)
    if (typeof window.Livewire !== 'undefined') {
      // Livewire kayıt işlemi tamamlandıktan sonra
      document.addEventListener('livewire:initialized', () => {
        Livewire.on('formSaved', (message) => {
          // Kayıt başarılı mesajı
          alert(message || "Form yapısı başarıyla kaydedildi!");
        });
      });
    }
  }, 500);
});