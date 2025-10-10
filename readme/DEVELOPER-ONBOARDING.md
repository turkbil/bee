# 👨‍💻 DEVELOPER ONBOARDING GUIDE

## 🚀 **Hızlı Başlangıç**

### **1. Sistem Gereksinimleri**
```bash
- PHP 8.2+
- MySQL 8.0+ / PostgreSQL 13+
- Redis 6.0+
- Node.js 18+
- Composer 2.0+
```

### **2. Kurulum Adımları**
```bash
# 1. Repository clone
git clone <repository-url>
cd laravel-cms

# 2. Dependencies yükle
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate --seed

# 5. Development server
php artisan serve
```

---

## 🏗️ **Proje Mimarisi**

### **Modül Yapısı**
```
Modules/
├── AI/                    # AI Content Generation
├── Page/                  # Sayfa Yönetimi
├── Portfolio/             # Portfolio Modülü
├── UserManagement/        # Kullanıcı Yönetimi
├── TenantManagement/      # Tenant Sistemi
├── LanguageManagement/    # Dil Yönetimi
└── ThemeManagement/       # Tema Yönetimi
```

### **Core Services**
```
app/Services/
├── GlobalCacheService.php      # Cache yönetimi
├── TenantCacheService.php      # Tenant cache
├── GlobalSeoService.php        # SEO işlemleri
└── DynamicRouteService.php     # Route yönetimi
```

---

## 📋 **Geliştirme Kuralları**

### **Code Style**
```php
<?php
// ✅ DOĞRU: PSR-12 standardı
namespace Modules\Example\App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->getData()
        ]);
    }
}
```

### **Commit Mesajları**
```bash
# Format: type(scope): description
feat(ai): add content generation API
fix(page): resolve N+1 query issue
docs(readme): update installation guide
test(unit): add PageRepository tests
```

### **Branch Naming**
```bash
feature/ai-content-generation
bugfix/page-n1-query-fix
hotfix/security-patch
docs/api-documentation
```

---

## 🔧 **Development Workflow**

### **1. Yeni Feature Geliştirme**
```bash
# 1. Feature branch oluştur
git checkout -b feature/new-feature-name

# 2. CLAUDE.md talimatlarını oku
cat CLAUDE.md

# 3. Pattern'e uygun kod yaz
# Page modülü pattern'ini takip et

# 4. Test yaz
php artisan test

# 5. Code review için PR aç
gh pr create --title "feat: new feature description"
```

### **2. Bug Fix Süreci**
```bash
# 1. Issue'yu reproduce et
# 2. Test case yaz (önce fail etmeli)
# 3. Fix uygula
# 4. Test pass olmalı
# 5. Regression test yap
```

---

## 🧪 **Testing Yaklaşımı**

### **Test Türleri**
```bash
# Unit Tests
php artisan test tests/Unit/

# Feature Tests
php artisan test tests/Feature/

# Browser Tests (Dusk)
php artisan dusk

# Specific test
php artisan test --filter PageRepositoryTest
```

### **Test Yazma Kuralları**
```php
<?php
// ✅ DOĞRU: Açıklayıcı test adı
public function test_it_creates_page_with_multilang_content()
{
    // Arrange
    $pageData = [
        'title' => ['tr' => 'Test', 'en' => 'Test'],
        'slug' => ['tr' => 'test', 'en' => 'test']
    ];

    // Act
    $page = Page::create($pageData);

    // Assert
    $this->assertDatabaseHas('pages', [
        'title->tr' => 'Test'
    ]);
}
```

---

## 🔍 **Debugging Araçları**

### **Laravel Telescope**
```bash
# Telescope dashboard
http://laravel.test/telescope

# Query monitoring
# Request monitoring
# Job monitoring
```

### **Debug Commands**
```bash
# Cache temizle
php artisan app:clear-all

# Queue durumu
php artisan horizon:status

# Log monitoring
tail -f storage/logs/laravel.log

# Database query log
DB::enableQueryLog();
// ... code ...
dd(DB::getQueryLog());
```

---

## 🛠️ **Kullanışlı Komutlar**

### **Development Commands**
```bash
# Migration & Seed
php artisan migrate:fresh --seed

# Queue restart
php artisan queue:restart && php artisan horizon:terminate

# Asset build
npm run dev        # Development
npm run build      # Production

# Code analysis
./vendor/bin/phpstan analyse
./vendor/bin/php-cs-fixer fix
```

### **Modül Commands**
```bash
# Clear module cache
php artisan module:clear-cache

# Module status
php artisan module:list

# Generate module files
php artisan module:make-controller Blog BlogController
```

---

## 📚 **Önemli Dökümanlar**

### **Teknik Docs**
- [`CLAUDE.md`] - Claude çalışma talimatları
- [`readme/claude-docs/`] - Detaylı teknik docs
- [`readme/modules/`] - Modül pattern'leri
- [`readme/global-services/`] - Global service'ler

### **Business Logic**
- [`readme/business/`] - İş kuralları
- [`readme/seo/`] - SEO implementasyonu
- [`readme/AI-MODULE-ANALYSIS/`] - AI modül detayları

---

## 🚨 **Yaygın Sorunlar & Çözümler**

### **Cache Sorunları**
```bash
# Problem: Config cache'i
php artisan config:clear

# Problem: Route cache'i
php artisan route:clear

# Problem: View cache'i
php artisan view:clear
```

### **Database Sorunları**
```bash
# Problem: Migration error
php artisan migrate:rollback
php artisan migrate

# Problem: Seeder error
php artisan db:seed --class=SpecificSeeder
```

### **Queue Sorunları**
```bash
# Problem: Job stuck
php artisan queue:flush
php artisan horizon:clear

# Problem: Worker not running
php artisan horizon:status
```

---

## 🤝 **Team Collaboration**

### **Code Review Checklist**
- [ ] CLAUDE.md kurallarına uygun mu?
- [ ] Test coverage yeterli mi?
- [ ] Performance optimizasyonu yapıldı mı?
- [ ] Security açıkları var mı?
- [ ] Documentation güncellendi mi?

### **Daily Standup**
- Dün ne yaptım?
- Bugün ne yapacağım?
- Blocker var mı?
- Help needed?

---

## 📞 **Destek & İletişim**

### **Technical Lead**
- **Email**: nurullah@nurullah.net
- **Slack**: @nurullah

### **Escalation Matrix**
- **P0**: Immediate - Slack mention + Email
- **P1**: 2 hours - Slack message
- **P2**: 24 hours - Email
- **P3**: 72 hours - Task assign

---

## 🎉 **Hoş Geldin!**

Bu guide ile Laravel CMS projesinde etkili bir şekilde çalışabilirsin.

**İlk görevin:**
1. Local environment'ı kur
2. Sample page oluştur
3. Test yaz
4. İlk PR'ını aç

**Sorular?** Çekinme, sor! 🚀