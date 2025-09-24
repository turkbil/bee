#!/bin/bash

# Laravel Otomatik Başlatma Script'i
# Bu script sistem başlangıcında otomatik çalışır

set -e

# Loglar için
LOG_FILE="/Users/nurullah/Desktop/cms/laravel/storage/logs/auto-start.log"
exec > >(tee -a "$LOG_FILE") 2>&1

echo "$(date): Laravel otomatik başlatma başlıyor..."

# Çalışma dizinine geç
cd /Users/nurullah/Desktop/cms/laravel

# Biraz bekle (sistem tamamen açılsın)
sleep 10

# start.sh'ı çalıştır (option 1 - local development)
echo "$(date): start.sh çalıştırılıyor..."
echo "1" | timeout 60 ./start.sh >/dev/null 2>&1

echo "$(date): Laravel otomatik başlatma tamamlandı!"