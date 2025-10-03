#!/bin/bash

FILE="app/Observers/PortfolioObserver.php"

# Namespace ve class düzelt
sed -i '' 's/namespace Modules\\Page\\App\\Observers;/namespace Modules\\Portfolio\\App\\Observers;/g' "$FILE"
sed -i '' 's/use Modules\\Page\\App\\Models\\Page;/use Modules\\Portfolio\\App\\Models\\Portfolio;/g' "$FILE"
sed -i '' 's/class PageObserver/class PortfolioObserver/g' "$FILE"

# Model reference düzelt
sed -i '' 's/Page \$page/Portfolio \$portfolio/g' "$FILE"
sed -i '' 's/Page::/Portfolio::/g' "$FILE"
sed -i '' 's/\$page->/\$portfolio->/g' "$FILE"

# Primary key düzelt
sed -i '' 's/page_id/portfolio_id/g' "$FILE"

# Config ve log düzelt
sed -i '' "s/config('page\./config('portfolio./g" "$FILE"
sed -i '' "s/'Page /'Portfolio /g" "$FILE"
sed -i '' "s/'page/'portfolio/g" "$FILE"
sed -i '' 's/"Page /"Portfolio /g' "$FILE"

# Cache method düzelt
sed -i '' 's/clearPageCaches/clearPortfolioCaches/g' "$FILE"
sed -i '' "s/'pages'/'portfolios'/g" "$FILE"

# Exception düzelt
sed -i '' 's/PageValidationException/PortfolioValidationException/g' "$FILE"
sed -i '' 's/PageProtectionException/PortfolioProtectionException/g' "$FILE"

# Homepage ile ilgili satırları kaldır (53-56, 94-105, 225-228, 315-318, 357 satırlar)
# Satır numaralarını kullanmadan pattern matching ile kaldır
sed -i '' '/Homepage kontrolü - sadece bir tane olabilir/,+3d' "$FILE"
sed -i '' '/Homepage pasif edilemez kontrolü/,+2d' "$FILE"
sed -i '' '/Homepage değişiklik kontrolü/,+5d' "$FILE"
sed -i '' '/Homepage koruması/,+2d' "$FILE"
sed -i '' "/Cache::forget('homepage_data');/d" "$FILE"

# HomepageProtectionException import'unu kaldır
sed -i '' '/HomepageProtectionException/d' "$FILE"

# Portfolio comment düzelt
sed -i '' 's/Page Model Observer/Portfolio Model Observer/g' "$FILE"

echo "✅ Observer dosyası düzeltildi!"
