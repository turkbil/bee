#!/bin/bash
################################################################################
# DATABASE BACKUP SCRIPT
# Tüm Central ve Tenant veritabanlarını yedekler
# Kullanım: bash readme/backups/backup.sh
################################################################################

# Renkli output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  DATABASE BACKUP TOOL${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Tarih ve saat
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="readme/backups/$TIMESTAMP"

# Backup klasörü oluştur
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✓${NC} Backup klasörü oluşturuldu: $BACKUP_DIR"
echo ""

# Database bilgileri (.env'den oku)
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2 | tr -d '"')
DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2 | tr -d '"')
CENTRAL_DB=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2 | tr -d '"')

echo -e "${BLUE}📊 Central Database Backup${NC}"
echo "Database: $CENTRAL_DB"

# Central DB Backup
mysqldump -u "$DB_USER" -p"$DB_PASS" "$CENTRAL_DB" 2>/dev/null | gzip > "$BACKUP_DIR/central_${CENTRAL_DB}.sql.gz"

if [ $? -eq 0 ]; then
    SIZE=$(du -h "$BACKUP_DIR/central_${CENTRAL_DB}.sql.gz" | cut -f1)
    echo -e "${GREEN}✓${NC} Central DB yedeklendi: $SIZE"
else
    echo -e "${RED}✗${NC} Central DB yedekleme hatası!"
fi

echo ""
echo -e "${BLUE}🏢 Tenant Databases Backup${NC}"

# Tenant veritabanlarını listele ve yedekle
TENANT_DBS=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT id, tenancy_db_name FROM $CENTRAL_DB.tenants" -N 2>/dev/null)

if [ -z "$TENANT_DBS" ]; then
    echo -e "${RED}⚠${NC}  Tenant bulunamadı veya bağlantı hatası"
else
    echo "$TENANT_DBS" | while IFS=$'\t' read -r TENANT_ID DB_NAME; do
        echo "Tenant ID: $TENANT_ID → Database: $DB_NAME"

        mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" 2>/dev/null | gzip > "$BACKUP_DIR/tenant${TENANT_ID}_${DB_NAME}.sql.gz"

        if [ $? -eq 0 ]; then
            SIZE=$(du -h "$BACKUP_DIR/tenant${TENANT_ID}_${DB_NAME}.sql.gz" | cut -f1)
            echo -e "${GREEN}✓${NC} Tenant $TENANT_ID yedeklendi: $SIZE"
        else
            echo -e "${RED}✗${NC} Tenant $TENANT_ID yedekleme hatası!"
        fi
    done
fi

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Backup Tamamlandı!${NC}"
echo ""
echo "Backup Klasörü: $BACKUP_DIR"
echo "Toplam Boyut: $(du -sh $BACKUP_DIR | cut -f1)"
echo ""
echo "Dosyalar:"
ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null | awk '{print "  - " $9 " (" $5 ")"}'
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
