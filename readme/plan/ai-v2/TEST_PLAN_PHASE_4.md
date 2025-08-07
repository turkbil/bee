# 📋 PHASE 4: FRONTEND AI ENTEGRASYONU TEST PLANI

## Test Edilecek Dosyalar

### 1. API Routes
**Lokasyon**: `Modules/AI/routes/api.php`
**Test Özellikleri**:
- Public endpoints
- Rate limiting
- Authentication
- Credit middleware

### 2. AIController API
**Lokasyon**: `Modules/AI/app/Http/Controllers/API/AIController.php`
**Test Özellikleri**:
- publicChat method
- publicFeature method
- userChat method
- creditBalance method

### 3. AI Chat Widget
**Lokasyon**: `Modules/AI/app/Widgets/AIChatWidget.php`
**Test Özellikleri**:
- Widget rendering
- Credit display
- Feature selection
- Message history

### 4. Alpine.js Components
**Lokasyon**: `public/ai-assets/js/ai-chat.js`
**Test Özellikleri**:
- Message sending
- Loading states
- Credit updates
- Error handling

## Test Senaryoları

### Test 1: Public API Access
**Test Endpoint**: `POST /api/ai/v1/chat`
**Kontrol Edilecek**:
- Rate limiting (5 request/dakika)
- Guest credit kontrolü
- Response format
- Error messages

**Test Request**:
```json
{
    "message": "Merhaba, nasılsın?",
    "feature": "chat",
    "session_id": "guest-123"
}
```

### Test 2: Authenticated API
**Test Endpoint**: `POST /api/ai/v1/chat/user`
**Kontrol Edilecek**:
- User authentication
- User credit kontrolü
- History kayıt
- Credit güncelleme

**Headers**:
```
Authorization: Bearer {user_token}
Content-Type: application/json
```

### Test 3: Widget Integration
**Test Sayfası**: `/test-ai-widget`
**Kontrol Edilecek**:
- Widget yüklenmesi
- Chat UI görünümü
- Feature dropdown
- Credit gösterimi

### Test 4: Alpine.js Functionality
**Test Sayfası**: `/ai-chat-demo`
**Kontrol Edilecek**:
- Mesaj gönderme
- Loading animation
- Error toast
- Credit countdown

## Frontend Test HTML

```html
<!-- Test sayfası -->
<div x-data="aiChat()" class="ai-chat-container">
    <div class="credit-display">
        Kalan Kredi: <span x-text="credits"></span>
    </div>
    
    <div class="messages" x-ref="messages">
        <template x-for="msg in messages">
            <div :class="msg.role">
                <span x-text="msg.content"></span>
            </div>
        </template>
    </div>
    
    <div class="input-area">
        <select x-model="selectedFeature">
            <option value="chat">Sohbet</option>
            <option value="blog-yaz">Blog Yaz</option>
            <option value="seo-analiz">SEO Analiz</option>
        </select>
        
        <input x-model="input" @keyup.enter="sendMessage">
        <button @click="sendMessage" :disabled="loading">
            <span x-show="!loading">Gönder</span>
            <span x-show="loading">...</span>
        </button>
    </div>
</div>
```

## Test Komutları

```bash
# API Test
curl -X POST http://laravel.test/api/ai/v1/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"Test","feature":"chat"}'

# Rate Limit Test
for i in {1..10}; do 
  curl -X POST http://laravel.test/api/ai/v1/chat \
    -H "Content-Type: application/json" \
    -d '{"message":"Test '$i'","feature":"chat"}'
done

# Widget Test
php artisan widget:test AIChatWidget
```