# Laravel 11 + Livewire 3.5 Güncelleme ve Modül Düzeni

Laravel 11 ve Livewire 3.5 kullanan bir sistem için, mevcut **component** ve **trait** dosyalarını güncelleyeceğiz.  
Sistemimiz **Nwidart Module** yapısını ve **Stancl Tenancy** çoklu kiracı ve çoklu veritabanlı sistemini kullanıyor.

Güncellemeleri yaparken şu kurallara uyacağız

## **Genel Kurallar**

-   Yeni dosya oluşturman gerekiyorsa dosya adı ve dizinini de söyleyip benden izin alacaksın.
-   İzin verdiğim dizin dışında başka bir dizinden dosya düzenlemesi yapacaksan bana söyleyip benden izin alacaksın.

-   [x] **Modül Kopyalama Mantığı:** Yeni bir modül oluştururken mevcut bir modülü kopyalayacağız.  
         - [x] Yeni modülün veritabanı adı, **Page** kelimesinin geçtiği yerler ve diğer modül adlarına özel kısımlar topluca değiştirilerek rahatlıkla oluşturulacak. - [x] Gerekirse bunlar değişken olarak listelenecek.
-   [x] **Kod Yapısı & Hiyerarşi:** Livewire component dosyalarında net bir düzen olacak:  
         - [x] **Property'ler en üstte** olacak  
         - [x] **Mount metodu hemen ardından** gelecek  
         - [x] **İş mantığı metodları ortada** yer alacak  
         - [x] **Render metodu en sonda** bulunacak
-   [x] **Gereksiz Tekrarları Önleme:**  
         - [x] Field'ları **bir kere** yazılacak  
         - [x] Kullanılmayan veya null olabilen field'lar eklenmeyecek  
         - [x] **Validation** kuralları sadece ilgili alanlara yazılacak
-   [x] **Validasyon Yapısı:**  
         - [x] Merkezi bir validasyon yapısı oluşturulacak  
         - [x] Eğer iş yükünü azaltıp süreci hızlandırırsa kullanılacak  
         - [x] Bu yapı **deneme amaçlı eklenip test edilecek**
-   [x] **Field Yönetimi:**  
         - [x] **title, slug, created_at, updated_at, is_active** gibi alanlar çoğu modülde sabit olacak  
         - [x] **modüle göre değişkenlik gösterebilecek** field'lar için **dinamik bir yapı** kurulacak  
         - [x] Yeni bir modül oluştururken sadece yeni alan adlarını belirleyerek sistem çalışır hale getirilebilecek
-   [x] **Livewire 3.5 Özellikleri:**  
         - [x] **#[Computed]**, **#[Modelable]**, **#[Layout]**, **#[URL]** gibi yeni Livewire özellikleri kullanılacak
-   [x] **Trait Kullanımı:**  
         - [x] **Toplu silme işlemleri ve inline edit işlemleri için mevcut trait'ler korunacak**  
         - [x] Yeni özellikler eklenecekse **bu trait'lere dahil edilecek**
-   [x] **Tutarlılık & SOLID Prensipleri:**  
         - [x] Component ve view dosyaları arasında tutarlı bir yapı olacak  
         - [x] Kod tekrarı olmayacak, DRY prensibine uyulacak  
         - [x] Her component tek bir sorumluluk alacak  
         - [x] Bağımlılıklar minimize edilecek
-   [x] **Önbellekleme:**  
         - [x] Tüm sorgular önbelleğe alınacak  
         - [x] Önbellek anahtarları tutarlı olacak  
         - [x] Veri değişikliğinde önbellek temizlenecek

## **Yapılacak Değişiklikler**

### **1. Page Component İyileştirmeleri**

-   [x] Property'ler en üste taşınacak ve gereksiz yorum satırları kaldırılacak
-   [x] `page_id` kullanımı tutarlı hale getirilecek
-   [x] Önbellekleme tüm metodlara eklenecek:

    ```php
    $cacheKey = sprintf('pages.%s.%s.%s.%d', $this->search, $this->sortField, $this->sortDirection, $this->perPage);
    return Cache::tags(['pages'])->remember($cacheKey, now()->addMinutes(30), function () {
        // Sorgu
    });
    ```

-   [x] Veri değişikliklerinde önbellek temizlenecek:
    ```php
    Cache::tags(['pages'])->flush();
    ```

### **4. Önbellekleme Stratejisi**

-   [x] Tüm sorgular için önbellekleme eklenecek
-   [x] Önbellek anahtarları optimize edilecek
-   [x] Veri değişikliklerinde otomatik önbellek temizlenecek

### **5. Livewire 3.5 Özellikleri**

-   [x] #[Computed] ile hesaplanan özellikler eklenecek
-   [x] #[Modelable] ile model bağlantıları kurulacak
-   [x] #[Layout] ile sayfa düzeni ayarlanacak
-   [x] #[URL] ile URL parametreleri yönetilecek
-   [x] #[Rule] ile validasyon kuralları tanımlanacak
-   [x] #[On] ile event dinleyicileri eklenecek

### **6. SOLID Prensipleri**

-   [x] Her trait tek bir sorumluluk alacak şekilde düzenlenecek
-   [x] Kod tekrarı önlenecek
-   [x] Bağımlılıklar minimize edilecek
-   [x] Modüler ve yeniden kullanılabilir yapı kurulacak
