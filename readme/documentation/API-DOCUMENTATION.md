# 📚 API DOCUMENTATION - LARAVEL CMS

## 🔗 **API Endpoints**

### **Authentication APIs**
```http
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
GET  /api/auth/me
```

### **AI Content Generation APIs**
```http
POST /api/ai/content/generate
GET  /api/ai/content/jobs/{id}
GET  /api/ai/content/jobs/{id}/status
POST /api/ai/content/jobs/{id}/cancel
```

### **Page Management APIs**
```http
GET    /api/pages
POST   /api/pages
GET    /api/pages/{id}
PUT    /api/pages/{id}
DELETE /api/pages/{id}
POST   /api/pages/{id}/publish
```

### **Translation APIs**
```http
POST /api/translate/content
GET  /api/translate/languages
POST /api/translate/batch
GET  /api/translate/progress/{sessionId}
```

---

## 🔧 **API Usage Examples**

### **AI Content Generation**
```javascript
// JavaScript Example
const response = await fetch('/api/ai/content/generate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token,
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        content_type: 'page',
        content_id: 123,
        target_language: 'en',
        source_language: 'tr',
        prompt_type: 'translation',
        model: 'claude-3-5-sonnet-20241022'
    })
});

const job = await response.json();
console.log('Job ID:', job.data.job_id);
```

### **Page API Usage**
```php
// PHP Example
$client = new GuzzleHttp\Client();

$response = $client->post('http://laravel.test/api/pages', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'title' => [
            'tr' => 'Yeni Sayfa',
            'en' => 'New Page'
        ],
        'slug' => [
            'tr' => 'yeni-sayfa',
            'en' => 'new-page'
        ],
        'body' => [
            'tr' => 'Türkçe içerik...',
            'en' => 'English content...'
        ],
        'is_active' => true
    ]
]);

$page = json_decode($response->getBody(), true);
```

---

## 🚀 **Response Formats**

### **Success Response**
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        "id": 123,
        "title": "Example Title",
        "created_at": "2024-01-01T00:00:00Z"
    },
    "meta": {
        "total": 100,
        "page": 1,
        "per_page": 10
    }
}
```

### **Error Response**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["Title field is required"],
        "email": ["Email format is invalid"]
    },
    "error_code": "VALIDATION_ERROR"
}
```

---

## 🔐 **Authentication**

### **Bearer Token Authentication**
```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### **CSRF Protection**
```javascript
// Include CSRF token in AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

---

## 📊 **Rate Limiting**

- **General APIs**: 60 requests/minute
- **AI APIs**: 10 requests/minute
- **Bulk Operations**: 5 requests/minute

### **Rate Limit Headers**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

---

## 🔍 **Error Codes**

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `UNAUTHORIZED` | Authentication required |
| `FORBIDDEN` | Insufficient permissions |
| `NOT_FOUND` | Resource not found |
| `RATE_LIMIT_EXCEEDED` | Too many requests |
| `AI_QUOTA_EXCEEDED` | AI usage limit reached |
| `TENANT_SUSPENDED` | Tenant account suspended |

---

## 🧪 **Testing APIs**

### **Postman Collection**
```bash
# Import collection (if available)
curl -o laravel-cms-api.postman_collection.json \
  http://laravel.test/api/documentation/postman
```

### **Health Check**
```http
GET /api/health
```

**Response:**
```json
{
    "status": "healthy",
    "services": {
        "database": "up",
        "redis": "up",
        "queue": "up"
    },
    "version": "1.0.0"
}
```

---

## 📝 **Changelog**

### **v1.0.0** (2024-09-18)
- ✅ AI Content Generation APIs
- ✅ Page Management APIs
- ✅ Translation APIs
- ✅ Authentication APIs
- ✅ Rate limiting implementation

---

## 🤝 **Support**

- **Email**: nurullah@nurullah.net
- **Documentation**: `/readme/` directory
- **Issues**: Create GitHub issue for bugs