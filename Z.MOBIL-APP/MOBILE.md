# 📱 Mobil Uygulama API Dokümantasyonu

## 📋 TODO LİSTESİ

### ✅ Tamamlanan Görevler
- [x] **Authentication System** - Login/Register/Logout/Token Management
- [x] **User Profile Management** - Profil CRUD, Avatar Upload, Şifre Değiştirme
- [x] **Token Management** - Token Refresh, Revoke, Validation
- [x] **Tenant Management** - Tenant Info, Domain-based Routing
- [x] **Pagination & Filtering** - Sayfalama, Arama, Filtreleme Trait'leri
- [x] **Error Handling** - Standardize API Response System
- [x] **Database Migration** - User tablosuna phone, bio, avatar alanları

### 🔄 Bekleyen Görevler
- [ ] **Rate Limiting & Security** - API güvenlik katmanı
- [ ] **Notification Module API** - Push notification sistemi (modül hazır olduğunda)
- [ ] **AI Features API** - AI chat, features, token balance (modüller hazır olduğunda)
- [ ] **File Management API** - Genel file upload/download sistemi
- [ ] **API Documentation** - Swagger/OpenAPI dokumentasyonu
- [ ] **Offline Mode Support** - Cache endpoint'leri ve sync mechanism

---

## 🚀 Mevcut API Endpoint'leri

### 🔐 Authentication
```
POST /api/v1/auth/login          - Giriş yap
POST /api/v1/auth/register       - Kayıt ol
GET  /api/v1/auth/me            - Kullanıcı bilgilerini al
POST /api/v1/auth/logout        - Çıkış yap
```

### 👤 User Profile
```
GET    /api/v1/profile                    - Profil bilgilerini al
PUT    /api/v1/profile                    - Profil güncelle
POST   /api/v1/profile/change-password    - Şifre değiştir
POST   /api/v1/profile/avatar             - Avatar upload
DELETE /api/v1/profile/avatar             - Avatar sil
```

### 🎫 Token Management
```
GET    /api/v1/tokens            - Aktif token'ları listele
POST   /api/v1/tokens/refresh    - Token yenile
DELETE /api/v1/tokens/{id}       - Belirli token'ı iptal et
DELETE /api/v1/tokens            - Diğer tüm token'ları iptal et
GET    /api/v1/tokens/current    - Mevcut token bilgisi
GET    /api/v1/tokens/validate   - Token doğrula
```

### 🏢 Tenant Info
```
GET /api/v1/tenant          - Mevcut tenant bilgisi
GET /api/v1/tenant/details  - Detaylı tenant bilgisi
```

---

## 🛠️ Geliştirici Notları

### 📊 Pagination & Filtering Sistemi
- **PaginationHelper Trait** - Sayfalama, arama, filtreleme, sıralama
- **ApiValidator Trait** - Request validation helper'ları
- **ApiResponse Trait** - Standardize API response formatı

### 🔧 Kullanım Örneği
```php
use App\Traits\PaginationHelper;
use App\Traits\ApiResponse;

class YourController extends Controller
{
    use PaginationHelper, ApiResponse;
    
    public function index(Request $request)
    {
        $query = YourModel::query();
        
        $config = [
            'searchFields' => ['name', 'email'],
            'filterableFields' => [
                'status' => 'exact',
                'created_at' => 'date_range',
                'is_active' => 'boolean',
            ],
            'sortableFields' => ['id', 'name', 'created_at'],
            'defaultSort' => 'created_at',
            'defaultDirection' => 'desc',
            'defaultPerPage' => 15,
        ];

        $paginatedData = $this->applyPaginationHelpers($query, $request, $config);
        
        return response()->json($this->formatPaginatedResponse($paginatedData));
    }
}
```

### 📁 Dosya Yapısı
```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php
│   ├── UserProfileController.php
│   ├── TokenController.php
│   ├── TenantController.php
│   └── ListsController.php (örnek)
├── Traits/
│   ├── ApiResponse.php
│   ├── PaginationHelper.php
│   └── ApiValidator.php
└── Models/
    └── User.php (güncellenmiş)
```

---

## 📱 Mobil Uygulama Entegrasyonu

### 🔑 Authentication Flow
1. **Login** → `POST /api/v1/auth/login`
2. **Token Sakla** → Local storage'a token kaydet
3. **Header Ekle** → `Authorization: Bearer {token}`
4. **Token Refresh** → Token süresi dolmadan önce yenile

### 🌐 Domain-based Tenancy
- Her tenant'ın kendi domain'i var
- API çağrıları ilgili domain'e yapılmalı
- Tenant bilgisi otomatik olarak context'te

### 📊 Pagination Parametreleri
```
?page=1                    - Sayfa numarası
?per_page=15              - Sayfa başına kayıt
?search=keyword           - Arama terimi
?sort_by=created_at       - Sıralama alanı
?sort_direction=desc      - Sıralama yönü
?filter_field=value       - Filtreleme
```

### 🛡️ Error Handling
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["validation error"]
    }
}
```

---

## 🎯 Gelecek Planlar

### 🤖 AI Features (Modüller hazır olduğunda)
- AI chat endpoint'leri
- Feature listesi ve test
- Token balance kontrolü
- Conversation history

### 🔔 Notification System (Modül hazır olduğunda)  
- Push notification gönderme
- Notification history
- Notification settings

### 📁 File Management (Sistem hazır olduğunda)
- Genel file upload
- File download
- Media management
- File validation

---

## 🚦 Test Bilgileri

### 🧪 Test Kullanıcısı
```
Email: nurullah@nurullah.net
Password: test
```

### 📡 Base URL
```
https://laravel.test/api/v1/
```

### 🔧 Headers
```
Content-Type: application/json
Authorization: Bearer {your_token}
```

---

*Son güncelleme: 11.07.2025*
*Geliştirici: Claude AI*