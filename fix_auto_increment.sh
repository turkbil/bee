#!/bin/bash

echo "=== Fixing Auto Increment values ==="

# Auto increment olan tabloları bulup düzeltiyoruz
echo "Getting auto increment tables..."

mysql -u root -h 127.0.0.1 laravel -e "
SELECT
    t.table_name,
    c.column_name,
    t.auto_increment
FROM information_schema.tables t
JOIN information_schema.columns c ON t.table_name = c.table_name
WHERE t.table_schema = 'laravel'
AND c.extra = 'auto_increment'
ORDER BY t.table_name;" | tail -n +2 | sort -u | while read table_name column_name auto_increment; do
    if [ "$table_name" != "TABLE_NAME" ] && [ -n "$table_name" ]; then
        echo "Processing table: $table_name, column: $column_name"

        # Get max value from table
        max_id=$(mysql -u root -h 127.0.0.1 laravel -s -e "SELECT COALESCE(MAX($column_name), 0) FROM $table_name;")
        next_id=$((max_id + 1))

        echo "  Max ID: $max_id, Setting auto_increment to: $next_id"

        # Update schema file to include AUTO_INCREMENT value
        sed -i '' "s/) ENGINE=/) AUTO_INCREMENT=$next_id ENGINE=/g" sql/schema/${table_name}.sql

        # Add reset auto increment command to a separate file
        echo "ALTER TABLE \`$table_name\` AUTO_INCREMENT = $next_id;" >> sql/reset_auto_increment.sql
    fi
done

echo "=== Auto Increment values fixed ==="