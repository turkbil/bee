#!/bin/bash
################################################################################
# MİNİMAL BACKUP (Sadece critical tablolar - ÇOK HIZLI!)
# Kullanım: Kod değişiklikleri öncesi hızlı yedek
# Süre: ~10-30 saniye (full backup yerine 3-5 dakika)
################################################################################

GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}⚡ MİNİMAL HIZLI BACKUP${NC}"

TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="readme/backups/minimal-$TIMESTAMP"
mkdir -p "$BACKUP_DIR"

DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2 | tr -d '"')
DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2 | tr -d '"')
CENTRAL_DB=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2 | tr -d '"')

# Sadece kritik tabloları yedekle (hızlı!)
CRITICAL_TABLES="pages tenants domains users settings"

echo "Yedeklenen tablolar: $CRITICAL_TABLES"
echo ""

for TABLE in $CRITICAL_TABLES; do
    echo -n "  - $TABLE... "
    nice -n 19 mysqldump \
        -u "$DB_USER" \
        -p"$DB_PASS" \
        "$CENTRAL_DB" \
        "$TABLE" \
        --single-transaction \
        2>/dev/null | gzip > "$BACKUP_DIR/${TABLE}.sql.gz"

    if [ $? -eq 0 ]; then
        SIZE=$(du -h "$BACKUP_DIR/${TABLE}.sql.gz" | cut -f1)
        echo -e "${GREEN}✓${NC} $SIZE"
    else
        echo -e "${RED}✗${NC}"
    fi
done

echo ""
echo -e "${GREEN}✅ Minimal backup tamamlandı: $BACKUP_DIR${NC}"
echo "Toplam: $(du -sh $BACKUP_DIR | cut -f1)"
