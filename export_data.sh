#!/bin/bash

echo "=== Exporting data for each table ==="

# Get table list
mysql -u root -h 127.0.0.1 -e "SHOW TABLES;" laravel | grep -v "Tables_in_laravel" | while read table; do
  echo "Exporting data for table: $table"
  mysqldump -u root -h 127.0.0.1 --no-create-info --single-transaction --complete-insert --skip-extended-insert laravel $table > sql/data/${table}.sql
done

echo "=== Data export completed ==="