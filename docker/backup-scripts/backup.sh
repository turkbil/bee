#!/bin/bash
# 🔥 BACKUP MANAGER SCRIPT - 500 Tenant Production
# Laravel Database + Redis + Storage Backup

set -e

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +"%Y%m%d_%H%M%S")
RETENTION_DAYS=${BACKUP_RETENTION_DAYS:-7}

# Database Configuration
DB_HOST=${DB_HOST:-mysql-master}
DB_USER="root"
DB_PASSWORD=${DB_ROOT_PASSWORD:-strongpassword123}
DB_NAME=${DB_DATABASE:-laravel}

# Redis Configuration
REDIS_HOST=${REDIS_HOST:-redis-cluster}
REDIS_PORT=6379

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Create backup directory
mkdir -p "${BACKUP_DIR}/${DATE}"

log "🔥 Starting backup process..."

# 1. MySQL Backup
log "📊 Backing up MySQL database..."
mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    "$DB_NAME" | gzip > "${BACKUP_DIR}/${DATE}/mysql_${DB_NAME}_${DATE}.sql.gz"

# 2. Redis Backup
log "⚡ Backing up Redis data..."
redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" --rdb "${BACKUP_DIR}/${DATE}/redis_${DATE}.rdb"

# 3. Storage Backup (if exists)
if [ -d "/var/www/html/storage/app" ]; then
    log "📁 Backing up storage files..."
    tar -czf "${BACKUP_DIR}/${DATE}/storage_${DATE}.tar.gz" -C /var/www/html/storage/app .
fi

# 4. Create backup manifest
log "📋 Creating backup manifest..."
cat > "${BACKUP_DIR}/${DATE}/manifest.json" <<EOF
{
    "backup_date": "${DATE}",
    "mysql_file": "mysql_${DB_NAME}_${DATE}.sql.gz",
    "redis_file": "redis_${DATE}.rdb",
    "storage_file": "storage_${DATE}.tar.gz",
    "retention_days": ${RETENTION_DAYS},
    "created_at": "$(date -Iseconds)"
}
EOF

# 5. Cleanup old backups
log "🗑️ Cleaning up old backups (older than ${RETENTION_DAYS} days)..."
find "$BACKUP_DIR" -type d -name "20*" -mtime +${RETENTION_DAYS} -exec rm -rf {} \;

# 6. S3 Upload (if enabled)
if [ "${AWS_S3_BACKUP:-false}" = "true" ]; then
    log "☁️ Uploading to S3..."
    aws s3 cp "${BACKUP_DIR}/${DATE}/" "s3://${AWS_S3_BUCKET:-laravel-backups}/${DATE}/" --recursive
fi

log "✅ Backup completed successfully: ${DATE}"
log "📊 Backup size: $(du -sh ${BACKUP_DIR}/${DATE} | cut -f1)"