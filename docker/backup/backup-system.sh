#!/bin/bash

# 💾 Laravel 500 Tenant System - Automated Backup System
# Handles database, files, and configuration backups

set -e

# Configuration
BACKUP_DIR="/backups"
RETENTION_DAYS=30
S3_BUCKET="${BACKUP_S3_BUCKET}"
SLACK_WEBHOOK="${BACKUP_SLACK_WEBHOOK}"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR $(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" >&2
}

success() {
    echo -e "${GREEN}[SUCCESS $(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# Send notification
notify() {
    local message=$1
    local status=$2
    
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"🏢 Laravel 500 Tenant System Backup\\n**Status:** $status\\n**Message:** $message\"}" \
            "$SLACK_WEBHOOK" > /dev/null 2>&1 || true
    fi
}

# Create backup directories
create_backup_dirs() {
    mkdir -p "$BACKUP_DIR"/{database,files,config}
    mkdir -p "$BACKUP_DIR"/tenant-{a,b,c,d}
}

# Backup central database
backup_central_database() {
    log "🗄️  Backing up central database..."
    
    local backup_file="$BACKUP_DIR/database/central-$(date +%Y%m%d_%H%M%S).sql.gz"
    
    if mysqldump -h mysql-master -u root -p"$DB_ROOT_PASSWORD" \
        --single-transaction --routines --triggers \
        laravel | gzip > "$backup_file"; then
        success "Central database backup completed: $backup_file"
    else
        error "Central database backup failed"
        return 1
    fi
}

# Backup tenant databases
backup_tenant_databases() {
    log "🏢 Backing up tenant databases..."
    
    local tenants=("tenant_a" "tenant_b" "tenant_c" "tenant_d")
    
    for tenant in "${tenants[@]}"; do
        log "Backing up $tenant database..."
        local backup_file="$BACKUP_DIR/tenant-${tenant#tenant_}/${tenant}-$(date +%Y%m%d_%H%M%S).sql.gz"
        
        if mysqldump -h mysql-master -u root -p"$DB_ROOT_PASSWORD" \
            --single-transaction --routines --triggers \
            "$tenant" | gzip > "$backup_file"; then
            success "Tenant $tenant backup completed"
        else
            error "Tenant $tenant backup failed"
        fi
    done
}

# Backup application files
backup_application_files() {
    log "📁 Backing up application files..."
    
    local backup_file="$BACKUP_DIR/files/app-files-$(date +%Y%m%d_%H%M%S).tar.gz"
    
    # Backup storage directory and uploads
    if tar -czf "$backup_file" \
        -C /var/www/html \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        storage/ public/storage/; then
        success "Application files backup completed"
    else
        error "Application files backup failed"
        return 1
    fi
}

# Backup configurations
backup_configurations() {
    log "⚙️  Backing up configurations..."
    
    local backup_file="$BACKUP_DIR/config/config-$(date +%Y%m%d_%H%M%S).tar.gz"
    
    if tar -czf "$backup_file" \
        -C / \
        etc/nginx/nginx.conf \
        etc/mysql/conf.d/ \
        var/www/html/.env* \
        var/www/html/docker/ 2>/dev/null; then
        success "Configurations backup completed"
    else
        error "Configurations backup failed"
        return 1
    fi
}

# Upload to S3 (if configured)
upload_to_s3() {
    if [ -n "$S3_BUCKET" ] && command -v aws >/dev/null 2>&1; then
        log "☁️  Uploading backups to S3..."
        
        aws s3 sync "$BACKUP_DIR" "s3://$S3_BUCKET/laravel-tenant-system/$(date +%Y/%m/%d)/" \
            --delete --storage-class STANDARD_IA
        
        success "S3 upload completed"
    else
        log "S3 upload skipped (not configured or AWS CLI not available)"
    fi
}

# Cleanup old backups
cleanup_old_backups() {
    log "🧹 Cleaning up old backups (older than $RETENTION_DAYS days)..."
    
    find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
    find "$BACKUP_DIR" -type f -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete
    
    success "Cleanup completed"
}

# Health check
health_check() {
    log "🏥 Running health checks..."
    
    # Check database connectivity
    if ! mysqladmin ping -h mysql-master -u root -p"$DB_ROOT_PASSWORD" --silent; then
        error "Database health check failed"
        return 1
    fi
    
    # Check disk space
    local disk_usage=$(df /backups | awk 'NR==2{print $5}' | sed 's/%//')
    if [ "$disk_usage" -gt 85 ]; then
        error "Disk usage is high: ${disk_usage}%"
        return 1
    fi
    
    success "Health checks passed"
}

# Main backup function
run_backup() {
    local start_time=$(date +%s)
    
    log "🚀 Starting Laravel 500 Tenant System backup process..."
    
    # Pre-backup checks
    health_check || { notify "Health check failed" "❌ FAILED"; exit 1; }
    
    create_backup_dirs
    
    # Run backups
    local errors=0
    
    backup_central_database || ((errors++))
    backup_tenant_databases || ((errors++))
    backup_application_files || ((errors++))
    backup_configurations || ((errors++))
    
    # Post-backup tasks
    upload_to_s3
    cleanup_old_backups
    
    local end_time=$(date +%s)
    local duration=$((end_time - start_time))
    
    if [ $errors -eq 0 ]; then
        success "🎉 Backup completed successfully in ${duration} seconds"
        notify "Backup completed successfully in ${duration} seconds" "✅ SUCCESS"
    else
        error "❌ Backup completed with $errors errors in ${duration} seconds"
        notify "Backup completed with $errors errors in ${duration} seconds" "⚠️  WARNING"
        exit 1
    fi
}

# Restore function (for recovery)
restore_backup() {
    local backup_date=$1
    
    if [ -z "$backup_date" ]; then
        error "Usage: $0 restore YYYYMMDD_HHMMSS"
        exit 1
    fi
    
    log "🔄 Starting restore process for backup: $backup_date"
    
    # Implementation for restore process
    # This would be used for disaster recovery
    
    log "Restore functionality - implement as needed for disaster recovery"
}

# Main script logic
case "${1:-backup}" in
    backup)
        run_backup
        ;;
    restore)
        restore_backup "$2"
        ;;
    health)
        health_check
        ;;
    *)
        echo "Usage: $0 {backup|restore|health}"
        exit 1
        ;;
esac