#!/bin/bash

# 🚀 Laravel Queue Worker Auto-Start Script
# KALICI ÇÖZÜM: Queue worker'ı otomatik başlatır ve monitoring ekler

echo "🚀 Laravel Queue Worker başlatılıyor..."

# Renk kodları
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Çalışma dizinine geç
cd /Users/nurullah/Desktop/cms/laravel

# Existing queue worker'ları temizle
echo -e "${YELLOW}🧹 Eski queue worker'lar temizleniyor...${NC}"
pkill -f "php.*artisan.*queue:work" 2>/dev/null || true

# Wait a moment
sleep 2

# Queue worker'ı TÜM QUEUE'LAR için başlat
echo -e "${YELLOW}⚙️  Queue Worker başlatılıyor (tüm queue'lar)...${NC}"
nohup php artisan queue:work \
    --queue=default,tenant_isolated,critical,central_isolated \
    --sleep=3 \
    --tries=3 \
    --max-jobs=1000 \
    --timeout=300 \
    --memory=1024 \
    > storage/logs/queue-worker.log 2>&1 &

WORKER_PID=$!

# PID'yi kaydet
echo $WORKER_PID > storage/queue-worker.pid

# Worker'ın gerçekten başladığından emin ol
sleep 3

if kill -0 $WORKER_PID 2>/dev/null; then
    echo -e "${GREEN}✅ Queue Worker başarıyla başlatıldı!${NC}"
    echo "📊 Log dosyası: storage/logs/queue-worker.log"
    echo "🔧 PID: $WORKER_PID"
    echo -e "${GREEN}🎯 Queue worker aktif ve çalışıyor!${NC}"
    
    # Queue durumunu kontrol et
    PENDING_JOBS=$(php artisan tinker --execute="return \DB::table('jobs')->count();" 2>/dev/null || echo "0")
    echo "📦 Bekleyen job sayısı: $PENDING_JOBS"
    
    # Queue manager'ı da başlat (arka planda monitoring için)
    if [ -f "./queue-manager.sh" ]; then
        echo -e "${YELLOW}🔍 Queue Manager monitoring başlatılıyor...${NC}"
        chmod +x ./queue-manager.sh
        nohup ./queue-manager.sh > storage/logs/queue-manager.log 2>&1 &
        echo "📊 Queue Manager log: storage/logs/queue-manager.log"
    fi
else
    echo -e "${RED}❌ Queue Worker başlatılamadı!${NC}"
    echo "Lütfen storage/logs/queue-worker.log dosyasını kontrol edin."
    exit 1
fi