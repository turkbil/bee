#!/bin/bash

# 🚀 HORIZON OTOMATIK BAŞLATMA SCRIPT'I
# Bu script sistemde Horizon yoksa otomatik başlatır

LARAVEL_PATH="/Users/nurullah/Desktop/cms/laravel"
HORIZON_PID_FILE="$LARAVEL_PATH/storage/horizon.pid"
LOG_FILE="$LARAVEL_PATH/storage/logs/horizon-auto.log"

echo "$(date): Horizon kontrol ediliyor..." >> "$LOG_FILE"

# Horizon çalışıyor mu kontrol et
if pgrep -f "artisan horizon" > /dev/null; then
    echo "$(date): ✅ Horizon zaten çalışıyor" >> "$LOG_FILE"
else
    echo "$(date): ❌ Horizon çalışmıyor, başlatılıyor..." >> "$LOG_FILE"
    
    # Eski queue worker'ları öldür
    pkill -f "queue:work"
    sleep 2
    
    # Horizon'u arka planda başlat
    cd "$LARAVEL_PATH"
    nohup php artisan horizon > /dev/null 2>&1 &
    echo $! > "$HORIZON_PID_FILE"
    
    echo "$(date): 🚀 Horizon başlatıldı (PID: $(cat $HORIZON_PID_FILE))" >> "$LOG_FILE"
fi

echo "$(date): Kontrol tamamlandı" >> "$LOG_FILE"