# 🛒 SHOP MODÜLÜ - HIZLI BAŞLANGIÇ PROMPTU

**DETAYLI PROMPT**: `/Users/nurullah/Desktop/cms/laravel/AI_PROMPT.md` dosyasını oku!
**ANALİZ RAPORU**: `/Users/nurullah/Desktop/cms/laravel/claudeguncel.md` dosyasını oku!

---

## 🚨 KRİTİK KURALLAR (BAŞLAMADAN MUTLAKA OKU!)

### ❌ SİLİNECEK DOSYALAR

```bash
# Admin Controller'ları SİL (Livewire kullanılacak)
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Controllers/Admin/
  → Tüm *Controller.php dosyalarını SİL
```

### ✅ KOPYALANACAK PATTERN DOSYALAR (Master Referanslar)

```bash
# Livewire Components (AYNEN KOPYALA!)
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/
  - PortfolioComponent.php → ShopProductComponent.php
  - PortfolioManageComponent.php → ShopProductManageComponent.php
  - PortfolioCategoryComponent.php → ShopCategoryComponent.php

# Blade Views (UI PATTERN - AYNEN KOPYALA!)
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/
  - portfolio-component.blade.php → product-component.blade.php
  - portfolio-manage-component.blade.php → product-manage-component.blade.php
  - category-component.blade.php → category-component.blade.php

# Models (PATTERN KOPYALA)
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Models/
  - Portfolio.php → ShopProduct.php pattern
  - PortfolioCategory.php → ShopCategory.php pattern
```

---

## 🎯 GÖREV SIRASI

### 1. KONTROL & TEMİZLİK
- [ ] Admin Controller'ları SİL (varsa)
- [ ] Phase-1 migration'ları oku

### 2. CORE MODELS (24 model)
- [ ] ShopProductVariant (EN KRİTİK!)
- [ ] ShopAttribute, ShopProductAttribute
- [ ] ShopCart, ShopCartItem
- [ ] ShopOrder, ShopOrderItem, ShopOrderAddress
- [ ] ShopPayment, ShopPaymentMethod

### 3. ADMIN PANEL (Livewire)
- [ ] ShopProductComponent (Portfolio AYNEN)
- [ ] ShopProductManageComponent (Portfolio AYNEN)
- [ ] ShopCategoryComponent (Portfolio AYNEN)

### 4. FRONTEND
- [ ] ShopController (Frontend)
- [ ] Views (Alpine.js + Tailwind)

---

## 🚨 UI/UX KURALLARI

### ✅ DOĞRU
- SortableJS drag-drop
- Toggle button (liste)
- Pretty checkbox (form)

### ❌ YANLIŞ
- Manuel sıralama
- Custom UI
- Controller kullanımı

---

**Hazır mısın? Başla!** 🚀
