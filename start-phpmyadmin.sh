#!/bin/bash
# PHPMyAdmin otomatik başlatma scripti

echo "PHPMyAdmin başlatılıyor..."
cd /opt/homebrew/share/phpmyadmin
php -S localhost:8001 &

echo "PHPMyAdmin http://localhost:8001 adresinde çalışmaya başladı"
echo "Durdurmak için: pkill -f 'php -S localhost:8001'"