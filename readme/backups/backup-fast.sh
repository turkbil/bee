#!/bin/bash
################################################################################
# HIZLI DATABASE BACKUP (Optimized)
# - Low priority (nice/ionice) - Sistemi yavaşlatmaz
# - Paralel sıkıştırma (pigz) - 3-4x daha hızlı
# - Sadece önemli tabloları yedekler (opsiyonel)
################################################################################

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  HIZLI DATABASE BACKUP TOOL${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="readme/backups/$TIMESTAMP"
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✓${NC} Backup klasörü: $BACKUP_DIR"
echo ""

# Database bilgileri
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2 | tr -d '"')
DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2 | tr -d '"')
CENTRAL_DB=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2 | tr -d '"')

# pigz var mı kontrol et (yoksa gzip kullan)
if command -v pigz &> /dev/null; then
    COMPRESS="pigz"
    echo -e "${GREEN}✓${NC} pigz kullanılıyor (paralel sıkıştırma)"
else
    COMPRESS="gzip"
    echo -e "${BLUE}ℹ${NC}  gzip kullanılıyor (pigz kurun: yum install pigz)"
fi
echo ""

# ═══════════════════════════════════════════════════════════════════
# CENTRAL DB BACKUP
# ═══════════════════════════════════════════════════════════════════
echo -e "${BLUE}📊 Central Database Backup${NC}"
echo "Database: $CENTRAL_DB"

# nice -n 19: En düşük CPU önceliği (sistem yavaşlamaz)
# ionice -c3: Idle I/O önceliği (disk yavaşlamaz)
nice -n 19 ionice -c3 mysqldump \
    -u "$DB_USER" \
    -p"$DB_PASS" \
    "$CENTRAL_DB" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    2>/dev/null | $COMPRESS > "$BACKUP_DIR/central_${CENTRAL_DB}.sql.gz" &

CENTRAL_PID=$!

# ═══════════════════════════════════════════════════════════════════
# TENANT DB BACKUP (Paralel)
# ═══════════════════════════════════════════════════════════════════
echo ""
echo -e "${BLUE}🏢 Tenant Databases Backup (Paralel)${NC}"

TENANT_DBS=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT id, tenancy_db_name FROM $CENTRAL_DB.tenants" -N 2>/dev/null)

if [ -n "$TENANT_DBS" ]; then
    echo "$TENANT_DBS" | while IFS=$'\t' read -r TENANT_ID DB_NAME; do
        echo "Tenant $TENANT_ID → $DB_NAME (arka planda)"

        # Her tenant paralel olarak yedeklenir (hızlı!)
        nice -n 19 ionice -c3 mysqldump \
            -u "$DB_USER" \
            -p"$DB_PASS" \
            "$DB_NAME" \
            --single-transaction \
            --quick \
            --lock-tables=false \
            2>/dev/null | $COMPRESS > "$BACKUP_DIR/tenant${TENANT_ID}_${DB_NAME}.sql.gz" &
    done
fi

# Central backup'ın bitmesini bekle
wait $CENTRAL_PID
if [ $? -eq 0 ]; then
    SIZE=$(du -h "$BACKUP_DIR/central_${CENTRAL_DB}.sql.gz" | cut -f1)
    echo -e "${GREEN}✓${NC} Central DB: $SIZE"
else
    echo -e "${RED}✗${NC} Central DB backup hatası!"
fi

# Tüm tenant backupların bitmesini bekle
wait

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Hızlı Backup Tamamlandı!${NC}"
echo ""
echo "Klasör: $BACKUP_DIR"
echo "Toplam: $(du -sh $BACKUP_DIR | cut -f1)"
echo ""
ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null | awk '{print "  - " $9 " (" $5 ")"}'
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
