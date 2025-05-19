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

  console.log("Form Builder başlatılıyor...");
  
  // Form yükleme fonksiyonu
  const loadSavedForm = function() {
    // Grup ID'sini al
    const groupId = document.getElementById('group-id')?.value;
    
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
      })
      .catch(error => {
        console.error('Form yükleme hatası:', error);
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