# 📖 API Documentation

## 🌐 Base URL
```
https://laravel.test/api/v1/
```

## 🔑 Authentication
Tüm korumalı endpoint'ler için header gerekli:
```
Authorization: Bearer {your_token}
Content-Type: application/json
```

---

## 📋 API Endpoint'leri

### 🔐 Authentication

#### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
    "email": "nurullah@nurullah.net",
    "password": "test"
}
```

**Response (Success):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Nurullah",
        "email": "nurullah@nurullah.net"
    },
    "token": "1|abcdef123456..."
}
```

**Response (Error):**
```json
{
    "message": "Invalid credentials"
}
```

#### Register
```http
POST /auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

#### Get User Info
```http
GET /auth/me
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "Nurullah",
        "email": "nurullah@nurullah.net",
        "avatar": null,
        "phone": null,
        "bio": null,
        "is_active": true
    }
}
```

#### Logout
```http
POST /auth/logout
```

---

### 👤 User Profile

#### Get Profile
```http
GET /profile
```

#### Update Profile
```http
PUT /profile
```

**Request Body:**
```json
{
    "name": "New Name",
    "email": "new@email.com",
    "phone": "+90 555 123 4567",
    "bio": "My bio"
}
```

#### Change Password
```http
POST /profile/change-password
```

**Request Body:**
```json
{
    "current_password": "oldpass",
    "new_password": "newpass123",
    "new_password_confirmation": "newpass123"
}
```

#### Upload Avatar
```http
POST /profile/avatar
```

**Request Body (Form Data):**
```
avatar: [file]
```

#### Delete Avatar
```http
DELETE /profile/avatar
```

---

### 🎫 Token Management

#### List Tokens
```http
GET /tokens
```

**Response:**
```json
{
    "message": "Tokens retrieved successfully",
    "tokens": [
        {
            "id": 1,
            "name": "mobile-app",
            "last_used_at": "2025-07-11 10:30:00",
            "created_at": "2025-07-11 09:00:00",
            "is_current": true
        }
    ]
}
```

#### Refresh Token
```http
POST /tokens/refresh
```

#### Revoke Token
```http
DELETE /tokens/{token_id}
```

#### Revoke All Other Tokens
```http
DELETE /tokens
```

#### Get Current Token Info
```http
GET /tokens/current
```

#### Validate Token
```http
GET /tokens/validate
```

---

### 🏢 Tenant Info

#### Get Current Tenant
```http
GET /tenant
```

**Response:**
```json
{
    "message": "Current tenant retrieved successfully",
    "tenant": {
        "id": 1,
        "name": "My Company",
        "is_active": true,
        "plan": "premium",
        "created_at": "2025-01-01 00:00:00"
    }
}
```

#### Get Tenant Details
```http
GET /tenant/details
```

---

## 📊 Pagination & Filtering

### Query Parameters
```
?page=1                    - Sayfa numarası
?per_page=15              - Sayfa başına kayıt (max 100)
?search=keyword           - Arama terimi
?sort_by=created_at       - Sıralama alanı
?sort_direction=desc      - Sıralama yönü (asc/desc)
```

### Filter Parameters
```
?is_active=true           - Boolean filtre
?created_at[from]=2025-01-01  - Tarih aralığı başlangıç
?created_at[to]=2025-12-31    - Tarih aralığı bitiş
?status=active            - Exact match filtre
?category=1,2,3           - IN filtre (virgülle ayrılmış)
```

### Paginated Response Format
```json
{
    "success": true,
    "message": "Data retrieved successfully",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7,
        "has_more_pages": true,
        "from": 1,
        "to": 15,
        "links": {
            "first": "https://laravel.test/api/v1/endpoint?page=1",
            "last": "https://laravel.test/api/v1/endpoint?page=7",
            "prev": null,
            "next": "https://laravel.test/api/v1/endpoint?page=2"
        }
    },
    "filters": {
        "search": "keyword",
        "sort_by": "created_at",
        "sort_direction": "desc",
        "per_page": 15
    }
}
```

---

## 🚨 Error Handling

### Standard Error Response
```json
{
    "success": false,
    "message": "Error message"
}
```

### Validation Error Response
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["Email is required"],
        "password": ["Password must be at least 8 characters"]
    }
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 🔧 Development Notes

### Required Headers
```
Content-Type: application/json
Authorization: Bearer {token}
```

### Test Credentials
```
Email: nurullah@nurullah.net
Password: test
```

### Domain-based Tenancy
- Her tenant'ın kendi domain'i var
- API çağrıları tenant domain'ine yapılmalı
- Tenant bilgisi otomatik context'te

---

*Son güncelleme: 11.07.2025*