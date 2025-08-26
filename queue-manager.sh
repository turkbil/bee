#!/bin/bash

# 🚀 Laravel Queue Manager - Otomatik Queue Worker Yönetim Sistemi
# Bu script queue worker'ı sürekli kontrol eder ve gerekirse yeniden başlatır

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Konfigürasyon
QUEUE_LOG="/Users/nurullah/Desktop/cms/laravel/storage/logs/queue-worker.log"
PID_FILE="/Users/nurullah/Desktop/cms/laravel/storage/queue-worker.pid"
CHECK_INTERVAL=10 # Her 10 saniyede kontrol et
MAX_RETRIES=3

echo -e "${BLUE}🚀 Laravel Queue Manager Başlatıldı${NC}"
echo "📊 Log: $QUEUE_LOG"
echo "🔧 PID File: $PID_FILE"
echo ""

# Queue worker'ı başlat
start_queue_worker() {
    echo -e "${YELLOW}⚙️  Queue Worker başlatılıyor...${NC}"
    
    # Eski process'leri temizle
    pkill -f "php.*artisan.*queue:work" 2>/dev/null || true
    sleep 2
    
    # Yeni worker başlat
    cd /Users/nurullah/Desktop/cms/laravel
    nohup php artisan queue:work \
        --queue=default,tenant_isolated,critical,central_isolated \
        --sleep=3 \
        --tries=3 \
        --max-jobs=1000 \
        --timeout=300 \
        --memory=1024 \
        > "$QUEUE_LOG" 2>&1 &
    
    local pid=$!
    echo $pid > "$PID_FILE"
    
    sleep 3
    
    # Başarıyla başladı mı kontrol et
    if kill -0 $pid 2>/dev/null; then
        echo -e "${GREEN}✅ Queue Worker başarıyla başlatıldı! (PID: $pid)${NC}"
        return 0
    else
        echo -e "${RED}❌ Queue Worker başlatılamadı!${NC}"
        return 1
    fi
}

# Queue worker çalışıyor mu kontrol et
check_queue_worker() {
    if [ -f "$PID_FILE" ]; then
        local pid=$(cat "$PID_FILE")
        if kill -0 $pid 2>/dev/null; then
            return 0 # Çalışıyor
        fi
    fi
    return 1 # Çalışmıyor
}

# Queue sağlığını kontrol et
check_queue_health() {
    # Jobs tablosunda işlenmemiş job var mı?
    local pending_jobs=$(php artisan tinker --execute="return \DB::table('jobs')->where('attempts', '<', 3)->count();" 2>/dev/null || echo "0")
    
    if [ "$pending_jobs" -gt 50 ]; then
        echo -e "${YELLOW}⚠️  Bekleyen job sayısı yüksek: $pending_jobs${NC}"
        return 1
    fi
    
    return 0
}

# Ana döngü
retry_count=0

while true; do
    if ! check_queue_worker; then
        echo -e "${RED}🔴 Queue Worker çalışmıyor!${NC}"
        
        if [ $retry_count -lt $MAX_RETRIES ]; then
            echo "🔄 Yeniden başlatma denemesi: $((retry_count + 1))/$MAX_RETRIES"
            
            if start_queue_worker; then
                retry_count=0
                echo -e "${GREEN}✅ Queue Worker yeniden başlatıldı${NC}"
            else
                ((retry_count++))
                echo -e "${RED}❌ Başlatma başarısız, $CHECK_INTERVAL saniye sonra tekrar deneyeceğim${NC}"
            fi
        else
            echo -e "${RED}🚨 KRİTİK: $MAX_RETRIES deneme başarısız! Manuel müdahale gerekiyor.${NC}"
            echo "📧 Log dosyasını kontrol edin: $QUEUE_LOG"
            
            # Sistem yöneticisine bildir (opsiyonel)
            # say "Queue worker kritik hata, manuel müdahale gerekiyor"
            
            # 1 dakika bekle ve tekrar dene
            sleep 60
            retry_count=0
        fi
    else
        # Worker çalışıyor, sağlık kontrolü yap
        if ! check_queue_health; then
            echo -e "${YELLOW}⚠️  Queue sağlık sorunu tespit edildi, worker yeniden başlatılıyor${NC}"
            start_queue_worker
        else
            echo -e "${GREEN}✅ Queue Worker sağlıklı çalışıyor${NC} - $(date '+%H:%M:%S')"
        fi
        retry_count=0
    fi
    
    sleep $CHECK_INTERVAL
done