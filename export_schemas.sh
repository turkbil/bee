#!/bin/bash

echo "=== Exporting schemas for each table ==="

# Get table list
mysql -u root -h 127.0.0.1 -e "SHOW TABLES;" laravel | grep -v "Tables_in_laravel" | while read table; do
  echo "Exporting schema for table: $table"
  mysqldump -u root -h 127.0.0.1 --no-data --single-transaction --routines --triggers laravel $table > sql/schema/${table}.sql
done

echo "=== Schema export completed ==="