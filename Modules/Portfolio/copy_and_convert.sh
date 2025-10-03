#!/bin/bash

# Portfolio modülü oluşturma scripti
# Page modülünden tüm dosyaları kopyalar ve Portfolio'ya dönüştürür

SOURCE_DIR="/Users/nurullah/Desktop/cms/laravel/Modules/Page"
TARGET_DIR="/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio"

echo "🚀 Portfolio modülü oluşturuluyor..."
echo "📂 Kaynak: $SOURCE_DIR"
echo "📂 Hedef: $TARGET_DIR"
echo ""

# Exceptions
echo "📋 Exceptions kopyalanıyor..."
cp "$SOURCE_DIR/app/Exceptions/PageException.php" "$TARGET_DIR/app/Exceptions/PortfolioException.php"
cp "$SOURCE_DIR/app/Exceptions/PageNotFoundException.php" "$TARGET_DIR/app/Exceptions/PortfolioNotFoundException.php"
cp "$SOURCE_DIR/app/Exceptions/PageCreationException.php" "$TARGET_DIR/app/Exceptions/PortfolioCreationException.php"
cp "$SOURCE_DIR/app/Exceptions/PageValidationException.php" "$TARGET_DIR/app/Exceptions/PortfolioValidationException.php"
cp "$SOURCE_DIR/app/Exceptions/PageProtectionException.php" "$TARGET_DIR/app/Exceptions/PortfolioProtectionException.php"

# DataTransferObjects
echo "📋 DTOs kopyalanıyor..."
cp "$SOURCE_DIR/app/DataTransferObjects/BulkOperationResult.php" "$TARGET_DIR/app/DataTransferObjects/BulkOperationResult.php"
cp "$SOURCE_DIR/app/DataTransferObjects/PageOperationResult.php" "$TARGET_DIR/app/DataTransferObjects/PortfolioOperationResult.php"

# Enums
echo "📋 Enums kopyalanıyor..."
cp "$SOURCE_DIR/app/Enums/CacheStrategy.php" "$TARGET_DIR/app/Enums/CacheStrategy.php"

# Events
echo "📋 Events kopyalanıyor..."
cp "$SOURCE_DIR/app/Events/TranslationCompletedEvent.php" "$TARGET_DIR/app/Events/TranslationCompletedEvent.php"

# Contracts
echo "📋 Contracts kopyalanıyor..."
cp "$SOURCE_DIR/app/Contracts/PageRepositoryInterface.php" "$TARGET_DIR/app/Contracts/PortfolioRepositoryInterface.php"

# Observers - Observer dosyası zaten models'de oluşturulacak, şimdilik atla

# Repositories
echo "📋 Repositories kopyalanıyor..."
cp "$SOURCE_DIR/app/Repositories/PageRepository.php" "$TARGET_DIR/app/Repositories/PortfolioRepository.php"

# Services
echo "📋 Services kopyalanıyor..."
cp "$SOURCE_DIR/app/Services/PageService.php" "$TARGET_DIR/app/Services/PortfolioService.php"

# Jobs
echo "📋 Jobs kopyalanıyor..."
cp "$SOURCE_DIR/app/Jobs/TranslatePageJob.php" "$TARGET_DIR/app/Jobs/TranslatePortfolioJob.php"
cp "$SOURCE_DIR/app/Jobs/BulkDeletePagesJob.php" "$TARGET_DIR/app/Jobs/BulkDeletePortfoliosJob.php"
cp "$SOURCE_DIR/app/Jobs/BulkUpdatePagesJob.php" "$TARGET_DIR/app/Jobs/BulkUpdatePortfoliosJob.php"

# Livewire Traits
echo "📋 Livewire Traits kopyalanıyor..."
cp -r "$SOURCE_DIR/app/Http/Livewire/Traits/" "$TARGET_DIR/app/Http/Livewire/Traits/"

# Livewire Components
echo "📋 Livewire Components kopyalanıyor..."
cp "$SOURCE_DIR/app/Http/Livewire/Admin/PageComponent.php" "$TARGET_DIR/app/Http/Livewire/Admin/PortfolioComponent.php"
cp "$SOURCE_DIR/app/Http/Livewire/Admin/PageManageComponent.php" "$TARGET_DIR/app/Http/Livewire/Admin/PortfolioManageComponent.php"

# Controllers
echo "📋 Controllers kopyalanıyor..."
mkdir -p "$TARGET_DIR/app/Http/Controllers/Api"
mkdir -p "$TARGET_DIR/app/Http/Controllers/Front"
cp "$SOURCE_DIR/app/Http/Controllers/Api/PageApiController.php" "$TARGET_DIR/app/Http/Controllers/Api/PortfolioApiController.php"
cp "$SOURCE_DIR/app/Http/Controllers/Front/PageController.php" "$TARGET_DIR/app/Http/Controllers/Front/PortfolioController.php"

# Resources
echo "📋 Resources kopyalanıyor..."
cp "$SOURCE_DIR/app/Http/Resources/PageResource.php" "$TARGET_DIR/app/Http/Resources/PortfolioResource.php"
cp "$SOURCE_DIR/app/Http/Resources/PageCollection.php" "$TARGET_DIR/app/Http/Resources/PortfolioCollection.php"

# Console
echo "📋 Console kopyalanıyor..."
cp "$SOURCE_DIR/app/Console/WarmPageCacheCommand.php" "$TARGET_DIR/app/Console/WarmPortfolioCacheCommand.php"

# Views
echo "📋 Views kopyalanıyor..."
mkdir -p "$TARGET_DIR/resources/views/admin/livewire"
mkdir -p "$TARGET_DIR/resources/views/admin/partials"
mkdir -p "$TARGET_DIR/resources/views/front"
mkdir -p "$TARGET_DIR/resources/views/themes/blank"

cp "$SOURCE_DIR/resources/views/admin/helper.blade.php" "$TARGET_DIR/resources/views/admin/helper.blade.php"
cp "$SOURCE_DIR/resources/views/admin/livewire/page-component.blade.php" "$TARGET_DIR/resources/views/admin/livewire/portfolio-component.blade.php"
cp "$SOURCE_DIR/resources/views/admin/livewire/page-manage-component.blade.php" "$TARGET_DIR/resources/views/admin/livewire/portfolio-manage-component.blade.php"
cp -r "$SOURCE_DIR/resources/views/admin/partials/" "$TARGET_DIR/resources/views/admin/partials/"
cp -r "$SOURCE_DIR/resources/views/front/" "$TARGET_DIR/resources/views/front/"

# Config
echo "📋 Config kopyalanıyor..."
cp "$SOURCE_DIR/config/config.php" "$TARGET_DIR/config/config.php"

# Lang files
echo "📋 Lang files kopyalanıyor..."
cp "$SOURCE_DIR/lang/tr/admin.php" "$TARGET_DIR/lang/tr/admin.php"
cp "$SOURCE_DIR/lang/en/admin.php" "$TARGET_DIR/lang/en/admin.php"
cp "$SOURCE_DIR/lang/ar/admin.php" "$TARGET_DIR/lang/ar/admin.php"
cp "$SOURCE_DIR/lang/tr/front.php" "$TARGET_DIR/lang/tr/front.php" 2>/dev/null || true

echo ""
echo "✅ Dosyalar kopyalandı!"
echo ""
echo "🔄 Şimdi tüm referanslar değiştiriliyor..."
echo ""

# Tüm PHP dosyalarında değişiklik yap
find "$TARGET_DIR" -type f -name "*.php" -not -path "*/vendor/*" -not -path "*/node_modules/*" | while read file; do
    # Page → Portfolio dönüşümleri
    sed -i '' 's/namespace Modules\\Page/namespace Modules\\Portfolio/g' "$file"
    sed -i '' 's/Modules\\Page/Modules\\Portfolio/g' "$file"
    sed -i '' 's/use Modules\\Page/use Modules\\Portfolio/g' "$file"
    
    # Class isimleri
    sed -i '' 's/class Page /class Portfolio /g' "$file"
    sed -i '' 's/class PageException/class PortfolioException/g' "$file"
    sed -i '' 's/class PageNotFoundException/class PortfolioNotFoundException/g' "$file"
    sed -i '' 's/class PageCreationException/class PortfolioCreationException/g' "$file"
    sed -i '' 's/class PageValidationException/class PortfolioValidationException/g' "$file"
    sed -i '' 's/class PageProtectionException/class PortfolioProtectionException/g' "$file"
    sed -i '' 's/class PageOperationResult/class PortfolioOperationResult/g' "$file"
    sed -i '' 's/class PageRepository/class PortfolioRepository/g' "$file"
    sed -i '' 's/class PageService/class PortfolioService/g' "$file"
    sed -i '' 's/class PageComponent/class PortfolioComponent/g' "$file"
    sed -i '' 's/class PageManageComponent/class PortfolioManageComponent/g' "$file"
    sed -i '' 's/class PageApiController/class PortfolioApiController/g' "$file"
    sed -i '' 's/class PageController/class PortfolioController/g' "$file"
    sed -i '' 's/class PageResource/class PortfolioResource/g' "$file"
    sed -i '' 's/class PageCollection/class PortfolioCollection/g' "$file"
    sed -i '' 's/class TranslatePage/class TranslatePortfolio/g' "$file"
    sed -i '' 's/class BulkDeletePages/class BulkDeletePortfolios/g' "$file"
    sed -i '' 's/class BulkUpdatePages/class BulkUpdatePortfolios/g' "$file"
    sed -i '' 's/class WarmPageCache/class WarmPortfolioCache/g' "$file"
    sed -i '' 's/interface PageRepositoryInterface/interface PortfolioRepositoryInterface/g' "$file"
    
    # Variables ve properties
    sed -i '' 's/\$page[^_s]/\$portfolio/g' "$file"
    sed -i '' 's/->page /->portfolio /g' "$file"
    sed -i '' 's/\$pages/\$portfolios/g' "$file"
    
    # Primary key
    sed -i '' "s/'page_id'/'portfolio_id'/g" "$file"
    sed -i '' 's/"page_id"/"portfolio_id"/g' "$file"
    sed -i '' 's/page_id/portfolio_id/g' "$file"
    
    # Table names
    sed -i '' "s/'pages'/'portfolios'/g" "$file"
    sed -i '' 's/"pages"/"portfolios"/g' "$file"
    
    # Route names
    sed -i '' "s/'page::/'portfolio::/g" "$file"
    sed -i '' "s/\"page::/\"portfolio::/g" "$file"
    
    # Translation keys
    sed -i '' "s/__('page::/__('portfolio::/g" "$file"
    sed -i '' 's/__("page::/__("portfolio::/g' "$file"
    
    # is_homepage KALDIR (Portfolio'da homepage yok)
    sed -i '' '/is_homepage/d' "$file"
    sed -i '' '/Homepage/d' "$file"
    sed -i '' '/homepage/d' "$file"
    
    # Log messages
    sed -i '' 's/Page created/Portfolio created/g' "$file"
    sed -i '' 's/Page updated/Portfolio updated/g' "$file"
    sed -i '' 's/Page deleted/Portfolio deleted/g' "$file"
    sed -i '' 's/Page /Portfolio /g' "$file"
    
    echo "✓ $(basename $file)"
done

# Blade dosyalarında değişiklik yap
find "$TARGET_DIR/resources/views" -type f -name "*.blade.php" | while read file; do
    sed -i '' 's/@section('"'"'title'"'"', __("page::/@section('"'"'title'"'"', __("portfolio::/g' "$file"
    sed -i '' "s/__('page::/__('portfolio::/g" "$file"
    sed -i '' 's/__("page::/__("portfolio::/g' "$file"
    sed -i '' 's/page-component/portfolio-component/g' "$file"
    sed -i '' 's/page-manage-component/portfolio-manage-component/g' "$file"
    sed -i '' 's/\$page/\$portfolio/g' "$file"
    sed -i '' 's/\$pages/\$portfolios/g' "$file"
    sed -i '' 's/page_id/portfolio_id/g' "$file"
    sed -i '' "s/'pages'/'portfolios'/g" "$file"
    
    # is_homepage kaldır
    sed -i '' '/is_homepage/d' "$file"
    sed -i '' '/Homepage/d' "$file"
    
    echo "✓ $(basename $file)"
done

# Config dosyasında değişiklik yap
sed -i '' 's/page/portfolio/g' "$TARGET_DIR/config/config.php"

# Lang dosyalarında değişiklik yap
find "$TARGET_DIR/lang" -type f -name "*.php" | while read file; do
    sed -i '' 's/pages/portfolios/g' "$file"
    sed -i '' 's/Pages/Portfolios/g' "$file"
    sed -i '' 's/page/portfolio/g' "$file"
    sed -i '' 's/Page/Portfolio/g' "$file"
    sed -i '' 's/Sayfa/Portfolio/g' "$file"
    sed -i '' 's/sayfa/portfolio/g' "$file"
    
    echo "✓ $(basename $file)"
done

echo ""
echo "✅ Tüm referanslar değiştirildi!"
echo "🎉 Portfolio modülü başarıyla oluşturuldu!"
