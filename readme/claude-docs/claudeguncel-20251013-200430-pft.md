# Shop Product Field Templates Sistemi - Plan

**Tarih:** 2025-10-13 20:04:30
**ID:** pft (Product Field Templates)

---

## Genel Bakış
Shop modülüne ürün özel alanlarını (custom JSON fields) template olarak kaydetmeye ve tekrar kullanmaya yarayan bir sistem ekliyoruz.

---

## Yapılacak İşlemler

### 1. Migration Oluştur
- [ ] `shop_product_field_templates` tablosunu oluştur
  - Kolonlar: template_id (PK), name, description, fields (JSON), is_active, sort_order, timestamps

### 2. Model Oluştur
- [ ] `ShopProductFieldTemplate.php` modelini oluştur
  - Namespace: `Modules\Shop\App\Models`
  - Fillable: name, description, fields, is_active, sort_order
  - Cast: fields → array
  - Primary key: template_id

### 3. Controller Oluştur
- [ ] `ShopFieldTemplateController.php` oluştur
  - Namespace: `Modules\Shop\App\Http\Controllers\Admin`
  - Methodlar: index, create, store, edit, update, destroy, toggleActive

### 4. View Dosyalarını Oluştur
- [ ] Liste sayfası: `resources/views/admin/field-templates/index.blade.php`
- [ ] Create sayfası: `resources/views/admin/field-templates/create.blade.php`
- [ ] Edit sayfası: `resources/views/admin/field-templates/edit.blade.php`
- [ ] Form partial: `resources/views/admin/field-templates/_form.blade.php`
- [ ] Alpine.js ile dinamik field builder ekle

### 5. Routes Ekle
- [ ] `routes/admin.php` dosyasına field-templates route'larını ekle
- [ ] Resource route + toggleActive route

### 6. Navigation (İsteğe Bağlı)
- [ ] Sidebar'a eklemek için menu dosyasını kontrol et

### 7. Dil Dosyaları
- [ ] `lang/tr/admin.php` güncelle (field template label'ları)

---

## Teknik Detaylar

### JSON Field Yapısı
```json
[
  {
    "name": "author",
    "type": "input"
  },
  {
    "name": "description",
    "type": "textarea"
  },
  {
    "name": "is_bestseller",
    "type": "checkbox"
  }
]
```

### Field Types
1. **input** - Text input
2. **textarea** - Textarea
3. **checkbox** - Boolean checkbox

### Validation Rules
- name: required, string, max:191
- fields: required, array, min:1
- fields.*.name: required, string
- fields.*.type: required, in:input,textarea,checkbox

---

## UI Standartları
- **Admin Panel**: Tabler.io + Bootstrap
- **Icons**: Tabler Icons (`ti ti-*`)
- **Dynamic UI**: Alpine.js (Vue/React yok)
- **Notifications**: Toast messages

---

## Notlar
- VERİTABANINA MANUEL İŞLEM YAPMA!
- Standard Controller kullan (Livewire değil, çünkü basit CRUD)
- helper.blade.php ekle sayfaların tepesine
- Tabler.io DataTable component kullan liste sayfasında

---

## Review (İşlem Sonrası Doldurulacak)
- [ ] Tüm dosyalar oluşturuldu
- [ ] Migration başarıyla çalıştı
- [ ] CRUD işlemleri test edildi
- [ ] UI/UX kontrol edildi
- [ ] Dil dosyaları güncellendi
