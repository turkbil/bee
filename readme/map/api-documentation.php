<?php
$title = "API Documentation - API Dökümantasyonu";
$description = "RESTful API ve GraphQL endpoints için kapsamlı geliştirici rehberi";
$keywords = "API, REST, GraphQL, endpoints, documentation, developer guide";
$canonical = "https://cms.nurullah.com.tr/api-documentation";
$image = "assets/images/api-documentation-hero.jpg";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-purple-600/20"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KPGcgZmlsbD0iIzAwMCIgZmlsbC1vcGFjaXR5PSIwLjAzIj4KPHBhdGggZD0iTTEwIDEwaDEwdjEwSDEweiIvPgo8cGF0aCBkPSJNMzAgMTBoMTB2MTBIMzB6Ii8+CjxwYXRoIGQ9Ik0xMCAzMGgxMHYxMEgxMHoiLz4KPHBhdGggZD0iTTMwIDMwaDEwdjEwSDMweiIvPgo8L2c+CjwvZz4KPC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 container mx-auto px-4 py-20">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-6">
                    <i class="fas fa-code text-2xl text-white"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    API Documentation
                    <span class="block text-xl md:text-2xl font-normal text-indigo-100 mt-2">
                        API Dökümantasyonu
                    </span>
                </h1>
                <p class="text-xl text-indigo-100 max-w-2xl mx-auto mb-8">
                    RESTful API ve GraphQL endpoints için kapsamlı geliştirici rehberi. 
                    Sistemimizi entegre etmek ve özelleştirmek için ihtiyacınız olan tüm teknik bilgiler.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#rest-api" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-medium hover:bg-indigo-50 transition-colors">
                        REST API
                    </a>
                    <a href="#graphql" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white hover:text-indigo-600 transition-colors">
                        GraphQL
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Start Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Hızlı Başlangıç
                        <span class="block text-lg text-indigo-600">Quick Start Guide</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        API'mizle entegrasyona başlamak için gereken temel adımlar ve authentication (kimlik doğrulama) 
                        süreçleri. Bearer token ile secure API access (güvenli API erişimi) sağlayabilirsiniz.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-key text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Authentication
                                    <span class="block text-sm text-gray-500">Kimlik Doğrulama</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            API erişimi için OAuth 2.0 ve JWT token sistemi kullanılır. 
                            Bearer token ile tüm protected endpoints'lere erişebilirsiniz.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Token Alma</h4>
                            <code class="text-sm text-gray-600">
                                POST /api/auth/token<br>
                                Content-Type: application/json<br><br>
                                {<br>
                                &nbsp;&nbsp;"email": "user@example.com",<br>
                                &nbsp;&nbsp;"password": "password"<br>
                                }
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-plug text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Base URL
                                    <span class="block text-sm text-gray-500">Temel URL</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Tüm API istekleri için base URL ve versioning (versiyon yönetimi) sistemi. 
                            Backward compatibility (geriye dönük uyumluluk) garantisi sağlanır.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">API Endpoint</h4>
                            <code class="text-sm text-gray-600">
                                https://api.yoursite.com/v1/<br><br>
                                Headers:<br>
                                Authorization: Bearer {token}<br>
                                Content-Type: application/json<br>
                                Accept: application/json
                            </code>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">
                        İlk API Çağrınızı Yapın
                        <span class="block text-lg text-indigo-600">Make Your First API Call</span>
                    </h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">1. Token Alın</h4>
                            <p class="text-gray-600 text-sm mb-4">
                                Email ve password ile authentication endpoint'ine istek gönderin.
                            </p>
                            <div class="bg-white p-3 rounded border text-xs">
                                <code>curl -X POST https://api.yoursite.com/v1/auth/token</code>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">2. Header Ekleyin</h4>
                            <p class="text-gray-600 text-sm mb-4">
                                Bearer token'ı Authorization header'ına ekleyin.
                            </p>
                            <div class="bg-white p-3 rounded border text-xs">
                                <code>Authorization: Bearer {your_token}</code>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">3. API Çağrısı</h4>
                            <p class="text-gray-600 text-sm mb-4">
                                Herhangi bir protected endpoint'e istek gönderin.
                            </p>
                            <div class="bg-white p-3 rounded border text-xs">
                                <code>GET /api/v1/users/profile</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- REST API Section -->
    <section id="rest-api" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        RESTful API
                        <span class="block text-lg text-indigo-600">REST API Endpoints</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        HTTP methods (GET, POST, PUT, DELETE) ile CRUD operations (oluştur, oku, güncelle, sil) 
                        yapabileceğiniz RESTful endpoint'ler. JSON format'ında data exchange (veri değişimi) desteklenir.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            User Management
                            <span class="block text-sm text-gray-500">Kullanıcı Yönetimi</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            User authentication, profile management ve role-based access control (rol tabanlı erişim kontrolü) 
                            için endpoint'ler.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-lg border-l-4 border-green-500">
                                <h4 class="font-medium text-gray-900 mb-1">GET /api/v1/users</h4>
                                <p class="text-sm text-gray-600">Tüm kullanıcıları listele (pagination destekli)</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-blue-500">
                                <h4 class="font-medium text-gray-900 mb-1">POST /api/v1/users</h4>
                                <p class="text-sm text-gray-600">Yeni kullanıcı oluştur</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-yellow-500">
                                <h4 class="font-medium text-gray-900 mb-1">PUT /api/v1/users/{id}</h4>
                                <p class="text-sm text-gray-600">Kullanıcı bilgilerini güncelle</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-red-500">
                                <h4 class="font-medium text-gray-900 mb-1">DELETE /api/v1/users/{id}</h4>
                                <p class="text-sm text-gray-600">Kullanıcı hesabını sil</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Content Management
                            <span class="block text-sm text-gray-500">İçerik Yönetimi</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Page, post, media ve widget management için kapsamlı endpoint'ler. 
                            Content versioning (içerik versiyon yönetimi) ve draft sistemi desteklenir.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-lg border-l-4 border-green-500">
                                <h4 class="font-medium text-gray-900 mb-1">GET /api/v1/pages</h4>
                                <p class="text-sm text-gray-600">Tüm sayfaları listele (filtering ve sorting)</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-blue-500">
                                <h4 class="font-medium text-gray-900 mb-1">POST /api/v1/pages</h4>
                                <p class="text-sm text-gray-600">Yeni sayfa oluştur</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-purple-500">
                                <h4 class="font-medium text-gray-900 mb-1">GET /api/v1/media</h4>
                                <p class="text-sm text-gray-600">Media library yönetimi</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border-l-4 border-indigo-500">
                                <h4 class="font-medium text-gray-900 mb-1">POST /api/v1/widgets</h4>
                                <p class="text-sm text-gray-600">Widget konfigürasyonu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8">
                        <h3 class="text-2xl font-bold text-white mb-4">
                            API Response Format
                            <span class="block text-lg text-indigo-100">API Yanıt Formatı</span>
                        </h3>
                        <p class="text-indigo-100">
                            Tüm API yanıtları consistent JSON format'ında döner. 
                            Success/error handling ve pagination için standardize edilmiş yapı.
                        </p>
                    </div>
                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Başarılı Yanıt (Success Response)</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <code class="text-sm text-gray-600">
                                        {<br>
                                        &nbsp;&nbsp;"success": true,<br>
                                        &nbsp;&nbsp;"message": "Data retrieved successfully",<br>
                                        &nbsp;&nbsp;"data": {<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;"users": [...],<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;"pagination": {<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"current_page": 1,<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"per_page": 15,<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"total": 150<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                                        &nbsp;&nbsp;}<br>
                                        }
                                    </code>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Hata Yanıtı (Error Response)</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <code class="text-sm text-gray-600">
                                        {<br>
                                        &nbsp;&nbsp;"success": false,<br>
                                        &nbsp;&nbsp;"message": "Validation failed",<br>
                                        &nbsp;&nbsp;"errors": {<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;"email": [<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"The email field is required."<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                                        &nbsp;&nbsp;},<br>
                                        &nbsp;&nbsp;"error_code": "VALIDATION_ERROR"<br>
                                        }
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GraphQL Section -->
    <section id="graphql" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        GraphQL API
                        <span class="block text-lg text-indigo-600">GraphQL Endpoint</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Single endpoint ile flexible queries (esnek sorgular) yapabileceğiniz GraphQL API. 
                        Over-fetching ve under-fetching problemlerini çözen modern API yaklaşımı.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-project-diagram text-pink-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Query Operations
                                    <span class="block text-sm text-gray-500">Sorgu İşlemleri</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            GraphQL queries ile ihtiyacınız olan exact data'yı alabilirsiniz. 
                            Nested relations ve field selection ile optimized data fetching.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Örnek Query</h4>
                            <code class="text-sm text-gray-600">
                                query {<br>
                                &nbsp;&nbsp;users(limit: 10) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;id<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;name<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;email<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;posts {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;title<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;content<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-edit text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Mutations
                                    <span class="block text-sm text-gray-500">Veri Değişiklikleri</span>
                                </h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">
                            GraphQL mutations ile create, update, delete operations yapabilirsiniz. 
                            Transactional support ile data consistency garantisi.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Örnek Mutation</h4>
                            <code class="text-sm text-gray-600">
                                mutation {<br>
                                &nbsp;&nbsp;createUser(input: {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;name: "John Doe"<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;email: "john@example.com"<br>
                                &nbsp;&nbsp;}) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;id<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;name<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;email<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </code>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-8">
                        <h3 class="text-2xl font-bold text-white mb-4">
                            GraphQL Schema
                            <span class="block text-lg text-purple-100">GraphQL Şeması</span>
                        </h3>
                        <p class="text-purple-100">
                            Type definitions ve schema structure ile strongly typed API. 
                            GraphQL playground ile interactive query testing yapabilirsiniz.
                        </p>
                    </div>
                    <div class="p-8">
                        <div class="grid md:grid-cols-3 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Core Types</h4>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-start">
                                        <i class="fas fa-user text-blue-600 mt-1 mr-3"></i>
                                        <span><strong>User:</strong> Kullanıcı bilgileri ve ilişkili veriler</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-file text-green-600 mt-1 mr-3"></i>
                                        <span><strong>Page:</strong> Sayfa içeriği ve metadata</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-image text-purple-600 mt-1 mr-3"></i>
                                        <span><strong>Media:</strong> Media dosyaları ve özellikleri</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Query Types</h4>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-start">
                                        <i class="fas fa-search text-blue-600 mt-1 mr-3"></i>
                                        <span><strong>Filtering:</strong> Complex filtering ve search</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-sort text-green-600 mt-1 mr-3"></i>
                                        <span><strong>Sorting:</strong> Multi-field sorting support</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-layer-group text-purple-600 mt-1 mr-3"></i>
                                        <span><strong>Pagination:</strong> Cursor-based pagination</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Advanced Features</h4>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-start">
                                        <i class="fas fa-bolt text-blue-600 mt-1 mr-3"></i>
                                        <span><strong>Subscriptions:</strong> Real-time data updates</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-shield-alt text-green-600 mt-1 mr-3"></i>
                                        <span><strong>Directives:</strong> Custom directives ve validation</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-database text-purple-600 mt-1 mr-3"></i>
                                        <span><strong>DataLoader:</strong> N+1 problem çözümü</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Webhooks Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Webhooks
                        <span class="block text-lg text-indigo-600">Event-Driven Integration</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Real-time event notifications ile third-party systems entegrasyonu. 
                        Webhook endpoints ile sistem değişikliklerini anlık olarak takip edebilirsiniz.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Event Types
                            <span class="block text-sm text-gray-500">Olay Türleri</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Sistemde gerçekleşen events için webhook notifications. 
                            Event filtering ve custom payload support.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-user-plus text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-900">user.created</h4>
                                    <p class="text-sm text-gray-600">Yeni kullanıcı kaydı</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-900">page.published</h4>
                                    <p class="text-sm text-gray-600">Sayfa yayınlama</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg">
                                <i class="fas fa-shopping-cart text-purple-600 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-900">order.completed</h4>
                                    <p class="text-sm text-gray-600">Sipariş tamamlama</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Webhook Security
                            <span class="block text-sm text-gray-500">Güvenlik</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            HMAC signature verification ile webhook security. 
                            Replay attack protection ve IP whitelisting desteği.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">Signature Verification</h4>
                                <code class="text-sm text-gray-600">
                                    X-Webhook-Signature: sha256=abc123...
                                </code>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">Retry Logic</h4>
                                <p class="text-sm text-gray-600">
                                    Exponential backoff ile automatic retry
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">Rate Limiting</h4>
                                <p class="text-sm text-gray-600">
                                    Per-endpoint rate limiting
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-8 text-white">
                    <div class="max-w-4xl mx-auto">
                        <h3 class="text-2xl font-bold mb-4">
                            Webhook Payload Örneği
                            <span class="block text-lg text-gray-300">Webhook Payload Example</span>
                        </h3>
                        <p class="text-gray-300 mb-6">
                            Webhook events için standardize edilmiş JSON payload structure. 
                            Event metadata ve context information içerir.
                        </p>
                        <div class="bg-black/50 p-6 rounded-lg overflow-x-auto">
                            <code class="text-sm text-green-400">
                                {<br>
                                &nbsp;&nbsp;"event": "user.created",<br>
                                &nbsp;&nbsp;"timestamp": "2024-01-15T10:30:00Z",<br>
                                &nbsp;&nbsp;"webhook_id": "wh_123456789",<br>
                                &nbsp;&nbsp;"data": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"user": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id": 123,<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "John Doe",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"email": "john@example.com",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"created_at": "2024-01-15T10:30:00Z"<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                                &nbsp;&nbsp;},<br>
                                &nbsp;&nbsp;"metadata": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"tenant_id": "tenant_123",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"source": "admin_panel"<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SDK Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        SDK ve Client Libraries
                        <span class="block text-lg text-indigo-600">SDK & Client Libraries</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Popüler programlama dilleri için hazır SDK'lar ve client libraries. 
                        Hızlı entegrasyon ve type safety ile development experience'i artırır.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-js-square text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">JavaScript SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Node.js ve browser environments için TypeScript desteği
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                npm install @yourapi/sdk
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-python text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Python SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Python 3.7+ için async/await desteği
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                pip install yourapi-sdk
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-php text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">PHP SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Laravel entegrasyonu ve PSR-4 compliance
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                composer require yourapi/sdk
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-java text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Java SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Spring Boot integration ve reactive programming
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                implementation 'com.yourapi:sdk:1.0.0'
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-microsoft text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">.NET SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            C# ve F# desteği ile .NET Core compatibility
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                Install-Package YourApi.SDK
                            </code>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-golang text-indigo-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Go SDK</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Goroutine support ve context-aware operations
                        </p>
                        <div class="bg-gray-50 p-3 rounded text-left">
                            <code class="text-sm text-gray-600">
                                go get github.com/yourapi/sdk
                            </code>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">
                        SDK Kullanım Örneği
                        <span class="block text-lg text-indigo-600">SDK Usage Example</span>
                    </h3>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">JavaScript/TypeScript</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <code class="text-sm text-gray-600">
                                    import { YourApiClient } from '@yourapi/sdk';<br><br>
                                    const client = new YourApiClient({<br>
                                    &nbsp;&nbsp;apiKey: 'your-api-key',<br>
                                    &nbsp;&nbsp;baseUrl: 'https://api.yoursite.com'<br>
                                    });<br><br>
                                    const users = await client.users.list({<br>
                                    &nbsp;&nbsp;limit: 10,<br>
                                    &nbsp;&nbsp;filters: { status: 'active' }<br>
                                    });
                                </code>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Python</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <code class="text-sm text-gray-600">
                                    from yourapi import YourApiClient<br><br>
                                    client = YourApiClient(<br>
                                    &nbsp;&nbsp;api_key='your-api-key',<br>
                                    &nbsp;&nbsp;base_url='https://api.yoursite.com'<br>
                                    )<br><br>
                                    users = await client.users.list(<br>
                                    &nbsp;&nbsp;limit=10,<br>
                                    &nbsp;&nbsp;filters={'status': 'active'}<br>
                                    )
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testing Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        API Testing
                        <span class="block text-lg text-indigo-600">API Test Ortamı</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Sandbox environment ve interactive API explorer ile API'nizi test edebilirsiniz. 
                        Postman collections ve OpenAPI specifications hazır olarak sunulur.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Interactive API Explorer
                            <span class="block text-sm text-gray-500">Etkileşimli API Test Aracı</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Browser-based API testing tool ile live API calls yapabilirsiniz. 
                            Authentication, parameter validation ve response preview.
                        </p>
                        <div class="bg-white p-6 rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-play-circle text-4xl mb-4"></i>
                                <p class="font-medium">API Explorer</p>
                                <p class="text-sm">Try API endpoints interactively</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Postman Collection
                            <span class="block text-sm text-gray-500">Postman Koleksiyonu</span>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Ready-to-use Postman collection ile tüm endpoints'leri test edebilirsiniz. 
                            Environment variables ve pre-request scripts dahil.
                        </p>
                        <div class="space-y-4">
                            <a href="#" class="flex items-center p-4 bg-white rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fas fa-download text-blue-600 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-900">Postman Collection</h4>
                                    <p class="text-sm text-gray-600">API_Collection_v1.json</p>
                                </div>
                            </a>
                            <a href="#" class="flex items-center p-4 bg-white rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fas fa-download text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-900">OpenAPI Spec</h4>
                                    <p class="text-sm text-gray-600">openapi.yaml</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    API ile Entegrasyona Başlayın
                </h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    RESTful API ve GraphQL endpoint'lerimiz ile sistemimizi 
                    mevcut infrastructure'ınıza entegre edin.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#get-started" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-medium hover:bg-indigo-50 transition-colors">
                        API Key Alın
                    </a>
                    <a href="#documentation" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white hover:text-indigo-600 transition-colors">
                        Dökümantasyon
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>