# Kullanıcı Listesi Güncelleme - Plan

**Tarih:** 2025-11-24 19:53
**Durum:** Plan
**Öncelik:** Orta

---

## Mevcut Durum

### Kullanılan Dosyalar
- `Modules/UserManagement/app/Http/Livewire/UserComponent.php`
- `Modules/UserManagement/resources/views/livewire/user-component.blade.php`

### Mevcut Özellikler
- Grid/List görünüm (viewType)
- Arama (search)
- Rol filtresi (roleFilter)
- Durum filtresi (statusFilter)
- Sıralama (sortBy)
- Toplu işlemler (bulk actions)
- Pagination

### Mevcut Kolonlar
- Avatar
- İsim
- Email
- Roller
- Durum (is_active)
- İşlemler

---

## Eklenecek Özellikler

### 1. Yeni Kolonlar

#### A. Abonelik Durumu
- **Kolon:** Subscription Status
- **Veri:** `$user->subscription`
- **Gösterim:**
  - Aktif abonelik → Yeşil badge "Premium" / "Basic"
  - Trial → Sarı badge "Deneme (X gün kaldı)"
  - Yok → Gri badge "Ücretsiz"

#### B. Onay Durumu
- **Kolon:** Approval Status
- **Veri:** `$user->is_approved`
- **Gösterim:**
  - Onaylı → Yeşil ✓
  - Bekliyor → Sarı ⏳

#### C. Cihaz Sayısı
- **Kolon:** Devices
- **Veri:** `$user->sessions()->count()`
- **Gösterim:**
  - "2/5" formatında
  - Limit dolmuşsa kırmızı

#### D. Kurumsal Hesap
- **Kolon:** Corporate
- **Veri:** `MuzibuCorporateAccount::getCorporateForUser($user->id)`
- **Gösterim:**
  - Kurum sahibi → "🏢 Şirket Adı"
  - Üye → "👤 Şirket Adına bağlı"
  - Yok → "-"

### 2. Yeni Filtreler

#### A. Abonelik Filtresi
- Dropdown: Tümü / Aktif / Deneme / Ücretsiz

#### B. Onay Filtresi
- Dropdown: Tümü / Onaylı / Bekliyor

#### C. Kurumsal Filtre
- Dropdown: Tümü / Kurum Sahibi / Üye / Bireysel

### 3. Yeni Toplu İşlemler

- Toplu onay (Bulk Approve)
- Toplu ret (Bulk Reject)

---

## Yaklaşım

### Adım 1: UserComponent.php Güncelleme

**Eklenecek Property'ler:**
```php
public $subscriptionFilter = '';
public $approvalFilter = '';
public $corporateFilter = '';
```

**Eklenecek Metodlar:**
- `updatedSubscriptionFilter()`
- `updatedApprovalFilter()`
- `updatedCorporateFilter()`
- `bulkApprove()`
- `bulkReject()`

**render() Güncelleme:**
- Subscription filtresi ekle
- Onay filtresi ekle
- Kurumsal filtre ekle
- Eager loading: roles, subscription.plan, sessions

### Adım 2: Blade View Güncelleme

**Filtre Alanları:**
- 3 yeni dropdown ekle

**Tablo Kolonları:**
- Abonelik kolonu
- Onay kolonu
- Cihaz kolonu
- Kurumsal kolonu

---

## Teknik Notlar

### Eager Loading
```php
$query = User::with([
    'roles',
    'subscription.plan',
    'sessions'
]);
```

### Performance
- Index'leri kontrol et (is_approved, is_active)
- Kurumsal veri için helper metod kullan

### Test Edilecekler
- Filtrelerin doğru çalışması
- Pagination
- Sorting
- Bulk actions
- Mobile responsive

---

## Beklenen Sonuç

Kullanıcı listesinde:
- ✅ Abonelik durumu görünecek
- ✅ Onay durumu görünecek
- ✅ Aktif cihaz sayısı görünecek
- ✅ Kurumsal hesap bilgisi görünecek
- ✅ 3 yeni filtre çalışacak
- ✅ Toplu onay/ret işlemleri yapılabilecek

---

**NOT:** Taslak HTML önce hazırlanacak, onay sonrası kod yazılacak!
