#!/bin/bash

echo "=== PRODUCTION DEPLOYMENT - İXTİF AI İYİLEŞTİRMELERİ ==="
echo ""
echo "📍 Server: tuufi.com (194.163.40.231)"
echo "📂 Path: /var/www/vhosts/tuufi.com/httpdocs"
echo ""

# SSH ve deployment komutları
ssh root@194.163.40.231 << 'ENDSSH'

cd /var/www/vhosts/tuufi.com/httpdocs

echo "✅ 1. Git pull (latest code)"
git pull origin main

echo ""
echo "✅ 2. Composer autoload dump"
composer dump-autoload

echo ""
echo "✅ 3. Clear all caches"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "✅ 4. Fix permissions"
chown -R apache:apache storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ""
echo "✅ 5. Restart PHP-FPM"
systemctl restart php-fpm

echo ""
echo "🎉 DEPLOYMENT TAMAMLANDI!"
echo ""
echo "📊 Test için:"
echo "curl -X POST https://ixtif.com/api/ai/v1/shop-assistant/chat \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"message\":\"transpalet ariyorum\"}'"

ENDSSH

echo ""
echo "✅ Deployment completed successfully!"
