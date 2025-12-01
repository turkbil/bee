#!/bin/bash
###############################################################################
# Tenant Storage Symlink Ownership Fix Script
#
# SORUN: Symlink'ler root:root ownership ile oluşuyor → Apache 403 Forbidden
# SEBEP: StorageTenancyBootstrapper PHP lchown() çalıştırıyor ama sudo yok
# ÇÖZÜM: Bu script'i cron ile 5 dakikada bir çalıştır
#
# Crontab:
# */5 * * * * /var/www/vhosts/tuufi.com/httpdocs/storage/scripts/fix-tenant-symlink-ownership.sh >> /var/log/tenant-symlink-fix.log 2>&1
###############################################################################

STORAGE_PATH="/var/www/vhosts/tuufi.com/httpdocs/public/storage"
OWNER="tuufi.com_"
GROUP="psaserv"
FIXED=0

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Checking tenant symlink ownership..."

for symlink in "$STORAGE_PATH"/tenant*; do
    if [ -L "$symlink" ]; then
        CURRENT_OWNER=$(stat -c '%U:%G' "$symlink")

        if [ "$CURRENT_OWNER" != "$OWNER:$GROUP" ]; then
            echo "  ⚠️  $(basename $symlink): $CURRENT_OWNER → $OWNER:$GROUP"
            chown -h "$OWNER:$GROUP" "$symlink"
            FIXED=$((FIXED + 1))
        fi
    fi
done

if [ $FIXED -gt 0 ]; then
    echo "✅ Fixed $FIXED symlink(s)"
else
    echo "✅ All symlinks OK"
fi
