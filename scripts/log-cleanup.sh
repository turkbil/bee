#!/bin/bash
# Log Temizleme Script - Her gün çalışır
# 7 günden eski logları siler, büyük logları truncate eder

LOG_DIR="/var/www/vhosts/tuufi.com/httpdocs/storage"
MAX_SIZE_MB=100  # 100MB üstü truncate
MAX_AGE_DAYS=7   # 7 günden eski sil

# 7 günden eski log dosyalarını sil
find $LOG_DIR -name "*.log" -type f -mtime +$MAX_AGE_DAYS -delete 2>/dev/null

# 100MB üstü logları truncate et
find $LOG_DIR -name "*.log" -type f -size +${MAX_SIZE_MB}M -exec truncate -s 0 {} \; 2>/dev/null

# Debugbar cache temizle (7 günden eski)
find $LOG_DIR/debugbar -type f -mtime +$MAX_AGE_DAYS -delete 2>/dev/null

echo "$(date): Log cleanup completed" >> /var/www/vhosts/tuufi.com/httpdocs/storage/logs/cleanup.log
