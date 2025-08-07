# 🚀 AI Module API V2 Documentation

## 📋 Table of Contents
- [Overview](#overview)
- [Authentication](#authentication)
- [Rate Limiting](#rate-limiting)
- [API Endpoints](#api-endpoints)
- [Error Handling](#error-handling)
- [Code Examples](#code-examples)
- [Testing](#testing)
- [Swagger Documentation](#swagger-documentation)

## Overview

The AI Module API V2 provides programmatic access to AI features including:
- 💬 Public and authenticated chat
- 🎯 Feature-specific AI processing
- 💰 Credit management system
- 📊 Usage analytics
- 🔒 Rate-limited public access

### Base URL
```
Production: https://api.turkbilbee.com/api/ai/v1
Staging: https://staging.turkbilbee.com/api/ai/v1
Development: http://localhost:8000/api/ai/v1
```

## Authentication

### Public Access
Public endpoints don't require authentication but are rate-limited:
- `/chat` - Public chat access
- `/feature/{slug}` - Public feature access
- `/features/public` - List public features
- `/status` - System status

### Authenticated Access
Authenticated endpoints require a Bearer token (Laravel Sanctum):

```http
Authorization: Bearer YOUR_TOKEN_HERE
```

Protected endpoints:
- `/chat/user` - Authenticated user chat
- `/credits/balance` - Credit balance and usage

### Getting an Auth Token

```php
// Login endpoint (example)
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password"
}

// Response
{
    "token": "1|abc123...",
    "user": {...}
}
```

## Rate Limiting

### Public Endpoints
| Endpoint | Limit | Window |
|----------|-------|--------|
| `/chat` | 10 requests | 1 hour |
| `/feature/{slug}` | 5 requests | 1 hour |
| `/features/public` | 60 requests | 1 minute |
| `/status` | 120 requests | 1 minute |

### Rate Limit Headers
```http
X-RateLimit-Limit: 10
X-RateLimit-Remaining: 7
X-RateLimit-Reset: 1704786400
```

### Rate Limit Error Response
```json
{
    "success": false,
    "error": "Rate limit exceeded",
    "retry_after": 3600
}
```

## API Endpoints

### 1. System Status
```http
GET /status
```

**Response:**
```json
{
    "success": true,
    "data": {
        "status": "operational",
        "version": "2.0",
        "features_available": true,
        "public_access": true,
        "timestamp": "2025-01-08T10:30:00Z"
    }
}
```

### 2. List Public Features
```http
GET /features/public
```

**Response:**
```json
{
    "success": true,
    "data": {
        "features": [
            {
                "slug": "translation",
                "name": "Translation Assistant",
                "description": "Translate text between multiple languages",
                "icon": "fas fa-language",
                "category": "language"
            }
        ],
        "total": 15
    }
}
```

### 3. Public Chat
```http
POST /chat
```

**Request Body:**
```json
{
    "message": "What is artificial intelligence?",
    "feature": "technical_writer",  // Optional
    "context": {                    // Optional
        "session_id": "abc123"
    }
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Artificial intelligence is...",
        "feature_used": "technical_writer",
        "remaining_requests": 9,
        "credits_used": 0,
        "response_id": "resp_123"
    }
}
```

### 4. Public Feature Access
```http
POST /feature/{slug}
```

**Parameters:**
- `slug` - Feature identifier (e.g., "translation", "seo_analysis")

**Request Body:**
```json
{
    "input": "Text to process",
    "options": {
        "target_language": "tr",
        "format": "formal"
    }
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "response": "Processed text",
        "feature": {
            "slug": "translation",
            "name": "Translation Assistant",
            "description": "Translate text between languages"
        },
        "formatted_response": {
            "original": "Text to process",
            "translated": "İşlenecek metin"
        },
        "remaining_requests": 4,
        "execution_time": 0.523
    }
}
```

### 5. Authenticated User Chat
```http
POST /chat/user
Authorization: Bearer YOUR_TOKEN
```

**Request Body:**
```json
{
    "message": "Explain quantum computing",
    "feature": "technical_writer",
    "context": {
        "expertise_level": "beginner"
    }
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Quantum computing is a revolutionary...",
        "credits_used": 2,
        "credits_remaining": 98,
        "feature_used": "technical_writer",
        "response_id": "resp_456"
    }
}
```

### 6. Get Credit Balance
```http
GET /credits/balance
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
    "success": true,
    "data": {
        "credits_available": 150,
        "recent_usage": [
            {
                "feature_slug": "translation",
                "credits_used": 5,
                "created_at": "2025-01-08T09:00:00Z"
            }
        ],
        "usage_summary": {
            "last_30_days": 45,
            "most_used_feature": "translation"
        }
    }
}
```

## Error Handling

### Error Response Format
```json
{
    "success": false,
    "error": "Error message here",
    "details": {
        "field": ["Validation error"]
    }
}
```

### HTTP Status Codes
| Code | Meaning |
|------|---------|
| 200 | Success |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 402 | Payment Required - Insufficient credits |
| 404 | Not Found - Feature or resource not found |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |

## Code Examples

### JavaScript (Fetch API)
```javascript
// Public chat
async function publicChat(message) {
    const response = await fetch('http://localhost:8000/api/ai/v1/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            feature: 'general_assistant'
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        console.log('AI Response:', data.data.message);
        console.log('Remaining requests:', data.data.remaining_requests);
    } else {
        console.error('Error:', data.error);
    }
}

// Authenticated chat
async function authenticatedChat(message, token) {
    const response = await fetch('http://localhost:8000/api/ai/v1/chat/user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            message: message
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        console.log('AI Response:', data.data.message);
        console.log('Credits used:', data.data.credits_used);
        console.log('Credits remaining:', data.data.credits_remaining);
    } else {
        if (response.status === 402) {
            console.error('Insufficient credits!');
        } else {
            console.error('Error:', data.error);
        }
    }
}
```

### PHP (Guzzle)
```php
use GuzzleHttp\Client;

class AIApiClient 
{
    private $client;
    private $baseUrl = 'http://localhost:8000/api/ai/v1';
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }
    
    public function publicChat(string $message, ?string $feature = null): array
    {
        try {
            $response = $this->client->post('/chat', [
                'json' => [
                    'message' => $message,
                    'feature' => $feature
                ]
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                throw new \Exception('Rate limit exceeded');
            }
            throw $e;
        }
    }
    
    public function authenticatedChat(string $message, string $token): array
    {
        try {
            $response = $this->client->post('/chat/user', [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ],
                'json' => [
                    'message' => $message
                ]
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 402) {
                throw new \Exception('Insufficient credits');
            }
            throw $e;
        }
    }
    
    public function getCreditBalance(string $token): array
    {
        $response = $this->client->get('/credits/balance', [
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ]
        ]);
        
        return json_decode($response->getBody(), true);
    }
}

// Usage
$client = new AIApiClient();

// Public chat
$response = $client->publicChat('What is AI?');
echo $response['data']['message'];

// Authenticated chat
$token = 'your-auth-token';
$response = $client->authenticatedChat('Explain quantum physics', $token);
echo "Credits used: " . $response['data']['credits_used'];

// Check balance
$balance = $client->getCreditBalance($token);
echo "Credits available: " . $balance['data']['credits_available'];
```

### cURL Examples
```bash
# Public chat
curl -X POST http://localhost:8000/api/ai/v1/chat \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "message": "What is artificial intelligence?"
  }'

# Public feature
curl -X POST http://localhost:8000/api/ai/v1/feature/translation \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "input": "Hello world",
    "options": {
      "target_language": "tr"
    }
  }'

# Authenticated chat
curl -X POST http://localhost:8000/api/ai/v1/chat/user \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "message": "Explain quantum computing"
  }'

# Get credit balance
curl -X GET http://localhost:8000/api/ai/v1/credits/balance \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Testing

### Using Postman
1. Import the OpenAPI specification from `/Modules/AI/docs/openapi.yaml`
2. Set up environment variables:
   - `base_url`: `http://localhost:8000/api/ai/v1`
   - `auth_token`: Your authentication token
3. Use the imported collection to test endpoints

### Using PHPUnit
```bash
# Run API tests
php artisan test --filter=PublicAIControllerTest

# Run with coverage
php artisan test --coverage --filter=Api
```

### Test Examples
```php
// tests/Feature/Api/PublicAIControllerTest.php
public function test_public_chat_works()
{
    $response = $this->postJson('/api/ai/v1/chat', [
        'message' => 'Test message'
    ]);
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'message',
                'remaining_requests',
                'credits_used'
            ]
        ]);
}

public function test_rate_limit_enforced()
{
    // Make 10 requests (the limit)
    for ($i = 0; $i < 10; $i++) {
        $this->postJson('/api/ai/v1/chat', [
            'message' => 'Test'
        ])->assertStatus(200);
    }
    
    // 11th request should be rate limited
    $response = $this->postJson('/api/ai/v1/chat', [
        'message' => 'Test'
    ]);
    
    $response->assertStatus(429)
        ->assertJson([
            'success' => false,
            'error' => 'Rate limit exceeded'
        ]);
}
```

## Swagger Documentation

### Viewing the Documentation
1. Open `/Modules/AI/docs/swagger-ui.html` in a browser
2. Or serve it via Laravel:
   ```php
   Route::get('/api/docs', function() {
       return view('ai::docs.swagger-ui');
   });
   ```

### OpenAPI Specification
The complete OpenAPI 3.0 specification is available at:
- `/Modules/AI/docs/openapi.yaml`

You can import this into:
- Postman
- Insomnia
- Swagger Editor
- API Gateway tools

### Generating Client SDKs
Use the OpenAPI spec to generate client libraries:

```bash
# Generate JavaScript client
npx @openapitools/openapi-generator-cli generate \
  -i Modules/AI/docs/openapi.yaml \
  -g javascript \
  -o ai-client-js

# Generate PHP client
npx @openapitools/openapi-generator-cli generate \
  -i Modules/AI/docs/openapi.yaml \
  -g php \
  -o ai-client-php

# Generate Python client
npx @openapitools/openapi-generator-cli generate \
  -i Modules/AI/docs/openapi.yaml \
  -g python \
  -o ai-client-python
```

## Best Practices

### 1. Handle Rate Limits Gracefully
```javascript
async function makeRequest(url, options, retries = 3) {
    try {
        const response = await fetch(url, options);
        
        if (response.status === 429 && retries > 0) {
            const retryAfter = response.headers.get('Retry-After') || 60;
            console.log(`Rate limited. Retrying after ${retryAfter} seconds...`);
            await new Promise(resolve => setTimeout(resolve, retryAfter * 1000));
            return makeRequest(url, options, retries - 1);
        }
        
        return response;
    } catch (error) {
        if (retries > 0) {
            await new Promise(resolve => setTimeout(resolve, 5000));
            return makeRequest(url, options, retries - 1);
        }
        throw error;
    }
}
```

### 2. Cache Public Feature List
```javascript
let featuresCache = null;
let cacheExpiry = null;

async function getPublicFeatures() {
    if (featuresCache && cacheExpiry > Date.now()) {
        return featuresCache;
    }
    
    const response = await fetch('/api/ai/v1/features/public');
    const data = await response.json();
    
    featuresCache = data.data.features;
    cacheExpiry = Date.now() + (6 * 60 * 60 * 1000); // 6 hours
    
    return featuresCache;
}
```

### 3. Monitor Credit Usage
```javascript
class CreditManager {
    constructor(token) {
        this.token = token;
        this.balance = null;
        this.lowCreditThreshold = 10;
    }
    
    async checkBalance() {
        const response = await fetch('/api/ai/v1/credits/balance', {
            headers: {
                'Authorization': `Bearer ${this.token}`
            }
        });
        
        const data = await response.json();
        this.balance = data.data.credits_available;
        
        if (this.balance < this.lowCreditThreshold) {
            this.onLowCredits(this.balance);
        }
        
        return this.balance;
    }
    
    onLowCredits(balance) {
        console.warn(`Low credit warning: ${balance} credits remaining`);
        // Notify user or trigger purchase flow
    }
}
```

## Support

For API support and questions:
- Email: support@turkbilbee.com
- Documentation: https://docs.turkbilbee.com/api/ai
- Status Page: https://status.turkbilbee.com

## Changelog

### Version 2.0.0 (2025-01-08)
- Initial API V2 release
- Public and authenticated endpoints
- Credit system integration
- Rate limiting implementation
- Comprehensive error handling
- OpenAPI documentation