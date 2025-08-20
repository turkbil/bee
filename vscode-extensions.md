# VS Code Laravel Tenant Projesi - Gerekli Eklentiler

## 🔥 PHP & Laravel Eklentileri (ZORUNLU)

### 1. PHP IntelliSense
**Eklenti ID**: `bmewburn.vscode-intelephense-client`
- PHP kod tamamlama ve intellisense
- Hata yakalama ve syntax highlighting
- Laravel için optimize edilmiş

### 2. Laravel Extension Pack  
**Eklenti ID**: `onecentlin.laravel-extension-pack`
- Laravel blade syntax
- Artisan komutları
- Route tanımlama yardımı

### 3. Laravel Blade Snippets
**Eklenti ID**: `onecentlin.laravel-blade`
- Blade template syntax highlighting
- Code snippets for blade directives

### 4. Laravel goto view
**Eklenti ID**: `codingyu.laravel-goto-view`
- View dosyalarına hızla geçiş
- Controller'dan view'a jump

### 5. Laravel Artisan
**Eklenti ID**: `ryannaddy.laravel-artisan`
- VS Code içinden artisan komutları çalıştırma

### 6. PHP Namespace Resolver
**Eklenti ID**: `mehedidracula.php-namespace-resolver`
- Otomatik namespace import
- Use statement'ları düzenleme

## 🗄️ Veritabanı & Tenant Yönetimi

### 7. MySQL
**Eklenti ID**: `formulahendry.vscode-mysql`
- Veritabanı bağlantısı ve yönetimi
- Query çalıştırma

### 8. SQLTools
**Eklenti ID**: `mtxr.sqltools`
- Gelişmiş SQL yönetimi
- Multiple database connections

### 9. SQLTools MySQL/MariaDB
**Eklenti ID**: `mtxr.sqltools-driver-mysql`
- MySQL driver for SQLTools

## 🎨 Frontend (Livewire, Tailwind, Alpine.js)

### 10. Tailwind CSS IntelliSense
**Eklenti ID**: `bradlc.vscode-tailwindcss`
- Tailwind class autocomplete
- CSS preview

### 11. Alpine.js IntelliSense
**Eklenti ID**: `adrianwilczynski.alpine-js-intellisense`
- Alpine.js syntax support

### 12. Livewire Language Support
**Eklenti ID**: `cierra.livewire-vscode`
- Livewire syntax highlighting
- Component navigation

### 13. Auto Rename Tag
**Eklenti ID**: `formulahendry.auto-rename-tag`
- HTML/Blade tag otomatik yeniden adlandırma

### 14. HTML CSS Support
**Eklenti ID**: `ecmel.vscode-html-css`
- CSS class intellisense HTML'de

## 📝 Git & Geliştirme Araçları

### 15. GitLens
**Eklenti ID**: `eamodio.gitlens`
- Advanced Git features
- Commit history ve blame

### 16. Git Graph
**Eklenti ID**: `mhutchie.git-graph`
- Visual git branch grafiği

### 17. Composer
**Eklenti ID**: `ikappas.composer`
- Composer.json intellisense

### 18. DotENV
**Eklenti ID**: `mikestead.dotenv`
- .env dosyası syntax highlighting

## 🧪 Test & Debug

### 19. PHP Debug
**Eklenti ID**: `xdebug.php-debug`
- Xdebug ile PHP debugging

### 20. PHPUnit
**Eklenti ID**: `emallin.phpunit`
- PHPUnit test runner

### 21. Better PHPUnit
**Eklenti ID**: `calebporzio.better-phpunit`
- Enhanced PHPUnit integration

## 🔧 Yardımcı Araçlar

### 22. Error Lens
**Eklenti ID**: `usernamehw.errorlens`
- Inline error display

### 23. Bracket Pair Colorizer 2
**Eklenti ID**: `CoenraadS.bracket-pair-colorizer-2`
- Bracket renklendirme

### 24. Indent Rainbow
**Eklenti ID**: `oderwat.indent-rainbow`
- Indentation görselleştirme

### 25. Path Intellisense
**Eklenti ID**: `christian-kohler.path-intellisense`
- Dosya yolu otomatik tamamlama

### 26. Thunder Client
**Eklenti ID**: `rangav.vscode-thunder-client`
- API testing (Postman alternatifi)

## ⚙️ KURULUM KOMUTLARI

VS Code'u açtıktan sonra:
1. `Cmd+Shift+P` (Mac) veya `Ctrl+Shift+P` (Windows/Linux)
2. "Extensions: Install Extensions" yazın
3. Her bir eklenti ID'sini arayıp yükleyin

VEYA

Extensions sekmesinde aratın:
- PHP IntelliSense
- Laravel Extension Pack
- Tailwind CSS IntelliSense
- GitLens
- MySQL
- vb.

## 🔥 HIZLI KURULUM (Tek Komutla)

Eğer VS Code terminal'den erişilebiliyorsa:

```bash
# PHP & Laravel
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension onecentlin.laravel-extension-pack
code --install-extension onecentlin.laravel-blade
code --install-extension codingyu.laravel-goto-view
code --install-extension ryannaddy.laravel-artisan
code --install-extension mehedidracula.php-namespace-resolver

# Database
code --install-extension formulahendry.vscode-mysql
code --install-extension mtxr.sqltools
code --install-extension mtxr.sqltools-driver-mysql

# Frontend
code --install-extension bradlc.vscode-tailwindcss
code --install-extension adrianwilczynski.alpine-js-intellisense
code --install-extension cierra.livewire-vscode
code --install-extension formulahendry.auto-rename-tag
code --install-extension ecmel.vscode-html-css

# Git & Tools
code --install-extension eamodio.gitlens
code --install-extension mhutchie.git-graph
code --install-extension ikappas.composer
code --install-extension mikestead.dotenv

# Debug & Test
code --install-extension xdebug.php-debug
code --install-extension emallin.phpunit
code --install-extension calebporzio.better-phpunit

# Utilities
code --install-extension usernamehw.errorlens
code --install-extension CoenraadS.bracket-pair-colorizer-2
code --install-extension oderwat.indent-rainbow
code --install-extension christian-kohler.path-intellisense
code --install-extension rangav.vscode-thunder-client
```

## 🎯 ÖNEMLİ NOTLAR

### VS Code Settings.json Önerileri:
```json
{
    "php.suggest.basic": false,
    "intelephense.files.maxSize": 5000000,
    "intelephense.environment.includePaths": [
        "vendor/"
    ],
    "tailwindCSS.includeLanguages": {
        "blade": "html"
    },
    "files.associations": {
        "*.blade.php": "blade"
    },
    "emmet.includeLanguages": {
        "blade": "html"
    }
}
```

Bu eklentiler Laravel tenant projesi için tüm ihtiyaçları karşılayacaktır!