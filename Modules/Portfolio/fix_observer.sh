#!/bin/bash

# Observer dosyasındaki homepage referanslarını tamamen kaldır
FILE="app/Observers/PortfolioObserver.php"

# Satır satır okuyup homepage içermeyenleri yaz
awk '!/homeportfolio/ && !/Homeportfolio/' "$FILE" > "$FILE.tmp" && mv "$FILE.tmp" "$FILE"

echo "✅ Homepage referansları kaldırıldı!"
